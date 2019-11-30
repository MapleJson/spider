<?php

declare (strict_types = 1);

namespace spider;

use Exception;

/**
 * Class Model
 *
 * @method \Workerman\MySQL\Connection select(string $cols = '*') static
 * @method \Workerman\MySQL\Connection insert() static
 * @method \Workerman\MySQL\Connection update() static
 * @method \Workerman\MySQL\Connection delete() static
 *
 * @package spider
 */
class Model
{
    protected $connect = '';

    protected $table = '';

    private static $_instance = [];

    /**
     * 拉取数据库配置
     *
     * @throws Exception
     */
    protected static function getDatabaseConfig()
    {
        if (empty(self::instance()->connect)) {
            Log::error('No such connection', __FILE__, __LINE__);
            throw new Exception('No such connection');
        }

        $setting = config('database.' . self::instance()->connect);

        if (empty(self::instance()->table)) {
            self::instance()->table = strtolower(basename(str_replace('\\', '/', static::class)));
        }

        if (!empty($setting['prefix'])) {
            self::instance()->table = $setting['prefix'] . ltrim(self::instance()->table, $setting['prefix']);
        }

        return $setting;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed|\Workerman\MySQL\Connection
     * @throws Exception
     */
    public static function __callStatic($name, $arguments)
    {
        $setting = self::getDatabaseConfig();

        if (empty($setting['type'])) return '';

        if ($setting['type'] === 'mysql') {
            if ($name === 'select') {
                return DB::mysql($setting)->select($arguments ?: '*')->from(self::instance()->table);
            }
            return DB::mysql($setting)->{$name}(self::instance()->table);
        }

        return DB::{$setting['type']}($setting);
    }

    /**
     * @return mixed
     */
    public static function instance()
    {
        if (empty(self::$_instance[static::class])) {
            self::$_instance[static::class] = new static();
        }
        return self::$_instance[static::class];
    }

    public static function save(array $data)
    {
        return static::insert()->cols($data)->query();
    }
}