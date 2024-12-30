<?php


use think\View;
error_reporting(E_ERROR | E_PARSE);
if (!function_exists("is_really_writable")) {
	function is_really_writable($_var_0)
	{
		if (DIRECTORY_SEPARATOR === "/") {
			return is_writable($_var_0);
		}
		if (is_dir($_var_0)) {
			$_var_0 = rtrim($_var_0, "/") . "/" . md5(mt_rand());
			if (($_var_1 = @fopen($_var_0, "ab")) === false) {
				return false;
			}
			fclose($_var_1);
			@chmod($_var_0, 0777);
			@unlink($_var_0);
			return true;
		} elseif (!is_file($_var_0) or ($_var_1 = @fopen($_var_0, "ab")) === false) {
			return false;
		}
		fclose($_var_1);
		return true;
	}
}
if (!function_exists("rmdirs")) {
	function rmdirs($dirname, $withself = true)
	{
		if (!is_dir($dirname)) {
			return false;
		}
		$_var_2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($_var_2 as $_var_3) {
			$_var_4 = $_var_3->isDir() ? "rmdir" : "unlink";
			$_var_4($_var_3->getRealPath());
		}
		if ($withself) {
			@rmdir($dirname);
		}
		return true;
	}
}
if (!function_exists("copydirs")) {
	function copydirs($source, $dest)
	{
		if (!is_dir($dest)) {
			mkdir($dest, 0755, true);
		}
		foreach ($_var_5 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $_var_6) {
			if ($_var_6->isDir()) {
				$_var_7 = $dest . DS . $_var_5->getSubPathName();
				if (!is_dir($_var_7)) {
					mkdir($_var_7, 0755, true);
				}
			} else {
				copy($_var_6, $dest . DS . $_var_5->getSubPathName());
			}
		}
	}
}
if (!function_exists("setconfig")) {
	function setconfig($file, $arr = [])
	{
		if (is_array($arr)) {
			$filename = $file . ".php";
			$filepath = APP_PATH . "/extra/" . $filename;
			if (!file_exists($filepath)) {
				$conf = "<?php return [];";
				file_put_contents($filepath, $conf);
			}
			$conf = (include $filepath);
			foreach ($arr as $key => $value) {
				$conf[$key] = $value;
			}
			$time = date("Y/m/d H:i:s");
			$str = "<?php\r\n/**\r\n * tg@yzlseo\r\n * " . $time . "\r\n */\r\nreturn \r\n";
			$str .= var_export($conf, 1);
			$str .= ";";
			file_put_contents($filepath, $str);
			return true;
		} else {
			return false;
		}
	}
}
function slog($_var_8)
{
	$_var_9 = date("Y-m-d-H");
	$_var_10 = date("Y-m-d H:i:s");
	$_var_11 = "./log/" . $_var_9 . ".txt";
	$_var_12 = @fopen($_var_11, "a+");
	@fputs($_var_12, $_var_10 . " " . $_var_8 . "\r\n");
	@fclose($_var_12);
}
function mac_string_is_ip($string)
{
	return preg_match("/^(\\d{1,3}\\.){3}\\d{1,3}(:\\d{1,5})?\$/", $string) === 1;
}
function mac_return($_var_13, $_var_14 = 1, $_var_15 = '')
{
	if (is_array($_var_13)) {
		return json_encode($_var_13);
	} else {
		$_var_16 = ["code" => $_var_14, "msg" => $_var_13, "data" => ""];
		if (is_array($_var_15)) {
			$_var_16["data"] = $_var_15;
		}
		return json_encode($_var_16);
	}
}
function mac_run_statistics()
{
	$_var_17 = microtime(true) - MAC_START_TIME;
	$_var_18 = memory_get_usage();
	$_var_19 = mac_format_size($_var_18);
	unset($_var_20);
	return "Processed in: " . round($_var_17, 4) . " second(s),&nbsp;" . $_var_19 . " Mem On.";
}
function mac_format_size($_var_21 = 0)
{
	if ($_var_21 == 0) {
		return "0 kb";
	}
	$_var_22 = array("b", "kb", "mb", "gb", "tb", "pb");
	return round($_var_21 / pow(1024, $_var_23 = floor(log($_var_21, 1024))), 2) . " " . $_var_22[$_var_23];
}
function mac_read_file($f)
{
	return @file_get_contents($f);
}
function mac_write_file($f, $c = '')
{
	$_var_24 = dirname($f);
	if (!is_dir($_var_24)) {
		mac_mkdirss($_var_24);
	}
	return @file_put_contents($f, $c);
}
function mac_mkdirss($_var_25, $_var_26 = 0777)
{
	if (!is_dir(dirname($_var_25))) {
		mac_mkdirss(dirname($_var_25));
	}
	if (!file_exists($_var_25)) {
		return mkdir($_var_25, $_var_26);
	}
	return true;
}
function mac_rmdirs($_var_27, $_var_28 = true)
{
	if (!is_dir($_var_27)) {
		return false;
	}
	$_var_29 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($_var_27, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
	foreach ($_var_29 as $_var_30) {
		$_var_31 = $_var_30->isDir() ? "rmdir" : "unlink";
		$_var_31($_var_30->getRealPath());
	}
	if ($_var_28) {
		@rmdir($_var_27);
	}
	return true;
}
function mac_arr2file($f, $arr = '')
{
	if (is_array($arr)) {
		$_var_32 = var_export($arr, true);
	} else {
		$_var_32 = $arr;
	}
	$_var_32 = "<?php\nreturn " . $_var_32 . ";";
	mac_write_file($f, $_var_32);
	if (function_exists("opcache_invalidate")) {
		opcache_invalidate($f, true);
	}
}
function mac_replace_text($_var_33, $_var_34 = 1)
{
	if ($_var_34 == 1) {
		return str_replace("#", Chr(13), $_var_33);
	}
	return str_replace(Chr(13), "#", str_replace(Chr(10), "", $_var_33));
}
function mac_compress_html($s)
{
	$s = str_replace(array("\r\n", "\n", "\t"), array("", "", ""), $s);
	$_var_35 = array("/> *([^ ]*) *</", "/[\\s]+/", "/<!--[\\w\\W\r\\n]*?-->/", "/ \"/", "'/\\*[^*]*\\*/'");
	$_var_36 = array(">\\1<", " ", "", "\"", "");
	return preg_replace($_var_35, $_var_36, $s);
}
function mac_build_regx($regstr, $regopt)
{
	return "/" . str_replace(["/", "\$", "+", "-", "{"], ["\\/", "\\\$", "\\+", "\\-", "\\{"], $regstr) . "/" . $regopt;
}
function mac_reg_replace($_var_37, $_var_38, $_var_39)
{
	$_var_40 = "";
	$_var_38 = mac_build_regx($_var_38, "is");
	if (!empty($_var_37)) {
		$_var_40 = preg_replace($_var_38, $_var_39, $_var_37);
	}
	return $_var_40;
}
function mac_reg_match($_var_41, $_var_42)
{
	$_var_43 = "";
	$_var_42 = mac_build_regx($_var_42, "is");
	preg_match_all($_var_42, $_var_41, $_var_44);
	$_var_45 = $_var_44[1];
	foreach ($_var_45 as $_var_46 => $_var_47) {
		$_var_43 = trim(preg_replace("/[ \r\n\t ]{1,}/", " ", $_var_47));
		break;
	}
	unset($_var_44);
	return $_var_43;
}
function mac_redirect($_var_48, $_var_49 = '')
{
	echo "<script>" . $_var_49 . "location.href=\"" . $_var_48 . "\";</script>";
	exit;
}
function mac_alert($_var_50)
{
	echo "<script>alert(\"" . $_var_50 . "\\t\\t\");history.go(-1);</script>";
}
function mac_alert_url($str, $url)
{
	echo "<script>alert(\"" . $str . "\\t\\t\");location.href=\"" . $url . "\";</script>";
}
function mac_jump($_var_51, $_var_52 = 0)
{
	echo "<script>setTimeout(function (){location.href=\"" . $_var_51 . "\";}," . $_var_52 * 1000 . ");</script><span>" . lang("pause") . "" . $_var_52 . "" . lang("continue_in_second") . "  >>>  </span><a href=\"" . $_var_51 . "\" >" . lang("browser_jump") . "</a><br>";
}
function mac_echo($str)
{
	echo $str . "<br>";
	ob_flush();
	flush();
}
function mac_day($_var_53, $_var_54 = '', $_var_55 = '#FF0000')
{
	if (empty($_var_53)) {
		return "";
	}
	if (is_numeric($_var_53)) {
		$_var_53 = date("Y-m-d H:i:s", $_var_53);
	}
	$_var_56 = date("Y-m-d", time());
	if ($_var_54 == "color" && strpos("," . $_var_53, $_var_56) > 0) {
		return "<font color=\"" . $_var_55 . "\">" . $_var_53 . "</font>";
	}
	return $_var_53;
}
function mac_friend_date($time)
{
	if (!$time) {
		return false;
	}
	$_var_57 = "";
	$_var_58 = time() - intval($time);
	$_var_59 = $time - mktime(0, 0, 0, 0, 0, date("Y"));
	$_var_60 = $time - mktime(0, 0, 0, date("m"), 0, date("Y"));
	$_var_61 = $time - mktime(0, 0, 0, date("m"), date("d") - 2, date("Y"));
	$_var_62 = $time - mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
	$_var_63 = $time - mktime(0, 0, 0, date("m"), date("d"), date("Y"));
	$_var_64 = $time - mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
	$_var_65 = $time - mktime(0, 0, 0, date("m"), date("d") + 2, date("Y"));
	if ($_var_58 == 0) {
		$_var_57 = lang("just");
	} else {
		switch ($_var_58) {
			case $_var_58 < $_var_65:
				$_var_57 = date("Y" . lang("year") . "m" . lang("month") . "d" . lang("day"), $time);
				break;
			case $_var_58 < $_var_64:
				$_var_57 = lang("day_after_tomorrow") . date("H:i", $time);
				break;
			case $_var_58 < 0:
				$_var_57 = lang("tomorrow") . date("H:i", $time);
				break;
			case $_var_58 < 60:
				$_var_57 = $_var_58 . lang("seconds_ago");
				break;
			case $_var_58 < 3600:
				$_var_57 = floor($_var_58 / 60) . lang("minutes_ago");
				break;
			case $_var_58 < $_var_63:
				$_var_57 = floor($_var_58 / 3600) . lang("hours_ago");
				break;
			case $_var_58 < $_var_62:
				$_var_57 = lang("yesterday") . date("H:i", $time);
				break;
			case $_var_58 < $_var_61:
				$_var_57 = lang("day_before_yesterday") . date("H:i", $time);
				break;
			case $_var_58 < $_var_60:
				$_var_57 = date("m" . lang("month") . "d" . lang("day") . " H:i", $time);
				break;
			case $_var_58 < $_var_59:
				$_var_57 = date("m" . lang("month") . "d" . lang("day"), $time);
				break;
			default:
				$_var_57 = date("Y" . lang("year") . "m" . lang("month") . "d" . lang("day"), $time);
				break;
		}
	}
	return $_var_57;
}
function mac_get_time_span($_var_66)
{
	$_var_67 = session($_var_66);
	if (empty($_var_67)) {
		$_var_67 = "1228348800";
	}
	$_var_68 = time() - intval($_var_67);
	session($_var_66, time());
	return $_var_68;
}
function mac_get_rndstr($length = 32, $f = '')
{
	$_var_69 = "234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	if ($f == "num") {
		$_var_69 = "1234567890";
	} elseif ($f == "letter") {
		$_var_69 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	}
	$_var_70 = strlen($_var_69) - 1;
	$_var_71 = "";
	for ($_var_72 = 0; $_var_72 < $length; $_var_72++) {
		$_var_71 .= $_var_69[mt_rand(0, $_var_70)];
	}
	return $_var_71;
}
function mac_convert_encoding($str, $nfate, $ofate)
{
	if ($ofate == "UTF-8") {
		return $str;
	}
	if ($ofate == "GB2312") {
		$ofate = "GBK";
	}
	if (function_exists("mb_convert_encoding")) {
		$str = mb_convert_encoding($str, $nfate, $ofate);
	} else {
		$ofate .= "//IGNORE";
		$str = iconv($nfate, $ofate, $str);
	}
	return $str;
}
function mac_get_refer()
{
	return trim(urldecode($_SERVER["HTTP_REFERER"]));
}
function mac_extends_list($_var_73)
{
	$_var_74 = "./application/common/extend/" . $_var_73;
	$_var_75 = glob($_var_74 . "/*.php", GLOB_NOSORT);
	$_var_76 = [];
	$_var_76["ext_list"] = [];
	$_var_76["ext_html"] = "";
	foreach ($_var_75 as $_var_77 => $_var_78) {
		$_var_79 = str_replace([$_var_74 . "/", ".php"], "", $_var_78);
		$_var_80 = "app\\common\\extend\\" . $_var_73 . "\\" . $_var_79;
		if (class_exists($_var_80)) {
			$_var_81 = new $_var_80();
			$_var_76["ext_list"][$_var_79] = $_var_81->name;
			if (file_exists("./application/admin/view/extend/" . $_var_73 . "/" . strtolower($_var_79) . ".html")) {
				$_var_76["ext_html"] .= View::instance()->fetch("admin@extend/" . $_var_73 . "/" . strtolower($_var_79));
			}
		}
	}
	return $_var_76;
}
function mac_send_sms($to, $code, $type_flag, $type_des, $msg)
{
	if (empty($GLOBALS["config"]["sms"]["type"])) {
		return ["code" => 9005, "msg" => lang("sms_not_config")];
	}
	$_var_82 = "/^1{1}\\d{10}\$/";
	if (!preg_match($_var_82, $to)) {
		return ["code" => 999, "msg" => lang("phone_format_err")];
	}
	if (empty($code)) {
		return ["code" => 998, "msg" => lang("title_not_empty")];
	}
	if (empty($type_flag)) {
		return ["code" => 997, "msg" => lang("tpl_not")];
	}
	$_var_83 = "app\\common\\extend\\sms\\" . ucfirst($GLOBALS["config"]["sms"]["type"]);
	if (class_exists($_var_83)) {
		$_var_84 = new $_var_83();
		return $_var_84->submit($to, $code, $type_flag, $type_des, $msg);
	} else {
		return ["code" => 991, "msg" => lang("sms_not")];
	}
}
function mac_send_mail($to, $title, $body, $conf = [])
{
	if (empty($GLOBALS["config"]["email"]["type"])) {
		return ["code" => 9005, "msg" => lang("email_not_config")];
	}
	$_var_85 = "/\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*/";
	if (!preg_match($_var_85, $to)) {
		return ["code" => 999, "msg" => lang("email_format_err")];
	}
	if (empty($title)) {
		return ["code" => 998, "msg" => lang("title_not_empty")];
	}
	if (empty($body)) {
		return ["code" => 997, "msg" => lang("body_not_empty")];
	}
	$_var_86 = "app\\common\\extend\\email\\" . ucfirst($GLOBALS["config"]["email"]["type"]);
	if (class_exists($_var_86)) {
		$_var_87 = new $_var_86();
		return $_var_87->submit($to, $title, $body, $conf);
	} else {
		return ["code" => 991, "msg" => lang("email_not")];
	}
}
function mac_check_back_link($_var_88)
{
	$_var_89 = [];
	$_var_89["code"] = 0;
	$_var_89["msg"] = lang("param_err");
	if (empty($_var_88)) {
		return json($_var_89);
	}
	$_var_90 = $GLOBALS["config"]["site"]["site_url"];
	$_var_91 = $GLOBALS["config"]["site"]["site_wapurl"];
	$_var_92 = mac_curl_get($_var_88);
	$_var_93 = "";
	$_var_94 = 1;
	$_var_95 = lang("back_link") . lang("normal");
	$_var_96 = lang("back_link") . lang("abnormal");
	$_var_93 .= "[" . $_var_90 . "]";
	if (strpos($_var_92, $_var_90) !== false) {
		$_var_94 = 1;
		$_var_93 .= $_var_95;
	} else {
		$_var_94 = 101;
		$_var_93 .= $_var_96;
	}
	$_var_93 .= "，[" . $_var_91 . "]";
	if (strpos($_var_92, $_var_91) !== false) {
		$_var_94 = 1;
		$_var_93 .= $_var_95;
	} else {
		$_var_94 = 101;
		$_var_93 .= $_var_96;
	}
	$_var_89["code"] = $_var_94;
	$_var_89["msg"] = $_var_93;
	return $_var_89;
}
function mac_list_to_tree($_var_97, $_var_98 = 'id', $_var_99 = 'pid', $_var_100 = 'child', $_var_101 = 0)
{
	$_var_102 = array();
	if (is_array($_var_97)) {
		$_var_103 = array();
		foreach ($_var_97 as $_var_104 => $_var_105) {
			$_var_103[$_var_105[$_var_98]] =& $_var_97[$_var_104];
		}
		foreach ($_var_97 as $_var_104 => $_var_105) {
			$_var_106 = $_var_105[$_var_99];
			if ($_var_101 == $_var_106) {
				$_var_102[] =& $_var_97[$_var_104];
			} else {
				if (isset($_var_103[$_var_106])) {
					$_var_107 =& $_var_103[$_var_106];
					$_var_107[$_var_100][] =& $_var_97[$_var_104];
				}
			}
		}
	}
	return $_var_102;
}
function mac_str_correct($_var_108, $_var_109, $_var_110)
{
	return str_replace($_var_109, $_var_110, $_var_108);
}
function mac_buildregx($_var_111, $_var_112)
{
	return "/" . str_replace("/", "\\/", $_var_111) . "/" . $_var_112;
}
function mac_em_replace($s)
{
	return preg_replace("/\\[em:(\\d{1,})?\\]/", "<img src=\"" . MAC_PATH . "static/images/face/\$1.gif\" border=0/>", $s);
}
function mac_page_param($_var_113, $_var_114, $_var_115, $_var_116, $_var_117 = 5)
{
	$_var_118 = array();
	$_var_119 = array();
	if ($_var_113 == 0) {
		return ["record_total" => 0];
	}
	if (empty($_var_117)) {
		$_var_117 = 5;
	}
	$_var_118["record_total"] = $_var_113;
	$_var_118["page_current"] = $_var_115;
	$_var_120 = ceil($_var_113 / $_var_114);
	$_var_118["page_total"] = $_var_120;
	$_var_118["page_sp"] = MAC_PAGE_SP;
	$_var_121 = $_var_115 - 1;
	if ($_var_121 <= 0) {
		$_var_121 = 1;
	}
	$_var_122 = $_var_115 + 1;
	if ($_var_122 > $_var_120) {
		$_var_122 = $_var_120;
	}
	$_var_118["page_prev"] = $_var_121;
	$_var_118["page_next"] = $_var_122;
	if ($_var_120 <= $_var_117) {
		for ($_var_123 = 1; $_var_123 <= $_var_120; $_var_123++) {
			$_var_119[$_var_123] = $_var_123;
		}
	} else {
		$_var_124 = floor($_var_117 / 2);
		$_var_125 = $_var_120 - $_var_117;
		if ($_var_115 <= $_var_124) {
			for ($_var_123 = 1; $_var_123 <= $_var_117; $_var_123++) {
				$_var_119[$_var_123] = $_var_123;
			}
		} elseif ($_var_115 > $_var_125) {
			for ($_var_123 = $_var_125 + 0; $_var_123 <= $_var_120; $_var_123++) {
				$_var_119[$_var_123] = $_var_123;
			}
		} else {
			for ($_var_123 = $_var_115 - $_var_124; $_var_123 <= $_var_115 + $_var_124; $_var_123++) {
				$_var_119[$_var_123] = $_var_123;
			}
		}
	}
	$_var_118["page_num"] = $_var_119;
	$_var_118["page_url"] = $_var_116;
	return $_var_118;
}
function mac_curl_post($url, $data, $heads = array(), $cookie = '')
{
	$_var_126 = @curl_init();
	curl_setopt($_var_126, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36");
	curl_setopt($_var_126, CURLOPT_URL, $url);
	curl_setopt($_var_126, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($_var_126, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($_var_126, CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($_var_126, CURLOPT_TIMEOUT, 30);
	curl_setopt($_var_126, CURLINFO_CONTENT_LENGTH_UPLOAD, strlen($data));
	curl_setopt($_var_126, CURLOPT_HEADER, 0);
	curl_setopt($_var_126, CURLOPT_REFERER, $url);
	curl_setopt($_var_126, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($_var_126, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($_var_126, CURLOPT_POST, 1);
	curl_setopt($_var_126, CURLOPT_POSTFIELDS, $data);
	if (!empty($cookie)) {
		curl_setopt($_var_126, CURLOPT_COOKIE, $cookie);
	}
	if (count($heads) > 0) {
		curl_setopt($_var_126, CURLOPT_HTTPHEADER, $heads);
	}
	$_var_127 = @curl_exec($_var_126);
	if (curl_errno($_var_126)) {
	}
	curl_close($_var_126);
	return $_var_127;
}
function mac_curl_get($_var_128, $_var_129 = array(), $cookie = '')
{
	$_var_130 = @curl_init();
	curl_setopt($_var_130, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36");
	curl_setopt($_var_130, CURLOPT_URL, $_var_128);
	curl_setopt($_var_130, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($_var_130, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($_var_130, CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($_var_130, CURLOPT_TIMEOUT, 30);
	curl_setopt($_var_130, CURLOPT_HEADER, 0);
	curl_setopt($_var_130, CURLOPT_REFERER, $_var_128);
	curl_setopt($_var_130, CURLOPT_POST, 0);
	curl_setopt($_var_130, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($_var_130, CURLOPT_SSL_VERIFYHOST, 1);
	if (!empty($cookie)) {
		curl_setopt($_var_130, CURLOPT_COOKIE, $cookie);
	}
	if (count($_var_129) > 0) {
		curl_setopt($_var_130, CURLOPT_HTTPHEADER, $_var_129);
	}
	$_var_131 = @curl_exec($_var_130);
	if (curl_errno($_var_130)) {
	}
	curl_close($_var_130);
	return $_var_131;
}
function mac_substring($str, $lenth, $start = 0)
{
	$_var_132 = strlen($str);
	$_var_133 = array();
	$_var_134 = 0;
	$_var_135 = 0;
	for ($_var_136 = 0; $_var_136 < $_var_132; $_var_136++) {
		$_var_137 = substr($str, $_var_136, 1);
		$_var_138 = base_convert(ord($_var_137), 10, 2);
		$_var_138 = substr("00000000 " . $_var_138, -8);
		if ($_var_134 < $start) {
			if (substr($_var_138, 0, 1) == 0) {
			} else {
				if (substr($_var_138, 0, 3) == 110) {
					$_var_136 += 1;
				} else {
					if (substr($_var_138, 0, 4) == 1110) {
						$_var_136 += 2;
					}
				}
			}
			$_var_134++;
		} else {
			if (substr($_var_138, 0, 1) == 0) {
				$_var_133[] = substr($str, $_var_136, 1);
			} else {
				if (substr($_var_138, 0, 3) == 110) {
					$_var_133[] = substr($str, $_var_136, 2);
					$_var_136 += 1;
				} else {
					if (substr($_var_138, 0, 4) == 1110) {
						$_var_133[] = substr($str, $_var_136, 3);
						$_var_136 += 2;
					} else {
						$_var_133[] = " ";
					}
				}
			}
			if (++$_var_135 >= $lenth) {
				break;
			}
		}
	}
	return join("", $_var_133);
}
function mac_array2xml($arr, $level = 1)
{
	$_var_139 = $level == 1 ? "<xml>" : "";
	foreach ($arr as $_var_140 => $_var_141) {
		if (is_numeric($_var_140)) {
			$_var_140 = $_var_141["TagName"];
			unset($_var_141["TagName"]);
		}
		if (!is_array($_var_141)) {
			$_var_139 .= "<" . $_var_140 . ">" . (!is_numeric($_var_141) ? "<![CDATA[" : "") . $_var_141 . (!is_numeric($_var_141) ? "]]>" : "") . "</" . $_var_140 . ">";
		} else {
			$_var_139 .= "<" . $_var_140 . ">" . mac_array2xml($_var_141, $level + 1) . "</" . $_var_140 . ">";
		}
	}
	$_var_139 = preg_replace("/([\1-\10 - \16-\37])+/", " ", $_var_139);
	return $level == 1 ? $_var_139 . "</xml>" : $_var_139;
}
function mac_xml2array($_var_142)
{
	libxml_disable_entity_loader(true);
	$_var_143 = json_decode(json_encode(simplexml_load_string($_var_142, "SimpleXMLElement", LIBXML_NOCDATA)), true);
	return $_var_143;
}
function mac_array_rekey($_var_144, $_var_145)
{
	$_var_146 = [];
	foreach ($_var_144 as $_var_147 => $_var_148) {
		$_var_146[$_var_148[$_var_145]] = $_var_148;
	}
	return $_var_146;
}
function mac_array_filter($_var_149, $_var_150)
{
	if (!is_array($_var_149)) {
		$_var_149 = explode(",", $_var_149);
	}
	$_var_149 = array_filter($_var_149);
	if (empty($_var_149)) {
		return false;
	}
	$_var_151 = str_replace($_var_149, "*", $_var_150);
	return $_var_151 != $_var_150;
}
function mac_parse_sql($sql = '', $limit = 0, $prefix = [])
{
	$_var_152 = "";
	$_var_153 = "";
	if (!empty($prefix)) {
		$_var_153 = current($prefix);
		$_var_152 = current(array_flip($prefix));
	}
	if ($sql != "") {
		$_var_154 = [];
		$_var_155 = false;
		$sql = str_replace(["\r\n", "\r"], "\n", $sql);
		$sql = explode("\n", trim($sql));
		foreach ($sql as $_var_156 => $_var_157) {
			if ($_var_157 == "") {
				continue;
			}
			if (preg_match("/^(#|--)/", $_var_157)) {
				continue;
			}
			if (preg_match("/^\\/\\*(.*?)\\*\\//", $_var_157)) {
				continue;
			}
			if (substr($_var_157, 0, 2) == "/*") {
				$_var_155 = true;
				continue;
			}
			if (substr($_var_157, -2) == "*/") {
				$_var_155 = false;
				continue;
			}
			if ($_var_155) {
				continue;
			}
			if ($_var_152 != "") {
				$_var_157 = str_replace("`" . $_var_152, "`" . $_var_153, $_var_157);
			}
			if ($_var_157 == "BEGIN;" || $_var_157 == "COMMIT;") {
				continue;
			}
			array_push($_var_154, $_var_157);
		}
		if ($limit == 1) {
			return implode("", $_var_154);
		}
		$_var_154 = implode("\n", $_var_154);
		$_var_154 = explode(";\n", $_var_154);
		return $_var_154;
	} else {
		return $limit == 1 ? "" : [];
	}
}
function mac_interface_type()
{
	$_var_158 = $GLOBALS["config"]["app"]["cache_flag"] . "_" . "interface_type";
	$_var_159 = think\Cache::get($_var_158);
	if (empty($_var_159)) {
		$_var_160 = config("maccms.interface");
		$_var_161 = str_replace([Chr(10), Chr(13)], ["", "#"], $_var_160["vodtype"]);
		$_var_162 = str_replace([Chr(10), Chr(13)], ["", "#"], $_var_160["arttype"]);
		$_var_163 = str_replace([Chr(10), Chr(13)], ["", "#"], $_var_160["actortype"]);
		$_var_164 = str_replace([Chr(10), Chr(13)], ["", "#"], $_var_160["websitetype"]);
		$_var_159 = [];
		$_var_165 = explode("#", $_var_161);
		foreach ($_var_165 as $_var_166 => $_var_167) {
			list($_var_168, $_var_169) = explode("=", $_var_167);
			$_var_159["vodtype"][$_var_169] = $_var_168;
		}
		$_var_165 = explode("#", $_var_162);
		foreach ($_var_165 as $_var_166 => $_var_167) {
			list($_var_168, $_var_169) = explode("=", $_var_167);
			$_var_159["arttype"][$_var_169] = $_var_168;
		}
		$_var_165 = explode("#", $_var_163);
		foreach ($_var_165 as $_var_166 => $_var_167) {
			list($_var_168, $_var_169) = explode("=", $_var_167);
			$_var_159["actortype"][$_var_169] = $_var_168;
		}
		$_var_165 = explode("#", $_var_164);
		foreach ($_var_165 as $_var_166 => $_var_167) {
			list($_var_168, $_var_169) = explode("=", $_var_167);
			$_var_159["websitetype"][$_var_169] = $_var_168;
		}
		think\Cache::set($_var_158, $_var_159);
	}
	$_var_170 = model("Type")->getCache("type_list");
	$_var_171 = [];
	foreach ($_var_170 as $_var_166 => $_var_167) {
		$_var_171[$_var_167["type_name"]] = $_var_167["type_id"];
	}
	foreach ($_var_159["vodtype"] as $_var_166 => $_var_167) {
		$_var_159["vodtype"][$_var_166] = (int) $_var_171[$_var_167];
	}
	foreach ($_var_159["arttype"] as $_var_166 => $_var_167) {
		$_var_159["arttype"][$_var_166] = (int) $_var_171[$_var_167];
	}
	foreach ($_var_159["actortype"] as $_var_166 => $_var_167) {
		$_var_159["actortype"][$_var_166] = (int) $_var_171[$_var_167];
	}
	foreach ($_var_159["websitetype"] as $_var_166 => $_var_167) {
		$_var_159["websitetype"][$_var_166] = (int) $_var_171[$_var_167];
	}
	return $_var_159;
}
function mac_rep_pse_rnd($psearr, $txt, $id = 0)
{
	if (empty($psearr)) {
		return $txt;
	}
	$_var_172 = count($psearr);
	if (empty($txt)) {
		if (empty($id)) {
			$_var_173 = mt_rand(0, $_var_172 - 1);
		} else {
			$_var_173 = $id % $_var_172;
		}
		$_var_174 = $psearr[$_var_173];
	} else {
		if (empty($id)) {
			$id = crc32($txt);
		}
		$_var_175 = mb_strpos($txt, "<br>");
		$_var_176 = mb_strlen($txt);
		if ($_var_175 == 0) {
			$_var_175 = mb_strpos($txt, "<br/>");
		}
		if ($_var_175 == 0) {
			$_var_175 = mb_strpos($txt, "<br />");
		}
		if ($_var_175 == 0) {
			$_var_175 = mb_strpos($txt, "</p>");
		}
		if ($_var_175 == 0) {
			$_var_175 = mb_strpos($txt, "。");
		}
		if ($_var_175 == 0) {
			$_var_175 = mb_strpos($txt, "！");
		}
		if ($_var_175 == 0) {
			$_var_175 = mb_strpos($txt, "!");
		}
		if ($_var_175 == 0) {
			$_var_175 = mb_strpos($txt, "？");
		}
		if ($_var_175 == 0) {
			$_var_175 = mb_strpos($txt, "?");
		}
		if ($_var_175 > 0) {
			$_var_174 = mac_substring($txt, $_var_175 - 1) . $psearr[$id % $_var_172] . mac_substring($txt, $_var_176 - $_var_175, $_var_175);
		} else {
			$_var_174 = $psearr[$id % $_var_172] . $txt;
		}
	}
	return $_var_174;
}
function mac_txt_explain($_var_177, $_var_178 = false)
{
	$_var_179 = explode("#", $_var_177);
	$_var_180 = [];
	foreach ($_var_179 as $_var_181) {
		if (stripos($_var_181, "=") === false) {
			continue;
		}
		list($_var_182, $_var_183) = explode("=", $_var_181, 2);
		if ($_var_178 === true && stripos($_var_182, "&") !== false && stripos($_var_182, ";") !== false) {
			$_var_182 = html_entity_decode($_var_182, ENT_QUOTES, "UTF-8");
		}
		if ($_var_178 === true && stripos($_var_183, "&") !== false && stripos($_var_183, ";") !== false) {
			$_var_183 = html_entity_decode($_var_183, ENT_QUOTES, "UTF-8");
		}
		$_var_180["from"][] = $_var_182;
		$_var_180["to"][] = $_var_183;
	}
	return $_var_180;
}
function mac_rep_pse_syn($_var_184, $_var_185)
{
	if (empty($_var_185)) {
		$_var_185 = "";
	}
	if (is_array($_var_184["from"]) && is_array($_var_184["to"])) {
		$_var_185 = str_replace($_var_184["from"], $_var_184["to"], $_var_185);
	}
	return $_var_185;
}
function mac_get_tag($_var_186, $_var_187)
{
	$_var_188 = base64_decode("aHR0cDovL2FwaS5kcGxheWVyc3RhdGljLmNvbQ==") . "/keyword/index?name=" . rawurlencode($_var_186) . "&txt=" . rawurlencode($_var_186) . rawurlencode(mac_substring(strip_tags($_var_187), 200));
	$_var_189 = mac_curl_get($_var_188);
	$_var_190 = @json_decode($_var_189, true);
	if ($_var_190) {
		if ($_var_190["code"] == 1) {
			return implode(",", $_var_190["data"]);
		}
	}
	return false;
}
function mac_get_client_ip()
{
	static $_var_191;
	if (!is_null($_var_191)) {
		return $_var_191;
	}
	$_var_192 = array();
	if (!empty($_SERVER["HTTP_CF_CONNECTING_IP"])) {
		$_var_192[] = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}
	if (!empty($_SERVER["HTTP_ALI_CDN_REAL_IP"])) {
		$_var_192[] = $_SERVER["HTTP_ALI_CDN_REAL_IP"];
	}
	if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$_var_192[] = $_SERVER["HTTP_CLIENT_IP"];
	}
	if (!empty($_SERVER["HTTP_PROXY_USER"])) {
		$_var_192[] = $_SERVER["HTTP_PROXY_USER"];
	}
	$_var_193 = getenv("HTTP_X_REAL_IP");
	if (!empty($_var_193)) {
		$_var_192[] = $_var_193;
	}
	if (!empty($_SERVER["REMOTE_ADDR"])) {
		$_var_192[] = $_SERVER["REMOTE_ADDR"];
	}
	foreach ($_var_192 as $_var_194) {
		$_var_195 = ip2long($_var_194);
		$_var_195 && ($_var_191 = $_var_194);
		if ($_var_195 > 0 && $_var_195 < 0xffffffff) {
			$_var_191 = long2ip($_var_195);
			break;
		}
	}
	empty($_var_191) && ($_var_191 = "0.0.0.0");
	return $_var_191;
}
function mac_get_ip_long($_var_196 = '')
{
	$_var_196 = !empty($_var_196) ? $_var_196 : mac_get_client_ip();
	$_var_197 = sprintf("%u", ip2long($_var_196));
	if ($_var_197 < 0 || $_var_197 >= 0xffffffff) {
		$_var_197 = 0;
	}
	return $_var_197;
}
function mac_get_uniqid_code($_var_198 = '')
{
	$_var_198 = strtoupper($_var_198);
	$_var_199 = date("YmdHis");
	$_var_200 = rand(100000, 999999);
	return $_var_198 . $_var_199 . $_var_200;
}
function mac_escape($string, $in_encoding = 'UTF-8', $out_encoding = 'UCS-2')
{
	$_var_201 = "";
	if (function_exists("mb_get_info")) {
		for ($_var_202 = 0; $_var_202 < mb_strlen($string, $in_encoding); $_var_202++) {
			$_var_203 = mb_substr($string, $_var_202, 1, $in_encoding);
			if (strlen($_var_203) > 1) {
				$_var_201 .= "%u" . strtoupper(bin2hex(mb_convert_encoding($_var_203, $out_encoding, $in_encoding)));
			} else {
				$_var_201 .= "%" . strtoupper(bin2hex($_var_203));
			}
		}
	}
	return $_var_201;
}
function mac_unescape($str)
{
	$_var_204 = "";
	$_var_205 = strlen($str);
	for ($_var_206 = 0; $_var_206 < $_var_205; $_var_206++) {
		if ($str[$_var_206] == "%" && $str[$_var_206 + 1] == "u") {
			$_var_207 = hexdec(substr($str, $_var_206 + 2, 4));
			if ($_var_207 < 0x7f) {
				$_var_204 .= Chr($_var_207);
			} else {
				if ($_var_207 < 0x800) {
					$_var_204 .= Chr(0xc0 | $_var_207 >> 6) . Chr(0x80 | $_var_207 & 0x3f);
				} else {
					$_var_204 .= Chr(0xe0 | $_var_207 >> 12) . Chr(0x80 | $_var_207 >> 6 & 0x3f) . Chr(0x80 | $_var_207 & 0x3f);
				}
			}
			$_var_206 += 5;
		} else {
			if ($str[$_var_206] == "%") {
				$_var_204 .= urldecode(substr($str, $_var_206, 3));
				$_var_206 += 2;
			} else {
				$_var_204 .= $str[$_var_206];
			}
		}
	}
	return $_var_204;
}
function mac_get_mid_code($_var_208)
{
	$_var_209 = [1 => "vod", 2 => "art", 3 => "topic", 4 => "comment", 5 => "gbook", 6 => "user", 7 => "label", 8 => "actor", 9 => "role", 10 => "plot", 11 => "website"];
	return $_var_209[$_var_208];
}
function mac_get_mid_text($_var_210)
{
	$_var_211 = [1 => lang("vod"), 2 => lang("art"), 3 => lang("topic"), 4 => lang("comment"), 5 => lang("gbook"), 6 => lang("user"), 7 => lang("label"), 8 => lang("actor"), 9 => lang("role"), 10 => lang("plot"), 11 => lang("website")];
	return $_var_211[$_var_210];
}
function mac_get_mid($_var_212)
{
	$_var_212 = strtolower($_var_212);
	$_var_213 = ["vod" => 1, "art" => 2, "topic" => 3, "comment" => 4, "gbook" => 5, "user" => 6, "label" => 7, "actor" => 8, "role" => 9, "plot" => 10, "website" => 11];
	return $_var_213[$_var_212];
}
function mac_get_aid($_var_214, $_var_215 = '')
{
	$_var_214 = strtolower($_var_214);
	$_var_215 = strtolower($_var_215);
	$_var_216 = $_var_214 . "/" . $_var_215;
	$_var_217 = ["index" => 1, "map" => 2, "rss" => 3, "gbook" => 4, "comment" => 5, "user" => 6, "label" => 7, "vod" => 10, "art" => 20, "topic" => 30, "actor" => 80, "role" => 90, "plot" => 100, "website" => 110];
	$_var_218 = $_var_217[$_var_214];
	$_var_217 = ["vod/type" => 11, "vod/show" => 12, "vod/search" => 13, "vod/detail" => 14, "vod/play" => 15, "vod/down" => 16, "vod/role" => 17, "art/type" => 21, "art/show" => 22, "art/search" => 23, "art/detail" => 24, "topic/search" => 33, "topic/detail" => 34, "actor/type" => 81, "actor/show" => 82, "actor/search" => 83, "actor/detail" => 84, "role/show" => 92, "role/search" => 93, "role/detail" => 94, "plot/search" => 103, "plot/detail" => 104, "website/type" => 111, "website/show" => 112, "website/search" => 113, "website/detail" => 114];
	if (!empty($_var_217[$_var_216])) {
		$_var_218 = $_var_217[$_var_216];
	}
	return $_var_218;
}
function mac_get_user_status_text($data)
{
	$_var_219 = [0 => lang("disable"), 1 => lang("enable")];
	return $_var_219[$data];
}
function mac_get_user_flag_text($data)
{
	$_var_220 = [0 => lang("counting_points"), 1 => lang("counting_times"), 2 => lang("counting_ips")];
	return $_var_220[$data];
}
function mac_get_ulog_type_text($_var_221)
{
	$_var_222 = [1 => lang("browse"), 2 => lang("collect"), 3 => lang("want_see"), 4 => lang("play"), 5 => lang("down")];
	return $_var_222[$_var_221];
}
function mac_get_plog_type_text($data)
{
	$_var_223 = [1 => lang("integral_recharge"), 2 => lang("registration_promotion"), 3 => lang("visit_promotion"), 4 => lang("one_level_distribution"), 5 => lang("two_level_distribution"), 6 => lang("three_level_distribution"), 7 => lang("points_upgrade"), 8 => lang("integral_consumption"), 9 => lang("integral_withdrawal")];
	return $_var_223[$data];
}
function mac_get_card_sale_status_text($data)
{
	$_var_224 = [0 => lang("not_sale"), 1 => lang("sold")];
	return $_var_224[$data];
}
function mac_get_card_use_status_text($data)
{
	$_var_225 = [0 => lang("not_used"), 1 => lang("used")];
	return $_var_225[$data];
}
function mac_get_order_status_text($_var_226)
{
	$_var_227 = [0 => lang("not_paid"), 1 => lang("paid")];
	return $_var_227[$_var_226];
}
function mac_get_user_portrait($_var_228)
{
	$_var_229 = MAC_PATH . "static/images/touxiang.png";
	if (!empty($_var_228)) {
		$_var_230 = "upload/user/" . $_var_228 % 10 . "/" . $_var_228 . ".jpg";
		if (file_exists(ROOT_PATH . $_var_230)) {
			$_var_229 = MAC_PATH . $_var_230;
		}
	}
	return $_var_229;
}
function mac_filter_html($_var_231)
{
	return strip_tags($_var_231);
}
function mac_filter_xss($_var_232)
{
	return trim(htmlspecialchars(strip_tags($_var_232), ENT_QUOTES));
}
function mac_restore_htmlfilter($str)
{
	if (stripos($str, "&amp;") !== false) {
		return htmlspecialchars_decode($str, ENT_QUOTES);
	}
	return $str;
}
function mac_format_text($str, $allow_space = false)
{
	$_var_233 = array("/", "，", "|", "、", ",,", ",,,");
	if ($allow_space === false) {
		$_var_233[] = " ";
	}
	return str_replace($_var_233, ",", $str);
}
function mac_format_count($_var_234)
{
	$_var_235 = explode(",", $_var_234);
	return count($_var_235);
}
function mac_txt_merge($_var_236, $_var_237)
{
	if (empty($_var_237)) {
		return $_var_236;
	}
	if ($GLOBALS["config"]["collect"]["vod"]["class_filter"] != "0") {
		if (mb_strlen($_var_237) > 2) {
			$_var_237 = str_replace([lang("slice")], [""], $_var_237);
		}
		if (mb_strlen($_var_237) > 2) {
			$_var_237 = str_replace([lang("drama")], [""], $_var_237);
		}
	}
	$_var_236 = mac_format_text($_var_236);
	$_var_237 = mac_format_text($_var_237);
	$_var_238 = explode(",", $_var_236);
	$_var_239 = explode(",", $_var_237);
	$_var_240 = array_merge($_var_238, $_var_239);
	return join(",", array_unique(array_filter($_var_240)));
}
function mac_array_check_num($arr)
{
	if (!is_array($arr)) {
		return false;
	}
	$_var_241 = true;
	foreach ($arr as $_var_242) {
		if (!is_numeric($_var_242)) {
			$_var_241 = false;
			break;
		}
	}
	return $_var_241;
}
function mac_like_arr($_var_243)
{
	$_var_244 = explode(",", $_var_243);
	$_var_245 = [];
	foreach ($_var_244 as $_var_246) {
		$_var_245[] = "%" . $_var_246 . "%";
	}
	return $_var_245;
}
function mac_art_list($art_title, $art_note, $art_content)
{
	$_var_247 = [];
	$_var_248 = [];
	$_var_249 = [];
	if (!empty($art_title)) {
		$_var_247 = explode("\$\$\$", $art_title);
	}
	if (!empty($art_note)) {
		$_var_248 = explode("\$\$\$", $art_note);
	}
	if (!empty($art_content)) {
		$_var_249 = explode("\$\$\$", $art_content);
	}
	$_var_250 = [];
	foreach ($_var_249 as $_var_251 => $_var_252) {
		$_var_250[$_var_251 + 1] = ["page" => $_var_251 + 1, "title" => $_var_247[$_var_251], "note" => $_var_248[$_var_251], "content" => $_var_252];
	}
	return $_var_250;
}
function mac_plot_list($vod_plot_name, $vod_plot_detail)
{
	$_var_253 = [];
	$_var_254 = [];
	if (!empty($vod_plot_name)) {
		$_var_253 = explode("\$\$\$", $vod_plot_name);
	}
	if (!empty($vod_plot_detail)) {
		$_var_254 = explode("\$\$\$", $vod_plot_detail);
	}
	$_var_255 = [];
	foreach ($_var_253 as $_var_256 => $_var_257) {
		$_var_255[$_var_256 + 1] = ["name" => $_var_253[$_var_256], "detail" => $_var_254[$_var_256]];
	}
	return $_var_255;
}
function mac_play_list($_var_258, $_var_259, $_var_260, $_var_261, $_var_262 = 'play')
{
	$_var_263 = [];
	$_var_264 = [];
	$_var_265 = [];
	$_var_266 = [];
	if (!empty($_var_258)) {
		$_var_263 = explode("\$\$\$", $_var_258);
	}
	if (!empty($_var_259)) {
		$_var_264 = explode("\$\$\$", $_var_259);
	}
	if (!empty($_var_260)) {
		$_var_265 = explode("\$\$\$", $_var_260);
	}
	if (!empty($_var_261)) {
		$_var_266 = explode("\$\$\$", $_var_261);
	}
	if ($_var_262 == "play") {
		$_var_267 = config("vodplayer");
	} else {
		$_var_267 = config("voddowner");
	}
	$_var_268 = config("vodserver");
	$_var_269 = [];
	$_var_270 = [];
	foreach ($_var_263 as $_var_271 => $_var_272) {
		$_var_273 = (string) $_var_265[$_var_271];
		$_var_274 = mac_play_list_one($_var_264[$_var_271], $_var_272);
		$_var_275 = $_var_267[$_var_272];
		$_var_276 = $_var_268[$_var_273];
		if ($_var_275["status"] == "1") {
			$_var_270[] = $_var_275["sort"];
			$_var_269[$_var_271 + 1] = ["sid" => $_var_271 + 1, "player_info" => $_var_275, "server_info" => $_var_276, "from" => $_var_272, "url" => $_var_264[$_var_271], "server" => $_var_273, "note" => $_var_266[$_var_271], "url_count" => count($_var_274), "urls" => $_var_274];
		}
	}
	if (ENTRANCE != "admin" && MAC_PLAYER_SORT == "1" || $GLOBALS["ismake"] == "1") {
		array_multisort($_var_270, SORT_DESC, SORT_FLAG_CASE, $_var_269);
		$_var_277 = [];
		foreach ($_var_269 as $_var_271 => $_var_272) {
			$_var_277[$_var_272["sid"]] = $_var_272;
		}
		$_var_269 = $_var_277;
	}
	return $_var_269;
}
function new_stripslashes($_var_278)
{
	if (!is_array($_var_278)) {
		return stripslashes($_var_278);
	}
	foreach ($_var_278 as $_var_279 => $_var_280) {
		$_var_278[$_var_279] = new_stripslashes($_var_280);
	}
	return $_var_278;
}
function mac_screenshot_list($_var_281)
{
	$_var_282 = array();
	$_var_283 = explode("#", $_var_281);
	foreach ($_var_283 as $_var_284 => $_var_285) {
		if (empty($_var_285)) {
			continue;
		}
		list($_var_286, $_var_287) = explode("\$", $_var_285);
		if (empty($_var_287)) {
			$_var_282[$_var_284 + 1]["name"] = $_var_284 + 1;
			$_var_282[$_var_284 + 1]["url"] = $_var_286;
		} else {
			$_var_282[$_var_284 + 1]["name"] = $_var_286;
			$_var_282[$_var_284 + 1]["url"] = $_var_287;
		}
	}
	return $_var_282;
}
function mac_play_list_one($_var_288, $_var_289, $_var_290 = '')
{
	$_var_291 = array();
	$_var_292 = explode("#", $_var_288);
	foreach ($_var_292 as $_var_293 => $_var_294) {
		if (empty($_var_294)) {
			continue;
		}
		list($_var_295, $_var_296, $_var_297) = explode("\$", $_var_294);
		if (empty($_var_296)) {
			$_var_291[$_var_293 + 1]["name"] = lang("the") . ($_var_293 + 1) . lang("episode");
			$_var_291[$_var_293 + 1]["url"] = $_var_290 . $_var_295;
		} else {
			$_var_291[$_var_293 + 1]["name"] = $_var_295;
			$_var_291[$_var_293 + 1]["url"] = $_var_290 . $_var_296;
		}
		if (empty($_var_297)) {
			$_var_297 = $_var_289;
		}
		$_var_291[$_var_293 + 1]["from"] = (string) $_var_297;
		$_var_291[$_var_293 + 1]["nid"] = $_var_293 + 1;
	}
	return $_var_291;
}
function mac_filter_words($p)
{
	$_var_298 = config("maccms.app");
	$_var_299 = explode(",", $_var_298["filter_words"]);
	if (is_array($p)) {
		foreach ($p as $_var_300 => $_var_301) {
			$p[$_var_300] = str_replace($_var_299, "***", $_var_301);
		}
	} else {
		$p = str_replace($_var_299, "***", $p);
	}
	return $p;
}
function mac_long2ip($ip)
{
	$ip = long2ip($ip);
	$_var_302 = "~(\\d+)\\.(\\d+)\\.(\\d+)\\.(\\d+)~";
	return preg_replace($_var_302, "\$1.\$2.*.*", $ip);
}
function mac_default($_var_303, $_var_304 = '')
{
	if (empty($_var_303)) {
		return $_var_304;
	}
	return $_var_303;
}
function mac_num_fill($_var_305)
{
	if ($_var_305 < 10) {
		$_var_305 = "0" . $_var_305;
	}
	return $_var_305;
}
function mac_multisort($arr, $col_sort, $sort_order, $col_status = '', $status_val = '')
{
	$_var_306 = [];
	foreach ($arr as $_var_307 => $_var_308) {
		if ($col_status != "" && $_var_308[$col_status] != $status_val) {
			unset($arr[$_var_307]);
		} else {
			$_var_306[] = isset($_var_308[$col_sort]) ? $_var_308[$col_sort] : 0;
		}
	}
	array_multisort($_var_306, $sort_order, SORT_FLAG_CASE, $arr);
	return $arr;
}
function mac_get_body($_var_309, $_var_310, $_var_311)
{
	if (empty($_var_309)) {
		return false;
	}
	if (empty($_var_310)) {
		return false;
	}
	if (empty($_var_311)) {
		return false;
	}
	$_var_310 = stripslashes($_var_310);
	$_var_311 = stripslashes($_var_311);
	if (strpos($_var_309, $_var_310) != "") {
		$_var_312 = substr($_var_309, strpos($_var_309, $_var_310) + strlen($_var_310));
		$_var_312 = substr($_var_312, 0, strpos($_var_312, $_var_311));
	} else {
		$_var_312 = "";
	}
	return $_var_312;
}
function mac_find_array($text, $start, $end)
{
	$start = stripslashes($start);
	$end = stripslashes($end);
	if (empty($text)) {
		return false;
	}
	if (empty($start)) {
		return false;
	}
	if (empty($end)) {
		return false;
	}
	$start = str_replace(["(", ")", "'", "?"], ["\\(", "\\)", "\\'", "\\?"], $start);
	$end = str_replace(["(", ")", "'", "?"], ["\\(", "\\)", "\\'", "\\?"], $end);
	$_var_313 = $start . "(.*?)" . $end;
	$_var_313 = mac_buildregx($_var_313, "is");
	preg_match_all($_var_313, $text, $_var_314);
	$_var_315 = count($_var_314[1]);
	$_var_316 = false;
	$_var_317 = "";
	$_var_318 = [];
	for ($_var_319 = 0; $_var_319 < $_var_315; $_var_319++) {
		if ($_var_316) {
			$_var_317 .= "{array}";
		}
		$_var_317 .= $_var_314[1][$_var_319];
		$_var_316 = true;
	}
	if (empty($_var_317)) {
		return false;
	}
	$_var_317 = str_replace($start, "", $_var_317);
	$_var_317 = str_replace($end, "", $_var_317);
	if (empty($_var_317)) {
		return false;
	}
	return $_var_317;
}
function mac_param_url()
{
	$_var_320 = input();
	$_var_321 = [];
	$_var_322 = $_REQUEST;
	$_var_320 = array_merge($_var_320, $_var_322);
	$_var_321["page"] = intval($_var_320["page"]) < 1 ? 1 : intval($_var_320["page"]);
	$_var_321["ajax"] = intval($_var_320["ajax"]);
	$_var_321["tid"] = intval($_var_320["tid"]);
	$_var_321["mid"] = intval($_var_320["mid"]);
	$_var_321["rid"] = intval($_var_320["rid"]);
	$_var_321["pid"] = intval($_var_320["pid"]);
	$_var_321["sid"] = intval($_var_320["sid"]);
	$_var_321["nid"] = intval($_var_320["nid"]);
	$_var_321["uid"] = intval($_var_320["uid"]);
	$_var_321["level"] = intval($_var_320["level"]);
	$_var_321["score"] = intval($_var_320["score"]);
	$_var_321["limit"] = intval($_var_320["limit"]);
	$_var_321["id"] = htmlspecialchars(urldecode(trim($_var_320["id"])));
	$_var_321["ids"] = htmlspecialchars(urldecode(trim($_var_320["ids"])));
	$_var_321["wd"] = htmlspecialchars(urldecode(trim($_var_320["wd"])));
	$_var_321["en"] = htmlspecialchars(urldecode(trim($_var_320["en"])));
	$_var_321["state"] = htmlspecialchars(urldecode(trim($_var_320["state"])));
	$_var_321["area"] = htmlspecialchars(urldecode(trim($_var_320["area"])));
	$_var_321["year"] = htmlspecialchars(urldecode(trim($_var_320["year"])));
	$_var_321["lang"] = htmlspecialchars(urldecode(trim($_var_320["lang"])));
	$_var_321["letter"] = htmlspecialchars(trim($_var_320["letter"]));
	$_var_321["actor"] = htmlspecialchars(urldecode(trim($_var_320["actor"])));
	$_var_321["director"] = htmlspecialchars(urldecode(trim($_var_320["director"])));
	$_var_321["tag"] = htmlspecialchars(urldecode(trim($_var_320["tag"])));
	$_var_321["class"] = htmlspecialchars(urldecode(trim($_var_320["class"])));
	$_var_321["order"] = htmlspecialchars(urldecode(trim($_var_320["order"])));
	$_var_321["by"] = htmlspecialchars(urldecode(trim($_var_320["by"])));
	$_var_321["file"] = htmlspecialchars(urldecode(trim($_var_320["file"])));
	$_var_321["name"] = htmlspecialchars(urldecode(trim($_var_320["name"])));
	$_var_321["url"] = htmlspecialchars(urldecode(trim($_var_320["url"])));
	$_var_321["type"] = htmlspecialchars(urldecode(trim($_var_320["type"])));
	$_var_321["sex"] = htmlspecialchars(urldecode(trim($_var_320["sex"])));
	$_var_321["version"] = htmlspecialchars(urldecode(trim($_var_320["version"])));
	$_var_321["blood"] = htmlspecialchars(urldecode(trim($_var_320["blood"])));
	$_var_321["starsign"] = htmlspecialchars(urldecode(trim($_var_320["starsign"])));
	$_var_321["domain"] = htmlspecialchars(urldecode(trim($_var_320["domain"])));
	return $_var_321;
}
function mac_get_page($page)
{
	if (empty($page)) {
		$_var_323 = mac_param_url();
		$page = $_var_323["page"];
	}
	return $page;
}
function mac_tpl_fetch($model, $tpl, $def = '')
{
	return $model . "/" . (empty($tpl) ? $def : str_replace(".html", "", $tpl));
}
function mac_get_order($order, $param)
{
	if (!empty($param["order"])) {
		$order = $param["order"];
	}
	if (!in_array($order, ["asc", "desc"])) {
		$order = "desc";
	}
	return $order;
}
function mac_url_img($url)
{
	if (substr($url, 0, 4) == "mac:") {
		$_var_324 = $GLOBALS["config"]["upload"]["protocol"];
		if (empty($_var_324)) {
			$_var_324 = "http";
		}
		$url = str_replace("mac:", $_var_324 . ":", $url);
	} elseif (substr($url, 0, 4) != "http" && substr($url, 0, 2) != "//" && substr($url, 0, 1) != "/") {
		if ($GLOBALS["config"]["upload"]["mode"] == "remote") {
			$url = $GLOBALS["config"]["upload"]["remoteurl"] . $url;
		} else {
			$url = MAC_PATH . $url;
		}
	} elseif (!empty($GLOBALS["config"]["upload"]["img_key"]) && preg_match("/" . $GLOBALS["config"]["upload"]["img_key"] . "/", $url)) {
		$url = $GLOBALS["config"]["upload"]["img_api"] . "" . $url;
	}
	$url = mac_filter_xss($url);
	$url = str_replace("&quot;&gt;", "", $url);
	$url = str_replace("&amp;", "&", $url);
	return $url;
}
function mac_url_content_img($content)
{
	$_var_325 = $GLOBALS["config"]["upload"]["protocol"];
	if (empty($_var_325)) {
		$_var_325 = "http";
	}
	$content = str_replace("mac:", $_var_325 . ":", $content);
	if (!empty($GLOBALS["config"]["upload"]["img_key"])) {
		$_var_326 = mac_buildregx("<img[^>]*src\\s*=\\s*['" . Chr(34) . "]?([\\w/\\-\\:.]*)['" . Chr(34) . "]?[^>]*>", "is");
		preg_match_all($_var_326, $content, $_var_327);
		if (is_array($_var_327[1])) {
			foreach ($_var_327[1] as $_var_328 => $_var_329) {
				$_var_330 = trim(preg_replace("/[ \r\n\t ]{1,}/", " ", $_var_329));
				if (preg_match("/" . $GLOBALS["config"]["upload"]["img_key"] . "/", $_var_330)) {
					$content = str_replace($_var_330, $GLOBALS["config"]["upload"]["img_api"] . "" . $_var_330, $content);
				}
			}
		}
	}
	return $content;
}
function mac_alphaID($in, $to_num = false, $pad_up = false, $passKey = '')
{
	$_var_331 = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	if (!empty($passKey)) {
		for ($_var_332 = 0; $_var_332 < strlen($_var_331); $_var_332++) {
			$_var_333[] = substr($_var_331, $_var_332, 1);
		}
		$_var_334 = strlen($_var_331);
		$_var_335 = hash("sha256", $passKey);
		$_var_335 = strlen($_var_335) < $_var_334 ? hash("sha512", $passKey) : $_var_335;
		for ($_var_332 = 0; $_var_332 < $_var_334; $_var_332++) {
			$_var_336[] = substr($_var_335, $_var_332, 1);
		}
		array_multisort($_var_336, SORT_DESC, $_var_333);
		$_var_331 = implode($_var_333);
	}
	$_var_337 = strlen($_var_331);
	if ($to_num) {
		$in = strrev($in);
		$_var_338 = 0;
		$_var_334 = strlen($in) - 1;
		for ($_var_339 = 0; $_var_339 <= $_var_334; $_var_339++) {
			$_var_340 = bcpow($_var_337, $_var_334 - $_var_339);
			$_var_338 = $_var_338 + strpos($_var_331, substr($in, $_var_339, 1)) * $_var_340;
		}
		if (is_numeric($pad_up)) {
			$pad_up--;
			if ($pad_up > 0) {
				$_var_338 -= pow($_var_337, $pad_up);
			}
		}
	} else {
		if (is_numeric($pad_up)) {
			$pad_up--;
			if ($pad_up > 0) {
				$in += pow($_var_337, $pad_up);
			}
		}
		$_var_338 = "";
		for ($_var_339 = floor(log10($in) / log10($_var_337)); $_var_339 >= 0; $_var_339--) {
			$_var_341 = floor($in / bcpow($_var_337, $_var_339));
			$_var_338 = $_var_338 . substr($_var_331, $_var_341, 1);
			$in = $in - $_var_341 * bcpow($_var_337, $_var_339);
		}
		$_var_338 = strrev($_var_338);
	}
	return $_var_338;
}
function mac_url($_var_342, $_var_343 = [], $_var_344 = [])
{
	foreach ($_var_343 as $_var_345 => $_var_346) {
		if (empty($_var_346)) {
			unset($_var_343[$_var_345]);
		}
	}
	if (!isset($_var_343["page"])) {
		$_var_343["page"] = 1;
	}
	if ($_var_343["page"] == 1) {
		$_var_343["page"] = "";
	}
	ksort($_var_343);
	$_var_347 = $GLOBALS["config"];
	$_var_348 = ["{id}", "{en}", "{page}", "{type_id}", "{type_en}", "{type_pid}", "{type_pen}", "{md5}", "{year}", "{month}", "{day}", "{sid}", "{nid}"];
	$_var_349 = [];
	$_var_350 = $_var_347["path"]["page_sp"];
	$_var_351 = "";
	switch ($_var_342) {
		case "index/index":
			if ($_var_347["view"]["index"] == 2) {
				$_var_351 = "index";
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
			} else {
				$_var_352 = url($_var_342, $_var_343);
				if ($_var_352 == "/PAGELINK.html") {
					$_var_352 = "/index-PAGELINK.html";
				}
			}
			break;
		case "map/index":
			if ($_var_347["view"]["map"] == 2) {
				$_var_351 = "map";
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
			} else {
				$_var_352 = url($_var_342, $_var_343);
			}
			break;
		case strpos($_var_342, "rss/") !== false:
			if ($_var_347["view"]["rss"] == 2) {
				$_var_351 = $_var_342;
				if ($_var_343["page"] != "") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
				$_var_351 .= ".xml";
			} else {
				$_var_352 = url($_var_342, $_var_343, "xml");
			}
			break;
		case strpos($_var_342, "label/") !== false:
			if ($_var_347["view"]["label"] == 2) {
				$_var_351 = $_var_342;
			} else {
				$_var_352 = url($_var_342, $_var_343);
			}
			break;
		case "vod/show":
		case "art/show":
		case "actor/show":
		case "website/show":
			switch ($_var_347["rewrite"]["type_id"]) {
				case 1:
					$_var_353 = $_var_344["type_en"];
					break;
				case 2:
					$_var_353 = mac_alphaID($_var_344["type_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
					break;
				default:
					$_var_353 = $_var_344["type_id"];
					break;
			}
			if (!empty($_var_353)) {
				$_var_343["id"] = $_var_353;
			}
			$_var_352 = url($_var_342, $_var_343);
			break;
		case "vod/type":
			$_var_349 = [$_var_344["type_id"], $_var_344["type_en"], $_var_343["page"], $_var_344["type_id"], $_var_344["type"]["type_en"], $_var_344["type_1"]["type_id"], $_var_344["type_1"]["type_en"]];
			if ($_var_347["view"]["vod_type"] == 2) {
				$_var_351 = $_var_347["path"]["vod_type"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["type_id"]);
				}
				if ($_var_343["page"] != "") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				switch ($_var_347["rewrite"]["type_id"]) {
					case 1:
						$_var_353 = $_var_344["type_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["type_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["type_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353, "page" => $_var_343["page"]]);
			}
			break;
		case "vod/detail":
			$_var_349 = [$_var_344["vod_id"], $_var_344["vod_en"], "", $_var_344["type_id"], $_var_344["type"]["type_en"], $_var_344["type_1"]["type_id"], $_var_344["type_1"]["type_en"]];
			if ($_var_347["view"]["vod_detail"] == 2) {
				$_var_351 = $_var_347["path"]["vod_detail"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["vod_id"]);
				}
			} else {
				switch ($_var_347["rewrite"]["vod_id"]) {
					case 1:
						$_var_353 = $_var_344["vod_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["vod_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					case 3:
						$_var_353 = $_var_344["vod_id"] . '-' . $_var_344["vod_en"];
						break;
					case 4:
						$_var_353 = date('Y', $_var_344["vod_time"]) . '-' . $_var_344["vod_id"];
						break;
					case 5:
						$_var_353 = $_var_344["type"]["type_en"] . '-' . $_var_344["vod_id"];
						break;
					default:
						$_var_353 = $_var_344["vod_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353]);
			}
			$_var_349 = array_merge($_var_349, [date("Y", $_var_344["vod_time"]), date("m", $_var_344["vod_time"]), date("d", $_var_344["vod_time"])]);
			break;
		case "vod/play":
			$_var_349 = [$_var_344["vod_id"], $_var_344["vod_en"], "", $_var_344["type_id"], $_var_344["type"]["type_en"], $_var_344["type_1"]["type_id"], $_var_344["type_1"]["type_en"]];
			if ($_var_347["view"]["vod_play"] >= 2) {
				$_var_351 = $_var_347["path"]["vod_play"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["vod_id"]);
				}
				if ($_var_347["view"]["vod_play"] == 2) {
					$_var_351 .= "." . $_var_347["path"]["suffix"];
					$_var_351 .= "?" . $_var_344["vod_id"] . "-" . $_var_343["sid"] . "-" . $_var_343["nid"];
				} elseif ($_var_347["view"]["vod_play"] == 3) {
					$_var_351 .= $_var_347["path"]["page_sp"] . $_var_343["sid"] . $_var_347["path"]["page_sp"] . $_var_343["nid"];
				} elseif ($_var_347["view"]["vod_play"] == 4) {
					$_var_351 .= $_var_347["path"]["page_sp"] . "" . $_var_343["sid"] . $_var_347["path"]["page_sp"] . "1";
					$_var_351 .= "." . $_var_347["path"]["suffix"];
					$_var_351 .= "?" . $_var_344["vod_id"] . "-" . $_var_343["sid"] . "-" . $_var_343["nid"];
				}
			} else {
				switch ($_var_347["rewrite"]["vod_id"]) {
					case 1:
						$_var_353 = $_var_344["vod_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["vod_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["vod_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353, "sid" => $_var_343["sid"], "nid" => $_var_343["nid"]]);
			}
			$_var_349 = array_merge($_var_349, [date("Y", $_var_344["vod_time"]), date("m", $_var_344["vod_time"]), date("d", $_var_344["vod_time"]), $_var_343["sid"], $_var_343["nid"]]);
			break;
		case "vod/down":
			$_var_349 = [$_var_344["vod_id"], $_var_344["vod_en"], "", $_var_344["type_id"], $_var_344["type"]["type_en"], $_var_344["type_1"]["type_id"], $_var_344["type_1"]["type_en"]];
			if ($_var_347["view"]["vod_down"] >= 2) {
				$_var_351 = $_var_347["path"]["vod_down"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["vod_id"]);
				}
				if ($_var_347["view"]["vod_down"] == 2) {
					$_var_351 .= "." . $_var_347["path"]["suffix"];
					$_var_351 .= "?" . $_var_344["vod_id"] . "-" . $_var_343["sid"] . "-" . $_var_343["nid"];
				} elseif ($_var_347["view"]["vod_down"] == 3) {
					$_var_351 .= $_var_347["path"]["page_sp"] . $_var_343["sid"] . $_var_347["path"]["page_sp"] . $_var_343["nid"];
				} elseif ($_var_347["view"]["vod_down"] == 4) {
					$_var_351 .= $_var_347["path"]["page_sp"] . "" . $_var_343["sid"] . $_var_347["path"]["page_sp"] . "1";
					$_var_351 .= "." . $_var_347["path"]["suffix"];
					$_var_351 .= "?" . $_var_344["vod_id"] . "-" . $_var_343["sid"] . "-" . $_var_343["nid"];
				}
			} else {
				switch ($_var_347["rewrite"]["vod_id"]) {
					case 1:
						$_var_353 = $_var_344["vod_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["vod_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["vod_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353, "sid" => $_var_343["sid"], "nid" => $_var_343["nid"]]);
			}
			$_var_349 = array_merge($_var_349, [date("Y", $_var_344["vod_time"]), date("m", $_var_344["vod_time"]), date("d", $_var_344["vod_time"]), $_var_343["sid"], $_var_343["nid"]]);
			break;
		case "vod/role":
			$_var_349 = [$_var_344["vod_id"], $_var_344["vod_en"], "", $_var_344["type_id"], $_var_344["type"]["type_en"], $_var_344["type_1"]["type_id"], $_var_344["type_1"]["type_en"]];
			if ($_var_347["view"]["vod_role"] == 2) {
				$_var_351 = $_var_347["path"]["vod_role"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["vod_id"]);
				}
			} else {
				switch ($_var_347["rewrite"]["vod_id"]) {
					case 1:
						$_var_353 = $_var_344["vod_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["vod_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["vod_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353]);
			}
			$_var_349 = array_merge($_var_349, [date("Y", $_var_344["vod_time"]), date("m", $_var_344["vod_time"]), date("d", $_var_344["vod_time"])]);
			break;
		case "vod/plot":
			$_var_349 = [$_var_344["vod_id"], $_var_344["vod_en"], $_var_343["page"], $_var_344["type_id"], $_var_344["type"]["type_en"], $_var_344["type_1"]["type_id"], $_var_344["type_1"]["type_en"]];
			if ($_var_347["view"]["vod_plot"] == 2) {
				$_var_351 = $_var_347["path"]["vod_plot"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["vod_id"]);
				}
				if ($_var_343["page"] != "") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				switch ($_var_347["rewrite"]["vod_id"]) {
					case 1:
						$_var_353 = $_var_344["vod_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["vod_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["vod_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353, "page" => $_var_343["page"]]);
			}
			$_var_349 = array_merge($_var_349, [date("Y", $_var_344["vod_time"]), date("m", $_var_344["vod_time"]), date("d", $_var_344["vod_time"])]);
			break;
		case "art/type":
			$_var_349 = [$_var_344["type_id"], $_var_344["type_en"], $_var_343["page"], $_var_344["type_id"], $_var_344["type"]["type_en"], $_var_344["type_1"]["type_id"], $_var_344["type_1"]["type_en"]];
			if ($_var_347["view"]["art_type"] == 2) {
				$_var_351 = $_var_347["path"]["art_type"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["type_id"]);
				}
				if ($_var_343["page"] != "") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				switch ($_var_347["rewrite"]["type_id"]) {
					case 1:
						$_var_353 = $_var_344["type_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["type_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["type_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353, "page" => $_var_343["page"]]);
			}
			break;
		case "art/detail":
			$_var_349 = [$_var_344["art_id"], $_var_344["art_en"], "", $_var_344["type_id"], $_var_344["type"]["type_en"], $_var_344["type_1"]["type_id"], $_var_344["type_1"]["type_en"]];
			if ($_var_347["view"]["art_detail"] == 2) {
				$_var_351 = $_var_347["path"]["art_detail"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["art_id"]);
				}
				if ($_var_343["page"] > 1 || $_var_343["page"] == "PAGELINK") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				switch ($_var_347["rewrite"]["art_id"]) {
					case 1:
						$_var_353 = $_var_344["art_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["art_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["art_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353, "page" => $_var_343["page"]]);
			}
			$_var_349 = array_merge($_var_349, [date("Y", $_var_344["art_time"]), date("m", $_var_344["art_time"]), date("d", $_var_344["art_time"])]);
			break;
		case "topic/index":
			if ($_var_347["view"]["topic_index"] == 2) {
				$_var_351 = $_var_347["path"]["topic_index"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if ($_var_343["page"] > 1 || $_var_343["page"] == "PAGELINK") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				$_var_352 = url($_var_342, ["page" => $_var_343["page"]]);
			}
			break;
		case "topic/detail":
			$_var_349 = [$_var_344["topic_id"], $_var_344["topic_en"], "", "", "", "", ""];
			if ($_var_347["view"]["topic_detail"] == 2) {
				$_var_351 = $_var_347["path"]["topic_detail"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["topic_id"]);
				}
			} else {
				switch ($_var_347["rewrite"]["topic_id"]) {
					case 1:
						$_var_353 = $_var_344["topic_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["topic_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["topic_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353]);
			}
			break;
		case "actor/index":
			if ($_var_347["view"]["actor_index"] == 2) {
				$_var_351 = $_var_347["path"]["actor_index"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if ($_var_343["page"] > 1 || $_var_343["page"] == "PAGELINK") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				$_var_352 = url($_var_342, ["page" => $_var_343["page"]]);
			}
			break;
		case "actor/type":
			$_var_349 = [$_var_344["type_id"], $_var_344["type_en"], $_var_343["page"], $_var_344["type_id"], $_var_344["type"]["type_en"], $_var_344["type_1"]["type_id"], $_var_344["type_1"]["type_en"]];
			if ($_var_347["view"]["actor_type"] == 2) {
				$_var_351 = $_var_347["path"]["actor_type"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["type_id"]);
				}
				if ($_var_343["page"] != "") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				switch ($_var_347["rewrite"]["type_id"]) {
					case 1:
						$_var_353 = $_var_344["type_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["type_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["type_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353, "page" => $_var_343["page"]]);
			}
			break;
		case "actor/detail":
			$_var_349 = [$_var_344["actor_id"], $_var_344["actor_en"], "", "", "", "", ""];
			if ($_var_347["view"]["actor_detail"] == 2) {
				$_var_351 = $_var_347["path"]["actor_detail"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["actor_id"]);
				}
			} else {
				switch ($_var_347["rewrite"]["actor_id"]) {
					case 1:
						$_var_353 = $_var_344["actor_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["actor_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["actor_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353]);
			}
			break;
		case "role/index":
			if ($_var_347["view"]["role_index"] == 2) {
				$_var_351 = $_var_347["path"]["role_index"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if ($_var_343["page"] > 1 || $_var_343["page"] == "PAGELINK") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				$_var_352 = url($_var_342, ["page" => $_var_343["page"]]);
			}
			break;
		case "role/detail":
			$_var_349 = [$_var_344["role_id"], $_var_344["actor_en"], "", "", "", "", ""];
			if ($_var_347["view"]["role_detail"] == 2) {
				$_var_351 = $_var_347["path"]["role_detail"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["role_id"]);
				}
			} else {
				switch ($_var_347["rewrite"]["role_id"]) {
					case 1:
						$_var_353 = $_var_344["role_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["role_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["role_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353]);
			}
			break;
		case "plot/index":
			if ($_var_347["view"]["plot_index"] == 2) {
				$_var_351 = $_var_347["path"]["plot_index"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if ($_var_343["page"] > 1 || $_var_343["page"] == "PAGELINK") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				$_var_352 = url($_var_342, ["page" => $_var_343["page"]]);
			}
			break;
		case "plot/detail":
			$_var_349 = [$_var_344["vod_id"], $_var_344["vod_en"], "", $_var_344["type_id"], $_var_344["type"]["type_en"], $_var_344["type_1"]["type_id"], $_var_344["type_1"]["type_en"]];
			if ($_var_347["view"]["plot_detail"] == 2) {
				$_var_351 = $_var_347["path"]["plot_detail"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["vod_id"]);
				}
				if ($_var_343["page"] > 1 || $_var_343["page"] == "PAGELINK") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				switch ($_var_347["rewrite"]["vod_id"]) {
					case 1:
						$_var_353 = $_var_344["vod_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["vod_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["vod_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353, "page" => $_var_343["page"]]);
			}
			$_var_349 = array_merge($_var_349, [date("Y", $_var_344["vod_time"]), date("m", $_var_344["vod_time"]), date("d", $_var_344["vod_time"])]);
			break;
		case "website/index":
			if ($_var_347["view"]["website_index"] == 2) {
				$_var_351 = $_var_347["path"]["website_index"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if ($_var_343["page"] > 1 || $_var_343["page"] == "PAGELINK") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				$_var_352 = url($_var_342, ["page" => $_var_343["page"]]);
			}
			break;
		case "website/type":
			$_var_349 = [$_var_344["type_id"], $_var_344["type_en"], $_var_343["page"], $_var_344["type_id"], $_var_344["type"]["type_en"], $_var_344["type_1"]["type_id"], $_var_344["type_1"]["type_en"]];
			if ($_var_347["view"]["website_type"] == 2) {
				$_var_351 = $_var_347["path"]["website_type"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["type_id"]);
				}
				if ($_var_343["page"] != "") {
					$_var_351 .= $_var_350 . $_var_343["page"];
				}
			} else {
				switch ($_var_347["rewrite"]["type_id"]) {
					case 1:
						$_var_353 = $_var_344["type_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["type_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["type_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353, "page" => $_var_343["page"]]);
			}
			break;
		case "website/detail":
			$_var_349 = [$_var_344["website_id"], $_var_344["website_en"], "", "", "", "", ""];
			if ($_var_347["view"]["website_detail"] == 2) {
				$_var_351 = $_var_347["path"]["website_detail"];
				if (substr($_var_351, strlen($_var_351) - 1, 1) == "/") {
					$_var_351 .= "index";
				}
				if (strpos($_var_351, "{md5}") !== false) {
					$_var_349[] = md5($_var_344["website_id"]);
				}
			} else {
				switch ($_var_347["rewrite"]["website_id"]) {
					case 1:
						$_var_353 = $_var_344["website_en"];
						break;
					case 2:
						$_var_353 = mac_alphaID($_var_344["website_id"], false, $_var_347["rewrite"]["encode_len"], $_var_347["rewrite"]["encode_key"]);
						break;
					default:
						$_var_353 = $_var_344["website_id"];
						break;
				}
				$_var_352 = url($_var_342, ["id" => $_var_353]);
			}
			break;
		case "gbook/index":
			$_var_352 = url($_var_342, ["page" => $_var_343["page"]]);
			break;
		case "comment/index":
			$_var_352 = url($_var_342, ["page" => $_var_343["page"]]);
			break;
		default:
			$_var_352 = url($_var_342, $_var_343);
			break;
	}
	if (!empty($_var_351)) {
		$_var_351 = str_replace($_var_348, $_var_349, $_var_351);
		$_var_351 = str_replace("//", "/", $_var_351);
		$_var_354 = false;
		if (substr($_var_351, strlen($_var_351) - 6) == "/index") {
			$_var_354 = true;
			$_var_351 = substr($_var_351, 0, strlen($_var_351) - 5);
		}
		if ($_var_354 == false && strpos($_var_351, ".") === false) {
			$_var_351 .= "." . $_var_347["path"]["suffix"];
		}
		$_var_352 = $_var_351;
		if (substr($_var_351, 0, 1) != "/") {
			$_var_352 = MAC_PATH . $_var_351;
		}
	} else {
		if (ENTRANCE != "index") {
			$_var_355 = MAC_PATH;
			if ($_var_347["rewrite"]["status"] == 0) {
				$_var_355 = MAC_PATH . "index.php/";
			}
			if (!empty(IN_FILE)) {
				$_var_352 = str_replace(IN_FILE . "/", $_var_355, $_var_352);
				$_var_352 = str_replace(ENTRANCE . "/", "", $_var_352);
			}
		} elseif ($_var_347["rewrite"]["status"] == 0 && strpos($_var_352, "index.php") === false) {
			if (MAC_PATH != "/") {
				$_var_352 = str_replace(MAC_PATH, "/", $_var_352);
			}
			$_var_352 = MAC_PATH . "index.php" . $_var_352;
		} elseif ($_var_347["rewrite"]["status"] == 1 && strpos($_var_352, "index.php") !== false) {
			$_var_352 = str_replace("index.php/", "", $_var_352);
		}
		if ($_var_347["rewrite"]["suffix_hide"] == 1) {
			$_var_352 = str_replace(".html", "/", $_var_352);
			if (strpos($_var_342, "/show") === false && strpos($_var_342, "/search") === false) {
				$_var_352 = str_replace(["-/", "_/", "-.", "_."], "/", $_var_352);
			}
		} else {
			if (strpos($_var_342, "search") === false && strpos($_var_342, "show") === false) {
				$_var_352 = str_replace(["-.", "/."], ".", $_var_352);
			}
		}
	}
	return $_var_352;
}
function mac_url_page($url, $num)
{
	$url = str_replace(MAC_PAGE_SP . "PAGELINK", $num > 1 ? MAC_PAGE_SP . $num : "", $url);
	$url = str_replace("PAGELINK", $num, $url);
	return $url;
}
function mac_url_create($_var_356, $_var_357 = 'actor', $_var_358 = 'vod', $_var_359 = 'search', $_var_360 = '&nbsp;')
{
	if (!$_var_356) {
		return "未知";
	}
	$_var_361 = [];
	$_var_356 = str_replace(array("/", "|", ",", "，", " "), ",", $_var_356);
	$_var_362 = explode(",", $_var_356);
	foreach ($_var_362 as $_var_363 => $_var_364) {
		$_var_361[$_var_363] = "<a href=\"" . mac_url($_var_358 . "/" . $_var_359, [$_var_357 => $_var_364]) . "\" target=\"_blank\">" . $_var_364 . "</a>" . $_var_360;
	}
	return implode("", $_var_361);
}
function mac_url_search($param = [], $flag = 'vod')
{
	return mac_url($flag . "/search", $param);
}
function mac_url_type($_var_365, $_var_366 = [], $_var_367 = 'type')
{
    $_var_368 = "vod";

    // 根据 type_mid 确定分类
    if ($_var_365["type_mid"] == 1) {
    } else {
        if ($_var_365["type_mid"] == 2) {
            $_var_368 = "art";
        } else {
            if ($_var_365["type_mid"] == 8) {
                $_var_368 = "actor";
            } else {
                if ($_var_365["type_mid"] == 11) {
                    $_var_368 = "website";
                }
            }
        }
    }

    // 添加映射逻辑
    $category_map = config('maccms.category_map'); // 获取分类映射表
    if (!empty($category_map) && isset($category_map[$_var_365['type_en']])) {
        $_var_365['type_en'] = $category_map[$_var_365['type_en']];
    }

    if (empty($_var_366["id"])) {
        $_var_366["id"] = $_var_365["type_id"];
    }

    // 调用 mac_url 返回最终 URL
    return mac_url($_var_368 . "/" . $_var_367, $_var_366, $_var_365);
}

function mac_url_topic_index($param = [])
{
	return mac_url("topic/index", ["page" => $param["page"]]);
}
function mac_url_topic_detail($info)
{
	return mac_url("topic/detail", [], $info);
}
function mac_url_role_index($_var_369 = [])
{
	return mac_url("role/index", ["page" => $_var_369["page"]]);
}
function mac_url_role_detail($info)
{
	return mac_url("role/detail", [], $info);
}
function mac_url_actor_index($param = [])
{
	return mac_url("actor/index", ["page" => $param["page"]]);
}
function mac_url_actor_detail($_var_370)
{
	return mac_url("actor/detail", [], $_var_370);
}
function mac_url_actor_search($param)
{
	return mac_url("actor/search", $param);
}
function mac_url_plot_index($_var_371 = [])
{
	return mac_url("plot/index", ["page" => $_var_371["page"]]);
}
function mac_url_plot_detail($_var_372, $_var_373 = [])
{
	return mac_url("plot/detail", ["page" => $_var_373["page"]], $_var_372);
}
function mac_url_vod_plot($info, $param = [])
{
	return mac_url("vod/plot", $param, $info);
}
function mac_url_website_index($param = [])
{
	return mac_url("website/index", ["page" => $param["page"]]);
}
function mac_url_website_detail($info)
{
	return mac_url("website/detail", [], $info);
}
function mac_url_website_search($_var_374)
{
	return mac_url("website/search", $_var_374);
}
function mac_url_art_index($_var_375 = [])
{
	return mac_url("art/index", ["page" => $_var_375["page"]]);
}
function mac_url_art_detail($info, $param = [])
{
	return mac_url("art/detail", ["page" => $param["page"]], $info);
}
function mac_url_art_search($_var_376)
{
	return mac_url("art/search", $_var_376);
}
function mac_url_vod_index($param = [])
{
	return mac_url("vod/index", ["page" => $param["page"]]);
}
function mac_url_vod_detail($info)
{
	return mac_url("vod/detail", [], $info);
}
function mac_url_vod_search($param)
{
	return mac_url("vod/search", $param);
}
function mac_url_vod_play($_var_377, $_var_378 = [])
{
	if ($_var_378 == "first") {
		$_var_379 = intval(key($_var_377["vod_play_list"]));
		$_var_380 = intval(key($_var_377["vod_play_list"][$_var_379]["urls"]));
		if ($_var_379 == 0 || $_var_380 == 0) {
			return "";
		}
		$_var_378 = [];
		$_var_378["sid"] = $_var_379;
		$_var_378["nid"] = $_var_380;
	}
	if (intval($_var_378["sid"]) < 1) {
		$_var_378["sid"] = 1;
	}
	if (intval($_var_378["nid"]) < 1) {
		$_var_378["nid"] = 1;
	}
	return mac_url("vod/play", ["sid" => $_var_378["sid"], "nid" => $_var_378["nid"]], $_var_377);
}
function mac_url_vod_down($info, $param = [])
{
	if ($param == "first") {
		$_var_381 = intval(key($info["vod_down_list"]));
		$_var_382 = intval(key($info["vod_down_list"][$_var_381]["urls"]));
		if ($_var_381 == 0 || $_var_382 == 0) {
			return "";
		}
		$param = [];
		$param["sid"] = $_var_381;
		$param["nid"] = $_var_382;
	}
	if (intval($param["sid"]) < 1) {
		$param["sid"] = 1;
	}
	if (intval($param["nid"]) < 1) {
		$param["nid"] = 1;
	}
	return mac_url("vod/down", ["sid" => $param["sid"], "nid" => $param["nid"]], $info);
}
function mac_label_website_detail($param)
{
	$_var_383 = [];
	if ($GLOBALS["config"]["rewrite"]["website_id"] == 1) {
		$_var_383["website_en"] = ["eq", $param["id"]];
	} else {
		if ($GLOBALS["config"]["rewrite"]["website_id"] == 2) {
			$param["id"] = mac_alphaID($param["id"], true, $GLOBALS["config"]["rewrite"]["encode_len"], $GLOBALS["config"]["rewrite"]["encode_key"]);
		}
		$_var_383["website_id"] = ["eq", $param["id"]];
	}
	$_var_383["website_status"] = ["eq", 1];
	$_var_384 = model("Website")->infoData($_var_383, "*", 1);
	$GLOBALS["type_id"] = $_var_384["info"]["type_id"];
	$GLOBALS["type_pid"] = $_var_384["info"]["type"]["type_pid"];
	return $_var_384;
}
function mac_label_actor_detail($param)
{
	$_var_385 = [];
	if ($GLOBALS["config"]["rewrite"]["actor_id"] == 1) {
		$_var_385["actor_en"] = ["eq", $param["id"]];
	} else {
		if ($GLOBALS["config"]["rewrite"]["actor_id"] == 2) {
			$param["id"] = mac_alphaID($param["id"], true, $GLOBALS["config"]["rewrite"]["encode_len"], $GLOBALS["config"]["rewrite"]["encode_key"]);
		}
		$_var_385["actor_id"] = ["eq", $param["id"]];
	}
	$_var_385["actor_status"] = ["eq", 1];
	$_var_386 = model("Actor")->infoData($_var_385, "*", 1);
	$GLOBALS["type_id"] = $_var_386["info"]["type_id"];
	$GLOBALS["type_pid"] = $_var_386["info"]["type"]["type_pid"];
	return $_var_386;
}
function mac_label_role_detail($_var_387)
{
	$_var_388 = [];
	if ($GLOBALS["config"]["rewrite"]["role_id"] == 1) {
		$_var_388["role_en"] = ["eq", $_var_387["id"]];
	} else {
		if ($GLOBALS["config"]["rewrite"]["role_id"] == 2) {
			$_var_387["id"] = mac_alphaID($_var_387["id"], true, $GLOBALS["config"]["rewrite"]["encode_len"], $GLOBALS["config"]["rewrite"]["encode_key"]);
		}
		$_var_388["role_id"] = ["eq", $_var_387["id"]];
	}
	$_var_388["role_status"] = ["eq", 1];
	$_var_389 = model("Role")->infoData($_var_388, "*", 1);
	return $_var_389;
}
function mac_label_topic_detail($param)
{
	$_var_390 = [];
	if ($GLOBALS["config"]["rewrite"]["topic_id"] == 1) {
		$_var_390["topic_en"] = ["eq", $param["id"]];
	} else {
		if ($GLOBALS["config"]["rewrite"]["topic_id"] == 2) {
			$param["id"] = mac_alphaID($param["id"], true, $GLOBALS["config"]["rewrite"]["encode_len"], $GLOBALS["config"]["rewrite"]["encode_key"]);
		}
		$_var_390["topic_id"] = ["eq", $param["id"]];
	}
	$_var_390["topic_status"] = ["eq", 1];
	$_var_391 = model("Topic")->infoData($_var_390, "*", 1);
	return $_var_391;
}
function mac_label_art_detail($param)
{
	$_var_392 = [];
	if ($GLOBALS["config"]["rewrite"]["art_id"] == 1) {
		$_var_392["art_en"] = ["eq", $param["id"]];
	} else {
		if ($GLOBALS["config"]["rewrite"]["art_id"] == 2) {
			$param["id"] = mac_alphaID($param["id"], true, $GLOBALS["config"]["rewrite"]["encode_len"], $GLOBALS["config"]["rewrite"]["encode_key"]);
		}
		$_var_392["art_id"] = ["eq", $param["id"]];
	}
	$_var_392["art_status"] = ["eq", 1];
	$_var_393 = model("Art")->infoData($_var_392, "*", 1);
	if ($_var_393["code"] == 1) {
		if ($param["page"] > $_var_393["info"]["art_page_total"]) {
			$param["page"] = $_var_393["info"]["art_page_total"];
		}
	}
	$GLOBALS["type_id"] = $_var_393["info"]["type_id"];
	$GLOBALS["type_pid"] = $_var_393["info"]["type"]["type_pid"];
	return $_var_393;
}
function mac_label_vod_detail($param)
{
	$_var_394 = [];
	if ($GLOBALS["config"]["rewrite"]["vod_id"] == 1) {
		$_var_394["vod_en"] = ["eq", $param["id"]];
	} else {
		if ($GLOBALS["config"]["rewrite"]["vod_id"] == 2) {
			$param["id"] = mac_alphaID($param["id"], true, $GLOBALS["config"]["rewrite"]["encode_len"], $GLOBALS["config"]["rewrite"]["encode_key"]);
		}
		$_var_394["vod_id"] = ["eq", $param["id"]];
	}
	$_var_394["vod_status"] = ["eq", 1];
	$_var_395 = model("Vod")->infoData($_var_394, "*", 1);
	$GLOBALS["type_id"] = $_var_395["info"]["type_id"];
	$GLOBALS["type_pid"] = $_var_395["info"]["type"]["type_pid"];
	return $_var_395;
}
function mac_label_vod_role($_var_396)
{
	$_var_397 = [];
	$_var_397["role_rid"] = $_var_396["rid"];
	$_var_397["role_status"] = ["eq", 1];
	$_var_398 = "role_sort desc,role_id desc";
	$_var_399 = model("Role")->listData($_var_397, $_var_398, 1, 999, 0, "*", 0, 0);
	return $_var_399;
}
function mac_label_type($param, $type_id_specified)
{
	if ($type_id_specified > 0) {
		$_var_400 = $type_id_specified;
	} else {
		if ($GLOBALS["config"]["rewrite"]["type_id"] == 1) {
		} else {
			if ($GLOBALS["config"]["rewrite"]["type_id"] == 2) {
				$param["id"] = mac_alphaID($param["id"], true, $GLOBALS["config"]["rewrite"]["encode_len"], $GLOBALS["config"]["rewrite"]["encode_key"]);
			}
		}
		$_var_400 = $param["id"];
	}
	$_var_401 = model("Type")->getCacheInfo($_var_400);
	$GLOBALS["type_id"] = $_var_401["type_id"];
	$GLOBALS["type_pid"] = $_var_401["type_pid"];
	$_var_402 = model("Type")->getCacheInfo($_var_401["type_pid"]);
	$_var_401["parent"] = $_var_402;
	return $_var_401;
}
function mac_data_count($_var_403 = 0, $_var_404 = 'all', $_var_405 = 'vod')
{
	if (!in_array($_var_405, ["vod", "art", "actor", "role", "topic", "website"])) {
		$_var_405 = "vod";
	}
	if (!in_array($_var_404, ["all", "today", "min"])) {
		$_var_404 = "all";
	}
	$_var_406 = model("Extend")->dataCount();
	$_var_407 = "type_" . $_var_404 . "_" . $_var_403;
	if ($_var_403 > 0 && in_array($_var_405, ["vod", "art"])) {
	} else {
		$_var_407 = $_var_405 . "_" . $_var_404;
	}
	return intval($_var_406[$_var_407]);
}
function mac_get_popedom_filter($group_type, $type_list = [])
{
	if (empty($type_list)) {
		$type_list = model("Type")->getCache("type_list");
	}
	$_var_408 = array_keys($type_list);
	$group_type = trim($group_type, ",");
	$_var_409 = explode(",", $group_type);
	$_var_410 = array_diff($_var_408, $_var_409);
	return implode(",", $_var_410);
}
function reset_html_filename($_var_411)
{
	$_var_412 = "./";
	if (substr($_var_411, strlen($_var_411) - 1, 1) == "/") {
		$_var_411 .= "index";
	}
	if (strpos($_var_411, ".") === false) {
		$_var_411 .= "." . $GLOBALS["config"]["path"]["suffix"];
	}
	if (strpos($_var_411, "?") !== false) {
		$_var_411 = substr($_var_411, 0, strpos($_var_411, "?"));
	}
	$_var_411 = $_var_412 . $_var_411;
	$_var_411 = str_replace("//", "/", $_var_411);
	if (MAC_PATH != "/") {
		$_var_411 = str_replace("." . MAC_PATH, "./", $_var_411);
	}
	$_var_411 = str_replace("//", "/", $_var_411);
	return $_var_411;
}
function mac_unicode_encode($str, $encoding = 'UTF-8', $prefix = '&#', $postfix = ';')
{
	$str = iconv($encoding, "UCS-2", $str);
	$_var_413 = str_split($str, 2);
	$_var_414 = "";
	for ($_var_415 = 0, $_var_416 = count($_var_413); $_var_415 < $_var_416; $_var_415++) {
		$_var_417 = hexdec(bin2hex($_var_413[$_var_415]));
		$_var_414 .= $prefix . $_var_417 . $postfix;
	}
	return $_var_414;
}
function mac_unicode_decode($unistr, $encoding = 'UTF-8', $prefix = '&#', $postfix = ';')
{
	$_var_418 = explode($prefix, $unistr);
	$unistr = "";
	for ($_var_419 = 1, $_var_420 = count($_var_418); $_var_419 < $_var_420; $_var_419++) {
		if (strlen($postfix) > 0) {
			$_var_418[$_var_419] = substr($_var_418[$_var_419], 0, strlen($_var_418[$_var_419]) - strlen($postfix));
		}
		$_var_421 = intval($_var_418[$_var_419]);
		$unistr .= $_var_421 < 256 ? Chr(0) . Chr($_var_421) : chr($_var_421 / 256) . Chr($_var_421 % 256);
	}
	return iconv("UCS-2", $encoding, $unistr);
}
function mac_escape_param($_var_422)
{
	if (is_array($_var_422)) {
		foreach ($_var_422 as $_var_423 => $_var_424) {
			if (!is_numeric($_var_424) && !empty($_var_424)) {
				if ($GLOBALS["config"]["app"]["wall_filter"] == 1) {
					$_var_424 = mac_unicode_encode($_var_424);
				} elseif ($GLOBALS["config"]["app"]["wall_filter"] == 2) {
					$_var_424 = "";
				}
				$_var_422[$_var_423] = $_var_424;
			}
		}
	} else {
		if (!is_numeric($_var_422) && !empty($_var_422)) {
			if ($GLOBALS["config"]["app"]["wall_filter"] == 1) {
				$_var_422 = mac_unicode_encode($_var_422);
			} elseif ($GLOBALS["config"]["app"]["wall_filter"] == 2) {
				$_var_422 = "";
			}
		}
	}
	return $_var_422;
}
function mac_search_len_check($param)
{
	$_var_425 = array("wd", "tag", "class", "letter", "name", "state", "level", "area", "lang", "version", "actor", "director", "starsign", "blood");
	foreach ($_var_425 as $_var_426) {
		if (mb_strlen($param[$_var_426]) > $GLOBALS["config"]["app"]["search_len"]) {
			$param[$_var_426] = mac_substring($param[$_var_426], $GLOBALS["config"]["app"]["search_len"]);
		}
	}
	return $param;
}
function mac_no_cahche()
{
	@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
	@header("Cache-Control: no-cache, must-revalidate");
	@header("Pragma: no-cache");
}
function mac_filter_tags($rs)
{
	$_var_427 = array("{:", "<script", "<iframe", "<frameset", "<object", "onerror");
	if (is_array($rs)) {
		foreach ($rs as $_var_428 => $_var_429) {
			if (!is_numeric($_var_429)) {
				$rs[$_var_428] = str_ireplace($_var_427, "*", $rs[$_var_428]);
			}
		}
	} else {
		if (!is_numeric($rs)) {
			$rs = str_ireplace($_var_427, "*", $rs);
		}
	}
	return $rs;
}
function wanneng($_var_430)
{
	$_var_431 = $_var_430;
	if (!file_exists($_var_430)) {
		foreach (glob(ROOT_PATH . "/data/" . $_var_430 . "/*.txt") as $_var_432) {
			$_var_433[] = basename($_var_432);
		}
		$_var_431 = ROOT_PATH . "/data/" . $_var_430 . "/" . $_var_433[mt_rand(0, count($_var_433) - 1)];
	}
	$_var_434 = file($_var_431);
	$_var_435 = $_var_434[mt_rand(0, count($_var_434) - 1)];
	return trim(strToUtf8($_var_435));
}
function sjvod($_var_436, $_var_437 = 3)
{
	$_var_438 = explode(",", $_var_436);
	shuffle($_var_438);
	$_var_439 = array_slice($_var_438, 0, $_var_437);
	return implode(",", $_var_439);
}
function gpt($_var_440 = 'gpt')
{
	$_var_441 = ROOT_PATH . "data/" . $_var_440;
	$_var_442 = opendir($_var_441);
	static $_var_443;
	if (empty($_var_443[$_var_440])) {
		$_var_443[$_var_440]["files"] = [];
		$_var_443[$_var_440]["count"] = 0;
		while (($_var_444 = readdir($_var_442)) !== false) {
			if ($_var_444 == "." || $_var_444 == "..") {
				continue;
			}
			$_var_443[$_var_440]["count"]++;
			$_var_443[$_var_440]["files"][] = $_var_444;
		}
	}
	$_var_445 = rand(1, $_var_443[$_var_440]["count"] - 1);
	$_var_446 = $_var_443[$_var_440]["files"][$_var_445];
	$_var_447 = str_replace(".txt", "", $_var_446);
	$_var_448["name"] = $_var_447;
	$_var_448["ml"] = $_var_440;
	return $_var_448;
}
function gpttitle()
{
	$_var_449 = glob(ROOT_PATH . "data/gpt/*");
	$_var_450 = rand(0, count($_var_449) - 1);
	$_var_451 = $_var_449[$_var_450];
	$_var_452 = explode("/", $_var_451);
	$_var_453 = str_replace(".txt", "", end($_var_452));
	return $_var_453;
}
function cut_str($content, $cut = 120, $str = "...")
{
	$content = strip_tags($content);
	if (!is_numeric($cut)) {
		return $content;
	}
	if ($cut > 0) {
		$content = mb_substr($content, 0, $cut);
	}
	return myTrim($content);
}
function strToUtf8($_var_454)
{
	$_var_455 = mb_detect_encoding($_var_454, array("ASCII", "UTF-8", "GB2312", "GBK", "BIG5"));
	if ($_var_455 == "UTF-8") {
		return $_var_454;
	} else {
		return mb_convert_encoding($_var_454, "UTF-8", $_var_455);
	}
}
function utf8_to_gb2312($str_caiji)
{
	return mb_convert_encoding($str_caiji, "utf-8", "gb2312");
}
function myTrim($_var_456)
{
	$_var_457 = array(" ", "　", "\n", "\r", "\t");
	$_var_458 = array("", "", "", "", "");
	return str_replace($_var_457, $_var_458, $_var_456);
}
function suiji($_var_459)
{
	$_var_460 = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
	$_var_461 = count($_var_460) - 1;
	$_var_462 = "";
	for ($_var_463 = 0; $_var_463 < $_var_459; $_var_463++) {
		$_var_462 .= $_var_460[mt_rand(0, $_var_461)];
	}
	return $_var_462;
}
function suijis($len)
{
	$_var_464 = array("<fss dropzone=\"[xwd]\"></fss>", "<subs lang=\"[xwd]\"></subs>", "<acronym draggable=\"[xwd]\"></acronym>", "<time dropzone=\"[xwd]\"></time>", "<sdu date-time=\"[xwd]\"></sdu>", "<tcenter draggable=\"[xwd]\"></tcenter>", "<stysle draggable=\"[xwd]\"></stysle>", "<ssmall draggable=\"[xwd]\"></ssmall>", "<areass draggable=\"[xwd]\"></areass>", "<is lang=\"[xwd]\"></is>", "<abbr date-time=\"[xwd]\"></abbr>", "<acronym id=\"[xwd]\"></acronym>", "<sadw dir=\"[xwd]\"></sadw>", "<dfn id=\"[xwd]\"></dfn>", "<sadw lang=\"[xwd]\"></sadw>", "<time dir=\"[xwd]\"></time>", "<map lang=\"CnwpIA\"></map>", "<areass date-time=\"[xwd]\"></areass>", "<sup draggable=\"[xwd]\"></sup>", "<kbd lang=\"[xwd]\"></kbd>", "<noscripta draggable=\"[xwd]\"></noscripta>", "<ecode date-time=\"[xwd]\"></ecode>", "<is dropzone=\"[xwd]\"></is>", "<font dir=\"[xwd]\"></font>", "<tyyt draggable=\"[xwd]\"></tyyt>", "<sup date-time=\"[xwd]\"></sup>", "<sgaddress lang=\"[xwd]\"></sgaddress>");
	$_var_465 = count($_var_464) - 1;
	$_var_466 = "";
	for ($_var_467 = 0; $_var_467 < $len; $_var_467++) {
		$_var_468 = $_var_464[mt_rand(0, $_var_465)];
		$_var_469 = suiji(mt_rand(3, 10));
		$_var_466 .= str_replace("[xwd]", $_var_469, $_var_468);
	}
	return $_var_466;
}
function url_root($_var_470 = "")
{
	$_var_470 = $_var_470 ? $_var_470 : $_SERVER["HTTP_HOST"];
	$_var_471 = array("aaa.pro", "ac.cn", "ac.kr", "ac.mu", "aca.pro", "acct.pro", "ae.org", "ah.cn", "ar.com", "avocat.pro", "bar.pro", "biz.ki", "biz.pl", "bj.cn", "br.com", "busan.kr", "chungbuk.kr", "chungnam.kr", "club.tw", "cn.com", "co.ag", "co.am", "co.at", "co.bz", "co.cm", "co.com", "co.gg", "co.gl", "co.gy", "co.il", "co.im", "co.in", "co.je", "co.kr", "co.lc", "co.mg", "co.ms", "co.mu", "co.nl", "co.nz", "co.uk", "co.ve", "co.za", "com.af", "com.ag", "com.am", "com.ar", "com.au", "com.br", "com.bz", "com.cm", "com.cn", "com.co", "com.de", "com.ec", "com.es", "com.gl", "com.gr", "com.gy", "com.hn", "com.ht", "com.im", "com.ki", "com.lc", "com.lv", "com.mg", "com.ms", "com.mu", "com.mx", "com.nf", "com.pe", "com.ph", "com.pk", "com.pl", "com.ps", "com.pt", "com.ro", "com.ru", "com.sb", "com.sc", "com.se", "com.sg", "com.so", "com.tw", "com.vc", "com.ve", "cpa.pro", "cq.cn", "daegu.kr", "daejeon.kr", "de.com", "ebiz.tw", "edu.cn", "edu.gl", "eng.pro", "es.kr", "eu.com", "fin.ec", "firm.in", "fj.cn", "game.tw", "gangwon.kr", "gb.com", "gb.net", "gd.cn", "gen.in", "go.kr", "gov.cn", "gr.com", "gs.cn", "gwangju.kr", "gx.cn", "gyeongbuk.kr", "gyeonggi.kr", "gyeongnam.kr", "gz.cn", "ha.cn", "hb.cn", "he.cn", "hi.cn", "hk.cn", "hl.cn", "hn.cn", "hs.kr", "hu.com", "hu.net", "idv.tw", "in.net", "incheon.kr", "ind.in", "info.ec", "info.ht", "info.ki", "info.nf", "info.pl", "info.ve", "jeju.kr", "jeonbuk.kr", "jeonnam.kr", "jl.cn", "jp.net", "jpn.com", "js.cn", "jur.pro", "jx.cn", "kg.kr", "kiwi.nz", "kr.com", "law.pro", "ln.cn", "me.uk", "med.ec", "med.pro", "mex.com", "mo.cn", "ms.kr", "ne.kr", "net.af", "net.ag", "net.am", "net.br", "net.bz", "net.cm", "net.cn", "net.co", "net.ec", "net.gg", "net.gl", "net.gr", "net.gy", "net.hn", "net.ht", "net.im", "net.in", "net.je", "net.ki", "net.lc", "net.lv", "net.mg", "net.mu", "net.my", "net.nf", "net.nz", "net.ph", "net.pk", "net.pl", "net.ps", "net.ru", "net.sb", "net.sc", "net.so", "net.vc", "net.ve", "nm.cn", "no.com", "nom.ag", "nom.co", "nom.es", "nom.ro", "nx.cn", "or.at", "or.jp", "or.kr", "or.mu", "org.af", "org.ag", "org.am", "org.bz", "org.cn", "org.es", "org.gg", "org.gl", "org.gr", "org.hn", "org.ht", "org.il", "org.im", "org.in", "org.je", "org.ki", "org.lc", "org.lv", "org.mg", "org.ms", "org.mu", "org.my", "org.nz", "org.pk", "org.pl", "org.ps", "org.ro", "org.ru", "org.sb", "org.sc", "org.so", "org.uk", "org.vc", "org.ve", "pe.kr", "pro.ec", "qc.com", "qh.cn", "radio.am", "radio.fm", "re.kr", "recht.pro", "ru.com", "sa.com", "sc.cn", "sc.kr", "sd.cn", "se.com", "senet", "seoul.kr", "sh.cn", "sn.cn", "sx.cn", "tj.cn", "tw.cn", "uk.com", "uk.net", "ulsan.kr", "us.com", "us.org", "uy.com", "web.ve", "xj.cn", "xz.cn", "yn.cn", "za.com", "zj.cn");
	$_var_472 = explode(".", $_var_470);
	if (count($_var_472) <= 2) {
		$_var_473 = $_var_470;
	} else {
		$_var_474 = array_pop($_var_472);
		$_var_475 = array_pop($_var_472);
		$_var_476 = array_pop($_var_472);
		$_var_473 = $_var_475 . "." . $_var_474;
		if (in_array($_var_473, $_var_471)) {
			$_var_473 = $_var_476 . "." . $_var_475 . "." . $_var_474;
		}
	}
	return $_var_473;
}
function qiuse_template_replace($_var_477, $_var_478)
{
	if ($_var_478 == 0) {
		return $_var_477;
	}
	$_var_479 = explode(",", "3,50");
	$sj = suijis(mt_rand($_var_479[0], $_var_479[1]));
	preg_match_all("#(</div>|</li>)#i", $_var_477, $_var_480);
	$_var_481 = count($_var_480[0]);
	$_var_482 = range(0, $_var_481);
	$_var_482 || ($_var_482 = [1]);
	$_var_483 = ceil($_var_481 * $_var_478 / 100);
	if ((int) $_var_483 == 0) {
		return $_var_477;
	}
	$tarrs = array_rand($_var_482, $_var_483);
	$_var_477 = preg_replace_callback("/(<\\/div>|<\\/li>)/i", function ($_var_484) use($tarrs, $sj) {
		static $_var_485;
		if (is_null($_var_485)) {
			$_var_485 = 0;
		}
		if (in_array($_var_485++, $tarrs)) {
			return $_var_484[0] . $sj;
		}
		return $_var_484[0];
	}, $_var_477);
	return $_var_477;
}
function randpic($img = 'img')
{
	$_var_486 = ROOT_PATH . "/data/" . $img;
	if (!file_exists($_var_486)) {
		return "";
	}
	$_var_487 = opendir($_var_486);
	$_var_488 = [];
	while ($_var_489 = readdir($_var_487)) {
		if ($_var_489 == "." || $_var_489 == "..") {
			continue;
		}
		$_var_488[] = $_var_489;
	}
	$_var_490 = rand(0, count($_var_488) - 1);
	return "/data/" . $img . "/" . $_var_488[$_var_490];
}
function randstring($_var_491 = 6, $_var_492 = 'string', $_var_493 = 0)
{
	$_var_494 = array("shuzi" => "1234567890", "xiaoxie" => "abcdefghijklmnopqrstuvwxyz", "letter" => "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", "string" => "abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ", "all" => "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
	if (!isset($_var_494[$_var_492])) {
		$_var_492 = "string";
	}
	$_var_495 = $_var_494[$_var_492];
	$_var_496 = "";
	$_var_497 = strlen($_var_495) - 1;
	for ($_var_498 = 0; $_var_498 < $_var_491; $_var_498++) {
		$_var_496 .= $_var_495[mt_rand(0, $_var_497)];
	}
	if (!empty($_var_493)) {
		$_var_496 = $_var_493 > 0 ? strtoupper($_var_496) : strtolower($_var_496);
	}
	return $_var_496;
}
function zhuanma($_var_499)
{
	$_var_500 = mb_strlen($_var_499);
	$_var_501 = "";
	$_var_502 = false;
	for ($_var_503 = 0; $_var_503 < $_var_500; $_var_503++) {
		$_var_504 = mb_substr($_var_499, $_var_503, 1);
		if ($_var_504 == "<") {
			$_var_502 = true;
			$_var_501 .= $_var_504;
			continue;
		}
		if ($_var_502 == true) {
			$_var_501 .= $_var_504;
			if ($_var_504 == ">") {
				$_var_502 = false;
			}
			continue;
		}
		$_var_501 .= "&#" . base_convert(bin2hex(mb_convert_encoding($_var_504, "ucs-4", "utf-8")), 16, 10) . ";";
	}
	return $_var_501;
}
function yuming($_var_505)
{
	switch ($_var_505) {
		case "xieyi":
			return $_SERVER["REQUEST_SCHEME"] . "://";
			break;
		case "dingji":
			return url_root($_SERVER["HTTP_HOST"]);
			break;
		case "host":
			return $_SERVER["HTTP_HOST"];
			break;
		case "url":
			return $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
			break;
	}
}
function sitelink()
{
	$_var_506 = config("domain");
	return $_var_506;
}
function playdir()
{
	$_var_507 = trim(config("maccms.site")["play"]);
	$_var_507 = preg_split("/((?<!\\\\|\\r)\\n)|((?<!\\\\)\\r\\n)/", $_var_507);
	$_var_507 = array_unique(array_map("trim", $_var_507));
	return $_var_507[array_rand($_var_507)];
}
function voddir()
{
	$_var_508 = trim(config("maccms.site")["xiangqing"]);
	$_var_508 = preg_split("/((?<!\\\\|\\r)\\n)|((?<!\\\\)\\r\\n)/", $_var_508);
	$_var_508 = array_unique(array_map("trim", $_var_508));
	return $_var_508[array_rand($_var_508)];
}
function rand_color()
{
	$_var_509 = rand(0, 255);
	$_var_510 = rand(0, 255);
	$_var_511 = rand(0, 255);
	return "rgb(" . $_var_509 . ", " . $_var_510 . ", " . $_var_511 . ")";
}
function qiuse_template_text_chunk($text, $length = 100)
{
	$_var_512 = array();
	$_var_513 = mb_strlen($text);
	while ($_var_513) {
		$_var_512[] = mb_substr($text, 0, $length, "utf8");
		$text = mb_substr($text, $length, $_var_513, "utf8");
		$_var_513 = mb_strlen($text);
	}
	return $_var_512;
}
function qiuse_template_md5($_var_514)
{
	$_var_515 = "static";
	$_var_516 = "unique";
	$_var_515 = is_null($_var_515) ? "static" : $_var_515;
	$_var_517 = $_SERVER["PHP_SELF"];
	$_var_518 = randstring(rand(1, 15));
	$_var_519 = qiuse_template_text_chunk($_var_518, 2);
	if ("static" != $_var_515) {
		shuffle($_var_519);
	}
	preg_match_all("/class=\\\"(.*?)\\\"/i", $_var_514, $_var_520);
	$_var_521 = $_var_520[0];
	$_var_514 = preg_replace("/class=\\\".*?\\\"/", "{FLAG}", $_var_514);
	foreach ($_var_521 as $_var_522) {
		$_var_522 = str_replace("\"", "", $_var_522);
		$_var_522 = str_replace("=", "=\"", $_var_522);
		$_var_522 = str_replace("class=\"", "", $_var_522);
		$_var_523 = qiuse_template_class_code_v2($_var_519, $_var_516);
		$_var_524 = "class=\"" . $_var_523 . " " . $_var_522 . "\"";
		$_var_514 = preg_replace("/\\{FLAG\\}/", $_var_524, $_var_514, 1);
	}
	preg_match_all("/class=\\'(.*?)\\'/i", $_var_514, $_var_520);
	$_var_521 = $_var_520[0];
	$_var_514 = preg_replace("/class=\\'.*?\\'/", "{FLAG}", $_var_514);
	foreach ($_var_521 as $_var_522) {
		$_var_522 = str_replace("'", "", $_var_522);
		$_var_522 = str_replace("=", "='", $_var_522);
		$_var_522 = str_replace("class='", "", $_var_522);
		$_var_523 = qiuse_template_class_code_v2($_var_519, $_var_516);
		$_var_524 = "class='" . $_var_523 . " " . $_var_522 . "'";
		$_var_514 = preg_replace("/\\{FLAG\\}/", $_var_524, $_var_514, 1);
	}
	return $_var_514;
}
function qiuse_template_class_code_v2($_var_525, $_var_526)
{
	global $tp_code_index;
	if (is_null($tp_code_index)) {
		$tp_code_index = 0;
	}
	$_var_527 = "";
	if (isset($_var_525[$tp_code_index])) {
		$_var_527 .= $_var_525[$tp_code_index];
	} else {
		$tp_code_index = 0;
		$_var_527 .= $_var_525[$tp_code_index];
	}
	if ("unique" != $_var_526) {
		$_var_527 .= "-";
	}
	if (isset($_var_525[$tp_code_index + 1])) {
		$_var_527 .= $_var_525[$tp_code_index + 1];
	} else {
		$tp_code_index = 0;
		$_var_527 .= $_var_525[$tp_code_index];
	}
	if ("unique" != $_var_526) {
		$_var_527 .= "-";
	}
	if (isset($_var_525[$tp_code_index + 2])) {
		$_var_527 .= $_var_525[$tp_code_index + 2];
	} else {
		$tp_code_index = 0;
		$_var_527 .= $_var_525[$tp_code_index];
	}
	if ("unique" != $_var_526) {
		$_var_527 .= "-";
	}
	if (isset($_var_525[$tp_code_index + 3])) {
		$_var_527 .= $_var_525[$tp_code_index + 3];
	} else {
		$tp_code_index = 0;
		$_var_527 .= $_var_525[$tp_code_index];
	}
	$tp_code_index++;
	return $_var_527;
}
function transStrToPinyin($str, $pc)
{
	$str || ($str = wanneng("juzi"));
	$_var_528 = [];
	$_var_529 = mb_strlen($str);
	$_var_530 = 0;
	$_var_531 = [];
	$_var_532 = "/[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	while ($_var_530 < $_var_529) {
		$_var_533 = mb_substr($str, $_var_530++, 1);
		if (preg_match($_var_532, $_var_533)) {
			if (!empty($_var_531)) {
				$_var_528[] = implode("", $_var_531);
				$_var_531 = [];
			}
			$_var_528[] = $_var_533;
			continue;
		}
		$_var_531[] = $_var_533;
	}
	if (!empty($_var_531)) {
		$_var_528[] = implode("", $_var_531);
		$_var_531 = [];
	}
	$_var_534 = new \topinyin\ChinesePinyin();
	$_var_529 = count($_var_528);
	$_var_528 || ($_var_528 = array("@xwd", "yzlseo"));
	$_var_535 = ceil($pc * $_var_529 / 100);
	$_var_535 || ($_var_535 = "1");
	$_var_536 = array_rand($_var_528, $_var_535);
	foreach ($_var_536 ?? [] as $_var_537) {
		$_var_538 = trim($_var_528[$_var_537]);
		if (empty($_var_538) || preg_match("/[a-z0-9\\s]/i", $_var_538) || !preg_match($_var_532, $_var_538) || mb_strlen($_var_538) > 3) {
			continue;
		}
		if (in_array($_var_538, ["'", "\\\"", "`", ",", "(", ")", "%", "=", "-", "*", "^", "@", "!", "{", "}", "[", "]", ":", ";", "|", "\\", "/", "?", "<", ">", ".", ",", "，", "。", "！", "《", "》", "”", "“"])) {
			continue;
		}
		$_var_539 = $_var_534->TransformWithTone($_var_538);
		if (empty($_var_539) || $_var_539 == $_var_538) {
			continue;
		}
		$_var_528[$_var_537] = $_var_538 . "(" . $_var_539 . ")";
	}
	return implode("", $_var_528);
}
function randfuhao($_var_540, $_var_541 = '50')
{
	if (empty($_var_540)) {
		return "";
	}
	$_var_542 = [];
	$_var_543 = mb_strlen($_var_540);
	$_var_544 = 0;
	$_var_545 = [];
	$_var_546 = "/[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	while ($_var_544 < $_var_543) {
		$_var_547 = mb_substr($_var_540, $_var_544++, 1);
		if (preg_match($_var_546, $_var_547)) {
			if (!empty($_var_545)) {
				$_var_542[] = implode("", $_var_545);
				$_var_545 = [];
			}
			$_var_542[] = $_var_547;
			continue;
		}
		$_var_545[] = $_var_547;
	}
	if (!empty($_var_545)) {
		$_var_542[] = implode("", $_var_545);
		$_var_545 = [];
	}
	$_var_543 = count($_var_542);
	$_var_548 = ceil($_var_541 * $_var_543 / 100);
	$_var_549 = array_rand($_var_542, $_var_548);
	foreach ($_var_549 ?? [] as $_var_550) {
		$_var_551 = trim($_var_542[$_var_550]);
		if (empty($_var_551) || preg_match("/[a-z0-9\\s]/i", $_var_551) || !preg_match($_var_546, $_var_551) || mb_strlen($_var_551) > 3) {
			continue;
		}
		if (in_array($_var_551, ["'", "\\\"", "`", ",", "(", ")", "%", "=", "-", "*", "^", "@", "!", "{", "}", "[", "]", ":", ";", "|", "\\", "/", "?", "<", ">", ".", ",", "，", "。", "！", "《", "》", "”", "“"])) {
			continue;
		}
		$_var_552 = wanneng("fuhao");
		if (empty($_var_552) || $_var_552 == $_var_551) {
			continue;
		}
		$_var_542[$_var_550] = $_var_551 . "(" . $_var_552 . ")";
	}
	return implode("", $_var_542);
}