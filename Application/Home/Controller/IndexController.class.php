<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller
{
    public $uid;
    public $userInfo;
    protected function _initialize()
    {
        if(is_login()){
	        $this->uid = is_login();
            $this->userInfo = M('ucenter_member')->find($this->uid);
            $this->userInfo['type_info'] = $this->userInfo['iType'] == 1 ? '老师':'';
            $this->userInfo['type_info'] = $this->userInfo['iType'] == 1 ? '老师':($this->userInfo['iType'] == 2?'':'学生');
            $this->userInfo['username_show'] = $this->userInfo['iType'] == 1 ? mb_substr($this->userInfo['realname'],0,1):$this->userInfo['realname'];
            $this->assign('userInfo',$this->userInfo);
        }else{
            $this->redirect('Home/Member/login');
        }
	}
    /**
     * 首页
     */
    public function index(){
        $list = M('student_msgs m')
        ->field('m.*,um.realname')
        ->join('ucenter_member as um ON um.id=m.uid','LEFT')
        ->where(['iTeacherId'=>$this->uid])
        ->order('m.iAddTime DESC')
        ->limit(5)
        ->select();
        foreach($list as &$l){
            // if(mb_strlen($l['sContent']) > 150){
            //     $l['sContent'] = mb_substr($l['sContent'],0,150).'...';
            // }
            $l['date'] = date("Y-m-d H:i",$l['iAddTime']);
        }
        $this->assign('reply_list',$list);//渲染到前端页面
        $this->assign('pageType','index');
        $this->Display();//显示对应html文件
    }

    //个人信息页面
    public function personInfo(){
        //数据库查询用户信息
        $userInfo = M('ucenter_member um')
        ->join('group_list as g ON um.iGroupId=g.id','LEFT')
        ->where(['um.id'=>$this->uid])
        ->find();
        $userInfo['iIsHeadman'] = $userInfo['iIsHeadman']>0?'是':'否';
        $userInfo['userType'] = $userInfo['iType'] == 2?'管理员':($userInfo['iType'] == 1?'老师':'学生');
        $userInfo['type_info'] = $userInfo['iType'] == 1 ? '老师':($userInfo['iType'] == 2?'':'学生');
        $userInfo['username_show'] = $userInfo['iType'] == 1 ? mb_substr($userInfo['realname'],0,1):$userInfo['realname'];
        $this->assign('userInfo',$userInfo);
        $this->assign('pageType','personInfo');
        $this->Display();
    }

    //个人信息保存
    public function savePersonInfo(){
        $data = [];
        $data['sLike'] = I('likes');
        $data['sSkill'] = I('skills');
        $data['realname'] = I('username');
        if(!empty(I('password'))){
            $password = md5(sha1(I('password')).C('DATA_AUTH_KEY'));
            $data['password'] = $password;
        }
        $result = M('ucenter_member')->where(['id'=>$this->uid])->save($data);
        if(!empty(I('password'))){
            session('user_auth', null);
            session('user_auth_sign', null);
            // $this->redirect('Home/Member/login');
            echo '<script>alert("密码已修改，请重新登录！");window.location.href="Home/Member/login";</script>';exit;
        }else{
            echo '<script>alert("保存成功");window.location.href="personInfo.html";</script>';exit;
        }
        // $this->ajaxReturn(['status'=>1,'message'=>'保存成功！']);
    }
    
    public function publish(){
        $this->assign('pageType','questionList');
        $this->Display();
    }
    //问题发布
    public function questionPublish(){
        //判断是否为老师，不是老师提示没权限
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1 && $userInfo['iIsAdmin'] != 1){
            $this->error("无权限！");
        }
        $post = I('');//接收参数
        $data = [];
        $data['sTitle'] = $post['title'];
        $data['sContent'] = $post['describe'];
        $data['iAddTime'] = time();
        $res = M('question_list')->add($data);
        if($res){
            $this->success("发布成功！","questionList.html");
        }
        // echo '<script>alert("发布成功！");window.location.href="questionList.html";</script>';exit;
    }

    //问题列表
    public function questionList(){
        //数据库查询问题列表
        $list = M('question_list q')
        ->field('q.*,um.username as teacher,COUNT(a.id) AS count')
        ->join('ucenter_member as um ON um.id=q.uid','LEFT')
        ->join('answer_list as a ON a.pid=q.id','LEFT')
        ->group('q.id')
        ->order('q.iAddTime DESC')
        ->select();
        foreach($list as &$l){
            if(mb_strlen($l['sContent']) > 150){
                $l['sContent'] = mb_substr($l['sContent'],0,150).'...';
            }
            $l['date'] = date("Y-m-d H:i",$l['iAddTime']);
        }
        $this->assign('question_list',$list);//渲染到前端页面
        $this->assign('pageType','questionList');
        $this->Display();
    }

     //学生列表，仅老师账号可见
     public function appraiseList(){
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1 && $this->userInfo['iIsAdmin'] != 1){
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
        $this->assign('pageType','appraiseList');
        $this->Display();
    }

    //问题详情
    public function questionDetail($questionId){
        $list = M('question_list q')
        ->field('q.*, COUNT(a.id) AS count')
        ->join('answer_list a ON a.pid = q.id','LEFT')
        ->where(['q.id'=>$questionId])
        ->find();
        $list['date'] = date("Y-m-d H:i",$list['iAddTime']);

        //加载该问题所有回复
        $answerList = M('answer_list a')
        ->field('a.*,um.username,f.sPath,f.sOriName')
        ->join('ucenter_member um ON a.uid = um.id','LEFT')
        ->join('file f ON a.iFileId = f.id','LEFT')
        ->where(['a.pid'=>$questionId])
        ->order('a.iAddTime DESC')
        ->select();
        foreach($answerList as &$answer){
            $answer['date'] = date("Y-m-d H:i",$answer['iAddTime']);
            $answer['appriseList'] = $this->getAppriseList($answer['id']);
        }
        $result = M('appraise_list a')
        ->join('answer_list w ON w.id = a.iAnswerId','LEFT')
        ->where(['w.pid'=>$questionId,'a.uid'=>$this->uid])
        ->find();
        $hasApprised = false;
        if($result){
            $hasApprised = true;
        }
        $this->assign('questionDetail',$list);
        $this->assign('hasApprised',$hasApprised);
        $this->assign('answerList',$answerList);
        $this->assign('pageType','questionList');
        $this->Display();
    }

    public function answerAppraise(){
        $post = I('');
        if(empty($post)){
            $this->ajaxReturn(['status'=>0,'msg'=>'请填入有效内容！']);
        }
        $data = [];
        $data['uid'] = $this->uid;
        $data['iAnswerId'] = $post['answerId'];
        $data['sMerit'] = $post['pros'];
        $data['sShortComing'] = $post['cons'];
        $data['sSuggestions'] = $post['suggestions'];
        $score = $post['score'];
        if($score > 10){
            $score = 10;
        }elseif($score < 1){
            $score = 1;
        }
        $data['iScore'] = $score;
        $data['sTrCode'] = md5($data['uid'].$data['iAnswerId'].$data['sMerit'].$data['sShortComing'].$post['suggestions']);
        if(M('appraise_list')->where(['sTrCode'=>$data['sTrCode']])->find()){
            $this->ajaxReturn(['status'=>0,'msg'=>'请勿提交重复内容！']);
        }
        $data['iAddTime'] = time();
        $res = M('appraise_list')->add($data);
        if($res){
            $data['date'] = date("Y-m-d H:i",$data['iAddTime']);
            $data['username'] = $this->userInfo['username'];
            $this->ajaxReturn(['status'=>1,'msg'=>'点评成功！','data'=>$data]);
        }else{
            $this->ajaxReturn(['status'=>0,'msg'=>'点评失败！','data'=>$data]);
        }
    }

    public function getAppriseList($id){
       $list = M('appraise_list a')
       ->field('a.*,um.username')
       ->join('ucenter_member um ON a.uid = um.id','LEFT')
       ->where(['iAnswerId'=>$id])
       ->order('a.iAddTime DESC')
       ->select();

       foreach($list as &$l){
          $l['date'] = date("Y-m-d H:i",$l['iAddTime']);
       }
       return $list;
    }

    public function saveReply(){
        $data = [];
        $config = C('DOWNLOAD_UPLOAD');
        $config['maxSize'] = 10*1024*1024;
        $upload = new \Think\Upload($config);// 实例化上传类
        // 上传单个文件
        if(!empty($_FILES['file'])){
            $info   =   $upload->uploadOne($_FILES['file']);
            $oriFileName = $_FILES['file']['name'];
        }
        if(!empty($info)){
            $fileData = [];
            $fileData['sOriName'] = $oriFileName;
            $savePath = $config['rootPath'].$info['savepath'];
            $fileData['sPath'] = $savePath.$info['savename'];
            $fileId = M('file')->add($fileData);
            $data['iFileId'] = $fileId;
        }elseif(empty(I('reply_text'))){
            $this->ajaxReturn(['status'=>0,'msg'=>'请输入回复内容']);
        }
        $data['pid'] = I('questionId');
        $data['sContent'] = I('reply_text');
        $data['uid'] = $this->uid;
        $data['iAddTime'] = time();
        $res = M('answer_list')->add($data);
        if($res){
            $this->ajaxReturn(['status'=>1,'msg'=>'回复成功']);
        }else{
            $this->ajaxReturn(['status'=>0,'msg'=>'回复失败']);
        }
    }

    //学生列表
    public function studentList(){
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1 && $userInfo['iIsAdmin'] != 1){
            $this->error("无权限！");
        }
        $studentList = M('ucenter_member um')
        ->field('um.*,COUNT(a.id) AS count')
        ->join('answer_list as a ON um.id=a.uid','LEFT')
        ->where(['um.iType'=>0])
        ->group('um.id')
        ->select();
        foreach($studentList as &$student){
            $student['iIsHeadman'] = $student['iIsHeadman']>0?'是':'否';
        }
        $this->assign('studentList',$studentList);
        $this->assign('pageType','studentList');
        $this->Display();
    }

    //学生点评
    public function studentApprise(){
        $uid = I('uid');
        $comment = I('comment');
        $res = M('ucenter_member')->where(['id'=>$uid])->save(['sApprise'=>$comment]);
        if($res){
            $this->ajaxReturn(['status'=>1,'评价成功！']);
        }else{
            $this->ajaxReturn(['status'=>0,'评价失败！']);
        }
    }

    //下载文件
    public function downloadById(){
        $fileId = I('fileId');
        $fileInfo = M('file')->find($fileId);
        $file = $fileInfo['sPath'];
        $fileName = $fileInfo['sOriName'];
        $this->download_file($file,$fileName);
    }

     //下载文件
     public function download_file($file='',$filleName=''){
        $fp=fopen($file,"r");
        $file_size=filesize($file);
        //下载的文件名特殊字符去除
        $filleName = str_replace(array('\\','/',':','*','?','"','<','>','|','~','#'), ' ', $filleName);
    
        //下载文件需要用到的头
        $ua = $_SERVER["HTTP_USER_AGENT"];
        
        
        if ($isiPhoneWechat = (preg_match("/iPhone OS/i", $ua) && preg_match("/MicroMessenger/i", $ua))) {
            $extension = pathinfo($file,PATHINFO_EXTENSION);
            if($extension === "docx") {
                $contentType = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
            }elseif($extension === "xlsx") {
                $contentType = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            }else{
                $contentType = "application/octet-stream";
            }
        }else{
            $isiPhoneWechat = false;
            $contentType = "application/octet-stream";
        }
        
        Header("Content-type: ".$contentType);
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:".$file_size);
        
        $encoded_filename = urlencode($filleName);
        $encoded_filename = str_replace("+", "%20", $encoded_filename);
        
        
        if (preg_match("/Firefox/i", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filleName . '"');
        } else if (preg_match("/rv:/i", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Edge/i", $ua)){
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '";filename*=utf-8\'\''.$encoded_filename);
        } else if (preg_match("/MSIE/i", $ua)){
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '";filename*=utf-8\'\''.$encoded_filename);
        } else if ($isiPhoneWechat){
            //
        }else{
            header('Content-Disposition: attachment; filename="' . $filleName . '";filename*=utf-8\'\''.$encoded_filename);
        }
    
        $buffer=51200;
        $file_count=0;
    
        //向浏览器返回数据
        while(!feof($fp) && $file_count<$file_size){
            $file_con=fread($fp,$buffer);
            $file_count+=$buffer;
            echo $file_con;
        }
        fclose($fp);
    }

    //管理员管理页面
    public function manage(){
        if($this->userInfo['iIsAdmin'] != 1){
            $this->error('无权限！');
        }
        $this->assign('pageType','manage');
        $this->Display();
    }

    public function manageUser(){
        if($this->userInfo['iIsAdmin'] != 1){
            $this->error('无权限！');
        }
        $userList = M('ucenter_member')->where(['iIsAdmin'=>0])->select();
        foreach($userList as &$user){
            $user['userType'] = $user['iType']>0?'老师':'学生';
            $user['regist_date'] = date("Y-m-d H:i",$user['reg_time']);
        }
        $this->assign('userList',$userList);
        $this->assign('pageType','manage');
        $this->Display();
    }

    //管理员编辑用户信息
    public function editUser($uid){
        if($this->userInfo['iIsAdmin'] != 1){
            $this->error('无权限！');
        }
        $list = M('group_list')->select();
        $this->assign('grouplist',$list);
        $userInfo = M('ucenter_member')->find($uid);
        $this->assign('userDetail',$userInfo);
        $this->assign('pageType','manage');
        $this->Display();
    }

    //编辑保存
    public function saveUser(){
        $post = I('');
        $uid = $post['uid'];
        $data = [];
        $data['realname'] = $post['realname'];
        $data['iType'] = $post['user_type'];
        $data['iGroupId'] = $post['group_select'];
        $data['iIsHeadman'] = $post['position_select'];
        $data['sLike'] = $post['likes'];
        $data['sSkill'] = $post['skills'];
        $res = M('ucenter_member')->where(['id'=>$uid])->save($data);
        if($res){
            $this->ajaxReturn(['status'=>1,'msg'=>'保存成功！','jumpUrl'=>'/Home/Index/manage.html']);
        }else{
            $this->ajaxReturn(['status'=>0,'msg'=>'保存失败！']);
        }
    }

    //学生达成评价
    public function studentReachApprise(){
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1 && $userInfo['iIsAdmin'] != 1){
            $this->error("无权限！");
        }
        $this->assign('pageType','studentList');
        $this->Display();
    }

    //联系老师
    public function contactTeacher(){
        $list = M('ucenter_member')->where(['iType'=>1])->select();
        $this->assign('teacher_list',$list);
        $this->assign('pageType','contactTeacher');
        $this->Display();
    }


    //留言保存
    public function saveMessage(){
        $data = [];
        $data['iTeacherId'] = I('teacher');
        $data['sMessage'] = I('message');
        $data['uid'] = $this->uid;
        $data['iAddTime'] = time();

        $res = M('student_msgs')->add($data);
        if($res){
            $this->success('提交成功！');
        }
    }

    //资源分享列表
    public function sharinglist(){
        $list = M('share_list a')
        ->field('a.*,um.username,f.sPath,f.sOriName')
        ->join('ucenter_member um ON a.uid = um.id','LEFT')
        ->join('file f ON a.iFileId = f.id','LEFT')
        ->order('a.iAddTime DESC')
        ->select();

        foreach($list as &$l){
            $l['date'] = date("Y-m-d H:i",$l['iAddTime']);
        }
        $this->assign('share_list',$list);
        $this->Display();
    }

    //资源分享发布
    public function sharePublish(){
        $this->Display();
    }

    //资源分享详情
    public function shareDetail($id){
        $list = M('share_list s')
        ->join('file f ON s.iFileId = f.id','LEFT')
        ->join('ucenter_member um ON s.uid = um.id','LEFT')
        ->where(['s.id'=>$id])
        ->find();
        $list['date'] = date("Y-m-d H:i",$list['iAddTime']);
        //加载所有回复
        $answerList = M('share_answer a')
        ->field('a.*,um.username')
        ->join('ucenter_member um ON a.uid = um.id','LEFT')
        ->where(['a.pid'=>$id])
        ->order('a.iAddTime DESC')
        ->select();
        foreach($answerList as &$answer){
            $answer['date'] = date("Y-m-d H:i",$answer['iAddTime']);
        }
        $this->assign('answerList',$answerList);
        $this->assign('shareDetail',$list);
        // $this->assign('pageType','questionList');
        $this->Display();
    }

    //资源分享保存
    public function shareSave(){
        $data = [];
        $config = C('DOWNLOAD_UPLOAD');
        $config['maxSize'] = 10*1024*1024;
        $upload = new \Think\Upload($config);// 实例化上传类
        // 上传单个文件
        if(!empty($_FILES['file'])){
            $info   =   $upload->uploadOne($_FILES['file']);
            $oriFileName = $_FILES['file']['name'];
        }
        if(!empty($info)){
            $fileData = [];
            $fileData['sOriName'] = $oriFileName;
            $savePath = $config['rootPath'].$info['savepath'];
            $fileData['sPath'] = $savePath.$info['savename'];
            $fileId = M('file')->add($fileData);
            $data['iFileId'] = $fileId;
        }
        $data['sTitle'] = I('title');
        $data['sContent'] = I('content');
        $data['uid'] = $this->uid;
        $data['iAddTime'] = time();
        $res = M('share_list')->add($data);
        if($res){
            $this->ajaxReturn(['status'=>1,'msg'=>'发布成功']);
        }else{
            $this->ajaxReturn(['status'=>0,'msg'=>'发布失败']);
        }
    }

    //资源分享回复
    public function saveShareReply(){
        $data['sReply'] = I('reply_text');
        $data['uid'] = $this->uid;
        $data['pid'] = I('shareid');
        $data['iAddTime'] = time();
        $res = M('share_answer')->add($data);
        if($res){
            $this->ajaxReturn(['status'=>1,'msg'=>'回复成功']);
        }else{
            $this->ajaxReturn(['status'=>0,'msg'=>'回复失败']);
        }
    }

    //资源分享情况统计
    public function sharingStatistics(){
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1 && $userInfo['iIsAdmin'] != 1){
            $this->error("无权限！");
        }
        $studentList = M('ucenter_member um')
        ->field('um.*,COUNT(a.id) AS count,g.sName')
        ->join('share_list as a ON um.id=a.uid','LEFT')
        ->join('group_list as g ON um.iGroupId=g.id','LEFT')
        ->where(['um.iType'=>0])
        ->group('um.id')
        ->select();
        foreach($studentList as &$student){
            $student['iIsHeadman'] = $student['iIsHeadman']>0?'是':'否';
        }
        array_multisort(array_column($studentList,'count'), SORT_DESC, $studentList);
        $this->assign('studentList',$studentList);
        $this->assign('pageType','studentList');
        $this->Display();
    }

    //问答情况统计
    public function questionStatistics($gid = ''){
        $map = [];
        $map['um.iType'] = 0;
        if(!empty($gid)){
            $map['um.iGroupId'] = $gid;
        }
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1 && $userInfo['iIsAdmin'] != 1){
            $this->error("无权限！");
        }
        $studentList = M('ucenter_member um')
        ->field('um.*,COUNT(a.id) AS count')
        ->join('answer_list as a ON um.id=a.uid','LEFT')
        ->where($map)
        ->group('um.id')
        ->select();

        $scores = $this->getAnswerAppraise();
        foreach($studentList as &$student){   
            $student['teacher_average'] = is_null($scores[$student['id']][1])?0:$scores[$student['id']][1];
            $student['group_average'] = is_null($scores[$student['id']][2])?0:$scores[$student['id']][2];
            $student['all_average'] = is_null($scores[$student['id']]['all'])?0:$scores[$student['id']]['all'];
        }
        array_multisort(array_column($studentList,'all_average'), SORT_DESC, $studentList);
        $this->assign('studentList',$studentList);
        $this->assign('pageType','studentList');
        $this->Display();
    }

    //获取平均分
    public function getAnswerAppraise($gid = ''){
       $map = [];
       $map['um.iType'] = ['GT',0];
       if(!empty($gid)){
           $map['um.iGroupId'] = $gid;
       }
       $list = M('appraise_list a')
       ->field('a.*,um.realname,um.iType,w.uid as puid')
       ->join('answer_list as w ON w.id=a.iAnswerId','LEFT')
       ->join('ucenter_member as um ON um.id=a.uid','LEFT')
       ->where($map)
       ->select();

       $scoreArr = [];
       foreach($list as $l){
           $scoreArr[$l['puid']][$l['iType']][] = $l['iScore'];
       }
       $averageArr = [];
       foreach($scoreArr as $uid=>$arr){
            foreach($arr as $type=>$val){
                $average = array_sum($val)/count($val);
                $average = is_int($average)?$average:number_format($average,2);
                $averageArr[$uid][$type] = $average;
            }
            $allAverage = $averageArr[$uid][1]*0.7 + $averageArr[$uid][2]*0.3;
            $averageArr[$uid]['all'] = $allAverage>0?number_format($allAverage,2):$allAverage;
       }

       return $averageArr;
    }

    //小组评分排行
    public function grouplist(){
       $groups = M('group_list')->select();
       foreach($groups as &$group){
          $memberCount = M('ucenter_member')->where(['iGroupId'=>$group['id']])->count();
          $group['member_count'] = $memberCount;
          $scores = $this->getAnswerAppraise($group['id']);
          $allScore = 0;
          foreach($scores as $score){
            $allScore += $score['all'];
          }
          $group['average'] = number_format($allScore/$memberCount,2);
       }
       array_multisort(array_column($groups,'average'), SORT_DESC, $groups);

       $this->assign('groups',$groups);
       $this->assign('pageType','studentList');
       $this->Display();

    }

    //分组管理
    public function groupManage(){
        $groups = M('group_list')->select();
        foreach($groups as &$group){
          $memberCount = M('ucenter_member')->where(['iGroupId'=>$group['id']])->count();
          $group['member_count'] = $memberCount;
        }
        $this->assign('groups',$groups);
        $this->Display();
    }

    //分组保存
    public function saveGroup(){
        $id = I('id');
        $name = I('name');
        $data['sName'] = $name;
        if(!empty($id)){
           $res = M('group_list')->where(['id'=>$id])->save($data);
        }else{
           $res = M('group_list')->add($data);
        }
        if($res){
            $this->ajaxReturn(['status'=>1,'msg'=>'保存成功']);
        }
    }

    //删除分组
    public function deleteGroup(){
        $gid = I('id');
        if(!empty($gid)){
            $res = M('group_list')->delete($gid);
         }

         if($res){
             $this->ajaxReturn(['status'=>1,'msg'=>'删除成功！']);
         }
    }

    //问题管理
    public function manageQuestion(){
        //数据库查询问题列表
        $list = M('question_list q')
        ->field('q.*,um.realname')
        ->join('ucenter_member as um ON um.id=q.uid','LEFT')
        ->group('q.id')
        ->order('q.iAddTime DESC')
        ->select();
        foreach($list as &$l){
            if(mb_strlen($l['sContent']) > 150){
                $l['sContent'] = mb_substr($l['sContent'],0,150).'...';
            }
            $l['date'] = date("Y-m-d H:i",$l['iAddTime']);
        }
        $this->assign('question_list',$list);//渲染到前端页面
        $this->assign('pageType','manage');
        $this->Display();
    }

    //问题删除
    public function deleteQuestion(){
        $id = I('id');
        if(!empty($id)){
            $res = M('question_list')->delete($id);
        }
        if($res){
            $this->ajaxReturn(['status'=>1,'msg'=>'删除成功！']);
        }
    }

    //辩论管理
    public function manageArgue(){
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1 && $userInfo['iIsAdmin'] != 1){
            $this->error("无权限！");
        }
        $list = M('argue_list a')
        ->order('a.iAddTime DESC')
        ->select();
        foreach($list as &$l){
            $l['date'] = date("Y-m-d H:i",$l['iAddTime']);
         }
        $this->assign('arguelist',$list);
        $this->assign('pageType','studentList');
        $this->Display();
        $this->assign('pageType','manage');
        $this->Display();
    }

     //辩论删除
     public function deleteArgue(){
        $id = I('id');
        if(!empty($id)){
            $res = M('argue_list')->delete($id);
        }

        if($res){
            $this->ajaxReturn(['status'=>1,'msg'=>'删除成功！']);
        }
    }
}