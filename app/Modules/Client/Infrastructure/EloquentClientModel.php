<?php

namespace App\Modules\Client\Infrastructure;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EloquentClientModel extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'id','name','age','region','pin','email','phone','income','score',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'income' => 'decimal:2',
        'pin' => 'encrypted',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::ulid();
            }
        });
    }

    protected static function newFactory()
    {
        return ClientFactory::new();
    }
}
