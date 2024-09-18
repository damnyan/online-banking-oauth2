<?php

namespace Dmn\OnlineBankingOAuth2;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\TransferStats;
use Laravel\Socialite\Two\AbstractProvider as TwoAbstractProvider;

abstract class AbstractProvider extends TwoAbstractProvider
{
    protected $lastReuqest;

    protected $lastRequestArray;

    /**
     * {@inheritDoc}
     */
    protected function getHttpClient()
    {
        $this->guzzle['on_stats']  = function (TransferStats $stats) {
            $this->lastReuqest = $stats->getRequest();
        };

        $client = parent::getHttpClient();
        return $client;
    }

    /**
     * Get last request
     *
     * @return Request
     */
    public function getLastRequest(): Request
    {
        return $this->lastReuqest;
    }

    /**
     * Get last request array
     *
     * @return Request
     */
    public function getLastRequestArray(): array
    {
        return $this->lastRequestArray ?? [];
    }

    /**
     * Set last request array
     *
     * @param string $uri
     * @param string $method
     * @param array $options
     *
     * @return void
     */
    protected function setLastRequestArray(
        string $uri,
        string $method,
        array $options
    ): void {
        $this->lastRequestArray = [
            'uri' => $uri,
            'method' => $method,
            'options' => $options,
        ];
    }
}
