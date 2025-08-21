<?php

namespace src\Infrastructure\External\Echat\Messengers;

use src\Infrastructure\External\Echat\Messengers\Telegram\Actions\TelegramActions;
use src\Infrastructure\External\Echat\Messengers\Viber\Actions\ViberActions;
use src\Infrastructure\External\Echat\Messengers\WhatsApp\Actions\WhatsActions;
use src\Infrastructure\External\Echat\Settings\Settings;

class Messengers
{
    private static  $instance = null;

    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function telegram(){
        Settings::getInstance()->setBaseUrl("https://telegram.e-chat.tech/api/");
        return TelegramActions::getInstance();
    }
    public function viber(){
        Settings::getInstance()->setBaseUrl("https://e-chat.tech/api/viber/v2/");
        return ViberActions::getInstance();
    }
    public function whatsapp(){
        Settings::getInstance()->setBaseUrl("https://e-chat.tech/api/whatsapp/v1/");
        return WhatsActions::getInstance();
    }
}
