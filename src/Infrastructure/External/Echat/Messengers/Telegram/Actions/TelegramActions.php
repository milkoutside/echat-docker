<?php

namespace src\Infrastructure\External\Echat\Messengers\Telegram\Actions;

use src\Application\Responses\MessangerSendedResponse;
use src\Infrastructure\External\Echat\Messengers\Telegram\Services\SendMessage;


class TelegramActions
{
    private static $instance = null;
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function sendMessage($messageData){
        $sendMessageService = new SendMessage();
        $response = $sendMessageService
            ->setData($messageData)
            ->apiRequest();
        return new MessangerSendedResponse(
            null,
            null,
            $response['status'],
            $response['description']
        );
    }
}
