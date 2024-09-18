<?php

namespace Dmn\OnlineBankingOAuth2\Tests\Bpi;

use Dmn\OnlineBankingOAuth2\Bpi\TokenDecryptor;
use Dmn\OnlineBankingOAuth2\Tests\Bpi\TestCase;

class DecryptorTest extends TestCase
{
    /**
     * @test
     * @testdox It can decrypt token
     *
     * @return void
     */
    public function decrypt(): void
    {
        $token = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.AnK8Zr00AOXXJYM6Yc8ZAIZt92yjnC-U1ucG6JmRHKxOodK0xihsWxSqh9WMP7FzBHJLJD7nfZc9GNm1wDELa_6r_UDKiWCyGXobXh8k2IIoYQUynAMXvuXduyDvog9Kc2A4Bn9ia5WnaH6GSpktJYNav-xUAbiUVDbAZcJogxGHzMriR-tEEXUQajhpNrXXwjFY312zxfOO6q7L7wc7E9h2ri5PzYl17PHaUkvyisabq_gTr5I9xIyug916tEwnPOqsv30-y0ldzPR27yQqrk-GJXCXGhkoQzGtsyg-Hduq9pMrwVf8iTemPpuYVA04HeDDmKew1y1GxiF_2dwN1w.KHiaQVdc8G2FZ2nc7BCtDg.Nz5nVydlwiiT3SqLeyNTY1oBaajzv3O4zS2H4UI1cJ-xtHNFgp2qNBjE1dVodjaYYWjx4zCyCWaaVJ2bngSIjPtfHdDjx9WfKDtVDtsE-oiLMTuF48atfG8aUUyLdWdd-ZC3j9QZE6DUouM_jNWPu6ArPPJ8YuIDz_g6RJUemMgl7J3BHhrT5V6YCugWrrA14iC5MXHwPg8wts9XjuvKZjDcPTWP9L1OQAMGGnHKwJ9MjlF5n67QLZ5g68JrRRGIuOb--ZmyGX8eIGUGBDVMlH7ZKVBt771zh3E9m3owsHbo0y8D7gOe0yyhh_fYxCXrQG34uPl-dphiRE_ZLiBXqJAp1Z8VQQgdSRbreC_isMt_UEtt0IwZsSdUsi0xUHClBjF77-b_LD_YIThHq1NB4GrLfaMkNQ6ULO2_DNQRDEEBXmlBIwxQPayXHNa9xNja_4LPjKhfCOjd3V9X_SGY5Fw-Pvlw7OuRFm9NIDrE9w9bH0AK4_mIvG5OW_18JQ7fJlApvo7eii8NREDk8MWVzoOg_Ew-kzkubKIPAS5_HgB5bxPNH3g1xcmtmmop1C7jvOKIujzjtVQ01I1yE3gg2n2hAiI4Ocr_3E6bIcZoDAWD-dmiUPAbI2hpKVrnvjIU_M7gwqN_B6UFTD6i28xF7wqrNRkDv9epLiWRFVtF0mYV-Go555R7-rAggws4p7VpDmKE_rNB7OKMoFflELCtZrt8st0d2BnDjVjutLTyCVzDtSlVx7N9JIsPxPExBorV615nxIGoS640fH4XCvbgVlbfwcpkvUcN49GAm8ZHL7WZ41NKIaEZhgs7bgT2ags483B7hDY28afdTULq22qVerCaNK24Km8_MGw4wN59IjE.y1qC7gM0vnbpKpimFOQlug';

        $decryptor = new TokenDecryptor();

        $response = $decryptor->decrypt($token);
        $this->assertEquals(true, is_array($response));
    }
}
