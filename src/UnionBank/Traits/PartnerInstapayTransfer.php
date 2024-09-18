<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank\Traits;

trait PartnerInstapayTransfer
{
    /**
     * Partner instapay fund transfer
     *
     * @param string $token
     * @param array $data
     *
     * @return array
     */
    public function partnerInstapayTransfer(string $token, array $data): array
    {
        $response = $this->callApiGateway(
            'partners/v3/instapay/transfers/single',
            $data,
            $token,
            'POST'
        );

        return $response;
    }

    /**
     * Partner inquire instapay fund transfer status
     *
     * @param string $token
     * @param string $reference
     *
     * @return array
     */
    public function partnerInquireInstapayTransfer(string $token, string $reference): array
    {
        return $this->callApiGateway(
            "partners/v3/instapay/transfers/single/$reference",
            [],
            $token
        );
    }

    /**
     * Partner instapay receiving banks
     *
     * @param string $token
     * @return array
     */
    public function partnerListInstapayReceivingBanks(string $token): array
    {
        return $this->callApiGateway(
            'partners/v3/instapay/banks',
            [],
            $token
        );
    }

    /**
     * Partner list instapay libraries
     *
     * @param string $token
     * @param string $type
     * @return array
     */
    public function partnerListInstapayLibraries(string $token, string $type = 'purpose'): array
    {
        return $this->callApiGateway(
            "partners/v3/instapay/libraries?type=$type",
            [],
            $token
        );
    }
}