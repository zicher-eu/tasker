<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 09.06.2018
 * Time: 20:49
 */

namespace Zicher\Tasker\Adapter\Pdo;

use Zicher\Tasker\Adapter\ManagerInterface;
use Zicher\Tasker\Adapter\Pdo\Adapter\MySql;
use Zicher\Tasker\Adapter\Pdo\Adapter\Sqlite;
use Zicher\Tasker\Adapter\TaskInterface;
use Zicher\Tasker\Task;

/**
 * Class PdoManager
 * @package Zicher\Tasker\Adapter\Pdo
 */
class PdoManager implements ManagerInterface
{
    /**
     *
     */
    const DEFAULT_TABLE_NAME = 'task';

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var AdapterInterface
     */
    private $driver;

    /**
     * PdoManager constructor.
     * @param \PDO $pdo
     * @param null|string $table
     */
    public function __construct(\PDO $pdo, ?string $table = self::DEFAULT_TABLE_NAME)
    {
        $this->pdo = $pdo;

        switch ($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
            case 'mysql':
                $this->driver = new MySql($pdo, $table);
                break;
            case 'sqlite':
                $this->driver = new Sqlite($pdo, $table);
                break;
        }
    }

    /**
     * @param string $queue
     * @return null|TaskInterface
     */
    public function occupy(string $queue = 'default'): ?TaskInterface
    {
        $this->pdo->beginTransaction();

        $statement = $this->driver->getOccupySelectStatement();
        $statement->bindValue(':queue', $queue);
        $statement->execute();

        $tasks = $statement->fetchAll();

        foreach ($tasks as $task) {
            $statement = $this->driver->getOccupyUpdateStatement();
            $statement->bindValue(':status', Task::OCCUPIED);
            $statement->bindValue(':id', $task['id']);
            $statement->execute();

            $this->pdo->commit();

            $taskEntity = new PdoTask(
                $task['id'],
                $task['queue'],
                $task['command'],
                $task['status'],
                $task['priority'],
                \DateTime::createFromFormat('Y-m-d H:i:s', $task['runAt']) ?: null
            );

            return $taskEntity;
        }

        $this->pdo->commit();

        return null;
    }

    /**
     * @param Task $task
     * @param string $queue
     * @return ManagerInterface
     */
    public function push(Task $task, string $queue = 'default'): ManagerInterface
    {
        $statement = $this->driver->getInsertStatement();

        $statement->bindValue(':id', $this->generateUuid(), \PDO::PARAM_STR);
        $statement->bindValue(':queue', $task->getQueue(), \PDO::PARAM_STR);
        $statement->bindValue(':command', $task->getCommand(), \PDO::PARAM_STR);
        $statement->bindValue(':priority', $task->getPriority(), \PDO::PARAM_INT);
        $statement->bindValue(':status', $task->getStatus(), \PDO::PARAM_INT);

        if ($task->getRunAt() instanceof \DateTime) {
            $statement->bindValue(':runAt', $task->getRunAt()->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
        } else {
            $statement->bindValue(':runAt', null, \PDO::PARAM_NULL);
        }

        $statement->execute();

        return $this;
    }

    /**
     * @param Task $task
     * @return ManagerInterface
     */
    public function finish(Task $task): ManagerInterface
    {
        $statement = $this->driver->getFinishStatement();
        $statement->bindValue(':status', $task->getStatus(), \PDO::PARAM_INT);
        $statement->bindValue(':log', json_encode($task->getLog()), \PDO::PARAM_STR);
        $statement->bindValue(':id', $task->getId(), \PDO::PARAM_STR);

        $statement->execute();

        return $this;
    }

    /**
     * @return array|null
     */
    public function gc(): ?array
    {
        $statement = $this->driver->getGcSelectStatement();
        $statement->bindValue(':status', Task::FINISHED, \PDO::PARAM_INT);
        $statement->execute();

        $tasks = $statement->fetchAll();

        $logs = [];
        foreach ($tasks as $task) {
            $logs[$task['id']] = $task['log'];
        }

        $ids = array_keys($logs);
        $idsPlaceholder = str_repeat('?,', count($ids) - 1) . '?';

        $statement = $this->driver->getGcDeleteStatement($idsPlaceholder);
        $statement->execute($ids);

        return $logs;
    }

    /**
     * http://php.net/manual/en/function.uniqid.php#94959
     * @return string
     */
    private function generateUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}