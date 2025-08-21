<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Actions;

use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services\AddRecordNote;
use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services\CreateRecord;
use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services\GetRecord;
use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services\GetRecordBlueprint;
use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services\GetRecords;
use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services\GetRelatedRecords;
use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services\SearchRecords;
use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services\UpdateRecord;
use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services\UpdateRecordBlueprint;

class RecordsActions
{
    public function getRecord($module, $id)
    {
        $getRecord =  new GetRecord();
        return $getRecord->setModule($module)->setId($id)->apiRequest();
    }
    public function getRecordBlueprint($module, $id)
    {
        $getRecord =  new GetRecordBlueprint();
        return $getRecord->setModule($module)->setId($id)->apiRequest();
    }
    public function getRecords($module, $fields, $page = 1,$perPage = 200)
    {
        $getRecord =  new GetRecords();
        return $getRecord->setModule($module)
            ->setFields($fields)
            ->setPage($page)
            ->setPerPage($perPage)
            ->apiRequest();
    }
    public function getRelatedRecords($module, $parentModule,$id,$fields, $page = 1,$perPage = 200)
    {
        $getRecord =  new GetRelatedRecords();
        return $getRecord->setModule($module)
            ->setParentModule($parentModule)
            ->setId($id)
            ->setFields($fields)
            ->setPage($page)
            ->setPerPage($perPage)
            ->apiRequest();
    }
    public function searchRecords($module, $criteria, $searchValue)
    {
        $getRecord =  new SearchRecords();
        return $getRecord->setModule($module)
            ->setCriteria($criteria)
            ->setSearchValue($searchValue)
            ->apiRequest();
    }
    public function updateRecord($module, $id,$data)
    {
        $updateRecord =  new UpdateRecord();
        return $updateRecord->setModule($module)->setId($id)->setData($data)->apiRequest();
    }
    public function updateRecordBlueprint($module, $id,$data)
    {
        $updateRecord =  new UpdateRecordBlueprint();
        return $updateRecord->setModule($module)->setId($id)->setData($data)->apiRequest();
    }

    public function createNote($module,$id,$subject = null,$content = null)
    {
        $updateRecord =  new AddRecordNote();
        return $updateRecord->setModule($module)->setId($id)->setSubject($subject)
            ->setContent($content)
            ->apiRequest();
    }

    public function createRecord($module,$createData)
    {
        $createRecord = new CreateRecord();
        return $createRecord->setModule($module)->setData($createData)->apiRequest();
    }
}
