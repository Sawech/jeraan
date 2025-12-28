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
        return $value;
    }
}
