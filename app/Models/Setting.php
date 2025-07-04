<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'user_id',
    ];

    /**
     * Get the value of a specific setting by its key.
     *
     * @param string $key
     * @return string|null
     */
    public static function getValue($key)
    {
        return static::where('setting_key', $key)
                     ->where('user_id', auth()->id())
                     ->pluck('setting_value')
                     ->first();
    }
}

