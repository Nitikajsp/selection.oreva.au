<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserBuilder;
use Illuminate\Validation\Rule;


class UserBuilderController extends Controller
{
    public function index()
    {
        $admin_user_id = auth()->user()->id;

        $builders = UserBuilder::where('admin_user_id',$admin_user_id)->orderBy('created_at', 'desc')->get();
        return view('user_builders.builder_list', compact('builders'));
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
        ]);

        UserBuilder::create([
            'builder_name' => $request->builder_name,
            'contact_email' => $request->contact_email,
            'admin_user_id' => $admin_user_id, 
        ]);

        return redirect()->route('user_builders.index');
    }

    public function edit($id)
    {
        $builders = UserBuilder::findOrFail($id);
        return view('user_builders.edit_builder', compact('builders'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'builder_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:user_builders,contact_email,' . $id, // Ensuring unique email except for the current record
        ]);

        // Find the builder by ID
        $builder = UserBuilder::findOrFail($id);

        // Update the builder's information
        $builder->update([
            'builder_name' => $request->builder_name,
            'contact_email' => $request->contact_email,
            // Add other fields here if needed
        ]);

        // Redirect back with a success message
        return redirect()->route('user_builders.index')->with('success', 'Builder updated successfully.');
    }


    public function destroy($id)
    {
        $user = UserBuilder::findOrFail($id);
        $user->delete();

        return redirect()->route('user_builders.index');
    }
}