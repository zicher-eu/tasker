<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 10.06.2018
 * Time: 11:55
 */

namespace Zicher\Tasker\Adapter\Pdo;

/**
 * Class AbstractAdapter
 * @package Zicher\Tasker\Adapter\Pdo
 */
abstract class AbstractAdapter
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $table;

    /**
     * MySql constructor.
     * @param \PDO $pdo
     * @param string $table
     */
    public function __construct(\PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }
}