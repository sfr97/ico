<?
/*

    在线获取ICO - PHP版   
    
    同样支持 ico,png,jpg,gif 格式的ICO
    
    ---- 已知Bug ----
    由于处理逻辑是，先获取根目录下的favicon.ico，再抓取重新声明ico的
    所以会导致类似于 https://aiqicha.baidu.com/ 既存在 favicon.ico  同时又重新声明ico的站
    
    ---- 新版 ----
    测试地址：http://icon.tudo.cn/?url=
    已更新缓存机制，日志记录，即将支持取标题+纯色背景（功能测试中...）
    欢迎测试反馈，稳定后或将考虑开放
    使用疑问：9869403
    
        --------> 月落 | www.Moonset.me  2022/03/11 

*/

include_once "class.php"; //加载类

$url = preg_replace('/([\x80-\xff]*)/i','',$_GET["url"]); // 清除链接中的中文

if (!Moonset::isUrl($url)){
    echo "不是合格的URL，格式：http://icon.tudo.cn/?url=https://www.zhuayuya.com/<br>
          BUG反馈：im#moonet.me (#换成@)<br>
          支持https";
    exit;
};

$url = Moonset::_clear_url($url); // 清除格式

getIcon_1($url); // 获取方式1
getIcon_2($url); // 获取方式2
defaultIcon(); //都获取不到则调用默认ICO

// 取ICO方式1
function getIcon_1($url){
    $urls = $url."/favicon.ico";
    $d = Moonset::get($urls);
    if(strstr($d,"<title>") or $d==""){
        return false;
    }else{
        header('Content-Type: image/x-icon');
        echo $d;exit;
    }
}

// 取ICO方式2
function getIcon_2($url){
    $d = Moonset::get($url);
    preg_match_all("#<link((.)*?rel(.)*?icon(.)*?)?(href=(.)*?(.)*?)?((.)*?rel=(.)*?icon(.)*?)?>#",$d,$data);
    for ($i = 0; $i < sizeof($data[0]); $i++) {
         if(strpos($data[0][$i],'href=""') > -1){
             continue;
         }else{
            $urls = Moonset::_get_substr($data[0][$i], 'href="', '"');
            $urls = Moonset::_get_substr("##".$urls,"##",'"');
            break;
         }
    }
    if(strpos($urls,"http://") > -1 or strpos($urls,"https://") > -1){
        $d2 = $urls;
    }elseif(strpos($urls,"http") == false and strpos($urls,"//") > -1){
        $d2 = "http:".$urls;
    }else{
        $urls = str_replace("../","",$urls);
        $urls = str_replace("./","",$urls);
        $d2 = $url."/".$urls;
    };
    Moonset::format($d2);// 调用函数
}

// 默认ico
function defaultIcon(){
    # base64编码图片，要把前面的声明去掉才能解码！！
    $icodata = "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";
    
    header('Content-Type: image/x-icon');
    echo base64_decode($icodata);
}
