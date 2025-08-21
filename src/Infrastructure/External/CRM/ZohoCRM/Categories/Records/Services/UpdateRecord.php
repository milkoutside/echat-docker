<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services;

use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Interfaces\SingleRecord;
use src\Infrastructure\External\CRM\ZohoCRM\Request\ApiRequest;

class UpdateRecord extends ApiRequest implements SingleRecord
{
    private string $module;

    private $id;
    private $data;
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
    function setData($data)
    {
        $this->data = $data;
        return $this;
    }


    public function apiRequest()
    {
        return $this->setHeaders()->setBody($this->data)
            ->request('PUT',"{$this->module}/{$this->id}");
    }
}
