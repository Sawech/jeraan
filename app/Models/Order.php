<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Auth;

class Order extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'fabric_id',
        'design_id',
        'description',
        'deposit_amount',
        'amount',
        'payment_image',
        'status',
        'delivery_date',
        'order_type'
    ];
    protected $hidden = ['updated_at','user_id','category_id','fabric_id','design_id','category','fabric','design']; // ,'deleted_at'

    protected $appends = ['deserved_amount'];
    public function orderDetails()
    {
        return $this->hasMany(OrderSizeGoneOption::class,'order_id');
    }
    
    public function buttons()
    {
        return $this->hasOne(OrderButton::class, 'order_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function fabric()
    {
        return $this->belongsTo(Fabric::class,'fabric_id');
    }

    public function design()
    {
        return $this->belongsTo(Category::class,'design_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function getPaymentImageAttribute($value)
    {
        if ($value) {
            return asset('storage/uploads/payments/'.$value);
        }

    }

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->toDateString();
        }

    }

    public function getDeservedAmountAttribute()
    {
        $deservedAmount =  round(($this->amount - $this->deposit_amount),2);
        return $deservedAmount;
       
    }

public function toArray()
{
    // Only hide email if `guest` or not an `admin`
    if (Auth::user()->role->type != 'user') {
        $this->setAttributeVisibility();
    }

    return parent::toArray();
}

public function setAttributeVisibility()
{
    $this->makeVisible(['created_at', 'user_id', 'category_id', 'fabric_id', 'design_id', 'category', 'fabric', 'design']);
}
}
