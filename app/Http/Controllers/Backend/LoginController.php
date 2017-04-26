<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Api\ApiController;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Cache;

class LoginController extends ApiController
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function login(Request $request)
    {
        $response = $this->client->post('http://localhost/UCenter/public/api/v1/user/login', [
            'form_params' => [
                'login' => $request->get('login'),
                'password' => $request->get('password'),
                'service_name' => 'laravue-backend',
            ],
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        $user_info =  json_decode((string) $response->getBody(), true);

        if ($user_info['status'] === 0) {
            return $user_info;
        } else {
            $jwt_token = $user_info['data']['jwt_token'];
            Cache::put('CMS'.$jwt_token['access_token'], $user_info['data']['id'], $jwt_token['expires_in']);
            return $user_info;
        }
    }
}