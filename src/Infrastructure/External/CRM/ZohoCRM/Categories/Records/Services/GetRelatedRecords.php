<?php

namespace src\infrastructure\external\crm\ZohoCRM\Categories\Records\Services;

use src\infrastructure\external\crm\ZohoCRM\Categories\Records\Interfaces\RelatedRecords;
use src\infrastructure\external\crm\ZohoCRM\Categories\Records\Interfaces\SingleRecord;
use src\infrastructure\external\crm\ZohoCRM\Request\ApiRequest;

class GetRelatedRecords extends ApiRequest implements RelatedRecords
{

    private string $module;
    private string $parentModule;

    private $id;

    private $page = 1;
    private $perPage = 200;

    private $fields;


    public function setModule(string $module)
    {
        $this->module = $module;
        return $this;
    }

    public function setParentModule(string $module)
    {
        $this->parentModule = $module;
        return $this;
    }

    public function setId($id)
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
            'fields'=>$this->fields
        ])
            ->request('GET',"{$this->parentModule}/{$this->id}/{$this->module}");

    }

}
