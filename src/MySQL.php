<?php
namespace Bee\Db;

use Bee\Db\MySQL\Pool;
use Bee\Db\MySQL\Item;

/**
 * MySQL 连接器
 *
 * @package Bee\Db
 */
class MySQL
{
    /**
     * @var Pool
     */
    private $masterPool;

    /**
     * @var Pool
     */
    private $slavePool;

    /**
     * MySQL constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        // 如果存在主节点配置，初始化
        if (isset($config['master'])) {
            $this->initMasterPool($config['master']);
        }

        // 如果存在从节点配置，初始化
        if (isset($config['slave'])) {
            $this->initSlavePool($config['slave']);
        }

        // 如果只存在一个配置，主从各创建
        if (isset($config['host'])) {
            $this->initMasterPool($config);
            $this->initSlavePool($config);
        }
    }

    /**
     * 初始化主节点连接池
     *
     * @param array $config
     */
    protected function initMasterPool(array $config)
    {
        $pool = [];

        if (isset($config['host'])) {
            $config = [$config];
        }

        foreach ($config as $item) {
            $size = $item['pool_size'] ?? 1;

            while ($size--) {
                $pool[] = new Item($item);
            }
        }

        // 创建连接池 chan
        $this->masterPool = new Pool(count($pool));
        // 将生成好的连接放入连接池
        foreach ($pool as $item) {
            $this->masterPool->put($item);
        }
    }

    /**
     * 初始化从节点连接池
     *
     * @param array $config
     */
    protected function initSlavePool(array $config)
    {
        $pool = [];

        if (isset($config['host'])) {
            $config = [$config];
        }

        foreach ($config as $item) {
            $size = $item['pool_size'] ?? 1;

            while ($size--) {
                $pool[] = new Item($item);
            }
        }

        // 创建连接池 chan
        $this->slavePool = new Pool(count($pool));
        // 将生成好的连接放入连接池
        foreach ($pool as $item) {
            $this->slavePool->put($item);
        }
    }

    /**
     * 发送待执行 sql
     *
     * @param string $sql
     * @param Item $item
     * @param float $timeout
     * @return array
     * @throws MySQL\Exception
     */
    protected function send(string $sql, Item $item, float $timeout)
    {
        return $item->query($sql, $timeout);
    }

    /**
     * 数据库主节点 sql 操作
     *
     * @param string $sql
     * @param float $timeout
     * @return array
     * @throws MySQL\Exception
     */
    public function master(string $sql, float $timeout = 0)
    {
        // 取一个连接进行业务操作
        $item   = $this->masterPool->get();
        // 执行数据库业务
        $result = $this->send($sql, $item, $timeout);

        // 业务处理结束，连接放回连接池
        $this->masterPool->put($item);

        return $result;
    }

    /**
     * 数据库从节点 sql 操作
     *
     * @param string $sql
     * @param float $timeout
     * @return array
     * @throws MySQL\Exception
     */
    public function slave(string $sql, float $timeout = 0)
    {
        // 取一个连接进行业务操作
        $item   = $this->slavePool->get();
        // 执行数据库业务
        $result = $this->send($sql, $item, $timeout);

        // 业务处理结束，连接放回连接池
        $this->masterPool->put($item);

        return $result;
    }

    /**
     * sql 插入操作
     *
     * @param string $sql
     * @param float $timeout
     * @return array
     * @throws MySQL\Exception
     */
    public function insert(string $sql, float $timeout = 0)
    {
        return $this->master($sql, $timeout);
    }

    /**
     * sql 查询操作
     *
     * @param string $sql
     * @param float $timeout
     * @return array
     * @throws MySQL\Exception
     */
    public function select(string $sql, float $timeout = 0)
    {
        return $this->slave($sql, $timeout);
    }

    /**
     * sql 更新操作
     *
     * @param string $sql
     * @param float $timeout
     * @return array
     * @throws MySQL\Exception
     */
    public function update(string $sql, float $timeout = 0)
    {
        return $this->slave($sql, $timeout);
    }

    /**
     * sql 删除操作
     *
     * @param string $sql
     * @param float $timeout
     * @return array
     * @throws MySQL\Exception
     */
    public function delete(string $sql, float $timeout = 0)
    {
        return $this->slave($sql, $timeout);
    }
}