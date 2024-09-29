<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
        "image",
        "is_active",
        "amount",
        "slug"
    ];

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;

        //Generate Slug from Title
        $slug = Str::slug($value);
        $this->attributes['slug'] = $slug;
    }
}
