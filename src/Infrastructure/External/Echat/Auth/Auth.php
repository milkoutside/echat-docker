<?php

namespace src\Infrastructure\External\Echat\Auth;

use Illuminate\Support\Facades\DB;
use src\Infrastructure\External\Echat\Settings\Settings;

class Auth
{
    private  function getApiKeyBySender($sender,$service){
        return DB::table('echat_api_keys')->where('sender',$sender)
            ->where('service',$service)->first()->api_key;
    }
    public static function setApiKeyBySender($sender,$service){
        Settings::getInstance()->setApiKey(DB::table('echat_api_keys')->where('sender',$sender)
            ->where('service',$service)->first()->api_key);

    }
}
