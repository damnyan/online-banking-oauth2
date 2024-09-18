<?php

namespace Dmn\OnlineBankingOAuth2\Tests\UnionBank;

use Dmn\OnlineBankingOAuth2\Exceptions\InvalidClientException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidGrantException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidRequestException;
use Dmn\OnlineBankingOAuth2\Exceptions\UnknownClientException;
use Dmn\OnlineBankingOAuth2\Tests\UnionBank\TestCase;
use Dmn\OnlineBankingOAuth2\UnionBank\Exceptions\ServerException;

class UnionBankAccessTokenTest extends TestCase
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
        $provider->getAccessTokenResponse('code');
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
        $provider->getAccessTokenResponse('code');
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
        $provider->getAccessTokenResponse('code');
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
        $provider->getAccessTokenResponse('code');
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
        $provider->getAccessTokenResponse('code');
    }

    /**
     * @test
     * @testdox It should throw BpiServerException on 500 response
     *
     * @return void
     */
    public function serverException(): void
    {
        $this->expectException(ServerException::class);
        $response = [
            'errors' => [
                [
                    'code' => 'code',
                    'message' => 'Server error',
                ],
            ],
        ];

        $this->mockResponse($response, 500);
        $provider = $this->service();
        $provider->getAccessTokenResponse('code');
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
            'access_token' => 'token',
            'metadata' => 'a:some meta',
            'expires_in' => 31536000,
            'consented_on' => 1606881434,
            'scope' => 'account_info payments',
            'refresh_token' => 'refresh-token',
            'refresh_token_expires_in' => 2682000,
        ];

        $this->mockResponse($res);

        $code = 'code';
        $provider = $this->service();
        $response = $provider->getAccessTokenResponse($code);

        $this->assertArrayHasKey('token_type', $response);
        $this->assertArrayHasKey('access_token', $response);
        $this->assertArrayHasKey('metadata', $response);
        $this->assertArrayHasKey('expires_in', $response);
        $this->assertArrayHasKey('consented_on', $response);
        $this->assertArrayHasKey('scope', $response);
        $this->assertArrayHasKey('refresh_token', $response);
        $this->assertArrayHasKey('refresh_token_expires_in', $response);
    }
}
