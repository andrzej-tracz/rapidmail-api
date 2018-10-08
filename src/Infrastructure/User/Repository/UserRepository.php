<?php

namespace App\Infrastructure\User\Repository;

use App\Domain\Account\Account;
use App\Domain\User\User;
use App\Infrastructure\Doctrine\Repository\DoctrineRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * UserRepository.
 */
class UserRepository extends DoctrineRepository implements UserLoaderInterface
{
    /**
     * @param string $username
     *
     * @return User
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->andWhere('u.isConfirmed = true')
            ->andWhere('u.isActive = true')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    /**
     * @param Account $account
     *
     * @return mixed
     */
    public function findUsersByAccount(Account $account)
    {
        $query = $this->createQueryBuilder('u')
            ->select('u')
            ->join('u.profiles', 'p')
            ->join('p.account', 'a')
            ->where('a = :account')
            ->setParameter('account', $account->getId())
            ->getQuery();

        return $query->getResult();
    }
}
