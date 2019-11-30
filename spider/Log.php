<?php

declare (strict_types = 1);

namespace spider;

/**
 * Class Log
 * @package Data
 * @example Log::log('this is logs');
 * @example Log::info('this is logs', __FILE__, __LINE__);
 * @example Log::error('this is logs', __FILE__, __LINE__);
 */
class Log
{
    /**
     * 无附加信息记录日志
     *
     * @param string $logs
     */
    public static function log(string $logs)
    {
        echo $logs, PHP_EOL;
    }

    /**
     * 记录执行日志
     *
     * @param string $logs
     * @param string $file
     * @param int    $line
     */
    public static function info(string $logs, string $file, int $line)
    {
        echo '[', date('Y-m-d H:i:s'), '] ', "{$file}({$line}) : {$logs}", PHP_EOL;
    }

    /**
     * 记录执行错误日志
     *
     * @param string $logs
     * @param string $file
     * @param int    $line
     */
    public static function error(string $logs, string $file, int $line)
    {
        echo 'ExecError: ', "{$file}({$line}) : {$logs}", PHP_EOL;
    }
}