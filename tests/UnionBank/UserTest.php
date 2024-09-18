<?php

namespace Dmn\OnlineBankingOAuth2\Tests\UnionBank;

use Dmn\OnlineBankingOAuth2\Tests\UnionBank\TestCase;
use Dmn\OnlineBankingOAuth2\UnionBank\UnionBankProvider;
use Dmn\OnlineBankingOAuth2\UnionBank\User;
use Laravel\Socialite\Two\InvalidStateException;

class UserTest extends TestCase
{
    /**
     * @test
     * @testdox It can get user from code
     *
     * @return void
     */
    public function userFromToken(): void
    {
        $sessionToken = 'session';
        $this->fakeSession();
        $this->mockResponses([
            [
                [
                    'token_type' => 'bearer',
                    'access_token' => 'token',
                    'metadata' => 'a:' . $sessionToken,
                    'expires_in' => 1800,
                    'consented_on' => 1606447997,
                    'scope' => 'transactionalAccountsForBillsPay fundTopUp',
                    'refresh_token' => 'refresh-token',
                    'refresh_token_expires_in' => 2592000,
                ],
            ],
            [
                ['token' => 'token'],
            ]
        ]);

        $code = 'code';
        $this->app->request->merge(['code' => $code]);

        $provider = $this->service()
            ->stateless();
        $user = $provider->user();
        /** @var \Dmn\OnlineBankingOAuth2\UnionBank\User $user */
        $user = $provider->user();
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(UnionBankProvider::class, $user->driver());
        $this->assertEquals($sessionToken, $user->getSessionToken());
    }

    /**
     * @test
     * @testdox It can get user from refresh token
     *
     * @return void
     */
    public function userFromRefreshToken(): void
    {
        $this->fakeSession();
        $this->mockResponses([
            [
                [
                    'token_type' => 'bearer',
                    'access_token' => 'access_token',
                    'metadata' => 'a:dmn,one',
                    'expires_in' => 1800,
                    'consented_on' => 1606447997,
                    'scope' => 'transactionalAccountsForBillsPay fundTopUp',
                    'refresh_token' => 'some-refresh-token',
                    'refresh_token_expires_in' => 2592000,
                ],
            ],
            [
                ['token' => 'some token'],
            ]
        ]);

        $refreshToken = 'refresh-token';

        $provider = $this->service()
            ->stateless();
        $user = $provider->userFromRefreshToken($refreshToken);
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(UnionBankProvider::class, $user->driver());
    }

    /**
     * @test
     * @testdox It can get user if there is an existing user
     *
     * @return void
     */
    public function userFromTokenWithExistingUser(): void
    {
        $sessionToken = 'session-token';
        $this->fakeSession();
        $this->mockResponses([
            [
                [
                    'token_type' => 'bearer',
                    'access_token' => 'token',
                    'metadata' => 'a:' . $sessionToken,
                    'expires_in' => 1800,
                    'consented_on' => 1606447997,
                    'scope' => 'transactionalAccountsForBillsPay fundTopUp',
                    'refresh_token' => 'some-refresh-tokne',
                    'refresh_token_expires_in' => 2592000,
                ],
            ],
            [
                ['token' => 'token'],
            ]
        ]);

        $code = 'code';
        $this->app->request->merge(['code' => $code]);

        $provider = $this->service()
            ->stateless();
        $user = $provider->user();
        /** @var \Dmn\OnlineBankingOAuth2\UnionBank\User $user */
        $user = $provider->user();
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(UnionBankProvider::class, $user->driver());
        $this->assertEquals($sessionToken, $user->getSessionToken());
    }

    /**
     * @test
     * @testdox It can get user if there is an existing user
     *
     * @return void
     */
    public function invalidState(): void
    {
        $this->expectException(InvalidStateException::class);
        $sessionToken = 'session';
        $this->fakeSession();
        $this->mockResponses([
            [
                [
                    'token_type' => 'bearer',
                    'access_token' => 'token',
                    'metadata' => 'a:' . $sessionToken,
                    'expires_in' => 1800,
                    'consented_on' => 1606447997,
                    'scope' => 'transactionalAccountsForBillsPay fundTopUp',
                    'refresh_token' => 'refresh_token',
                    'refresh_token_expires_in' => 2592000,
                ],
            ],
            [
                ['token' => 'token'],
            ]
        ]);

        $code = 'code';
        $this->app->request->merge(['code' => $code]);

        $provider = $this->service();
        $provider->user();
    }
}
