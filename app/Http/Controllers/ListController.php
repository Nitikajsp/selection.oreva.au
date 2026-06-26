<?php

namespace App\Http\Controllers;

// make sure to import listmodel
use App\Models\ListModel;
use App\Models\Order;
use App\Models\Customer; // Import the Customer model
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;
use App\Models\UserBuilder;
use Barryvdh\DomPDF\Facade\Pdf;

// Make sure to import Product model    
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListController extends Controller


//  crate a new  list  file redirect controller  start  //

{
    public function createlist($customer_id)

    {
        return view('list.add_list', compact('customer_id'));
    }


    // insert new list controller start //

    public function store(Request $request)


    {

        $request->validate([

            'list_name' => 'required|max:255',
            // 'suburb' => 'required|max:255',
            // 'state' => 'required|max:255',
            // 'pincod' => 'required|max:255',
            //'list_description' => 'required',
            'contact_number' => ['nullable', 'max:20', 'regex:/^\d+$/'],
            'contact_email' => 'required|email|max:255',
            //'builder_name' => 'required|max:255',
            //'status' => 'required|max:255',
            'customer_id' => 'required|exists:customers,id',

        ], [
            'contact_number.regex' => 'The contact number may contain digits only.',
        ]);


        ListModel::create([

            'name' => $request->input('list_name'),
            'suburb' => $request->input('suburb'),
            'state' => $request->input('state'),
            'pincod' => $request->input('pincod'),
            'description' => $request->input('list_description'),
            'contact_number' => $request->input('contact_number'),
            'contact_email' => $request->input('contact_email'),
            'builder_name' => $request->input('builder_name'),
            'status' => $request->input('status'),
            'customer_id' => $request->input('customer_id'),

        ]);


        return redirect()->route('customers.show', $request->input('customer_id'))
            ->with('success', 'List created successfully.');
    }


    //  singal list show contriller start //

    public function show($id)

    {
        $admin_user_id = auth()->user()->id;
        $list = ListModel::whereHas('customer', function ($query) use ($admin_user_id) {
            $query->where('admin_user_id', $admin_user_id);
        })->findOrFail($id);
        //$list = ListModel::findOrFail($id);

        return view('list.show_list', compact('list'));
    }


    // list edit file redirect  controller start  //


    public function edit($id)

    {
        $list = ListModel::findOrFail($id);

        $adminId = auth()->id();
        $allCustomers = Customer::where('admin_user_id', $adminId)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('list.edit_list', compact('list', 'allCustomers'));
    }



    // list update controller strat //

    public function update(Request $request, $id)

    {
        $request->validate([

            'name' => 'required|max:255',
            //'suburb' => 'required|max:255',
            //'state' => 'required|max:255',
            //'pincod' => 'required|max:255',
            //'description' => 'required',
            'contact_number' => ['nullable', 'max:20', 'regex:/^\d+$/'],
            'contact_email' => 'required|email|max:255',
            //'builder_name' => 'required|max:255',
            //'status' => 'required|max:255',

        ], [
            'contact_number.regex' => 'The contact number may contain digits only.',
        ]);

        $list = ListModel::findOrFail($id);

        $list->update($request->all());

        return redirect()->route('customers.show', $list->customer_id)
            ->with('success', 'List updated successfully.');
    }


    public function reassignCustomer(Request $request, $id)

    {
        $request->validate([
            'new_customer_id' => 'required|exists:customers,id',
        ]);

        $adminId = auth()->id();

        $list = ListModel::whereHas('customer', function ($query) use ($adminId) {
            $query->where('admin_user_id', $adminId);
        })->findOrFail($id);

        $newCustomer = Customer::where('admin_user_id', $adminId)
            ->findOrFail($request->input('new_customer_id'));
        // If the selected customer is the same as current, nothing to move
        if ($newCustomer->id === $list->customer_id) {
            return redirect()->route('customers.show', $newCustomer->id)
                ->with('success', 'Project is already assigned to the selected customer.');
        }

        // If the selected customer already has a project with the same name, do not move
        $duplicateProjectExists = ListModel::where('customer_id', $newCustomer->id)
            ->where('name', $list->name)
            ->exists();

        if ($duplicateProjectExists) {
            return redirect()->route('customers.show', $newCustomer->id)
                ->with('success', 'Project is already assigned to the selected customer.');
        }

        DB::transaction(function () use ($list, $newCustomer) {
            $oldCustomerId = $list->customer_id;

            // Move the list to the new customer
            $list->customer_id = $newCustomer->id;
            $list->save();

            // Move existing orders from the old customer to the new customer
            $list->orders()
                ->where('customer_id', $oldCustomerId)
                ->update([
                    'customer_id' => $newCustomer->id,
                ]);
        });

        return redirect()->route('customers.show', $newCustomer->id)
            ->with('success', 'Project moved to selected customer successfully.');
    }


    // list delete controller start  //

    public function destroy($id)

    {
        $adminId = auth()->id();

        $list = ListModel::whereHas('customer', function ($query) use ($adminId) {
            $query->where('admin_user_id', $adminId);
        })->findOrFail($id);

        $list->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project delete successfully.'
            ]);
        }

        return redirect()->route('customers.show', $list->customer_id)
            ->with('success', 'Project delete successfully.');
    }

    // add cart product controller start  //

    public function addcartproduct(Request $request, ListModel $list, $customerId)
    {
        $adminId = auth()->id();
        $search = trim((string) $request->query('search', ''));

        $lists = ListModel::where('customer_id', $customerId)
            ->whereHas('customer', function ($query) use ($adminId) {
                $query->where('admin_user_id', $adminId);
            })
            ->with(['products', 'customer'])
            ->get();
        $products = Product::where('in_stock', 1)
            ->where('delete_status', '1')
            ->where('admin_user_id', $adminId)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('product_name', 'like', "%{$search}%")
                        ->orWhere('product_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends($request->only('search'));

        // Fetch all categories
        $categories = DB::table('categories')
            ->where('admin_user_id', $adminId)
            ->pluck('category_name', 'id');

        // Add category names to products
        $products->getCollection()->transform(function ($product) use ($categories) {
            $categoryIds = explode(',', $product->product_category);
            $product->category_names = array_map(function ($id) use ($categories) {
                return $categories[$id] ?? 'Unknown';
            }, $categoryIds);
            return $product;
        });

        if ($request->ajax()) {
            return response()->json([
                'html' => view('list.partials.add_cart_products', compact('products', 'search'))->render(),
                'total' => $products->total(),
            ]);
        }

        return view('list.add_cart_product', compact('list', 'products', 'search'));
    }



    // addtocart product  create a session listid wise code //

    public function addToCart(Request $request, $listId)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        $comment = $request->input('comment'); // Capture the comment

        $customerId = session()->get('customer_id');

        $cart = session()->get('cart', []);

        if (!isset($cart[$listId])) {
            $cart[$listId] = [];
        }

        if (!isset($cart[$listId][$customerId])) {
            $cart[$listId][$customerId] = [];
        }

        // Store the product, quantity, and comment
        $cart[$listId][$customerId][$productId] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'comment' => $comment, // Store the comment
        ];

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart successfully.');
    }


    // add to cart product is a view listid wise //

    public function viewCart($listId)

    {

        $list = ListModel::findOrFail($listId);


        $customerId = $list->customer_id;

        $customer = Customer::findOrFail($customerId);

        $customerId = session()->get('customer_id');

        $cart = session()->get('cart', []);

        $cartItems = [];

        if (isset($cart[$listId][$customerId])) {

            $productIds = array_keys($cart[$listId][$customerId]);

            $products = Product::whereIn('id', $productIds)->get();

            foreach ($products as $product) {

                if (isset($cart[$listId][$customerId][$product->id])) {

                    $cartItems[] = [

                        'product' => $product,

                        'quantity' => $cart[$listId][$customerId][$product->id]['quantity'],

                        'comment' => $cart[$listId][$customerId][$product->id]['comment'],




                    ];
                }
            }
        }

        return view('list.view_cart', compact('list', 'customer', 'cartItems'));
    }

    public function updateqty(Request $request, $listId, $productId)
    {
        $customerId = session()->get('customer_id');
        $cart = session()->get('cart', []);

        // Get quantity and comment from the request
        $quantity = $request->input('quantity');
        $comment = $request->input('comment');

        if (isset($cart[$listId][$customerId][$productId])) {
            $cart[$listId][$customerId][$productId]['quantity'] = $quantity;
            $cart[$listId][$customerId][$productId]['comment'] = $comment;

            session()->put('cart', $cart);

            return redirect()->route('cart.view', $listId)->with('success', 'Quantity and comment updated successfully.');
        }

        return redirect()->route('cart.view', $listId)->with('error', 'Product not found in cart.');
    }


    //  remove product in add to cart product //

    public function removeFromCart($listId, $productId, $customerId)

    {

        $sessionCustomerId = session()->get('customer_id');


        $cart = session()->get('cart', []);

        if (isset($cart[$listId][$sessionCustomerId]) && array_key_exists($productId, $cart[$listId][$sessionCustomerId])) {

            unset($cart[$listId][$sessionCustomerId][$productId]);

            session()->put('cart', $cart);

            return redirect()->route('lists.view-cart', ['list' => $listId, 'customer_id' => $customerId])
                ->with('success', 'Product removed from cart successfully.');
        }

        return redirect()->route('lists.view-cart', ['list' => $listId, 'customer_id' => $customerId])
            ->with('error', 'Product not found in cart.');
    }

    // public function saveOrder(Request $request)

    // {
    //     if ($request->isMethod('post')) {
    //         $listId = $request->input('list_id');
    //         $customerId = $request->input('customer_id');
    //         $cartItems = $request->input('cart_items');
    //         $listEmail = $request->input('list_email');
    //         $customerEmail = $request->input('customer_email');
    //         $actionType = $request->input('action_type'); // Get the action type

    //         try {
    //             // Retrieve the list data based on the list_id
    //             $list = ListModel::find($listId);

    //             if (!$list) {
    //                 throw new \Exception("List not found.");
    //             }

    //             $ordersData = [];
    //             $orderId = null;

    //             foreach ($cartItems as $item) {
    //                 $productCode = $item['product_code'];
    //                 $productName = $item['product_name'];
    //                 $quantity = $item['quantity'];
    //                 $productImage = $item['product_image'];
    //                 $productId = $item['product_id']; // Make sure this exists in your cart items
    //                 $comment = $item['comment'];  // Get the comment


    //                 // Check if an order with the same product_code and list_id exists
    //                 $existingOrder = Order::where('product_id', $productId)
    //                     ->where('list_id', $listId)
    //                     ->where('customer_id', $customerId)
    //                     ->first();

    //                 if ($existingOrder) {
    //                     // Update the quantity of the existing order
    //                     $existingOrder->quantity = $quantity;
    //                     $existingOrder->comment = $comment;  // Update the comment

    //                     $existingOrder->save();

    //                     // Add the updated order details to ordersData
    //                     $ordersData[] = [
    //                         'product_name' => $productName,
    //                         'product_code' => $productCode,
    //                         'quantity' => $existingOrder->quantity, // Updated quantity
    //                         'comment' => $comment,  // Add comment to the order data
    //                         'product_image' => $productImage,
    //                         'order_id' => $existingOrder->id, // Existing order ID
    //                     ];
    //                 } else {
    //                     // Create a new order
    //                     $order = Order::create([
    //                         'quantity' => $quantity,
    //                         'customer_id' => $customerId,
    //                         'list_id' => $listId,
    //                         'product_id' => $productId, // Include the product_id
    //                         'comment' => $comment,  // Save the comment in the new order

    //                     ]);

    //                     // Add the order ID to the ordersData array
    //                     $ordersData[] = [
    //                         'product_name' => $productName,
    //                         'product_code' => $productCode,
    //                         'quantity' => $quantity,
    //                         'comment' => $comment,  // Add comment to the new order data
    //                         'product_image' => $productImage,
    //                         'order_id' => $order->id, // New order ID
    //                     ];

    //                     if (!$orderId) {
    //                         $orderId = $order->id;
    //                     }
    //                 }
    //             }

    //             // Clear the cart from session
    //             $request->session()->forget('cart.' . $listId);

    //             $customer = Customer::find($customerId); // Ensure $customer is correctly retrieved
    //             $customerName = $customer ? $customer->name : 'Customer';

    //             $orderDate = now()->format('Y-m-d H:i:s');

    //             $orderData = [
    //                 'customerName' => $customerName,
    //                 'orderId' => $orderId,
    //                 'orderDate' => $orderDate,
    //                 'ordersData' => $ordersData,
    //                 'customerEmail' => $customerEmail,
    //                 'customer' => $customer, // Pass all customer details to the view
    //                 'list' => $list, // Use the retrieved $list data here
    //             ];

    //             // Check if action_type is "save_send"
    //             if ($actionType == 'save_send') {
    //                 $pdf = Pdf::loadView('emails.order_confirmation', compact('orderData'));

    //                 // Send the email to the customer with the PDF attachment
    //                 Mail::send([], [], function ($message) use ($customer, $list, $pdf) {
    //                     $message->to($customer->email)
    //                         ->subject('Product List Received from Oreva Selection')
    //                         ->attachData($pdf->output(), "Selection Oreva_{$list->id}.pdf");
    //                 });

    //                 // Send the email to the list email with the PDF attachment
    //                 Mail::send([], [], function ($message) use ($list, $pdf) {
    //                     $message->to($list->contact_email)
    //                         ->subject('Product List Received from Oreva Selection')
    //                         ->attachData($pdf->output(), "Selection Oreva_{$list->id}.pdf");
    //                 });



    //                 $adminEmails = get_setting('email'); // e.g., "email1@example.com,email2@example.com"
    //                 if ($adminEmails) {
    //                     $emails = array_map('trim', explode(',', $adminEmails));
    //                     Mail::send([], [], function ($message) use ($emails, $list, $pdf) {
    //                         $message->to($emails)
    //                             ->subject('Product List Received from Oreva Selection (Admin Copy)')
    //                             ->attachData($pdf->output(), "Selection Oreva_{$list->id}_Admin.pdf");
    //                     });
    //                 }

    //                 return redirect()->route('showlistcustomer', [
    //                     'listId' => $listId,
    //                     'customerId' => $customerId
    //                 ])->with('success', 'Order saved successfully and email sent successfully.');
    //             }

    //             return redirect()->route('showlistcustomer', [
    //                 'listId' => $listId,
    //                 'customerId' => $customerId
    //             ])->with('success', 'Order saved successfully.');
    //         } catch (\Exception $e) {
    //             return redirect()->back()->with('error', 'Failed to save order. ' . $e->getMessage());
    //         }
    //     } else {
    //         return redirect()->back();
    //     }
    // }


    // public function saveOrder(Request $request)
    // {
    //     ini_set('max_execution_time', 300);
    //     set_time_limit(300);

    //     if ($request->isMethod('post')) {
    //         $listId = $request->input('list_id');
    //         $customerId = $request->input('customer_id');
    //         $cartItems = $request->input('cart_items');
    //         $customerEmail = $request->input('customer_email');
    //         $actionType = $request->input('action_type');

    //         try {
    //             $list = ListModel::find($listId);
    //             if (!$list) throw new \Exception("List not found.");

    //             $ordersData = [];
    //             $orderId = null;

    //             foreach ($cartItems as $item) {
    //                 $productId = $item['product_id'];
    //                 $productCode = $item['product_code'];
    //                 $productName = $item['product_name'];
    //                 $quantity = $item['quantity'];
    //                 $comment = $item['comment'];
    //                 $productImage = $item['product_image'];

    //                 $existingOrder = Order::where('product_id', $productId)
    //                     ->where('list_id', $listId)
    //                     ->where('customer_id', $customerId)
    //                     ->first();

    //                 if ($existingOrder) {
    //                     $existingOrder->update([
    //                         'quantity' => $quantity,
    //                         'comment' => $comment
    //                     ]);

    //                     $ordersData[] = [
    //                         'product_name' => $productName,
    //                         'product_code' => $productCode,
    //                         'quantity' => $existingOrder->quantity,
    //                         'comment' => $comment,
    //                         'product_image' => $productImage,
    //                         'order_id' => $existingOrder->id,
    //                     ];
    //                 } else {
    //                     $order = Order::create([
    //                         'quantity' => $quantity,
    //                         'customer_id' => $customerId,
    //                         'list_id' => $listId,
    //                         'product_id' => $productId,
    //                         'comment' => $comment,
    //                     ]);

    //                     $ordersData[] = [
    //                         'product_name' => $productName,
    //                         'product_code' => $productCode,
    //                         'quantity' => $quantity,
    //                         'comment' => $comment,
    //                         'product_image' => $productImage,
    //                         'order_id' => $order->id,
    //                     ];

    //                     if (!$orderId) {
    //                         $orderId = $order->id;
    //                     }
    //                 }
    //             }

    //             // Clear cart session
    //             $request->session()->forget('cart.' . $listId);

    //             $customer = Customer::find($customerId);
    //             $customerName = $customer ? $customer->name : 'Customer';

    //             $orderDate = now()->format('Y-m-d H:i:s');

    //             $orderData = [
    //                 'customerName' => $customerName,
    //                 'orderId' => $orderId,
    //                 'orderDate' => $orderDate,
    //                 'ordersData' => $ordersData,
    //                 'customerEmail' => $customerEmail,
    //                 'customer' => $customer,
    //                 'list' => $list,
    //             ];

    //             // Email logic
    //             if ($actionType == 'save_send') {
    //                 $pdfContent = Pdf::loadView('emails.order_confirmation', compact('orderData'))->output();

    //                 // Send to customer
    //                 Mail::to($customer->email)->send(new OrderConfirmation($orderData, $pdfContent));

    //                 // Send to list contact
    //                 Mail::to($list->contact_email)->send(new OrderConfirmation($orderData, $pdfContent));

    //                 // Send to admin(s)
    //                 $adminEmails = get_setting('email');
    //                 if ($adminEmails) {
    //                     $emails = array_map('trim', explode(',', $adminEmails));
    //                     foreach ($emails as $adminEmail) {
    //                         Mail::to($adminEmail)->send(new OrderConfirmation($orderData, $pdfContent));
    //                     }
    //                 }

    //                 return redirect()->route('showlistcustomer', [
    //                     'listId' => $listId,
    //                     'customerId' => $customerId
    //                 ])->with('success', 'Order saved successfully and email with PDF sent.');
    //             }

    //             return redirect()->route('showlistcustomer', [
    //                 'listId' => $listId,
    //                 'customerId' => $customerId
    //             ])->with('success', 'Order saved successfully.');
    //         } catch (\Exception $e) {
    //             return redirect()->back()->with('error', 'Failed to save order. ' . $e->getMessage());
    //         }
    //     }

    //     return redirect()->back();
    // }

    public function saveOrder(Request $request)
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);

        if ($request->isMethod('post')) {
            $listId = $request->input('list_id');
            $customerId = $request->input('customer_id');
            $cartItems = $request->input('cart_items');
            $customerEmail = $request->input('customer_email');
            $actionType = $request->input('action_type');
            $posCustomerName = trim((string) $request->input('customer_name', ''));
            $posSignature = $request->input('signature');

            try {
                $list = ListModel::find($listId);
                if (!$list) throw new \Exception("List not found.");

                $ordersData = [];
                $orderId = null;

                foreach ($cartItems as $item) {
                    $productId = $item['product_id'];
                    $productCode = $item['product_code'];
                    $productName = $item['product_name'];
                    $quantity = (int) $item['quantity'];
                    $comment = $item['comment'];
                    $productImage = $item['product_image'];

                    $existingOrder = Order::where('product_id', $productId)
                        ->where('list_id', $listId)
                        ->where('customer_id', $customerId)
                        ->first();

                    if ($existingOrder) {
                        $existingOrder->update([
                            'quantity' => $quantity,
                            'comment' => $comment,
                            'pos_customer_name' => $posCustomerName ?: null,
                            'pos_customer_signature' => $posSignature ?: null,
                        ]);

                        $ordersData[] = [
                            'product_name' => $productName,
                            'product_code' => $productCode,
                            'quantity' => $existingOrder->quantity,
                            'comment' => $comment,
                            'product_image' => $productImage,
                            'order_id' => $existingOrder->id,
                        ];
                    } else {
                        $order = Order::create([
                            'quantity' => $quantity,
                            'customer_id' => $customerId,
                            'list_id' => $listId,
                            'product_id' => $productId,
                            'comment' => $comment,
                            'pos_customer_name' => $posCustomerName ?: null,
                            'pos_customer_signature' => $posSignature ?: null,
                        ]);

                        $ordersData[] = [
                            'product_name' => $productName,
                            'product_code' => $productCode,
                            'quantity' => $quantity,
                            'comment' => $comment,
                            'product_image' => $productImage,
                            'order_id' => $order->id,
                        ];

                        if (!$orderId) {
                            $orderId = $order->id;
                        }
                    }

                    // ✅ Deduct stock only if Save & Send
                    if ($actionType === 'save_send') {
                        $product = Product::find($productId);
                        if ($product) {
                            $newStock = $product->product_stock - $quantity;

                            if ($newStock < 0) {
                                return redirect()->back()->with('error', "Not enough stock for product: {$product->product_name}");
                            }

                            $product->product_stock = $newStock;
                            $product->save();
                        }
                    }
                }

                // Clear cart session
                $request->session()->forget('cart.' . $listId);

                $customer = Customer::find($customerId);
                $customerName = $customer ? $customer->name : 'Customer';

                $orderDate = now()->format('Y-m-d H:i:s');

                $orderData = [
                    'customerName' => $customerName,
                    'orderId' => $orderId,
                    'orderDate' => $orderDate,
                    'ordersData' => $ordersData,
                    'customerEmail' => $customerEmail,
                    'customer' => $customer,
                    'list' => $list,
                    'posCustomerName' => $posCustomerName ?: null,
                    'posCustomerSignature' => $posSignature ?: null,
                ];

                // ✅ Email logic
                if ($actionType === 'save_send') {
                    $pdfContent = Pdf::loadView('emails.order_confirmation', ['orderData' => $orderData, 'isPdf' => true])->output();

                    $bccEmails = [];

                    if (!empty($list->contact_email) && $list->contact_email !== $customer->email) {
                        $bccEmails[] = $list->contact_email;
                    }

                    $adminEmails = get_setting('email');

                    if ($adminEmails) {
                        $emails = array_values(array_filter(array_map('trim', explode(',', $adminEmails))));
                        foreach ($emails as $adminEmail) {
                            if (!empty($adminEmail) && $adminEmail !== $customer->email) {
                                $bccEmails[] = $adminEmail;
                            }
                        }
                    }

                    $bccEmails = array_values(array_unique($bccEmails));

                    $mail = Mail::to($customer->email);
                    if (!empty($bccEmails)) {
                        $mail->bcc($bccEmails);
                    }

                    $mail->send(new OrderConfirmation($orderData, $pdfContent));

                    return redirect()->route('showlistcustomer', [
                        'listId' => $listId,
                        'customerId' => $customerId
                    ])->with('success', 'Order saved successfully and email with PDF sent.');
                }

                return redirect()->route('showlistcustomer', [
                    'listId' => $listId,
                    'customerId' => $customerId
                ])->with('success', 'Order saved successfully.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to save order. ' . $e->getMessage());
            }
        }

        return redirect()->back();
    }



    public function removeShowListFromCart($listId, $productId, $customerId)

    {

        $sessionCustomerId = session()->get('customer_id');

        $cart = session()->get('cart', []);

        if (isset($cart[$listId][$sessionCustomerId]) && array_key_exists($productId, $cart[$listId][$sessionCustomerId])) {

            unset($cart[$listId][$sessionCustomerId][$productId]);

            session()->put('cart', $cart);


            return redirect()->route('lists.showlistcoustomer', ['list' => $listId, 'customer_id' => $customerId])
                ->with('success', 'Product removed from cart successfully.');
        }

        return redirect()->route('lists.showlistcoustomer', ['list' => $listId, 'customer_id' => $customerId])
            ->with('error', 'Product not found in cart.');
    }

    public function showListCustomer($listId, $customerId)

    {
        $adminId = auth()->id();
        $search = trim((string) request('search', ''));
        $list = ListModel::where('id', $listId)
            ->where('customer_id', $customerId)
            ->whereHas('customer', function ($query) use ($adminId) {
                $query->where('admin_user_id', $adminId);
            })
            ->firstOrFail();

        $customer = $list->customer;

        $orders = Order::where('list_id', $listId)
            ->where('customer_id', $customerId)
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('product_name', 'like', "%{$search}%")
                        ->orWhere('product_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $products = Product::whereIn('id', $orders->getCollection()->pluck('product_id')->unique())->get()->keyBy('id');
        $categories = DB::table('categories')->pluck('category_name', 'id')->toArray();

        $orders->getCollection()->transform(function ($order) use ($products, $categories) {
            $product = $products->get($order->product_id);
            if ($product) {
                $categoryIds = explode(',', $product->product_category);
                $product->category_names = array_map(function ($id) use ($categories) {
                    return $categories[$id] ?? 'Unknown';
                }, $categoryIds);
            }
            $order->product = $product;
            return $order;
        });

        $projectAddress = trim(collect([$list->name, $list->suburb, $list->state, $list->pincod])->filter()->implode(', '));

        if (request()->ajax()) {
            return response()->json([
                'html' => view('list.partials.selection_products', compact('orders', 'categories', 'search', 'projectAddress'))->render(),
                'total' => $orders->total(),
            ]);
        }

        return view('list.show_list', compact('list', 'customer', 'orders', 'categories', 'search'));
    }

    //  show list order update qty //

    public function updateQuantity(Request $request, $orderId)

    {
        $order = Order::find($orderId);

        if ($order) {

            $order->quantity = $request->input('quantity');

            $order->save();

            return response()->json(['success' => true, 'message' => 'Quantity updated successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Order not found'], 404);
    }

    /**
     * Bulk update quantities for multiple orders from the Show List page.
     */
    public function bulkUpdateQuantities(Request $request)
    {
        $ordersPayload = $request->input('orders', []);

        if (!is_array($ordersPayload) || empty($ordersPayload)) {
            return redirect()->back()->with('error', 'No quantities to update.');
        }

        $updated = 0;

        foreach ($ordersPayload as $orderId => $data) {
            $quantity = isset($data['quantity']) ? (int) $data['quantity'] : null;
            if ($quantity === null) {
                continue;
            }

            $order = Order::find($orderId);
            if (!$order) {
                continue;
            }

            $order->quantity = max(0, $quantity);
            $order->save();
            $updated++;
        }

        if ($updated === 0) {
            return redirect()->back()->with('error', 'No orders were updated.');
        }

        return redirect()->back()->with('success', 'Quantity updated successfully!');
    }


    //  show list delete order  //

    public function destroyOrders(Order $order)

    {
        $order->delete();

        return redirect()->back()->with('success', 'Order deleted successfully.');
    }

    public function getLists(Request $request)

    {
        $customerId = $request->input('customer_id');
        // $lists = ListModel::where('customer_id', $customerId)->get(['id', 'name']);
        $adminId = auth()->id();
        $lists = ListModel::where('customer_id', $customerId)
            ->whereHas('customer', function ($query) use ($adminId) {
                $query->where('admin_user_id', $adminId);
            })
            ->get(['id', 'name']);

        return response()->json($lists);
    }


    public function showList($list, $customer_id)

    {
        // Fetch the necessary data based on $list and $customer_id
        // For example, fetch list details, customer details, etc.

        // Return a view with the data
        return view('lists.show_list', compact('list', 'customer_id'));
    }

    // public function sendEmail(Request $request, $list_id, $customer_id)
    // {
    //     $request->validate([
    //         'customer_email' => 'required|email',
    //     ]);

    //     // Retrieve list & customer
    //     $list = ListModel::find($list_id);
    //     $customer = Customer::find($customer_id);

    //     if (!$list || !$customer) {
    //         return redirect()->back()->with('error', 'List or Customer not found');
    //     }

    //     // Orders fetch
    //     $ordersData = Order::select('orders.*', 'products.product_name', 'products.product_image')
    //         ->join('products', 'orders.product_id', '=', 'products.id')
    //         ->where('orders.list_id', $list_id)
    //         ->where('orders.customer_id', $customer_id)
    //         ->get();

    //     $orderData = [
    //         'list' => $list,
    //         'customer' => $customer,
    //         'ordersData' => $ordersData
    //     ];

    //     $pdf = Pdf::loadView('emails.order_confirmation', ['orderData' => $orderData]);

    //     // ✅ Send to selected email from form
    //     $selectedEmail = $request->customer_email;
    //     Mail::send([], [], function ($message) use ($selectedEmail, $list, $pdf) {
    //         $message->to($selectedEmail)
    //             ->subject('Product List Received from Oreva Selection')
    //             ->attachData($pdf->output(), "Selection_Oreva_{$list->id}.pdf");
    //     });

    //     // Send to list contact email
    //     if (!empty($list->contact_email)) {
    //         Mail::send([], [], function ($message) use ($list, $pdf) {
    //             $message->to($list->contact_email)
    //                 ->subject('Product List Received from Oreva Selection')
    //                 ->attachData($pdf->output(), "Selection_Oreva_{$list->id}.pdf");
    //         });
    //     }

    //     // Send to admin emails
    //     $adminEmails = get_setting('email');
    //     if ($adminEmails) {
    //         $emails = array_map('trim', explode(',', $adminEmails));
    //         Mail::send([], [], function ($message) use ($emails, $list, $pdf) {
    //             $message->to($emails)
    //                 ->subject('Product List Received from Oreva Selection (Admin Copy)')
    //                 ->attachData($pdf->output(), "Selection_Oreva_{$list->id}_Admin.pdf");
    //         });
    //     }

    //     return redirect()->back()->with('success', 'Email sent successfully to ' . $selectedEmail);
    // }

    public function sendEmail(Request $request, $list_id, $customer_id)
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);

        $request->validate([
            'customer_email' => 'required|email',
        ]);

        // Retrieve list & customer
        $list = ListModel::find($list_id);
        $customer = Customer::find($customer_id);

        if (!$list || !$customer) {
            return redirect()->back()->with('error', 'List or Customer not found');
        }

        // Orders fetch
        // $ordersData = Order::select('orders.*', 'products.product_name', 'products.product_image')
        //     ->join('products', 'orders.product_id', '=', 'products.id')
        //     ->where('orders.list_id', $list_id)
        //     ->where('orders.customer_id', $customer_id)
        //     ->get();

        $ordersData = Order::select(
            'orders.*',
            'products.product_name',
            'products.product_image',
            'products.specification_product_image'
        )
        ->join('products', 'orders.product_id', '=', 'products.id')
        ->where('orders.list_id', $list_id)
        ->where('orders.customer_id', $customer_id)
        ->get();

        $orderData = [
            'list' => $list,
            'customer' => $customer,
            'ordersData' => $ordersData
        ];

        $pdf = Pdf::loadView('emails.order_confirmation', ['orderData' => $orderData, 'isPdf' => true]);
        $pdfContent = $pdf->output();

        // Subject with customer name
        $addressParts = [];
        if (!empty($list->name)) {
            $addressParts[] = $list->name;
        }
        if (!empty($list->suburb)) {
            $addressParts[] = $list->suburb;
        }
        if (!empty($list->state)) {
            $addressParts[] = $list->state;
        }
        if (!empty($list->pincod)) {
            $addressParts[] = $list->pincod;
        }
        $addressString = implode(', ', array_values(array_filter($addressParts, function ($v) {
            return $v !== null && $v !== '';
        })));

        $subject = "Product List Received from {$customer->name}";
        if (!empty($addressString)) {
            $subject .= " - {$addressString}";
        }
        $subject .= " - Oreva Selection";

        // ✅ Send to selected email from form
        $selectedEmail = $request->customer_email;
        $bccEmails = [];
        if (!empty($list->contact_email) && $list->contact_email !== $selectedEmail) {
            $bccEmails[] = $list->contact_email;
        }

        // $adminEmails = 'admin@varnihomes.com.au';
        $adminEmails = get_setting('email');

        if ($adminEmails) {
            $emails = array_values(array_filter(array_map('trim', explode(',', $adminEmails))));
            foreach ($emails as $adminEmail) {
                if (!empty($adminEmail) && $adminEmail !== $selectedEmail) {
                    $bccEmails[] = $adminEmail;
                }
            }
        }

        $bccEmails = array_values(array_unique($bccEmails));

        $bodyHtml = '<p>Hi, please find attached the selected product list for your project.</p>'
            . '<p>Kindly review and let us know if you would like to make any changes or proceed further</p>'
            . '<p>Thanks</p>';

        Mail::send([], [], function ($message) use ($selectedEmail, $bccEmails, $list, $pdfContent, $subject, $bodyHtml) {
            $message->to($selectedEmail)
                ->subject($subject)
                ->html($bodyHtml)
                ->attachData($pdfContent, "Selection_Oreva_{$list->id}.pdf");

            if (!empty($bccEmails)) {
                $message->bcc($bccEmails);
            }
        });

        return redirect()->back()->with('success', 'Email sent successfully to ' . $selectedEmail);
    }



    public function getCustomer(Request $request)
    {
        // dd($request->all());
        $search = $request->query('term');
        $customers = Customer::where('name', 'LIKE', "%{$search}%")
            ->where('admin_user_id', auth()->id())
            ->select('id', 'name', 'email')
            ->get();
        return response()->json($customers);
    }

    // public function getCustomer(Request $request){
    //     // dd($request->all());
    //     $search = $request->query('term');
    //     $customers = UserBuilder::where('builder_name', 'LIKE', "%{$search}%")
    //         ->select('id', 'builder_name', 'contact_email') 
    //         ->get();
    //     //dd($customers->all());
    //     return response()->json($customers);
    // }

}
