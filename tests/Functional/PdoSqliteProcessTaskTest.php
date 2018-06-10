<?php

use PHPUnit\Framework\TestCase;
use Zicher\Tasker\Adapter\Pdo\PdoManager;
use Zicher\Tasker\Log;
use Zicher\Tasker\Manager;
use Zicher\Tasker\Task;

/**
 * Class PdoProcessTaskTest
 */
class PdoSqliteProcessTaskTest extends TestCase
{
    /**
     * @var Manager
     */
    private static $manager;

    /**
     *
     */
    public static function setUpBeforeClass()
    {
        $databasePath = __DIR__ . '/../Fixtures/pdo.sqlite';

        unlink($databasePath);

        $pdo = new \PDO('sqlite:' .  $databasePath);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('create table if not exists ' . PdoManager::DEFAULT_TABLE_NAME . '(
            id CHAR(36) not null primary key,
            queue VARCHAR(255) not null,
            command VARCHAR(255) not null,
            status INTEGER not null,
            priority INTEGER not null,
            runAt DATETIME default NULL,
            log CLOB default NULL
        );');

        self::$manager = new Manager(new PdoManager($pdo));
    }

    /**
     *
     */
    public function testProcessTask()
    {
        $task = self::$manager->occupy();
        $this->assertEquals(null, $task);

        $task = new Task('test:command');
        self::$manager->push($task);
        $this->assertEquals(Task::QUEUED, $task->getStatus());

        $start = microtime(true);
        $task = self::$manager->occupy();
        $this->assertEquals(Task::OCCUPIED, $task->getStatus());

        $logData = [
            'data' => ['test' => 'foo'],
            'start' => $start,
            'stop' => microtime(true),
        ];

        self::$manager->finish($task, new Log($logData['data'], $logData['start'], $logData['stop']));
        $this->assertEquals(Task::FINISHED, $task->getStatus());

        $logs = self::$manager->gc();
        $this->assertTrue(count($logs) >= 1);
        $this->assertEquals($logData, json_decode(reset($logs), true));
    }
}