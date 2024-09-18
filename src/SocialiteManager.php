<?php

namespace Dmn\OnlineBankingOAuth2;

use Dmn\OnlineBankingOAuth2\Bpi\BpiProvider;
use Dmn\OnlineBankingOAuth2\UnionBank\UnionBankProvider;
use Laravel\Socialite\SocialiteManager as SocialiteSocialiteManager;

class SocialiteManager extends SocialiteSocialiteManager
{
    /**
     * Create BPI Provider
     *
     * @return BpiProvider
     */
    public function createBpiDriver(): BpiProvider
    {
        $config = $this->config->get('services.bpi');
        $bpi    = $this->buildProvider(BpiProvider::class, $config);
        $bpi->setOtherConfig($config);
        return $bpi;
    }

    /**
     * Undocumented function
     *
     * @return UnionBankProvider
     */
    public function createUnionbankDriver(): UnionBankProvider
    {
        $config    = $this->config->get('services.unionbank');
        $unionbank = $this->buildProvider(UnionBankProvider::class, $config);
        $unionbank->setOtherConfig($config);
        return $unionbank;
    }
}
