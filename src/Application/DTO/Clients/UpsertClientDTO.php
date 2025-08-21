<?php

namespace src\Application\DTO\Clients;

class UpsertClientDTO
{

    public function __construct(
        public ?array $phones,
        public ?string $username
    ) {}
}
