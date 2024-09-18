<?php

namespace Dmn\OnlineBankingOAuth2\Tests\UnionBank;

use Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Address;
use Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Beneficiary;
use Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Remittance;
use Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Sender;

class PartnerPesonetTransferTest extends TestCase
{
    /**
     * @test
     * @testdox Partner can fund transfer unionbank to pesonet
     *
     * @return void
     */
    public function transfer(): void
    {
        $response = [
            'code' => 'SP',
            'senderRefId' => 'TEST1231SB',
            'state' => 'Sent for Processing',
            'uuid' => 'f21b837e-3044-4902-8b10-44c58aabd400',
            'description' => 'Transaction is sent for processing',
            'type' => 'PESONET',
            'amount' => '30.00',
            'ubpTranId' => 'trans-id',
            'remittanceId' => 'some-remittance-id',
            'tranRequestDate' => '2018-08-10T08:40:45.897',
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
        $transaction = $user->partnerPesonetTransfer(
            $reference,
            $sender,
            $beneficiary,
            $remittance
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
        $this->assertEquals($response['remittanceId'], $transaction['remittance_id']);
        $this->assertEquals($response['tranRequestDate'], $transaction['tran_request_date']);
    }

    /**
     * @testdox Partner can inquire unionbank to pesonet fund transfer
     * @test
     *
     */
    public function inquireTransfer()
    {
        $response = [
            'record' => [
                'senderRefId' => 'TEST1231SB',
                'code' => 'SP',
                'state' => 'Credited Beneficiary Account',
                'uuid' => '50dcf474-c25f-486d-a8bc-286827607509',
                'description' => 'Credited to the customer\'s account',
                'type' => 'PESONET',
                'amount' => '123.00',
                'ubpTranId' => 'PESONET',
                'remittanceId' => 'remittance-id',
                'createdAt' => '2020-11-26T04:14:41.000Z',
                'updatedAt' => NULL,
            ]
        ];

        $this->mockResponse($response);

        $reference = 'TEST1231SB';
        $user = $this->user();
        $transaction = $user->partnerInquirePesonetTransfer($reference);

        $this->assertTrue(is_array($transaction));
        $record = $response['record'];
        $this->assertEquals($record['senderRefId'], $transaction['reference']);
        $this->assertEquals($record['code'], $transaction['code']);
        $this->assertEquals($record['state'], $transaction['state']);
        $this->assertEquals($record['uuid'], $transaction['uuid']);
        $this->assertEquals($record['description'], $transaction['description']);
        $this->assertEquals($record['type'], $transaction['type']);
        $this->assertEquals($record['amount'], $transaction['amount']);
        $this->assertEquals($record['ubpTranId'], $transaction['ubp_tran_id']);
        $this->assertEquals($record['remittanceId'], $transaction['remittance_id']);
        $this->assertEquals($record['createdAt'], $transaction['created_at']);
        $this->assertEquals($record['updatedAt'], $transaction['updated_at']);
    }

    /**
     * @test
     * @testdox Partner can list unionbank to pesonet receiving banks
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
        $partners = $user->partnerListPesonetReceivingBanks();

        $this->assertTrue(is_array($partners));
        $this->assertEquals($response['records'], $partners);
    }
}
