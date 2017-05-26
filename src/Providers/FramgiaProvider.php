<?php

namespace Framgia\Education\Auth\Providers;

use Exception;
use Illuminate\Support\Arr;

class FramgiaProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Base OAuth url
     *
     * @var string
     */
    protected $baseUrl = 'http://auth.framgia.vn';

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['email'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/auth/hr_system/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->baseUrl . '/auth/hr_system/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $userUrl = $this->baseUrl . '/me' . '?access_token=' . $token;

        $response = $this->getHttpClient()->get($userUrl);

        $user = json_decode($response->getBody(), true);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => Arr::get($user, 'employee_code'),
            'avatar' => Arr::get($user, 'avatar'),
            'name' => Arr::get($user, 'name'),
            'email' => Arr::get($user, 'email'),
            'gender' => Arr::get($user, 'gender'),
            'birthday' => Arr::get($user, 'birthday'),
            'phoneNumber' => Arr::get($user, 'phone_number'),
        ]);
    }
}
