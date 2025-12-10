<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSizeGoneOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'size_gown_option_id',
        'value'

    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $table = 'orders_size_gowns_options';
}
