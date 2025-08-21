<?php

namespace src\Domain\Interfaces\Messages;

use src\Application\Responses\MessangerSendedResponse;
use src\Domain\Entities\Clients\Clients;
use src\Domain\Entities\Messages\Messages;

interface IMessageServiceStrategy
{
    public function send(Messages $message, Clients $client) : MessangerSendedResponse;
    public function receive(Messages $message);


}
