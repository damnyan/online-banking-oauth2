<?php

namespace Dmn\OnlineBankingOAuth2\Providers;

use Dmn\OnlineBankingOAuth2\SocialiteManager;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\SocialiteServiceProvider as AppSocialiteServiceProvider;

class SocialiteServiceProvider extends AppSocialiteServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->app->singleton(Factory::class, function ($app) {
            return new SocialiteManager($app);
        });
    }
}
