<?php

namespace Dmn\OnlineBankingOAuth2\Tests\UnionBank;

class CustomerIntraFundTransferTest extends TestCase
{
    /**
     * @test
     * @testdox Customer can fund transfer unionbank to unionbank
     *
     * @return void
     */
    public function transfer(): void
    {
        $response = [
            'senderRefId' => '12345678',
            'ubpTranId' => 'UB8506416323919',
            'createdAt' => '2015-10-03T15:29:16.333',
            'state' => 'Credited Beneficiary Account',
        ];
        $this->mockResponse($response);

        $user = $this->user();

        $reference = '12345678';
        $accountNumber = '105239265518';
        $amount = 100;
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

        $transaction = $user->customerIntraFundTransfer(
            $reference,
            $accountNumber,
            $amount,
            $info,
            $remarks
        );

        $this->assertTrue(is_array($transaction));
        $this->assertEquals($response['senderRefId'], $transaction['reference']);
        $this->assertEquals($response['ubpTranId'], $transaction['ubp_tran_id']);
        $this->assertEquals($response['createdAt'], $transaction['created_at']);
        $this->assertEquals($response['state'], $transaction['state']);
    }

    /**
     * @testdox Customer can inquire unionbank to unionbank fund transfer
     * @test
     *
     */
    public function inquireTransfer()
    {
        $response = [
            'records' => [
                [
                    'transferId' => 'UB4062703398837',
                    'type' => 'TRANSFER',
                    'createdAt' => '2020-07-22T05:13:52.000Z',
                    'state' => 'Credited Beneficiary Account',
                    'senderTransferId' => '12345678',
                ],
                [
                    'transferId' => 'UB5284403971363',
                    'type' => 'TRANSFER',
                    'createdAt' => '2015-10-03T07:29:16.000Z',
                    'state' => 'Failed to Credit Beneficiary Account',
                    'senderTransferId' => '12345678',
                ],
            ]
        ];

        $this->mockResponse($response);

        $reference = '12345678';
        $user = $this->user();
        $transactions = $user->customerInquireIntraTransfer($reference);

        $this->assertTrue(is_array($transactions));
        $record = $response['records'][0];
        $transaction = $transactions[0];
        $this->assertEquals($record['transferId'], $transaction['transfer_id']);
        $this->assertEquals($record['type'], $transaction['type']);
        $this->assertEquals($record['createdAt'], $transaction['created_at']);
        $this->assertEquals($record['state'], $transaction['state']);
        $this->assertEquals($record['senderTransferId'], $transaction['reference']);
        $record = $response['records'][1];
        $transaction = $transactions[1];
        $this->assertEquals($record['transferId'], $transaction['transfer_id']);
        $this->assertEquals($record['type'], $transaction['type']);
        $this->assertEquals($record['createdAt'], $transaction['created_at']);
        $this->assertEquals($record['state'], $transaction['state']);
        $this->assertEquals($record['senderTransferId'], $transaction['reference']);
    }
}