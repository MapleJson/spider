<?php

declare (strict_types = 1);

use spider\Config;
use spider\DB;

if (!function_exists('config')) {
    /**
     * 获取和设置配置参数
     *
     * @param null $key
     * @param null $default
     *
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return Config::instance();
        }

        if (is_array($key)) {
            return Config::instance()->set($key);
        }

        return Config::instance()->get($key, $default);
    }
}

if (!function_exists('curl')) {
    /**
     * 获取一个curl实例
     *
     * @return \spider\Curl|null
     */
    function curl()
    {
        return \spider\Curl::instance();
    }
}

if (!function_exists('root_path')) {
    /**
     * 获取项目根目录
     *
     * @param string $path
     *
     * @return string
     */
    function root_path($path = '')
    {
        if (empty($path)) {
            return dirname(__DIR__) . DIRECTORY_SEPARATOR;
        }

        $rootPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . $path;

        if (!is_readable($rootPath)) {
            if (false === strpos($path, '.')) {
                is_file($rootPath) or mkdir($rootPath, 0700);
            }
            is_file($rootPath) or touch($rootPath);
        }

        return is_dir($rootPath) ? $rootPath . DIRECTORY_SEPARATOR : $rootPath;
    }
}

if (!function_exists('config_path')) {
    /**
     * 获取应用配置目录
     *
     * @return string
     */
    function config_path()
    {
        return root_path('config');
    }
}