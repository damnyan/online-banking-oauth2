<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank\Traits;

trait CustomerIntraFundTransfer
{
    /**
     * Customer unionbank to unionbank fund transfer
     *
     * @param string $token
     * @param array $data
     *
     * @return array
     */
    public function customerIntraTransfer(string $token, array $data): array
    {
        return $this->callApiGateway(
            'online/v2/transfers/single',
            $data,
            $token,
            'POST'
        );
    }

    /**
     * Inquire unionbank to unionbank fund transfer transaction status
     *
     * @param string $token
     * @param string $reference
     * @return array
     */
    public function customerInquireIntraTransfer(string $token, string $reference): array
    {
        return $this->callApiGateway(
            "online/v1/transfers/single/$reference",
            [],
            $token
        );
    }
}