<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Interfaces;

interface MultipleRecords
{
    public function setModule(string $module);
    public function apiRequest();
}
