<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\Setting;
use App\Models\ListModel;

class HomeController extends Controller

{
    /**
     * Create a new controller instance.
     *
     * @return void
     */     

    public function __construct()

    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */ 

    // public function index()

    // {

    //     $customerCount = Customer::count();
    //     $customers = Customer::with('orders')->get();
    //     $productCount = Product::where('delete_status', '1')->count();
    //     $listCount = ListModel::count();
    //     $recentProduct = Product::latest()->take(3)->get();
    //     $recentOrders = Order::with('product', 'customer')->latest()->take(4)->get();
    
    //     // Monthly data for chart

    //     $monthlyData = Order::selectRaw('MONTH(created_at) as month, COUNT(DISTINCT list_id) as count')
    //     ->groupBy('month')
    //     ->orderBy('month')
    //     ->pluck('count', 'month')
    //     ->toArray();

    //     // Fill missing months with 0

    //     $monthlyData = array_replace(array_fill(1, 12, 0), $monthlyData);
    
    //     // Calculate total orders and determine percentage scale (where 10 orders = 1%)

    //     $totalOrders = array_sum($monthlyData);
    //     $percentageScale = 1; // 
    
    //     // Convert monthly data to percentages
        
    //     $monthlyDataPercentages = array_map(function($count) use ($percentageScale) {
    //         return $count / $percentageScale; // Convert to percentage
    //     }, $monthlyData);
    
    //     return view('home', compact('customerCount', 'productCount', 'listCount', 'recentProduct', 'customers', 'recentOrders', 'monthlyDataPercentages'));

    // }


    public function index()
    {
        $adminId = auth()->id();

        // Only count customers belonging to the logged-in admin
        $customerCount = Customer::where('admin_user_id', $adminId)->count();

        // Get customers for this admin along with their orders
        $customers = Customer::with('orders')
            ->where('admin_user_id', $adminId)
            ->get();

        // Get customer IDs for filtering related data
        $customerIds = $customers->pluck('id');

        // Count products with delete_status '1' which have been ordered by these customers
        // $productCount = Product::where('delete_status', '1')
        //     ->whereIn('id', function ($query) use ($customerIds) {
        //         $query->select('product_id')
        //             ->from('orders')
        //             ->whereIn('customer_id', $customerIds);
        //     })
        //     ->count();

        $productCount = Product::where('admin_user_id',$adminId)->where('delete_status', '1')->count();

        // Count lists belonging to these customers
        // $listCount = ListModel::whereIn('customer_id', $customerIds)->count();

        $userorderCount = Order::whereIn('customer_id',$customerIds)->count();


        // Get recent products ordered by these customers
        $recentProduct = Product::where('delete_status', '1')
            ->whereIn('id', function ($query) use ($customerIds) {
                $query->select('product_id')
                    ->from('orders')
                    ->whereIn('customer_id', $customerIds);
            })
            ->latest()
            ->take(3)
            ->get();

        // Get recent orders for these customers
        $recentOrders = Order::with('product', 'customer')
            ->whereIn('customer_id', $customerIds)
            ->latest()
            ->take(4)
            ->get();

        // Monthly orders data (distinct list_ids) for these customers
        $monthlyData = Order::selectRaw('MONTH(created_at) as month, COUNT(DISTINCT list_id) as count')
            ->whereIn('customer_id', $customerIds)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Ensure data for every month from 1 to 12
        $monthlyData = array_replace(array_fill(1, 12, 0), $monthlyData);

        // Total orders (for percentage scale)
        $totalOrders = array_sum($monthlyData);
        $percentageScale = 1; // Adjust if needed

        // Convert monthly order counts to percentages
        $monthlyDataPercentages = array_map(function ($count) use ($percentageScale) {
            return $count / $percentageScale;
        }, $monthlyData);

        return view('home', compact(
            'customerCount', 
            'productCount', 
            'userorderCount', 
            'recentProduct', 
            'customers', 
            'recentOrders', 
            'monthlyDataPercentages'
        ));
    }

    
}