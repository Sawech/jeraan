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
        return $value;

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
