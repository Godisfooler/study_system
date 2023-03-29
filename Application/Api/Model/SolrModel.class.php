<?php

namespace Api\Model;
use Think\Model;
use Api\Model\CurlModel;
use Vendor\requester;

/**
 * 辅助类
 */
class SolrModel extends Model{
    private $model;
    private $CurlModel;
    private $php_os = 'windows';
    public $inCludeImg = 0;
    
    public function __construct() {
        $this->CurlModel = new CurlModel();
        
        //操作系统判断
        if(strtolower(substr(PHP_OS, 0, 3)) == 'win'){
            $this->php_os = 'windows';
        }else{
            $this->php_os = 'linux';
        }
    }
    
    /**
     * 预处理关键词
     * @param unknown $keyNameArr
     * @return string
     */
    public function preDealKey($keyNameArr) {
        //+号兼容全角字符
        if(is_array($keyNameArr)) {
            foreach($keyNameArr as $key => $keyName) {
                $keyNameArr[$key] = $this->dealOneKey($keyName);
            }
        }else{
            $keyNameArr = $this->dealOneKey($keyNameArr);
        }
        
        return $keyNameArr;
    }
    
    /**
     * 单个关键词处理
     * @param unknown $keyName
     * @return string
     */
    private function dealOneKey($keyName) {
        return '("'.preg_replace(array('/(’|\')(\+|＋)(’|\')/'), array('" AND "'), $keyName).'")';
    }
    
    /**
     * 简繁体转化
     */
    public function simTraKey($keyNameArr) {
        $commonModel = D('Common');
        //+号兼容全角字符
        if(!is_array($keyNameArr)) {
            $keyNameArr = array($keyNameArr);
        }
        
        $keyArr_temp = array();
        foreach($keyNameArr as $val) {
            //原始
            if(!in_array($val, $keyArr_temp)) {
                $keyArr_temp[] = $val;
            }
        
            //简体
            $simVal = $commonModel->tradition2simple($val);
            if(!in_array($simVal, $keyArr_temp)) {
                $keyArr_temp[] = $simVal;
            }
            
            if($this->php_os == 'linux') {
                //opencc繁体
                $traVal = $commonModel->s2t($simVal);
                if(!in_array($traVal, $keyArr_temp)) {
                    $keyArr_temp[] = $traVal;
                }
                
                //台湾繁体
                $traVal = $commonModel->s2tw($simVal);
                if(!in_array($traVal, $keyArr_temp)) {
                    $keyArr_temp[] = $traVal;
                }
                
                //香港繁体
                $traVal = $commonModel->s2hk($simVal);
                if(!in_array($traVal, $keyArr_temp)) {
                    $keyArr_temp[] = $traVal;
                }
                
            }else{//windows系统使用以前的转化器
                //繁体
                $traVal = $commonModel->simple2tradition($val);
                if(!in_array($traVal, $keyArr_temp)) {
                    $keyArr_temp[] = $traVal;
                }
            }
        }
        
        return $keyArr_temp;
    }
    
    
    public function productSolrParameter($keyNameArr,$iSearchType='4',$iInCludeAuthor=0,$other=array()) {
        if(empty($keyNameArr)) {
            $keyNameArr = array('*');
        }else{
            $keyNameArr = array($keyNameArr);
//             $keyNameArr = $this->preDealKey($keyNameArr);
        }
        
        $keyQ = I('keyQ',null);
        
        if($iSearchType == 4) {
            $qsContentStr = 'sTitleAndContent:('.implode(' OR ', $keyNameArr).')';
            $q = $qsContentStr;
        }else{
            $q = 'sTitle:('.implode(' OR ', $keyNameArr).')';
        }
        
        if($iInCludeAuthor) {
            $qsAuthorStr = 'sAuthor:'.implode(' OR sAuthor:', $keyNameArr).'';
            $q = $q.' OR '.$qsAuthorStr;
        }
        
        
        if(!empty($keyQ)) {
            $keyQArr = $this->simTraKey(trim($keyQ,'"'));
            $keyQ = '"'.trim($keyQ,'"').'"';//默认加上引号
            //限制搜索90天內的新聞，防止搜索過久
            $q = '( '.$q.' ) AND iPublishTime:['.(time()-86400*90).' TO '.time().'] AND (sTitleAndContent:"'.implode('" OR sTitleAndContent:"', $keyQArr).'")';
        }
        
        //加载新闻开始时间优化
        $endTime = (int)I('endTime',0);
        if(empty($endTime)) {
            $endTime = time()-7*86400;
        }else{
            $endTime = $endTime-7*86400;
        }
        
        
        //舆情新闻只显示最近30天
        $q = '( '.$q.' ) AND iPublishTime:['.$endTime.' TO '.time().']';
        
        $parameter = array(
            'indent'=>'on',
            'q'=>$q,
            'wt'=>'json',
            'hl'=>'on',
//             'defType'=>'dismax',
            'hl.fl'=>'sTitle',
            'hl.simple.pre'=>I('hlPre','<span class="key_color">'),
            'hl.simple.post'=>I('hlEnd','</span>'),
        );
        
        $iSiteIdPromiseArr = $this->siteIdFilter(__PUID__);
        if(!empty($iSiteIdPromiseArr[0])) {//白名单
            if(isset($other['fq'])) {
                $other['fq'] .= ' AND (iSiteId:('.implode(' ', $iSiteIdPromiseArr[0]).') AND iType:(1 2 4 256 1024))';
            }else{
                $other['fq'] = 'iSiteId:('.implode(' ', $iSiteIdPromiseArr[0]).') AND iType:(1 2 4 256 1024)';
            }
        }
        
        if(!empty($iSiteIdPromiseArr[1])) {//私有站点
            if(isset($other['fq'])) {
                $other['fq'] .= ' AND -(iSiteId:('.implode(' ', $iSiteIdPromiseArr[1]).') AND iType:(1 2 4 256 1024))';
            }else{
                $other['fq'] = '-(iSiteId:('.implode(' ', $iSiteIdPromiseArr[1]).') AND iType:(1 2 4 256 1024))';
            }
        }
        $parameter = array_merge($parameter,$other);
        
        return $parameter;
    }
    
    public function siteIdFilter($uid) {
        $siteIdArr = M('website_filter')->field(array('uid','iSiteId','iType'))->select();
        
        if(empty($siteIdArr)) {
            return array([],[]);
        }
        
        $otherSiteIdArr = array();
        $iSiteIdWhiteArr = array();
        $iSiteIdBlackArr = array();
        $mySiteIdArr = array();
        foreach($siteIdArr as  $siteId) {
            if($siteId['iType'] == 0) {//私有站点处理
                if($siteId['uid'] == $uid) {
                    $mySiteIdArr[] = $siteId['iSiteId'];
                }else{
                    $otherSiteIdArr[] = $siteId['iSiteId'];
                }
            }elseif($siteId['uid'] == $uid && $siteId['iType'] == 1) {//白名单
                $iSiteIdWhiteArr[] = $siteId['iSiteId'];
            }elseif($siteId['uid'] == $uid && $siteId['iType'] == 2) {//黑名单
                $iSiteIdBlackArr[] = $siteId['iSiteId'];
            }
        }
        
        //存在白名单，无效私有站点和黑名单，直接返回
        if(!empty($iSiteIdWhiteArr)) {
            return array($iSiteIdWhiteArr,[]);//array(白名单，私有站点)
        }
        
        //存在黑名单--并入私有站点
        if(!empty($iSiteIdBlackArr)) {
            $otherSiteIdArr = array_merge($otherSiteIdArr,$iSiteIdBlackArr);
        }
        
        //排除自己设置的私有站点（即使设置了黑名单）
        foreach ($otherSiteIdArr as $key=>$otherSiteId) {
            if(in_array($otherSiteId, $mySiteIdArr)) {
                unset($otherSiteIdArr[$key]);
            }
        }
        
        return array([],$otherSiteIdArr);
    }
    
    public function getNewsList($keyName,$iSearchType,$iInCludeAuthor,$other) {
        if(!empty($keyName)) {
            $parameter = $this->productSolrParameter(empty($keyName)?'':'('.$keyName.')',$iSearchType,$iInCludeAuthor,$other);
        }else{
            $parameter = $other;
        }
        
//         echo $parameter['q'];
//         var_dump($parameter);
//         exit;
        $url = C('SOLR_SELECT_HANDLE');
        $json = json_decode($this->outSpecialLabel($this->CurlModel->getUrlHtml($url, $parameter)),true);
        
        return $json;
    }
    
    public function outSpecialLabel($content) {
        if($this->inCludeImg) {
            return $content;
        }else{
            return preg_replace(['/(?:\{092\}r\{092\}n|\{09\})*\{060\}\S+?\{062\}|(?:\{092\}r\{092\}n|\{09\})*(?=")/i','/\:"(?:\{092\}r\{092\}n)+/i'], ['',':"'], $content);
        }
    }
    
    
    public function getNewsInfo($id,$other) {
        $q = 'id:'.$id;
        $parameter = array(
            'indent'=>'on',
            'q'=>$q,
            'wt'=>'json',
            'hl'=>'on',
            'hl.fl'=>'sContent',
            'hl.simple.pre'=>I('hlPre','<span class="key_color">'),
            'hl.simple.post'=>I('hlEnd','</span>'),
        );
        $parameter = array_merge($parameter,$other);
        
        $url = C('SOLR_SELECT_HANDLE');
        $json = json_decode($this->outSpecialLabel($this->CurlModel->getUrlHtml($url, $parameter)),true);
        return $json;
    }
    
    
    public function getSiteList($siteIdArr,$language='',$field='id,sLanguage,sSiteName,sSiteSimName,sSiteTraName,sSiteEnglishName,sSiteUrl') {
        $url = C('JAVA_DB_API').'/news/Website/index';
        if(empty($language)) {
            $language = cookie('think_language');
        }
//         var_dump($url);
//         exit;
        $json = json_decode($this->CurlModel->getUrlHtml($url,array('ids'=>implode(',', $siteIdArr),'field'=>$field)),true);
        $json = empty($json)?array():$json;
        
        $returnArr = array();
        foreach($json as $val) {
            $val['siteOrgName'] = $val['sSiteName'];
            //加入语言拉取数据
            switch ($language) {
                case 'zh-cn':
                    $val['sSiteName'] = (empty($val['sSiteSimName']))?((empty($val['sSiteName']))?$val['sSiteUrl']:$val['sSiteName']):$val['sSiteSimName'];
                    break;
                case 'zh-tw':
                    $val['sSiteName'] = (empty($val['sSiteTraName']))?((empty($val['sSiteName']))?$val['sSiteUrl']:$val['sSiteName']):$val['sSiteTraName'];
                    break;
                case 'en-us':
                    $val['sSiteName'] = (empty($val['sSiteEnglishName']))?((empty($val['sSiteName']))?$val['sSiteUrl']:$val['sSiteName']):$val['sSiteEnglishName'];
                    break;
            }
            
            $returnArr[$val['id']] = $val;
        }
        
        return $returnArr;
    }
    
    public function goShortSiteList() {
        //获取做空机构报告站点
        $request = new requester();
        $request->url = C('STOCK_API');
        $request->method = 'POST';
        $request->data = 'interface=shortSellerSiteList';
        $interface = $request->request();
        $goShortSiteArr = json_decode($interface[1],true);
        $goShortSiteArr = empty($goShortSiteArr)?array():$goShortSiteArr;
        
        $goShortSiteArr_temp = array();
        foreach($goShortSiteArr as $goShortSite) {
            $goShortSiteArr_temp[$goShortSite['id']] = $goShortSite['sWebSiteName'];
        }
        
        return $goShortSiteArr_temp;
    }
    
    //((yes AND (no OR like)) OR (1 OR 2) (bug AND hei))
    //("yes" AND 123 OR "AND" AND ("\(no OR" OR like))
    /**
     * 自动添加引号
     * @param unknown $para
     * @return string
     */
    public function parserParameters($para) {
    	$para = str_replace(array('（','）','(',')'), array(' (',') ',' (',') '), $para);
    	//首位添加不完整的括号
    	$para = $this->preg_bracket($para,true);
    
    	//         var_dump($para);
    	//         exit;
    	$para = $this->preg_bracket($para);
    	return $para;
    }
    
    public function preg_bracket($para,$init = false) {
    	$para_de_pre = '(';
    	$para_de_end = ')';
    	$para_de_space = ' ';
    	$para_de_pre_count = 0;
    	$para_de_pre_min_count = 0;
    	$strLen = mb_strlen($para);
    
    	$start = '';
    	$end = '';
    
    	//去掉首位无效括号
    	if(mb_substr($para, $i,1) == $para_de_pre && mb_substr($para, $strLen-1,1) == $para_de_end  && $init == false && $this->isNeedDelBracket($para)) {
    		$start = $para_de_pre;
    		$end = $para_de_end;
    		$para = mb_substr($para, 1,-1);
    		$strLen = mb_strlen($para);
    	}
    
    	//         var_dump($para);
    
    	$str_cache = '';
    	$isStr = true;
    	$haveAdded = false;
    	$subArr = array();
    	$subBracketArr = array();
    	for($i = 0;$i<$strLen;$i++) {
    		$code = mb_substr($para, $i,1);
    		if($code == '\\') {
    			$code = mb_substr($para, $i++,2);
    		}
    
    		if($isStr == false) {
    			$str_cache .= $code;
    			$haveAdded = true;
    		}
    		if($code == $para_de_pre) {
    			if($isStr && 0 == $para_de_pre_count) {
    				if($str_cache !== '') {
    					$subArr[] = $str_cache;
    					$subBracketArr[] = true;
    				}
    				$str_cache = '';
    			}
    			$para_de_pre_count++;
    
    			$isStr = false;
    		}
    
    		if($code == $para_de_end) {
    			$para_de_pre_count--;
    			if($para_de_pre_count < $para_de_pre_min_count) {
    				$para_de_pre_min_count = $para_de_pre_count;
    			}
    			if(0 == $para_de_pre_count) {
    				$subArr[] = $str_cache;
    				$str_cache = '';
    				$isStr = true;
    			}
    		}
    
    		if($haveAdded == false) {
    			$str_cache .= $code;
    		}
    
    		$haveAdded = false;
    	}
    
    	if($init) {
    		if($para_de_pre_min_count < 0) {
    			while($para_de_pre_min_count++ !== 0 ) {
    				$subArr[0] = '('.$subArr[0];
    			}
    			$subArr[] = $str_cache;
    
    			//         		var_dump($subArr);
    			//         		exit;
    			return $this->preg_bracket(trim(implode('', $subArr)),true);
    		}
    		 
    		//后括号缺失
    		if($para_de_pre_count > 0) {
    			while($para_de_pre_count-- !== 0) {
    				$str_cache = $str_cache.')';
    			}
    			$subArr[] = $str_cache;
    			return trim(implode('', $subArr));
    			//前括号缺失
    		}elseif($para_de_pre_count < 0) {
    			while($para_de_pre_count++ !== 0 ) {
    				$subArr[0] = '('.$subArr[0];
    			}
    			$subArr[] = $str_cache;
    
    			return trim(implode('', $subArr));
    		}else{
    			$subArr[] = $str_cache;
    			return trim(implode('', $subArr));
    		}
    		 
    	}
    	if($str_cache != '') {
    		$subArr[] = $str_cache;
    	}
    
    	$subArr = $this->rulesLeftToRight($subArr);
//     	        var_dump($start,$subArr,$end);
//     	        exit;
    
    	$para = '';
    	$para .= $start;
    	foreach($subArr as $sub) {
    		if($sub == '') {
    			continue;
    		}
    		//             var_dump($sub,$this->needSub($sub));
    		//             echo $para;
    		//             exit;
    		$para .= ($this->needSub($sub)?$this->preg_bracket($sub):$this->addQuot($sub));
    	}
    	$para .= $end;
    
    	return $para;
    	//             var_dump($subArr,$para);
    }
    
    public function rulesLeftToRight($subArr) {
    	$returnArr = array();
    	$preg = '/ AND | OR | NOT /';
    	foreach($subArr as $val) {
    		if(preg_match('/\([\s\S]*\)/', $val)) {
    			$returnArr[] = $val;
    		}else{
    			$valArr = preg_split($preg, $val);
    			preg_match_all($preg, $val, $valArrDe);
    			
    			$quotaCount = 0;
    			$strTemp = '';
    			foreach($valArr as $key => $val_2) {
    				$val_2 = trim($val_2);
    				if($key == 0) {
    					if($val_2 != '') {
    						$returnArr[] = $val_2;
    					}
    				}else{
    					$returnArr[] = $valArrDe[0][$key-1];
    					if($val_2 != '') {
    						$returnArr[] = $val_2;
    					}
    				}
    
    
    			}
    		}
    	}
    	
//     	var_dump($returnArr);
//     	exit;
    	
    	$returnTempArr = array ();
    	$strTemp = '';
    	$quotaCount = 0;
		foreach ( $returnArr as $val ) {
			if (strpos ( $val, '"' ) !== false) {
				$quotaCount += substr_count($val, '"' );
			}
			
			if ($quotaCount%2 === 1 || strpos($val,'"')) {
				$strTemp .= $val;
				continue;
			} else {
				if($strTemp != '') {
					$returnTempArr[] = $strTemp;
				}
				
				if($val != '') {
					$returnTempArr[] = $val;
				}
				$strTemp = '';
			}
    	}
    	
    	if($strTemp != '') {
    		$returnTempArr[] = $strTemp;
    	}
    	
    	$returnArr = $returnTempArr;
    	
//     	var_dump($returnArr,$returnTempArr);
//     	exit;
    	
    	$return_tempArr = array();
    	$return_tempArr[] = '';//用于储存括号
    	$residueStr = '';
    	foreach($returnArr as $key => $return) {
    		$return_tempArr[] = $return;
    		if($residueStr !== '') {
    			$return_tempArr[] = $residueStr;
    			$residueStr = '';
    		}
    		switch ($return) {
    			case ' AND ':
    			case ' OR ':
    			case ' NOT ':
    				$return_tempArr[0] = '('.$return_tempArr[0];
    				$residueStr = ')';
    				break;
    		}
    	}
    	if($residueStr !== '') {
    		$return_tempArr[] = $residueStr;
    		$residueStr = '';
    	}
    	 
    	//取出最后一个括号
    	 
    	if($return_tempArr[count($return_tempArr)-1] == ')') {
    		$return_tempArr[0] = substr($return_tempArr[0], 1);
    		unset($return_tempArr[count($return_tempArr)-1]);
    	}
    	 
    	return $return_tempArr;
    }
    
    public function needSub($para) {
    	$para_de_pre = '(';
    	$para_de_end = ')';
    	$para_de_quot = '"';
    	$para_de_quot_count = 0;
    	$strLen = mb_strlen($para);
    
    	$para_de_quot_count_total = 0;
    
    	for($i = 0;$i<$strLen;$i++) {
    		$code = mb_substr($para, $i,1);
    		if($code == '\\') {
    			$code = mb_substr($para, $i++,2);
    		}
    
    		if($code == $para_de_pre) {
    			$para_de_quot_count++;
    			$para_de_quot_count_total++;
    		}
    		if($code == $para_de_end) {
    			$para_de_quot_count--;
    			$para_de_quot_count_total++;
    		}
    	}
    
    	if($para_de_quot_count_total > 0 && $para_de_quot_count === 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    public function isNeedDelBracket($para) {
    	//     	var_dump($para);
    	//     	exit;
    	$para_de_pre = '(';
    	$para_de_end = ')';
    	$para_de_space = ' ';
    	$para_de_pre_count = 0;
    	$strLen = mb_strlen($para);
    	 
    	$bracketArr = array();
    	 
    	for($i = 0;$i<$strLen;$i++) {
    		$code = mb_substr($para, $i,1);
    		if($code == '\\') {
    			$code = mb_substr($para, $i++,2);
    		}
    		if($code == $para_de_pre) {
    			$bracketArr[] = $i;
    		}
    		 
    		if($code == $para_de_end && $i == $strLen-1) {
    			if(array_pop($bracketArr) == 0) {
    				return true;
    			}
    		}
    
    		if($code == $para_de_end) {
    			//去除数组最后一个
    			array_pop($bracketArr);
    		}
    	}
    	 
    	return false;
    	 
    }
    
    public function addQuot($para,$getArr = false,$preg = '/ AND | OR | NOT /',$addBracket = false,$fromOneWord = false) {
    	static $paraStatic = array();
    	$paraMd5 = md5($para);
    	if(!isset($paraStatic[$paraMd5])) {
    		$para_de_pre = '(';
    		$para_de_end = ')';
    		$para_de_space = ' ';
    		$para_de_quot = '"';
    		$para_de_quot_count = 0;
    		$strLen = mb_strlen($para);
    
    		$str_cache = '';
    		$para_de_quoted = false;
    		$paraArr = array();
    		for($i = 0;$i<$strLen;$i++) {
    			$code = mb_substr($para, $i,1);
    			if($code == '\\') {
    				$code = mb_substr($para, $i++,2);
    			}
    
    			if($code == $para_de_quot) {
    				$para_de_quot_count++;
    				if(1 == $para_de_quot_count%2) {
    					if($str_cache !== '') {
    						$paraArr[] = $str_cache;
    					}
    					$str_cache = $code;
    				}elseif(0 == $para_de_quot_count%2) {
    					$str_cache .= $code;
    					if($str_cache !== '') {
    						$paraArr[] = $str_cache;
    					}
    					$str_cache = '';
    				}
    			}else{
    				$str_cache .= $code;
    			}
    		}
    		if($str_cache !== '') {
	    		$paraArr[] = $str_cache;
    		}
    
    		$paraStatic[$paraMd5]['paraArr'] = $paraArr;
    
//     		    		var_dump($paraArr);
//     		    		exit;
    		$paraStr = '';
    		foreach($paraArr as $val) {
    			if(preg_match('/"[\s\S]+"/', $val)) {
    				$paraStr .= $this->addQuotation($val,$pre = ' AND ',$de='',$fromOneWord);
    			}else{
    				$valArr = preg_split($preg, $val);
    				preg_match_all($preg, $val, $valArrDe);
    
    				//     				var_dump($valArrDe,$valArr);
    				//     				exit;
    				$count = count($array_or_countable);
    				$paraStr_temp = '';
    				foreach($valArr as $key => $temp) {
    					$temp = trim($temp);
    					if($key == 0) {
    						if($temp !== '') {
    							$paraStr_temp .=  $this->addQuotation($temp,' AND ','"',$fromOneWord);
    						}else{
    							$count--;
    						}
    					}else{
    						$paraStr_temp .= $valArrDe[0][$key-1];
    
    						if($temp !== '') {
    						    if($valArrDe[0][$key-1] === ' NOT ') {
    						        $paraStr_temp = '('.$paraStr_temp.$this->addQuotation($temp,$valArrDe[0][$key-1],'"',$fromOneWord).')';
    						    }else{
    						        $paraStr_temp .= $this->addQuotation($temp,$valArrDe[0][$key-1],'"',$fromOneWord);
    						    }
    						}else{
    							$count--;
    						}
    					}
    
    				}
    				$paraStr .= ($addBracket)?'('.$paraStr_temp.')':$paraStr_temp;
    
    				//     				$paraStr .= ($addBracket == 1 && $count > 1)?'('.$paraStr_temp.')':$paraStr_temp;
    			}
    		}
    
    		$paraStatic[$paraMd5]['paraStr'] = $paraStr;
    	}
    	 
    	if($getArr) {
    		return $paraStatic[$paraMd5]['paraArr'];
    	}else{
    		//     				var_dump($paraStatic[$paraMd5]['paraStr']);
    		return $paraStatic[$paraMd5]['paraStr'];
    	}
    }
    
    private function addQuotation($key,$pre = ' AND ',$de='"',$fromOneWord = false) {
    	$key = trim($key);
    	if($key == '') {
    		return '';
    	}
    	//     	$key = preg_replace(array('/^(\(*)([^\(]+)/','/([^\)]+)(\)*)$/'), array("$1".$de."$2","$1".$de."$2"), $key);
		if($fromOneWord) {
			$key = $de.$key.$de;
		}else{
			$key = preg_replace_callback('/^(\(*)([\s\S]*?)(\)*)$/', function($match) use ($de){
				if(trim($match[2]) == '') {
					return $match[1].$match[3];
				}else{
					return $match[1].$de.$match[2].$de.$match[3];
				}
			}, $key);
		}
    	
//     		    	echo $key;
//     		    	exit;
    		//     	$key = $de.$key.$de;
    		switch ($pre) {
    			case ' AND ':
    			case ' OR ':
    				$keyArr = $this->simTraKey($key);
    				if(count($keyArr) == 1) {
    					return implode(' OR ', $keyArr);
    				}else{
    					return '('.implode(' OR ', $keyArr).')';
    				}
    				break;
    			case ' NOT ':
    				$keyArr = $this->simTraKey($key);
    				return implode(' NOT ', $keyArr);
    				break;
    		}
    		 
    }
    
    public function getKeyStr($kmArr=array()) {
    	$kmStrArr = array();
    	foreach($kmArr as $km) {
    		$km = trim(strtoupper($km));
    		if($km == '') {
    			continue;
    		}
    		$kmStrArr[] = $this->addQuot($km,false,'/ AND | NOT /',false,true);
    	}
    	 
    	$kmStr = '(('.implode(') OR (', $kmStrArr).'))';
    	return $kmStr;
    }
    
    public function imgTagConvertImageUrl($contents=[],$iPublishs=[]) {
        if(empty($contents)) {
            return [];
        }
    
        $tags = [];
        foreach($contents as $key => $content) {
            preg_match_all('/\{060\}([0-9a-f]+?)\{062\}/i', $content,$match);
            if(empty($match[1])) {
                continue;
            }
    
            $Ym = date('Ym',$iPublishs[$key]);
            if(isset($tags[$Ym])) {
                $tags[$Ym] = array_merge($tags[$Ym],$match[1]);
            }else{
                $tags[$Ym] = $match[1];
            }
        }
    
        $sqlArr = [];
        foreach($tags as $Ym => $tag) {
            $table = 'oc_yuqing_img_'.$Ym;
            $sqlArr[] = $model = M('','','DB_SPIDER_CONFIG')
            ->table($table)
            ->field('sLinkMD5,sOSSLink')
            ->where(['sLinkMD5'=>['in',$tag],'iState'=>1])
            ->buildSql();
        }
    
        if(!empty($sqlArr)) {
            $sql = implode(' UNION ', $sqlArr);
            $reuslt = M('','','DB_SPIDER_CONFIG')
            ->query($sql);
            $reuslt = $reuslt?$reuslt:array();
        }else{
            $reuslt = array();
        }
    
        $md5ConvertPath = [];
        foreach ($reuslt as $val) {
            $md5ConvertPath[$val['sLinkMD5']] = getOssUrlByFullUrl(str_replace('./', '',$val['sOSSLink']),['x-oss-process' => "image/resize,w_800"]);
        }
    
        //         print_r($contents);
        foreach($contents as $key => $content) {
            $contents[$key] = preg_replace_callback('/(?:\{092\}r\{092\}n|\{09\})*\{060\}([0-9a-f]+?)\{062\}/i', function($match) use($md5ConvertPath) {
                if(isset($md5ConvertPath[$match[1]])) {
                    return '{060}'.$md5ConvertPath[$match[1]].'{062}';
                }else{
                    return '';
                }
            }, $content);
        }
    
        //         print_r($contents);
        //         exit;
    
        return $contents;
    }
}
