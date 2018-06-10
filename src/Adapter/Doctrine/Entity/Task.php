<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 25.05.2018
 * Time: 23:29
 */

namespace Zicher\Tasker\Adapter\Doctrine\Entity;

use Zicher\Tasker\Adapter\TaskInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Task
 * @package Zicher\Tasker\Adapter\Doctrine\Entity
 * @ORM\MappedSuperclass()
 */
class Task implements TaskInterface
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $queue;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $command;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $priority;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $runAt;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    protected $log;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $queue
     * @return self
     */
    public function setQueue(string $queue): self
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * @return string
     */
    public function getQueue(): string
    {
        return $this->queue;
    }

    /**
     * @param string $command
     * @return self
     */
    public function setCommand(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @param int $status
     * @return self
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $priority
     * @return self
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param \DateTime|null $runAt
     * @return self
     */
    public function setRunAt(?\DateTime $runAt): self
    {
        $this->runAt = $runAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getRunAt(): ?\DateTime
    {
        return $this->runAt;
    }

    /**
     * @param null|string $log
     * @return self
     */
    public function setLog(?string $log): self
    {
        $this->log = $log;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLog(): ?string
    {
        return $this->log;
    }
}