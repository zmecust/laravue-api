<?php

namespace App\Http\Controllers;

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
        $this->client->get(self::GET_CODE, [
            'query' => [
                'redirect_uri' => config('services.github.redirect'),
                'client_id' => config('services.github.client_id'),
                'response_type' => 'code',
                'state' => str_random(10),
            ],
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
            ]
        ]);
    }

    public function githubLogin()
    {
        $response = $this->client->post(self::GET_ACCESS_TOKEN, [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => config('services.github.client_id'),
                'client_secret' => config('services.github.client_secret'),
                'code' => request('code'),
                'redirect_uri' => config('services.github.redirect')
            ],
        ]);

        $access_token = json_decode((string) $response->getBody(), true)['access_token'];

        $response = $this->client->get(self::GET_USER_INFO . $access_token, [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
            ],
        ]);

        $githubUser = json_decode((string) $response->getBody(), true);
        dd($githubUser);
        $user_names = User::pluck('name')->toArray();

        if (in_array($githubUser->getNickname(), $user_names)) {
            $user = User::where('name', $githubUser->getNickname())->first();
        } else {
            $user = User::create([
                'name' => $githubUser->getNickname(),
                'avatar' => $githubUser->getAvatar(),
                'email' => $githubUser->getEmail(),
                'password' => $githubUser->getToken(),
                'is_confirmed' => 1,
                'confirm_code' => str_random(60)
            ]);
            $user->attachRole(3);
        }
        $token = JWTAuth::fromUser($user);
        $user->jwt_token = [
            'access_token' => $token,
            'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
        ];
        return redirect('https://laravue.org/#/github/login')->cookie('user', $user, 24*365);
    }
}
