<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories;
use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Actions\RecordsActions;

class Categories
{
    private static $instance = null;

    public static function getInstance(): ?Categories
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

   public function records()
   {
       return new RecordsActions();
   }
}
