<?php

namespace src\Infrastructure\External\CRM\ZohoCRM;

use src\Infrastructure\External\CRM\ZohoCRM\Categories\Categories;
use src\Infrastructure\External\CRM\ZohoCRM\Settings\Core\Settings;

class ZohoCRMApi
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
    public function __construct()
    {
        Settings::getInstance()->setByConfig();
    }

    public function settings()
    {
        return Settings::getInstance();
    }
    public function api()
    {
        return new Categories();
    }
}
