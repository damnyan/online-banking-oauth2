<?php

namespace Dmn\OnlineBankingOAuth2\Tests\UnionBank;

class PartnerIntraFundTransferTest extends TestCase
{
    /**
     * @test
     * @testdox Partner can fund transfer unionbank to unionbank
     *
     * @return void
     */
    public function transfer(): void
    {
        $response = [
            'code' => 'TS',
            'senderRefId' => 'UB127',
            'state' => 'Credited Beneficiary Account',
            'uuid' => 'd23ff4bf-bcef-4c58-b98c-c0d40b35294f',
            'description' => 'Successful transaction',
            'type' => 'TRANSFER',
            'amount' => '100',
            'ubpTranId' => 'trans-id',
            'tranRequestDate' => '2017-10-10T12:11:50.333',
        ];
        $this->mockResponse($response);

        $user = $this->user();

        $reference = '12345678';
        $accountNumber = '105239265518';
        $amount = 123;
        $remarks = 'Transfer remarks';
        $info = [
            [
                'index' => 1,
                'name' => 'Recipient',
                'value' => 'Juan Dela Cruz',
            ],
            [
                'index' => 2,
                'name' => 'Message',
                'value' => 'Happy Birthday',
            ]
        ];

        $transaction = $user->partnerIntraFundTransfer(
            $reference,
            $accountNumber,
            $amount,
            $info,
            $remarks
        );

        $this->assertTrue(is_array($transaction));
        $this->assertEquals($response['code'], $transaction['code']);
        $this->assertEquals($response['senderRefId'], $transaction['reference']);
        $this->assertEquals($response['state'], $transaction['state']);
        $this->assertEquals($response['uuid'], $transaction['uuid']);
        $this->assertEquals($response['description'], $transaction['description']);
        $this->assertEquals($response['type'], $transaction['type']);
        $this->assertEquals($response['amount'], $transaction['amount']);
        $this->assertEquals($response['ubpTranId'], $transaction['ubp_tran_id']);
        $this->assertEquals($response['tranRequestDate'], $transaction['tran_request_date']);
    }

    /**
     * @testdox Partner can inquire unionbank to unionbank fund transfer
     * @test
     *
     */
    public function inquireTransfer()
    {
        $response = [
            'records' => [
                [
                    'ubpTranId' => 'UB6323361452962',
                    'type' => 'TRANSFER',
                    'createdAt' => '2020-10-05T04:11:50.000Z',
                    'state' => 'Failed to Credit Beneficiary Account',
                    'senderRefId' => 'UB127',
                ],
                [
                    'ubpTranId' => 'UB1306832082994',
                    'type' => 'TRANSFER',
                    'createdAt' => '2017-10-10T04:11:50.000Z',
                    'state' => 'Credited Beneficiary Account',
                    'senderRefId' => 'UB127',
                ],
            ]
        ];

        $this->mockResponse($response);

        $reference = 'UB127';
        $user = $this->user();
        $transactions = $user->partnerInquireIntraTransfer($reference);

        $this->assertTrue(is_array($transactions));
        $record = $response['records'][0];
        $transaction = $transactions[0];
        $this->assertEquals($record['ubpTranId'], $transaction['ubp_tran_id']);
        $this->assertEquals($record['type'], $transaction['type']);
        $this->assertEquals($record['createdAt'], $transaction['created_at']);
        $this->assertEquals($record['state'], $transaction['state']);
        $this->assertEquals($record['senderRefId'], $transaction['reference']);
        $record = $response['records'][1];
        $transaction = $transactions[1];
        $this->assertEquals($record['ubpTranId'], $transaction['ubp_tran_id']);
        $this->assertEquals($record['type'], $transaction['type']);
        $this->assertEquals($record['createdAt'], $transaction['created_at']);
        $this->assertEquals($record['state'], $transaction['state']);
        $this->assertEquals($record['senderRefId'], $transaction['reference']);
    }
}
