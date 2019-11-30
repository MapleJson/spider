<?php

declare (strict_types = 1);

namespace spider;

class DB
{
    /**
     * 数据库实例连接池
     * @var array
     */
    protected static $connection = [];

    /**
     * 连接数据库
     *
     * @param array $config
     *
     * @return \Workerman\MySQL\Connection
     */
    public static function mysql(array $config): \Workerman\MySQL\Connection
    {
        if (empty(self::$connection[$config['database']])) {
            self::$connection[$config['database']] = new \Workerman\MySQL\Connection(
                $config['hostname'],
                $config['port'],
                $config['username'],
                $config['password'],
                $config['database']
            );
        }
        return self::$connection[$config['database']];
    }
}