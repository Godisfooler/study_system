<?php
namespace Home\Controller;
use Think\Controller;

class ArgueController extends Controller
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

    
    public function publish(){
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1 && $userInfo['iIsAdmin'] != 1){
            $this->error("无权限！");
        }
        // $this->assign('pageType','questionList');
        $this->Display();
    }

    //辩论发布
    public function arguePublish(){
        //判断是否为老师，不是老师提示没权限
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1 && $userInfo['iIsAdmin'] != 1){
            $this->error("无权限！");
        }
        $post = I('');//接收参数
        $data = [];
        $data['uid'] = $this->uid;
        $data['sTitle'] = $post['title'];
        $data['sSupport'] = $post['support'];
        $data['sAgainst'] = $post['against'];
        $data['sDescribe'] = $post['describe'];
        $data['iAddTime'] = time();
        $res = M('argue_list')->add($data);
        if($res){
            $this->success("发布成功！","argueList.html");
        }
        // echo '<script>alert("发布成功！");window.location.href="questionList.html";</script>';exit;
    }

    //辩论列表
    public function argueList(){
        //数据库查询辩论列表
        $list = M('argue_list a')
        // ->field('a.*,um.username as teacher')
        // ->join('ucenter_member as um ON um.id=a.uid','LEFT')
        // ->join('answer_list as a ON a.pid=q.id','LEFT')
        // ->group('a.id')
        ->order('a.iAddTime DESC')
        ->select();
        foreach($list as &$l){
            if(mb_strlen($l['sContent']) > 150){
                $l['sContent'] = mb_substr($l['sContent'],0,150).'...';
            }
            $l['date'] = date("Y-m-d H:i",$l['iAddTime']);
        }
        $this->assign('argue_list',$list);//渲染到前端页面
        $this->assign('pageType','argue_list');
        $this->Display();
    }

    //问题详情
    public function argueDetail($id){
        $detail = M('argue_list a')
        ->field('a.*, COUNT(s.id) AS count')
        ->join('argue_answer s ON s.pid = a.id','LEFT')
        ->where(['a.id'=>$id])
        ->find();
        $detail['date'] = date("Y-m-d H:i",$detail['iAddTime']);

        //加载该问题所有赞成回复
        $supportList = M('argue_answer a')
        ->field('a.*,um.realname')
        ->join('ucenter_member um ON a.uid = um.id','LEFT')
        ->where(['a.pid'=>$id,'a.iType'=>1])
        ->order('a.iAddTime DESC')
        ->select();
        foreach($supportList as &$list){
            $list['date'] = date("Y-m-d H:i",$list['iAddTime']);
        }
        //加载该辩论下所有反对回复
        $againstList = M('argue_answer a')
        ->field('a.*,um.realname')
        ->join('ucenter_member um ON a.uid = um.id','LEFT')
        ->where(['a.pid'=>$id,'a.iType'=>0])
        ->order('a.iAddTime DESC')
        ->select();
        foreach($againstList as &$list){
            $list['date'] = date("Y-m-d H:i",$list['iAddTime']);
        }
        $this->assign('questionDetail',$detail);
        $this->assign('supportList',$supportList);
        $this->assign('againstList',$againstList);
        $this->assign('pageType','questionList');
        $this->Display();
    }

    public function agreeCount(){
        $data = [];
        $id = I('id');
        $res = M('argue_answer')->where(['id'=>$id])->setInc('iVoteCount',1);
        if($res){
            $this->ajaxReturn(['status'=>1,'msg'=>'请求成功！']);
        }
    }

    //辩论评论
    public function saveComment(){
        $data = [];
        $data['pid'] = I('pid');
        $data['uid'] = $this->uid;
        $data['iType'] = I('type');
        $data['sReply'] = I('comment');
        $data['iAddTime'] = time();
        $res = M('argue_answer')->add($data);
        if($res){
            $this->ajaxReturn(['status'=>1,'评论成功！']);
        }else{
            $this->ajaxReturn(['status'=>0,'评论失败！']);
        }
    }
    
    public function argueStatistics(){
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1 && $userInfo['iIsAdmin'] != 1){
            $this->error("无权限！");
        }
        $list = M('argue_list a')
        ->order('a.iAddTime DESC')
        ->select();
        foreach($list as &$l){
            $supportCount = $this->getAgreeCount($l['id'],1);
            $againstCount = $this->getAgreeCount($l['id'],0);
            $l['supportCount'] = $supportCount;
            $l['againstCount'] = $againstCount;
            $l['date'] = date("Y-m-d H:i",$l['iAddTime']);
         }
        $this->assign('arguelist',$list);
        $this->assign('pageType','studentList');
        $this->Display();
    }

    public function getAgreeCount($pid,$iType){
        $list = M('argue_answer')->where(['pid'=>$pid,'iType'=>$iType])->select();
        $count = 0;
        foreach($list as $l){
            $count += $l['iVoteCount'];
        }
        return $count;
    }

    public function argueStatisticsDetail($id){
        $userInfo = session('user_auth');
        if($userInfo['iType'] != 1 && $userInfo['iIsAdmin'] != 1){
            $this->error("无权限！");
        }
        $list = M('argue_answer a')
        ->field('a.*,um.realname')
        ->join('ucenter_member as um ON um.id=a.uid','LEFT')
        // ->join('argue_count as c ON c.uid=a.uid','LEFT')
        ->where(['a.pid'=>$id])
        // ->group('a.uid')
        ->order('a.iVoteCount DESC')
        ->select();
        $sortArr = [];
        foreach($list as &$l){
            $sortArr[$l['iType']][$l['uid']]['iType'] = $l['iType'];
            $sortArr[$l['iType']][$l['uid']]['type_text'] = $l['iType'] > 0?'正方':'反方';
            $sortArr[$l['iType']][$l['uid']]['iVoteCount'] += $l['iVoteCount'];
            $sortArr[$l['iType']][$l['uid']]['realname'] = $l['realname'];
            $sortArr[$l['iType']][$l['uid']]['uid'] = $l['uid'];
            $sortArr[$l['iType']][$l['uid']]['pid'] = $l['pid'];
            // $l['date'] = date("Y-m-d H:i",$l['iAddTime']);
        }
        $type0 = empty($sortArr[0])?[]:$sortArr[0];
        $type1 = empty($sortArr[1])?[]:$sortArr[1];
        $sortArr = array_merge($type0,$type1);
        array_multisort(array_column($sortArr,'iVoteCount'), SORT_DESC, $sortArr);
        $this->assign('arguelist',$sortArr);
        $this->assign('pageType','studentList');
        $this->Display();
    }

    public function detailAnswer(){
        $pid = I('pid');
        $uid = I('uid');
        $type = I('type');
        $list = M('argue_answer')->where(['pid'=>$pid,'uid'=>$uid,'iType'=>$type])->order('iAddTime DESC')->select();
        $this->ajaxReturn(['status'=>1,'data'=>$list]);
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

    public function contactTeacher(){
        $list = M('ucenter_member')->where(['iType'=>1])->select();
        $this->assign('teacher_list',$list);
        $this->assign('pageType','contactTeacher');
        $this->Display();
    }

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

    public function sharePublish(){
        $this->Display();
    }

    public function shareDetail($id){
        $list = M('share_list s')
        ->join('file f ON s.iFileId = f.id','LEFT')
        ->join('ucenter_member um ON s.uid = um.id','LEFT')
        ->where(['s.id'=>$id])
        ->find();
        $list['date'] = date("Y-m-d H:i",$list['iAddTime']);
        //加载该问题所有回复
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

    public function sharingStatistics(){
        $userInfo = session('user_auth');
        // if($userInfo['iType'] != 1 && $userInfo['iIsAdmin'] != 1){
        //     $this->error("无权限！");
        // }
        $studentList = M('ucenter_member um')
        ->field('um.*,COUNT(a.id) AS count')
        ->join('share_list as a ON um.id=a.uid','LEFT')
        ->where(['um.iType'=>0])
        ->group('um.id')
        ->select();
        var_dump($studentList);exit;
        foreach($studentList as &$student){
            $student['iIsHeadman'] = $student['iIsHeadman']>0?'是':'否';
        }
        $this->assign('studentList',$studentList);
        $this->assign('pageType','studentList');
        $this->Display();
    }
}