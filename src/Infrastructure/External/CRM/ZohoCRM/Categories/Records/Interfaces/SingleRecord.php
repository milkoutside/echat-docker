<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Interfaces;

interface SingleRecord
{
    function setModule(string $module);
    function setId($id);

    public function apiRequest();
}
