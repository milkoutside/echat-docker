<?php

namespace src\Infrastructure\External\Echat\Messengers\Viber\Services;

use src\Infrastructure\External\Echat\Core\HeadersFactory\HeadersFactory;
use src\Infrastructure\External\Echat\Core\Request;

class SendMessage extends Request
{
    private $messageData = [];


    public function setData($messageData)
    {
        $this->messageData = $messageData;

        return $this;
    }

    public function apiRequest()
    {
        return $this
            ->setParams([])->setBody($this->messageData)
            ->setHeaders(
                HeadersFactory::create('viber')->getHeaders()
            )
            ->request('POST','messages/send');
    }
}

