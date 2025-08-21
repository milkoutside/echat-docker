<?php

namespace src\Infrastructure\DB\Clients;

use Illuminate\Database\Eloquent\Model;

class DBClients extends Model
{
    protected $table = 'clients';

    /**
     * Атрибуты, которые можно массово заполнять
     *
     * @var array
     */
    protected $fillable = [
        'phones',
        'username'
    ];

    protected $guarded = [];

    /**
     * Касты для атрибутов модели
     *
     * @var array
     */
    protected $casts = [
        'phones' => 'array',
    ];
}
