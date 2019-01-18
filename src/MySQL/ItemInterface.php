<?php
namespace Bee\Db\MySQL;

/**
 * Interface ItemInterface
 *
 * @package Bee\Db
 */
interface ItemInterface
{
    /**
     * 连接数据库
     *
     * @return mixed
     */
    public function connect();

    /**
     * 数据库重连
     *
     * @return bool
     */
    public function reconnect();

    /**
     * 检查数据库是否连接
     *
     * @return bool
     */
    public function isConnect();

    /**
     * 执行SQL
     *
     * @param string $sql
     * @param float $timeout
     * @return array
     */
    public function query(string $sql, float $timeout);

    /**
     * 关闭数据库连接
     */
    public function close();
}
