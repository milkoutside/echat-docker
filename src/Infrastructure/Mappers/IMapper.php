<?php

namespace src\Infrastructure\Mappers;

interface IMapper
{
    public static function toDomain(array|object $object);
    public static function toEntity(array|object $object);
}
