<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderButton extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'jaap_num',
        'neck_num',
        'neck_count',
        'japz_num',
        'japz_count',
        'cabk_num',
        'cabk_count',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the order that owns the button details
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}