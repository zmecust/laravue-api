<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;

class GithubLoginController extends Controller
{

    const GET_USER_INFO = 'https://api.github.com/user?access_token=';

    const GET_CODE  = 'https://github.com/login/oauth/authorize';

    const GET_ACCESS_TOKEN  = 'https://github.com/login/oauth/access_token';

    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function github()
    {
        $codeFields = [
            'redirect_uri' => config('services.github.redirect'),
            'client_id' => config('services.github.client_id'),
            'response_type' => 'code',
            'state' => str_random(10)
        ];

        return redirect(self::GET_CODE . '?' . http_build_query($codeFields, '', '&', PHP_QUERY_RFC1738));
    }

    /**
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function githubLogin()
    {
        $response = $this->client->post(self::GET_ACCESS_TOKEN, [
            'form_params' => [
                'client_id' => config('services.github.client_id'),
                'client_secret' => config('services.github.client_secret'),
                'code' => request('code'),
                'redirect_uri' => config('services.github.redirect')
            ],
            'headers' => ['Accept' => 'application/json']
        ]);

        $body = json_decode((string) $response->getBody(), true);

        if (empty($body['access_token'])) {
            return $this->responseError('Authorize Failed: ' . json_encode($body, JSON_UNESCAPED_UNICODE));
        }

        return redirect('https://laravue.org/#/github/login?token=' . $body['access_token']);
    }

    /**
     * @return $this
     */
    public function getUserInfo()
    {
        $response = $this->client->get(self::GET_USER_INFO . request('access_token'));
        $githubUser = json_decode((string) $response->getBody(), true);

        $user_names = User::pluck('name')->toArray();
        if (in_array($githubUser['login'], $user_names)) {
            $user = User::where('name', $githubUser['login'])->first();
        } else {
            $user = User::create([
                'name' => $githubUser['login'],
                'avatar' => $githubUser['avatar_url'],
                'email' => $githubUser['email'],
                'password' => str_random(40),
                'is_confirmed' => 1,
                'confirm_code' => str_random(60)
            ]);
            $user->attachRole(3);
        }
        $token = \JWTAuth::fromUser($user);
        $user->jwt_token = [
            'access_token' => $token,
            'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
        ];

        return $this->responseSuccess('登录成功', $user);
    }
}
