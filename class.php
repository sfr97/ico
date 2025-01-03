<?
/*
    一些常用方法，不懂勿动
*/
class Moonset{
    
    /**
     * 取格式 供方法2使用
     */
    public static function format($url){
        $format_array = array("ico","png","jpg","gif");
        for ($i = 0; $i < count($format_array); $i++) {
             if(strpos($url,".".$format_array[$i]) > -1){
                 $_format = $format_array[$i];
             }
        }
        $d3 = self::get($url);
        if(strpos($d3,"<title>") > -1 or $d3==""){
            return false;
        }else{
            self::_output_img($d3,$_format);
        }
    }
    
    /**
     * 输出图像
     */
    public static function _output_img($data,$format){
        if ($format=="jpg") {
            $format = "x-jpg";
        }elseif($format=="ico"){
            $format = "x-icon";
        }
        header('Content-type: image/'.$format);
        echo $data;
        exit;
    }
    
    /**
     * 取出中间文本
     */
    public static function _get_substr($str, $leftStr, $rightStr){
        $left = strpos($str, $leftStr);
        //echo '左边:'.$left;
        $right = strpos($str, $rightStr,$left);
        //echo '<br>右边:'.$right;
        if($left < 0 or $right < $left) return '';
        return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
    }
    
    /**
     * 将链接格式化，只留根域名，便于获取ico
     */
    public static function _clear_url($url){
        preg_match_all('#((http|https)://)?((\w)+(-)?(\.)?)+(:[0-9]{1,4})?#',$url,$result);
        return $result[0][0];
        //$url = get($url,true);
        $url = self::_get_substr($url,"Location: ","\n");
        $url = str_replace(array("\r\n", "\r", "\n"), "", $url);
    }
    
    /**
     * 验证域格式是否正确
     */
    public static function isUrl($s){  
        return preg_match('/^(http[s]?:\/\/)?'.  
            '(([0-9]{1,3}\.){3}[0-9]{1,3}'. // IP形式的URL- 199.194.52.184
            '|'. // 允许IP和DOMAIN（域名）  
            '([0-9a-z_!~*\'()-]+\.)*'. // 三级域验证- www.  
            '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. // 二级域验证  
            '[a-z]{2,6})'.  // 顶级域验证.com or .museum  
            '(:[0-9]{1,4})?'.  // 端口- :80  
            '((\/\?)|'.  // 如果含有文件对文件部分进行校验  
            '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/',  
            $s) == 1;  
    }
    
    /**
     * 简版curl类, 可用于简单的内容获取和访问判断
     * 参数2为true只返回状态, 不含正文, 减少宽带和时间
     */
    public static function get($url,$head=false){ 
        $headers = array(
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:97.0) Gecko/20100101 Firefox/97.0"
        );
        $ch = curl_init();											   // 创建一个curl连接
        curl_setopt($ch,CURLOPT_URL, $url);                            // 提交地址   
        curl_setopt($ch,CURLOPT_TIMEOUT,5);
        curl_setopt($ch,CURLINFO_HEADER_OUT,true);
        curl_setopt($ch, CURLOPT_HEADER, 0);                           //自动设置头部
        curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);             //设置请求头
        if(preg_match("#https#",$url)==1){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);              // 对认证证书来源的检查  
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);             // 从证书中检查SSL加密算法是否存在  
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);				  // 将返回值储存到变量
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);			      // 设置等待时长 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);                  // 使用自动跳转 
        if ($head==true) {
            // 返回 response_header, 该选项非常重要,如果不为 true, 只会获得响应的正文
            curl_setopt($ch, CURLOPT_HEADER, true);
            // 是否不需要响应的正文,为了节省带宽及时间,在只需要响应头的情况下可以不要正文
            curl_setopt($ch, CURLOPT_NOBODY, true);}
        $handles = curl_exec($ch);                                      // 执行curl会话
        curl_close($ch);                                                // 释放curl
        return $handles;                                                // 返回数据
        //echo 'Curl error: ' . curl_error($ch);                        // 输出curl错误信息
    }
}

