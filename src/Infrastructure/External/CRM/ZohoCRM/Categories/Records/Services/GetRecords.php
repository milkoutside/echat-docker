<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services;

use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Interfaces\MultipleRecords;
use src\Infrastructure\External\CRM\ZohoCRM\Request\ApiRequest;

class GetRecords extends ApiRequest implements MultipleRecords
{

    private string $module;
    private $page = 1;
    private $perPage = 200;
    private $fields;
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

    public function setPerPage($perPage = 200)
    {
        $this->perPage = $perPage;
        return $this;
    }
    public function setPage($page = 1)
    {
        $this->page = $page;
        return $this;
    }
    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }
    public function apiRequest()
    {
        return $this->setHeaders()->setParams([
            'per_page'=>$this->perPage,
            'page'=>$this->page,
            'fields'=>$this->fields
        ])
            ->request('GET',"{$this->module}");
    }
}
