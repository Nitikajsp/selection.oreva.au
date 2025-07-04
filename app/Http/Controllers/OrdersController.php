<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ListModel;
use App\Models\Order;
use App\Models\Customer; // Import the Customer model
use App\Models\Product;
class OrdersController extends Controller

{
    /**
     * Display a listing of the resource.
     */
    public function showallorderdata()
    {
        $adminId = auth()->id();
        
        $list = ListModel::whereHas('customer', function ($query) use ($adminId) {
            $query->where('admin_user_id', $adminId);
        })
        ->with('customer') // Eager load customer to display info
        ->orderBy('created_at', 'desc')
        ->get();
    
        return view('order.order_list', compact('list'));
    }
    

    public function viewsingalorders($listId)

    {
        // Fetch orders by list_id, with related product, customer, and list data
        $orders = Order::with('product', 'customer', 'list')
                        ->where('list_id', $listId)
                        ->orderBy('created_at', 'desc')
                        ->get();
    
        // Pass the orders to the view
        return view('order.show_orders', compact('orders'));
    }
    


}
