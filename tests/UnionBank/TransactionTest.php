<?php

namespace Dmn\OnlineBankingOAuth2\Tests\UnionBank;

use Dmn\OnlineBankingOAuth2\Tests\UnionBank\TestCase;
use Dmn\OnlineBankingOAuth2\UnionBank\Exceptions\ClientException;
use Dmn\OnlineBankingOAuth2\UnionBank\Exceptions\ServerException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Str;

class TransactionTest extends TestCase
{
    /**
     * @test
     * @testdox It can handle client exceptions
     *
     * @return void
     */
    public function clientException(): void
    {
        $response = [
            'errors' => [
                [
                    'code' => -20,
                    'message' => 'senderRefId already exist',
                ],
            ],
        ];

        $this->mockResponse($response, 400);

        $reference = Str::random(20);
        $user      = $this->user();

        try {
            $user->merchantPay(
                $reference,
                100.12,
                [
                    'payor' => 'Juan Sanchez',
                    'isa' => 'pa',
                ],
                'remarks',
                '2711C83E7AB7BFE8BB2729B3A8FC8DB1',
                '111111'
            );
        } catch (ClientException $exception) {
            $this->assertTrue(is_array($exception->errors()));
        }
    }

    /**
     * @test
     * @testdox It can handle server exception
     *
     * @return void
     */
    public function serverException(): void
    {
        $response = [
            'errors' => [
                [
                    'code' => 'code',
                    'message' => 'Server error',
                ],
            ],
        ];

        $this->mockResponse($response, 500);

        $reference = Str::random(20);
        $user      = $this->user();

        try {
            $user->merchantPay(
                $reference,
                100.12,
                [
                    'payor' => 'Juan Sanchez',
                    'isa' => 'pa',
                ],
                'remarks',
                'some code here',
                '111111'
            );
        } catch (ServerException $exception) {
            $this->assertTrue(is_array($exception->errors()));
        }
    }

    /**
     * @test
     * @testdox It can initiate transaction
     *
     * @return void
     */
    public function merchantPay(): void
    {
        $response = [
            'payload' => [
                'code' => 'TS',
                'senderRefId' => '00000000000000000013',
                'state' => 'Credited Beneficiary Account',
                'uuid' => 'fd4235c6-04fb-436e-b07f-b6084d30b20b',
                'description' => 'Successful transaction',
                'type' => 'MERCHANT_PAYMENT',
                'amount' => '100.12124',
                'ubpTranId' => 'UB2679320541179',
                'tranRequestDate' => '2017-10-10T12:11:50.333',
            ],
            'signature' => 'singature',
        ];
        $this->mockResponse($response, 200);

        $reference = Str::random(20);
        $user      = $this->user();

        $transaction = $user->merchantPay(
            $reference,
            100.12,
            [
                'payor' => 'Juan Sanchez',
                'isa' => 'pa',
            ],
            'remarks',
            '2711C83E7AB7BFE8BB2729B3A8FC8DB1',
            '111111'
        );

        $this->assertTrue(is_array($transaction));
        $this->assertEquals($response['payload']['code'], $transaction['code']);
        $this->assertEquals($response['payload']['senderRefId'], $transaction['reference']);
        $this->assertEquals($response['payload']['state'], $transaction['state']);
        $this->assertEquals($response['payload']['uuid'], $transaction['uuid']);
        $this->assertEquals($response['payload']['description'], $transaction['description']);
        $this->assertEquals($response['payload']['type'], $transaction['type']);
        $this->assertEquals($response['payload']['amount'], $transaction['amount']);
        $this->assertEquals($response['payload']['ubpTranId'], $transaction['ubp_tran_id']);
        $this->assertEquals($response['payload']['tranRequestDate'], $transaction['tran_request_date']);
        $this->assertEquals($response['signature'], $transaction['signature']);
    }

    /**
     * @test
     * @testdox It can inquire transaction
     *
     * @return void
     */
    public function inquire(): void
    {
        $response = [
            'payload' => [
                'record' => [
                    'senderRefId' => '00000000000000000013',
                    'code' => 'TS',
                    'state' => 'Credited Beneficiary Account',
                    'uuid' => 'fd4235c6-04fb-436e-b07f-b6084d30b20b',
                    'description' => 'Successful transaction',
                    'type' => 'MERCHANT_PAYMENT',
                    'amount' => '100.12124',
                    'ubpTranId' => 'UB2679320541179',
                    'tranRequestDate' => '2020-12-02T02:23:59.000Z',
                    'createdAt' => '2020-12-02T21:31:51.000Z',
                    'updatedAt' => ''
                ]
            ],
            'signature' => 'signature',
        ];
        $this->mockResponse($response, 200);

        $reference = 'BQl8022dHatjWMqaTuF1';
        $user      = $this->user();

        $transaction = $user->merchantInquire($reference);

        $this->assertTrue(is_array($transaction));
        $this->assertEquals($response['payload']['record']['code'], $transaction['code']);
        $this->assertEquals($response['payload']['record']['senderRefId'], $transaction['reference']);
        $this->assertEquals($response['payload']['record']['state'], $transaction['state']);
        $this->assertEquals($response['payload']['record']['uuid'], $transaction['uuid']);
        $this->assertEquals($response['payload']['record']['description'], $transaction['description']);
        $this->assertEquals($response['payload']['record']['type'], $transaction['type']);
        $this->assertEquals($response['payload']['record']['amount'], $transaction['amount']);
        $this->assertEquals($response['payload']['record']['ubpTranId'], $transaction['ubp_tran_id']);
        $this->assertEquals($response['payload']['record']['tranRequestDate'], $transaction['tran_request_date']);
        $this->assertEquals($response['signature'], $transaction['signature']);
    }

    /**
     * @test
     * @testdox It can get last request
     *
     * @return void
     */
    public function lastRequest(): void
    {
        $response = [
            'payload' => [
                'record' => [
                    'senderRefId' => '00000000000000000013',
                    'code' => 'TS',
                    'state' => 'Credited Beneficiary Account',
                    'uuid' => 'fd4235c6-04fb-436e-b07f-b6084d30b20b',
                    'description' => 'Successful transaction',
                    'type' => 'MERCHANT_PAYMENT',
                    'amount' => '100.12124',
                    'ubpTranId' => 'UB2679320541179',
                    'tranRequestDate' => '2020-12-02T02:23:59.000Z',
                    'createdAt' => '2020-12-02T21:31:51.000Z',
                    'updatedAt' => ''
                ]
            ],
            'signature' => 'signature',
        ];
        $this->mockResponse($response, 200);

        $reference = 'BQl8022dHatjWMqaTuF1';
        $user      = $this->user();
        $user->merchantInquire($reference);

        $request = $user->getLastRequest();

        $this->assertInstanceOf(Request::class, $request);
    }

    /**
     * @test
     * @testdox It can get last request array
     *
     * @return void
     */
    public function lastRequestArray(): void
    {
        $response = [
            'payload' => [
                'record' => [
                    'senderRefId' => '00000000000000000013',
                    'code' => 'TS',
                    'state' => 'Credited Beneficiary Account',
                    'uuid' => 'fd4235c6-04fb-436e-b07f-b6084d30b20b',
                    'description' => 'Successful transaction',
                    'type' => 'MERCHANT_PAYMENT',
                    'amount' => '100.12124',
                    'ubpTranId' => 'UB2679320541179',
                    'tranRequestDate' => '2020-12-02T02:23:59.000Z',
                    'createdAt' => '2020-12-02T21:31:51.000Z',
                    'updatedAt' => ''
                ]
            ],
            'signature' => 'signature',
        ];
        $this->mockResponse($response, 200);

        $reference = 'BQl8022dHatjWMqaTuF1';
        $user      = $this->user();
        $user->merchantInquire($reference);

        $request = $user->getLastRequestArray();

        $this->assertTrue(true == is_array($request));
        $this->assertArrayHasKey('uri', $request);
        $this->assertArrayHasKey('method', $request);
        $this->assertArrayHasKey('options', $request);
    }

    /**
     * @test
     * @testdox It can generate merchant pay OTP
     *
     * @return void
     */
    public function merchantPayOTP(): void
    {
        $response = [
            'requestId' => '2711C83E7AB7BFE8BB2729B3A8FC8DB1',
            'coreCode' => '200',
        ];
        $this->mockResponse($response);

        $user = $this->user();

        $otp = $user->merchantPayOTP();

        $this->assertTrue(is_array($otp));
        $this->assertEquals($response['requestId'], $otp['request_id']);
        $this->assertEquals($response['coreCode'], $otp['core_code']);
    }
}
