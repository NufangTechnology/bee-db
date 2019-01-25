<?php
namespace Bee\Db\Redis;

use Bee\Db\ItemInterface;

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
     * @var string
     */
    protected $auth = '';

    /**
     * @var int
     */
    protected $timeout = 2;

    /**
     * @var \Redis
     */
    protected $resource;

    /**
     * Item
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['host'])) {
            $this->host = $config['host'];
        }
        if (isset($config['port'])) {
            $this->port = $config['port'];
        }
        if (isset($config['auth'])) {
            $this->auth = $config['auth'];
        }
        if (isset($config['timeout'])) {
            $this->timeout = $config['timeout'];
        }

        $this->resource = new \Redis();
        $this->resource->connect($this->host, $this->port, $this->timeout);

        if ($this->auth) {
            $this->resource->auth($this->auth);
        }
    }

    /**
     * 连接数据库
     *
     * @return bool
     * @throws Exception
     */
    public function connect()
    {
//        if (!$this->resource->connected) {
//            $this->resource->connect($this->host, $this->port);
//            $this->resource->auth($this->password);
//        }
//
//        // 重新连接失败
//        if ($this->resource->connected == false) {
//            throw new Exception('Redis connection close by peer(' . $this->resource->errMsg . ')', $this->resource->errCode);
//        }

        return true;
    }

    /**
     * 关闭数据库连接
     */
    public function close()
    {
        $this->resource->close();
    }

    /**
     * @return \Redis
     */
    public function getResource(): \Redis
    {
        return $this->resource;
    }

    /**
     * 动态调用
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // FIXME 错误处理
        $this->resource->ping();
        // 如果未连接，连接数据库

        return call_user_func_array([$this->resource, $name], $arguments);
    }
}