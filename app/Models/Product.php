<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_category',
        'product_name',
        'product_description',
        'product_code',
        'product_stock',
        'product_image', // Add this line
        'delete_status',
        'admin_user_id',

    ];
}
