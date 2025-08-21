<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services;

use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Interfaces\SingleRecord;
use src\Infrastructure\External\CRM\ZohoCRM\Request\ApiRequest;

class GetRecordBlueprint extends ApiRequest implements SingleRecord
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
        return $this->setHeaders()->setParams([])
            ->request('GET',"{$this->module}/{$this->id}/actions/blueprint");
    }
}
