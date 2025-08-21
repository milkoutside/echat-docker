<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services;

use src\Infrastructure\External\CRM\ZohoCRM\Request\ApiRequest;

class CreateRecord extends ApiRequest
{
    private string $module;
    private $data;

    function setModule(string $module)
    {
        $this->module = $module;
        return $this;
    }
    function setData($data)
    {
        $this->data = $data;
        return $this;
    }


    public function apiRequest()
    {
        return $this->setHeaders()->setBody($this->data)
            ->request('POST',$this->module);
    }
}
