<?php

namespace src\Infrastructure\Mappers\Clients;

use src\Domain\Entities\Clients\Clients;
use src\Domain\Entities\Messages\Messages;
use src\Infrastructure\DB\Messages\DBMessage;
use src\Infrastructure\Mappers\IMapper;

class EchatIncomingViberMessageClientMapper implements IMapper
{

    public static function toDomain(array|object $object)
    {
        if (is_array($object)) {
            return new Clients(
                 null,
                !empty($object['contact']['number']) ? preg_replace('/\D/', '', $object['contact']['number'] ): null ,
                null ,

            );
        }
        // Если входной параметр — объект
        if (is_object($object)) {
            return new Clients(
                null,
                !empty($object->contact->number) ? preg_replace('/\D/', '',  $object->contact->number ): null ,
                null ,

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
