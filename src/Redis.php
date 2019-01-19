<?php
namespace Bee\Db;

use Bee\Db\Redis\Item;
use Bee\Db\Redis\Pool;

/**
 * Redis 连接器
 *
 * @package Bee\Db
 */
class Redis extends Layer
{
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
     * 执行主节点相关业务
     *
     * @param callable $callback
     */
    public function master(callable $callback)
    {
        // 取一个连接进行业务操作
        $item = $this->masterPool->get();
        // 执行业务回调
        $callback($item);
        // 业务处理结束，连接放回连接池
        $this->masterPool->put($item);
    }

    /**
     * 执行从节点相关业务
     *
     * @param callable $callback
     */
    public function slave(callable $callback)
    {
        // 取一个连接进行业务操作
        $item = $this->slavePool->get();
        // 执行业务回调
        $callback($item);
        // 业务处理结束，连接放回连接池
        $this->slavePool->put($item);
    }

    /**
     * 获取一个主节点连接
     *
     * @return \Swoole\Coroutine\Redis
     */
    public function getMasterConnect()
    {
        return $this->masterPool->get();
    }

    /**
     * 将主节点连接放回连接池
     *
     * @param $item
     */
    public function putMasterConnect($item)
    {
        $this->masterPool->put($item);
    }

    /**
     * 获取一个从节点连接
     *
     * @return \Swoole\Coroutine\Redis
     */
    public function getSlaveConnect()
    {
        return $this->slavePool->get();
    }

    /**
     * 将从节点连接放回连接池
     *
     * @param $item
     */
    public function putSlaveConnect($item)
    {
        $this->slavePool->put($item);
    }
}
