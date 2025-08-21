<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Settings\Core;

use Illuminate\Support\Facades\Config;

class Settings
{
    private static $instance = null;

    public string $domain = "";
    public string $refreshToken = "";
    public string $clientId = "";
    public string $clientSecret = "";

    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param null $instance
     */
    public function setByName(string $apikey)
    {
        $this->apikey = $apikey;
        return $this;
    }
    public function setByConfig()
    {
        $config = Config::get('zohocrm.zohocrmconfig');
        $this->domain = $config['domain'];
        $this->clientId = $config['clientId'];
        $this->clientSecret = $config['clientSecret'];
        $this->refreshToken = $config['refreshToken'];
        return $this;
    }
}
