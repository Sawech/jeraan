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
        return $this->belongsToMany(
            SizeType::class,
            'size_types_categories',
            'category_id',
            'size_type_id'
        )->withPivot('id');
    }

    
    public function getImageAttribute($value)
{
    // If it's already a full URL (from Cloudinary), return as is
    if (filter_var($value, FILTER_VALIDATE_URL)) {
        return $value;
    }
    
    // Fallback for old local images
    return $value ? asset('storage/uploads/category/' . $value) : null;
}
    
    // Add this to get user-specific size values
    public function sizeTypesWithUserValues($userId)
    {
        return $this->sizeTypes()->with(['sizeTypeCategoryUsers' => function($query) use ($userId) {
            $query->where('user_id', $userId);
        }]);
    }
}
