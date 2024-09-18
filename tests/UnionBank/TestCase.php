<?php

namespace Dmn\OnlineBankingOAuth2\Tests\UnionBank;

use Dmn\OnlineBankingOAuth2\UnionBank\UnionBankProvider;
use Dmn\OnlineBankingOAuth2\UnionBank\User;
use Dmn\OnlineBankingOAuth2\Providers\SocialiteServiceProvider;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Laravel\Socialite\Contracts\Factory;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @inheritDoc
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = require __DIR__ . '/../../config/services.php';
        $app['config']->set('services', $config);

        $app['config']->set('session', [
            'driver'          => 'array',
            'lifetime'        => 120,
            'expire_on_close' => false,
            'encrypt'         => false,
            'lottery'         => [2, 100],
            'path'            => '/',
            'domain'          => 'localhost',
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getPackageProviders($app)
    {
        return [SocialiteServiceProvider::class];
    }

    /**
     * Get unionbank socialite provider
     *
     * @return UnionBankProvider
     */
    protected function service(): UnionBankProvider
    {
        $factory = $this->app->make(Factory::class);
        return $factory->driver('unionbank');
    }

    protected function user(): User
    {
        $token        = 'token';
        $refreshToken = 'refresh';
        $sessionToken = 'a:some text';

        $user = new User();
        $user->setToken($token)
            ->setRefreshToken($refreshToken)
            ->setExpiresIn(2592000)
            ->setSessionToken($sessionToken);

        return $user;
    }

    /**
     * Mock response
     *
     * @param array $response
     * @param int $status
     * @param array $headers
     *
     * @return void
     */
    protected function mockResponse(
        array $response,
        int $status = 200,
        array $headers = []
    ): void {
        $mock = new MockHandler([
            new Response($status, $headers, json_encode($response)),
        ]);
        $handlerStack = HandlerStack::create($mock);

        $this->app['config']->set('services.unionbank.guzzle', ['handler' => $handlerStack]);
    }

    /**
     * Mock Responses
     * $responses[0] = array response
     * $responses[1] = http status code
     * $responses[2] = array headers
     *
     *
     * @param array $response
     *
     * @return void
     */
    protected function mockResponses(array $responses): void
    {
        $mockResponses = [];
        foreach ($responses as $response) {
            $mockResponses[] = new Response(
                $response[1] ?? 200,
                $response[2] ?? [],
                json_encode($response[0])
            );
        }
        $mock = new MockHandler($mockResponses);
        $handlerStack = HandlerStack::create($mock);

        $this->app['config']->set('services.unionbank.guzzle', ['handler' => $handlerStack]);
    }

    /**
     * Fake session
     *
     * @return void
     */
    protected function fakeSession(): void
    {
        $req = $this->app['request'];
        $sessionProp = new \ReflectionProperty($req, 'session');
        $sessionProp->setAccessible(true);
        $sessionProp->setValue($req, $this->app['session']->driver('array'));
    }
}
