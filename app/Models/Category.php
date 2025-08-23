<?php

namespace App\Models;

class Category extends BaseModel
{
    protected $fillable = ['name', 'external_id'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}