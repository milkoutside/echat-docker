<?php

namespace src\Infrastructure\Mappers\Messages;

use src\Domain\Entities\Messages\Messages;
use src\Infrastructure\DB\Messages\DBMessage;
use src\Infrastructure\Mappers\IMapper;

class EchatIncomingWhatsAppMessageMapper implements IMapper
{

    public static function toDomain(array|object $object)
    {
        if (is_array($object)) {
            return new Messages(
                $object['number'] ?? null,
                !empty($object['message']['text']) ? $object['message']['text'] : null ,
                'whatsapp',
                    !empty($object['message']['file']) ? $object['message']['file'] : null,
                !empty($object['message']['message_id']) ? $object['message']['message_id'] : null,
                 'success' ,
                now('Europe/Kiev'),
                 null,
                 $object['direction'],
                 false,
                ''
            );
        }
        // Если входной параметр — объект
        if (is_object($object)) {
            return new Messages(
                $object->number ?? null,
                !empty($object->message->text) ? $object->message->text : null,
                'whatsapp',
                !empty($object->message->file) ? $object->message->file : null,
                !empty($object->message->message_id) ? $object->message->message_id : null,
                 'success',
                now('Europe/Kiev'),
                 null,
                    $object->direction,
                 false,
                ''
            );
        }

        return null;
    }

    public static function toEntity(array|object $object)
    {
        // Если входной параметр — массив
       throw new \Exception('Not implemented');
    }
}
