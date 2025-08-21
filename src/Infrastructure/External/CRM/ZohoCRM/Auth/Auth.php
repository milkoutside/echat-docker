<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Auth;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use src\Infrastructure\External\CRM\ZohoCRM\Settings\Core\Settings;

class Auth
{
    private static $instance = null;


    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function fetchAccessToken()
    {
        $settings = Settings::getInstance();
        $client = new Client();
        $response = $client->post('https://accounts.zoho.'.$settings->domain.'/oauth/v2/token', [
            'form_params' => [
                'refresh_token' => $settings->refreshToken,
                'client_id' => $settings->clientId,
                'client_secret' => $settings->clientSecret,
                'grant_type' => 'refresh_token',
            ]
        ]);

        $body = $response->getBody();
        return json_decode($body->getContents(),true);
    }
    public function getToken()
    {
        $settings = Settings::getInstance();
        $token = DB::table('table_zoho_crm_tokens')
            ->where('refresh_token',$settings->refreshToken)->first();
        if($token != null && $token->expire_at >= Carbon::now()) {
            return $token->access_token;
        }else{
            $newTokensData = $this->fetchAccessToken();
            if(isset($newTokensData['access_token'])){
                if ($token) {
                    // Если запись существует, обновляем ее
                    DB::table('table_zoho_crm_tokens')
                        ->where('refresh_token', $settings->refreshToken)
                        ->update([
                            'access_token' => $newTokensData['access_token'],
                            'expire_at' => Carbon::now()->addSeconds(3600),
                        ]);
                } else {
                    // Если запись не существует, создаем новую
                    DB::table('table_zoho_crm_tokens')->insert([
                        'refresh_token' => $settings->refreshToken,
                        'access_token' => $newTokensData['access_token'],
                        'expire_at' => Carbon::now()->addSeconds(3600),
                    ]);
                }
                return $newTokensData['access_token'];
            }
        }
        return null;
    }

}
