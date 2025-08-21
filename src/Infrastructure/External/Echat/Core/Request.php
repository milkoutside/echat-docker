<?php

namespace src\Infrastructure\External\Echat\Core;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use src\Infrastructure\External\Echat\Settings\Settings;
use GuzzleHttp\Exception\ClientException;
class Request
{
    private array $headers = [];
    private array $params = [];

    private array $body = [];


    protected function setHeaders($headers = []): static
    {
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
        return $this;
    }
    private function getHttpClient()
    {
        $httpConf = array_merge(
            [
                'base_uri' => Settings::getInstance()->getBaseUrl()
            ]
        );
        return new Client($httpConf);
    }


    protected function request(string $method, string $endpoint)
    {
        try {
            $response = $this->getHttpClient()->request($method, $endpoint, [
                'headers' => $this->headers,
                'query' => $this->params,
                'json' => $this->body
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {

            return json_decode($e->getResponse()->getBody()->getContents(), true);
        }

    }


}
