<?php

namespace Dmn\OnlineBankingOAuth2\Tests\Bpi;

use Dmn\OnlineBankingOAuth2\Bpi\Exceptions\ServerException as BpiServerException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidClientException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidGrantException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidRequestException;
use Dmn\OnlineBankingOAuth2\Exceptions\UnknownClientException;
use Dmn\OnlineBankingOAuth2\Tests\Bpi\TestCase;

class BpiAccessTokenTest extends TestCase
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
        $this->expectException(BpiServerException::class);
        $this->mockResponse([
                'code' => 'code',
                'status' => 'status',
                'description' => 'message',
        ], 500);
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
            'access_token' => 'AAIkNGJmM2ZjMjktM2EyZi00OTYzLWFlMDItNTY3OTJhZDA0NmFkmKs9dCdd94BG5vxzRE8IsxszjFtxUeBC4XSJjW16hxykCn_zYAWh3ROzeCMGpV47r24FxOh1eQ5J1ObUbidcyI6i6mXGiqwg5BKrPhxLBSzLvIrZQhsgkIHuazYWucXuvUoK9DOZxj-_z9Nr0KmbfLpKwhFzuuwzgm_W_Ywwk40',
            'metadata' => 'a:dmn,one',
            'expires_in' => 1800,
            'consented_on' => 1606273328,
            'scope' => 'transactionalAccountsForBillsPay fundTopUp',
            'refresh_token' => 'AAKxOfN-2QBDzz9hRefLfoFx_1PPFdTpxbGLDK4xVqHwLj5DOH8Vij-68dlBA4zAw0yGs1TXX3d7uxRptGh8xbHwxPDLPzjP33Pc1PqYPD4d7Tu31Anmspw6YrQEMbzScyO1KqWGs1EXEvmK_zmJwfdEIXkPRiqP2D4eLr0PHMQorUAsyMuX6gb7X1BTyRQnKeY',
            'refresh_token_expires_in' => 2592000,
        ];

        $this->mockResponse($res);

        $code = 'AAL1KUzx2DqmRmdKemiq3sKBGhgmVG3uvRaEF-1B0UAF9Su8Suk688u6L-6XZbqBz073d938-77s2RcswDjKOnY4ZMBB5FD9961LD1p3sIlUBe-ieb6n9hyVoVOrvHZymdJCz4BJoOGiPnNrRDUhgTfvFj_VgXcuRWKvf8e8GQ0A7kafWIkNAKTWaUAXHQgsmsW4bJ6qBw1spBpQHdD9yeQu';
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
