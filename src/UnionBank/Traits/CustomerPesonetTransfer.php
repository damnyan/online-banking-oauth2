<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank\Traits;

trait CustomerPesonetTransfer
{
    /**
     * Customer pesonet fund transfer
     *
     * @param string $token
     * @param array $data
     *
     * @return array
     */
    public function customerPesonetTransfer(string $token, array $data): array
    {
        return $this->callApiGateway(
            'online/v2/pesonet/transfers/single',
            $data,
            $token,
            'POST'
        );
    }

    /**
     * Inquire pesonet fund transfer transactions
     *
     * @param string $token
     * @param string $reference
     * @return array
     */
    public function customerInquirePesonetTransfer(string $token, string $reference): array
    {
        return $this->callApiGateway(
            "online/v1/pesonet/transfers/single/$reference",
            [],
            $token
        );
    }

    /**
     * List pesonet receiving banks
     *
     * @param string $token
     * @return array
     */
    public function customerListPesonetReceivingBanks(string $token): array
    {
        return $this->callApiGateway(
            'online/v1/pesonet/banks',
            [],
            $token
        );
    }
}