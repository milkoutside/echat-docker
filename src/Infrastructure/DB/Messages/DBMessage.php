<?php

namespace src\Infrastructure\DB\Messages;

use Illuminate\Database\Eloquent\Model;

class DBMessage extends Model
{
    protected $table = 'messages';

    /**
     * Атрибуты, которые можно массово заполнять
     *
     * @var array
     */
    protected $fillable = [
        'sender',
        'text',
        'channel',
        'fileUrl',
        'message_id',
        'status',
        'send_time',
        'client_id',
        'send_type',
        'message_read',
        'error'
    ];

    protected $guarded = [];

    protected $casts = [
        'message_read' => 'boolean',
        'send_time' => 'datetime',
    ];
}
