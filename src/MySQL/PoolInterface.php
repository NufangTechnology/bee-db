<?php
namespace Bee\Db\MySQL;

/**
 * Interface PoolInterface
 *
 * @package Bee\Db\MySQL
 */
interface PoolInterface
{
    /**
     * @param Item $mySQL
     * @return bool
     */
    public function put(Item $mySQL);

    /**
     * @return Item
     */
    public function get();
}
