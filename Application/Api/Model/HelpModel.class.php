<?php

namespace Api\Model;
use Think\Model;
use Vendor\requester;

/**
 * 辅助类
 */
class HelpModel extends Model{
    
    /*
     * 数组快速排序
     */
    public function quickSort($arr,$key='iAddTime') {
        //判断是否继续进行
        $length = count($arr);
        if($length <= 1) {
            return $arr;
        }
        //选择第一个数字作为基准
        $base_num = $arr[0];
        //遍历除了基准外的所有数字，按照大小关系放入两个数组内，之后初始化两个数组
        $left_array = array();  //小于基准
        $right_array = array();  //大于基准
        for($i=1; $i<$length; $i++) {
            if($base_num[$key] > $arr[$i][$key]) {
                //放入左边数组
                $right_array[] = $arr[$i];
            } else {
                //放入右边数组
                $left_array[] = $arr[$i];
            }
        }
        //分别对两数组进行相同的排序处理方式递归
        $left_array = $this->quickSort($left_array,$key);
        $right_array = $this->quickSort($right_array,$key);
    
        //合并数组
        return array_merge($left_array, array($base_num), $right_array);
    }
    /*
     * 数组快速排序-降序
     */
    public function quickSortDesc($arr,$key='iAddTime') {
        //判断是否继续进行
        $length = count($arr);
        if($length <= 1) {
            return $arr;
        }
        //选择第一个数字作为基准
        $base_num = $arr[0];
        //遍历除了基准外的所有数字，按照大小关系放入两个数组内，之后初始化两个数组
        $left_array = array();  //小于基准
        $right_array = array();  //大于基准
        for($i=1; $i<$length; $i++) {
            if($base_num[$key] <= $arr[$i][$key]) {
                //放入左边数组
                $right_array[] = $arr[$i];
            } else {
                //放入右边数组
                $left_array[] = $arr[$i];
            }
        }
        //分别对两数组进行相同的排序处理方式递归
        $left_array = $this->quickSortDesc($left_array,$key);
        $right_array = $this->quickSortDesc($right_array,$key);
    
        //合并数组
        return array_merge($left_array, array($base_num), $right_array);
    }
    
    /**
     * 所属日期判断
     * @param timestamp $timestamp
     * @return string
     */
    public function timeBelongDate($timestamp) {
        return date('Y-m-d',$timestamp-($timestamp+28800)%86400);
    }
    
    //触发某个连接
    public function touchUrl($url,$post='',$timeoutMs=10000) {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//https
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//https
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if(!empty($timeoutMs)){
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeoutMs);//10秒未响应就断开连接
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        
        
        if(!empty($post)) {
            curl_setopt($ch, CURLOPT_POST, 1);//post方式提交
            if(is_array($post))
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));//要提交的信息
            else
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);//要提交的信息
        
        }
        $rs = curl_exec($ch); //执行cURL抓取页面内容
        curl_close($ch);
        
        return $rs;
    }
    
    public function getLanguage() {
        $think_language = cookie('think_language');
        $think_language = empty($think_language)?'zh-cn':$think_language;
        
        return $think_language;
    }
    
    
    public function isItStrict($keyName) {
        //只处理英文关键词
        if(!preg_match('/^[a-z0-9\@\_\&\-\s\+]+$/i',$keyName)) {
            return false;
        }
    
        $keyNameArr = explode('+',$keyName);
    
//                 var_dump(sizeof($keyNameArr),strlen($keyName),$keyName,$keyNameArr);
//                 exit;
    
        $keyNameArrCount = sizeof($keyNameArr);
        //没有+号,5个字符长度代表严格匹配
        if($keyNameArrCount == 1 && strlen($keyName) <= 5) {
            //             var_dump('1');
            return true;
            //有+号,8个字符长度代表严格匹配
        }elseif($keyNameArrCount >= 2 && strlen($keyName) <= 8){
            return true;
        }
    
        return false;
    }
    
    public function strictedKeyName($keyName) {
        $preg_pre = '([^a-z0-9\@\_\&\-]|^)';
        $preg_end = '([^a-z0-9\@\_\&\-]|$)';
        if(is_array($keyName)) {
            foreach($keyName as &$val) {
                $val = $preg_pre.$val.$preg_end;
            }
        }else{
            $keyName = $preg_pre.str_replace(array('+','(',')','*'), array('\+','\(','\)','\*'), $keyName).$preg_end;
        }
        
        return $keyName;
    }
    
    public function getNotKeyNameArr() {
        $CommonModel = D('Common');
        static $notKeyNameArr;
        if(!isset($notKeyNameArr)) {
            $notKeyNameArr = array();
            $not_search_termsModel = M('not_search_terms');
            $notKeyDatas = $not_search_termsModel->field('sName,sNotName')->where(array('itStatus'=>1))->select();
            //             echo $not_search_termsModel->getLastSql();
            //             var_dump($notKeyDatas);
            foreach($notKeyDatas as $notKeyData) {
                $notKeyData['sNotName'] = strtoupper(trim($notKeyData['sNotName']));
                $notKeyData['sName'] = strtoupper(trim($notKeyData['sName']));
                $simpleName = $CommonModel->tradition2simple($notKeyData['sName']);
                
                if(!in_array($notKeyData['sNotName'],$notKeyNameArr[$notKeyData['sName']])) {
                    $notKeyNameArr[$simpleName][] = $notKeyData['sNotName'];
                }
    
                
                $simpleNotName = $CommonModel->tradition2simple($notKeyData['sNotName']);
                if(!in_array($simpleNotName,$notKeyNameArr[$simpleName])) {
                    $notKeyNameArr[$simpleName][] = $simpleNotName;
                }
                
                $notKeyNameArr[$simpleName] = array_unique(array_merge($notKeyNameArr[$simpleName],$CommonModel->simple2traditionMuSmart($simpleNotName)));
            }
        }
    
        return $notKeyNameArr;
    }
    
    /**
     * 新闻类型iType与name关系
     */
    public function getItypeName() {
        $iTypeName = array(
            1=>L('type_1'),
            2=>L('type_2'),
            4=>L('type_4'),
            8=>L('type_8'),
            16=>L('type_16'),
            32=>L('type_32'),
            64=>L('type_64'),
            128=>L('type_128'),
            256=>L('type_256'),
            512=>L('type_512'),
            1024=>L('type_1024'),
            2048=>L('type_2048'),
            4096=>L('type_4096'),
        );
        
        return $iTypeName;
    }
    
    /**
     * 区分a股和h股公司code
     */
    public function difCompanyCode($codeArr) {
        $aCode = array();
        $hCode = array();
        foreach($codeArr as $code) {
            if(strlen($code) === 5) {
                $hCode[] = $code;
            }else{
                $aCode[] = $code;
            }
        }
        
        return array('a'=>$aCode,'h'=>$hCode);
    }
    
    //格式化code--提至公用函数
    public function formatCompanyCode($code) {
        return formatCompanyCode($code);
    }
    
    /**
     * 导出文件函数
     * @param unknown $filleName
     * @param unknown $expCellName
     * @param unknown $expTableData
     */
    public function exportExcel($file,$expTableData,$style=array()){
        $fileName = $xlsTitle;//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expTableData[0]);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        //自动居中
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        for($j=0;$j<$cellNum;$j++){
            if(!empty($style[$j])) {
                $sheet->getColumnDimension($cellName[$j])->setWidth($style[$j]);
            }else{
                $sheet->getColumnDimension($cellName[$j])->setAutoSize(true);
            }
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $sheet->setCellValue($cellName[$j].($i+1), htmlspecialchars($expTableData[$i][$j],ENT_COMPAT, 'UTF-8'),true);
            }
        }
        
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        return $file;
    }
    
    /**
     * 批量压缩url文件
     * @param unknown $urlInfos
     * @param string $zipName
     */
    public function  batchDownFileToZip($urlInfos = array(),$filename=''){
        $zip = new \ZipArchive();
        if(empty($filename)) {
            die('zip filename 不能为空！');
        }
    
        if ($zip->open($filename, \ZIPARCHIVE::CREATE)!==TRUE) {
            die('无法打开文件，或者文件创建失败！');
        }
    
        foreach($urlInfos as $urlInfo) {
            $zip->addFromString(iconv('UTF-8', 'GBK',$urlInfo['localName']),file_get_contents($urlInfo['url']));
        }
        $zip->close();//关闭
    }
    

    /**
     * 根据语言获取字段值
     * @param unknown $prefix 表别名
     * @param string $language 当前语言
     * @param string $asValue 返回字段名
     * @return string
     */
    function languageSql($prefix,$filedArr=array(),$language='zh-cn') {
        
        if(!empty($prefix)) {
            $prefix = $prefix.'.';
        }else{
            $prefix = '';
        }
        
        if(!empty(I('language'))) {
            $language = I('language');
        }
        
        //加入语言拉取数据
        switch ($language) {
            case 'zh-cn':
                $sql = 'IF('.$prefix.$filedArr['zh-cn'].' is NULL OR '.$prefix.$filedArr['zh-cn'].' = \'\','.$prefix.$filedArr['default'].','.$prefix.$filedArr['zh-cn'].')';
                break;
            case 'zh-tw':
                $sql = 'IF('.$prefix.$filedArr['zh-tw'].' is NULL OR '.$prefix.$filedArr['zh-tw'].' = \'\','.$prefix.$filedArr['default'].','.$prefix.$filedArr['zh-tw'].')';
                break;
            case 'en-us':
                $sql = 'IF('.$prefix.$filedArr['en-us'].' is NULL OR '.$prefix.$filedArr['en-us'].' = \'\','.$prefix.$filedArr['default'].','.$prefix.$filedArr['en-us'].')';
                break;
        }
    
        return $sql;
    }
    
    /**
     * 格式化正文
     * @param unknown $content
     * @return mixed
     */
    public function formatterText($content) {
        $content = $this->newsContentSplit($content);
        
//         MatchStr.replace('{060}',(index==1?'':'<br/>')+'<img style="max-width:100%;" src="').replace('{062}','" />');
        $content = preg_replace_callback('/(?:\{092\}r\{092\}n)*\{060\}([\s\S]+?)\{062\}(?:\{092\}r\{092\}n)*/i', function($match,$index){
            return '<br/><img style="max-width:100%;" src="'.$match[1].'" /><br/>';
        }, $content);
        $content = str_replace(array('{09}','{092}r{092}n'), array('　　','<br/>'), $content);
        
        return $content;
    }
    
    public function getOssUrlByid($id) {
        $request = new requester();
        $request->url = C('SOLR_SELECT_HANDLE_FILE');
        $request->method = 'POST';
        $request->data = 'indent=on&q=id:'.$id.'&wt=json';
        $json = $request->request();
        $json = json_decode($json[1],true);
        
        $fileInfo = $json['response']['docs'][0];
        
        $path = getOssUrlByFullUrl($fileInfo['savepath'].'/'.$fileInfo['name']);
        
        return $path;
    }
    
    public function redirect($url) {
        header("Location: ".$url);
        exit;
    }
    
    public function newsContentSplit($content='') {
        static $iContentLength = null;
        
        if(is_null($iContentLength)) {
            $uid = is_login();
            $allLength = C('PR_CONTENT_LENGTH');
            if($allLength > 0) {
                $iContentLength = $allLength;
            }else{
                $iContentLength = M('member')->where(['uid'=>$uid])->getField('iContentLength');
            }
        }
        
        if($iContentLength == 0) {
            return $content;
        }
        
        $contentArr = preg_split('/(\{09\}|\{092\}r\{092\}n|\{060\}\w+?\{062\}|\<span class\=\"key_color\"\>|\<\/span\>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $returnContent = '';
        $count = 0;
        $willBreak = false;
        foreach ($contentArr as $index => $p) {
            if($index%2 === 0) {
                $pCount = mb_strlen($p,"utf8");
                $count += $pCount;
                if($count > $iContentLength) {
                    $p = mb_substr($p, 0, $pCount-($count - $iContentLength), "utf8").'...';
                    $willBreak = true;
                }
                $returnContent .= $p;
            }else{
                if(strpos($p, '<') === 0) {
                    $returnContent .= $p;
                }
                
                if($willBreak) {
                    break;
                }
                
                if(strpos($p, '{') === 0) {
                    $returnContent .= $p;
                }
            }
        }
        
        return $returnContent;
    }
}
