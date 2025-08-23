<?php

namespace App\Models;

class Product extends BaseModel
{
    protected $fillable = [
        'external_id',
        'title',
        'description',
        'price',
        'image_url',
        'category_id'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}