<?php

namespace src\Domain\Services\Messages;

use src\Application\Responses\MessangerSendedResponse;
use src\Domain\Entities\Messages\Messages;
use src\Domain\Interfaces\Messages\IMessageServiceStrategy;
use src\Infrastructure\Helpers\Messages\GenerateMessageIdHelper;

class MessageServiceContext
{
    private array $strategies = [];

    public function addStrategy(string $service, IMessageServiceStrategy $strategy): void
    {
        $this->strategies[$service] = $strategy;
    }

    public function sendMessage(Messages $message,$client) : MessangerSendedResponse
    {

        $service = $message->channel;
        $this->checkStrategy($service);
        $message->messageId = GenerateMessageIdHelper::generate($service,$message->clientId);
        $message->sentAt = now('Europe/Kiev');
        return $this->strategies[$service]->send($message,$client);
    }

    public function receiveMessageStatus($message){
        $service = $message->channel;
        $this->checkStrategy($service);
        return $this->strategies[$service]->receive($message);
    }

    private function checkStrategy($service){
        if (!array_key_exists($service, $this->strategies)) {
            throw new \Exception("Unknown service: {$service}");
        }
    }

}
