<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{

    protected function authorizeCategory(Category $category)
    {
        if ($category->admin_user_id != auth()->id()) {
            abort(403, 'Unauthorized access to this customer.');
        }
    }

    // public function showallcategory()
    // {
    //     $admin_user_id = auth()->user()->id;
    //     $categories = Category::where('admin_user_id', $admin_user_id)
    //                      ->get(); 
    //    // $categories = Category::all();
    //     return view('category.category_list', compact('categories'));
    // }

    public function showallcategory()
    {
        if (request()->ajax()) {
            $admin_user_id = auth()->user()->id;

            $data = Category::where('admin_user_id', $admin_user_id)
                ->orderBy('created_at', 'desc')
                ->select(['id', 'category_name', 'created_at']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y') : '';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                     <div class="d-flex gap-1 align-items-center">
                    <div class="d-flex justify-content-center align-items-center">
             
                        <div class="d-inline-block">
                            <a href="javascript:;" class="btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow text-black" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical ti-md"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end m-0">
                                <a href="' . route('editcategory', $row->id) . '" class="dropdown-item"><i class="ti ti-pencil me-1"></i> Edit</a>
                                <div class="dropdown-divider"></div>
                                <form action="' . route('destroycategory', $row->id) . '" method="POST">
                                    ' . csrf_field() . method_field("DELETE") . '
                                    <button type="submit" class="text-danger dropdown-item delete-btn"><i class="ti ti-trash me-1"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('category.category_list');
    }


    public function addcategory()
    {
        return view('category.add_category');
    }

    // public function categorystore(Request $request)
    // {
    //     $request->validate([
    //         'category_name' => 'required',
    //     ]);

    //     // Check if the category already exists
    //     $existingCategory = Category::where('category_name', $request->category_name)->first();

    //     if ($existingCategory) {
    //         return redirect()->route('showcategory')
    //                          ->with('error', 'Category already exists.');
    //     }

    //     // If the category does not exist, create a new one
    //     //Category::create($request->all());
    //     Category::create([
    //         'category_name' => $request->category_name,
    //         'admin_user_id' => auth()->user()->id,
    //     ]);

    //     return redirect()->route('showcategory')
    //                      ->with('success', 'Category added successfully.');
    // }

    public function categorystore(Request $request)
    {
        $request->validate([
            'category_name' => [
                'required',
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('admin_user_id', auth()->user()->id);
                }),
            ],
        ]);

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

    // public function destroycategory(Request $request)
    // {
    //     $category = Category::find($request->id);
    //     $this->authorizeCategory($category);
    //     if ($category) {
    //         $category->delete();

    //         return redirect()->route('showcategory')
    //             ->with('success', 'Category deleted successfully.');
    //     }

    //     return redirect()->route('showcategory')
    //         ->with('error', 'Category not found.');
    // }

    public function destroycategory(Request $request)
    {
        $category = Category::find($request->id);
        $this->authorizeCategory($category);

        if ($category) {
            $category->delete();

            if ($request->ajax()) {
                return response()->json(['message' => 'Category deleted successfully.']);
            }

            return redirect()->route('showcategory')
                ->with('success', 'Category deleted successfully.');
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Category not found.'], 404);
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
