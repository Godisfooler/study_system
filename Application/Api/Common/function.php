<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

function formatCompanyCode($code) {
    if(empty($code)) {
        return $code;
    }
    if(strlen($code) == 5) {
        $code = $code.'.HK';
    }else{
        if($code[0] == '6') {
            $code = $code.'.SH';
        }else{
            $code = $code.'.SZ';
        }
    }

    return $code;
}

function autoWholeDate($timeF,$dayF,$time) {
    if(date('H:i',$time) == '00:00') {
        return date($dayF,$time);
    }else{
        return date($timeF,$time);
    }
}