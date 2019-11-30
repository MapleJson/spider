<?php

declare (strict_types = 1);

namespace app;

use spider\Big5;
use Workerman\Worker;
use spider\Log;

class BaseWorker
{
    protected $socket;

    protected $name = '';

    protected $count = 1;

    protected $reloadable = true;

    protected $stdoutFile = 'workerman.log';

    public function __construct()
    {
        Worker::$stdoutFile = root_path('log/' . $this->stdoutFile);
        $worker             = new Worker($this->socket);
        $worker->name       = $this->name;
        $worker->count      = $this->count;
        $worker->reloadable = $this->reloadable;

        $this->init($worker);
    }

    /**
     * 初始化workerman
     *
     * @param Worker $worker
     */
    public function init(Worker $worker)
    {
        $worker->onWorkerStart = function ($worker) { };

        $worker->onConnect = function ($connection) {
            Log::log('new connection from ip ' . $connection->getRemoteIp());
        };

        $worker->onMessage = function ($connection, $data) { };

        $worker->onWorkerReload = function ($worker) {
            foreach ($worker->connections as $connection) {
                $connection->send('worker reloading');
            }
        };

        $worker->onClose = function ($connection) {
            Log::info('connection closed', __FILE__, __LINE__);
        };

        $worker->onError = function ($connection, $code, $msg) {
            Log::error("error {$code} {$msg}", __FILE__, __LINE__);
        };
    }

    /**
     * 转换编码
     *
     * @param $content
     * @param $conf
     *
     * @return string
     */
    protected function convert($content, $conf)
    {
        if ($content) {
            // 处理编码
            if (empty($conf['charset']) || !in_array($conf['charset'], ['auto', 'utf-8', 'gbk'])) {
                $data['charset'] = 'auto';
            }
            // 检测编码
            if ($conf['charset'] == 'auto') {
                if (preg_match('/[;\s\'"]charset[=\'\s]+?big/i', $content)) {
                    $data['charset'] = 'big5';
                } elseif (preg_match('/[;\s\'"]charset[=\'"\s]+?gb/i', $content) || preg_match('/[;\s\'"]encoding[=\'"\s]+?gb/i', $content)) {
                    $data['charset'] = 'gbk';
                } elseif (mb_detect_encoding($content) != 'UTF-8') {
                    $data['charset'] = 'gbk';
                }
            }
            // 转换
            switch ($conf['charset']) {
                case 'gbk':
                    $content = mb_convert_encoding($content, 'UTF-8', 'GBK');
                    break;
                case 'big5':
                    $content = mb_convert_encoding($content, 'UTF-8', 'big-5');
                    $content = Big5::toUtf8($content);
                    break;
                case 'utf-16':
                    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16');
                default:
            }
            return $content;
        }
        return '';
    }
}