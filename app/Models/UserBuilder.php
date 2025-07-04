<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBuilder extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_user_id','builder_name', 'contact_email'
    ];
}
