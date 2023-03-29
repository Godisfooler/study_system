<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller
{
    public $uid;
    
    protected function _initialize()
    {
        if(is_login()){
	        $this->uid = is_login();
        }else{
            $this->redirect('Home/Member/login');
        }
	}
    /**
     * 首页
     */
    public function index($accredit='',$uid = ''){
        $this->Display();//显示对应html文件
    }

    //个人信息页面
    public function personInfo(){
        //数据库查询用户信息
        $res = M('user_info')->where(['uid'=>$this->uid])->find();
        $this->assign('userInfo',$res);//渲染到前端页面
        $this->Display();
    }
    
    //问题列表
    public function questionList(){
        //数据库查询问题列表
        $list = M('question_list')->select();

        $this->assign('question_list',$list);//渲染到前端页面
        $this->Display();
    }

    //回复列表
    public function answerList(){
        //数据库查询用户回答列表
        $list = M('answer_list')->select();

        $this->assign('answer_list',$list);//渲染到前端页面
        $this->Display();
    }
}
