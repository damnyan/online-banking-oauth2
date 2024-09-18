<?php

namespace Dmn\OnlineBankingOAuth2;

use GuzzleHttp\Psr7\Request;
use Laravel\Socialite\Two\User;

abstract class AbstractUser extends User
{
    /**
     * Get last request
     *
     * @return Request
     */
    public function getLastRequest(): Request
    {
        return $this->driver()->getLastRequest();
    }

    /**
     * Get last transaction request
     *
     * @return array
     */
    public function getLastRequestArray(): array
    {
        return $this->driver()->getLastRequestArray();
    }
}
