<?php

namespace Dmn\OnlineBankingOAuth2\Tests\UnionBank;

use Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Address;
use Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Beneficiary;
use Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Remittance;
use Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Sender;

class PartnerInstapayTransferTest extends TestCase
{
    /**
     * @test
     * @testdox Partner can fund transfer unionbank to instapay
     *
     * @return void
     */
    public function transfer(): void
    {
        $response = [
            'code' => 'TS',
            'senderRefId' => 'TEST4321',
            'tranRequestDate' => '2015-10-03T15:29:16.333',
            'ubpTranId' => 'UB1706',
            'state' => 'Credited Beneficiary Account',
            'uuid' => '95607606-2f54-4e6e-be1a-58e008960a26',
            'coreRefId' => '2302560968054408760',
            'traceNo' => '211272'
        ];
        $this->mockResponse($response);

        $reference = 'TEST4321';

        $address = new Address;
        $address->setLine1('GRA CE');
        $address->setLine2('PARK CALOOCAN CITY');
        $address->setCity('Caloocan');
        $address->setProvince('142');
        $address->setCountry('204');
        $address->setZipCode('1900');
        $sender = new Sender;
        $sender->setName('Juan Dela Cruz');
        $sender->setAddress($address);

        $address = new Address;
        $address->setLine1('241 A.DEL MUNDO ST BET. 5TH 6TH AVE GRACE');
        $address->setLine2('PARK CALOOCAN CITY');
        $address->setCity('Caloocan');
        $address->setProvince('142');
        $address->setCountry('204');
        $address->setZipCode('1900');
        $beneficiary = new Beneficiary;
        $beneficiary->setName('Rachelle');
        $beneficiary->setAccountNumber('109450542671');
        $beneficiary->setAddress($address);

        $remittance = new Remittance;
        $remittance->setAmount("30");
        $remittance->setPurpose(1001);
        $remittance->setReceivingBank(161312);

        $user = $this->user();

        $transaction = $user->partnerInstapayTransfer(
            $reference,
            $sender,
            $beneficiary,
            $remittance
        );

        $this->assertTrue(is_array($transaction));
        $this->assertEquals($response['code'], $transaction['code']);
        $this->assertEquals($response['senderRefId'], $transaction['reference']);
        $this->assertEquals($response['ubpTranId'], $transaction['ubp_tran_id']);
        $this->assertEquals($response['tranRequestDate'], $transaction['tran_request_date']);
        $this->assertEquals($response['state'], $transaction['state']);
        $this->assertEquals($response['uuid'], $transaction['uuid']);
        $this->assertEquals($response['coreRefId'], $transaction['core_ref_id']);
        $this->assertEquals($response['traceNo'], $transaction['trace_no']);
    }

    /**
     * @testdox Partner can inquire unionbank to instapay fund transfer
     * @test
     *
     * @return void
     */
    public function inquireTransfer(): void
    {
        $response = [
            'records' => [
                [
                    'senderRefId' => 'TEST4321',
                    'code' => 'TS',
                    'uuid' => '95607606-2f54-4e6e-be1a-58e008960a26',
                    'description' => 'Credited Beneficiary Account',
                    'type' => 'INSTAPAY',
                    'ubpTranId' => 'UB6219682539904',
                    'amount' => '30.00',
                    'createdAt' => '2021-07-25T18:56:10.000Z',
                    'updatedAt' => NULL,
                ]
            ]
        ];

        $this->mockResponse($response);

        $reference = 'TEST4321';
        $user = $this->user();
        $transactions = $user->partnerInquireInstapayTransfer($reference);

        $this->assertTrue(is_array($transactions));
        $record = $response['records'][0];
        $transaction = $transactions[0];
        $this->assertEquals($record['senderRefId'], $transaction['reference']);
        $this->assertEquals($record['code'], $transaction['code']);
        $this->assertEquals($record['uuid'], $transaction['uuid']);
        $this->assertEquals($record['description'], $transaction['description']);
        $this->assertEquals($record['type'], $transaction['type']);
        $this->assertEquals($record['ubpTranId'], $transaction['ubp_tran_id']);
        $this->assertEquals($record['amount'], $transaction['amount']);
        $this->assertEquals($record['createdAt'], $transaction['created_at']);
        $this->assertEquals($record['updatedAt'], $transaction['updated_at']);
    }

    /**
     * @test
     * @testdox Partner can list unionbank to instapay receiving banks
     *
     * @return void
     */
    public function listReceivingBanks(): void
    {
        $response = [
            'records' => [
                [
                    'code' => '3237',
                    'bank' => 'Allbank Corp.',
                    'brstn' => NULL,
                ],
                [
                    'code' => '148120',
                    'bank' => 'Sun Savings Bank',
                    'brstn' => NULL,
                ],
                [
                    'code' => '161207',
                    'bank' => 'Sterling Bank Of Asia',
                    'brstn' => '011190019',
                ],
            ]
        ];
        $this->mockResponse($response);

        $user = $this->user();
        $partners = $user->partnerListInstapayReceivingBanks();

        $this->assertTrue(is_array($partners));
        $this->assertEquals($response['records'], $partners);
    }

    /**
     * @test
     * @testdox Partner can list unionbank to instapay libraries
     *
     * @return void
     */
    public function listLibraries(): void
    {
        $response = [
            'records' => [
                [
                    'code' => '1001',
                    'description' => 'Donation',
                ],
                [
                    'code' => '1002',
                    'description' => 'Payment',
                ],
                [
                    'code' => '1003',
                    'description' => 'Fund Transfer',
                ],
            ]
        ];
        $this->mockResponse($response);

        $user = $this->user();
        $libraries = $user->partnerListInstapayLibraries();

        $this->assertTrue(is_array($libraries));
        $this->assertEquals($response['records'], $libraries);
    }
}