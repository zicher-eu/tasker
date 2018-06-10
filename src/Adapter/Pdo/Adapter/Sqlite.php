<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 10.06.2018
 * Time: 11:35
 */

namespace Zicher\Tasker\Adapter\Pdo\Adapter;

use Zicher\Tasker\Adapter\Pdo\AbstractAdapter;
use Zicher\Tasker\Adapter\Pdo\AdapterInterface;

/**
 * Class Sqlite
 * @package Zicher\Tasker\Adapter\Pdo\Adapter
 */
class Sqlite extends AbstractAdapter implements AdapterInterface
{
    /**
     * @return \PDOStatement
     */
    public function getOccupySelectStatement(): \PDOStatement
    {
        return $this->pdo->prepare(
            'SELECT 
                id, queue, command, status, priority, runAt 
            FROM ' . $this->table . ' 
            WHERE queue = :queue AND (runAt IS NULL OR runAt > CURRENT_TIMESTAMP)
            ORDER BY priority DESC
            LIMIT 1 
        ');
    }

    /**
     * @return \PDOStatement
     */
    public function getOccupyUpdateStatement(): \PDOStatement
    {
        return $this->pdo->prepare('UPDATE ' . $this->table . ' SET status = :status WHERE id = :id');
    }

    /**
     * @return \PDOStatement
     */
    public function getInsertStatement(): \PDOStatement
    {
        return $this->pdo->prepare('INSERT INTO ' . $this->table . ' 
            (id, queue, command, priority, runAt, status) VALUES (:id, :queue, :command, :priority, :runAt, :status)'
        );
    }

    /**
     * @return \PDOStatement
     */
    public function getFinishStatement(): \PDOStatement
    {
        return $this->pdo->prepare('UPDATE ' . $this->table . ' SET status = :status, log = :log WHERE id = :id');
    }

    /**
     * @return \PDOStatement
     */
    public function getGcSelectStatement(): \PDOStatement
    {
        return $this->pdo->prepare('SELECT id, log FROM ' . $this->table . ' WHERE status = :status');
    }

    /**
     * @param string $placeholders
     * @return \PDOStatement
     */
    public function getGcDeleteStatement(string $placeholders): \PDOStatement
    {
        return $this->pdo->prepare('DELETE FROM ' . $this->table . ' WHERE id IN (' . $placeholders . ')');
    }
}