<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'list_id',
        'customer_id',
        'product_id',
        'quantity',
        'comment',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function list()
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }
}
