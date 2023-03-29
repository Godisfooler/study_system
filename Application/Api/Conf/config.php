<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.thinkphp.cn>
// +----------------------------------------------------------------------

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__IMG__' => __ROOT__ . '/Application/Api'   . '/Static/images',
        '__CSS__' => __ROOT__ . '/Application/Api'   . '/Static/css',
        '__JS__' => __ROOT__ . '/Application/Api'  . '/Static/js',
        '__STATIC_VERSION__' => STATIC_VERSION
    ),

);
