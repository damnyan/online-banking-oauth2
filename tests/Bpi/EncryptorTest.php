<?php

namespace Dmn\OnlineBankingOAuth2\Tests\Bpi;

use Dmn\OnlineBankingOAuth2\Bpi\TokenEncryptor;

class EncryptorTest extends TestCase
{
    /**
     * @test
     * @testdox It can encrypt array
     *
     * @return void
     */
    public function encrypt(): void
    {
        $data = [[
            'accountNumber' => 'XXXXXX2988',
            'accountNumberToken' => '1a153c4adfe76818d2e2ea017c6164842f738b2432689610de6048cf0f04464e8e2e7181493a8896c7e0ee153b5fc5928',
            'displayOrder' => '001',
            'accountPreferredName' => 'Peso Account',
            'institution' => 'BPI',
            'accountType' => 'SAVINGS ACCOUNT',
        ]];

        $encryptor = new TokenEncryptor();

        $jwe = $encryptor->encrypt($data);
        $segments = explode('.', $jwe);

        $this->assertTrue(is_string($jwe));
        $this->assertEquals(5, count($segments));
    }
}
