<?php

namespace Dmn\OnlineBankingOAuth2\Tests\Bpi;

use Dmn\OnlineBankingOAuth2\Tests\Bpi\TestCase;
use Dmn\OnlineBankingOAuth2\UnionBank\UnionBankProvider;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Contracts\Factory;

class UnionBankAuthTest extends TestCase
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
        $provider = $factory->driver('unionbank');
        $this->assertInstanceOf(UnionBankProvider::class, $provider);
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
        $provider = $factory->driver('unionbank');
        $authUrl = $provider
            ->scopes(['payments', 'account_info'])
            ->setState('test')
            ->with(['type' => 'single'])
            ->redirect();

        $url = $authUrl->getTargetUrl();
        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $params);

        $this->assertInstanceOf(RedirectResponse::class, $authUrl);
        $this->assertEquals('test', $params['state']);
        $this->assertEquals(
            config('services.unionbank.partner_id'),
            $params['partnerId']
        );
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
        $provider = $factory->driver('unionbank');
        $authUrl = $provider
            ->scopes(['payments', 'account_info'])
            ->with(['type' => 'single'])
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
        $provider = $factory->driver('unionbank');
        $authUrl = $provider
            ->scopes(['payments', 'account_info'])
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
        $provider = $factory->driver('unionbank');
        $authUrl = $provider
            ->scopes(['payments', 'account_info'])
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
