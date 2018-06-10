<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 10.06.2018
 * Time: 11:22
 */

namespace Zicher\Tasker\Adapter\Pdo;

/**
 * Interface PdoAdapterInterface
 * @package Zicher\Tasker\Adapter\Pdo
 */
interface AdapterInterface
{
    public function getOccupySelectStatement(): \PDOStatement;
    public function getOccupyUpdateStatement(): \PDOStatement;
    public function getInsertStatement(): \PDOStatement;
    public function getFinishStatement(): \PDOStatement;
    public function getGcSelectStatement(): \PDOStatement;
    public function getGcDeleteStatement(string $placeholders): \PDOStatement;
}