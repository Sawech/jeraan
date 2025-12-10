<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeTypeCategoryUser extends Model
{
    use HasFactory;
    protected $table = 'size_types_categories_users';
    protected $hidden = ['created_at', 'updated_at','size_type_category_id'];
    protected $fillable = [
        'size_type_category_id',
        'user_id',
        'value',
    ];

    public function sizeTypeCategory()
    {
        return $this->belongsTo(SizeTypeCategory::class, 'size_type_category_id');
    }
}
