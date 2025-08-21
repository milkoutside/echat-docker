<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Request\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use src\Infrastructure\External\CRM\ZohoCRM\Auth\Auth;
use src\Infrastructure\External\CRM\ZohoCRM\Settings\Core\Settings;
use src\Infrastructure\Helpers\Logger\Logger;

class Request
{
    use Logger;
    private $settings = null;
    private $baseApiUrl = "";

    private string $siteKey = "";

    private array $headers = [];
    private array $params = [];

    private array $body = [];

    public function __construct()
    {
        $this->settings = Settings::getInstance();
        $this->baseApiUrl = "https://www.zohoapis.".$this->settings->domain."/crm/v7/";
    }

    protected function setHeaders($headers = []): static
    {   $headers['Authorization'] = 'Zoho-oauthtoken '.Auth::getInstance()->getToken();
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    protected function setBody($body)
    {
        $this->body = $body;
        $this->body = array_merge($this->body,$body);
        return $this;
    }
    protected function setParams($params)
    {
        $this->params = $params;
        $this->params = array_merge($this->params,$params);
        return $this;
    }
    private function getHttpClient()
    {
        $httpConf = array_merge(
            [
                'base_uri' => $this->baseApiUrl
            ]
        );
        return new Client($httpConf);
    }
    public function setApiVersion($version)
    {
        $this->baseApiUrl = "https://www.zohoapis.".$this->settings->domain."/crm/".$version."/";
        return $this;
    }

    protected function request(string $method, string $endpoint)
    {
        $options = [
            'headers' => $this->headers,
            'query' => $this->params,
        ];
        if (!empty($this->body)) {
            $options['json'] =
                $this->body;
        }
        try {
            $response = $this->getHttpClient()->request($method, $endpoint, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $this->createLog($e->getMessage(),'ZOHO CRM API','ERROR');
            return null;
        }
    }
}
