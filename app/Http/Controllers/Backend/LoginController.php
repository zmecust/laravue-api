<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Cache;

class LoginController extends Controller
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function login(Request $request)
    {
        $token_info = $this->getToken($request);

        if ($token_info['status'] === 0) {
            return $token_info;
        }

        $user_info = $this->getUserInfo($token_info['data']['access_token']);

        if ($user_info['status'] === 0) {
            return $user_info;
        }

        $user = User::find($user_info['data']['id']);

        if (empty($user)) {
            User::create(['id' => $user_info['data']['id']]);
        }

        Cache::put('CMS'.$token_info['data']['access_token'], $user_info['data']['id'], 150000);

        $data = [
            'user' => $user_info['data'],
            'token' => $token_info['data']
        ];
        return $this->responseSuccess('OK', $data);

    }

    public function getToken($request)
    {
        $response = $this->client->post('http://api.laravue.xyz/api/v1/admin/login', [
            'form_params' => [
                'name' => $request->get('name'),
                'password' => $request->get('password'),
            ],
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function getUserInfo($token)
    {
        $response = $this->client->get('http://api.laravue.xyz/api/v1/admin/user/me', [
            'query' => [
                'service_name' => 'CMS',
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $token
            ]
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}