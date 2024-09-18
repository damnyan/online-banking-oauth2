<?php

namespace Dmn\OnlineBankingOAuth2\Bpi;

use Dmn\OnlineBankingOAuth2\AbstractUser;
use Dmn\OnlineBankingOAuth2\Bpi\BpiProvider;
use Illuminate\Support\Collection;
use Laravel\Socialite\Contracts\Factory;

class User extends AbstractUser
{
    protected $accounts;

    protected $driver = 'bpi';

    /**
     * Get accounts
     *
     * @return array
     */
    public function accounts(): Collection
    {
        return $this->accounts;
    }

    /**
     * Set Accounts
     *
     * @param array $accounts
     *
     * @return self
     */
    public function setAccounts(array $accounts): self
    {
        $this->accounts = collect($accounts);
        return $this;
    }

    /**
     * Get driver
     *
     * @return BpiProvider
     */
    public function driver(): BpiProvider
    {
        $factory = app()->make(Factory::class);
        return $factory->driver($this->driver);
    }

    /**
     * Initiate transaction
     *
     * @param array $data
     *
     * @return array
     */
    public function initiateTransaction(
        string $reference,
        string $accountNumberToken,
        float $amount,
        string $remarks
    ): array {
        $data = [
            'merchantTransactionReference' => $reference,
            'accountNumberToken' => $accountNumberToken,
            'amount' => $amount,
            'remarks' => $remarks,
        ];
        $response = $this->driver()->initiateTransaction($this->token, $data);

        return [
            'transaction_id' => $response['headers']['transactionId'][0],
            'mobile_number' => $response['content']['body']['mobileNumber'],
            'mobile_number_token' => $response['content']['body']['mobileNumberToken'],
        ];
    }

    /**
     * Send OTP
     *
     * @param string $transactionId
     * @param string $mobileNumberToken
     *
     * @return array
     */
    public function sendOtp(
        string $transactionId,
        string $mobileNumberToken
    ): array {
        $response = $this->driver()->sendOtp(
            $this->token,
            $transactionId,
            $mobileNumberToken
        );

        return [
            'otp_valid_until' => $response['content']['body']['otpValidUntil'],
        ];
    }

    /**
     * Process transaction
     *
     * @param string $transactionId
     * @param string $otp
     *
     * @return array
     */
    public function process(string $transactionId, string $otp): array
    {
        $response = $this->driver()->process(
            $this->token,
            $transactionId,
            $otp
        );

        return [
            'confirmation_timestamp' => $response['content']['body']['confirmationTimestamp'],
            'confirmation_number' => $response['content']['body']['confirmationNumber'],
        ];
    }

    /**
     * Inquire transaction
     *
     * @param string $transactionId
     *
     * @return array
     */
    public function inquire(string $transactionId): array
    {
        $response = $this->driver()->inquire($this->token, $transactionId);

        return [
            'status' => $response['content']['body']['transactionStatus'],
            'confirmation_timestamp' => $response['content']['body']['confirmationTimestamp'] ?? null,
            'confirmation_number' => $response['content']['body']['confirmationNumber'] ?? null,
        ];
    }
}
