<?php
namespace Bee\Db\MySQL;

use Swoole\Coroutine\Channel;

/**
 * MySQL 连接池
 *
 * @package Bee\Db\MySQL
 */
class Pool implements PoolInterface
{
    /**
     * channel 获取对象超时时间
     *
     * @var float
     */
    protected $timeout = 2;

    /**
     * @var Channel
     */
    protected $pool;

    /**
     * Pool constructor.
     *
     * @param int $size
     */
    public function __construct(int $size)
    {
        $this->pool = new Channel($size);
    }

    /**
     * @param Item $mysql
     * @return bool
     */
    public function put(Item $mysql)
    {
        return $this->pool->push($mysql);
    }

    /**
     * @return Item
     * @throws Exception
     */
    public function get() : Item
    {
        $mysql = $this->pool->pop($this->timeout);

        if ($mysql === false) {
            throw new Exception('Get Item instance timeout, all connection is used!');
        }

        return $mysql;
    }

    /**
     * @return int
     */
    public function getLength() : int
    {
        return $this->pool->length();
    }
}
