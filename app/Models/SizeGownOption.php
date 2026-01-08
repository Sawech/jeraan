<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class SizeGownOption extends Model
{
    use Translatable;
    use HasFactory;
    protected $table = 'size_gowns_options';
    public $translatedAttributes = ['name'];
    protected $hidden = ['created_at', 'updated_at', 'translations'];
    protected $fillable = ['iamge'];

    public function getImageAttribute($value)
{
    // If it's already a full URL (from Cloudinary), return as is
    if (filter_var($value, FILTER_VALIDATE_URL)) {
        return $value;
    }
    
    // Fallback for old local images
    return $value ? asset('storage/uploads/sizeGown/' . $value) : null;
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
    $this->makeVisible(['translations']);
}
}
