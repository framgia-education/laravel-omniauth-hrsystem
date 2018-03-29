<?php

namespace Tests;

use Mockery as m;
use Illuminate\Http\Request;
use GuzzleHttp\ClientInterface;
use PHPUnit_Framework_TestCase;
use Tests\Fixtures\FramgiaTestProviderStub;

class FramgiaAuthTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRedirectGeneratesTheProperIlluminateRedirectResponse()
    {
        $request = Request::create('foo');
        $request->setLaravelSession($session = m::mock('Illuminate\Contracts\Session\Session'));
        $session->shouldReceive('put')->once();
        $provider = new FramgiaTestProviderStub($request, 'client_id', 'client_secret', 'redirect');
        $response = $provider->redirect();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertSame('http://auth.framgia.vn/auth/hr_system/authorize', $response->getTargetUrl());
    }

    public function testUserReturnsAUserInstanceForTheAuthenticatedRequest()
    {
        $request = Request::create('foo', 'GET', ['state' => str_repeat('A', 40), 'code' => 'code']);
        $request->setLaravelSession($session = m::mock('Illuminate\Contracts\Session\Session'));
        $session->shouldReceive('pull')->once()->with('state')->andReturn(str_repeat('A', 40));
        $provider = new FramgiaTestProviderStub($request, 'client_id', 'client_secret', 'redirect_uri');
        $provider->http = m::mock('StdClass');
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';
        $provider->http->shouldReceive('post')->once()->with('http://auth.framgia.vn/auth/hr_system/access_token', [
            'headers' => ['Accept' => 'application/json'], $postKey => ['client_id' => 'client_id', 'client_secret' => 'client_secret', 'code' => 'code', 'redirect_uri' => 'redirect_uri'],
        ])->andReturn($response = m::mock('StdClass'));
        $response->shouldReceive('getBody')->once()->andReturn('{ "access_token" : "access_token", "refresh_token" : "refresh_token", "expires_in" : 3600 }');
        $user = $provider->user();

        $this->assertInstanceOf('Framgia\Education\Auth\Providers\User', $user);
        $this->assertSame('B123', $user->id);
        $this->assertSame('access_token', $user->token);
        $this->assertSame('refresh_token', $user->refreshToken);
        $this->assertSame(3600, $user->expiresIn);
    }

    /**
     * @expectedException \Framgia\Education\Auth\Providers\InvalidStateException
     */
    public function testExceptionIsThrownIfStateIsInvalid()
    {
        $request = Request::create('foo', 'GET', ['state' => str_repeat('B', 40), 'code' => 'code']);
        $request->setLaravelSession($session = m::mock('Illuminate\Contracts\Session\Session'));
        $session->shouldReceive('pull')->once()->with('state')->andReturn(str_repeat('A', 40));
        $provider = new FramgiaTestProviderStub($request, 'client_id', 'client_secret', 'redirect');
        $provider->user();
    }

    /**
     * @expectedException \Framgia\Education\Auth\Providers\InvalidStateException
     */
    public function testExceptionIsThrownIfStateIsNotSet()
    {
        $request = Request::create('foo', 'GET', ['state' => 'state', 'code' => 'code']);
        $request->setLaravelSession($session = m::mock('Illuminate\Contracts\Session\Session'));
        $session->shouldReceive('pull')->once()->with('state');
        $provider = new FramgiaTestProviderStub($request, 'client_id', 'client_secret', 'redirect');
        $provider->user();
    }
}
