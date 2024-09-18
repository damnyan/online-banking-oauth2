<?php

namespace Dmn\OnlineBankingOAuth2\Tests\Bpi;

use Dmn\OnlineBankingOAuth2\Bpi\BpiProvider;
use Dmn\OnlineBankingOAuth2\Bpi\User;
use Dmn\OnlineBankingOAuth2\Tests\Bpi\TestCase;
use Illuminate\Support\Collection;

class UserTest extends TestCase
{
    /**
     * @test
     * @testdox It can get user from code
     *
     * @return void
     */
    public function userCode(): void
    {
        $this->fakeSession();
        $this->mockResponses([
            [
                [
                    'token_type' => 'bearer',
                    'access_token' => 'AAIkNGJmM2ZjMjktM2EyZi00OTYzLWFlMDItNTY3OTJhZDA0NmFkxJT4OdPB49rrIg3fIj-qIkson89bro0avvgnE7g-trJU_DjWbPPz8z5iKsUltUo2RCfVt8q2EoYKo3gXFMqvyIrRu3e0sxhhIJogdvoawMuCZ2tPgu3dT6tssRvBcmFkcFDjUNWvCunM5cfODiP_FuvI8DC_XaeDXRwjtazx3n4',
                    'metadata' => 'a:dmn,one',
                    'expires_in' => 1800,
                    'consented_on' => 1606447997,
                    'scope' => 'transactionalAccountsForBillsPay fundTopUp',
                    'refresh_token' => 'AAIM9iE6r1vLNso802MS0REpoSYx4tn_zhrCVXg4eGc_XVtzM6JIJQhX90Ur2PdpSiZftTcIzE2ooUvRh9SkVFVNGxEGFxm2gHX06aJb7vvLIxSEQQRHULOX2qVSQEM9_e64tAIbKxWDqQubwdBiwhiViAonl5Sv8-KzfFYMTdZMX371HMJHHqHfT6lyBUq9Zwk',
                    'refresh_token_expires_in' => 2592000,
                ],
            ],
            [
                ['token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.gxG1viXLmGHgYl_CLkxC18kEN5bfHey__ySJa6dU6bfrKRuMkdb8HbiNgQU59WKmc7eogzEuZqjDpKuP8saI7uN4jQ2aq1vjqRwv6RNe82M3NvuJdZkjaQnJXVDgvMYicvdHp0ao8SbOTOlj4q2JL4XsjsfeYsnT4GRYAm037z0gnVM-Zcfi7WvwjMZlIrwF_MBsZDG0-x6CHYZ84cGG4MRXnp4hW21T9XHLqNfdsKW8Y-vBWIkOFlFKqXNp7U-DPrOQGvMqo2dC8epYXSsykbzfLN9CpMPwqKQ24Lmwy8VlcPkYdwtfnZ6tFiKGCy0MorwuX8jypMBbR1tCi-1ayA.6aPquE0SjSXzRsDibJDnUA.B8wUwPlOfhvktVEaJFsrfZGJSsa1IvTf4kb2aRmQjXuwXZgyKBu5UhfSXBH9_dyhS4YDwj_fVlhWgqmuVqCe2aE4DBth1FhJ2MlIASRmcGH3uvlSY50mHbgJ5UdYo5wTdFAbEAKlo2tO55F7Tot3k-VeT6GlOsqglGXBBh54RBKGg8rSG-Wds-yZzX3qXUTjYItfTWghnb6iIVWpAvUz4oiDkPc8Rk8rVak2sjPxbjFZXqPe-WXFGDkqtnkODmZipIP5yZNsj1LovWBuuXTj5seShhzIPukKqzboHjenVLxTPdf-BCFi3TcHXwRI4QNPBy7NHTrLySwU-f89ta8kt0lqYMOoYNnjPCi6gSZok35bDpQeR7NiCfV7dSCHAJDrUGOZYAIg2mXk9hfjwlCaOnr41wnOL7khUcgZb5hsnyoYiS9zRLZfQsech9BbgLI5h6MdHx4fBcp9xaPJKn8Plxn9cph1h42sHXprApT6eg03HRPDUNsWk5mCcOP93WeMOvdkVJ8mu-9ijTQeKP_oe3WSaD7YKkTH0QVzvBSwT8VVQHuGyrYetiTXxBiHC5GYSJGjDlPXp0i2wZph4KujSBqHJGTGPSJamhYoQraEsXXv3mcHV6nxnKj_rafxuCvkJLRXxlLz6fcrJE1JDNx1wl7NmPqAp8hk5l7RNB3v1Vus25mNa22XDnt1QthdzlHcaIT5PZ8bd8aG00LMIaU9GsDdDuJbir6Qpn5cJBZK_dZ_2Zh__r69tMsxeCm98Bu4wKE4LDCwqbkdSM_O0-9oeOHY__mtfXLyhFdKgX5vOHXFmBziOR6bvFWo_CC4wVOxEQxVfRdw6KuRvP_m8Mikgc4tCgu-Nf54WSyxTw_pL3ieruflikyeLfxEzX2orryyb-1LWWLU-x-FkzqQ2ZqSvnUUrP2cRLWP4Ax4Ih_YVDOfGhOnd0DaTQ9MrmYR02IWZleM5bz3c4qgdoKJMtmYgDGbeNE2ydUNgfvsA_HjnIQG8_I6prU9vxphn8zEqQXPMR7_Vz9WkSDhYhZpHEkjJkvkcLViB0CzUjPDJppamFhMWZ9vchw76JP8-7YT--PklNOY-P1PfhjrmILPW9sgY-Bqu4Jp3jnGtMG0xcGuwBwODUZn8enRlFFRCJ6GVoHjYoMr9ymmg3SzY_bSsbNh0gBDzo9Q-FWdAspDOrZHqi96JoNtUb4ZaF0Y-kY7A2T7ywZLmGp-2mn4UmQvS09DzND6zJMxvhVNEIgp63vdvIrRTokbnTG2DOPvcFm9-8ytF-zxbDhAG9y1BfckFq4mTdyxllCj0pV3cPnMHiDMVpMly0r3JNOnxzaad-89EBXi.UKhmRL1fgS8fDjwMLFpU7g'],
            ]
        ]);

        $code = 'AAInHeduF04IBzNPHddtrr9lAphzXZoFbHTA_rqrPx8g9m42vLp3HWfiHGirYU9VPVnwgu9S1UsoO-1zdLkKPlKXAziSs3db7Jl0KxMHXccFxHpnM7wfdYdqLtgomjcAr566VCt4Yipezk2qyb3KqmCCYdzZtIAiv98Lc39xc1K6Hme2rd8SLz_r_uKm9nx36zhwWQeulY-pYV0wjtU-yvKV';
        $this->app->request->merge(['code' => $code]);

        $provider = $this->service()
            ->stateless();
        $user = $provider->user();
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Collection::class, $user->accounts());
        $this->assertInstanceOf(BpiProvider::class, $user->driver());
    }

    /**
     * @test
     * @testdox It can get user from token
     *
     * @return void
     */
    public function userFromToken(): void
    {
        $this->fakeSession();
        $this->mockResponses([
            [
                ['token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.gxG1viXLmGHgYl_CLkxC18kEN5bfHey__ySJa6dU6bfrKRuMkdb8HbiNgQU59WKmc7eogzEuZqjDpKuP8saI7uN4jQ2aq1vjqRwv6RNe82M3NvuJdZkjaQnJXVDgvMYicvdHp0ao8SbOTOlj4q2JL4XsjsfeYsnT4GRYAm037z0gnVM-Zcfi7WvwjMZlIrwF_MBsZDG0-x6CHYZ84cGG4MRXnp4hW21T9XHLqNfdsKW8Y-vBWIkOFlFKqXNp7U-DPrOQGvMqo2dC8epYXSsykbzfLN9CpMPwqKQ24Lmwy8VlcPkYdwtfnZ6tFiKGCy0MorwuX8jypMBbR1tCi-1ayA.6aPquE0SjSXzRsDibJDnUA.B8wUwPlOfhvktVEaJFsrfZGJSsa1IvTf4kb2aRmQjXuwXZgyKBu5UhfSXBH9_dyhS4YDwj_fVlhWgqmuVqCe2aE4DBth1FhJ2MlIASRmcGH3uvlSY50mHbgJ5UdYo5wTdFAbEAKlo2tO55F7Tot3k-VeT6GlOsqglGXBBh54RBKGg8rSG-Wds-yZzX3qXUTjYItfTWghnb6iIVWpAvUz4oiDkPc8Rk8rVak2sjPxbjFZXqPe-WXFGDkqtnkODmZipIP5yZNsj1LovWBuuXTj5seShhzIPukKqzboHjenVLxTPdf-BCFi3TcHXwRI4QNPBy7NHTrLySwU-f89ta8kt0lqYMOoYNnjPCi6gSZok35bDpQeR7NiCfV7dSCHAJDrUGOZYAIg2mXk9hfjwlCaOnr41wnOL7khUcgZb5hsnyoYiS9zRLZfQsech9BbgLI5h6MdHx4fBcp9xaPJKn8Plxn9cph1h42sHXprApT6eg03HRPDUNsWk5mCcOP93WeMOvdkVJ8mu-9ijTQeKP_oe3WSaD7YKkTH0QVzvBSwT8VVQHuGyrYetiTXxBiHC5GYSJGjDlPXp0i2wZph4KujSBqHJGTGPSJamhYoQraEsXXv3mcHV6nxnKj_rafxuCvkJLRXxlLz6fcrJE1JDNx1wl7NmPqAp8hk5l7RNB3v1Vus25mNa22XDnt1QthdzlHcaIT5PZ8bd8aG00LMIaU9GsDdDuJbir6Qpn5cJBZK_dZ_2Zh__r69tMsxeCm98Bu4wKE4LDCwqbkdSM_O0-9oeOHY__mtfXLyhFdKgX5vOHXFmBziOR6bvFWo_CC4wVOxEQxVfRdw6KuRvP_m8Mikgc4tCgu-Nf54WSyxTw_pL3ieruflikyeLfxEzX2orryyb-1LWWLU-x-FkzqQ2ZqSvnUUrP2cRLWP4Ax4Ih_YVDOfGhOnd0DaTQ9MrmYR02IWZleM5bz3c4qgdoKJMtmYgDGbeNE2ydUNgfvsA_HjnIQG8_I6prU9vxphn8zEqQXPMR7_Vz9WkSDhYhZpHEkjJkvkcLViB0CzUjPDJppamFhMWZ9vchw76JP8-7YT--PklNOY-P1PfhjrmILPW9sgY-Bqu4Jp3jnGtMG0xcGuwBwODUZn8enRlFFRCJ6GVoHjYoMr9ymmg3SzY_bSsbNh0gBDzo9Q-FWdAspDOrZHqi96JoNtUb4ZaF0Y-kY7A2T7ywZLmGp-2mn4UmQvS09DzND6zJMxvhVNEIgp63vdvIrRTokbnTG2DOPvcFm9-8ytF-zxbDhAG9y1BfckFq4mTdyxllCj0pV3cPnMHiDMVpMly0r3JNOnxzaad-89EBXi.UKhmRL1fgS8fDjwMLFpU7g'],
            ]
        ]);

        $token = 'AAIkNGJmM2ZjMjktM2EyZi00OTYzLWFlMDItNTY3OTJhZDA0NmFkpasQ_c_nFfQWAYTLVuXLpWwzp7ISGe6s5eBi1zjWEx87p3FIL0lSAJHdU2UjqhNlEwRxouRM-VcFx6qidGIqsH1dKt8Y1_4iLK55DEQVWo3oykNN9zAKSyJgaR55217AE9LFbvtuuUHEDwmJ1ANsvtXoamstO3bjH9PensYryd4';

        $provider = $this->service()
            ->stateless();
        $user = $provider->userFromToken($token);
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Collection::class, $user->accounts());
        $this->assertInstanceOf(BpiProvider::class, $user->driver());
    }

    /**
     * @test
     * @testdox It can get user from refresh token
     *
     * @return void
     */
    public function userFromRefreshToken(): void
    {
        $this->fakeSession();
        $this->mockResponses([
            [
                [
                    'token_type' => 'bearer',
                    'access_token' => 'AAIkNGJmM2ZjMjktM2EyZi00OTYzLWFlMDItNTY3OTJhZDA0NmFkxJT4OdPB49rrIg3fIj-qIkson89bro0avvgnE7g-trJU_DjWbPPz8z5iKsUltUo2RCfVt8q2EoYKo3gXFMqvyIrRu3e0sxhhIJogdvoawMuCZ2tPgu3dT6tssRvBcmFkcFDjUNWvCunM5cfODiP_FuvI8DC_XaeDXRwjtazx3n4',
                    'metadata' => 'a:dmn,one',
                    'expires_in' => 1800,
                    'consented_on' => 1606447997,
                    'scope' => 'transactionalAccountsForBillsPay fundTopUp',
                    'refresh_token' => 'AAIM9iE6r1vLNso802MS0REpoSYx4tn_zhrCVXg4eGc_XVtzM6JIJQhX90Ur2PdpSiZftTcIzE2ooUvRh9SkVFVNGxEGFxm2gHX06aJb7vvLIxSEQQRHULOX2qVSQEM9_e64tAIbKxWDqQubwdBiwhiViAonl5Sv8-KzfFYMTdZMX371HMJHHqHfT6lyBUq9Zwk',
                    'refresh_token_expires_in' => 2592000,
                ],
            ],
            [
                ['token' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiUlNBLU9BRVAiLCJjdHkiOiJKV1QifQ.gxG1viXLmGHgYl_CLkxC18kEN5bfHey__ySJa6dU6bfrKRuMkdb8HbiNgQU59WKmc7eogzEuZqjDpKuP8saI7uN4jQ2aq1vjqRwv6RNe82M3NvuJdZkjaQnJXVDgvMYicvdHp0ao8SbOTOlj4q2JL4XsjsfeYsnT4GRYAm037z0gnVM-Zcfi7WvwjMZlIrwF_MBsZDG0-x6CHYZ84cGG4MRXnp4hW21T9XHLqNfdsKW8Y-vBWIkOFlFKqXNp7U-DPrOQGvMqo2dC8epYXSsykbzfLN9CpMPwqKQ24Lmwy8VlcPkYdwtfnZ6tFiKGCy0MorwuX8jypMBbR1tCi-1ayA.6aPquE0SjSXzRsDibJDnUA.B8wUwPlOfhvktVEaJFsrfZGJSsa1IvTf4kb2aRmQjXuwXZgyKBu5UhfSXBH9_dyhS4YDwj_fVlhWgqmuVqCe2aE4DBth1FhJ2MlIASRmcGH3uvlSY50mHbgJ5UdYo5wTdFAbEAKlo2tO55F7Tot3k-VeT6GlOsqglGXBBh54RBKGg8rSG-Wds-yZzX3qXUTjYItfTWghnb6iIVWpAvUz4oiDkPc8Rk8rVak2sjPxbjFZXqPe-WXFGDkqtnkODmZipIP5yZNsj1LovWBuuXTj5seShhzIPukKqzboHjenVLxTPdf-BCFi3TcHXwRI4QNPBy7NHTrLySwU-f89ta8kt0lqYMOoYNnjPCi6gSZok35bDpQeR7NiCfV7dSCHAJDrUGOZYAIg2mXk9hfjwlCaOnr41wnOL7khUcgZb5hsnyoYiS9zRLZfQsech9BbgLI5h6MdHx4fBcp9xaPJKn8Plxn9cph1h42sHXprApT6eg03HRPDUNsWk5mCcOP93WeMOvdkVJ8mu-9ijTQeKP_oe3WSaD7YKkTH0QVzvBSwT8VVQHuGyrYetiTXxBiHC5GYSJGjDlPXp0i2wZph4KujSBqHJGTGPSJamhYoQraEsXXv3mcHV6nxnKj_rafxuCvkJLRXxlLz6fcrJE1JDNx1wl7NmPqAp8hk5l7RNB3v1Vus25mNa22XDnt1QthdzlHcaIT5PZ8bd8aG00LMIaU9GsDdDuJbir6Qpn5cJBZK_dZ_2Zh__r69tMsxeCm98Bu4wKE4LDCwqbkdSM_O0-9oeOHY__mtfXLyhFdKgX5vOHXFmBziOR6bvFWo_CC4wVOxEQxVfRdw6KuRvP_m8Mikgc4tCgu-Nf54WSyxTw_pL3ieruflikyeLfxEzX2orryyb-1LWWLU-x-FkzqQ2ZqSvnUUrP2cRLWP4Ax4Ih_YVDOfGhOnd0DaTQ9MrmYR02IWZleM5bz3c4qgdoKJMtmYgDGbeNE2ydUNgfvsA_HjnIQG8_I6prU9vxphn8zEqQXPMR7_Vz9WkSDhYhZpHEkjJkvkcLViB0CzUjPDJppamFhMWZ9vchw76JP8-7YT--PklNOY-P1PfhjrmILPW9sgY-Bqu4Jp3jnGtMG0xcGuwBwODUZn8enRlFFRCJ6GVoHjYoMr9ymmg3SzY_bSsbNh0gBDzo9Q-FWdAspDOrZHqi96JoNtUb4ZaF0Y-kY7A2T7ywZLmGp-2mn4UmQvS09DzND6zJMxvhVNEIgp63vdvIrRTokbnTG2DOPvcFm9-8ytF-zxbDhAG9y1BfckFq4mTdyxllCj0pV3cPnMHiDMVpMly0r3JNOnxzaad-89EBXi.UKhmRL1fgS8fDjwMLFpU7g'],
            ]
        ]);

        $refreshToken = 'AAIkbZKSDcR_W6zXSLFR2DWGGcDaYc9rO1SaS4_KCO-ZdwMzt2GN9cQvXxLaw_3r4GQSHgyyC-TATXNhjwkSFqejvxDExPvDWBQ2iOEkMtapn0HsegNf0xlJaBAsNw8VmdM5ev0JhL2CBOy486OFYJXBR1hYmg3cDzJOyS1uwYq63nwvT0AAXGNbDh1_zF27igQ';

        $provider = $this->service()
            ->stateless();
        $user = $provider->userFromRefreshToken($refreshToken);
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Collection::class, $user->accounts());
        $this->assertInstanceOf(BpiProvider::class, $user->driver());
    }
}
