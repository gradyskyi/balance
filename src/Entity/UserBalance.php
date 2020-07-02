<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\BalanceException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class UserBalance
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(
     *     type="string",
     *     unique=true,
     *     nullable=false
     * )
     */
    private string $userId;

    /**
     * @ORM\Column(
     *     type="integer",
     *     nullable=false
     * )
     */
    private int $balance = 0;

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function credit(int $amount): self
    {
        $this->balance += $amount;

        return $this;
    }

    public function debit(int $amount): self
    {
        if ($this->balance < $amount) {
            throw new BalanceException('insufficient funds');
        }

        $this->balance -= $amount;

        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }
}