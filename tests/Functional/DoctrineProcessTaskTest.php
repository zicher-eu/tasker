<?php

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;
use Zicher\Tasker\Adapter\Doctrine\DoctrineManager;
use Zicher\Tasker\Log;
use Zicher\Tasker\Manager;
use Zicher\Tasker\Task;

/**
 * Class DoctrineProcessTaskTest
 */
class DoctrineProcessTaskTest extends TestCase
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
        $config = Setup::createAnnotationMetadataConfiguration([
            __DIR__ . '../../src'
        ], true, null, null, false);

        $conn = array(
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../Fixtures/doctrine.sqlite',
        );

        $class = '\Fixture\DoctrineTask';

        try {
            $entityManager = EntityManager::create($conn, $config);
        } catch (ORMException $e) {
        }

        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getClassMetadata($class);
        $sqlDiff = $schemaTool->getUpdateSchemaSql([$metadata], true);

        $connection = $entityManager->getConnection();
        foreach ($sqlDiff as $sql) {
            try {
                $statement = $connection->prepare($sql);
                $statement->execute();
            } catch (DBALException $e) {
            }
        }

        self::$manager = new Manager(new DoctrineManager($entityManager, $class));
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