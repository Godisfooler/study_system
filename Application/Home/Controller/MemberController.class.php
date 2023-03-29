<?php
/**
 * API接口中心【用户  接口中心】
 * @author  onep2p<onep2p@163.com>
 *
 */
namespace Home\Controller;

use Api\Model\CommonModel;
use Think\Controller;

class MemberController extends Controller
{
  
    public $username;
    public $password;
    public $uid = 1;
    public $common;
    
    /**
     * 会员登录
     * @param string username 会员名称
     * @return \think\response\Json
     * @author xj
     */
    public function login($username = '',$password = ''){
        $this->username = $username;
        $this->password = $password;
        
        if(!empty($this->username) || !empty($this->password)){
            
            $this->common = new CommonModel();
            $username = $this->username;
            $password = md5(sha1($this->password).C('DATA_AUTH_KEY'));
            
            $map['_string'] = '(  `type` = 1 AND `username` = \''.$username.'\' ) OR (  `type` = 2 AND `email` = \''.$username.'\' ) OR (  `type` = 3 AND `mobile` = \''.$username.'\' )';
            $map['password'] = array('EQ',$password);
            
            $userInfo = M('ucenter_member')
                        ->where($map)
                        ->find();
            //验证账户密码是否正确
            if(empty($userInfo['id'])){
                $this->ajaxReturn(array('status'=>0,'code'=>'10007','msg'=>'账号不存在或者密码错误'));
            }

            $this->uid = $userInfo['id'];
            
            //验证账户是否被禁用
            $status = $this->checkStatus($userInfo);
            if($status == 0){
                $this->ajaxReturn(array('status'=>0,'code'=>'10006','msg'=>'账户已被禁用'));
            }
            
            $time = time();
            
            //验证账户是否到期
            $days = $this->checkMember($userInfo);
            if($days <= 0){
                $this->ajaxReturn(array('status'=>0,'code'=>'10004','msg'=>'您的账户已过期，请联系我们并续费以便继续使用'));
            }
            
            $map = array();
            
            $map['uid'] = array('EQ',$this->uid);
            
            $res = M("member_auth")
                        ->where($map)
                        ->find();
            
            $data = array();
            
            $data['uid'] = $this->uid;
            $data['password'] = $password;
            $data['iBeginTime'] = $time;
            $data['sAccredit'] = md5($username.$password.$time);
            
            if(empty($res)) $data['iCreateTime'] = time();
            
            $id = $this->common->addData('member_auth',$data,'uid="'.$data['uid'].'"');
            
            if($id){
                $map = [];
                $map['uid'] = array('EQ',$this->uid);
                
                $info = M('ucenter_company')
                ->field("sCompanyName,sArea,uid,sEmail,sContect,sLogoUrl,sLanguage")
                ->where($map)
                ->find();
                
                // $map = [];
                // $map['uid'] = array('EQ',$this->uid);
                // $res = M('user_info')
                // ->field("sLanguage")
                // ->where($map)
                // ->find();
                
                $info['sLanguage'] = empty($info['sLanguage']) ? 'zh-cn' : $info['sLanguage'] ;
                $info['sEmail'] = empty($info['sEmail']) ? '' : $info['sEmail'] ;
                $data = array(
                    'accredit' => $data['sAccredit'],
                    'beginTime' => $time,
                    'days' => $days,
                    // 'sCompanyName' => $info['sCompanyName'],
                    // 'sArea' => $info['sArea'],
                    'uid' => $this->uid,
                    'sEmail' => $info['sEmail'],
                    'username' => $userInfo['username'],
                    'sLanguage' => $info['sLanguage'],
                    'sLogoUrl' => IpStatic($info['sLogoUrl']),
                    // 'auth' => intval($userInfo['auth'])
                );
                $this->ajaxReturn(array('status'=>1,'code'=>'10000','msg'=>'请求成功','data'=>$data));
            }else{
                $this->ajaxReturn(array('status'=>0,'code'=>'10001','msg'=>'请求失败','data'=>array()));
            }
            
        }else{
            $this->ajaxReturn(array('status'=>0,'code'=>'10001','msg'=>'参数错误','data'=>array()));
        }
    }
    
    
    /**
     * 验证账户是否过期
     * @return \think\response\Json
     * @author xj
     */
    public function checkMember($userInfo=null){
        if(is_null($userInfo)){
            $map['id'] = array("EQ",$this->uid);
            $userInfo = M('ucenter_member')
            ->where($map)
            ->find();
        }
        //验证账户是否到期
        $days = 0;
        $time = time();
    
        if($userInfo['iExpirationTime'] > $time){  //在14天以内    && $userInfo['iExpirationTime'] - $time <= 1209600
            $days = ceil(($userInfo['iExpirationTime']-$time)/3600/24); //四舍五入 获得剩余天数
        }
    
        return $days;
    }
    
    
    /**
     * 验证账户是否被禁用
     * @return \think\response\Json
     * @author xj
     */
    public function checkStatus($userInfo=null){
        if(is_null($userInfo)){
            $map['id'] = array("EQ",$this->uid);
    
            $userInfo = M('ucenter_member')
            ->field('status')
            ->where($map)
            ->find();
        }
        return $userInfo['status'];
    }
    
}
