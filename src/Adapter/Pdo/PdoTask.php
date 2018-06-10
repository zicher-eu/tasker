<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 09.06.2018
 * Time: 21:33
 */

namespace Zicher\Tasker\Adapter\Pdo;

use Zicher\Tasker\Adapter\TaskInterface;

/**
 * Class PdoTask
 * @package Zicher\Tasker\Adapter\Pdo
 */
class PdoTask implements TaskInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $queue;

    /**
     * @var string
     */
    private $command;

    /**
     * @var int
     */
    private $status;

    /**
     * @var int|null
     */
    private $priority;

    /**
     * @var \DateTime|null
     */
    private $runAt;

    /**
     * PdoTask constructor.
     * @param string $id
     * @param string $queue
     * @param string $command
     * @param int $status
     * @param int|null $priority
     * @param \DateTime|null $runAt
     */
    public function __construct(
        string $id,
        string $queue,
        string $command,
        int $status,
        ?int $priority,
        ?\DateTime $runAt
    )
    {
        $this->id = $id;
        $this->queue = $queue;
        $this->command = $command;
        $this->priority = $priority;
        $this->runAt = $runAt;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getQueue(): string
    {
        return $this->queue;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return \DateTime|null
     */
    public function getRunAt(): ?\DateTime
    {
        return $this->runAt;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}