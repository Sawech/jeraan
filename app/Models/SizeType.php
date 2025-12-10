<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Auth;

class SizeType extends Model
{
    use Translatable;
    use HasFactory;
    public $translatedAttributes = ['name'];
    protected $with = ['sizeTypeUser'];
    protected $hidden = ['created_at', 'updated_at','pivot','translations'];

    public function categories(){
        return $this->belongsToMany(Category::class,'size_types_categories','size_type_id','category_id');    
    }

    public function sizeTypeUser(){
        return $this->hasMany(SizeTypeCategoryUser::class,'size_type_category_id')->where('user_id',Auth::id());    
    }

    public function sizeTypeAdmin(){
        return $this->hasMany(SizeTypeCategoryUser::class,'size_type_category_id')->where('user_id',Auth::id());    
    }

    
}
