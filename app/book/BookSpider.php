<?php

declare (strict_types = 1);

namespace app\book;

use app\BaseWorker;
use app\book\model\AddOnArticle;
use app\book\model\Archives;
use app\book\model\ArcType;
use app\traits\BookCommon;
use spider\Log;
use Workerman\Worker;

class BookSpider extends BaseWorker
{
    use BookCommon;

    protected $socket = 'http://0.0.0.0:9951';

    protected $name = 'e-book-spider';

    protected $stdoutFile = 'e-book-spider.log';

    public function init(Worker $worker)
    {
        $worker->onWorkerStart = function ($worker) {
            foreach (config('books') as $config) {
                $this->spiderEBook($config);
            }
        };
    }

    private function spiderEBook($config)
    {
        $bookLinks = [];
        // 采集小说章节地址
        foreach ($config['links'] as $key => $link) {
            // 采集页面html文档
            $content = $this->request($link, $config);
            // 正则
            $rule = $config['list']['rule'];
            if ($link === 'paihangbang/') {
                $rule = 'li>(\d*)<a href="https://www.biqutxt.com/(.*?)/"';
            }
            // 得到正则匹配到的数据(所有小说的章节列表页地址)
            $bookLinks = array_unique(array_merge($bookLinks, $this->getMatchAll($rule, $content)[1]));
        }

        // 采集章节
        // 章节开始需要写入数据库
        // 将$bookLink作为每一本小说的唯一标识
        // 最后根据章节采每一章的内容
        foreach ($bookLinks as $bookLink) {
            $content = $this->request($bookLink, $config);

            $title    = $this->getMatch($config['cover']['title'], $content);
            $author   = $this->getMatch($config['cover']['author'], $content);
            $lastTime = $this->getMatch($config['cover']['lastTime'], $content);
            $info     = str_replace('<p>', '', $this->getMatch($config['cover']['info'], $content));
            $img      = $this->getMatch($config['cover']['img'], $content);

            if (!$arcId = ArcType::getBookName($title)) {
                $arcId = ArcType::save(['typename' => $title]);
            }

            list($chapterLink, $chapterName) = $this->getMatchAll($config['chapter']['rule'], $content);

            // 存入小说内容
            foreach ($chapterName as $key => $name) {
                if (!$chaId = Archives::getChapter($arcId, $name)) {
                    $chaId = Archives::save([
                        'typeid'      => $arcId,
                        'title'       => $name,
                        'shorttitle'  => $name,
                        'writer'      => $author,
                        'litpic'      => $img,
                        'pubdate'     => $lastTime,
                        'description' => $info,
                    ]);

                    $content = $this->request(['info' => "{$bookLink}/$chapterLink[$key]", 'ext' => '.html'], $config);
                    $content = $this->getMatch($config['content']['rule'], $content);

                    // 默认有章节信息就有文章内容
                    // 如果有问题则将此代码移至if判断外面，并放开注释
                    // if (!AddOnArticle::getArticle($chaId)) {
                    AddOnArticle::save([
                        'aid' => $chaId,
                        'typeid' => $arcId,
                        'body'   => $content,
                    ]);
                    // }
                }

            }
        }
        Log::log('spider success');
    }

}