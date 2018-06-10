<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 25.05.2018
 * Time: 23:27
 */

namespace Zicher\Tasker;

/**
 * Class Task
 * @package Zicher\Tasker
 */
class Task
{
    const QUEUED = 0;
    const OCCUPIED = 1;
    const FINISHED = 2;

    /**
     * @var string|null
     */
    private $id;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $queue;

    /**
     * @var int
     */
    private $status;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var \DateTime|null
     */
    private $runAt;

    /**
     * @var Log|null
     */
    private $log;

    /**
     * Task constructor.
     * @param string $command
     * @param string $queue
     * @param int $priority
     * @param \DateTime|null $runAt
     * @param string|null $id
     */
    public function __construct(string $command, string $queue = 'default', int $priority = 0, ?\DateTime $runAt = null, ?string $id = null)
    {
        $this->command = $command;
        $this->queue = $queue;
        $this->priority = $priority;
        $this->runAt = $runAt;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getQueue(): string
    {
        return $this->queue;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
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
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return null|Log
     */
    public function getLog(): ?Log
    {
        return $this->log;
    }

    /**
     * @return self
     */
    public function markQueued(): self
    {
        $this->status = self::QUEUED;

        return $this;
    }

    /**
     * @return self
     */
    public function markOccupied(): self
    {
        $this->status = self::OCCUPIED;

        return $this;
    }

    /**
     * @param null|Log $log
     * @return self
     */
    public function markFinished(?Log $log): self
    {
        $this->status = self::FINISHED;
        $this->log = $log;

        return $this;
    }
}