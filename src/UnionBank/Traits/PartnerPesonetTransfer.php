<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank\Traits;

trait PartnerPesonetTransfer
{
    /**
     * Partner pesonet fund transfer
     *
     * @param string $token
     * @param array $data
     *
     * @return array
     */
    public function partnerPesonetTransfer(string $token, array $data): array
    {
        return $this->callApiGateway(
            'partners/v3/pesonet/transfers/single',
            $data,
            $token,
            'POST'
        );
    }

    /**
     * Partner inquire pesonet fund transfer transactions
     *
     * @param string $token
     * @param string $reference
     * @return array
     */
    public function partnerInquirePesonetTransfer(string $token, string $reference): array
    {
        return $this->callApiGateway(
            "partners/v3/pesonet/transfers/single/$reference",
            [],
            $token
        );
    }

    /**
     * Partner list pesonet receiving banks
     *
     * @param string $token
     * @return array
     */
    public function partnerListPesonetReceivingBanks(string $token): array
    {
        return $this->callApiGateway(
            'partners/v3/pesonet/banks',
            [],
            $token
        );
    }
}