<?php

namespace App\Infrastructure\Template\Repository;

use App\Domain\Account\Account;
use App\Infrastructure\Doctrine\Repository\DoctrineRepository;

class PurchasedTemplateRepository extends DoctrineRepository
{
    /**
     * @param Account $account
     *
     * @return mixed
     */
    public function findPurchasedByAccount(Account $account)
    {
        return $this->createQueryBuilder('p')
            ->where('p.account = :account')
            ->setParameter('account', $account)
            ->join('p.template', 't')
            ->join('p.purchasedBy', 'pb')
            ->getQuery()
            ->getResult();
    }
}
