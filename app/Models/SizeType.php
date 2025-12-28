<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Auth;

class SizeType extends Model
{
    use Translatable;
    use HasFactory;
    
    public $translatedAttributes = ['name'];
    // Keep the original $with if other pages need it
    protected $with = ['sizeTypeUser'];
    protected $hidden = ['created_at', 'updated_at','pivot','translations'];

    public function categories(){
        return $this->belongsToMany(Category::class,'size_types_categories','size_type_id','category_id')
            ->withPivot('id');    
    }

    // Keep this for backward compatibility
    public function sizeTypeUser(){
        return $this->hasMany(SizeTypeCategoryUser::class,'size_type_category_id')->where('user_id',Auth::id());    
    }

    public function sizeTypeAdmin(){
        return $this->hasMany(SizeTypeCategoryUser::class,'size_type_category_id')->where('user_id',Auth::id());    
    }

    public function sizeTypeCategoryUsers(){
        return $this->hasManyThrough(
            SizeTypeCategoryUser::class,
            SizeTypeCategory::class,
            'size_type_id',           
            'size_type_category_id',  
            'id',                     
            'id'                      
        );
    }
    
    // Add this new method to load user values for a specific category
    public function loadUserValuesForCategory($categoryId, $userId)
    {
        $pivotRecord = \DB::table('size_types_categories')
            ->where('category_id', $categoryId)
            ->where('size_type_id', $this->id)
            ->first();

        if ($pivotRecord) {
            $userValues = SizeTypeCategoryUser::where('size_type_category_id', $pivotRecord->id)
                ->where('user_id', $userId)
                ->select('id', 'value', 'user_id')
                ->get();
            
            return $userValues;
        }

        return collect([]);
    }
}