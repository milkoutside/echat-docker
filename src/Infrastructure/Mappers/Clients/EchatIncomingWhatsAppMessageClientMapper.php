<?php

namespace src\Infrastructure\Mappers\Clients;

use src\Domain\Entities\Clients\Clients;
use src\Domain\Entities\Messages\Messages;
use src\Infrastructure\DB\Messages\DBMessage;
use src\Infrastructure\Mappers\IMapper;

class EchatIncomingWhatsAppMessageClientMapper implements IMapper
{

    public static function toDomain(array|object $object)
    {
        $username = null;
        $phone = null;
        if (is_array($object)) {
            $phone = !empty($object['contact']['number']) ? preg_replace('/\D/', '', $object['contact']['number']) : null;

        } elseif (is_object($object)) {
            $phone = !empty($object->contact->number) ? preg_replace('/\D/', '', $object->contact->number) : null;

        }
        return new Clients(
            null,
            $phone ? ['whatsapp' => $phone] : null ,
            null ,

        );
    }

    public static function toEntity(array|object $object)
    {
        // Если входной параметр — массив
       throw new \Exception('Not implemented');
    }
}
