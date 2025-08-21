<?php

namespace src\Domain\Entities\Messages;

class Messages
{

    public function __construct(
        public string|null $sender,
        public string|null $text,
        public string|null $channel,
        public string|null $fileUrl,
        public string|null $messageId,
        public string|null $status,
        public string|null $sentAt,
        public string|null $clientId,
        public string|null $sendType,
        public bool|null $messageRead,
        public string|null $error
    ) {}

    /**
     * Преобразует объект в ассоциативный массив
     *
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }



}
