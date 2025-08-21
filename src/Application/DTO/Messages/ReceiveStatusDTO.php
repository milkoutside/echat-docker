<?php

namespace src\Application\DTO\Messages;

class ReceiveStatusDTO
{
    public function __construct(
        public string $messageId,
        public string $status,
        public $statusId,
        public string $messenger,
        public string $event,
    ) {}
}
