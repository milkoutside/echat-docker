<?php

namespace src\Domain\Entities\Clients;

class Clients
{
    public function __construct(
        public ?int $id = 0,
        public ?array $phones, // Массив телефонов по сервисам
        public ?string $username,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    // Метод для обратной совместимости (если нужно)
    public function getPhone(): ?string
    {
        return $this->phones['telegram'] ?? $this->phones['viber'] ?? $this->phones['whatsapp'] ?? null;
    }
}
