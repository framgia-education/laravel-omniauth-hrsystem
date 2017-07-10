<?php

namespace Tests\Fixtures;

use Framgia\Education\Auth\Providers\FramgiaProvider;
use Framgia\Education\Auth\Providers\User;
use Mockery as m;

class FramgiaTestProviderStub extends FramgiaProvider
{
    public $http;

    /**
     * @inheritdoc
     */
    protected function getUserByToken($token)
    {
        return [
            'email' => 'test@framgia.com',
            'avatar' => 'http://lorempixel.com/400/400/',
            'name' => '___ Name ___',
            'authentication_token' => '___ authentication_token ___',
            'gender' => 0,
            'role' => 'normal',
            'birthday' => null,
            'employee_code' => 'B123',
            'position' => null,
            'contract_date' => null,
            'status' => 0,
            'phone_number' => null,
            'contract_type' => null,
            'university' => null,
            'join_date' => null,
            'resigned_date' => null,
        ];
    }

    /**
     * Get a fresh instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        if ($this->http) {
            return $this->http;
        }

        return $this->http = m::mock('StdClass');
    }
}
