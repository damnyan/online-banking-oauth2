<?php

namespace Dmn\OnlineBankingOAuth2\Bpi;

use Dmn\OnlineBankingOAuth2\AbstractProvider;
use Dmn\OnlineBankingOAuth2\Bpi\Exceptions\ClientException as BpiClientException;
use Dmn\OnlineBankingOAuth2\Bpi\Exceptions\ServerException as BpiServerException;
use Dmn\OnlineBankingOAuth2\Bpi\TokenDecryptor;
use Dmn\OnlineBankingOAuth2\Bpi\User;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidClientException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidGrantException;
use Dmn\OnlineBankingOAuth2\Exceptions\InvalidRequestException;
use Dmn\OnlineBankingOAuth2\Exceptions\UnknownClientException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\ProviderInterface;

class BpiProvider extends AbstractProvider implements ProviderInterface
{
    protected $authUri;

    protected $apiGateway;

    protected $authProxy;

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
        $this->authUri = $config['auth_uri'];
        $this->apiGateway = $config['api_gateway_uri'];
        $this->authProxy = $config['auth_proxy'];
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
            $this->authUri . 'oauth2/authorize',
            $state
        );
    }

    /**
     * @inheritDoc
     */
    protected function getTokenUrl(): string
    {
        return $this->authUri . 'oauth2/token';
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
                'proxy' => $this->authProxy,
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
                'proxy' => $this->authProxy,
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
    protected function getUserByToken($token)
    {
        $response = $this->callApiGateway(
            'accounts/transactionalAccounts',
            [],
            $token
        );

        return $response['content'];
    }

    /**
     * @inheritDoc
     */
    protected function mapUserToObject(array $user)
    {
        $accounts = $user['body']['transactionalAccounts'];

        return (new User())->setAccounts($accounts);
    }

    /**
     * @inheritDoc
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
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
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ];
    }

    /**
     * Initiate transaction
     *
     * @param string $token
     * @param array $data
     *
     * @return array
     */
    public function initiateTransaction(string $token, array $data): array
    {
        return $this->callApiGateway(
            'fundTopUp/initiate',
            $data,
            $token,
            'POST'
        );
    }

    /**
     * Set Otp
     *
     * @param string $token
     * @param string $transactionId
     * @param string $mobileNumberToken
     *
     * @return void
     */
    public function sendOtp(
        string $token,
        string $transactionId,
        string $mobileNumberToken
    ): array {
        return $this->callApiGateway(
            'fundTopUp/otp',
            ['mobileNumberToken' => $mobileNumberToken],
            $token,
            'POST',
            $transactionId
        );
    }

    /**
     * Process
     *
     * @param string $token
     * @param string $transactionId
     * @param string $otp
     *
     * @return array
     */
    public function process(
        string $token,
        string $transactionId,
        string $otp
    ): array {
        return $this->callApiGateway(
            'fundTopUp/process',
            ['otp' => $otp],
            $token,
            'POST',
            $transactionId
        );
    }

    /**
     * Inquire transaction
     *
     * @param string $token
     * @param string $transactionId
     *
     * @return void
     */
    public function inquire(string $token, string $transactionId): array
    {
        return $this->callApiGateway(
            'fundTopUp/status',
            [],
            $token,
            'GET',
            $transactionId
        );
    }

    /**
     * Call API Gateway
     *
     * @param string $uri
     * @param array $data
     * @param string $token
     * @param string $method
     * @param string $transactionId
     *
     * @return array
     */
    private function callApiGateway(
        string $uri,
        array $data,
        string $token,
        string $method = 'GET',
        string $transactionId = null
    ): array {
        $uri = $this->apiGateway . $uri;

        $options = $this->getOptions(
            $uri,
            $method,
            $token,
            $data,
            $transactionId
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

        $decryptor    = new TokenDecryptor();
        $jsonResponse = json_decode($response->getBody()->getContents(), true);
        $decrypted    = $decryptor->decrypt($jsonResponse['token']);

        return [
            'headers' => $response->getHeaders(),
            'content' => $decrypted,
        ];
    }

    /**
     * Get options
     *
     * @param string $uri
     * @param string $method
     * @param string $token
     * @param array $data
     * @param string $transactionId
     *
     * @return array
     */
    private function getOptions(
        string $uri,
        string $method,
        string $token,
        array $data = [],
        string $transactionId = null
    ): array {
        $headers = [
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
            'x-ibm-client-id' => $this->clientId,
            'x-ibm-client-secret' => $this->clientSecret,
            'Authorization' => 'Bearer ' . $token,
        ];

        if ($transactionId) {
            $headers['transactionId'] = $transactionId;
        }

        $options = [
            'headers' => $headers,
            'verify' => false,
        ];

        if ($data) {
            $builder = new TokenEncryptor();
            $jwe     = $builder->encrypt($data);
            $data    = [
                'token' => $jwe
            ];

            $options['json'] = $data;
        }

        $this->setLastRequestArray($uri, $method, $options);

        return $options;
    }

    /**
     * Throw Client Exception
     *
     * @param ClientException $exception
     *
     * @throws BpiClientException
     */
    private function throwClientException(ClientException $exception): void
    {
        $response = json_decode(
            $exception->getResponse()->getBody()->getContents(),
            true
        );

        if (!isset($response['token'])) {
            $this->clientErrors($exception->getCode(), $response);
        }

        $decryptor = new TokenDecryptor();
        $decrypted = $decryptor->decrypt($response['token']);
        throw new BpiClientException(
            $decrypted['description'],
            $decrypted['code'],
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
     * @throws BpiServerException
     */
    private function throwServerException($exception)
    {
        $response = json_decode(
            $exception->getResponse()->getBody()->getContents(),
            true
        );

        if (isset($response['token'])) {
            $decryptor = new TokenDecryptor();
            $decrypted = $decryptor->decrypt($response['token']);
            throw new BpiServerException(
                $decrypted['description'],
                $decrypted['code'],
                $exception
            );
        }

        throw new BpiServerException(
            $response['description'],
            $response['code'],
            $exception
        );
    }
}
