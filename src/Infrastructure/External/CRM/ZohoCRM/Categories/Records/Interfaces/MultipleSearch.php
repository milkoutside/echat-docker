<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Interfaces;

interface MultipleSearch
{
    public function setModule(string $module);
    public function setCriteria(string $criteria);

    public function apiRequest();
}
