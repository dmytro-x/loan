<?php

namespace App\Modules\Credit\Infrastructure;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EloquentCreditModel extends Model
{
    protected $table = 'credits';

    protected $fillable = [
        'id','client_id','name','amount','rate','start_date','end_date', 'is_approved', 'rejection_reasons',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rejection_reasons' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::ulid();
            }
        });
    }
}
