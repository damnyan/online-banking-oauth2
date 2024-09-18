<?php

namespace Dmn\OnlineBankingOAuth2\Tests\Bpi;

use Dmn\OnlineBankingOAuth2\Bpi\Exceptions\ClientException;
use Dmn\OnlineBankingOAuth2\Bpi\Exceptions\ServerException;
use Dmn\OnlineBankingOAuth2\Tests\Bpi\TestCase;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Str;

class TransactionTest extends TestCase
{
    /**
     * @test
     * @testdox It can initiate transaction
     *
     * @return void
     */
    public function initiateTransaction(): void
    {
        $response = [
            'token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.JZBkrVTqenF72lnEVNQB2WtWyJTYMcK8MACsxd3cpchNjxJA4zViP6OwOgVoJfKc5xwGTmd_4dw0kz034dtsao5Lj6KOG3k866hfEP7-0b3-C__8yPW-vynontEyYoe9acJpC4GBOjJTZBnlMnZQCU-z-zMXeED3H3ctWZIplgQ2QxPNE-b_rKRAu6koymFiZGAYTMnPG83AIwSmDH_hOZfyHAAaJ_WRai6Z9G6sIrrF1OQUUymrrT7vxGBvpmpeu2MRoL_c5lIThpfePovzMR6G_VKuQtHkjDRxi2vkPTJtnElTqXEbP6_bnV-0vkv1LDn012KseNCGiqKbwXQK7g.cNXh1Tr6zl8DGG1jrmn0fw.TH0vT7XL2_8f-JSLQG6iz6DkXO112bRmNFd2UP1gajIMi7-VK4QzbxOHZF-UwszfbDxq2rXDWQ7xhO599xik0G8_zcuG5dtq-Nx4vTJ--8tkUWze8GGHWhNFxixFADVG4LweVCsTDcP734Bpl16t91FMg6NwU4X3zCa8YPZImhDRB_NVGh3Z-Fji55fx3oq5K0yC5vSdjC-tkv7LIHkJXSzvsb3CS8APY6Dn3y5QL0_bPlC80gi1y1HrQ5frPi2I_Q_BfpdqGvuKxssxSbc6HCKLXuaSqYSE29XC-mgAnCIsjoOyzWkTwX6CyvShcNOdn3ilm--9txh0SiyfNK7LZwhkRZejc-O7dAnz5xBHR-RW3ZwMSOl6LBq7zlBmdpwi_VpD349pjU73WbogW-ckCw5xWI230bsJmlv1o_o1Sak6EZrugemzCAeOe4kJMKc58jQ-OFCGP0SaVy2zW-oVZoAK_H0rBUXG8EI64Ivw8cRx6NN4uSk1qWNDIuo_SvDgObIRdjFpELUxYVWYj5lwjHEWuhV_5D8zlaAP9djEG1oCB7uH_fqG0gnMm0kce3af1A1mGkkX-qywZuy53HDccC4Yjc8OHTFuaBOb0aw2bYovHkhbpvkCW6h73LbMIxWPtLxm19a-e575GqAwM8jBvmkNvpSFhjbL2Gfg2OI1T2cyHwbosaZc3XvXm-ixP2Lh7W46IxU26aNCNKLZPrVpjgSzEtB9x4Dja3r0kFObGnTMl8yznF7pQmGYDXOVWoG5mA-yWGs6hgWAaHKPjJEmbRBg1psvHN5ddnumSjb51JmCPkktuSnnb_l4PRDEvm_KMe8cDs62emBk6O4x5Vf2QZIdFdLs3AyJZ-RNPD4-3ObL1aOM2rrSCAZInD-4BVjIipa9cUFLGYVNiBDRa1m2ZkOqlXqIT6D564PI3ejnaODz3XShPTuY-saOxUPDrlsuPHLAgyPNb8k6uj28nYDgmQ.6_K2c0q44e2ab819A1Y4Bg',
        ];

        $this->mockResponse(
            $response,
            200,
            ['transactionId' => '8fba4119-8868-464f-996b-d7be3ab6e7e5']
        );

        $reference = Str::random(16);
        $user      = $this->user();
        $account   = $user->accounts()->first();

        $transaction = $user->initiateTransaction(
            $reference,
            $account['accountNumberToken'],
            100,
            'remarks'
        );

        $this->assertTrue(is_array($transaction));
        $this->assertTrue(isset($transaction['transaction_id']));
        $this->assertTrue(isset($transaction['mobile_number']));
        $this->assertTrue(isset($transaction['mobile_number_token']));
    }

    /**
     * @test
     * @testdox It can handle client errors on initiating transaction
     *
     * @return void
     */
    public function initiateTransactionClientError(): void
    {
        $response = [
            'token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.IzzxQQmHRCrkTi_ZwCQn0UCGNrM_Z2MI7vSIZCarAzTcZOVEz8ceKeK_T5xWrXduRGt1boxuY3FoFZQt53M6kK6pthYJ2cAGNMrSTPPdKcHRRXYmNXFpYEgsifDzUIXeyTQ3NamzCR5vMUKBOG09qBKC5A8bZdmB2M-Ym84n2NeYYpBjF7vNdv9hU-vavKhRpLFatmiDPTZz_b7gKeWgHioJ-nkjroRRO_ZWXLGPxtcisVLWx9WRuS3kbGhZvcXTq0GHwXdpKk9VyB3zwmQYgPtkplllP-ZssjS_VsryNsi1pmyhhDjW7exO9a8mWsUbRrgnd8y515yM1AeSNKAN8A.9lyzN5VKS_YlGx6DcVFH9Q.ddru7YZXOyF3Rjc3HQzORNOYMtQ2wAYiIP8h1VvVtHCpalWttq7uhr7lSpuV_yY-ocGF02f0PBHn9Urn5v6vWgKdAOZKK2Wpuw1CqmYk-V6B8Trt3uOi5iFNjh9rmAHKGnvA_tH64XkGFBexfxA-XDCO2JEvm9-sk5wi1_IZNE8o7mrXlu9-U6QK5w0NE8bfjWiXK9zJ4wW1TOBCU39jg8zBqDka7NtHwyG1DIMyfzcrD77Xblz3R7zzMEsXYyewd7pAszqeFizjaDszcVxvi_NomaHQnUz5c-vUuln1Bf5za9YknreuvFFvGEw3dGaURvslZrW7rHozSFLvzuErr_kh-DNfj2Ly8mu2ZQ8iTTCYFc6kL3MBV5gfvoW0NfdrdoJwVBebPt_Bb9N0qJxODx8v4ijVPv7T-Na2Xi9gWvfrRGG6e1SlA9uW71B1E26CHcYq8VJS1A99WAZbKe8BRgIzwbXxhlxHYSIHzIejk4Sf21B6v8dN1iHUFoYyoIYjO3eapTaF5A9YbnRjfu5csDwS41RjgfHyKrvvzrE6L4A-EX4SE365FWgCfnl9Rka1bJXqKTSllGttJwerW08Iy43z_0Zv_EXKK1jx4zKstiBRys6xe-jjcQ2fmn3f4HfBI0RxzNaqSNjv8jtpchPFsFPMHeTvNOOK63AWDwLvIpRaXFvfPsEeccXlsTydaXrwumZvwWcySx1viG-w4BLasOVLmJhgPheoJCiDJeS1694omfXOj7YyfF_1AL_WfzsfJZ66bin8jOlsLYioTIiQVsi3SUp7q7CPwb42ovGoGuOqD3jrGZeck-FDeCPWfTbYNqHu5tIDFiY8vu3yQlArKSQu4N7-giALZfzA1vRfmQY.R5P0oLCOLKy426tkUDbR5Q',
        ];

        $this->mockResponse($response, 400);

        $reference = Str::random(16);
        $user      = $this->user();
        $account   = $user->accounts()->first();

        try {
            $user->initiateTransaction(
                $reference,
                $account['accountNumberToken'],
                100,
                'remarks'
            );
        } catch (ClientException $exception) {
            $this->assertEquals('FTUVE002', $exception->code());
            $this->assertEquals(
                'Account number token missing or invalid',
                $exception->getMessage()
            );
        }
    }

    /**
     * @test
     * @testdox It can handle server errors on initiating transaction
     *
     * @return void
     */
    public function initiateTransactionServerError(): void
    {
        $response = [
            'token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.IzzxQQmHRCrkTi_ZwCQn0UCGNrM_Z2MI7vSIZCarAzTcZOVEz8ceKeK_T5xWrXduRGt1boxuY3FoFZQt53M6kK6pthYJ2cAGNMrSTPPdKcHRRXYmNXFpYEgsifDzUIXeyTQ3NamzCR5vMUKBOG09qBKC5A8bZdmB2M-Ym84n2NeYYpBjF7vNdv9hU-vavKhRpLFatmiDPTZz_b7gKeWgHioJ-nkjroRRO_ZWXLGPxtcisVLWx9WRuS3kbGhZvcXTq0GHwXdpKk9VyB3zwmQYgPtkplllP-ZssjS_VsryNsi1pmyhhDjW7exO9a8mWsUbRrgnd8y515yM1AeSNKAN8A.9lyzN5VKS_YlGx6DcVFH9Q.ddru7YZXOyF3Rjc3HQzORNOYMtQ2wAYiIP8h1VvVtHCpalWttq7uhr7lSpuV_yY-ocGF02f0PBHn9Urn5v6vWgKdAOZKK2Wpuw1CqmYk-V6B8Trt3uOi5iFNjh9rmAHKGnvA_tH64XkGFBexfxA-XDCO2JEvm9-sk5wi1_IZNE8o7mrXlu9-U6QK5w0NE8bfjWiXK9zJ4wW1TOBCU39jg8zBqDka7NtHwyG1DIMyfzcrD77Xblz3R7zzMEsXYyewd7pAszqeFizjaDszcVxvi_NomaHQnUz5c-vUuln1Bf5za9YknreuvFFvGEw3dGaURvslZrW7rHozSFLvzuErr_kh-DNfj2Ly8mu2ZQ8iTTCYFc6kL3MBV5gfvoW0NfdrdoJwVBebPt_Bb9N0qJxODx8v4ijVPv7T-Na2Xi9gWvfrRGG6e1SlA9uW71B1E26CHcYq8VJS1A99WAZbKe8BRgIzwbXxhlxHYSIHzIejk4Sf21B6v8dN1iHUFoYyoIYjO3eapTaF5A9YbnRjfu5csDwS41RjgfHyKrvvzrE6L4A-EX4SE365FWgCfnl9Rka1bJXqKTSllGttJwerW08Iy43z_0Zv_EXKK1jx4zKstiBRys6xe-jjcQ2fmn3f4HfBI0RxzNaqSNjv8jtpchPFsFPMHeTvNOOK63AWDwLvIpRaXFvfPsEeccXlsTydaXrwumZvwWcySx1viG-w4BLasOVLmJhgPheoJCiDJeS1694omfXOj7YyfF_1AL_WfzsfJZ66bin8jOlsLYioTIiQVsi3SUp7q7CPwb42ovGoGuOqD3jrGZeck-FDeCPWfTbYNqHu5tIDFiY8vu3yQlArKSQu4N7-giALZfzA1vRfmQY.R5P0oLCOLKy426tkUDbR5Q',
        ];

        $this->mockResponse($response, 500);

        $reference = Str::random(16);
        $user      = $this->user();
        $account   = $user->accounts()->first();

        try {
            $user->initiateTransaction(
                $reference,
                $account['accountNumberToken'],
                100,
                'remarks'
            );
        } catch (ServerException $exception) {
            $this->assertEquals('FTUVE002', $exception->code());
            $this->assertEquals(
                'Account number token missing or invalid',
                $exception->getMessage()
            );
        }
    }

    /**
     * @test
     * @testdox It can send OTP
     *
     * @return void
     */
    public function sendOtp(): void
    {
        $response = [
            'token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.EkF7GPQq0W0zMya_Iqq7rD_O6dfUEX706OII7_w1wDvyMaNYFqXIxckNsjX-LEiZp50jYGtldfFWnsQ2UNCkgwkXU_Bq61A8KuQJPsjX1Jd70A0OP69dRnc8hOntOsHZ6AyPfEastvaVYJ5zCIUj5TQIIyKg0KvqCN6SRpKNqxdsgK3Nbs2qvAhLeW51e35GfgkuQd6uAsbTDd6ABAz9YKRZDUtwWBCdHmc21Rm3demO8EZikn8CUkL-90F_T1GPpkhTiXX9262saEKApwZK8fGiJiJ7xLlumInPTvYZwNjl-Jq9wIhSXAnIjdTMtX7igBNpjxhWuOak1B6k2xbOBg.nDB8uAc7TJIU46-8Ox-gPQ.mgcI7KE_mrl2soJMABGCobgy_Ofuzqasv7rBcNufzmJnxamJt4q47ijkuuFIYOnjYaFvz2OrKLWQa7Zrf6DT83AA11aIxvp0L9GCobbQC-VYe4QRMZRzwCCGccSxa3x3PXL_sYRYoavc1W7y3FENvWdQRm-wuBsm9GTdIJzbw9DDJ2ej7oIotdhfmubpq-6kxS8ImshnJru-_rvkKnY17my1dD_GoP9dSGT2fJiFDOJ6--HI7BC-UpNT0dlL6OamgtMNHzmP3pQo7JfCWxYTQH6eNJ3miPatrRs7951n0ajOpfamxAkO-yARD2fhKOUjkm1Bcs06kcwTR2qzUVr4H3883DZ8vJg5NcrP2mcJpJxV_YJjsTxqxk5PM8Tkr5vEAn1nKND-7X4qP0KgOshap64idgmKdqtMmglnSUGzW_5YyG8wfQZL_3mj0Z8GxAZREug9Kbm_gAmZPlu1dJVgfArAcSrlsjIaEP6o1XE6yzPhf5lji15Rm6kxvzJq72y6iefdsiZcuOUpTo0JmT68eH1EZNyN850DwRXZpuXCw1E4DSO_HG84RL-4c_8WThFX-nyPQNSFKiDECgktYhWLDS3tnI8l4Nj2LlCSB0PkLHHtEdZNThXSbwCNOekSKoNdOAROfMRuraorqGuZ4Gfyv99rfEnccpYEgwo4uHFgC9Vvm6W3VBIYdvfKim9YzMhsM0JuMi7kG3PcZzu7AV22zXtzRzAzxun1Jmx0jG6pfykMA_jFNnxFfLZpPH8WbNy0NHyxecEemU2_I3P5pHZxbUYs-3ehjmCB3QzVGNtkPHA5DSL-_bKKJxbkJ_Ln2IZCQv7sCiKinW-yo6DlsdQ9SWYUAHUysIzKwLZRNe6x61FPI83_ebyVrlUyvJKUuLP4sHxWq5O-uVdv_PUtHQGymXB_-adE-ndvszdxykmgjz0.1oVBx7D1Qmw09iykwiIx5w',
        ];

        $this->mockResponse($response);

        $user              = $this->user();
        $transactionId     = '06545279-d229-4475-8452-792ee523deee';
        $mobileNumberToken = '94277b576a8a0ffcb323731288dc5e75';

        $response = $user->sendOtp(
            $transactionId,
            $mobileNumberToken
        );

        $this->assertTrue(is_array($response));
        $this->assertEquals(
            'Tue Dec 01 2020 11:54:30 GMT+0800 (DST)',
            $response['otp_valid_until']
        );
    }

    /**
     * @test
     * @testdox It can process a transaction
     *
     * @return void
     */
    public function process(): void
    {
        $response = [
            'token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.XbrH1vpNB9DlfbSZdM9881gqYz_s3Q8OWZixOQ2lRxUdYYhYpScSgUP2egjgDeqTyWSIWCy9YNZROA735cLq5xmEofmKiYS3_1igAUYqvn--DhO_GB0nRIfglVNSDoe6KZfwfn8q6K6pe7jvW-I8WTRvMcm1_MTMit7oObg93rYjPJMe3uHtzCh9A0fEJ4M5y1Tif6QuSIwFTCw5rqb5U64dO2pgSf91JY7vWizIjYBYr90kmu9WyEVnnvvFUUjF9azgz32Dns2bXml-Y7otasN1_8-C5K7GiQGXqAcz46vYBygaiD3FoglEomylH7eTD-tiLPhn03q4xlLc9ZrZ3A.NtCGnHGhkVyRJdim399YuQ.Du_cA8wNyaRTkqe7mmucxMcu1udvy4u7zMHIORkHx6c1GH1N9-HIfSBh5wKOCsFM9RJ5dwTuN5MLe10HUaFgHk2sbQ8hOdiKTawcPZV80wDx0rzzng1ebgYwOgytOyBXVE89olPkY8zbMOmAvjPjGfgc-1OlPpHB-HGHtMSV2B9rF1sXA9c4DWrg_xck7jPTU1A4JBjpWA9ENKNwRWzUq3p5uUzZJ5XuGKqqXUAMGgVr7oyu0liA-9c65CoNwXO5W1bKI191Lbdh60RLG39EDXanwuqI6oajymQC1X5uKMVTMs62EG46a_hjJA11noHHsTQlR8OkVLJ2ktnnNJrmo7ehpINrppuKcSKRClPeL1Hg58eJp9mW5IoKyXWo4_QkiDcrnMLxc2cEXxR7z5YGGkXfQ1kYXNvnNYUjmY07m1bo_a-IXpYmQtx4xDHvV214SfuyjN-YMUuwqlwurzOM4BT0nAIXDshOxDLXIS9-JQ0ZzThLufX6sXCakwB0Ss5TGFJsxWdcxYVzdu8vS3LlxaSvTJPKYKYKnMDrU7kzd9wRgz8Y2mpLIyraz8wAxHvjlMbQZNJp8XjccUHHp3lD6uMOFKumMRIaiCcwnCmEET7DdLJvSMY7bPDOVo-sU2NE2yoXnQJNPxoCvGmiSTGMMQhxBCzVzTSdlbiZQYq5bsWyxtJuPIFukEyiIiODapHJvV46XfS1obZcT_hBg2-PGvddXXRGUX5ljklMNq8ktmc3VC8g-g9Wf8yhxXqPvHSUbzQujhJRzPiv4yF91N9mvIYYRWcb2bhnzDjCxboxO3zT7VXUlh0wYyDcMm9YwwcmCMU4-5WMfKVlQP4m1l-nNoRYCA6MN6a_FlogQ7xMzYGD_UwlLr8djiq1SKs7fgEIZ3LtQIT0zA0OwcjoJy0FiOD2UmB_Nv0Diqzl3GTIGjJwO6M-z3x6xpCA_PqcQHIxn5Vqays-3U7uV6grf8gbdhzn6DDgywLUuITd7yUZPZuw0rfZbmUY0hp4nY_cAyZo.Mvv8epePO7gMYFwx33VLFA',
        ];

        $this->mockResponse($response);

        $user          = $this->user();
        $transactionId = '492be839-7834-41f8-9b34-c093031cf3c2';
        $otp           = '060166';

        $response = $user->process($transactionId, $otp);

        $this->assertTrue(is_array($response));
        $this->assertEquals(
            'Tue Dec 01 2020 03:04:14 GMT+0800 (DST)',
            $response['confirmation_timestamp']
        );
        $this->assertEquals(
            '16068062539019003917',
            $response['confirmation_number']
        );
    }

    /**
     * @test
     * @testdox It can inquire for transaction status
     *
     * @return void
     */
    public function inquire(): void
    {
        $response = [
            'token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.L-Sp2hKgOm1DarK-oxrjK6GJMeKeRZpGaIIFrzvbF2rMOrrozeHK8dqSIbMYPappzbMespTsCfzG9NwLKlLsogaGKJE77OW7txMMEtwo2SzPqy5UXlmcKwJunghImk7eCkueB6mJDF2MKSgkOPXisAjChvZcNt2hCnGjq_Dn9BBRypwM2CkCwt9aOWEL2ycO09Wk3sagh-ZV-Uz6juSfVaxgY4pm9Et0nGakq0pBqFiMsBTxvSRDn-HTHGD9lmC3rkSK9-I0y1YqAroj8oEqIYss_20U35GwI5dOPgbwj2F551L1zDoQvm8zr9pAnHnsO9l-4HPMb3SDd1yynpCc3A.RMkkh-43bHl5o_SaohFxXA.9cHxW3p9wQoEAN3PxQyrP8ZdygEVFAdPdqpNQWFOgEvJtfBF7t-uYlGlJT7Mvv-5JkK-QmhwnSer9BRdpFlOMIjrRUNgp81xwB7uhRxem8BXBhTSMH2WKV5RiMFAXfmjaY7eT11qgXsIwwMRBls_SnC_ZPPZtLpm0jR40HOH7-Veahe7VTi3M_-pnLQXkEOHb-Ap0F6aZ1h9Zj2Zlgj49IxB3IPLyYEnwOLh6Tu5-eBUmie2bRWz0G5rXWPRAjsHf56T-QBd1fzDsLNkFBICFjOWpkPxU1e0lQ0iL3kQqae4ll7ckHdh_OvQnbBavdpW3DLIZ312foQl7RK22T0e21n87AaMFLvEWuxT3ARi7BCXD6Dx2qEe7U_M3BS0w01hlvla2dM_6sOlum8Mr2jJ9Z_UxplyjBaeWmuZGC4--2UTTCKCF3AiNjgisthq0EuDsJ8TfMv-V_hCobo1k-LAY4m90zsBzyyjl8Q-t4R_QpOKUavmy3j_9UAObPhJN2Tsrkk2a_s8PwJhOD65dYn4nD_uqo2tvj0DWAK70yi4MKO-oYrcDlBsYuSBs6KhAUVwWWOKVVfp3rWryJc4Hj7fmFy1JHmImMQ2_p0zNRCFbQdi1j6mbE_m-WPhZb5arte8Dlmpk0By0MiIUsdOIL7ISkYuAF_iHvp8dHEK6hJPfh-ubHwIQomqhAQwmBK-qkGLPNi6ixyAFYkvW3Wazq2eO4Ux2o1VTXole9_3Kkbfwiz4lvinuPWoEu3UceRqkbtu61vIxhmPWMJLBMCuHKrQDLR4yrextxHyPsUbq8YREnoUAo9dr_vgauDbjPkgoyVxSSl44MptLo6Q3x4mcNtIKet42vSZcO-VXK9-WWp1WybQpaS0CIk2uLx9rErlZSVhPhRNZKlvjwNZMA_u-VS5KA-Dil5qdkv83fGrhI5u9BZBrE_4JVn3oSzW_rCzy2UGWa5r9dSdSEjuay73PaHCdhG6LkDTF1LA8zQE9I_8BreAcHRwv4B-9SbFc8mYEw_83hITIKEGEEaZjv3RqSzFIqdRpvoe0H0rHBHlstInsODwkIb__fBNmZgyeUNirakpll1dXpZxiZ-OOxHy7X8JvGJPsZBvUfhSrBXdHpILl3R3oHMV3YWl8bH1Wfjx9G1iswiDvkWm8FjszwErJaW8DA.Jwat2RBGfnSzI0WvgFCR4Q',
        ];

        $this->mockResponse($response);

        $user          = $this->user();
        $transactionId = '492be839-7834-41f8-9b34-c093031cf3c2';

        $response = $user->inquire($transactionId);
        $this->assertTrue(is_array($response));
        $this->assertEquals(
            'SUCCESSFUL',
            $response['status']
        );

        $this->assertEquals(
            'Tue Dec 01 2020 03:09:03 GMT+0800 (DST)',
            $response['confirmation_timestamp']
        );

        $this->assertEquals(
            '16068065429288546789',
            $response['confirmation_number']
        );
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
            'token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.L-Sp2hKgOm1DarK-oxrjK6GJMeKeRZpGaIIFrzvbF2rMOrrozeHK8dqSIbMYPappzbMespTsCfzG9NwLKlLsogaGKJE77OW7txMMEtwo2SzPqy5UXlmcKwJunghImk7eCkueB6mJDF2MKSgkOPXisAjChvZcNt2hCnGjq_Dn9BBRypwM2CkCwt9aOWEL2ycO09Wk3sagh-ZV-Uz6juSfVaxgY4pm9Et0nGakq0pBqFiMsBTxvSRDn-HTHGD9lmC3rkSK9-I0y1YqAroj8oEqIYss_20U35GwI5dOPgbwj2F551L1zDoQvm8zr9pAnHnsO9l-4HPMb3SDd1yynpCc3A.RMkkh-43bHl5o_SaohFxXA.9cHxW3p9wQoEAN3PxQyrP8ZdygEVFAdPdqpNQWFOgEvJtfBF7t-uYlGlJT7Mvv-5JkK-QmhwnSer9BRdpFlOMIjrRUNgp81xwB7uhRxem8BXBhTSMH2WKV5RiMFAXfmjaY7eT11qgXsIwwMRBls_SnC_ZPPZtLpm0jR40HOH7-Veahe7VTi3M_-pnLQXkEOHb-Ap0F6aZ1h9Zj2Zlgj49IxB3IPLyYEnwOLh6Tu5-eBUmie2bRWz0G5rXWPRAjsHf56T-QBd1fzDsLNkFBICFjOWpkPxU1e0lQ0iL3kQqae4ll7ckHdh_OvQnbBavdpW3DLIZ312foQl7RK22T0e21n87AaMFLvEWuxT3ARi7BCXD6Dx2qEe7U_M3BS0w01hlvla2dM_6sOlum8Mr2jJ9Z_UxplyjBaeWmuZGC4--2UTTCKCF3AiNjgisthq0EuDsJ8TfMv-V_hCobo1k-LAY4m90zsBzyyjl8Q-t4R_QpOKUavmy3j_9UAObPhJN2Tsrkk2a_s8PwJhOD65dYn4nD_uqo2tvj0DWAK70yi4MKO-oYrcDlBsYuSBs6KhAUVwWWOKVVfp3rWryJc4Hj7fmFy1JHmImMQ2_p0zNRCFbQdi1j6mbE_m-WPhZb5arte8Dlmpk0By0MiIUsdOIL7ISkYuAF_iHvp8dHEK6hJPfh-ubHwIQomqhAQwmBK-qkGLPNi6ixyAFYkvW3Wazq2eO4Ux2o1VTXole9_3Kkbfwiz4lvinuPWoEu3UceRqkbtu61vIxhmPWMJLBMCuHKrQDLR4yrextxHyPsUbq8YREnoUAo9dr_vgauDbjPkgoyVxSSl44MptLo6Q3x4mcNtIKet42vSZcO-VXK9-WWp1WybQpaS0CIk2uLx9rErlZSVhPhRNZKlvjwNZMA_u-VS5KA-Dil5qdkv83fGrhI5u9BZBrE_4JVn3oSzW_rCzy2UGWa5r9dSdSEjuay73PaHCdhG6LkDTF1LA8zQE9I_8BreAcHRwv4B-9SbFc8mYEw_83hITIKEGEEaZjv3RqSzFIqdRpvoe0H0rHBHlstInsODwkIb__fBNmZgyeUNirakpll1dXpZxiZ-OOxHy7X8JvGJPsZBvUfhSrBXdHpILl3R3oHMV3YWl8bH1Wfjx9G1iswiDvkWm8FjszwErJaW8DA.Jwat2RBGfnSzI0WvgFCR4Q',
        ];

        $this->mockResponse($response);

        $user          = $this->user();
        $transactionId = '492be839-7834-41f8-9b34-c093031cf3c2';

        $user->inquire($transactionId);

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
            'token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.L-Sp2hKgOm1DarK-oxrjK6GJMeKeRZpGaIIFrzvbF2rMOrrozeHK8dqSIbMYPappzbMespTsCfzG9NwLKlLsogaGKJE77OW7txMMEtwo2SzPqy5UXlmcKwJunghImk7eCkueB6mJDF2MKSgkOPXisAjChvZcNt2hCnGjq_Dn9BBRypwM2CkCwt9aOWEL2ycO09Wk3sagh-ZV-Uz6juSfVaxgY4pm9Et0nGakq0pBqFiMsBTxvSRDn-HTHGD9lmC3rkSK9-I0y1YqAroj8oEqIYss_20U35GwI5dOPgbwj2F551L1zDoQvm8zr9pAnHnsO9l-4HPMb3SDd1yynpCc3A.RMkkh-43bHl5o_SaohFxXA.9cHxW3p9wQoEAN3PxQyrP8ZdygEVFAdPdqpNQWFOgEvJtfBF7t-uYlGlJT7Mvv-5JkK-QmhwnSer9BRdpFlOMIjrRUNgp81xwB7uhRxem8BXBhTSMH2WKV5RiMFAXfmjaY7eT11qgXsIwwMRBls_SnC_ZPPZtLpm0jR40HOH7-Veahe7VTi3M_-pnLQXkEOHb-Ap0F6aZ1h9Zj2Zlgj49IxB3IPLyYEnwOLh6Tu5-eBUmie2bRWz0G5rXWPRAjsHf56T-QBd1fzDsLNkFBICFjOWpkPxU1e0lQ0iL3kQqae4ll7ckHdh_OvQnbBavdpW3DLIZ312foQl7RK22T0e21n87AaMFLvEWuxT3ARi7BCXD6Dx2qEe7U_M3BS0w01hlvla2dM_6sOlum8Mr2jJ9Z_UxplyjBaeWmuZGC4--2UTTCKCF3AiNjgisthq0EuDsJ8TfMv-V_hCobo1k-LAY4m90zsBzyyjl8Q-t4R_QpOKUavmy3j_9UAObPhJN2Tsrkk2a_s8PwJhOD65dYn4nD_uqo2tvj0DWAK70yi4MKO-oYrcDlBsYuSBs6KhAUVwWWOKVVfp3rWryJc4Hj7fmFy1JHmImMQ2_p0zNRCFbQdi1j6mbE_m-WPhZb5arte8Dlmpk0By0MiIUsdOIL7ISkYuAF_iHvp8dHEK6hJPfh-ubHwIQomqhAQwmBK-qkGLPNi6ixyAFYkvW3Wazq2eO4Ux2o1VTXole9_3Kkbfwiz4lvinuPWoEu3UceRqkbtu61vIxhmPWMJLBMCuHKrQDLR4yrextxHyPsUbq8YREnoUAo9dr_vgauDbjPkgoyVxSSl44MptLo6Q3x4mcNtIKet42vSZcO-VXK9-WWp1WybQpaS0CIk2uLx9rErlZSVhPhRNZKlvjwNZMA_u-VS5KA-Dil5qdkv83fGrhI5u9BZBrE_4JVn3oSzW_rCzy2UGWa5r9dSdSEjuay73PaHCdhG6LkDTF1LA8zQE9I_8BreAcHRwv4B-9SbFc8mYEw_83hITIKEGEEaZjv3RqSzFIqdRpvoe0H0rHBHlstInsODwkIb__fBNmZgyeUNirakpll1dXpZxiZ-OOxHy7X8JvGJPsZBvUfhSrBXdHpILl3R3oHMV3YWl8bH1Wfjx9G1iswiDvkWm8FjszwErJaW8DA.Jwat2RBGfnSzI0WvgFCR4Q',
        ];

        $this->mockResponse($response);

        $user          = $this->user();
        $transactionId = '492be839-7834-41f8-9b34-c093031cf3c2';

        $user->inquire($transactionId);

        $request = $user->getLastRequestArray();

        $this->assertTrue(true == is_array($request));
        $this->assertArrayHasKey('uri', $request);
        $this->assertArrayHasKey('method', $request);
        $this->assertArrayHasKey('options', $request);
    }
}
