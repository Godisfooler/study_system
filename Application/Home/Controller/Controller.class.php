<?php
/**
 * API接口中心【用户  接口中心】
 * @author  onep2p<onep2p@163.com>
 *
 */
namespace Home\Controller;

use Think\Controller;
use Api\Model\CommonModel;
use Vendor\requester;

class IndexController extends MemberAccreditController
{
    public $common;
    
    /**
     * 首页
     */
    public function index($accredit='',$uid = ''){
        $this->Display();
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
        $this->Display();
    }
    
}
