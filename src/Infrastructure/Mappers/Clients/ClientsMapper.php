<?php

namespace src\Infrastructure\Mappers\Clients;

use src\Domain\Entities\Clients\Clients;
use src\Infrastructure\DB\Clients\DBClients;
use src\Infrastructure\Mappers\IMapper;

class ClientsMapper implements IMapper
{

    public static function toDomain(array|object $object)
    {
        // Если входной параметр — массив
        if (is_array($object) && isset($object[0])) {
            $clientsArray = [];
            foreach ($object as $client) {
                $clientsArray[] = new Clients(
                    $client['id'] ?? null,
                    $client['phone'] ?? null,
                    $client['username'] ?? null
                );
            }
            return $clientsArray;
        }
        if (is_array($object)) {
            return new Clients(
                $object['id'] ?? null,
                    $object['phone'] ?? null,
                    $object['username'] ?? null
            );
        }
        // Если входной параметр — объект
        if (is_object($object)) {
            return new Clients(
                $object->id ?? null,
                $object->phone ?? null,
                    $object->username ?? null
            );
        }

        // Если формат неподходящий, возвращаем null
        return null;
    }

    public static function toEntity(array|object $object)
    {
        // Если входной параметр — массив
        if (is_array($object) && isset($object[0])) {
            $clientsArray = [];
            foreach ($object as $client) {
                $clientsArray[] = new DBClients([
                        'id' => $client['id'] ?? null,
                        'phone' => $client['phone'] ?? null,
                        'username' => $client['username'] ?? null,
                    ]
                );
            }
            return $clientsArray;
        }
        if (is_array($object)) {
            return new DBClients([
                    'id' => $object['id'] ?? null,
                    'phone' => $object['phone'] ?? null,
                    'username' => $object['username'] ?? null,
                ]
            );
        }
        // Если входной параметр — объект
        if (is_object($object)) {
            return new DBClients([
                    'id' => $object->id ?? null,
                    'phone' => $object->phone ?? null,
                    'username' => $object->username ?? null,
                ]
            );
        }

        // Если формат неподходящий, возвращаем null
        return null;
    }
}
