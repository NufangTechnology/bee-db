<?php
namespace Bee\Db\MySQL;

use Swoole\Coroutine\MySQL;

/**
 * MySQL 连接实例
 *
 * @package Bee\Db
 */
class Item implements ItemInterface
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var MySQL
     */
    protected $resource;

    /**
     * @var float
     */
    protected $timeout = 3;

    /**
     * Item
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['size'])) {
            $this->size = $config['size'];
        }

        $this->config   = $config;
        $this->resource = new MySQL;

        // 执行连接
        $this->connect();
    }

    /**
     * 连接数据库
     *
     * @return bool
     */
    public function connect()
    {
        return $this->resource->connect($this->config);
    }

    /**
     * 数据库重连
     *
     * @return bool
     */
    public function reconnect()
    {
        if (!$this->isConnect()) {
            return $this->connect();
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
     * 执行SQL
     *  - 如果查询结果为false，执行自动重连
     *  - 自动重连数大于 3 时，抛出异常
     *
     * @param string $sql
     * @param float $timeout
     * @return array
     * @throws Exception
     */
    public function query(string $sql, float $timeout)
    {
        if ($timeout == 0) {
            $timeout = $this->timeout;
        }

        // 执行查询
        $result = $this->resource->query($sql, $timeout);

        // 查询结果为false，自动重连
        if ($result == false && !$this->isConnect()) {

            // 重新连接
            $this->connect();

            if (!$this->isConnect()) {
                throw new Exception('Connection close by peer(' . $this->resource->error . ')', $this->resource->errno);
            }

            // 重新执行当前 sql
            $result = $this->resource->query($sql, $timeout);
        }

        return $result;
    }

    /**
     * 关闭数据库连接
     */
    public function close()
    {
        $this->resource->close();
    }
}
