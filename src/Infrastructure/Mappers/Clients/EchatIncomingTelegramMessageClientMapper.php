<?php

namespace src\Infrastructure\Mappers\Clients;

use src\Domain\Entities\Clients\Clients;
use src\Infrastructure\Mappers\IMapper;

class EchatIncomingTelegramMessageClientMapper implements IMapper
{
    public static function toDomain(array|object $object)
    {
        $phone = null;
        $username = null;

        if (is_array($object)) {
            $phone = !empty($object['sender']['phone']) ? preg_replace('/\D/', '', $object['sender']['phone']) : null;
            $username = $object['sender']['username'] ?? null;
        } elseif (is_object($object)) {
            $phone = !empty($object->sender->phone) ? preg_replace('/\D/', '', $object->sender->phone) : null;
            $username = $object->sender->username ?? null;
        }

        return new Clients(
            null,
            $phone ? ['telegram' => $phone] : null,
            $username
        );
    }

    public static function toEntity(array|object $object)
    {
        throw new \Exception('Not implemented');
    }
}
