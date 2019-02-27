<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 29.05.2018
 * Time: 21:31
 */

namespace Zicher\Tasker\Adapter\Doctrine;

use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\TransactionRequiredException;
use Zicher\Tasker\Adapter\Doctrine\Repository\TaskRepository;
use Zicher\Tasker\Adapter\ManagerInterface;
use Zicher\Tasker\Adapter\TaskInterface;
use Zicher\Tasker\Task;
use Zicher\Tasker\Adapter\Doctrine\Entity\Task as TaskEntity;

/**
 * Class DoctrineManager
 * @package Zicher\Tasker\Adapter\Doctrine
 */
class DoctrineManager implements ManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $class;

    /**
     * DoctrineManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param string $class
     */
    public function __construct(EntityManagerInterface $entityManager, string $class)
    {
        $this->entityManager = $entityManager;
        $this->class = $class;
    }

    /**
     * @param string $queue
     * @return null|TaskInterface
     * @throws NonUniqueResultException
     * @throws TransactionRequiredException
     * @throws ConnectionException
     */
    public function occupy(string $queue = 'default'): ?TaskInterface
    {
        $taskRepository = $this->getTaskRepository();

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        /** @var TaskEntity $taskEntity */
        $taskEntity = $taskRepository->findOneByQueueAndHighestPriorityAndStatusWaitingAndScheduledNow($queue);

        if (null === $taskEntity) {
            return null;
        }

        $taskEntity->setStatus(Task::OCCUPIED);

        $this->entityManager->persist($taskEntity);
        $this->entityManager->flush();

        $connection->commit();

        return $taskEntity;
    }

    /**
     * @param Task $task
     * @param string $queue
     * @param array $options
     * @return ManagerInterface
     */
    public function push(Task $task, string $queue = 'default', array $options = []): ManagerInterface
    {
        $class = $this->class;

        /** @var TaskEntity $taskEntity */
        $taskEntity = new $class();
        $taskEntity
            ->setCommand($task->getCommand())
            ->setQueue($task->getQueue())
            ->setStatus($task->getStatus())
            ->setPriority($task->getPriority())
            ->setRunAt($task->getRunAt());

        $this->entityManager->persist($taskEntity);

        if (false === (array_key_exists('noflush', $options) && true === $options['noflush'])) {
            $this->entityManager->flush();
        }

        return $this;
    }

    /**
     * @param Task $task
     * @return ManagerInterface
     */
    public function finish(Task $task): ManagerInterface
    {
        $taskRepository = $this->getTaskRepository();

        /** @var TaskEntity $taskEntity */
        $taskEntity = $taskRepository->find($task->getId());
        $taskEntity
            ->setStatus($task->getStatus())
            ->setLog(json_encode($task->getLog()));

        $this->entityManager->persist($taskEntity);
        $this->entityManager->flush();

        return $this;
    }

    /**
     * @return array|null
     */
    public function gc(): ?array
    {
        /** @var TaskEntity[] $tasks */
        $tasks = $this->getTaskRepository()->findAllFinished();

        /** @var string[] $logs */
        $logs = [];

        foreach ($tasks as $task) {
            $logs[$task->getId()] = $task->getLog();

            $this->entityManager->remove($task);
        }

        $this->entityManager->flush();

        return $logs;
    }

    /**
     * @return TaskRepository
     */
    private function getTaskRepository(): TaskRepository
    {
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->entityManager->getRepository($this->class);

        return $taskRepository;
    }
}