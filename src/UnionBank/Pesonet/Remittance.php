<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank\Pesonet;

class Remittance
{
    protected float $amount;
    protected string $currency;
    protected string $receivingBank;
    protected string $purpose;
    protected string $instructions;

    /**
     * Remittance constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        if (!isset($this->currency)) {
            $this->setCurrency('PHP');
        }
        return $this->currency;
    }

    /**
     * @return int
     */
    public function getReceivingBank(): int
    {
        return $this->receivingBank;
    }

    /**
     * @param int $receivingBank
     */
    public function setReceivingBank(int $receivingBank): void
    {
        $this->receivingBank = $receivingBank;
    }

    /**
     * @return string
     */
    public function getPurpose(): string
    {
        return $this->purpose;
    }

    /**
     * @param string $purpose
     */
    public function setPurpose(string $purpose): void
    {
        $this->purpose = $purpose;
    }

    /**
     * @return string
     */
    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    /**
     * @param string $instructions
     */
    public function setInstructions(string $instructions): void
    {
        $this->instructions = $instructions;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->getAmount(),
            'instructions' => $this->getInstructions(),
            'currency' => $this->getCurrency(),
            'receivingBank' => $this->getReceivingBank(),
            'purpose' => $this->getPurpose(),
        ];
    }
}