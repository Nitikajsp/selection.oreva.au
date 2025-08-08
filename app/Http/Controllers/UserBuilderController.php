<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserBuilder;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserBuilderController extends Controller
{
    // public function index()
    // {
    //     $admin_user_id = auth()->user()->id;

    //     $builders = UserBuilder::where('admin_user_id', $admin_user_id)->orderBy('created_at', 'desc')->get();
    //     return view('user_builders.builder_list', compact('builders'));
    // }

    public function index()
    {
        if (request()->ajax()) {
            $admin_user_id = auth()->user()->id;

            $data = UserBuilder::where('admin_user_id', $admin_user_id)
                ->orderBy('created_at', 'desc')
                ->select(['id', 'builder_name', 'contact_email']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '
                    <div class="d-flex gap-1 align-items-center">
               
               
                    <a href="javascript:;" class="btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow text-black" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical ti-md"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end m-0">
                        <a href="' . route('user_builders.edit', $row->id) . '" class="dropdown-item"><i class="ti ti-pencil me-1"></i> Edit</a>
                        <a href="' . route('user_builders.show', $row->id) . '" class="dropdown-item"><i class="ti ti-eye me-1"></i> View</a>
                        <div class="dropdown-divider"></div>
                        <form action="' . route('user_builders.destroy', $row->id) . '" method="POST">
                            ' . csrf_field() . method_field("DELETE") . '
                            <button type="submit" class="text-danger dropdown-item delete-btn"><i class="ti ti-trash me-1"></i> Delete</button>
                        </form>
                    </div>
                </div> </div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('user_builders.builder_list');
    }

    public function create()
    {
        return view('user_builders.add_builder');
    }

    public function store(Request $request)
    {
        $admin_user_id = auth()->user()->id;


        $request->validate([
            'builder_name' => 'required|string|max:255',
            // 'contact_email' => 'required|email|unique:user_builders,contact_email',
            'contact_email' => [
                'required',
                'email',
                Rule::unique('user_builders')->where(function ($query) use ($admin_user_id) {
                    return $query->where('admin_user_id', $admin_user_id);
                }),
            ],
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        UserBuilder::create([
            'builder_name' => $request->builder_name,
            'contact_email' => $request->contact_email,
            'admin_user_id' => $admin_user_id,
            'customer_id' => $request->customer_id,
        ]);

        return redirect()->route('user_builders.index');
    }
    public function show($id)
    {
        $admin_user_id = auth()->id();

        $userBuilder = UserBuilder::with('customer') // eager load customer
            ->where('id', $id)
            ->where('admin_user_id', $admin_user_id)
            ->firstOrFail();

        return view('user_builders.show_builder', compact('userBuilder'));
    }


    public function edit($id)
    {
        // $builders = UserBuilder::findOrFail($id);
        $builders = UserBuilder::with('customer')->findOrFail($id);
        return view('user_builders.edit_builder', compact('builders'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'builder_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:user_builders,contact_email,' . $id, // Ensuring unique email except for the current record
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        // Find the builder by ID
        $builder = UserBuilder::findOrFail($id);

        // Update the builder's information
        $builder->update([
            'builder_name' => $request->builder_name,
            'contact_email' => $request->contact_email,
            'customer_id' => $request->customer_id,
            // Add other fields here if needed
        ]);

        // Redirect back with a success message
        return redirect()->route('user_builders.index')->with('success', 'Builder updated successfully.');
    }


    public function destroy($id)
    {
        $builder = UserBuilder::findOrFail($id);
        $builder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Builder deleted successfully!'
        ]);
    }


    //  public function destroytest($id)
    // {
    //     $user = UserBuilder::findOrFail($id);
    //     $user->delete();

    //     return redirect()->route('user_builders.index');
    // }
}
