<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller


{
    protected function authorizeProduct(Product $product)
    {
        if ($product->admin_user_id != auth()->id()) {
            abort(403, 'Unauthorized access to this product.');
        }
    }
  
    public function showallproductdata()
    
    {
        // Fetch products with delete_status = '1' (products that are not marked as deleted)
         $admin_user_id = auth()->user()->id;
        $products = \DB::table('products')
            ->where('delete_status', '1')
            ->where('admin_user_id', $admin_user_id)
            ->orderBy('created_at', 'asc')
            ->get();  // Use get() to fetch all products without pagination
    
        // Fetch all categories
        $categories = \DB::table('categories')->pluck('category_name', 'id');
    
        // Add category names to products
        foreach ($products as $product) {
            $categoryIds = explode(',', $product->product_category);
            $product->category_names = array_map(function($id) use ($categories) {
                return $categories[$id] ?? 'Unknown';
            }, $categoryIds);
        }
    
        return view('products.product_list', compact('products'));
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
        // Update the delete_status to '0' (as a string)
        $this->authorizeProduct($product);
        $product->delete_status = '0';
        $product->save();
    
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

        return response()->json(['success' => true]);

    } catch (\Exception $e) {

        return response()->json(['success' => false, 'error' => $e->getMessage()]);

    }
}
  

}