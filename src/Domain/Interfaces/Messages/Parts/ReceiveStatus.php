<?php

namespace src\Domain\Interfaces\Messages\Parts;

interface ReceiveStatus
{
    public function receive($message);
}
