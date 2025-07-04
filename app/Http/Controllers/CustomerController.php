<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Validation\Rule;

class CustomerController extends Controller

{
    /**
     * Display a listing of the resource.
     */
    
    protected function authorizeCustomer(Customer $customer)
    {
        $admin_user_id = auth()->user()->id;
        if ($customer->admin_user_id != auth()->id()) {
            abort(403, 'Unauthorized access to this customer.');
        }
    } 

    public function index()
    {
        $admin_user_id = auth()->user()->id;
        $customers = Customer::where('admin_user_id', $admin_user_id)
                         ->orderBy('created_at', 'desc')
                         ->get(); 
        return view('customers.customers_list', compact('customers'));
    }
    
    /**
     * Show the form for creating a new resource.
     */

    public function create()

    {
        return view('customers.add_customers');
    }

    /**
     * Store a newly created resource in storage.
     */

    // public function store(Request $request)
    // {

    //     $request->validate([

    //         'name' => 'required',
    //         // 'email' => 'required|email|unique:customers,email',
    //         'email'  => [
    //         'required',
    //         'email',
    //         Rule::unique('customers')->where(function ($query) {
    //             return $query->where('admin_user_id', auth()->user()->id);
    //         }),
    //     ],
    //         'phone' => 'required',
    //         'street'=> 'required',
    //         'suburb'=> 'required',
    //         'state'=> 'required',
    //         'pincod'=> 'required',


    //     ], [
    //         'phone.regex' => 'The phone number must be in international format, e.g., +1234567890.',
    //         // 'email.unique' => 'The email address has already been taken.',
    //     ]);

    //    // Customer::create($request->only(['name', 'email', 'phone', 'street', 'house_number', 'suburb', 'state', 'pincod']));
    //     $data = $request->only(['name', 'email', 'phone', 'street', 'house_number', 'suburb', 'state', 'pincod']);
    //     $data['admin_user_id'] = auth()->user()->id;

    //     Customer::create($data);
    //     return redirect()->route('customers.index')->with('success', 'Customer created successfully.');

    // }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => [
            'required',
            'email',
            Rule::unique('customers')->where(function ($query) {
                return $query->where('admin_user_id', auth()->user()->id);
            }),
        ],
        'phone' => 'required',
        'street'=> 'required',
        'suburb'=> 'required',
        'state'=> 'required',
        'pincod'=> 'required',
    ], [
        'email.unique' => 'This email is already used under your admin account.',
        'phone.regex' => 'The phone number must be in international format, e.g., +1234567890.',
    ]);

    $data = $request->only(['name', 'email', 'phone', 'street', 'house_number', 'suburb', 'state', 'pincod']);
    $data['admin_user_id'] = auth()->user()->id;

    Customer::create($data);

    return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
}

    /**
     * Display the specified resource.
     */

    public function show(Customer $customer)
{
    // Ensure lists are sorted by 'created_at' in descending order
    $this->authorizeCustomer($customer);
    $lists = $customer->lists()->orderBy('created_at', 'desc')->get();

    return view('customers.show_customers', compact('customer', 'lists'));
}

     

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(Customer $customer)

    {
        $this->authorizeCustomer($customer);
        return view('customers.edit_customers', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */

     public function update(Request $request, Customer $customer)

     {
        $this->authorizeCustomer($customer);

         $request->validate([
             'name' => 'required',
            //  'email' => 'required|email|unique:customers,email,' . $customer->id,
            'email' => [
            'required',
            'email',
            Rule::unique('customers')->ignore($customer->id)->where(function ($query) {
                return $query->where('admin_user_id', auth()->user()->id);
            }),
        ],
             'phone' => 'required',
             'street'=> 'required',
             'suburb'=> 'required',
             'state'=> 'required',
             'pincod'=> 'required',
         ], [
             'phone.regex' => 'The phone number must be in international format, e.g., +1234567890.',
             'email.unique' => 'The email address has already been taken.',
         ]);
     
         $customer->update($request->only(['name', 'email', 'phone', 'street', 'house_number', 'suburb', 'state', 'pincod']));
     
         return redirect()->route('customers.edit', ['customer' => $customer->id])->with('success', 'Customer updated successfully.');
     }
     

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Customer $customer)

    {
        $this->authorizeCustomer($customer);


        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');

    }

    public function updateStatus(Request $request, $id)

    {
        $customer = Customer::findOrFail($id);
        $this->authorizeCustomer($customer);      

        $customer->status = $request->input('status');

        $customer->save();

        // Store email in session

        $request->session()->put('status_email', $customer->email);

        // Fetch all customers again for display (or you can fetch just one customer and pass it to the view)

        $customers = Customer::all();

        // Return view with updated status message
        return response()->json(['success' => 'Status updated successfully']);
        // return redirect()->route('customers.index')->with('success', 'Customer status updated successfully.');

    }


    public function showlistcoustomer($id)

{
    // Fetch the customer details based on $id
    $customer = Customer::findOrFail($id); 
    $this->authorizeCustomer($customer);  

    return view('list.show_list', compact('customer'));
}

// public function checkEmail(Request $request)
// {
//     $email = $request->input('email');
//     $exists = Customer::where('email', $email)->exists();

//     return response()->json(['available' => !$exists]);
// }

public function checkEmail(Request $request)
{

    $adminId = auth()->user()->id;

    $email = $request->input('email');

    $exists = Customer::where('email', $email)
        ->where('admin_user_id', $adminId)
        ->exists();

    return response()->json(['available' => !$exists]);
}





}