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

        $userChoose = '';
        //加载该问题所有赞成回复
        $supportList = M('argue_answer a')
        ->field('a.*,um.realname')
        ->join('ucenter_member um ON a.uid = um.id','LEFT')
        ->where(['a.pid'=>$id,'a.iType'=>1])
        ->order('a.iAddTime DESC')
        ->select();
        foreach($supportList as &$list){
            if($list['uid'] == $this->uid){
                $userChoose = 1;
            }
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
            if($list['uid'] == $this->uid){
                $userChoose = 0;
            }
            $list['date'] = date("Y-m-d H:i",$list['iAddTime']);
        }
        $this->assign('questionDetail',$detail);
        $this->assign('supportList',$supportList);
        $this->assign('againstList',$againstList);
        $this->assign('userchoose',$userChoose);
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
    
    //辩论统计
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

    //辩论统计详情
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
}