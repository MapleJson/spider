<?php

declare (strict_types = 1);

namespace app\traits;

trait BookCommon
{
    /**
     * 采集
     *
     * @param string|array $link
     * @param array        $config
     *
     * @return mixed
     */
    public function request($link, array $config)
    {
        // 采集根地址
        $url = $this->parseUrl($config['baseUrl'], $link);
        //Log::log($url);
        $ip = '220.181.108.' . rand(1, 255);
        // 采集页面html文档
        $html = curl()->get($url, [
                'headers' => [
                    'X-FORWARDED-FOR' => $ip,
                    'CLIENT-IP'       => $ip,
                ]
            ]
        )->body();

        // 转换为utf-8
        return $this->convert($html, $config);
    }

    /**
     * 根据正则批量获取
     *
     * @param mixed  $preg    正则
     * @param string $content 源内容
     *
     * @return array|bool
     */
    public function getMatchAll(string $preg, $content)
    {
        if (!$this->isReg($preg)) return [];

        $rule = '{' . $preg . '}';

        $match = [];

        preg_match_all($rule, $content, $match);

        if (is_array($match)) {
            unset($match[0]);
            return array_values($match);
        }

        return [];
    }

    /**
     * 根据正则获取指定数据 单个
     *
     * @param string $preg    正则
     * @param string $content 源内容
     *
     * @return bool|string
     */
    public function getMatch($preg, $content)
    {
        if (!$this->isReg($preg)) return $preg;
        $rule = '{' . $preg . '}';
        preg_match($rule, $content, $match);

        if (empty($match[1])) {
            return '';
        }

        return $match[1];
    }

    /**
     * 是否是正则表达式
     *
     * @param string $preg
     *
     * @return bool
     */
    public function isReg(string $preg)
    {
        return (strpos($preg, ')') !== false || strpos($preg, '(') !== false);
    }

    /**
     * 预处理url
     *
     * @param string       $url
     * @param string|array $path
     *
     * @return string
     */
    public function parseUrl(string $url, $path)
    {
        if (is_array($path)) {
            $path = $path['info'] . $path['ext'];
        }
        return rtrim($url, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

}