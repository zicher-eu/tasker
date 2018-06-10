<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 28.01.2018
 * Time: 23:57
 */

namespace Zicher\Tasker\Adapter\Doctrine\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\TransactionRequiredException;
use Zicher\Tasker\Adapter\Doctrine\Entity\Task;

/**
 * Class TaskRepository
 * @package Zicher\Tasker\Adapter\Doctrine\Repository
 */
class TaskRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function findOneByQueueAndHighestPriorityAndStatusWaitingAndScheduledNowQueryBuilder(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.status = 0')
            ->andWhere('t.queue = :queue')
            ->andWhere('t.runAt is null or t.runAt < CURRENT_TIMESTAMP()')
            ->orderBy('t.priority', 'DESC')
            ->setMaxResults(1);
    }

    /**
     * @return Query
     */
    public function findOneByQueueAndHighestPriorityAndStatusWaitingAndScheduledNowQuery(): Query
    {
        return $this
            ->findOneByQueueAndHighestPriorityAndStatusWaitingAndScheduledNowQueryBuilder()
            ->getQuery();
    }

    /**
     * @param string $queue
     * @return Task|null
     * @throws TransactionRequiredException
     * @throws NonUniqueResultException
     */
    public function findOneByQueueAndHighestPriorityAndStatusWaitingAndScheduledNow(string $queue): ?Task
    {
        $query = $this
            ->findOneByQueueAndHighestPriorityAndStatusWaitingAndScheduledNowQuery()
            ->setParameter('queue', $queue);

        if ($this->getEntityManager()->getConnection()->isTransactionActive()) {
            $query->setLockMode(LockMode::PESSIMISTIC_WRITE);
        }

        return $query->getOneOrNullResult();
    }

    /**
     * @return QueryBuilder
     */
    public function findAllFinishedQueryBuilder(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.status = 2');
    }

    /**
     * @return Query
     */
    public function findAllFinishedQuery(): Query
    {
        return $this
            ->findAllFinishedQueryBuilder()
            ->getQuery();
    }

    /**
     * @return array
     */
    public function findAllFinished(): array
    {
        return $this
            ->findAllFinishedQuery()
            ->getResult();
    }
}