<?php

namespace Dmn\OnlineBankingOAuth2\Tests\Bpi;

use Dmn\OnlineBankingOAuth2\Bpi\BpiProvider;
use Dmn\OnlineBankingOAuth2\Bpi\User;
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
     * Get bpi socialite provider
     *
     * @return BpiProvider
     */
    protected function service(): BpiProvider
    {
        $factory = $this->app->make(Factory::class);
        return $factory->driver('bpi');
    }

    protected function user(): User
    {
        $token        = 'AAIkNGJmM2ZjMjktM2EyZi00OTYzLWFlMDItNTY3OTJhZDA0NmFkKNcT51GQuhymO-M117IEISmuoPKNemFoOhlAFRWtEE9WyouSBGLsoXzAg99ALEDEts2k_KrbGVIhSxuhSKfJgxvDwI5ms3VGiNFKuESJfRFwAbKuluug7Fphh6ktpAiaMDaG6KX1KLSn1AM79zivR0N4WB5IBtgFwof3khrzeuw';
        $refreshToken = 'AAKaJqfLxe8j7K5PcLzGf0FWUgtzR3YXYaQ8X7QIxTT6IqL_Pyv2q0BrySSUk-P0VbgtiQ-8tks9Rto8kEhVrduCAa7X9zFOp4G2v5f9C6T2EAyQLaQtgQ_8PGSqtioiLVb3jrdNwQ7WH3xQPnActXXsdsJM4zzGaVDpT9YsluGaczfddhtUK6-XE2bu8SB4kWw';
        $accounts     = [[
            'accountNumber' => 'XXXXXX2988',
            'accountNumberToken' => 'a153c4adfe76818d2e2ea017c6164842f738b2432689610de6048cf0f04464e8e2e7181493a8896c7e0ee153b5fc5928',
            'displayOrder' => '001',
            'accountPreferredName' => 'Peso Account',
            'institution' => 'BPI',
            'accountType' => 'SAVINGS ACCOUNT',
        ]];

        $user = new User();
        $user->setToken($token)
            ->setRefreshToken($refreshToken)
            ->setExpiresIn(2592000)
            ->setAccounts($accounts);

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

        $this->app['config']->set('services.bpi.guzzle', ['handler' => $handlerStack]);
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

        $this->app['config']->set('services.bpi.guzzle', ['handler' => $handlerStack]);
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
