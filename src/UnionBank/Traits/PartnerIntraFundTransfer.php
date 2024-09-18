<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank\Traits;

trait PartnerIntraFundTransfer
{
    /**
     * Partner unionbank to unionbank fund transfer
     *
     * @param string $token
     * @param array $data
     *
     * @return array
     */
    public function partnerIntraTransfer(string $token, array $data): array
    {
        return $this->callApiGateway(
            'partners/v3/transfers/single',
            $data,
            $token,
            'POST'
        );
    }

    /**
     * Partner inquire unionbank to unionbank fund transfer transaction status
     *
     * @param string $token
     * @param string $reference
     *
     * @return array
     */
    public function partnerInquireIntraTransfer(string $token, string $reference): array
    {
        return $this->callApiGateway(
            "partners/v2/transfers/single/$reference",
            [],
            $token
        );
    }
}