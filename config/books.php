<?php

return [
    'biqutxt' => [
        'links'   => [
            'xuanhuanxiaoshuo/',
            'xiuzhenxiaoshuo/',
            'dushixiaoshuo/',
            'lishixiaoshuo/',
            'wangyouxiaoshuo/',
            'kehuanxiaoshuo/',
            'qitaxiaoshuo/',
            'paihangbang/',
            'wanben/1_1',
        ],
        'baseUrl' => 'https://www.biqutxt.com/',
        'charset' => 'gbk',
        //列表页
        'list'    => [
            //规则
            'rule'    => 'span class="s2"([\>《\<]*?)a href="https://www.biqutxt.com/(.*?)/"',
            //列表页数量
            'pages'   => 50,
            'pageUrl' => '',
        ],
        //小说封面
        'cover'   => [
            //规则
            'title'    => '<h1>(.*?)</h1>',
            'author'   => '<p>作&nbsp;&nbsp;&nbsp;&nbsp;者：(.*?)</p>',
            'lastTime' => '<p>最后更新：(.*?)</p>',
            'info'     => 'id="intro">([\\s\\S]*?)</p>',
            'img'      => 'id="fmimg"><img src="(.*?)"',
        ],
        //章节列表页
        'chapter' => [
            //规则
            'rule'    => '<li><a href="(.*?).html">(.*?)</a></li>',
            'pageUrl' => '',
        ],
        //详情页
        'content' => [
            'rule' => '<div id="content">([\\s\\S]*?)</div>'
        ],
    ],
//    'wx999'   => [
//        'baseUrl' => 'http://www.999wx.com/',
//        'charset' => 'gb2312',
//        'links'   => [7, 13, 17, 19, 15, 25, 16, 76, 18],
//        //列表页
//        'list'    => [
//            //规则
//            'rule'    => 'span class="s2"([\>《\<]*?)a href="https://www.biqutxt.com/(.*?)/"',
//            //列表页数量
//            'pages'   => 50,
//            'pageUrl' => '',
//        ],
//        //小说封面
//        'cover'   => [
//            //规则
//            'title'    => '<h1>(.*?)</h1>',
//            'author'   => '<p>作&nbsp;&nbsp;&nbsp;&nbsp;者：(.*?)</p>',
//            'lastTime' => '<p>最后更新：(.*?)</p>',
//            'info'     => 'id="intro">([\\s\\S]*?)</p>',
//            'img'      => 'id="fmimg"><img src="(.*?)"',
//        ],
//        //章节列表页
//        'chapter' => [
//            //规则
//            'rule'    => '<li><a href="(.*?).html">(.*?)</a></li>',
//            'pageUrl' => '',
//        ],
//        //详情页
//        'content' => [
//            'rule' => '<div id="content">([\\s\\S]*?)</div>'
//        ],
//    ],
];