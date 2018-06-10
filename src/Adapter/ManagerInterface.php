<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 26.05.2018
 * Time: 19:31
 */

namespace Zicher\Tasker\Adapter;

use Zicher\Tasker\Task;

/**
 * Interface ManagerInterface
 * @package Zicher\Tasker\Adapter
 */
interface ManagerInterface
{
    /**
     * @param string $queue
     * @return null|TaskInterface
     */
    public function occupy(string $queue = 'default'): ?TaskInterface;

    /**
     * @param Task $task
     * @param string $queue
     * @return ManagerInterface
     */
    public function push(Task $task, string $queue = 'default'): self;

    /**
     * @param Task $task
     * @return ManagerInterface
     */
    public function finish(Task $task): self;

    /**
     * @return array|null
     */
    public function gc(): ?array;
}