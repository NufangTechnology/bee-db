<?php
namespace Bee\Db;


/**
 * Interface PoolInterface
 *
 * @package Bee\Db
 */
interface PoolInterface
{
    /**
     * @param \Bee\Db\MySQL\Item|\Bee\Db\Redis\Item $item
     * @return bool
     */
    public function put($item);

    /**
     * @return \Bee\Db\MySQL\Item|\Swoole\Coroutine\Redis
     */
    public function get();
}
