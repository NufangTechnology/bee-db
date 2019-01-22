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
     * Item
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['options'])) {
            $this->options  = $config['options'];
        }
        if (isset($config['host'])) {
            $this->host = $config['host'];
        }
        if (isset($config['port'])) {
            $this->port = $config['port'];
        }

        $this->resource = new Redis($config);
        // 设置 redis 配置
        $this->resource->setOptions($this->options);
    }

    /**
     * 连接数据库
     *
     * @return bool
     * @throws Exception
     */
    public function connect()
    {
        if (!$this->resource->connected) {
            $this->resource->connect($this->host, $this->port);
        }

        // 重新连接失败
        if ($this->resource->connected == false) {
            throw new Exception('Redis connection close by peer(' . $this->resource->errMsg . ')', $this->resource->errCode);
        }

        return true;
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

    /**
     * @return Redis
     */
    public function getResource(): Redis
    {
        return $this->resource;
    }

    /**
     * 动态调用
     *
     * @param string $name
     * @param string $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        // 如果未连接，连接数据库
        $this->connect();

        return call_user_func_array([$this->resource, $name], $arguments);
    }
}