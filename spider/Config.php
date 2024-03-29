<?php

declare (strict_types = 1);

namespace spider;

class Config
{
    /**
     * 缓存存储变量
     *
     * @var array
     */
    protected static $config = [];

    /**
     * 配置文件目录
     * @var string
     */
    protected $path;

    /**
     * 配置文件后缀
     * @var string
     */
    protected $ext;

    /**
     * 构造方法
     *
     * @param string|null $path
     * @param string      $ext
     *
     * @access public
     */
    public function __construct(string $path = null, string $ext = '.php')
    {
        $this->path = $path ?: config_path();
        $this->ext  = $ext;
        if (empty(self::$config)) {
            $files = [];

            if (is_dir(config_path())) {
                $files = glob(config_path() . '*' . $this->ext);
            }

            foreach ($files as $file) {
                $this->load($file, pathinfo($file, PATHINFO_FILENAME));
            }
        }
    }

    /**
     * 加载配置文件（多种格式）
     * @access public
     *
     * @param  string $file 配置文件名
     * @param  string $name 一级配置名
     *
     * @return array
     */
    protected function load(string $file, string $name = ''): array
    {
        if (is_file($file)) {
            $filename = $file;
        } elseif (is_file($this->path . $file . $this->ext)) {
            $filename = $this->path . $file . $this->ext;
        }

        if (isset($filename)) {
            return $this->parse($filename, $name);
        }

        return self::$config;
    }

    /**
     * 解析配置文件
     * @access public
     *
     * @param  string $file 配置文件名
     * @param  string $name 一级配置名
     *
     * @return array
     */
    protected function parse(string $file, string $name): array
    {
        $type   = pathinfo($file, PATHINFO_EXTENSION);
        $config = [];
        switch ($type) {
            case 'php':
                $config = include $file;
                break;
            case 'yml':
            case 'yaml':
                if (function_exists('yaml_parse_file')) {
                    $config = yaml_parse_file($file);
                }
                break;
            case 'ini':
                $config = parse_ini_file($file, true, INI_SCANNER_TYPED) ?: [];
                break;
            case 'json':
                $config = json_decode(file_get_contents($file), true);
                break;
        }

        return is_array($config) ? $this->set($config, strtolower($name)) : [];
    }

    /**
     * 检测配置是否存在
     * @access public
     *
     * @param  string $name 配置参数名（支持多级配置 .号分割）
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return !is_null($this->get($name));
    }

    /**
     * 获取一级配置
     * @access protected
     *
     * @param  string $name 一级配置名
     *
     * @return array
     */
    protected function pull(string $name): array
    {
        $name = strtolower($name);

        return self::$config[$name] ?? [];
    }

    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     *
     * @param  string $name    配置参数名（支持多级配置 .号分割）
     * @param  mixed  $default 默认值
     *
     * @return mixed
     */
    public function get(string $name = null, $default = null)
    {
        // 无参数时获取所有
        if (empty($name)) {
            return self::$config;
        }

        if (false === strpos($name, '.')) {
            return $this->pull($name);
        }

        $name    = explode('.', $name);
        $name[0] = strtolower($name[0]);
        $config  = self::$config;

        // 按.拆分成多维数组进行判断
        foreach ($name as $val) {
            if (isset($config[$val])) {
                $config = $config[$val];
            } else {
                return $default;
            }
        }

        return $config;
    }

    /**
     * 设置配置参数 name为数组则为批量设置
     * @access public
     *
     * @param  array  $config 配置参数
     * @param  string $name   配置名
     *
     * @return array
     */
    public function set(array $config, string $name = null): array
    {
        if (!empty($name)) {
            if (isset(self::$config[$name])) {
                $result = array_merge(self::$config[$name], $config);
            } else {
                $result = $config;
            }

            self::$config[$name] = $result;
        } else {
            $result = self::$config = array_merge(self::$config, array_change_key_case($config));
        }

        return $result;
    }

    public static function instance()
    {
        return new self();
    }

}