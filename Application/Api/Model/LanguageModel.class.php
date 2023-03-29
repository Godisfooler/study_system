<?php

namespace Api\Model;
use Think\Model;

/**
 * 语言包加载
 */
class LanguageModel{
    
    public function getLanguageFileArray($fileName='',$languageType='zh-cn') {
        $Larray = array();
        switch($languageType)  {
            case 'zh-cn':
                $Larray = array_merge($Larray,include dirname(__FILE__).'/../Lang/zh-cn.php');
                if(!empty($fileName)) 
                    $Larray = array_merge($Larray,include dirname(__FILE__).'/../Lang/zh-cn/'.$fileName.'.php');
                break;
            case 'zh-tw':
                $Larray = array_merge($Larray,include dirname(__FILE__).'/../Lang/zh-tw.php');
                if(!empty($fileName))
                    $Larray = array_merge($Larray,include dirname(__FILE__).'/../Lang/zh-tw/'.$fileName.'.php');
                break;
            case 'en-us':
                $Larray = array_merge($Larray,include dirname(__FILE__).'/../Lang/en-us.php');
                if(!empty($fileName))
                    $Larray = array_merge($Larray,include dirname(__FILE__).'/../Lang/en-us/'.$fileName.'.php');
                break;
            default:
                break;
            
        }
        
        L($Larray);
        return $Larray;
    }
}
