<?php

namespace src\Infrastructure\External\Echat;

use src\Infrastructure\External\Echat\Auth\Auth;
use src\Infrastructure\External\Echat\Messengers\Messengers;
use src\Infrastructure\External\Echat\Settings\Settings;

class EchatApi
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
    public function __construct()
    {
    }

    public function settings()
    {
        return Settings::getInstance();
    }
    public function api()
    {
        return Messengers::getInstance();
    }
}
