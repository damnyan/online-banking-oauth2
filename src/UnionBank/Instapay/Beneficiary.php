<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank\Instapay;

class Beneficiary
{
    protected string $name;
    protected string $accountNumber;
    protected Address $address;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     */
    public function setAccountNumber(string $accountNumber): void
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    /**
     * @return array
     */
    public function toArray():array
    {
        return [
            'name' => $this->getName(),
            'accountNumber' => $this->getAccountNumber(),
            'address' => $this->getAddress()->toArray(),
        ];
    }
}