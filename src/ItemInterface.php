<?php
namespace Bee\Db;

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
     * 检查数据库是否连接
     *
     * @return bool
     */
    public function isConnect();

    /**
     * 关闭数据库连接
     */
    public function close();
}
