<?php

namespace src\Domain\Interfaces\Messages\Parts;

interface SendMessage
{
    public function send($messageData);
}
