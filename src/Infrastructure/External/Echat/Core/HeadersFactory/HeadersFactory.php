<?php

namespace src\Infrastructure\External\Echat\Core\HeadersFactory;

use src\Infrastructure\External\Echat\Core\HeadersFactory\Headers\TelegramHeaders;
use src\Infrastructure\External\Echat\Core\HeadersFactory\Headers\ViberHeaders;
use src\Infrastructure\External\Echat\Core\HeadersFactory\Interfaces\IHeadersFactory;

class HeadersFactory
{
    public static function create(string $messenger): IHeadersFactory
    {
        return match ($messenger) {
            'telegram' => new TelegramHeaders(),
            'viber' => new ViberHeaders(),
            default => throw new \InvalidArgumentException("Unsupported messenger: $messenger"),
        };
    }


}
