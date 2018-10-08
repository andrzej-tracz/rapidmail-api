<?php

namespace App\Infrastructure\Campaign\Repository;

use App\Domain\Account\Account;
use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Message;
use App\Domain\Subscriber\Subscriber;
use App\Infrastructure\Doctrine\Repository\DoctrineRepository;
use App\Infrastructure\Utils\Str;
use Cake\Chronos\Chronos;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * MessageRepository.
 */
class MessageRepository extends DoctrineRepository
{
    /**
     * Fetches chunk of queued messages.
     *
     * @param Campaign $campaign
     *
     * @return mixed
     */
    public function getPending(Campaign $campaign, $maxResult = 100, $offset = 0)
    {
        $query = $this
            ->createQueryBuilder('m')
            ->join('m.campaign', 'c')
            ->select('m')
            ->where('m.campaign = :campaign')
            ->setParameter('campaign', $campaign)
            ->andWhere('m.status = :status')
            ->setParameter('status', Message::STATUS_PENDING)
            ->orderBy('m.id')
            ->setMaxResults($maxResult)
            ->setFirstResult($offset)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Removes all queued messages by given campaign.
     *
     * @param Campaign $campaign
     *
     * @return bool
     */
    public function removePendingOrQueued(Campaign $campaign)
    {
        $query = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->delete(Message::class, 'm')
            ->where('m.campaign = :campaign')
            ->setParameter('campaign', $campaign)
            ->andWhere('m.status in (:queued, :pending)')
            ->setParameter('pending', Message::STATUS_PENDING)
            ->setParameter('queued', Message::STATUS_QUEUED)
            ->getQuery();

        return $query->execute();
    }

    /**
     * @param Campaign $campaign
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createFromSubscribersList(Campaign $campaign)
    {
        $sql = "
            insert into
              message (`campaign_id`, `subscriber_id`, `status`,`token`)
            select
              c.id, s.id, 'pending', uuid()
            from subscriber s
              join subscriber_list sl on sl.id = s.subscribers_list_id
              join campaign c on sl.id = c.receivers_list_id
            where c.id = ?
            on duplicate key update campaign_id = c.id;
        ";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(1, $campaign->getId());

        $stmt->execute();
    }

    /**
     * Creates new Message.
     *
     * @param Campaign   $campaign
     * @param Subscriber $subscriber
     *
     * @return Message
     */
    public function create(Campaign $campaign, Subscriber $subscriber)
    {
        $message = new Message();
        $message->setCampaign($campaign);
        $message->setSubscriber($subscriber);
        $message->setToken(Str::random());
        $message->setStatus(Message::STATUS_QUEUED);

        $this->save($message);

        return $message;
    }

    /**
     * @param Campaign $campaign
     *
     * @return int|mixed
     */
    public function getQueuedMessagesCount(Campaign $campaign): ?int
    {
        try {
            return (int) $this->createQueryBuilder('m')
                ->select('count(m)')
                ->where('m.campaign = :campaign')
                ->setParameter('campaign', $campaign)
                ->andWhere('m.status = :status')
                ->setParameter('status', Message::STATUS_QUEUED)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * @param Campaign $campaign
     *
     * @return int
     */
    public function getPendingMessagesCount(Campaign $campaign): ?int
    {
        try {
            return (int) $this->createQueryBuilder('m')
                ->select('count(m)')
                ->where('m.campaign = :campaign')
                ->setParameter('campaign', $campaign)
                ->andWhere('m.status = :status')
                ->setParameter('status', Message::STATUS_PENDING)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * @param Campaign $campaign
     */
    public function restorePausedMessages(Campaign $campaign)
    {
        $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->update(Message::class, 'm')
            ->set('m.status', ':pending')
            ->where('m.status = :paused')
            ->andWhere('m.campaign = :campaign')
            ->setParameter('pending', Message::STATUS_PENDING)
            ->setParameter('paused', Message::STATUS_PAUSED)
            ->setParameter('campaign', $campaign)
            ->getQuery()
            ->execute();
    }

    /**
     * @param Account      $account
     * @param Chronos|null $from
     * @param Chronos|null $to
     *
     * @return mixed
     *
     * @throws NonUniqueResultException
     */
    public function getSentMessagesCount(Account $account, Chronos $from = null, Chronos $to = null)
    {
        $query = $this->getAccountScopeQuery($account)
            ->select(
                'count(m) as count'
            )
            ->andWhere('m.status = :sent')
            ->setParameter('sent', Message::STATUS_SENT);

        $query = $this->applyTimeScope($query, $from, $to);

        try {
            return $query
                ->groupBy('a.id')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * @param Account      $account
     * @param Chronos|null $from
     * @param Chronos|null $to
     *
     * @return mixed
     *
     * @throws NonUniqueResultException
     */
    public function getOpenedMessagesCount(Account $account, Chronos $from = null, Chronos $to = null)
    {
        $query = $this->getAccountScopeQuery($account)
            ->select(
                'count(m) as count'
            )
            ->andWhere('m.status = :sent')
            ->setParameter('sent', Message::STATUS_SENT);

        $query = $this->applyTimeScope($query, $from, $to);

        try {
            return $query->andWhere('m.openedAt is not null')
                ->groupBy('a.id')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * Returns status of send messages count.
     *
     * @return array
     */
    public function getDailySentMessageStats(Account $account, Chronos $from = null, Chronos $to = null)
    {
        $query = $this->createQueryBuilder('m')
            ->join('m.campaign', 'c')
            ->join('c.account', 'a')
            ->select(
                'count(m) as count',
                'm.status',
                'year(m.sentAt) as year',
                'month(m.sentAt) as month',
                'day(m.sentAt) as day'
            )
            ->where('a = :account')
            ->andWhere('m.status not in (:pending, :queued)')
            ->setParameter('account', $account)
            ->setParameter('pending', Message::STATUS_PENDING)
            ->setParameter('queued', Message::STATUS_QUEUED);

        if ($from) {
            $query->andWhere('m.sentAt >= :from')
                ->setParameter('from', $from);
        }

        if ($to) {
            $query->andWhere('m.sentAt <= :to')
                ->setParameter('to', $to);
        }

        return $query
            ->groupBy('m.status')
            ->addGroupBy('year')
            ->addGroupBy('month')
            ->addGroupBy('day')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param QueryBuilder $query
     * @param Chronos|null $from
     * @param Chronos|null $to
     *
     * @return QueryBuilder
     */
    protected function applyTimeScope(QueryBuilder $query, Chronos $from = null, Chronos $to = null)
    {
        if ($from) {
            $query->andWhere('m.sentAt >= :from')
                ->setParameter('from', $from);
        }

        if ($to) {
            $query->andWhere('m.sentAt <= :to')
                ->setParameter('to', $to);
        }

        return $query;
    }

    /**
     * @param Account $account
     *
     * @return QueryBuilder
     */
    protected function getAccountScopeQuery(Account $account)
    {
        return $this->createQueryBuilder('m')
            ->join('m.campaign', 'c')
            ->join('c.account', 'a')
            ->where('a = :account')
            ->setParameter('account', $account);
    }
}
