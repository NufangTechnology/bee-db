<?php
namespace Bee\Db\MySQL;

use Bee\Db\ItemInterface;
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
    protected $timeout = 1;

    /**
     * Item
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config   = $config;
        $this->resource = new MySQL;
    }

    /**
     * 连接数据库
     *
     * @return bool
     * @throws Exception
     */
    public function connect()
    {
        $this->resource->connect($this->config);

        // 重新连接失败
        if ($this->resource->connected == false) {
            throw new Exception('MySQL connection close by peer(' . $this->resource->connect_error . ')', $this->resource->errno);
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

        // 检测数据库是否已连接
        // 如果未连接，尝试进行连接
        if (!$this->isConnect()) {
            $this->connect();
        }

        // 执行查询
        $result = $this->resource->query($sql, $timeout);

        // 查询结果为false
        if ($result == false) {
            // SQL 执行超时
            if ($this->resource->errno == 110) {
                throw new Exception('SQL execution timeout', $this->resource->errno);
            }
            // 查询出错
            elseif ($this->resource->error) {
                throw new Exception($this->resource->error, $this->resource->errno);
            }
        }

        // 插入操作时，返回插入记录的 ID
        if ($this->resource->insert_id) {
            return $this->resource->insert_id;
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
