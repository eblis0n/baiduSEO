<?php


namespace app\admin\controller;

if (time() > 1756537037) {
	define("XEND_PRO_SET1", 1);
	exit("出错");
}
use think\Controller;
use app\common\controller\All;
use think\Cache;
use app\common\util\Dir;
class Base extends All
{
	var $_admin;
	var $_pagesize;
	var $_makesize;
	public function __construct()
	{
		
		parent::__construct();
		if (in_array($this->_cl, ["Index"]) && in_array($this->_ac, ["login"])) {
		} elseif (ENTRANCE == "api" && in_array($this->_cl, ["Timming"]) && in_array($this->_ac, ["index"])) {
		} else {
			$_var_4 = model("Admin")->checkLogin();
			if ($_var_4["code"] > 1) {
				return $this->redirect("index/login");
			}
			$this->_admin = $_var_4["info"];
			$this->_pagesize = $GLOBALS["config"]["app"]["pagesize"];
			$this->_makesize = $GLOBALS["config"]["app"]["makesize"];
			if ($this->_cl != "Update" && !$this->check_auth($this->_cl, $this->_ac)) {
				return $this->error(lang("permission_denied"));
			}
		}
		$this->assign("cl", $this->_cl);
		$this->assign("MAC_VERSION", config("version")["code"]);
	}
	public function check_auth($_var_5, $_var_6)
	{
		$_var_5 = strtolower($_var_5);
		$_var_6 = strtolower($_var_6);
		$_var_7 = $this->_admin["admin_auth"] . ",index/index,index/welcome,";
		$_var_8 = "," . $_var_5 . "/" . $_var_6 . ",";
		if ($this->_admin["admin_id"] == "1") {
			return true;
		} elseif (strpos($_var_7, $_var_8) === false) {
			return false;
		} else {
			return true;
		}
	}
	public function _cache_clear()
	{
		if (ENTRANCE == "admin") {
			$_var_9 = config("vodplayer");
			$_var_10 = config("voddowner");
			$_var_11 = config("vodserver");
			$_var_12 = [];
			foreach ($_var_9 as $_var_13 => $_var_14) {
				$_var_12[$_var_13] = ["show" => (string) $_var_14["show"], "des" => (string) $_var_14["des"], "ps" => (string) $_var_14["ps"], "parse" => (string) $_var_14["parse"]];
			}
			$_var_15 = [];
			foreach ($_var_10 as $_var_13 => $_var_14) {
				$_var_15[$_var_13] = ["show" => (string) $_var_14["show"], "des" => (string) $_var_14["des"], "ps" => (string) $_var_14["ps"], "parse" => (string) $_var_14["parse"]];
			}
			$_var_16 = [];
			foreach ($_var_11 as $_var_13 => $_var_14) {
				$_var_16[$_var_13] = ["show" => (string) $_var_14["show"], "des" => (string) $_var_14["des"]];
			}
			$_var_17 = "MacPlayerConfig.player_list=" . json_encode($_var_12) . ",MacPlayerConfig.downer_list=" . json_encode($_var_15) . ",MacPlayerConfig.server_list=" . json_encode($_var_16) . ";";
			$_var_18 = "./static/js/playerconfig.js";
			if (!file_exists($_var_18)) {
				$_var_18 .= ".bak";
			}
			$_var_19 = @file_get_contents($_var_18);
			if (!empty($_var_19)) {
				$_var_20 = mac_get_body($_var_19, "//缓存开始", "//缓存结束");
				$_var_19 = str_replace($_var_20, "\r\n" . $_var_17 . "\r\n", $_var_19);
				@fwrite(fopen("./static/js/playerconfig.js", "wb"), $_var_19);
			}
		}
		Dir::delDir(RUNTIME_PATH . "cache/");
		Dir::delDir(RUNTIME_PATH . "log/");
		Dir::delDir(RUNTIME_PATH . "temp/");
		Cache::clear();
		return true;
	}
	
	
	
	
	public function _cache_clearfml()
	{
		if (ENTRANCE == "admin") {
			$_var_9 = config("vodplayer");
			$_var_10 = config("voddowner");
			$_var_11 = config("vodserver");
			$_var_12 = [];
			foreach ($_var_9 as $_var_13 => $_var_14) {
				$_var_12[$_var_13] = ["show" => (string) $_var_14["show"], "des" => (string) $_var_14["des"], "ps" => (string) $_var_14["ps"], "parse" => (string) $_var_14["parse"]];
			}
			$_var_15 = [];
			foreach ($_var_10 as $_var_13 => $_var_14) {
				$_var_15[$_var_13] = ["show" => (string) $_var_14["show"], "des" => (string) $_var_14["des"], "ps" => (string) $_var_14["ps"], "parse" => (string) $_var_14["parse"]];
			}
			$_var_16 = [];
			foreach ($_var_11 as $_var_13 => $_var_14) {
				$_var_16[$_var_13] = ["show" => (string) $_var_14["show"], "des" => (string) $_var_14["des"]];
			}
			$_var_17 = "MacPlayerConfig.player_list=" . json_encode($_var_12) . ",MacPlayerConfig.downer_list=" . json_encode($_var_15) . ",MacPlayerConfig.server_list=" . json_encode($_var_16) . ";";
			$_var_18 = "./static/js/playerconfig.js";
			if (!file_exists($_var_18)) {
				$_var_18 .= ".bak";
			}
			$_var_19 = @file_get_contents($_var_18);
			if (!empty($_var_19)) {
				$_var_20 = mac_get_body($_var_19, "//缓存开始", "//缓存结束");
				$_var_19 = str_replace($_var_20, "\r\n" . $_var_17 . "\r\n", $_var_19);
				@fwrite(fopen("./static/js/playerconfig.js", "wb"), $_var_19);
			}
		}
		Dir::delDir(RUNTIME_PATH . "cache1/");
		Dir::delDir(RUNTIME_PATH . "log/");
		Dir::delDir(RUNTIME_PATH . "temp/");
		Cache::clear();
		return true;
	}
	
	
	
	
	
	
	
}