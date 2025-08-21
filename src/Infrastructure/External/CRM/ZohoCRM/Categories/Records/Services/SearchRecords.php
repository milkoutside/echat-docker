<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services;

use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Interfaces\MultipleSearch;
use src\Infrastructure\External\CRM\ZohoCRM\Request\ApiRequest;

class SearchRecords extends ApiRequest implements MultipleSearch
{

    private string $module;
    private string $criteria;
    private string $searchValue;

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
    public function setCriteria(string $criteria)
    {
        $this->criteria = $criteria;
        return $this;
    }
    public function setSearchValue(string $searchValue)
    {
        $this->searchValue = $searchValue;
        return $this;
    }
    public function apiRequest()
    {
        return $this->setHeaders()->setParams([
            $this->criteria=>$this->searchValue
        ])
            ->request('GET',"{$this->module}/search");
    }

}
