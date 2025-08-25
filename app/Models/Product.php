<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->logExcept(['created_at', 'updated_at', 'deleted_at'])
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Product {$eventName}");
    }
}