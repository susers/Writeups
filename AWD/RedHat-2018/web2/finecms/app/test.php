<?php
// 此文件用于开发插件用
return array(


    'menu' => array(
        'test/index' => array('测试插件', 'fa fa-user')
    ),

    'cache' => array(
        array(
            'url' => dr_url('test/cache', array('admin' => 1)),
            'name' => fc_lang('%s缓存', fc_lang('测试')),
        ),

    )

);