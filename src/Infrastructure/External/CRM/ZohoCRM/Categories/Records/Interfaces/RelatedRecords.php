<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Interfaces;

interface RelatedRecords
{
    public function setModule(string $module);
    public function setParentModule(string $module);
    public function setId($id);

    public function apiRequest();
}
