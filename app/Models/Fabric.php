<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Fabric extends Model
{
    use Translatable;
    use HasFactory;
    public $translatedAttributes = ['name','description','title','raw_material','supplier','item','color','source_country','type'];
    protected $hidden = ['created_at', 'updated_at','translations'];

    public function getImageAttribute($value)
{
    // If it's already a full URL (from Cloudinary), return as is
    if (filter_var($value, FILTER_VALIDATE_URL)) {
        return $value;
    }
    
    // Fallback for old local images
    return $value ? asset('storage/uploads/fabric/' . $value) : null;
}
}
