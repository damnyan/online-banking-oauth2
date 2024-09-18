<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank;

use Carbon\Carbon;
use Dmn\OnlineBankingOAuth2\AbstractUser;
use Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Beneficiary as InstapayBeneficiary;
use Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Remittance as InstapayRemittance;
use Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Sender as InstaPaySender;
use Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Beneficiary as PesonetBeneficiary;
use Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Remittance as PesonetRemittance;
use Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Sender as PesonetSender;
use Dmn\OnlineBankingOAuth2\UnionBank\UnionBankProvider;
use Laravel\Socialite\Contracts\Factory;

class User extends AbstractUser
{
    protected $accounts;

    protected $driver = 'unionbank';

    protected $sessionToken;

    /**
     * Get driver
     *
     * @return UnionBankProvider
     */
    public function driver(): UnionBankProvider
    {
        $factory = app()->make(Factory::class);
        return $factory->driver($this->driver);
    }

    /**
     * Set session token
     *
     * @param string $metaData
     *
     * @return self
     */
    public function setSessionToken(string $metaData): self
    {
        $parsedMetaData     = $this->parseMetaData($metaData);
        $this->sessionToken = $parsedMetaData[1];

        return $this;
    }

    /**
     * Parse metadata
     *
     * @param string $metaData
     *
     * @return array
     */
    private function parseMetaData(string $metaData): array
    {
        return explode(':', $metaData);
    }

    /**
     * Get session token
     *
     * @return string
     */
    public function getSessionToken(): string
    {
        return $this->sessionToken;
    }

    /**
     * Merchant payment
     *
     * @param string $reference
     * @param float $amount
     * @param array $information
     * @param string $remarks
     * @param Carbon|null $transactionDate
     * @param string $particulars
     * @param string $currency
     * @param string $requestId
     * @param string $otp
     * @return array
     */
    public function merchantPay(
        string $reference,
        float $amount,
        array $information,
        string $remarks,
        string $requestId,
        string $otp,
        Carbon $transactionDate = null,
        string $particulars = 'Payment particulars',
        string $currency = 'PHP'
    ): array {
        if (is_null($transactionDate) == true) {
            $transactionDate = new Carbon();
        }

        $payload = [
            'senderRefId' => $reference,
            'tranRequestDate' => $transactionDate->format('Y-m-d\TH:i:s.v'),
            'amount' => [
                'currency' => $currency,
                'value' => $amount,
            ],
            'remarks' => $remarks,
            'particulars' => $particulars,
            'info' => $this->encodeInformation($information),
            'requestId' => $requestId,
            'otp' => $otp,
        ];

        $response = $this->driver()->merchantPay($this->token, $payload);
        $payload  = $response['payload'];

        return [
            'code' => $payload['code'],
            'reference' => $payload['senderRefId'],
            'state' => $payload['state'],
            'uuid' => $payload['uuid'],
            'description' => $payload['description'],
            'type' => $payload['type'],
            'amount' => $payload['amount'],
            'ubp_tran_id' => $payload['ubpTranId'],
            'tran_request_date' => $payload['tranRequestDate'],
            'signature' => $response['signature']
        ];
    }


    /**
     * Merchant payment
     *
     * @return array
     */
    public function merchantPayOTP(): array
    {
        $response = $this->driver()->merchantPayOTP($this->token);

        return [
            'request_id' => $response['requestId'],
            'core_code' => $response['coreCode'],
        ];
    }

    /**
     * Merchant inquire
     *
     * @param string $reference
     *
     * @return array
     */
    public function merchantInquire(string $reference): array
    {
        $response = $this->driver()->merchantInquire(
            $this->token,
            $reference
        );

        $payload  = $response['payload']['record'];

        return [
            'code' => $payload['code'],
            'reference' => $payload['senderRefId'],
            'state' => $payload['state'],
            'uuid' => $payload['uuid'],
            'description' => $payload['description'],
            'type' => $payload['type'],
            'amount' => $payload['amount'],
            'ubp_tran_id' => $payload['ubpTranId'],
            'tran_request_date' => $payload['tranRequestDate'],
            'created_at' => $payload['createdAt'],
            'updated_at' => $payload['updatedAt'],
            'signature' => $response['signature']
        ];
    }

    /**
     * Encode information
     *
     * @param array $information
     *
     * @return array
     */
    protected function encodeInformation(array $information): array
    {
        $arr = [];
        $counter = 1;
        foreach ($information as $key => $value) {
            $arr[] = [
                'index' => $counter,
                'name' => $key,
                'value' => $value,
            ];
            $counter++;
        }

        return $arr;
    }

    /**
     * Customer instapay fund transfer
     *
     * @param string $reference
     * @param InstaPaySender $sender
     * @param InstapayBeneficiary $beneficiary
     * @param InstapayRemittance $remittance
     * @param Carbon|null $transactionDate
     * @return array
     */
    public function customerInstapayTransfer(
        string $reference,
        InstaPaySender $sender,
        InstapayBeneficiary $beneficiary,
        InstapayRemittance $remittance,
        Carbon $transactionDate = null
    ): array {
        if (is_null($transactionDate) == true) {
            $transactionDate = new Carbon();
        }

        $payload = [
            'senderRefId' => $reference,
            'tranRequestDate' => $transactionDate->format('Y-m-d\TH:i:s.v'),
            'sender' => $sender->toArray(),
            'beneficiary' => $beneficiary->toArray(),
            'remittance' => $remittance->toArray()
        ];

        $response = $this->driver()->customerInstapayTransfer($this->token, $payload);

        return [
            'reference' => $response['senderRefId'],
            'ubp_tran_id' => $response['ubpTranId'],
            'created_at' => $response['createdAt'],
            'state' => $response['state'],
        ];
    }

    /**
     * Customer inquire instapay fund transfer
     *
     * @param string $reference
     * @return array
     */
    public function customerInquireInstapayTransfer(string $reference): array
    {
        $response = $this->driver()->customerInquireInstapayTransfer(
            $this->token,
            $reference
        );
        $records = $response['records'];

        return array_map(function ($record) {
            return [
                'transfer_id' => $record['transferId'],
                'type' => $record['type'],
                'created_at' => $record['createdAt'],
                'state' => $record['state'],
                'reference' => $record['senderTransferId'],
            ];
        }, $records);
    }

    /**
     * Customer list instapay receiving banks
     *
     * @return array
     */
    public function customerListInstapayReceivingBanks(): array
    {
        $response = $this->driver()->customerListInstapayReceivingBanks($this->token);

        return $response['records'];
    }

    /**
     * Customer pesonet fund transfer
     *
     * @param string $reference
     * @param PesonetSender $sender
     * @param PesonetBeneficiary $beneficiary
     * @param PesonetRemittance $remittance
     * @param Carbon|null $transactionDate
     * @return array
     */
    public function customerPesonetTransfer(
        string $reference,
        PesonetSender $sender,
        PesonetBeneficiary $beneficiary,
        PesonetRemittance $remittance,
        Carbon $transactionDate = null
    ): array {
        if (is_null($transactionDate) == true) {
            $transactionDate = new Carbon();
        }

        $payload = [
            'senderRefId' => $reference,
            'tranRequestDate' => $transactionDate->format('Y-m-d\TH:i:s.v'),
            'sender' => $sender->toArray(),
            'beneficiary' => $beneficiary->toArray(),
            'remittance' => $remittance->toArray()
        ];
        $response = $this->driver()->customerPesonetTransfer($this->token, $payload);

        return [
            'reference' => $response['senderRefId'],
            'ubp_tran_id' => $response['ubpTranId'],
            'created_at' => $response['createdAt'],
            'state' => $response['state'],
        ];
    }

    /**
     * Customer inquire pesonet Fund transfer
     *
     * @param string $reference
     * @return array
     */
    public function customerInquirePesonetTransfer(string $reference): array
    {
        $response = $this->driver()->customerInquirePesonetTransfer(
            $this->token,
            $reference
        );
        $records = $response['records'];

        return array_map(function ($record) {
            return [
                'transfer_id' => $record['transferId'],
                'type' => $record['type'],
                'created_at' => $record['createdAt'],
                'state' => $record['state'],
                'reference' => $record['senderTransferId'],
            ];
        }, $records);
    }

    /**
     * Customer List pesonet receiving banks
     *
     * @return array
     */
    public function customerlistPesonetReceivingBanks(): array
    {
        $response = $this->driver()->customerListPesonetReceivingBanks($this->token);

        return $response['records'];
    }

    /**
     * Customer intra-bank fund transfer
     *
     * @param string $reference
     * @param string $accountNumber
     * @param float $amount
     * @param array $information
     * @param string $remarks
     * @param Carbon|null $transactionDate
     * @param string $particulars
     * @param string $currency
     * @return array
     */
    public function customerIntraFundTransfer(
        string $reference,
        string $accountNumber,
        float $amount,
        array $information,
        string $remarks,
        Carbon $transactionDate = null,
        string $particulars = 'Transfer particulars',
        string $currency = 'PHP'
    ): array {
        if (is_null($transactionDate) == true) {
            $transactionDate = new Carbon();
        }

        $payload = [
            'senderRefId' => $reference,
            'tranRequestDate' => $transactionDate->format('Y-m-d\TH:i:s.v'),
            'accountNo' => $accountNumber,
            'amount' => [
                'currency' => $currency,
                'value' => $amount,
            ],
            'remarks' => $remarks,
            'particulars' => $particulars,
            'info' => $this->encodeInformation($information),
        ];

        $response = $this->driver()->customerIntraTransfer($this->token, $payload);
        $payload = $response;

        return [
            'reference' => $payload['senderRefId'],
            'ubp_tran_id' => $payload['ubpTranId'],
            'created_at' => $payload['createdAt'],
            'state' => $payload['state'],
        ];
    }

    /**
     * Customer inquire intra-bank fund transfer
     *
     * @param string $reference
     * @return array
     */
    public function customerInquireIntraTransfer(string $reference): array
    {
        $response = $this->driver()->customerInquireIntraTransfer(
            $this->token,
            $reference
        );
        $records  = $response['records'];

        return array_map(function ($record) {
            return [
                'transfer_id' => $record['transferId'],
                'type' => $record['type'],
                'created_at' => $record['createdAt'],
                'state' => $record['state'],
                'reference' => $record['senderTransferId'],
            ];
        }, $records);
    }


    /**
     * Partner instapay fund transfer
     *
     * @param string $reference
     * @param InstaPaySender $sender
     * @param InstapayBeneficiary $beneficiary
     * @param InstapayRemittance $remittance
     * @param Carbon|null $transactionDate
     * @return array
     */
    public function partnerInstapayTransfer(
        string $reference,
        InstaPaySender $sender,
        InstapayBeneficiary $beneficiary,
        InstapayRemittance $remittance,
        Carbon $transactionDate = null
    ): array {
        if (is_null($transactionDate) == true) {
            $transactionDate = new Carbon();
        }

        $payload = [
            'senderRefId' => $reference,
            'tranRequestDate' => $transactionDate->format('Y-m-d\TH:i:s.v'),
            'sender' => $sender->toArray(),
            'beneficiary' => $beneficiary->toArray(),
            'remittance' => $remittance->toArray()
        ];

        $response = $this->driver()->partnerInstapayTransfer($this->token, $payload);

        return [
            'code' => $response['code'],
            'reference' => $response['senderRefId'],
            'ubp_tran_id' => $response['ubpTranId'],
            'tran_request_date' => $response['tranRequestDate'],
            'state' => $response['state'],
            'uuid' => $response['uuid'],
            'core_ref_id' => $response['coreRefId'],
            'trace_no' => $response['traceNo']
        ];
    }


    /**
     * Partner inquire instapay fund transfer
     *
     * @param string $reference
     * @return array
     */
    public function partnerInquireInstapayTransfer(string $reference): array
    {
        $response = $this->driver()->partnerInquireInstapayTransfer(
            $this->token,
            $reference
        );
        $records = $response['records'];

        return array_map(function ($record) {
            return [
                'reference' => $record['senderRefId'],
                'code' => $record['code'],
                'uuid' => $record['uuid'],
                'description' => $record['description'],
                'type' => $record['type'],
                'ubp_tran_id' => $record['ubpTranId'],
                'amount' => $record['amount'],
                'created_at' => $record['createdAt'],
                    'updated_at' => $record['updatedAt'],
            ];
        }, $records);
    }

    /**
     * Partner list instapay receiving banks
     *
     * @return array
     */
    public function partnerListInstapayReceivingBanks(): array
    {
        $response = $this->driver()->partnerListInstapayReceivingBanks($this->token);

        return $response['records'];
    }

    public function partnerListInstapayLibraries()
    {
        $response = $this->driver()->partnerListInstapayLibraries($this->token);

        return $response['records'];
    }

    /**
     * Partner pesonet fund transfer
     *
     * @param string $reference
     * @param PesonetSender $sender
     * @param PesonetBeneficiary $beneficiary
     * @param PesonetRemittance $remittance
     * @param Carbon|null $transactionDate
     * @return array
     */
    public function partnerPesonetTransfer(
        string $reference,
        PesonetSender $sender,
        PesonetBeneficiary $beneficiary,
        PesonetRemittance $remittance,
        Carbon $transactionDate = null
    ): array {
        if (is_null($transactionDate) == true) {
            $transactionDate = new Carbon();
        }

        $payload = [
            'senderRefId' => $reference,
            'tranRequestDate' => $transactionDate->format('Y-m-d\TH:i:s.v'),
            'sender' => $sender->toArray(),
            'beneficiary' => $beneficiary->toArray(),
            'remittance' => $remittance->toArray()
        ];
        $response = $this->driver()->partnerPesonetTransfer($this->token, $payload);

        return [
            'code' => $response['code'],
            'reference' => $response['senderRefId'],
            'state' => $response['state'],
            'uuid' => $response['uuid'],
            'description' => $response['description'],
            'type' => $response['type'],
            'amount' => $response['amount'],
            'ubp_tran_id' => $response['ubpTranId'],
            'remittance_id' => $response['remittanceId'],
            'tran_request_date' => $response['tranRequestDate'],
        ];
    }

    /**
     * Partner inquire pesonet Fund transfer
     *
     * @param string $reference
     * @return array
     */
    public function partnerInquirePesonetTransfer(string $reference): array
    {
        $response = $this->driver()->partnerInquirePesonetTransfer(
            $this->token,
            $reference
        );
        $record = $response['record'];

        return [
            'reference' => $record['senderRefId'],
            'code' => $record['code'],
            'state' => $record['state'],
            'uuid' => $record['uuid'],
            'description' => $record['description'],
            'type' => $record['type'],
            'amount' => $record['amount'],
            'ubp_tran_id' => $record['ubpTranId'],
            'remittance_id' => $record['remittanceId'],
            'created_at' => $record['createdAt'],
            'updated_at' => $record['updatedAt'],
        ];
    }

    /**
     * Partner List pesonet receiving banks
     *
     * @return array
     */
    public function partnerListPesonetReceivingBanks(): array
    {
        $response = $this->driver()->partnerListPesonetReceivingBanks($this->token);

        return $response['records'];
    }

    /**
     * Partner intra-bank fund transfer
     *
     * @param string $reference
     * @param string $accountNumber
     * @param float $amount
     * @param array $information
     * @param string $remarks
     * @param Carbon|null $transactionDate
     * @param string $particulars
     * @param string $currency
     * @return array
     */
    public function partnerIntraFundTransfer(
        string $reference,
        string $accountNumber,
        float $amount,
        array $information,
        string $remarks,
        Carbon $transactionDate = null,
        string $particulars = 'Transfer particulars',
        string $currency = 'PHP'
    ): array {
        if (is_null($transactionDate) == true) {
            $transactionDate = new Carbon();
        }

        $payload = [
            'senderRefId' => $reference,
            'tranRequestDate' => $transactionDate->format('Y-m-d\TH:i:s.v'),
            'accountNo' => $accountNumber,
            'amount' => [
                'currency' => $currency,
                'value' => $amount,
            ],
            'remarks' => $remarks,
            'particulars' => $particulars,
            'info' => $this->encodeInformation($information),
        ];

        $response = $this->driver()->partnerIntraTransfer($this->token, $payload);
        $payload = $response;

        return [
            'code' => $payload['code'],
            'reference' => $payload['senderRefId'],
            'state' => $payload['state'],
            'uuid' => $payload['uuid'],
            'description' => $payload['description'],
            'type' => $payload['type'],
            'amount' => $payload['amount'],
            'ubp_tran_id' => $payload['ubpTranId'],
            'tran_request_date' => $payload['tranRequestDate'],
        ];
    }

    /**
     * Partner inquire intra-bank fund transfer
     *
     * @param string $reference
     * @return array
     */
    public function partnerInquireIntraTransfer(string $reference): array
    {
        $response = $this->driver()->partnerInquireIntraTransfer(
            $this->token,
            $reference
        );
        $records  = $response['records'];

        return array_map(function ($record) {
            return [
                'ubp_tran_id' => $record['ubpTranId'],
                'type' => $record['type'],
                'created_at' => $record['createdAt'],
                'state' => $record['state'],
                'reference' => $record['senderRefId'],
            ];
        }, $records);
    }
}
