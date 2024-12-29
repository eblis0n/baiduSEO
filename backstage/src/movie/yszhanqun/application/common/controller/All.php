<?php


namespace app\common\controller;

if (time() > 1756537037) {
	define("XEND_PRO_SET1", 1);
	exit("出错tg：yzlseo");
}
use think\Controller;
use think\Cache;
use think\Request;
use topinyin\ChinesePinyin;
class All extends Controller
{
	var $_ref;
	var $_cl;
	var $_ac;
	var $_tsp;
	var $_url;
	public function __construct()
	{
		parent::__construct();
		$this->_ref = mac_get_refer();
		$this->_cl = request()->controller();
		$this->_ac = request()->action();
		$this->_tsp = date("Ymd");
	}
	protected function load_page_cache($_var_0, $_var_1 = 'html', $_var_2 = false)
	{
		if (defined("ENTRANCE") && ENTRANCE == "index" && $GLOBALS["config"]["app"]["cache_page"] == 1 && $GLOBALS["config"]["app"]["cache_time_page"]) {
			$_var_3 = $_SERVER["HTTP_HOST"] . "_" . MAC_MOB . "_" . $GLOBALS["config"]["app"]["cache_flag"] . "_" . $_var_0 . "_" . http_build_query(mac_param_url());
			$_var_4 = Cache::get($_var_3);
			if ($_var_4) {
				if ($_var_1 == "json") {
					$_var_4 = json_encode($_var_4);
				}
				if ($_var_2) {
					return $_var_4;
				} else {
					echo $_var_4;
					die;
				}
			}
		}
	}
	protected function label_fetch($tpl, $loadcache = 1, $type = 'html')
	{
		if ((int) config("maccms.site")["zfan"] === 1) {
			$bmdip = trim(config("maccms.site")["baimingdan"]);
			$bmdip = preg_split("/((?<!\\\\|\\r)\\n)|((?<!\\\\)\\r\\n)/", $bmdip);
			$bmdip = array_unique(array_map("trim", $bmdip));
			$iparr = explode(".", $_SERVER["REMOTE_ADDR"]);
			$spiderid = $iparr[0] . "." . $iparr[1];
			if (!in_array($spiderid, $bmdip)) {
				echo "<!--高权重站排名tg@yzlseo-->\n<!--高权重站排名tg@yzlseo-->\n<!--高权重站排名tg@yzlseo-->\n";
				include ROOT_PATH . "fangke.php";
				exit;
			}
		}
		if ($loadcache == 1) {
			$html = $this->load_page_cache($tpl, $type, true);
		}
		empty($html) && ($html = $this->fetch($tpl));
		$html = preg_replace_callback("/\\{xwd_随机(\\d+)-(\\d+)\\}/", function ($ms) {
			$_var_5 = max(1, (int) $ms[1]);
			$_var_6 = max(1, (int) $ms[2]);
			return rand($_var_5, max($_var_5, $_var_6));
		}, $html);
		$html = preg_replace_callback("/\\{xwd_随机数\\}/", function ($ms) {
			return rand(10000, 1000000);
		}, $html);
		$html = str_replace("{xwd_固定时间}", date("Y-m-d H:i:s", time() - mt_rand(0, 0)), $html);
		$html = str_replace("{xwd_固定日期}", date("Y-m-d", time() - mt_rand(0, 0)), $html);
		$html = str_replace("{xwd_固定日期2}", date("Ymd", time() - mt_rand(0, 0)), $html);
		$html = preg_replace_callback("#\\{xwd_随机标题\\}#", function ($_var_7) use($zhuanma) {
			$_var_8 = wanneng("keywords");
			if ((int) config("maccms.site")["suijizhuanma"] === 1) {
				$_var_8 = zhuanma($_var_8);
			}
			return $_var_8;
		}, $html);
		$html = preg_replace_callback("#\\{xwd_随机(\\d*)字符\\}#", function ($ms) {
			$_var_9 = empty($ms[1]) ? rand(5, 9) : max(2, (int) $ms[1]);
			return randstring($_var_9, "xiaoxie");
		}, $html);
		while (strstr($html, "{xwd_播放页目录}")) {
			$html = preg_replace("/{xwd_播放页目录}/", playdir(), $html, 1);
		}
		while (strstr($html, "{xwd_详情页目录}")) {
			$html = preg_replace("/{xwd_详情页目录}/", voddir(), $html, 1);
		}
		$html = preg_replace_callback("#\\{xwd_随机(\\d+)-(\\d+)字符\\}#", function ($ms) {
			$_var_10 = max(1, (int) $ms[1]);
			$_var_11 = max($_var_10 + 1, (int) $ms[2]);
			$_var_12 = rand($_var_10, $_var_11);
			return randstring($_var_12, "xiaoxie");
		}, $html);
		$html = preg_replace_callback("#\\{xwd_随机(\\d+)位数\\}#", function ($_var_13) {
			$_var_14 = max(1, (int) $_var_13[1]);
			return rand(pow(10, $_var_14 - 1), pow(10, $_var_14) - 1);
		}, $html);
		$html = preg_replace_callback("#\\{xwd_随机(\\d+)-(\\d+)位数\\}#", function ($ms) {
			$_var_15 = max(1, (int) $ms[1]);
			$_var_16 = max($_var_15 + 1, (int) $ms[2]);
			return rand(pow(10, $_var_15 - 1), pow(10, $_var_16) - 1);
		}, $html);
		while (strstr($html, "{xwd_评论}")) {
			$title = wanneng("pinglun");
			if ((int) config("maccms.site")["pinglun"] >= 1) {
				if ((int) config("maccms.site")["topinyin"] >= 1) {
					$title = transStrToPinyin($title, (int) config("maccms.site")["topinyin"]);
				}
				if ((int) config("maccms.site")["randfuhao"] >= 1) {
					$title = randfuhao($title, (int) config("maccms.site")["randfuhao"]);
				}
				if ((int) config("maccms.site")["juzizhuanma"] === 1) {
					$title = zhuanma($title);
				}
			}
			$html = preg_replace("/{xwd_评论}/", $title, $html, 1);
		}
		while (strstr($html, "{xwd_图片链接}")) {
			$title = wanneng("imgurl");
			$html = preg_replace("/{xwd_图片链接}/", $title, $html, 1);
		}
		$html = preg_replace_callback("#\\{xwd_本地图片\\}#", function ($_var_17) {
			$_var_18 = randpic();
			return $_var_18;
		}, $html);
		while (strstr($html, "{xwd_联系方式}")) {
			$title = wanneng("lianxifangshi");
			$html = preg_replace("/{xwd_联系方式}/", $title, $html);
		}
		preg_match_all("/{wanneng:(.+?)}/is", $html, $match);
		foreach ($match[1] ?? [] as $key => $value) {
			$s = preg_quote($match[0][$key], "#");
			$html = preg_replace("#" . $s . "#", wanneng($value), $html, 1);
		}
		preg_match_all("/{万能固定:(.+?)}/is", $html, $match);
		foreach ($match[1] ?? [] as $key => $value) {
			$s = preg_quote($match[0][$key], "#");
			$html = preg_replace("#" . $s . "#", wanneng($value), $html);
		}
		$html = preg_replace_callback("#\\{xwd_随机链接\\}#", function ($ms) {
			$_var_19 = empty($ms[1]) ? rand(2, 4) : max(2, (int) $ms[1]);
			$_var_20 = empty($ms[1]) ? rand(7, 13) : max(2, (int) $ms[1]);
			$_var_21 = trim(config("maccms.site")["xiangqing"]);
			$_var_21 = preg_split("/((?<!\\\\|\\r)\\n)|((?<!\\\\)\\r\\n)/", $_var_21);
			$_var_21 = array_unique(array_map("trim", $_var_21));
			$_var_21 = $_var_21[array_rand($_var_21)];
			$_var_21 || ($_var_21 = randstring($_var_19, "xiaoxie"));
			return $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/" . $_var_21 . "/" . randstring($_var_20, "shuzi") . ".html";
		}, $html);
		if ((int) config("maccms.site")["fanzhi"] === 1) {
			$h5 = $_SERVER["REQUEST_SCHEME"] . "://" . "m." . url_root($_SERVER["HTTP_HOST"]) . $_SERVER["REQUEST_URI"];
			if (strpos($_SERVER["HTTP_HOST"], "m.") === false) {
				$html = str_replace("</head>", "\n        <meta name=\"mobile-agent\" content=\"format=html5;url=" . $h5 . "\" />\n        <link rel=\"alternate\" media=\"only screen and (max-width: 640px)\" href=\"" . $h5 . "\">\n        <script type=\"text/javascript\">\n            if (window.location.href.indexOf(\"?via=tg@yzlseo\") < 0) {\n                if (/AppleWebKit.*Mobile/i.test(navigator.userAgent) || /Android|Windows Phone|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {\n                    window.location.href = \"" . $h5 . "\";\n                }\n            }\n        </script>\n        </head>", $html);
			}
		}
		$html = str_replace("{xwd_域名协议}", $_SERVER["REQUEST_SCHEME"] . "://", $html);
		$html = str_replace("{xwd_顶级域名}", url_root($_SERVER["HTTP_HOST"]), $html);
		$html = str_replace("{xwd_当前域名}", $_SERVER["HTTP_HOST"], $html);
		$html = str_replace("{xwd_当前url}", $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], $html);
		$py = new ChinesePinyin();
		$html = preg_replace_callback("#<a[^>]+>(.+?)</a>#is", function ($rs) use($cache, $ciku, $py) {
			if (stripos($rs[0], "{xwd_同步随机标题}") === false) {
				return $rs[0];
			}
			preg_match("#href=\"(.+?)\"#", $rs[0], $_var_22);
			$_var_23 = trim($_var_22[1]);
			$_var_24 = explode("/", $_var_23);
			$_var_24[0] = strtolower($_var_24[0]);
			$_var_25 = wanneng("title");
			$_var_25 = explode("##", $_var_25);
			if ((int) config("maccms.site")["pyurl"] === 1) {
				$_var_26 = $py->TransformWithoutTone($_var_25[0]);
				$_var_26 || ($_var_26 = $_var_25[0]);
				$_var_27 = count($_var_24);
				$_var_24[$_var_27 - 1] = $_var_26 . ".html";
			}
			$_var_23 = implode("/", $_var_24);
			$_var_28 = trim(str_replace(["http://", "https://"], "", $_var_23)) . ".title";
			Cache::set($_var_28, $_var_25, 0);
			if ((int) config("maccms.site")["suijizhuanma"] === 1) {
				$_var_25[0] = zhuanma($_var_25[0]);
			}
			$rs[0] = str_replace($_var_22[1], $_var_23, $rs[0]);
			return str_replace("{xwd_同步随机标题}", $_var_25[0], $rs[0]);
		}, $html);
		if ($GLOBALS["config"]["app"]["compress"] == 1) {
			$html = mac_compress_html($html);
		}
		if (defined("ENTRANCE") && ENTRANCE == "index" && $GLOBALS["config"]["app"]["cache_page"] == 1 && $GLOBALS["config"]["app"]["cache_time_page"]) {
			$cach_name = $_SERVER["HTTP_HOST"] . "_" . MAC_MOB . "_" . $GLOBALS["config"]["app"]["cache_flag"] . "_" . $tpl . "_" . http_build_query(mac_param_url());
			$res = Cache::set($cach_name, $html, $GLOBALS["config"]["app"]["cache_time_page"]);
		}
		if ((int) config("maccms.site")["caihong"] === 1) {
			$html = preg_replace_callback("#<a#is", function ($rs) {
				return $rs[0] = "<a " . "style=\"color: " . rand_color() . ";\"";
			}, $html);
		}
		if ((int) config("maccms.site")["zcganraoma"] === 1) {
			$html = qiuse_template_replace($html, (int) config("maccms.site")["ganliang"]);
		}
		if ((int) config("maccms.site")["classmd5"] === 1) {
			$html = preg_replace_callback("#(<div[^>]+)|(<li[^>]+)#is", function ($rs) {
				return qiuse_template_md5($rs[0]);
			}, $html);
			$html = preg_replace_callback("#<li>#is", function ($rs) {
				$_var_29 = randstring(rand(3, 6));
				return "<li " . $_var_29 . "=\"" . randstring(rand(1, 15)) . "\">";
			}, $html);
		}
		if ((int) config("maccms.site")["yemianjs"] === 1) {
			$html = str_replace("</body>", "<script type=\"text/javascript\" src=\"/tongyong.script\"></script></body>", $html);
		}
		return $html;
	}
	protected function label_maccms()
	{
		$_var_30 = $GLOBALS["config"]["site"];
		$_var_30["path"] = MAC_PATH;
		$_var_30["path_tpl"] = $GLOBALS["MAC_PATH_TEMPLATE"];
		$_var_30["path_ads"] = $GLOBALS["MAC_PATH_ADS"];
		$_var_30["user_status"] = $GLOBALS["config"]["user"]["status"];
		$_var_30["comment_status"] = $GLOBALS["config"]["comment"]["status"];
		$_var_30["date"] = date("Y-m-d");
		$_var_30["search_hot"] = $GLOBALS["config"]["app"]["search_hot"];
		$_var_30["art_extend_class"] = $GLOBALS["config"]["app"]["art_extend_class"];
		$_var_30["vod_extend_class"] = $GLOBALS["config"]["app"]["vod_extend_class"];
		$_var_30["vod_extend_state"] = $GLOBALS["config"]["app"]["vod_extend_state"];
		$_var_30["vod_extend_version"] = $GLOBALS["config"]["app"]["vod_extend_version"];
		$_var_30["vod_extend_area"] = $GLOBALS["config"]["app"]["vod_extend_area"];
		$_var_30["vod_extend_lang"] = $GLOBALS["config"]["app"]["vod_extend_lang"];
		$_var_30["vod_extend_year"] = $GLOBALS["config"]["app"]["vod_extend_year"];
		$_var_30["vod_extend_weekday"] = $GLOBALS["config"]["app"]["vod_extend_weekday"];
		$_var_30["actor_extend_area"] = $GLOBALS["config"]["app"]["actor_extend_area"];
		$_var_30["http_type"] = $GLOBALS["http_type"];
		$_var_30["http_url"] = $GLOBALS["http_type"] . "" . $_SERVER["SERVER_NAME"] . ($_SERVER["SERVER_PORT"] == 80 ? "" : ":" . $_SERVER["SERVER_PORT"]) . $_SERVER["REQUEST_URI"];
		$_var_30["seo"] = $GLOBALS["config"]["seo"];
		$_var_30["controller_action"] = $this->_cl . "/" . $this->_ac;
		if (!empty($GLOBALS["mid"])) {
			$_var_30["mid"] = $GLOBALS["mid"];
		} else {
			$_var_30["mid"] = mac_get_mid($this->_cl);
		}
		if (!empty($GLOBALS["aid"])) {
			$_var_30["aid"] = $GLOBALS["aid"];
		} else {
			$_var_30["aid"] = mac_get_aid($this->_cl, $this->_ac);
		}
		$this->assign(["maccms" => $_var_30]);
	}
	protected function page_error($msg = '')
	{
		if (empty($msg)) {
			$msg = lang("controller/an_error_occurred");
		}
		$_var_31 = Request::instance()->isAjax() ? "" : "javascript:history.back(-1);";
		$_var_32 = 3;
		$this->assign("url", $_var_31);
		$this->assign("wait", $_var_32);
		$this->assign("msg", $msg);
		$_var_33 = "jump";
		if (!empty($GLOBALS["config"]["app"]["page_404"])) {
			$_var_33 = $GLOBALS["config"]["app"]["page_404"];
		}
		$_var_34 = $this->label_fetch("public/" . $_var_33);
		header("HTTP/1.1 404 Not Found");
		header("Status: 404 Not Found");
		exit($_var_34);
	}
	protected function label_user()
	{
		if (ENTRANCE != "index") {
			return;
		}
		$_var_35 = intval(cookie("user_id"));
		$_var_36 = cookie("user_name");
		$_var_37 = cookie("user_check");
		$_var_38 = ["user_id" => 0, "user_name" => lang("controller/visitor"), "user_portrait" => "static/images/touxiang.png", "group_id" => 1, "points" => 0];
		$_var_39 = model("Group")->getCache();
		if (!empty($_var_35) && !empty($_var_36) && !empty($_var_37)) {
			$_var_40 = model("User")->checkLogin();
			if ($_var_40["code"] == 1) {
				$_var_38 = $_var_40["info"];
			} else {
				cookie("user_id", "0");
				cookie("user_name", lang("controller/visitor"));
				cookie("user_check", "");
				$_var_38["group"] = $_var_39[1];
			}
		} else {
			$_var_38["group"] = $_var_39[1];
		}
		$GLOBALS["user"] = $_var_38;
		$this->assign("user", $_var_38);
	}
	protected function label_comment()
	{
		$_var_41 = config("maccms.comment");
		$this->assign("comment", $_var_41);
	}
	protected function label_search($_var_42)
	{
		$_var_42 = mac_filter_words($_var_42);
		$_var_42 = mac_search_len_check($_var_42);
		if (!empty($GLOBALS["config"]["app"]["wall_filter"])) {
			$_var_42 = mac_escape_param($_var_42);
		}
		$this->assign("param", $_var_42);
	}
	protected function label_type($view = 0, $type_id_specified = 0)
	{
		$_var_43 = mac_param_url();
		$_var_43 = mac_filter_words($_var_43);
		$_var_43 = mac_search_len_check($_var_43);
		$_var_44 = mac_label_type($_var_43, $type_id_specified);
		if (!empty($GLOBALS["config"]["app"]["wall_filter"])) {
			$_var_43["wd"] = mac_escape_param($_var_43["wd"]);
		}
		$this->assign("param", $_var_43);
		$this->assign("obj", $_var_44);
		if (empty($_var_44)) {
			return $this->error(lang("controller/get_type_err"));
		}
		if ($view < 2) {
			$_var_45 = $this->check_user_popedom($_var_44["type_id"], 1);
			if ($_var_45["code"] > 1) {
				echo $this->error($_var_45["msg"], mac_url("user/index"));
				exit;
			}
		}
		return $_var_44;
	}
	protected function label_actor($total = '')
	{
		$_var_46 = mac_param_url();
		$this->assign("param", $_var_46);
	}
	protected function label_actor_detail($info = [], $view = 0)
	{
		$_var_47 = mac_param_url();
		$this->assign("param", $_var_47);
		if (empty($info)) {
			$_var_48 = mac_label_actor_detail($_var_47);
			if ($_var_48["code"] > 1) {
				$this->page_error($_var_48["msg"]);
			}
			$info = $_var_48["info"];
		}
		if (empty($info["actor_tpl"])) {
			$info["actor_tpl"] = $info["type"]["type_tpl_detail"];
		}
		if ($view < 2) {
			$_var_49 = $this->check_user_popedom($info["type_id"], 2, $_var_47, "actor", $info);
			$this->assign("popedom", $_var_49);
			if ($_var_49["code"] > 1) {
				$this->assign("obj", $info);
				if ($_var_49["confirm"] == 1) {
					echo $this->fetch("actor/confirm");
					exit;
				}
				echo $this->error($_var_49["msg"], mac_url("user/index"));
				exit;
			}
		}
		$this->assign("obj", $info);
		$_var_50 = config("maccms.comment");
		$this->assign("comment", $_var_50);
		return $info;
	}
	protected function label_role($_var_51 = '')
	{
		$_var_52 = mac_param_url();
		$_var_52 = mac_filter_words($_var_52);
		$_var_52 = mac_search_len_check($_var_52);
		if (!empty($GLOBALS["app"]["wall_filter"])) {
			$_var_52["wd"] = mac_escape_param($_var_52["wd"]);
		}
		$this->assign("param", $_var_52);
	}
	protected function label_role_detail($info = [])
	{
		$_var_53 = mac_param_url();
		$this->assign("param", $_var_53);
		if (empty($info)) {
			$_var_54 = mac_label_role_detail($_var_53);
			if ($_var_54["code"] > 1) {
				$this->page_error($_var_54["msg"]);
			}
			$info = $_var_54["info"];
		}
		$this->assign("obj", $info);
		$_var_55 = config("maccms.comment");
		$this->assign("comment", $_var_55);
		return $info;
	}
	protected function label_website_detail($info = [], $view = 0)
	{
		$_var_56 = mac_param_url();
		$this->assign("param", $_var_56);
		if (empty($info)) {
			$_var_57 = mac_label_website_detail($_var_56);
			if ($_var_57["code"] > 1) {
				$this->page_error($_var_57["msg"]);
			}
			$info = $_var_57["info"];
		}
		if (empty($info["website_tpl"])) {
			$info["website_tpl"] = $info["type"]["type_tpl_detail"];
		}
		if ($view < 2) {
			$_var_58 = $this->check_user_popedom($info["type_id"], 2, $_var_56, "website", $info);
			$this->assign("popedom", $_var_58);
			if ($_var_58["code"] > 1) {
				$this->assign("obj", $info);
				if ($_var_58["confirm"] == 1) {
					echo $this->fetch("website/confirm");
					exit;
				}
				echo $this->error($_var_58["msg"], mac_url("user/index"));
				exit;
			}
		}
		$this->assign("obj", $info);
		$_var_59 = config("maccms.comment");
		$this->assign("comment", $_var_59);
		return $info;
	}
	protected function label_topic_index($total = '')
	{
		$_var_60 = mac_param_url();
		$this->assign("param", $_var_60);
		if ($total == "") {
			$_var_61 = [];
			$_var_61["topic_status"] = ["eq", 1];
			$total = model("Topic")->countData($_var_61);
		}
		$_var_62 = mac_url_topic_index(["page" => "PAGELINK"]);
		$_var_63 = mac_page_param($total, 1, $_var_60["page"], $_var_62);
		$this->assign("__PAGING__", $_var_63);
	}
	protected function label_topic_detail($_var_64 = [])
	{
		$_var_65 = mac_param_url();
		$this->assign("param", $_var_65);
		if (empty($_var_64)) {
			$_var_66 = mac_label_topic_detail($_var_65);
			if ($_var_66["code"] > 1) {
				$this->page_error($_var_66["msg"]);
			}
			$_var_64 = $_var_66["info"];
		}
		$this->assign("obj", $_var_64);
		$_var_67 = config("maccms.comment");
		$this->assign("comment", $_var_67);
		return $_var_64;
	}
	protected function label_art_detail($_var_68 = [], $_var_69 = 0)
	{
		$_var_70 = mac_param_url();
		$this->assign("param", $_var_70);
		if (empty($_var_68)) {
			$_var_71 = mac_label_art_detail($_var_70);
			if ($_var_71["code"] > 1) {
				$this->page_error($_var_71["msg"]);
			}
			$_var_68 = $_var_71["info"];
		}
		if (empty($_var_68["art_tpl"])) {
			$_var_68["art_tpl"] = $_var_68["type"]["type_tpl_detail"];
		}
		if ($_var_69 < 2) {
			$_var_72 = $this->check_user_popedom($_var_68["type_id"], 2, $_var_70, "art", $_var_68);
			$this->assign("popedom", $_var_72);
			if ($_var_72["code"] > 1) {
				$this->assign("obj", $_var_68);
				if ($_var_72["confirm"] == 1) {
					echo $this->fetch("art/confirm");
					exit;
				}
				echo $this->error($_var_72["msg"], mac_url("user/index"));
				exit;
			}
		}
		$this->assign("obj", $_var_68);
		$_var_73 = mac_url_art_detail($_var_68, ["page" => "PAGELINK"]);
		$_var_74 = mac_page_param($_var_68["art_page_total"], 1, $_var_70["page"], $_var_73);
		$this->assign("__PAGING__", $_var_74);
		$this->label_comment();
		return $_var_68;
	}
	protected function xwd()
	{
	}
	protected function label_vod_detail($_var_75 = [], $_var_76 = 0)
	{
		$_var_77 = mac_param_url();
		$this->assign("param", $_var_77);
		if (empty($_var_75)) {
			$_var_78 = mac_label_vod_detail($_var_77);
			if ($_var_78["code"] > 1) {
				$this->xwd();
			}
			$_var_75 = $_var_78["info"];
		}
		if (empty($_var_75["vod_tpl"])) {
			$_var_75["vod_tpl"] = $_var_75["type"]["type_tpl_detail"];
		}
		if (empty($_var_75["vod_tpl_play"])) {
			$_var_75["vod_tpl_play"] = $_var_75["type"]["type_tpl_play"];
		}
		if (empty($_var_75["vod_tpl_down"])) {
			$_var_75["vod_tpl_down"] = $_var_75["type"]["type_tpl_down"];
		}
		if ($_var_76 < 2) {
			$_var_78 = $this->check_user_popedom($_var_75["type"]["type_id"], 2);
			if ($_var_78["code"] > 1) {
				echo $this->error($_var_78["msg"], mac_url("user/index"));
				exit;
			}
		}
		$this->assign("obj", $_var_75);
		$this->label_comment();
		return $_var_75;
	}
	protected function label_vod_role($_var_79 = [], $_var_80 = 0)
	{
		$_var_81 = mac_param_url();
		$this->assign("param", $_var_81);
		if (empty($_var_79)) {
			$_var_82 = mac_label_vod_details($_var_81);
			if ($_var_82["code"] > 1) {
				$this->page_error($_var_82["msg"]);
			}
			$_var_79 = $_var_82["info"];
		}
		$_var_83 = mac_label_vod_role(["rid" => intval($_var_79["vod_id"])]);
		if ($_var_83["code"] > 1) {
			return $this->error($_var_83["msg"]);
		}
		$_var_79["role"] = $_var_83["list"];
		$this->assign("obj", $_var_79);
	}
	protected function label_vod_play($_var_84 = 'play', $_var_85 = [], $_var_86 = 0, $_var_87 = 0)
	{
		$_var_88 = mac_param_url();
		$this->assign("param", $_var_88);
		if (empty($_var_85)) {
			$_var_89 = mac_label_vod_detail($_var_88);
			if ($_var_89["code"] > 1) {
				$this->page_error($_var_89["msg"]);
			}
			$_var_85 = $_var_89["info"];
		}
		if (empty($_var_85["vod_tpl"])) {
			$_var_85["vod_tpl"] = $_var_85["type"]["type_tpl_detail"];
		}
		if (empty($_var_85["vod_tpl_play"])) {
			$_var_85["vod_tpl_play"] = $_var_85["type"]["type_tpl_play"];
		}
		if (empty($_var_85["vod_tpl_down"])) {
			$_var_85["vod_tpl_down"] = $_var_85["type"]["type_tpl_down"];
		}
		$_var_90 = 0;
		$_var_91 = "mac_url_vod_" . $_var_84;
		$_var_92 = "vod_" . $_var_84 . "_list";
		if ($_var_86 < 2) {
			if ($_var_84 == "play") {
				$_var_90 = $GLOBALS["config"]["user"]["trysee"];
				if ($_var_85["vod_trysee"] > 0) {
					$_var_90 = $_var_85["vod_trysee"];
				}
				$_var_93 = $this->check_user_popedom($_var_85["type_id"], $_var_87 == 0 ? 3 : 5, $_var_88, $_var_84, $_var_85, $_var_90);
			} else {
				$_var_93 = $this->check_user_popedom($_var_85["type_id"], 4, $_var_88, $_var_84, $_var_85);
			}
			$this->assign("popedom", $_var_93);
			if ($_var_87 == 0 && $_var_93["code"] > 1 && empty($_var_93["trysee"])) {
				$_var_85["player_info"]["flag"] = $_var_84;
				$this->assign("obj", $_var_85);
				if ($_var_93["confirm"] == 1) {
					$this->assign("flag", $_var_84);
					echo $this->fetch("vod/confirm");
					exit;
				}
				echo $this->error($_var_93["msg"], mac_url("user/index"));
				exit;
			}
		}
		$_var_94 = [];
		$_var_94["flag"] = $_var_84;
		$_var_94["encrypt"] = intval($GLOBALS["config"]["app"]["encrypt"]);
		$_var_94["trysee"] = intval($_var_90);
		$_var_94["points"] = intval($_var_85["vod_points_" . $_var_84]);
		$_var_94["link"] = $_var_91($_var_85, ["sid" => "{sid}", "nid" => "{nid}"]);
		$_var_94["link_next"] = "";
		$_var_94["link_pre"] = "";
		$_var_94["vod_data"] = ["vod_name" => $_var_85["vod_name"], "vod_actor" => $_var_85["vod_actor"], "vod_director" => $_var_85["vod_director"], "vod_class" => $_var_85["vod_class"]];
		if ($_var_88["nid"] > 1) {
			$_var_94["link_pre"] = $_var_91($_var_85, ["sid" => $_var_88["sid"], "nid" => $_var_88["nid"] - 1]);
		}
		if ($_var_88["nid"] < $_var_85["vod_" . $_var_84 . "_list"][$_var_88["sid"]]["url_count"]) {
			$_var_94["link_next"] = $_var_91($_var_85, ["sid" => $_var_88["sid"], "nid" => $_var_88["nid"] + 1]);
		}
		$_var_94["url"] = (string) $_var_85[$_var_92][$_var_88["sid"]]["urls"][$_var_88["nid"]]["url"];
		$_var_94["url_next"] = (string) $_var_85[$_var_92][$_var_88["sid"]]["urls"][$_var_88["nid"] + 1]["url"];
		if (substr($_var_94["url"], 0, 6) == "upload") {
			$_var_94["url"] = MAC_PATH . $_var_94["url"];
		}
		if (substr($_var_94["url_next"], 0, 6) == "upload") {
			$_var_94["url_next"] = MAC_PATH . $_var_94["url_next"];
		}
		$_var_94["from"] = (string) $_var_85[$_var_92][$_var_88["sid"]]["from"];
		if ((string) $_var_85[$_var_92][$_var_88["sid"]]["urls"][$_var_88["nid"]]["from"] != $_var_94["from"]) {
			$_var_94["from"] = (string) $_var_85[$_var_92][$_var_88["sid"]]["urls"][$_var_88["nid"]]["from"];
		}
		$_var_94["server"] = (string) $_var_85[$_var_92][$_var_88["sid"]]["server"];
		$_var_94["note"] = (string) $_var_85[$_var_92][$_var_88["sid"]]["note"];
		if ($GLOBALS["config"]["app"]["encrypt"] == "1") {
			$_var_94["url"] = mac_escape($_var_94["url"]);
			$_var_94["url_next"] = mac_escape($_var_94["url_next"]);
		} elseif ($GLOBALS["config"]["app"]["encrypt"] == "2") {
			$_var_94["url"] = base64_encode(mac_escape($_var_94["url"]));
			$_var_94["url_next"] = base64_encode(mac_escape($_var_94["url_next"]));
		}
		$_var_94["id"] = $_var_88["id"];
		$_var_94["sid"] = $_var_88["sid"];
		$_var_94["nid"] = $_var_88["nid"];
		$_var_85["player_info"] = $_var_94;
		$this->assign("obj", $_var_85);
		$_var_95 = "1-" . ($_var_84 == "play" ? "4" : "5") . "-" . $_var_85["vod_id"];
		if ($_var_87 == 0 && $_var_84 == "play" && $_var_93["trysee"] > 0 || $_var_85["vod_pwd_" . $_var_84] != "" && session($_var_95) != "1" || $_var_85["vod_copyright"] == 1 && !empty($_var_85["vod_jumpurl"]) && $GLOBALS["config"]["app"]["copyright_status"] == 4) {
			$_var_96 = $_var_85["vod_id"];
			if ($GLOBALS["config"]["rewrite"]["vod_id"] == 2) {
				$_var_96 = mac_alphaID($_var_85["vod_id"], false, $GLOBALS["config"]["rewrite"]["encode_len"], $GLOBALS["config"]["rewrite"]["encode_key"]);
			}
			$_var_97 = mac_url("index/vod/" . $_var_84 . "er", ["id" => $_var_96, "sid" => $_var_88["sid"], "nid" => $_var_88["nid"]]);
			$this->assign("player_data", "");
			$this->assign("player_js", "<div class=\"MacPlayer\" style=\"z-index:99999;width:100%;height:100%;margin:0px;padding:0px;\"><iframe id=\"player_if\" name=\"player_if\" src=\"" . $_var_97 . "\" style=\"z-index:9;width:100%;height:100%;\" border=\"0\" marginWidth=\"0\" frameSpacing=\"0\" marginHeight=\"0\" frameBorder=\"0\" scrolling=\"no\" allowfullscreen=\"allowfullscreen\" mozallowfullscreen=\"mozallowfullscreen\" msallowfullscreen=\"msallowfullscreen\" oallowfullscreen=\"oallowfullscreen\" webkitallowfullscreen=\"webkitallowfullscreen\" ></iframe></div>");
		} else {
			$this->assign("player_data", "<script type=\"text/javascript\">var player_aaaa=" . json_encode($_var_94) . "</script>");
			$this->assign("player_js", "<script type=\"text/javascript\" src=\"" . MAC_PATH . "static/js/playerconfig.js?t=" . $this->_tsp . "\"></script><script type=\"text/javascript\" src=\"" . MAC_PATH . "static/js/player.js?t=a" . $this->_tsp . "\"></script>");
		}
		$this->label_comment();
		return $_var_85;
	}
}