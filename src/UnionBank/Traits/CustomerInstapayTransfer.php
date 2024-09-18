<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank\Traits;

trait CustomerInstapayTransfer
{
    /**
     * Customer instapay fund transfer
     *
     * @param string $token
     * @param array $data
     *
     * @return array
     */
    public function customerInstapayTransfer(string $token, array $data): array
    {
        $response = $this->callApiGateway(
            'online/v2/instapay/transfers/single',
            $data,
            $token,
            'POST'
        );

        return $response;
    }

    /**
     * Customer inquire instapay fund transfer status
     *
     * @param string $token
     * @param string $reference
     *
     * @return array
     */
    public function customerInquireInstapayTransfer(string $token, string $reference): array
    {
        return $this->callApiGateway(
            "online/v1/instapay/transfers/single/$reference",
            [],
            $token
        );
    }

    /**
     * List instapay receiving banks
     *
     * @param string $token
     *
     * @return array
     */
    public function customerListInstapayReceivingBanks(string $token): array
    {
        return $this->callApiGateway(
            'online/v1/instapay/banks',
            [],
            $token
        );
    }
}