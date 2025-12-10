<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Language extends Model
{
    use Translatable;
    use HasFactory;
    public $translatedAttributes = ['name'];
    protected $hidden = ['created_at', 'updated_at','translations'];
}
