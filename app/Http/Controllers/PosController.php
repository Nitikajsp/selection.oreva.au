<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ListModel;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $adminId = auth()->id();

        $initialCustomer = null;
        $initialList = null;

        $customerId = $request->query('customer_id');
        $listId = $request->query('list_id');

        if ($customerId) {
            $initialCustomer = Customer::where('id', $customerId)
                ->where('admin_user_id', $adminId)
                ->first();
        }

        if ($listId) {
            $initialList = ListModel::where('id', $listId)
                ->whereHas('customer', function ($q) use ($adminId) {
                    $q->where('admin_user_id', $adminId);
                })
                ->first();
        }

        return view('pos.index', compact('initialCustomer', 'initialList'));
    }

    public function products(Request $request)
    {
        $adminId = auth()->id();
        $search = $request->get('term');
        $page = max((int) $request->get('page', 1), 1);
        $perPage = (int) $request->get('per_page', 10);

        if ($perPage <= 0) {
            $perPage = 10;
        }

        $query = Product::with('category')->orderBy('product_name', 'asc');

        // Apply admin filter only if column exists
        if ($adminId && Schema::hasColumn('products', 'admin_user_id')) {
            $query->where('admin_user_id', $adminId);
        }

        // Only filter by delete_status / in_stock if those columns actually exist
        if (Schema::hasColumn('products', 'delete_status')) {
            $query->where('delete_status', '1');
        }

        if (Schema::hasColumn('products', 'in_stock')) {
            $query->where('in_stock', 1);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        // Build a safe select list based on actual columns present
        $select = ['id', 'product_name', 'product_code'];

        if (Schema::hasColumn('products', 'product_category')) {
            $select[] = 'product_category';
        }

        if (Schema::hasColumn('products', 'product_price')) {
            $select[] = 'product_price';
        }

        if (Schema::hasColumn('products', 'product_stock')) {
            $select[] = 'product_stock';
        }

        if (Schema::hasColumn('products', 'product_description')) {
            $select[] = 'product_description';
        }

        if (Schema::hasColumn('products', 'product_image')) {
            $select[] = 'product_image';
        }

        $paginator = $query->paginate($perPage, $select, 'page', $page);

        return response()->json([
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function customers(Request $request)
    {
        $adminId = auth()->id();
        $term = $request->get('term');

        $query = Customer::query()->where('admin_user_id', $adminId);

        if (!empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        $customers = $query->limit(20)->get([
            'id',
            'name',
            'email',
            'phone',
        ]);

        return response()->json($customers);
    }

    public function orders(Request $request)
    {
        $adminId = auth()->id();
        $search = $request->get('q');

        $query = Order::with(['product', 'customer', 'list.customer'])
            ->whereHas('customer', function ($q) use ($adminId) {
                $q->where('admin_user_id', $adminId);
            });

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('list', function ($lq) use ($search) {
                        $lq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $rows = $query->orderBy('created_at', 'desc')->get();

        $orders = $rows->groupBy('list_id')->map(function ($items) {
            $first = $items->first();

            $totalItems = $items->sum('quantity');

            $orderDate = null;
            $orderTime = null;

            if ($first->created_at) {
                $createdAtIst = $first->created_at->copy()->timezone('Asia/Kolkata');
                $orderDate = $createdAtIst->format('d M Y');
                $orderTime = $createdAtIst->format('h:i A');
            }

            $subtotal = 0;
            $mappedItems = $items->map(function ($row) use (&$subtotal) {
                $price = ($row->product && $row->product->product_price !== null)
                    ? (float) $row->product->product_price
                    : 0;
                $lineTotal = $price * $row->quantity;
                $subtotal += $lineTotal;

                return [
                    'product_name' => $row->product ? $row->product->product_name : null,
                    'quantity' => $row->quantity,
                    'price' => $price,
                    'total' => $lineTotal,
                ];
            })->values();

            $list = $first->list;
            $customer = $first->customer;

            return [
                // Use project (list) name as the order reference
                'order_number' => $list ? $list->name : ('List #' . $first->list_id),
                'customer_name' => $customer ? $customer->name : null,
                'order_date' => $orderDate,
                'order_time' => $orderTime,
                'items_count' => $totalItems,
                'subtotal' => $subtotal,
                'grand_total' => $subtotal,
                'items' => $mappedItems,
            ];
        })->values();

        if ($request->ajax()) {
            return response()->json([
                'data' => $orders,
            ]);
        }

        return view('pos.orders', [
            'orders' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'list_id' => 'required|exists:lists,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.comment' => 'nullable|string|max:1000',
            'action_type' => 'nullable|in:save,save_send',
            'customer_name' => 'nullable|string|max:255',
            'signature' => 'nullable|string',
        ]);

        $adminId = auth()->id();
        $customerId = $request->input('customer_id');
        $listId = $request->input('list_id');
        $items = $request->input('items');
        $actionType = $request->input('action_type', 'save');
        $posCustomerName = trim((string) $request->input('customer_name', ''));
        $posSignature = $request->input('signature');

        // Ensure customer belongs to this admin
        $customer = Customer::where('id', $customerId)
            ->where('admin_user_id', $adminId)
            ->firstOrFail();

        // Ensure list (project) belongs to this customer and admin
        $list = ListModel::where('id', $listId)
            ->where('customer_id', $customerId)
            ->whereHas('customer', function ($q) use ($adminId) {
                $q->where('admin_user_id', $adminId);
            })
            ->firstOrFail();

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $ordersData = [];

            foreach ($items as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('admin_user_id', $adminId)
                    ->firstOrFail();

                $qty = (int) $item['quantity'];
                $itemComment = isset($item['comment']) ? trim((string) $item['comment']) : '';

                if ($qty <= 0) {
                    continue;
                }

                if ($product->product_stock < $qty) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Not enough stock for product: {$product->product_name}",
                    ], 422);
                }

                // Compute total for UI only (do not store price in orders table)
                $price = $product->product_price;
                $lineTotal = $price * $qty;
                $totalAmount += $lineTotal;

                // Create or update order row in shared orders table
                $existingOrder = Order::where('product_id', $product->id)
                    ->where('list_id', $listId)
                    ->where('customer_id', $customerId)
                    ->first();

                if ($existingOrder) {
                    $existingOrder->update([
                        'quantity' => $qty,
                        'comment' => $itemComment,
                        'pos_customer_name' => $posCustomerName ?: null,
                        'pos_customer_signature' => $posSignature ?: null,
                    ]);

                    $ordersData[] = [
                        'product_name' => $product->product_name,
                        'product_code' => $product->product_code,
                        'quantity' => $existingOrder->quantity,
                        'comment' => $itemComment,
                        'product_image' => $product->product_image ?? null,
                        'order_id' => $existingOrder->id,
                    ];
                } else {
                    $order = Order::create([
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'customer_id' => $customerId,
                        'list_id' => $listId,
                        'comment' => $itemComment,
                        'pos_customer_name' => $posCustomerName ?: null,
                        'pos_customer_signature' => $posSignature ?: null,
                    ]);

                    $ordersData[] = [
                        'product_name' => $product->product_name,
                        'product_code' => $product->product_code,
                        'quantity' => $qty,
                        'comment' => $itemComment,
                        'product_image' => $product->product_image ?? null,
                        'order_id' => $order->id,
                    ];
                }

                // Deduct stock only once order is confirmed
                $product->product_stock = $product->product_stock - $qty;
                $product->save();
            }

            // Prepare data for optional email
            $orderData = [
                'customerName' => $customer->name,
                'orderDate' => now()->format('Y-m-d H:i:s'),
                'ordersData' => $ordersData,
                'customerEmail' => $customer->email,
                'customer' => $customer,
                'list' => $list,
                'posCustomerName' => $posCustomerName ?: null,
                'posCustomerSignature' => $posSignature ?: null,
            ];

            if ($actionType === 'save_send') {
                $pdfContent = Pdf::loadView('emails.order_confirmation', compact('orderData'))->output();

                // 2️⃣ STORE the PDF privately (THIS IS THE MISSING STEP)
                $fileName = 'invoice_' . $list->id . '_' . time() . '.pdf';
                $path = "private/invoices/{$fileName}";

                Storage::put($path, $pdfContent); // ✅ PUT IT HERE

                // 3️⃣ Create temporary signed URL for WhatsApp
                $pdfUrl = URL::temporarySignedRoute(
                    'secure.pdf',
                    now()->addMinutes(10),
                    ['filename' => $fileName]
                );


                // Send to customer
                if ($customer->email) {
                    Mail::to($customer->email)->send(new OrderConfirmation($orderData, $pdfContent));
                }

                // Send to list contact
                if (!empty($list->contact_email)) {
                    Mail::to($list->contact_email)->send(new OrderConfirmation($orderData, $pdfContent));
                }

                // Send to admin(s)
                $adminEmails = get_setting('email');
                if ($adminEmails) {
                    $emails = array_map('trim', explode(',', $adminEmails));
                    foreach ($emails as $adminEmail) {
                        Mail::to($adminEmail)->send(new OrderConfirmation($orderData, $pdfContent));
                    }
                }

                // 5️⃣ Send WhatsApp using Twilio cURL

                $account_sid  = env('TWILIO_SID');
                $auth_token   = env('TWILIO_TOKEN');
                $template_sid = env('TWILIO_WHATSAPP_TEMPLATE_ID');
                $from         = env('TWILIO_WHATSAPP_FROM');

                // Your 2 fixed WhatsApp numbers
                $whatsappNumbers = [
                    'whatsapp:+919327505310',
                    // 'whatsapp:+919876543210',
                ];

                foreach ($whatsappNumbers as $to) {

                    $url = "https://api.twilio.com/2010-04-01/Accounts/{$account_sid}/Messages.json";

                    $data = [
                        'From'       => $from,
                        'To'         => $to,

                        // WhatsApp template (required for first / 24h-expired message)
                        'ContentSid' => $template_sid,

                        // Variables must match your approved template
                        'ContentVariables' => json_encode([
                            "1" => $customer->name,
                            "2" => $list->name,
                        ]),

                        // 🔑 This is the PDF
                        'MediaUrl' => [$pdfUrl],
                    ];

                    $ch = curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST           => true,
                        CURLOPT_POSTFIELDS     => http_build_query($data),
                        CURLOPT_USERPWD        => "{$account_sid}:{$auth_token}",
                    ]);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $error    = curl_error($ch);
                    curl_close($ch);

                    if ($httpCode !== 201) {
                        \Log::error('WhatsApp send failed', [
                            'to'       => $to,
                            'status'   => $httpCode,
                            'error'    => $error,
                            'response' => $response,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $actionType === 'save_send'
                    ? 'Order saved and email sent successfully.'
                    : 'Order saved successfully.',
                'project_name' => $list->name,
                'total_amount' => $totalAmount,
                'redirect_url' => route('showlistcustomer', [
                    'listId' => $list->id,
                    'customerId' => $customer->id,
                ]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to save POS order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
