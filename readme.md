# dmn/online-banking-oauth2
Available Drivers:
1. [BPI](#BPI)
2. [UnionBank](#UnionBank)

## Install

1. Install via composer
```
composer require dmn/online-banking-oauth2
```

2. Add this to your `config/services.php`
    ```php
    ...
    'bpi' => [
        'client_id' => env('BPI_CLIENT_ID'),
        'client_secret' => env('BPI_CLIENT_SECRET'),
        'redirect' => env('BPI_REDIRECT'),
        'auth_uri' => env('BPI_AUTH_URI', 'https://testoauth.bpi.com.ph/bpi/api/'),
        'api_gateway_uri' => env('BPI_API_GATEWAY_URI', 'https://apitest.bpi.com.ph/bpi/api/'),
        'auth_proxy' => env('BPI_AUTH_PROXY'),

        /**
         * You may use storage_path() here
         */
        'public_key' => __DIR__ . env('APP_PUBLIC_KEY'),
        'private_key' => __DIR__ . env('APP_PRIVATE_KEY'),
        'sender_certificate' => __DIR__ . env('SENDER_PUBLIC_KEY'),
    ],

    'unionbank' => [
        'client_id' => env('UNIONBANK_CLIENT_ID'),
        'client_secret' => env('UNIONBANK_CLIENT_SECRET'),
        'redirect' => env('UNIONBANK_REDIRECT'),
        'uri' => env('UNIONBANK_URI', 'https://api-uat.unionbankph.com/'),
        'partner_id' => env('UNIONBANK_PARTNER_ID'),
    ],
    ...
    ```

## How to use
1. Please refer to [laravel's socialite documentation](https://laravel.com/docs/8.x/socialite)

## BPI
1. Initiate transaction

    ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('bpi');
    $user = $socialite->user();

    $accounts = $user->accounts(); //Collection of accounts of a user

    $response = $user->initiateTransaction(
        'reference', // unique transaction reference or id
        $accounts->first(), // account number token
        100, // amount,
        'remarks', // remarks
    );
    ```
`$response` is an array of `transaction_id`, `mobile_number` and `mobile_number_token` that will be used for sending OTP.

2. Send OTP
    ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('bpi');
    $user = $socialite->user();
    $response = $user->sendOtp($transactionId, $mobileNumberToken);

    //$response = ['otp_valid_unitl' => 'Tue Dec 01 2020 11:54:30 GMT+0800 (DST)'];
    ```
`$response` will be the expiration of OTP.
<br/>User will receive an OTP from BPI.

3. Process
<br/>This is where the transaction will be processed.
    ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('bpi');
    $user = $socialite->user();
    $response  = $user->process($transactionId, $otp);

    // $response will be
    $response = [
            'confirmation_timestamp' => 'Tue Dec 01 2020 03:04:14 GMT+0800 (DST)',
            'confirmation_number' => '16068062539019003917'
    ];
    ```

4. Inquire <br/>
    You can inquire for the status of a transaction.
    ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('bpi');
    $user = $socialite->user();
    $response = $user->inquire($transactionId);

    // $response will be
    $response = [
        'status' => 'SUCCESS',
        'confirmation_timestamp' => 'Tue Dec 01 2020 03:09:03 GMT+0800 (DST)',
        'confirmation_number' => '16068065429288546789'
    ];
    ```

## UnionBank
1. Generate OTP
    ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $otp = $user->merchantPayOTP();
   
    /** return */
    $response = [
        'requestId' => '2711C83E7AB7BFE8BB2729B3A8FC8DB1',
        'coreCode' => '200',
    ];
    ```
2. Transact
    ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $reference = 'Som3Uniqu3SH';
    $amount = 100.11;
    $otherInformation = [
        'Payor' => 'Juan Sanchez',
        'InvoiceNo' => '123123123',
    ];
    $transaction = $user->merchantPay(
        $reference,
        $amount,
        $otherInformation
        'remarks',
        '2711C83E7AB7BFE8BB2729B3A8FC8DB1', // requestId from generate OTP
        '123123', // OTP received from generate OTP
    );

    /** return */
    $transaction = [
        'code' => 'TS',
        'reference' => '00000000000000000013',
        'state' => 'Credited Beneficiary Account',
        'uuid' => 'fd4235c6-04fb-436e-b07f-b6084d30b20b',
        'description' => 'Successful transaction',
        'type' => 'MERCHANT_PAYMENT',
        'amount' => 100.11,
        'ubp_tran_id' => 'UB2679320541179',
        'tran_request_date' => '2017-10-10T12:11:50.333',
        'signature' => '1a2c80dd625281edc1ad0b5a88784761daa34412ca4cc722755f90a5440fd32766bb46b2bb0677e51b7f3025039f0f44eb5b0fb55b2bb4dc6a19d8cc6edb1d63e4a7e0ad296309eb8963c9e8ba64811e9bc3dfd84de551437387efe69fedaf991d04aa36d6efaed4339153dff835fe1e00d610971da89eca97d50c72cfea8b25286682a2b60759cb7fc844ed3082112a327b35ef26185fbc3d9a34971202298beb21d5e028c1fc8bcad047700a912cd29730e6f42bd4e799caf2d653a8e006704602c736e4874e58aea07cf752f995d70b6eb3f9f73943f053aac23ce8797530a59be8f8a6cf6cc036e44edbe57762b587be9076ed8f0861235ddb92e173be1e'
    ];
    ```
3. Inquire
    ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $transaction = $user->merchantInquire('Som3ref');

    /** return */
    $transaction = [
        'code' => 'TS',
        'reference' => '00000000000000000013',
        'state' => 'Credited Beneficiary Account',
        'uuid' => 'fd4235c6-04fb-436e-b07f-b6084d30b20b',
        'description' => 'Successful transaction',
        'type' => 'MERCHANT_PAYMENT',
        'amount' => 100.11,
        'ubp_tran_id' => 'UB2679320541179',
        'tran_request_date' => '2020-12-02T02:23:59.000Z',
        'created_at' => '2020-12-02T21:31:51.000Z',
        'updated_at' => '',
        'signature' => '1a2c80dd625281edc1ad0b5a88784761daa34412ca4cc722755f90a5440fd32766bb46b2bb0677e51b7f3025039f0f44eb5b0fb55b2bb4dc6a19d8cc6edb1d63e4a7e0ad296309eb8963c9e8ba64811e9bc3dfd84de551437387efe69fedaf991d04aa36d6efaed4339153dff835fe1e00d610971da89eca97d50c72cfea8b25286682a2b60759cb7fc844ed3082112a327b35ef26185fbc3d9a34971202298beb21d5e028c1fc8bcad047700a912cd29730e6f42bd4e799caf2d653a8e006704602c736e4874e58aea07cf752f995d70b6eb3f9f73943f053aac23ce8797530a59be8f8a6cf6cc036e44edbe57762b587be9076ed8f0861235ddb92e173be1e'
    ];
    ```

4. Customer Instapay Fund Transfer 
   
   3.1. Instapay Fund Transfer Via Customer
    ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
   
    $address = new Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Address;
    $address->setLine1('GRACE');
    $address->setLine2('PARK CALOOCAN CITY');
    $address->setCity('Caloocan');
    $address->setProvince('142');
    $address->setCountry('204');
    $address->setZipCode('1900');
    $sender = new Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Sender;
    $sender->setName('Juan Dela Cruz');
    $sender->setAddress($address);
   
    $address = new Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Address;
    $address->setLine1('241 A.DEL MUNDO ST BET. 5TH 6TH AVE GRACE');
    $address->setLine2('PARK CALOOCAN CITY');
    $address->setCity('Caloocan');
    $address->setProvince('142');
    $address->setCountry('204');
    $address->setZipCode('1900');
    $beneficiary = new Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Beneficiary;
    $beneficiary->setName('Rachelle');
    $beneficiary->setAccountNumber('107324511489');
    $beneficiary->setAddress($address);
    
    $remittance = new Dmn\OnlineBankingOAuth2\UnionBank\Instapay\Remittance;
    $remittance->setAmount(30.00);
    $remittance->setPurpose(1001);
    $remittance->setReceivingBank(161312);
    $remittance->setInstruction('Instruction data..'); //optional

    $transaction = $user->customerInstapayTransfer(
         'UB123',
         $sender,
         $beneficiary,
         $remittance
    );

    /** return */
    $transaction = [
        'reference' => 'UB123',
        'ubp_tran_id' => "UB2706855175254",
        'created_at' => "2021-07-22T08:09:41.311",
        'state' => "Credited Beneficiary Account"
    ];
    ```
   3.2. Instapay Inquire Transfer Via Customer
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $transactions = $user->customerInquireInstapayTransfer('UB123');
   
    /** return */
    $transaction = [
       [
          "transfer_id" => "UB7567161618794",
          "type" => "INSTAPAY",
          "created_at" => "2015-10-03T07:29:16.000Z",
          "state" => "Credited Beneficiary Account",
          "reference" => "UB123"
       ]
    ];
   ```
   3.3. Instapay List Receiving Banks via Customer
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $receivingBanks = $user->customerListInstapayReceivingBanks();
   
    /** return */
    $receivingBanks = [
       [
          "code" => 3237,
          "bank" => "Allbank Corp.",
       ],
       [
          "code" => 148120,
          "bank" => "Sun Savings Bank",
       ],
       [
          "code" => 161207,
          "bank" => "Sterling Bank Of Asia",
       ]
    ];
   ```

5. Partner Instapay Fund Transfer

   4.1. Instapay Fund Transfer Via Partner
    ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
   
    $address = new Dmn\OnlineBankingOAuth2\UnionBank\Address;
    $address->setLine1('GRA CE');
    $address->setLine2('PARK CALOOCAN CITY');
    $address->setCity('Caloocan');
    $address->setProvince('142');
    $address->setCountry('204');
    $address->setZipCode('1900');
    $sender = new Dmn\OnlineBankingOAuth2\UnionBank\Sender;
    $sender->setName('Juan Dela Cruz');
    $sender->setAddress($address);
    
    $address = new Dmn\OnlineBankingOAuth2\UnionBank\Address;
    $address->setLine1('241 A.DEL MUNDO ST BET. 5TH 6TH AVE GRACE');
    $address->setLine2('PARK CALOOCAN CITY');
    $address->setCity('Caloocan');
    $address->setProvince('142');
    $address->setCountry('204');
    $address->setZipCode('1900');
    $beneficiary = new Dmn\OnlineBankingOAuth2\UnionBank\Beneficiary;
    $beneficiary->setName('Rachelle');
    $beneficiary->setAccountNumber('109450542671');
    $beneficiary->setAddress($address);
   
    $remittance = new Remittance;
    $remittance->setAmount("30");
    $remittance->setPurpose(1001);
    $remittance->setReceivingBank(161312);
    $remittance->setInstruction('Instruction data..'); //optional

    $transaction = $user->partnerInstapayTransfer(
        'TEST4321',
        $sender,
        $beneficiary,
        $remittance
    );

    /** return */
    $transaction = [
        "code" => "TS",
        "reference" => "TEST4321",
        "ubp_tran_id" => "UB1706",
        "tran_request_date" => "2015-10-03T15:29:16.333",
        "state" => "Credited Beneficiary Account",
        "uuid" => "95607606-2f54-4e6e-be1a-58e008960a26",
        "core_ref_id" => "2302560968054408760",
        "trace_no" => "211272"
    ];

    ```
   4.2. Instapay Inquire Transfer Via Partner
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $transactions = $user->partnerInquireInstapayTransfer('UB123');
   
    /** return */
    $transaction = [
       [
          "reference" => "TEST4321",
          "code" => "TS",
          "uuid" => "95607606-2f54-4e6e-be1a-58e008960a26",
          "description" => "Credited Beneficiary Account",
          "type" => "INSTAPAY",
          "ubp_tran_id" => "UB6219682539904",
          "amount" => "30.00",
          "created_at" => "2021-07-25T18:56:10.000Z",
          "updated_at" => null
       ]
    ];
   ```
   4.3. Instapay List Receiving Banks via Partner
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $receivingBanks = $user->partnerListInstapayReceivingBanks();
   
    /** return */
    $receivingBanks = [
       [
          "code" => "3237",
          "bank" => "Allbank Corp.",
          "brstn" => null
       ],
       [
          "code" => "148120",
          "bank" => "Sun Savings Bank",
          "brstn" => null
       ],
       [
          "code" => "161207",
          "bank" => "Sterling Bank Of Asia",
          "brstn" => "011190019"
       ]
    ];
   ```
   4.4. Instapay List Libraries via Partner
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $libraries = $user->partnerListInstapayLibraries();
   
    /** return */
    $libraries = [
       [
          "code" => "1001",
          "description" => "Donation"
       ],
       [
          "code" => "1002",
          "description" => "Payment"
       ],
       [
          "code" => "1003",
          "description" => "Fund Transfer"
       ]
    ];
   ```

6. Customer Pesonet Fund Transfer

   5.1. Pesonet Fund Transfer Via Customer
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
   
    $address = new Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Address;
    $address->setLine1('241 A.DEL MUNDO ST BET. 5TH 6TH AVE GRACE');
    $address->setLine2('PARK CALOOCAN CITY');
    $address->setCity('Caloocan');
    $address->setProvince('142');
    $address->setZipCode('1900');
    $address->setCountry('204');
    $sender = new Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Sender;
    $sender->setName('Juan Dela Cruz');
    $sender->setAddress($address);
     
    $address = new Address;
    $address->setLine1('241 A.DEL MUNDO ST BET. 5TH 6TH AVE GRACE');
    $address->setLine2('PARK CALOOCAN CITY');
    $address->setCity('Caloocan');
    $address->setProvince('142');
    $address->setZipCode('1900');
    $address->setCountry('204');
    $beneficiary = new Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Beneficiary;
    $beneficiary->setName('Juan');
    $beneficiary->setAccountNumber('107324511489');
    $beneficiary->setAddress($address);
   
    $remittance = new Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Remittance;
    $remittance->setAmount(3000.00);
    $remittance->setPurpose('5 632');
    $remittance->setReceivingBank(161203);
    $remittance->setInstructions('instructions data');

    $transaction = $user->customerPesonetTransfer(
       '12345678',
       $sender,
       $beneficiary,
       $remittance
    );

    /** return */
    $transaction = [
       "reference" => "12345678",
       "ubp_tran_id" => "UB2454429517477",
       "created_at" => "2021-07-23T02:43:42.604",
       "state" => "Sent for Processing"
    ];
   ```
   5.2. Pesonet Inquire Transfer Via Customer
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $transactions = $user->customerInquirePesonetTransfer('12345678');
   
    /** return */
    $transaction = [
       [
          "transfer_id" => "UB4377175400409",
          "type" => "PESONET",
          "created_at" => "2018-08-10T00:40:46.000Z",
          "state" => "Sent for Processing",
          "reference" => "12345678"
       ],
       [
          "transfer_id" => "UB9653097653551",
          "type" => "PESONET",
          "created_at" => "2021-02-17T21:01:45.000Z",
          "state" => "Sent for Processing",
          "reference" => "12345678"
       ]
    ];
   ```
   5.3. Pesonet List Receiving Banks via Customer
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $receivingBanks = $user->customerListPesonetReceivingBanks();
   
    /** return */
    $receivingBanks = [
       [
          "code" => "79570",
          "bank" => "One Network Bank, Inc. (A Rural Bank)"
       ],
       [
          "code" => "135038",
          "bank" => "Philippine Business Bank"
       ]
    ];
   ```

7. Partner Pesonet Fund Transfer

   6.1. Pesonet Fund Transfer Via Partner
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();

    $address = new Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Address;
    $address->setLine1('241 A.DEL MUNDO ST BET. 5TH 6TH AVE GRACE');
    $address->setLine2('PARK CALOOCAN CITY');
    $address->setCity('Caloocan');
    $address->setProvince('142');
    $address->setZipCode('1900');
    $address->setCountry('204');
    $sender = new Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Sender;
    $sender->setName('Juan Dela Cruz');
    $sender->setAddress($address);

    $address = new Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Address;
    $address->setLine1('241 A.DEL MUNDO ST BET. 5TH 6TH AVE GRACE');
    $address->setLine2('PARK CALOOCAN CITY');
    $address->setCity('Caloocan');
    $address->setProvince('142');
    $address->setZipCode('1900');
    $address->setCountry('204');
    $beneficiary = new Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Beneficiary;
    $beneficiary->setName('Juan');
    $beneficiary->setAccountNumber('107324511489');
    $beneficiary->setAddress($address);

    $remittance = new Dmn\OnlineBankingOAuth2\UnionBank\Pesonet\Remittance;
    $remittance->setAmount(3000.00);
    $remittance->setPurpose('5 632');
    $remittance->setReceivingBank(161203);
    $remittance->setInstructions('instructions data');

    $transaction = $user->partnerPesonetTransfer(
       '12345678',
       $sender,
       $beneficiary,
       $remittance
    );

    /** return */
    $transaction = [
       "code" => "SP",
       "reference" => "TEST1231SB",
       "state" => "Sent for Processing",
       "uuid" => "f21b837e-3044-4902-8b10-44c58aabd400",
       "description" => "Transaction is sent for processing",
       "type" => "PESONET",
       "amount" => "30.00",
       "ubp_tran_id" => "UB8291906576991",
       "remittance_id" => "E90118RYQXQJMFIX",
       "tran_request_date" => "2018-08-10T08:40:45.897"
    ];
   ```
   6.2. Pesonet Inquire Transfer Via Partner
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $transactions = $user->partnerInquirePesonetTransfer('TEST1231SB');
    
    /** return */
    $transaction = [
       "reference" => "TEST1231SB",
       "code" => "SP",
       "state" => "Credited Beneficiary Account",
       "uuid" => "50dcf474-c25f-486d-a8bc-286827607509",
       "description" => "Credited to the customer's account",
       "type" => "PESONET",
       "amount" => "123.00",
       "ubp_tran_id" => "PESONET",
       "remittance_id" => "E90118MRDEZNQOGM",
       "created_at" => "2020-11-26T04:14:41.000Z",
       "updated_at" => null
    ];
   ```
   6.3. Pesonet List Receiving Banks via Partner
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $receivingBanks = $user->partnerListPesonetReceivingBanks();
    
    /** return */
    $receivingBanks = [
        [
            'code' => '79570',
            'bank' => 'One Network Bank, Inc. (A Rural Bank)',
        ],
        [
            'code' => '135038',
            'bank' => 'Philippine Business Bank',
        ]
    ];
   ```

8. Customer Unionbank to Unionbank Fund Transfer

   7.1. Unionbank to Unionbank Fund Transfer Via Customer
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();

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

    $transaction = $user->customerIntraTransfer(
        '12345678',
        '105239265518',
        100,
        $info,
        'Transfer remarks'
    );

    /** return */
    $transaction = [
       "reference" => "12345678",
       "ubp_tran_id" => "UB8506416323919",
       "created_at" => "2015-10-03T15:29:16.333",
       "state" => "Credited Beneficiary Account"
    ];
   ```
   7.2. Unionbank to Unionbank Inquire Transfer Via Customer
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $transactions = $user->customerInquireIntraTransfer('12345678');
   
    /** return */
    $transaction =  [
       [
          "transfer_id" => "UB4062703398837",
          "type" => "TRANSFER",
          "created_at" => "2020-07-22T05:13:52.000Z",
          "state" => "Credited Beneficiary Account",
          "reference" => "12345678"
       ],
       [
          "transfer_id" => "UB5284403971363",
          "type" => "TRANSFER",
          "created_at" => "2015-10-03T07:29:16.000Z",
          "state" => "Failed to Credit Beneficiary Account",
          "reference" => "12345678"
       ]
    ];
   ```

9. Partner Unionbank to Unionbank Fund Transfer

   8.1. Unionbank to Unionbank Fund Transfer Via Partner
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();

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

    $transaction = $user->partnerIntraTransfer(
        '12345678',
        '105239265518',
        100,
        $info,
        'Transfer remarks'
    );

    /** return */
    $transaction = [
       "code" => "TS",
       "reference" => "UB127",
       "state" => "Credited Beneficiary Account",
       "uuid" => "d23ff4bf-bcef-4c58-b98c-c0d40b35294f",
       "description" => "Successful transaction",
       "type" => "TRANSFER",
       "amount" => "100",
       "ubp_tran_id" => "UB6391875781961",
       "tran_request_date" => "2017-10-10T12:11:50.333"
    ];
   ```
   8.2. Unionbank to Unionbank Inquire Transfer Via Partner
   ```php
    $socialite = Laravel\Socialite\Facades\Socialite::driver('unionbank');
    $user = $socialite->user();
    $transactions = $user->partnerInquireIntraTransfer('12345678');
   
    /** return */
    $transaction = [
       [
          "ubp_tran_id" => "UB6323361452962",
          "type" => "TRANSFER",
          "created_at" => "2020-10-05T04:11:50.000Z",
          "state" => "Failed to Credit Beneficiary Account",
          "reference" => "UB127"
       ],
       [
          "ubp_tran_id" => "UB1306832082994",
          "type" => "TRANSFER",
          "created_at" => "2017-10-10T04:11:50.000Z",
          "state" => "Credited Beneficiary Account",
          "reference" => "UB127"
       ]
    ];
   ```
## Get the last request

```php
$user->getLastRequest();
// This will return a GuzzleHttp\Psr7\Request 
```
Reference [here](https://docs.guzzlephp.org/en/stable/psr7.html#requests)

## Get the last request in array format

```php
$request = $user->getLastRequestArray();
// This will return an array with format:
$request = [
    'uri' => '....',
    'method' => '....',
    'options' => '....', // this is where the headers, json and other guzzle otpions will be located
];
```
