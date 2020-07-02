<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(
     *     type="integer",
     *     nullable=false
     * )
     */
    private int $amount;

    /**
     * @ORM\Column(
     *     type="string",
     *     unique=true,
     *     nullable=false
     * )
     */
    private string $transactionId;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\UserBalance",
     * )
     */
    private UserBalance $userBalance;

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getUserBalance(): UserBalance
    {
        return $this->userBalance;
    }

    public function setUserBalance(UserBalance $userBalance): self
    {
        $this->userBalance = $userBalance;

        return $this;
    }
}