<?php
/**
 * Created by PhpStorm.
 * User: Tomasz Kotlarek (ZICHER)
 * Date: 25.05.2018
 * Time: 23:32
 */

namespace Zicher\Tasker;

use JsonSerializable;

/**
 * Class Log
 * @package Zicher\Tasker
 */
class Log implements JsonSerializable
{
    /**
     * @var float|null
     */
    private $start;

    /**
     * @var float|null
     */
    private $stop;

    /**
     * @var mixed
     */
    private $data;

    /**
     * Log constructor.
     * @param $data
     * @param float|null $start
     * @param float|null $stop
     */
    public function __construct($data, ?float $start = null, ?float $stop = null)
    {
        $this->data = $data;
        $this->start = $start;
        $this->stop = $stop;
    }

    /**
     * @return float|null
     */
    public function getStart(): ?float
    {
        return $this->start;
    }

    /**
     * @return float|null
     */
    public function getStop(): ?float
    {
        return $this->stop;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'data' => $this->getData(),
            'start' => $this->getStart(),
            'stop' => $this->getStop(),
        ];
    }
}