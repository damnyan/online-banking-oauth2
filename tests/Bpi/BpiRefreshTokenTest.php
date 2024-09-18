<?php

namespace Dmn\OnlineBankingOAuth2\Tests\Bpi;

use Dmn\OnlineBankingOAuth2\Bpi\Exceptions\ServerException as BpiServerException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidClientException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidGrantException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidRequestException;
use Dmn\OnlineBankingOAuth2\Exceptions\UnknownClientException;
use Dmn\OnlineBankingOAuth2\Tests\Bpi\TestCase;

class BpiRefreshTokenTest extends TestCase
{
    /**
     * @test
     * @testdox Error when hitting the /token endpoint with invalid Authorization_code
     *
     * @return void
     */
    public function invalidGrant(): void
    {
        $this->expectException(InvalidGrantException::class);
        $this->mockResponse(['error' => 'invalid_grant'], 400);
        $provider = $this->service();
        $provider->getNewRefreshToken('code');
    }

    /**
     * @test
     * @testdox Error when hitting the /token endpoint missing Authorization_code
     *
     * @return void
     */
    public function invalidRequest(): void
    {
        $this->expectException(InvalidRequestException::class);
        $this->mockResponse(['error' => 'invalid_request'], 400);
        $provider = $this->service();
        $provider->getNewRefreshToken('code');
    }

    /**
     * @test
     * @testdox Error when hitting the /token endpoint re-using Authorization_code
     *
     * @return void
     */
    public function invalidGrant2(): void
    {
        $this->expectException(InvalidGrantException::class);
        $this->mockResponse(['error' => 'invalid_grant'], 400);
        $provider = $this->service();
        $provider->getNewRefreshToken('code');
    }

    /**
     * @test
     * @testdox Error when hitting the /token endpoint with missing grant type
     *
     * @return void
     */
    public function unknownError(): void
    {
        $this->expectException(UnknownClientException::class);
        $this->mockResponse(['error' => 'unkown'], 400);
        $provider = $this->service();
        $provider->getNewRefreshToken('code');
    }

    /**
     * @test
     * @testdox Error when hitting the /token endpoint with invalid clientId or client_secret
     *
     * @return void
     */
    public function invalidClient(): void
    {
        $this->expectException(InvalidClientException::class);
        $this->mockResponse(['error' => 'invalid_client'], 401);
        $provider = $this->service();
        $provider->getNewRefreshToken('code');
    }

    /**
     * @test
     * @testdox Error when token is invalid or missing or expired in calling the functional APIs secured with 3 legged OAUTH
     *
     * @return void
     */
    public function unauthorized(): void
    {
        $this->expectException(InvalidClientException::class);
        $this->mockResponse([
                'httpCode' => '401',
                'httpMessage' => 'Unauthorized',
                'more information' => 'application is not registered, or active',
        ], 401);
        $provider = $this->service();
        $provider->getNewRefreshToken('code');
    }

    /**
     * @test
     * @testdox It should throw BpiServerException on 500 response
     *
     * @return void
     */
    public function serverException(): void
    {
        $this->expectException(BpiServerException::class);
        $this->mockResponse([
                'code' => 'code',
                'status' => 'status',
                'description' => 'message',
        ], 500);
        $provider = $this->service();
        $provider->getNewRefreshToken('code');
    }

    /**
     * @test
     * @testdox It can get access_token and expiration
     *
     * @return void
     */
    public function accessToken(): void
    {
        $res = [
            'token_type' => 'bearer',
            'access_token' => 'AAIkNGJmM2ZjMjktM2EyZi00OTYzLWFlMDItNTY3OTJhZDA0NmFkiiDhUrYK243b2wx64j3f0IAyOkvJQgid9NP--w93wcqqKQOq3WODvay1A8-r_9mUfT7m8gYl_wTTgYytZYDefGHaxelkGJq-9V4qbfbMesHrP2XY_gFYE6UEXhfckpK5LfrwZeoECuCkJCecKSdNnE1MRWLRwC_H8fsFDbUWy8A',
            'expires_in' => 1800,
            'consented_on' => 1606285991,
            'scope' => 'transactionalAccountsForBillsPay fundTopUp',
            'refresh_token' => 'AAK0l8hdthb47iVFh09fPlW3YaY4yRMRWpBazZXAHYkDV8ET3XHAu_CJAjpFVwd7mHiUBJifTWlyKxepE5pUUdDo5rejs_-7gA8mOd5G8VT6mOmAQENciav77Ku7qx5epz_oFj_gGSTX6R-iVp5-z8SpyAFppbMt5rggX_Gq4aRaunFe7nmK-NYLMd6vCnVx2Y4',
            'refresh_token_expires_in' => 2592000,
        ];

        $this->mockResponse($res);

        $code = 'AAJa8Q55tPFJWfzyjtwqlx8Wi7pQbOrZ0hUAP-kxFZjPJYtS60taDLTUaZqi2XkzVwfwxtIlbId6IjpACLrV2v69U93YS2wOyesPePTw09inE3CwrvUuh31g396DNnikS9VIv8tTe-F66TgMAIiVpny-j1U7WM79Juvkj-6rBmXwIWL-OjIQhk6tKMP33cjmPSQ';
        $provider = $this->service();
        $response = $provider->getNewRefreshToken($code);

        $this->assertArrayHasKey('token_type', $response);
        $this->assertArrayHasKey('access_token', $response);
        $this->assertArrayHasKey('expires_in', $response);
        $this->assertArrayHasKey('consented_on', $response);
        $this->assertArrayHasKey('scope', $response);
        $this->assertArrayHasKey('refresh_token', $response);
        $this->assertArrayHasKey('refresh_token_expires_in', $response);
    }
}
