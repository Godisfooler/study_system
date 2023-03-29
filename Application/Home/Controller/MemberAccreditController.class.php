<?php
namespace Home\Controller;

use Think\Controller;
class MemberAccreditController extends Controller
{
    
    public $accreditTime = 86400;    //授权码有效时间   1天
    public $loginStatus = 1;         //授权状态   0:参数错误 ,1:授权码正确,2:授权时间已过期
    public $memberAccredit;      //授权码
    public $uid = 1;                 //用户id   验证账户是否过期
    
    public function _initialize() {
        $this->uid = 1;
        $accredit = $_SERVER['HTTP_LOGINTOKEN'];
        $accredit = 'd577bbde2984f4cb3f5def778712ab92';
        if(empty($accredit)) {
            $this->ajaxReturn(array('status'=>0,'code'=>'10001','msg'=>'未登陆！'));
        }

        //验证授权码
        $this->memberAccredit = $accredit;
        
        $this->checkAccredit();
        switch ($this->loginStatus){
            case 0:
                $this->ajaxReturn(array('status'=>0,'code'=>'10002','msg'=>'授权码不存在,请重新登录'));
                break;
            case 2:
                $this->ajaxReturn(array('status'=>0,'code'=>'10003','msg'=>'授权码已过期'));
                break;
            case 5:
                $this->ajaxReturn(array('status'=>0,'code'=>'10005','msg'=>'密码已被修改'));
                break;
            default:
                //验证账户是否被禁用
                $status = $this->checkStatus();
                if($status == 0){
                    $this->ajaxReturn(array('status'=>0,'code'=>'10006','msg'=>'账户已被禁用'));
                }
                
                //验证账户是否过期
                $days = $this->checkMember();
                if($days <= 0){
                    $this->ajaxReturn(array('status'=>0,'code'=>'10004','msg'=>'您的账户已过期，请联系我们并续费以便继续使用'));
                }
        }
        
        //控制器初始化
        if(method_exists($this,'_initialize_c'))
            $this->_initialize_c();
    }
    
    public function checkMember() {
        return A('Member')->checkMember();
    }
    
    public function checkStatus() {
        return A('Member')->checkStatus();
    }
    
    public function checkAccredit()
    {
        if(!empty($this->memberAccredit)){
            
            $map['sAccredit'] = $this->memberAccredit;
            
            $res = M('member_auth')
                    ->where($map)
                    ->find();
            if(!empty($res)){
                unset($map);
                $map['id'] = array('EQ',$res['uid']);
                $member = M('ucenter_member')
                            ->field("password,iSubAccount")
                            ->where($map)
                            ->find();
                if($member['password'] != $res['password']){
                    
                    $this->loginStatus = 5;
                    return ;
                    
                }
                
                $time = time();
                
                if($res['iBeginTime'] <= $time-$this->accreditTime){
                    $this->loginStatus = 2;
                    return ;
                }else{
                    define('__UID__',$res['uid']);
                    $this->loginStatus = 1;
                    return ;
                }
                
            }
        }
        
    }
    
    /**
     * 接口地址格式化
     * @return \think\response\Json
     * @author xj
     */
    public function getBaseUrl($url) {
        $match = pathinfo($url);
        
        $this->baseUrl = $match['dirname'];
        preg_match('/^(http[s]{0,1}:\/\/.+?)(\/|\?|$)/i', $url, $match);
        $this->hostUrl = $match[1];

    }
    
    /**
     * 接口调用函数
     * @return \think\response\Json
     * @author xj
     */
    function request_post($url = '',$post = array(),$ispost=true) {
    
        if (empty($url)) {
            return false;
        }
    
        $curlPost = '';
    
        $ch = curl_init();//初始化curl
    
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
    
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    
        if($ispost){  //post提交方式
            curl_setopt($ch, CURLOPT_POST, 1);
    
            if(is_array($post)){
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));//要提交的信息
            }else{
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);//要提交的信息
            }
        }
    
        $data = curl_exec($ch);
    
        //运行curl
        curl_close($ch);
    
        return $data;
    }
}
