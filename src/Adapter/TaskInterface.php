<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 26.05.2018
 * Time: 19:32
 */

namespace Zicher\Tasker\Adapter;

/**
 * Interface TaskInterface
 * @package Zicher\Tasker\Adapter
 */
interface TaskInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getQueue(): string;

    /**
     * @return string
     */
    public function getCommand(): string;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @return \DateTime|null
     */
    public function getRunAt(): ?\DateTime;
}