<?php

namespace Fixture;

use Doctrine\ORM\Mapping as ORM;
use Zicher\Tasker\Adapter\Doctrine\Entity\Task as BaseTask;

/**
 * Class DoctrineTask
 * @package Fixture
 * @ORM\Entity(repositoryClass="Zicher\Tasker\Adapter\Doctrine\Repository\TaskRepository")
 */
class DoctrineTask extends BaseTask
{

}