<?php

namespace Dmn\OnlineBankingOAuth2\Tests\UnionBank;

use Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Address;
use Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Beneficiary;
use Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Remittance;
use Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Sender;

class CustomerInstapayTransferTest extends TestCase
{
    /**
     * @test
     * @testdox Customer can fund transfer unionbank to instapay
     *
     * @return void
     */
    public function transfer(): void
    {
        $response = [
            'senderRefId' => 321,
            'ubpTranId' => 'UB2706855175254',
            'createdAt' => '2021-07-22T08:09:41.311',
            'state' => 'Credited Beneficiary Account',
        ];
        $this->mockResponse($response);

        $reference = '321';

        $address = new Address;
        $address->setLine1('GRACE');
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
        $beneficiary->setAccountNumber('107324511489');
        $beneficiary->setAddress($address);

        $remittance = new Remittance;
        $remittance->setAmount(30.00);
        $remittance->setPurpose(1001);
        $remittance->setReceivingBank(161312);

        $user = $this->user();

        $transaction = $user->customerInstapayTransfer(
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
     * @testdox Vustomer can inquire  unionbank to instapay fund transfer
     * @test
     *
     * @return void
     */
    public function inquireTransfer(): void
    {
        $response = [
            'records' => [
                [
                    'transferId' => 'UB7567161618794',
                    'type' => 'INSTAPAY',
                    'createdAt' => '2015-10-03T07:29:16.000Z',
                    'state' => 'Credited Beneficiary Account',
                    'senderTransferId' => '321',
                ],
                [
                    'transferId' => 'UB9695059726214',
                    'type' => 'INSTAPAY',
                    'createdAt' => '2015-10-03T07:29:16.000Z',
                    'state' => 'Credited Beneficiary Account',
                    'senderTransferId' => '321',
                ]
            ]
        ];

        $this->mockResponse($response);

        $reference = '321';
        $user = $this->user();
        $transactions = $user->customerInquireInstapayTransfer($reference);

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
     * @testdox It can list unionbank to instapay receiving banks
     *
     * @return void
     */
    public function listReceivingBanks(): void
    {
        $response = [
            'records' => [
                [
                    'code' => 3237,
                    'bank' => 'Allbank Corp.',
                ],
                [
                    'code' => 148120,
                    'bank' => 'Sun Savings Bank',
                ],
                [
                    'code' => 161207,
                    'bank' => 'Sterling Bank Of Asia',
                ],
                [
                    'code' => 161233,
                    'bank' => 'Malayan Bank Savings and Mortgage Bank',
                ],
            ]
        ];
        $this->mockResponse($response);

        $user = $this->user();
        $partners = $user->customerListInstapayReceivingBanks();

        $this->assertTrue(is_array($partners));
        $this->assertEquals($response['records'], $partners);
    }
}