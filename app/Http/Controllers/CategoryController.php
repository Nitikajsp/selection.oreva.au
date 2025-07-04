<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
     
    protected function authorizeCategory(Category $category)
    {
        if ($category->admin_user_id != auth()->id()) {
            abort(403, 'Unauthorized access to this customer.');
        }
    } 

    public function showallcategory()
    {
        $admin_user_id = auth()->user()->id;
        $categories = Category::where('admin_user_id', $admin_user_id)
                         ->get(); 
       // $categories = Category::all();
        return view('category.category_list', compact('categories'));
    }


    public function addcategory()

    {
        return view('category.add_category');
    }
    public function categorystore(Request $request)
    {
        $request->validate([
            'category_name' => 'required',
        ]);
    
        // Check if the category already exists
        $existingCategory = Category::where('category_name', $request->category_name)->first();
    
        if ($existingCategory) {
            return redirect()->route('showcategory')
                             ->with('error', 'Category already exists.');
        }
    
        // If the category does not exist, create a new one
        //Category::create($request->all());
        Category::create([
            'category_name' => $request->category_name,
            'admin_user_id' => auth()->user()->id,
        ]);
    
        return redirect()->route('showcategory')
                         ->with('success', 'Category added successfully.');
    }
    
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);
    
        // Find the category by ID
        $category = Category::find($id);
        $this->authorizeCategory($category);
    
        if ($category) {
            // Update the category name
            $category->category_name = $request->category_name;
            $category->save();
    
            // Redirect with success message
            return redirect()->route('showcategory')
                             ->with('success', 'Category updated successfully.');
        }
    
        // Redirect with error message if category not found
        return redirect()->route('showcategory')
                         ->with('error', 'Category not found.');
    }
    
    public function destroycategory(Request $request)
    {
        $category = Category::find($request->id);
        $this->authorizeCategory($category);
        if ($category) {
            $category->delete();

            return redirect()->route('showcategory')
                             ->with('success', 'Category deleted successfully.');
        }

        return redirect()->route('showcategory')
                         ->with('error', 'Category not found.');
    }

    public function edit(Category $category)

    {
        $this->authorizeCategory($category);
        return view('category.edit_category', compact('category'));
    }

    public function getCategories()
    {
        //$categories = Category::all(); // Fetch all categories from the database
        $categories = Category::where('admin_user_id', auth()->user()->id)->get();
        return response()->json($categories); // Return categories as JSON
    }
    
}
