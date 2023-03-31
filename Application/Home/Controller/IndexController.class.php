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
        // $this->ajaxReturn(['status'=>1,'message'=>'保存成功！']);
    }
    
    //问题发布
    public function questionPublish(){
        //判断是否为老师，不是老师提示没权限
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1){
            // $this->error("无权限！");
        }
        $post = I('');//接收参数
        if(empty($post)){
            // $this->ajaxReturn(['status'=>0,'message'=>'参数错误！']);
        }
        $data = [];
        $data['sTitle'] = $post['title'];
        $data['sContent'] = $post['content'];
        // M('question_list')->add($data);
        $this->success("发布成功！","questionList.html");exit;
        echo '<script>alert("发布成功！");window.location.href="questionList.html";</script>';exit;
    }

    //问题列表
    public function questionList(){
        //数据库查询问题列表
        $list = M('question_list q')
        ->field('q.*,um.username as teacher')
        ->join('ucenter_member as um ON um.id=q.uid','LEFT')
        ->select();
        var_dump($list);exit;
        $this->assign('question_list',$list);//渲染到前端页面
        $this->Display();
    }

    //回复列表
    public function answerList(){
        $questionId = 1;
        // $questionId = I('questionId');
        //数据库查询用户回答列表
        $list = M('answer_list')->where(['pid'=>$questionId])->select();
        var_dump($list);exit;
        $this->assign('answer_list',$list);//渲染到前端页面
        $this->Display();
    }

     //学生列表，仅老师账号可见
     public function studentList(){
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1){
            $this->error("无权限！");
        }
        //数据库查询学生列表并统计学生回答次数
        $list = M('ucenter_member um')
        ->field('um.*, COUNT(b.uid) AS count')
        ->join('answer_list b ON b.uid = um.id','LEFT')
        ->group('um.id')
        ->where(['iType'=>0])
        ->select();
        var_dump($list); 
        $this->assign('answer_list',$list);//渲染到前端页面
        $this->Display();

    }
}
