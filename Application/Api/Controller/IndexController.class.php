<?php
/**
 * API接口中心【用户  接口中心】
 * @author  onep2p<onep2p@163.com>
 *
 */
namespace Api\Controller;

use Think\Controller;
use Api\Model\CommonModel;
use Vendor\requester;

class IndexController extends MemberAccreditController
{
    public $common;
    
    /**
     * 接口统一入口
     * @param string accredit 授权码
     * @param string url 请求的接口地址
     * msg   10002  分组不存在
     * msg   10003  分组code已更新
     * msg   10001  参数错误
     * @return \think\response\Json  param  参数  
     * @author xj
     */
    public function index($accredit='',$url = '',$param = array(),$uid = ''){
        if(!empty($url)){
            
            preg_match('/^(http[s]{0,1}:\/\/.+?)(\/|\?|$)/i', $url, $match);
            $hostUrl = $match[1];
            
            if(!empty($hostUrl)){
                $url = str_replace($hostUrl, C('API_IP'), $url);
            }
            
            $param = is_array($param) ? $param : json_decode($param,true);
            
            if(!empty($uid)){
                
                $code = M("corp_group")->field("sCompanyCode")->where('id="'.$uid.'"')->find();
                if(empty($code)) $this->ajaxReturn(array('status'=>0,'msg'=>'10002'));
                
                if($code['sCompanyCode'] != $param['sCompanyCode']) $this->ajaxReturn(array('status'=>0,'msg'=>'10003'));
                
            }
            
            $result = $this->request_post($url,$param);
            $result = is_array($result) ? json_encode($result) : $result ;
            echo $result;exit;
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>'10001'));
        }
    }

    public function login(){
        
    }
    

    
    
}
