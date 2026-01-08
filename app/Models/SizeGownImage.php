<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SizeGownImage extends Model
{
    use HasFactory;
    protected $table = 'size_gowns_images';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['image'];

    public function getImageAttribute($value)
{
    // If it's already a full URL (from Cloudinary), return as is
    if (filter_var($value, FILTER_VALIDATE_URL)) {
        return $value;
    }
    
    // Fallback for old local images
    return $value ? asset('storage/uploads/sizeGown/' . $value) : null;
}
}
