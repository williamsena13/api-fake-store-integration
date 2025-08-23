<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

abstract class BaseModel extends Model
{
    use SoftDeletes, LogsActivity;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (in_array('external_id', $model->getFillable()) && empty($model->external_id)) {
                $model->external_id = $model->generateExternalId();
            }
        });
    }

    protected function generateExternalId(): string
    {
        return Str::uuid()->toString();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->logExcept(['created_at', 'updated_at'])
            ->dontSubmitEmptyLogs();
    }
}
