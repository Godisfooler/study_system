<?php
namespace Home\Controller;

use Think\Controller;

class MemberController extends Controller
{
  
    public $username;
    public $password;
    public $uid = 1;
    

    //登录页面显示
    public function login(){
        $this->Display();
    }

    //注册页面显示
    public function regist(){
        $this->Display();
    }

    //注册成功提示页面
    public function loginSuccess(){
        $this->Display();
    }

    /**
     * 登录账号
     */
    public function loginAction($username = '',$password = ''){
        $this->username = $username;
        $this->password = $password;
        //判断账号密码是否为空
        if(!empty($this->username) || !empty($this->password)){
            
            $username = $this->username;
            $password = md5(sha1($this->password).C('DATA_AUTH_KEY'));
            
            $map['username'] = array('EQ',$username);
            $map['password'] = array('EQ',$password);
            
            $userInfo = M('ucenter_member')
                        ->where($map)
                        ->find();
            //验证账户密码是否正确
            if(empty($userInfo['id'])){
                $this->ajaxReturn(array('status'=>0,'code'=>'10007','msg'=>'账号不存在或者密码错误'));
            }

            $this->uid = $userInfo['id'];
            //记住登录信息
            $this->autoLogin($userInfo);
            $this->ajaxReturn(array('status'=>1,'code'=>'10000','msg'=>'登录成功','jumpUrl'=>U(C('AFTER_LOGIN_JUMP_URL'))));
            // if (is_login()) {
            //     //跳转到首页
            //     redirect(U(C('AFTER_LOGIN_JUMP_URL')));
            // }
        }else{
            $this->ajaxReturn(array('status'=>0,'code'=>'10001','msg'=>'参数错误','data'=>array()));
        }
    }
    
    //注册账号
    public function registAction(){
        //接受post参数
        $post = I('');
        $username = $post['username'];//用户名
        $password = $post['password'];//密码
        $iType = empty($post['user_type'])?0:$post['user_type'];//用户类型
        $iIsHeadman = empty($post['position_select'])?0:$post['position_select'];//是否为组长
        $iGroupId = empty($post['group_select'])?0:$post['group_select'];//小组id
        $sLike = empty($post['likes'])?"":$post['likes'];//兴趣爱好
        $sSkill = empty($post['skills'])?"":$post['skills'];//特长
        //判断用户名或密码是否为空
        if(empty($username) || empty($password)){
            $this->ajaxReturn(['status'=>0,'message'=>'请输入用户名或密码！']);
        }
        //sha1加密拼接配置加密参数，再取md5值
        $password = md5(sha1($password).C('DATA_AUTH_KEY'));

        //判断数据库是否存在同名账号
        if(M('ucenter_member')->where(['username'=>$username])->find()){
            $this->ajaxReturn(['status'=>0,'message'=>'用户名已存在！']);
        }else{
            //账号信息添加到数据库
            $saveData = [];
            $saveData['username'] = $username;
            $saveData['password'] = $password;
            $saveData['iType'] = $iType;
            $saveData['sLike'] = $sLike;
            $saveData['sSkill'] = $sSkill;
            $saveData['iIsHeadman'] = $iIsHeadman;
            $saveData['iGroupId'] = $iGroupId;
            $saveData['reg_time'] = time();
            $res = M('ucenter_member')->add($saveData);
            if($res){
                $this->ajaxReturn(['status'=>1,'message'=>'注册成功！','jumpUrl'=>U('Home/Member/login')]);
            }else{
                $this->ajaxReturn(['status'=>0,'message'=>'注册失败！']);
            }
        }
    }
    
     /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
        $this->redirect('Home/Member/login');
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){
        /* 记录登录SESSION */
        $auth = array(
            'uid'             => $user['id'],
            'username'        => $user['username'],
            'iType'           => $user['iType'],
            'iIsAdmin'        => $user['iIsAdmin'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

    }
}
