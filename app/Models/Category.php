<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Translatable;
    use HasFactory;
    public $translatedAttributes = ['name'];
    protected $hidden = ['created_at', 'updated_at', 'pivot', 'translations'];
    protected $fillable = ['type'];

    public function sizeTypes()
    {
        return $this->belongsToMany(SizeType::class, 'size_types_categories', 'category_id', 'size_type_id');
    }

    public function getImageAttribute($value)
    {
        return asset('storage/uploads/category/'.$value);
    }
}
