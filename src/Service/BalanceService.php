<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\UserBalance;
use App\Exception\DuplicateTransactionException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class BalanceService
{
    protected EntityManagerInterface $em;

    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @throws \Exception
     */
    public function debit(array $request): void
    {
        $this->validateBalanceRequest($request);

        $this->em->getConnection()->beginTransaction();

        try {
            /* @var UserBalance $userBalance */
            $userBalance = $this->em->getRepository(UserBalance::class)->findOneBy(['userId' => $request['uid']]);

            if (!$userBalance) {
                $this->logger->error(\sprintf(
                    'Unexisted user id: %s',
                    $request['uid']
                ));
                throw new \Exception();
            }

            $transaction = (new Transaction())
                ->setTransactionId((string) $request['tid'])
                ->setAmount((int) $request['amount'])
                ->setUserBalance($userBalance);

            $userBalance->debit((int) $request['amount']);

            $this->em->persist($transaction);
            $this->em->flush();

            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function credit(array $request): void
    {
        $this->validateBalanceRequest($request);

        /* @var UserBalance $userBalance */
        $userBalance = $this->em->getRepository(UserBalance::class)->findOneBy(['userId' => $request['uid']]);

        if (!$userBalance) {
            $userBalance = (new UserBalance())
                ->setUserId($request['uid']);

            $this->em->persist($userBalance);
            $this->em->flush();
        }

        $this->em->getConnection()->beginTransaction();

        try {
            $transaction = (new Transaction())
                ->setTransactionId((string) $request['tid'])
                ->setAmount((int) $request['amount'])
                ->setUserBalance($userBalance);

            $userBalance->credit((int) $request['amount']);

            $this->em->persist($transaction);
            $this->em->flush();

            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }

    }

    /**
     * @throws \Exception
     */
    private function validateBalanceRequest(array $data): void
    {
        $constraint = new Assert\Collection([
            'allowExtraFields' => false,
            'fields' => [
                'amount' => [
                    new Assert\NotBlank(),
                    new Assert\Positive(),
                    new Assert\Type(['type' => 'numeric'])
                ],
                'tid' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 12, 'max' => 32]),
                ],
                'uid' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 1, 'max' => 32]),
                ],
            ],
        ]);

        $violations = Validation::createValidator()->validate($data, $constraint);

        if (0 !== \count($violations)) {
            foreach ($violations as $violation) {
                /* @var ConstraintViolation $violation */
                $this->logger->error(\sprintf(
                    'Data violation: property %s -> %s, invalid value: %s',
                    $violation->getPropertyPath(),
                    $violation->getMessage(),
                    $violation->getInvalidValue()
                ));
            }

            throw new \Exception();
        }

        if ($this->em->getRepository(Transaction::class)->findOneBy(['transactionId' => $data['tid']])) {
            $this->logger->notice(\sprintf(
                'Duplicate transaction %s',
                $data['tid']
            ));

            throw new DuplicateTransactionException();
        }
    }
}
