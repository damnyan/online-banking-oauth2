<?php

namespace Dmn\OnlineBankingOAuth2\Tests\Bpi;

use Dmn\OnlineBankingOAuth2\Bpi\BpiProvider;
use Dmn\OnlineBankingOAuth2\Tests\Bpi\TestCase;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Contracts\Factory;

class BpiAuthTest extends TestCase
{
    /**
     * @test
     * @testdox It can intitiate the BPI Driver
     *
     * @return void
     */
    public function driver(): void
    {
        $factory = $this->app->make(Factory::class);
        $provider = $factory->driver('bpi');
        $this->assertInstanceOf(BpiProvider::class, $provider);
    }

    /**
     * @test
     * @testdox Auth url
     *
     * @return void
     */
    public function authUrl(): void
    {
        $this->fakeSession();

        $factory = $this->app->make(Factory::class);
        $provider = $factory->driver('bpi');
        $authUrl = $provider
            ->scopes(['transactionalAccountsForBillsPay', 'fundTopUp'])
            ->setState('test')
            ->redirect();

        $url = $authUrl->getTargetUrl();
        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $params);

        $this->assertInstanceOf(RedirectResponse::class, $authUrl);
        $this->assertEquals('test', $params['state']);
    }

    /**
     * @test
     * @testdox Auth url random state
     *
     * @return void
     */
    public function authUrlRandomState(): void
    {
        $this->fakeSession();

        $factory = $this->app->make(Factory::class);
        $provider = $factory->driver('bpi');
        $authUrl = $provider
            ->scopes(['transactionalAccountsForBillsPay', 'fundTopUp'])
            ->redirect();

        $url = $authUrl->getTargetUrl();
        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $params);

        $this->assertInstanceOf(RedirectResponse::class, $authUrl);
        $this->assertEquals(true, isset($params['state']));
    }

    /**
     * @test
     * @testdox Auth url stateless
     *
     * @return void
     */
    public function authUrlStateless(): void
    {
        $this->fakeSession();

        $factory = $this->app->make(Factory::class);
        $provider = $factory->driver('bpi');
        $authUrl = $provider
            ->scopes(['transactionalAccountsForBillsPay', 'fundTopUp'])
            ->stateless()
            ->redirect();

        $url = $authUrl->getTargetUrl();
        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $params);

        $this->assertInstanceOf(RedirectResponse::class, $authUrl);
        $this->assertEquals(false, isset($params['state']));
    }

    /**
     * @test
     * @testdox Auth url - it should disregard the provided state if it is stateless
     *
     * @return void
     */
    public function authUrlStatelessWithState(): void
    {
        $this->fakeSession();

        $factory = $this->app->make(Factory::class);
        $provider = $factory->driver('bpi');
        $authUrl = $provider
            ->scopes(['transactionalAccountsForBillsPay', 'fundTopUp'])
            ->setState('test')
            ->stateless()
            ->redirect();

        $url = $authUrl->getTargetUrl();
        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $params);

        $this->assertInstanceOf(RedirectResponse::class, $authUrl);
        $this->assertEquals(false, isset($params['state']));
    }
}
