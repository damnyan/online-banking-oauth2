<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank;

use Dmn\OnlineBankingOAuth2\AbstractProvider;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidClientException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidGrantException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidRequestException;
use Dmn\OnlineBankingOAuth2\Exceptions\UnknownClientException;
use Dmn\OnlineBankingOAuth2\UnionBank\Traits\CustomerInstapayTransfer;
use Dmn\OnlineBankingOAuth2\UnionBank\Exceptions\ClientException as ExceptionsClientException;
use Dmn\OnlineBankingOAuth2\UnionBank\Exceptions\ServerException as UnionbankServerException;
use Dmn\OnlineBankingOAuth2\UnionBank\Traits\CustomerPesonetTransfer;
use Dmn\OnlineBankingOAuth2\UnionBank\Traits\CustomerIntraFundTransfer;
use Dmn\OnlineBankingOAuth2\UnionBank\Traits\PartnerInstapayTransfer;
use Dmn\OnlineBankingOAuth2\UnionBank\Traits\PartnerIntraFundTransfer;
use Dmn\OnlineBankingOAuth2\UnionBank\Traits\PartnerPesonetTransfer;
use Dmn\OnlineBankingOAuth2\UnionBank\User;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\ProviderInterface;

class UnionBankProvider extends AbstractProvider implements ProviderInterface
{
    use CustomerInstapayTransfer;
    use CustomerPesonetTransfer;
    use CustomerIntraFundTransfer;
    use PartnerInstapayTransfer;
    use PartnerPesonetTransfer;
    use PartnerIntraFundTransfer;

    protected $uri;

    protected $partnerId;

    protected $scopeSeparator = ' ';

    protected $encodingType = PHP_QUERY_RFC3986;

    protected $state = null;

    /**
     * Set Other Config
     *
     * @param array $config
     *
     * @return void
     */
    public function setOtherConfig(array $config)
    {
        $this->uri       = $config['uri'];
        $this->partnerId = $config['partner_id'];
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return self
     */
    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getState()
    {
        if (is_null($this->state)) {
            return parent::getState();
        }

        return $this->state;
    }

    /**
     * @inheritDoc
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            $this->uri . 'customers/v1/oauth2/authorize',
            $state
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function getCodeFields($state = null)
    {
        $codeFields = parent::getCodeFields($state);

        return array_merge($codeFields, [
            'partnerId' => $this->partnerId,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getTokenUrl(): string
    {
        return $this->uri . 'customers/v1/oauth2/token';
    }

    /**
     * @inheritDoc
     */
    public function getAccessTokenResponse($code)
    {
        try {
            $response = $this->getHttpClient()->post($this->getTokenUrl(), [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'form_params' => $this->getTokenFields($code),
            ]);
        } catch (ClientException $exception) {
            $this->throwClientException($exception);
        } catch (ServerException $exception) {
            $this->throwServerException($exception);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Get New refresh token
     *
     * @param string $refreshToken
     *
     * @return array
     */
    public function getNewRefreshToken(string $refreshToken): array
    {
        try {
            $response = $this->getHttpClient()->post($this->getTokenUrl(), [
                'headers' => ['Accept' => 'application/json'],
                'form_params' => $this->getRefreshTokenFields($refreshToken),
            ]);
        } catch (ClientException $exception) {
            $this->throwClientException($exception);
        } catch (ServerException $exception) {
            $this->throwServerException($exception);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Get user from refresh token
     *
     * @param string $refreshToken
     *
     * @return User
     */
    public function userFromRefreshToken(string $refreshToken): User
    {
        $response = $this->getNewRefreshToken($refreshToken);

        $user = $this->mapUserToObject($this->getUserByToken(
            $token = Arr::get($response, 'access_token')
        ));

        return $user->setToken($token)
                    ->setRefreshToken(Arr::get($response, 'refresh_token'))
                    ->setExpiresIn(Arr::get($response, 'expires_in'));
    }

    /**
     * @inheritDoc
     */
    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        if ($this->hasInvalidState()) {
            throw new InvalidStateException();
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $this->user = $this->mapUserToObject($this->getUserByToken(
            $token = Arr::get($response, 'access_token')
        ));

        return $this->user->setToken($token)
                    ->setRefreshToken(Arr::get($response, 'refresh_token'))
                    ->setExpiresIn(Arr::get($response, 'expires_in'))
                    ->setSessionToken(Arr::get($response, 'metadata'));
    }

    /**
     * @inheritDoc
     */
    protected function getUserByToken($token)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function mapUserToObject(array $user)
    {
        return new User();
    }

    /**
     * @inheritDoc
     */
    protected function getTokenFields($code)
    {
        return [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];
    }

    /**
     * Get refresh token fields
     *
     * @param string $refreshToken
     *
     * @return array
     */
    protected function getRefreshTokenFields($refreshToken): array
    {
        return [
            'client_id' => $this->clientId,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ];
    }

    /**
     * Throw Client Exception
     *
     * @param ClientException $exception
     *
     * @throws ExceptionsClientException
     */
    private function throwClientException(ClientException $exception): void
    {
        $response = json_decode(
            $exception->getResponse()->getBody()->getContents(),
            true
        );

        if (isset($response['errors']) == false) {
            $this->clientErrors($exception->getCode(), $response);
        }

        throw new ExceptionsClientException(
            'Client error.',
            $response['errors'],
            $exception
        );
    }

    /**
     * Client errors
     *
     * @param string $code
     * @param array $response
     *
     * @throws Exception
     */
    private function clientErrors(string $code, array $response): void
    {
        if (isset($response['error'])) {
            if ($response['error'] == 'invalid_grant') {
                throw new InvalidGrantException();
            }

            if ($response['error'] == 'invalid_request') {
                throw new InvalidRequestException();
            }
        }

        if ($code == 401) {
            throw new InvalidClientException();
        }

        throw new UnknownClientException();
    }

    /**
     * Throw server exception
     *
     * @param ServerException $exception
     *
     * @throws UnionbankServerException
     */
    private function throwServerException($exception)
    {
        $response = json_decode(
            $exception->getResponse()->getBody()->getContents(),
            true
        );

        throw new UnionbankServerException(
            'Server error.',
            $response['errors'],
            $exception
        );
    }

    /**
     * Generate Merchant Payment OTP
     * @param string $token
     * @return array
     */
    public function merchantPayOTP(string $token): array
    {
        $response = $this->callApiGateway(
            'merchants/v5/payments/otp/single',
            [],
            $token,
        );

        return $response;
    }

    /**
     * Merchant Payment
     *
     * @param string $token
     * @param array $data
     *
     * @return array
     */
    public function merchantPay(string $token, array $data): array
    {
        $response = $this->callApiGateway(
            'merchants/v5/payments/single',
            $data,
            $token,
            'POST'
        );

        return $response;
    }

    /**
     * Merchant Payment Inquire
     *
     * @param string $token
     * @param string $reference
     *
     * @return array
     */
    public function merchantInquire(string $token, string $reference): array
    {
        $response = $this->callApiGateway(
            'merchants/v5/payments/single/' . $reference,
            [],
            $token
        );

        return $response;
    }

    /**
     * Call API Gateway
     *
     * @param string $uri
     * @param array $data
     * @param string $token
     * @param string $method
     *
     * @return array
     */
    protected function callApiGateway(
        string $uri,
        array $data,
        string $token,
        string $method = 'GET'
    ): array {
        $uri = $this->uri . $uri;

        $options = $this->getOptions(
            $uri,
            $method,
            $token,
            $data
        );

        try {
            $response = $this->getHttpClient()->request(
                $method,
                $uri,
                $options
            );
        } catch (ClientException $exception) {
            $this->throwClientException($exception);
        } catch (ServerException $exception) {
            $this->throwServerException($exception);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Get options
     *
     * @return array
     */
    private function getOptions(
        string $uri,
        string $method,
        string $token,
        array $data
    ): array {
        $headers = [
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
            'x-ibm-client-id' => $this->clientId,
            'x-ibm-client-secret' => $this->clientSecret,
            'Authorization' => 'Bearer ' . $token,
            'x-partner-id' => $this->partnerId,
        ];

        $options = [
            'headers' => $headers,
            'verify' => false,
            'json' => $data
        ];

        $this->setLastRequestArray($uri, $method, $options);

        return $options;
    }
}
