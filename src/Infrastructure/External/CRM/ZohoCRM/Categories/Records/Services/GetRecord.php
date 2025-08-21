<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services;

use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Interfaces\SingleRecord;
use src\Infrastructure\External\CRM\ZohoCRM\Request\ApiRequest;

class GetRecord extends ApiRequest implements SingleRecord
{

    private string $module;

    private $id;
    function setModule(string $module)
    {
        $this->module = $module;
        return $this;
    }

    function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function apiRequest()
    {
        $response = $this->setHeaders()->setParams([])
            ->request('GET',"{$this->module}/{$this->id}");
        return !empty($response['data'][0])? $response['data'][0] : null;
    }
}
