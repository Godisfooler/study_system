<?php
namespace Api\Model;
use Think\Model;

class CurlModel extends Model {
    private $timeoutMs = 90000;
    private $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.130 Safari/537.36';
    private $cookisString = '';
    public function __construct(){
    }
    /*
     * 单线程抓取
     */
    public function getUrlHtml($url,$post,$timeoutMs='',$cookisString='',$header = '',$CURLOPT_REFERER='',$proxy='',$is_gzip = ''){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//https
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//https
        curl_setopt($ch, CURLOPT_URL, $url);
        if(!empty($header)){
        	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        
        if(!empty($CURLOPT_REFERER)) {
        	curl_setopt($ch, CURLOPT_REFERER, $CURLOPT_REFERER);
        }
        
        if(!empty($proxy)) {
            foreach($proxy as $key => $val) {
                 curl_setopt($ch, $key, $val);
            }
        }
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//         curl_setopt($ch, CURLOPT_ENCODING, "");
        if(!empty($timeoutMs)){
        	curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeoutMs);//10秒未响应就断开连接
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        
        if(!empty($is_gzip)){
        	curl_setopt($ch, CURLOPT_ENCODING,'gzip');//解压
        }
        
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
    
    /*
     * 多线程抓取
     */
    public function multiGetUrlHtml($urls,$post = array(),$flag='') {
    	$mh = curl_multi_init();
    
    	foreach ($urls as $i => $url) {
    		$conn[$i] = curl_init($url);
    		curl_setopt($conn[$i], CURLOPT_SSL_VERIFYPEER, FALSE);//https
    		curl_setopt($conn[$i], CURLOPT_SSL_VERIFYHOST, FALSE);//https
    		curl_setopt($conn[$i], CURLOPT_URL, $url);
    		curl_setopt($conn[$i], CURLOPT_HEADER, 0);
    		curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($conn[$i], CURLOPT_FOLLOWLOCATION, 1);
    		curl_setopt($conn[$i], CURLOPT_ENCODING, "");
    		if(!empty($this->timeoutMs))
    			curl_setopt($conn[$i], CURLOPT_TIMEOUT_MS, $this->timeoutMs);//10秒未响应就断开连接
    			if(!empty($this->userAgent))
    				curl_setopt($conn[$i], CURLOPT_USERAGENT, $this->userAgent);//客户端设置
    
    				if(isset($post) && !empty($post) && !empty($post[$i])) {
    					curl_setopt($conn[$i], CURLOPT_POST, 1);//post方式提交
    					if(is_array($post[$i]))
    						curl_setopt($conn[$i], CURLOPT_POSTFIELDS, http_build_query($post[$i]));//要提交的信息
    						else
    							curl_setopt($conn[$i], CURLOPT_POSTFIELDS, $post[$i]);//要提交的信息
    				}
    
    				curl_multi_add_handle ($mh,$conn[$i]);
    	}
    	 
    	do {
    		curl_multi_exec($mh,$active);
    	} while ($active);
    	 
    	foreach ($urls as $i => $url) {
    		$data[$i] = curl_multi_getcontent($conn[$i]); // 获得爬取的代码字符串
    	}
    	 
    	foreach ($urls as $i => $url) {
    		curl_multi_remove_handle($mh,$conn[$i]);
    		curl_close($conn[$i]);
    	}
    	 
    	curl_multi_close($mh);//关闭多线程
    
    	return $data;
    }
    
}