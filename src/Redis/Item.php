<?php
namespace Bee\Db\Redis;

use Bee\Db\ItemInterface;
use Swoole\Coroutine\Redis;

/**
 * Redis 连接实例
 *
 * @package Bee\Db\Redis
 */
class Item implements ItemInterface
{
    /**
     * @var string
     */
    protected $host = '127.0.0.1';

    /**
     * @var int
     */
    protected $port = 6379;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var Redis
     */
    protected $resource;

    /**
     * @var float
     */
    protected $timeout = 1;

    /**
     * Item
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['options'])) {
            $this->options  = $config['options'];
        }


        $this->resource = new Redis($config);
        // 设置 redis 配置
        $this->resource->setOptions($this->options);
    }

    /**
     * 连接数据库
     *
     * @return bool
     */
    public function connect()
    {
        return $this->resource->connect($this->host, $this->port);
    }

    /**
     * 检查数据库是否连接
     *
     * @return bool
     */
    public function isConnect()
    {
        return $this->resource->connected;
    }

    /**
     * 关闭数据库连接
     */
    public function close()
    {
        return $this->resource->close();
    }
}