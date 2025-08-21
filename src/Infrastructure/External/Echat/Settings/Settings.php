<?php

namespace src\Infrastructure\External\Echat\Settings;

use Illuminate\Support\Facades\Config;

class Settings
{
    private static $instance = null;

    private string $apikey = "";
    private string $baseUrl = "";
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
    public function setApiKey(string $apikey): static
    {
        $this->apikey = $apikey;
        return $this;
    }
    public function getApiKey(): string
    {
        return $this->apikey;
    }
    public function setBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setConfigByPhone($phone)
    {
        $config = Config::get('echat.echat_config');
        if(!empty($config[$phone])){
            $this->setApiKey($config[$phone]['apiKey']);
        }else{
            $this->setApiKey($config['380673126106']['apiKey']);
        }
        $this->setApiKey('67bbc866aa3fe');
    }
}
