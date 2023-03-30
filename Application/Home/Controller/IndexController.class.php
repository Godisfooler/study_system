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
            $userInfo = M('ucenter_member')->find($this->uid);
            $this->assign('userInfo',$userInfo);
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
        $userInfo = M('ucenter_member')->find($this->uid);
        $userInfo['iIsHeadman'] = $userInfo['iIsHeadman']>0?'是':'否';
        $this->assign('userInfo',$userInfo);
        $this->Display();
    }

    //个人信息保存
    public function savePersonInfo(){
        $data = [];
        $data['sLike'] = I('likes');
        $data['sSkill'] = I('skills');

        $result = M('ucenter_member')->where(['id'=>$this->uid])->save($data);
        echo '<script>alert("保存成功");window.location.href="personInfo.html";</script>';exit;
        $this->redirect('Home/Index/personInfo');
        $this->ajaxReturn(['status'=>1,'message'=>'保存成功！']);
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
