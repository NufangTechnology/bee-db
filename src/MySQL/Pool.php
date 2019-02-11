<?php
namespace Bee\Db\MySQL;

use Bee\Db\PoolInterface;
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
    protected $timeout = 40;

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
     * @param Item $item
     * @return bool
     */
    public function put($item)
    {
        return $this->pool->push($item);
    }

    /**
     * @return Item
     * @throws Exception
     */
    public function get() : Item
    {
        $item = $this->pool->pop($this->timeout);

        if ($item === false) {
            throw new Exception('Get Item instance timeout, all connection is used!');
        }

        return $item;
    }

    /**
     * @return int
     */
    public function getLength() : int
    {
        return $this->pool->length();
    }
}
