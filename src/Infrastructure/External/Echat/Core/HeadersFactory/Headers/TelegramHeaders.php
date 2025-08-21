<?php

namespace src\Infrastructure\External\Echat\Core\HeadersFactory\Headers;

use src\Infrastructure\External\Echat\Core\HeadersFactory\Interfaces\IHeadersFactory;
use src\Infrastructure\External\Echat\Settings\Settings;

class TelegramHeaders implements IHeadersFactory
{
    public function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'API' => Settings::getInstance()->getApiKey()
        ];

    }
}
