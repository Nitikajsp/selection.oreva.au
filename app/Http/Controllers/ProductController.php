<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Imports\ProductImport;
use App\Exports\ProductsExport;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller


{
    protected function authorizeProduct(Product $product)
    {
        if ($product->admin_user_id != auth()->id()) {
            abort(403, 'Unauthorized access to this product.');
        }
    }

    // public function showallproductdata()

    // {
    //     // Fetch products with delete_status = '1' (products that are not marked as deleted)
    //     $admin_user_id = auth()->user()->id;
    //     $products = DB::table('products')
    //         ->where('delete_status', '1')
    //         ->where('admin_user_id', $admin_user_id)
    //         ->orderBy('created_at', 'asc')
    //         ->get();  // Use get() to fetch all products without pagination

    //     // Fetch all categories
    //     $categories = DB::table('categories')->pluck('category_name', 'id');

    //     // Add category names to products
    //     foreach ($products as $product) {
    //         $categoryIds = explode(',', $product->product_category);
    //         $product->category_names = array_map(function ($id) use ($categories) {
    //             return $categories[$id] ?? 'Unknown';
    //         }, $categoryIds);
    //     }

    //     return view('products.product_list', compact('products'));
    // }

    public function showallproductdata()
    {
        if (request()->ajax()) {
            $admin_user_id = auth()->user()->id;

            $data = DB::table('products')
                ->where('delete_status', '1')
                ->where('admin_user_id', $admin_user_id)
                ->orderBy('created_at', 'desc')
                ->select(['id', 'product_image', 'product_category', 'product_name', 'product_code', 'product_stock', 'in_stock']);

            $categories = DB::table('categories')->pluck('category_name', 'id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product_image', function ($row) {
                    $imageUrl = asset('images/products/' . $row->product_image);
                    return '<img src="' . $imageUrl . '" alt="' . e($row->product_name) . '" width="80">';
                })
                ->addColumn('product_category', function ($row) use ($categories) {
                    $categoryIds = explode(',', $row->product_category);
                    $names = array_map(function ($id) use ($categories) {
                        return $categories[$id] ?? 'Unknown';
                    }, $categoryIds);
                    return implode(', ', $names);
                })
                ->addColumn('stock', function ($row) {
                    $checked = $row->in_stock ? 'checked' : '';
                    return '
                    <div class="form-check form-switch">
                        <input class="form-check-input stock-toggle on-off-setbutton" type="checkbox" data-id="' . $row->id . '" ' . $checked . '>
                    </div>
                ';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('products.edit', $row->id);
                    $viewUrl = route('products.show', $row->id);
                    $deleteForm = '
                    <form id="deleteForm' . $row->id . '" action="' . route('products.destroy', $row->id) . '" method="POST">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="button" class="delete-btn text-danger dropdown-item" data-id="' . $row->id . '">
                            <i class="ti ti-trash me-1"></i> Delete
                        </button>
                    </form>
                ';
                    return '
                    <div class="d-inline-block">
                        <a href="javascript:;" class="btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow text-black" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical ti-md"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end m-0">
                            <a href="' . $editUrl . '" class="dropdown-item"><i class="ti ti-pencil me-1"></i> Edit</a>
                            <a href="' . $viewUrl . '" class="dropdown-item"><i class="ti ti-eye me-1"></i> View</a>
                            <div class="dropdown-divider"></div>
                            ' . $deleteForm . '
                        </div>
                    </div>
                ';
                })
                ->rawColumns(['product_image', 'product_category', 'stock', 'action'])
                ->make(true);
        }

        return view('products.product_list');
    }




    //  create a product page ridirect controller start  // 

    public function create()

    {
        $categories = Category::where('admin_user_id', auth()->id())->get();
        return view('products.add_product');
    }


    // product insert controller start //
    public function addproduct(Request $request)

    {
        $request->validate([
            'product_name' => 'required',
            'product_category' => 'required|array',
            'product_description' => 'required',
            'product_code' => 'required|unique:products,product_code',
            'product_stock' => 'required|integer',
            'product_image' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'product_image.max' => 'Please upload an image smaller than 2MB.',

        ]);

        $input = $request->all();

        // Convert the product_category array into a comma-separated string
        $input['product_category'] = implode(',', $request->input('product_category'));

        if ($image = $request->file('product_image')) {
            $destinationPath = 'images/products/';
            $productImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $productImage);
            $input['product_image'] = "$productImage";
        }

        $input['admin_user_id'] = auth()->id();
        Product::create($input);

        return redirect()->route('showproduct')
            ->with('success', 'Product created successfully.');
    }

    // view product show contaroller start //

    public function show(Product $product)

    {
        $this->authorizeProduct($product);
        return view('products.show_product', compact('product'));
    }


    // product edit button edit page redirect controller start //
    public function edit(Product $product)
    {
        // Retrieve all categories
        $this->authorizeProduct($product);
        // $categories = Category::all();
        $categories = Category::where('admin_user_id', auth()->id())->get();


        // Pass the product and categories to the view
        return view('products.edit_product', compact('product', 'categories'));
    }

    // product update controller start //

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        $request->validate([

            'product_name' => 'required|string|max:255',
            'product_description' => 'required',
            'product_code' => 'required|string|',
            // 'product_price' => 'required|numeric|min:0',
            'product_stock' => 'required|integer|min:0',
            'product_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
            'product_category' => 'required|array',


        ]);

        $input = $request->all();

        $input['product_category'] = implode(',', $request->input('product_category'));

        if ($image = $request->file('product_image')) {

            $destinationPath = 'images/products/';
            $productImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $productImage);
            $input['product_image'] = "$productImage";

            if ($product->product_image && file_exists(public_path('images/products/' . $product->product_image))) {
                unlink(public_path('images/products/' . $product->product_image));
            }
        } else {

            unset($input['product_image']);
        }

        $product->update($input);

        return redirect()->route('showproduct')

            ->with('success', 'Product updated successfully');
    }

    //  product delete controller start //
    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);
        $product->delete_status = '0';
        $product->save();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('showproduct')->with('success', 'Product marked as deleted successfully.');
    }


    public function checkProductCode(Request $request)
    {

        $productCode = $request->input('product_code');

        $exists = Product::where('product_code', $productCode)->exists();

        return response()->json(['exists' => $exists]);
    }


    public function updateStock(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:products,id',
            'in_stock' => 'required|boolean',
        ]);

        try {
            $product = Product::find($validated['id']);
            $product->in_stock = $validated['in_stock'];
            $product->save();

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return back()->with('success', 'Stock updated successfully.');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        Excel::import(new ProductImport, $request->file('import_file'));

        return redirect()->back()->with('success', 'Products imported successfully!');
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'products_export.xlsx');
    }

    // public function downloadSample(): BinaryFileResponse
    // {
    //     $sampleData = [
    //         [
    //             'product_name'      => 'Towel Ring Black',
    //             'category_name'     => 'Towel Rings',
    //             'product_description' => 'Towel Ring Black',
    //             'product_code'      => 'A022b',
    //             'product_stock'     => 10,
    //             'in_stock'          => 5,
    //             'product_image'     => 'https://oreva.com.au/cdn/shop/files/TowelRingBlack-A022b-Copy_2.jpg?v=1718600952&width=493',
    //         ],
    //         [
    //             'product_name'      => 'Towel ring Chrome',
    //             'category_name'     => 'Towel Rings',
    //             'product_description' => 'Towel ring Chrome',
    //             'product_code'      => 'A022',
    //             'product_stock'     => 10,
    //             'in_stock'          => 5,
    //             'product_image'     => 'https://oreva.com.au/cdn/shop/files/TowelringChrome-Copy_2.jpg?v=1718601860&width=493',
    //         ],
    //     ];

    //     $collection = collect($sampleData);

    //     return Excel::download(new class($collection) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize {
    //         private $collection;

    //         public function __construct($collection)
    //         {
    //             $this->collection = $collection;
    //         }

    //         public function collection()
    //         {
    //             return $this->collection;
    //         }

    //         public function headings(): array
    //         {
    //             return [
    //                 'product_name',
    //                 'category_name',
    //                 'product_description',
    //                 'product_code',
    //                 'product_stock',
    //                 'in_stock',
    //                 'product_image',
    //             ];
    //         }
    //     }, 'product_sample.xlsx', ExcelFormat::XLSX);
    // }

    public function downloadSample(): BinaryFileResponse
    {
        $sampleData = [
            [
                'product_name' => 'Towel Ring Black',
                'category_name' => 'Towel Rings',
                'product_description' => 'Towel Ring Black',
                'product_code' => 'A022b',
                'product_stock' => 10,
                'in_stock' => 5,
                'product_image' => 'http://selection.oreva.au/images/products/example.jpg',
            ],
            [
                'product_name' => 'Towel ring Chrome',
                'category_name' => 'Towel Rings',
                'product_description' => 'Towel ring Chrome',
                'product_code' => 'A022',
                'product_stock' => 10,
                'in_stock' => 5,
                'product_image' => 'http://selection.oreva.au/images/products/example-2.jpg',
            ],
        ];

        return Excel::download(new class(collect($sampleData)) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
        {
            protected $rows;

            public function __construct($rows)
            {
                $this->rows = $rows;
            }

            public function collection()
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return [
                    'product_name',
                    'category_name',
                    'product_description',
                    'product_code',
                    'product_stock',
                    'in_stock',
                    'product_image',
                ];
            }

            // ✅ This fixes the error you got
            public function styles(Worksheet $sheet)
            {
                return [
                    1 => ['font' => ['bold' => true]],
                ];
            }
        }, 'product_sample.xlsx', ExcelFormat::XLSX);
    }
}
