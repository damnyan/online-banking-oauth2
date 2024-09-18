<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank\Instapay;

class Remittance
{
    protected string $amount;
    protected string $currency;
    protected string $receivingBank;
    protected int $purpose;
    protected ?string $instructions;

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        if (round($amount) === $amount) {
            $amount = number_format($amount, 2, '.', '');
        }
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
     * @return int
     */
    public function getPurpose(): int
    {
        return $this->purpose;
    }

    /**
     * @param int $purpose
     */
    public function setPurpose(int $purpose): void
    {
        $this->purpose = $purpose;
    }

    /**
     * @return ?string
     */
    public function getInstructions(): ?string
    {
        if (!isset($this->instructions)) {
            $this->setInstructions(null);
        }
        return $this->instructions;
    }

    /**
     * @param ?string $instructions
     */
    public function setInstructions(?string $instructions): void
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