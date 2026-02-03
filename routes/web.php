<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserBuilderController;
use App\Exports\ProductsExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\InvoiceCleanupController;

use Maatwebsite\Excel\Facades\Excel;

// Authentication routes
Auth::routes();

// Routes requiring authentication
Route::middleware(['auth'])->group(function () {

    //Remove pdf manually
    Route::get('/delete-invoices', [InvoiceCleanupController::class, 'deleteAll']);

    // Home Routes
    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index')->name('home');
        Route::get('/home', 'index')->name('home');
        Route::get('/home/customers-data', 'customersData')->name('home.customers.data');
    });

    // Customer Routes
    Route::controller(CustomerController::class)->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::put('/customers/{id}/updateStatus', 'updateStatus')->name('customers.updateStatus');
        Route::post('/check-email', 'checkEmail')->name('check.email');

        Route::get('/get-customers', [CustomerController::class, 'getCustomers']);
    });

    // Product Routes
    Route::controller(ProductController::class)->group(function () {
        Route::resource('products', ProductController::class);
        Route::post('/check-product-code', 'checkProductCode')->name('checkProductCode');
        Route::post('/products/add', 'addproduct')->name('addproduct');
        Route::get('/showproduct', 'showallproductdata')->name('showproduct');
        Route::post('/products/update-stock', 'updateStock')->name('products.updateStock');
        Route::post('/products/import', 'import')->name('products.import');
        Route::get('/export-products', 'export')->name('products.export');

        Route::get('sample-download', 'downloadSample')->name('products.sample.download');
    });

    // List Routes
    Route::controller(ListController::class)->group(function () {
        Route::get('/get-lists', 'getLists')->name('get-lists');
        Route::get('/createlist/{customer_id}', 'createlist')->name('createlist');
        Route::post('/lists', 'store')->name('lists.store');
        Route::get('/lists/{id}', 'show')->name('lists.show');
        Route::get('/lists/{id}/edit', 'edit')->name('lists.edit');
        Route::put('/lists/{id}', 'update')->name('lists.update');
        Route::post('/lists/{id}/reassign-customer', 'reassignCustomer')->name('lists.reassignCustomer');
        Route::delete('/lists/{id}', 'destroy')->name('lists.destroy');
        Route::get('/lists/{list}/products/{customer}', 'addCartProduct')->name('lists.addcartproduct');
        Route::post('/lists/add-to-cart/{list}/{customer}', 'addToCart')->name('lists.add-to-cart');
        Route::post('/lists/{list}/view-cart/{customer_id}', 'viewCart')->name('lists.view-cart');
        Route::get('/lists/{list}/view-cart/{customer_id}', 'viewCart')->name('lists.view-cart-get-method');
        Route::delete('/cart/remove/{list}/{productId}/{customerId}', 'removeFromCart')->name('cart.remove');
        Route::get('/cart/view/{listId}', 'viewCart')->name('cart.view');
        Route::patch('/cart/update/{list}/{productId}', 'updateqty')->name('cart.updateqty');
        Route::post('/orders/save', 'saveOrder')->name('orders.save');
        Route::get('/list/{listId}/customer/{customerId}', 'showListCustomer')->name('showlistcustomer');
        Route::patch('/orders/{order}/updateQuantity', 'updateQuantity')->name('orders.updateQuantity');
        Route::post('/orders/bulk-update-quantities', 'bulkUpdateQuantities')->name('orders.bulkUpdateQuantities');
        Route::delete('/orders/{order}', 'destroyOrders')->name('orders.destroyOrders');
        // Route::get('/send-email/{list_id}/{customer_id}', 'sendEmail')->name('send.email');
        Route::match(['get', 'post'], '/send-email/{list_id}/{customer_id}', 'sendEmail')
            ->name('send.email');

        Route::get('/get-customers', 'getCustomer')->name('get.customer');
    });

    // Order Routes
    Route::controller(OrdersController::class)->group(function () {
        Route::get('/showorder', 'showallorderdata')->name('showorder');
        Route::get('/viewsingalorders/{listId}', 'viewsingalorders')->name('vieworders');
    });

    // POS Routes
    Route::controller(PosController::class)->group(function () {
        Route::get('/pos', 'index')->name('pos.index');
        Route::get('/pos/products', 'products')->name('pos.products');
        Route::get('/pos/customers', 'customers')->name('pos.customers');
        Route::get('/pos/orders', 'orders')->name('pos.orders.index');
        Route::post('/pos/orders', 'store')->name('pos.orders.store');
    });

    // Category Routes
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/showcategory', 'showallcategory')->name('showcategory');
        Route::get('/addcategory', 'addcategory')->name('addcategory');
        Route::post('/categorystore', 'categorystore')->name('categorystore');
        Route::get('/editcategory/{category}', 'edit')->name('editcategory');
        Route::delete('/destroycategory/{id}', 'destroycategory')->name('destroycategory');
        Route::put('/updatecategory/{category}', 'update')->name('updatecategory');
        Route::get('/categories', 'getCategories')->name('getCategories');
    });

    // Setting Routes
    Route::controller(SettingController::class)->group(function () {
        Route::get('/settings', 'index')->name('settings.index');
        Route::post('/settings', 'update')->name('settings.update');
    });

    //Builder Routes
    Route::resource('user_builders', UserBuilderController::class);

    // Logout Route
    Route::post('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login'); // or redirect('/')
    })->name('logout');
});
