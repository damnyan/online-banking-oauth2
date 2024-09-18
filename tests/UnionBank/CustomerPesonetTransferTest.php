<?php

namespace Dmn\OnlineBankingOAuth2\Tests\UnionBank;

use Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Address;
use Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Beneficiary;
use Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Remittance;
use Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Sender;

class CustomerPesonetTransferTest extends TestCase
{
    /**
     * @test
     * @testdox Customer can fund transfer unionbank to pesonet
     *
     * @return void
     */
    public function transfer(): void
    {
        $response = [
            'senderRefId' => '12345678',
            'ubpTranId' => 'UB2454429517477',
            'createdAt' => '2021-07-23T02:43:42.604',
            'state' => 'Sent for Processing',
        ];
        $this->mockResponse($response);

        $reference = '12345678';
        $address = new Address;
        $address->setLine1('241 A.DEL MUNDO ST BET. 5TH 6TH AVE GRACE');
        $address->setLine2('PARK CALOOCAN CITY');
        $address->setCity('Caloocan');
        $address->setProvince('142');
        $address->setZipCode('1900');
        $address->setCountry('204');
        $sender = new Sender;
        $sender->setName('Juan Dela Cruz');
        $sender->setAddress($address);

        $address = new Address;
        $address->setLine1('241 A.DEL MUNDO ST BET. 5TH 6TH AVE GRACE');
        $address->setLine2('PARK CALOOCAN CITY');
        $address->setCity('Caloocan');
        $address->setProvince('142');
        $address->setZipCode('1900');
        $address->setCountry('204');
        $beneficiary = new Beneficiary;
        $beneficiary->setName('Juan');
        $beneficiary->setAccountNumber('107324511489');
        $beneficiary->setAddress($address);

        $remittance = new Remittance;
        $remittance->setAmount(3000.00);
        $remittance->setPurpose('5 632');
        $remittance->setReceivingBank(161203);
        $remittance->setInstructions('instructions data');

        $user = $this->user();
        $transaction = $user->customerPesonetTransfer(
            $reference,
            $sender,
            $beneficiary,
            $remittance
        );

        $this->assertTrue(is_array($transaction));
        $this->assertEquals($response['senderRefId'], $transaction['reference']);
        $this->assertEquals($response['ubpTranId'], $transaction['ubp_tran_id']);
        $this->assertEquals($response['createdAt'], $transaction['created_at']);
        $this->assertEquals($response['state'], $transaction['state']);
    }

    /**
     * @testdox Customer can inquire unionbank to pesonet fund transfer
     * @test
     *
     */
    public function inquireTransfer()
    {
        $response = [
            'records' => [
                [
                    'transferId' => 'UB4377175400409',
                    'type' => 'PESONET',
                    'createdAt' => '2018-08-10T00:40:46.000Z',
                    'state' => 'Sent for Processing',
                    'senderTransferId' => '12345678',
                ],
                [
                    'transferId' => 'UB9653097653551',
                    'type' => 'PESONET',
                    'createdAt' => '2021-02-17T21:01:45.000Z',
                    'state' => 'Sent for Processing',
                    'senderTransferId' => '12345678',
                ],
            ]
        ];

        $this->mockResponse($response);

        $reference = '12345678';
        $user = $this->user();
        $transactions = $user->customerInquirePesonetTransfer($reference);

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

    /**
     * @test
     * @testdox Customer can list unionbank to pesonet receiving banks
     *
     * @return void
     */
    public function listReceivingBanks(): void
    {
        $response = [
            'records' => [
                [
                    'code' => '79570',
                    'bank' => 'One Network Bank, Inc. (A Rural Bank)',
                ],
                [
                    'code' => '135038',
                    'bank' => 'Philippine Business Bank',
                ],
                [
                    'code' => '161203',
                    'bank' => 'Bank Of China',
                ],
            ]
        ];
        $this->mockResponse($response);

        $user = $this->user();
        $partners = $user->customerlistPesonetReceivingBanks();

        $this->assertTrue(is_array($partners));
        $this->assertEquals($response['records'], $partners);
    }
}