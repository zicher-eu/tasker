<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 26.05.2018
 * Time: 19:30
 */

namespace Zicher\Tasker;

use Zicher\Tasker\Adapter\ManagerInterface;

/**
 * Class Manager
 * @package Zicher\Tasker
 */
class Manager
{
    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * Manager constructor.
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string $queue
     * @return null|Task
     */
    public function occupy(string $queue = 'default'): ?Task
    {
        $taskAdapter = $this->manager->occupy($queue);

        if (null === $taskAdapter) {
            return null;
        }

        $task =  new Task(
            $taskAdapter->getCommand(),
            $taskAdapter->getQueue(),
            $taskAdapter->getPriority(),
            $taskAdapter->getRunAt(),
            $taskAdapter->getId()
        );

        return $task->markOccupied();
    }

    /**
     * @param Task $task
     * @param array $options
     * @return Manager
     */
    public function push(Task $task, array $options = []): self
    {
        $this->manager->push($task->markQueued(), $options);

        return $this;
    }

    /**
     * @param Task $task
     * @param null|Log $log
     * @return Manager
     */
    public function finish(Task $task, ?Log $log): self
    {
        $this->manager->finish($task->markFinished($log));

        return $this;
    }

    /**
     * @return array
     */
    public function gc(): array
    {
        return $this->manager->gc();
    }
}