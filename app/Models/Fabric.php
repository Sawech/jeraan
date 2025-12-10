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
        return asset('storage/uploads/fabric/'.$value);
    }
}
