<?php

/**
 * 这里由开发者自定义伪静态规则,放在下面括号里面
 */
 

return array(


    // 内容模型搜索
    "search\/(.+).html"                                         =>	"search/index/rewrite/$1",
    // tag关键词库
    "tag\/(.+).html"                                           =>	"tag/index/name/$1",

    // 栏目列表(分页)
    "([a-z0-9]+)-list-(\d+).html"                               =>	"category/index/dir/$1/page/$2",
    // 栏目列表
    "([a-z0-9]+)-list.html"                                     =>	"category/index/dir/$1",
    // 内容页(分页)
    "([a-z0-9]+)-show-(\d+)-(\d+).html"                         =>	"show/index/id/$2/page/$3",
    // 内容页
    "([a-z0-9]+)-show-(\d+).html"                               =>	"show/index/id/$2",

);

