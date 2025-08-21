<?php

namespace src\Infrastructure\Helpers\Messages;

class GenerateMessageIdHelper
{
    public static function generate($service,$clientId){
        return "{$service}_{$clientId}_" . time();
    }
}
