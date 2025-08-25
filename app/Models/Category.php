<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;

class Category extends BaseModel
{
    protected $fillable = ['name', 'external_id'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->logExcept(['created_at', 'updated_at', 'deleted_at'])
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Category {$eventName}");
    }
}