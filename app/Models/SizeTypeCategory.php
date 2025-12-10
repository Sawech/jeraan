<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeTypeCategory extends Model
{
    use HasFactory;
    protected $table = 'size_types_categories';
    protected $with = ['category', 'sizeType'];
    protected $hidden = ['created_at', 'updated_at'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function sizeType()
    {
        return $this->belongsTo(SizeType::class, 'size_type_id');
    }
}
