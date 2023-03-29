<?php
/**
 * API接口中心【用户  接口中心】
 * @author  onep2p<onep2p@163.com>
 *
 */
namespace Home\Controller;

use Think\Controller;

class IndexController extends MemberAccreditController
{
    public $common;
    public $uid;
    
    /**
     * 首页
     */
    public function index($accredit='',$uid = ''){
        $this->Display();//显示对应html文件
    }

    //登录页面显示
    public function login(){
        $this->Display();
    }

    //注册页面显示
    public function regist(){
        $this->Display();
    }

    //个人信息页面
    public function personInfo(){
        //数据库查询用户信息
        $res = M('user_info')->where(['uid'=>$this->uid])->find();
        $this->assign('userInfo',$res);//渲染到前端页面
        $this->Display();
    }
    
}
