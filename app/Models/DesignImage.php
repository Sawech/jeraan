<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignImage extends Model
{
    use HasFactory;
    protected $table = 'designs_images';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['image'];

    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('storage/uploads/design/'.$value);
        }
    }
}
