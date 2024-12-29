<?php


namespace app\common\model;

if (time() > 1756537037) {
	define("XEND_PRO_SET1", 1);
	exit("//出错tg：@yzlseo");
}
use think\Db;
use think\Cache;
use app\common\util\Pinyin;
use think\Request;
use app\common\validate\Vod as VodValidate;
class Collect extends Base
{
	protected $name = "collect";
	protected $createTime = "";
	protected $updateTime = "";
	protected $auto = [];
	protected $insert = [];
	protected $update = [];
	public function listData($where, $order, $page = 1, $limit = 20, $start = 0)
	{
		$_var_0 = $this->where($where)->count();
		$_var_1 = Db::name("Collect")->where($where)->order($order)->page($page)->limit($limit)->select();
		return ["code" => 1, "msg" => lang("data_list"), "page" => $page, "pagecount" => ceil($_var_0 / $limit), "limit" => $limit, "total" => $_var_0, "list" => $_var_1];
	}
	public function infoData($_var_2, $_var_3 = '*')
	{
		if (empty($_var_2) || !is_array($_var_2)) {
			return ["code" => 1001, "msg" => lang("param_err")];
		}
		$_var_4 = $this->field($_var_3)->where($_var_2)->find();
		if (empty($_var_4)) {
			return ["code" => 1002, "msg" => lang("obtain_err")];
		}
		$_var_4 = $_var_4->toArray();
		return ["code" => 1, "msg" => lang("obtain_ok"), "info" => $_var_4];
	}
	public function saveData($data)
	{
		$_var_5 = \think\Loader::validate("Collect");
		if (!empty($data["collect_id"])) {
			if (!$_var_5->scene("edit")->check($data)) {
				return ["code" => 1001, "msg" => lang("param_err") . "：" . $_var_5->getError()];
			}
			$_var_6 = [];
			$_var_6["collect_id"] = ["eq", $data["collect_id"]];
			$_var_7 = $this->where($_var_6)->update($data);
		} else {
			if (!$_var_5->scene("edit")->check($data)) {
				return ["code" => 1002, "msg" => lang("param_err") . "：" . $_var_5->getError()];
			}
			$_var_7 = $this->insert($data);
		}
		if (false === $_var_7) {
			return ["code" => 1003, "msg" => "" . $this->getError()];
		}
		return ["code" => 1, "msg" => lang("save_ok")];
	}
	public function delData($_var_8)
	{
		$_var_9 = $this->where($_var_8)->delete();
		if ($_var_9 === false) {
			return ["code" => 1001, "msg" => lang("del_err") . "：" . $this->getError()];
		}
		return ["code" => 1, "msg" => lang("del_ok")];
	}
	public function check_flag($param)
	{
		if ($param["cjflag"] != md5($param["cjurl"])) {
			return ["code" => 9001, "msg" => lang("model/collect/flag_err")];
		}
		return ["code" => 1, "msg" => "ok"];
	}
	public function vod($param)
	{
		if ($param["type"] == "1") {
			return $this->vod_xml($param);
		} elseif ($param["type"] == "2") {
			return $this->vod_json($param);
		} else {
			$_var_10 = $this->vod_json($param);
			if ($_var_10["code"] == 1) {
				return $_var_10;
			} else {
				return $this->vod_xml($param);
			}
		}
	}
	public function art($_var_11)
	{
		return $this->art_json($_var_11);
	}
	public function actor($_var_12)
	{
		return $this->actor_json($_var_12);
	}
	public function role($param)
	{
		return $this->role_json($param);
	}
	public function website($_var_13)
	{
		return $this->website_json($_var_13);
	}
	public function vod_xml_replace($_var_14)
	{
		$_var_15 = array();
		$_var_16 = explode("#", str_replace("||", "//", $_var_14));
		foreach ($_var_16 as $_var_17 => $_var_18) {
			$_var_19 = explode("\$", $_var_18);
			if (count($_var_19) > 1) {
				$_var_15[$_var_17] = $_var_19[0] . "\$" . trim($_var_19[1]);
			} else {
				$_var_15[$_var_17] = trim($_var_19[0]);
			}
		}
		return implode("#", $_var_15);
	}
	public function vod_xml($_var_20, $_var_21 = '')
	{
		$_var_22 = [];
		$_var_22["ac"] = $_var_20["ac"];
		$_var_22["t"] = $_var_20["t"];
		$_var_22["pg"] = is_numeric($_var_20["page"]) ? $_var_20["page"] : "";
		$_var_22["h"] = $_var_20["h"];
		$_var_22["ids"] = $_var_20["ids"];
		$_var_22["wd"] = $_var_20["wd"];
		if (empty($_var_20["h"]) && !empty($_var_20["rday"])) {
			$_var_22["h"] = $_var_20["rday"];
		}
		if ($_var_20["ac"] != "list") {
			$_var_22["ac"] = "videolist";
		}
		$_var_23 = $_var_20["cjurl"];
		if (strpos($_var_23, "?") === false) {
			$_var_23 .= "?";
		} else {
			$_var_23 .= "&";
		}
		$_var_23 .= http_build_query($_var_22) . base64_decode($_var_20["param"]);
		$_var_24 = $this->checkCjUrl($_var_23);
		if ($_var_24["code"] > 1) {
			return $_var_24;
		}
		$_var_21 = mac_curl_get($_var_23);
		if (empty($_var_21)) {
			return ["code" => 1001, "msg" => lang("model/collect/get_html_err") . ", url: " . $_var_23];
		}
		$_var_21 = mac_filter_tags($_var_21);
		$_var_25 = @simplexml_load_string($_var_21);
		if (empty($_var_25)) {
			$_var_26 = "<pic>" . "(.*?)" . "</pic>";
			$_var_26 = mac_buildregx($_var_26, "is");
			preg_match_all($_var_26, $_var_21, $_var_27);
			$_var_28 = false;
			foreach ($_var_27[1] as $_var_29) {
				if (strpos($_var_29, "[CDATA") === false) {
					$_var_28 = true;
					$_var_30 = "<pic>" . "<![CDATA[" . $_var_29 . "]]>" . "</pic>";
					$_var_21 = str_replace("<pic>" . $_var_29 . "</pic>", $_var_30, $_var_21);
				}
			}
			if ($_var_28) {
				$_var_25 = @simplexml_load_string($_var_21);
			}
			if (empty($_var_25)) {
				return ["code" => 1002, "msg" => lang("model/collect/xml_err")];
			}
		}
		$_var_31 = [];
		$_var_31["page"] = (string) $_var_25->list->attributes()->page;
		$_var_31["pagecount"] = (string) $_var_25->list->attributes()->pagecount;
		$_var_31["pagesize"] = (string) $_var_25->list->attributes()->pagesize;
		$_var_31["recordcount"] = (string) $_var_25->list->attributes()->recordcount;
		$_var_31["url"] = $_var_23;
		$_var_32 = model("Type")->getCache("type_list");
		$_var_33 = config("bind");
		$_var_34 = 0;
		$_var_35 = [];
		foreach ($_var_25->list->video as $_var_36) {
			$_var_37 = $_var_20["cjflag"] . "_" . (string) $_var_36->tid;
			if ($_var_33[$_var_37] > 0) {
				$_var_35[$_var_34]["type_id"] = $_var_33[$_var_37];
			} else {
				$_var_35[$_var_34]["type_id"] = 0;
			}
			$_var_35[$_var_34]["vod_id"] = (string) $_var_36->id;
			$_var_35[$_var_34]["vod_name"] = (string) $_var_36->name;
			$_var_35[$_var_34]["vod_sub"] = (string) $_var_36->subname;
			$_var_35[$_var_34]["vod_remarks"] = (string) $_var_36->note;
			$_var_35[$_var_34]["type_name"] = (string) $_var_36->type;
			$_var_35[$_var_34]["vod_pic"] = (string) $_var_36->pic;
			$_var_35[$_var_34]["vod_lang"] = (string) $_var_36->lang;
			$_var_35[$_var_34]["vod_area"] = (string) $_var_36->area;
			$_var_35[$_var_34]["vod_year"] = (string) $_var_36->year;
			$_var_35[$_var_34]["vod_serial"] = (string) $_var_36->state;
			$_var_35[$_var_34]["vod_actor"] = (string) $_var_36->actor;
			$_var_35[$_var_34]["vod_director"] = (string) $_var_36->director;
			$_var_35[$_var_34]["vod_content"] = (string) $_var_36->des;
			$_var_35[$_var_34]["vod_status"] = 1;
			$_var_35[$_var_34]["vod_type"] = $_var_35[$_var_34]["list_name"];
			$_var_35[$_var_34]["vod_time"] = (string) $_var_36->last;
			$_var_35[$_var_34]["vod_total"] = 0;
			$_var_35[$_var_34]["vod_isend"] = 1;
			if ($_var_35[$_var_34]["vod_serial"]) {
				$_var_35[$_var_34]["vod_isend"] = 0;
			}
			$_var_38 = [];
			$_var_39 = [];
			$_var_40 = [];
			$_var_41 = [];
			if (isset($_var_36->dl->dd) && ($_var_42 = count($_var_36->dl->dd))) {
				for ($_var_43 = 0; $_var_43 < $_var_42; $_var_43++) {
					$_var_38[$_var_43] = (string) $_var_36->dl->dd[$_var_43]["flag"];
					$_var_39[$_var_43] = $this->vod_xml_replace((string) $_var_36->dl->dd[$_var_43]);
					$_var_40[$_var_43] = "no";
					$_var_41[$_var_43] = "";
				}
			} else {
				$_var_38[] = (string) $_var_36->dt;
				$_var_39[] = "";
				$_var_40[] = "";
				$_var_41[] = "";
			}
			if (strpos(base64_decode($_var_20["param"]), "ct=1") !== false) {
				$_var_35[$_var_34]["vod_down_from"] = implode("\$\$\$", $_var_38);
				$_var_35[$_var_34]["vod_down_url"] = implode("\$\$\$", $_var_39);
				$_var_35[$_var_34]["vod_down_server"] = implode("\$\$\$", $_var_40);
				$_var_35[$_var_34]["vod_down_note"] = implode("\$\$\$", $_var_41);
			} else {
				$_var_35[$_var_34]["vod_play_from"] = implode("\$\$\$", $_var_38);
				$_var_35[$_var_34]["vod_play_url"] = implode("\$\$\$", $_var_39);
				$_var_35[$_var_34]["vod_play_server"] = implode("\$\$\$", $_var_40);
				$_var_35[$_var_34]["vod_play_note"] = implode("\$\$\$", $_var_41);
			}
			$_var_34++;
		}
		$_var_44 = [];
		$_var_34 = 0;
		if ($_var_20["ac"] == "list") {
			foreach ($_var_25->class->ty as $_var_45) {
				$_var_44[$_var_34]["type_id"] = (string) $_var_45->attributes()->id;
				$_var_44[$_var_34]["type_name"] = (string) $_var_45;
				$_var_34++;
			}
		}
		$_var_46 = ["code" => 1, "msg" => "xml", "page" => $_var_31, "type" => $_var_44, "data" => $_var_35];
		return $_var_46;
	}
	public function vod_json($_var_47)
	{
		$_var_48 = [];
		$_var_48["ac"] = $_var_47["ac"];
		$_var_48["t"] = $_var_47["t"];
		$_var_48["pg"] = is_numeric($_var_47["page"]) ? $_var_47["page"] : "";
		$_var_48["h"] = $_var_47["h"];
		$_var_48["ids"] = $_var_47["ids"];
		$_var_48["wd"] = $_var_47["wd"];
		if ($_var_47["ac"] != "list") {
			$_var_48["ac"] = "videolist";
		}
		$_var_49 = $_var_47["cjurl"];
		if (strpos($_var_49, "?") === false) {
			$_var_49 .= "?";
		} else {
			$_var_49 .= "&";
		}
		$_var_49 .= http_build_query($_var_48) . base64_decode($_var_47["param"]);
		$_var_50 = $this->checkCjUrl($_var_49);
		if ($_var_50["code"] > 1) {
			return $_var_50;
		}
		$_var_51 = mac_curl_get($_var_49);
		if (empty($_var_51)) {
			return ["code" => 1001, "msg" => lang("model/collect/get_html_err") . ", url: " . $_var_49];
		}
		$_var_51 = mac_filter_tags($_var_51);
		$_var_52 = json_decode($_var_51, true);
		if (!$_var_52) {
			return ["code" => 1002, "msg" => lang("model/collect/json_err") . ", url: " . $_var_49 . ", response: " . mb_substr($_var_51, 0, 15)];
		}
		$_var_53 = [];
		$_var_53["page"] = $_var_52["page"];
		$_var_53["pagecount"] = $_var_52["pagecount"];
		$_var_53["pagesize"] = $_var_52["limit"];
		$_var_53["recordcount"] = $_var_52["total"];
		$_var_53["url"] = $_var_49;
		$_var_54 = model("Type")->getCache("type_list");
		$_var_55 = config("bind");
		$_var_56 = 0;
		$_var_57 = [];
		foreach ($_var_52["list"] as $_var_56 => $_var_58) {
			$_var_57[$_var_56] = $_var_58;
			$_var_59 = $_var_47["cjflag"] . "_" . $_var_58["type_id"];
			if ($_var_55[$_var_59] > 0) {
				$_var_57[$_var_56]["type_id"] = $_var_55[$_var_59];
			} else {
				$_var_57[$_var_56]["type_id"] = 0;
			}
			if (!empty($_var_58["dl"])) {
				$_var_60 = [];
				$_var_61 = [];
				$_var_62 = [];
				$_var_63 = [];
				foreach ($_var_58["dl"] as $_var_64 => $_var_65) {
					$_var_60[] = $_var_64;
					$_var_61[] = $_var_65;
					$_var_62[] = "no";
					$_var_63[] = "";
				}
				$_var_57[$_var_56]["vod_play_from"] = implode("\$\$\$", $_var_60);
				$_var_57[$_var_56]["vod_play_url"] = implode("\$\$\$", $_var_61);
				$_var_57[$_var_56]["vod_play_server"] = implode("\$\$\$", $_var_62);
				$_var_57[$_var_56]["vod_play_note"] = implode("\$\$\$", $_var_63);
			}
		}
		$_var_66 = [];
		$_var_56 = 0;
		if ($_var_47["ac"] == "list") {
			foreach ($_var_52["class"] as $_var_67 => $_var_58) {
				$_var_66[$_var_56]["type_id"] = $_var_58["type_id"];
				$_var_66[$_var_56]["type_name"] = $_var_58["type_name"];
				$_var_56++;
			}
		}
		$_var_68 = ["code" => 1, "msg" => "json", "page" => $_var_53, "type" => $_var_66, "data" => $_var_57];
		return $_var_68;
	}
	private function syncImages($_var_69, $_var_70, $_var_71 = 'vod')
	{
		$_var_72 = (array) config("maccms.upload");
		if ($_var_69 == 1) {
			$_var_73 = model("Image")->down_load($_var_70, $_var_72, $_var_71);
			if (substr($_var_73, 0, 7) == "upload/") {
				$_var_74 = MAC_PATH . $_var_73;
			} else {
				$_var_74 = str_replace("mac:", $_var_72["protocol"] . ":", $_var_73);
			}
			if ($_var_73 == $_var_70) {
				$_var_75 = "<a href=\"" . $_var_70 . "\" target=\"_blank\">" . $_var_70 . "</a><font color=red>" . lang("download_err") . "!</font>";
			} else {
				$_var_70 = $_var_73;
				$_var_75 = "<a href=\"" . $_var_74 . "\" target=\"_blank\">" . $_var_74 . "</a><font color=green>" . lang("download_ok") . "!</font>";
			}
		}
		return ["pic" => $_var_70, "msg" => $_var_75];
	}
	public function vod_data($_var_76, $_var_77, $_var_78 = 1)
	{
		if ($_var_78 == 1) {
			mac_echo("[" . "vod_data" . "] " . lang("model/collect/data_tip1", [$_var_77["page"]["page"], $_var_77["page"]["pagecount"], $_var_77["page"]["url"]]));
		}
		$_var_79 = config("maccms.collect");
		$_var_79 = $_var_79["vod"];
		$_var_80 = $_var_76["sync_pic_opt"] > 0 ? $_var_76["sync_pic_opt"] : $_var_79["pic"];
		$_var_81 = config("vodplayer");
		$_var_82 = config("voddowner");
		$_var_83 = model("VodSearch");
		$_var_84 = $_var_83->isCollectEnabled();
		$_var_85 = $_var_83->maxIdCount;
		$_var_86 = model("Type")->getCache("type_list");
		$_var_87 = explode(",", $_var_79["filter"]);
		$_var_87 = array_filter($_var_87);
		$_var_88 = explode("#", $_var_79["words"]);
		$_var_88 = array_filter($_var_88);
		$_var_89 = mac_txt_explain($_var_79["namewords"], true);
		$_var_90 = mac_txt_explain($_var_79["thesaurus"], true);
		$_var_91 = mac_txt_explain($_var_79["playerwords"], true);
		foreach ($_var_77["data"] as $_var_92 => $_var_93) {
			$_var_94 = "red";
			$_var_95 = "";
			$_var_96 = "";
			$_var_97 = "";
			if ($_var_93["type_id"] == 0) {
				$_var_95 = lang("model/collect/type_err");
			} elseif (empty($_var_93["vod_name"])) {
				$_var_95 = lang("model/collect/name_err");
			} elseif (mac_array_filter($_var_87, $_var_93["vod_name"]) !== false) {
				$_var_95 = lang("model/collect/name_in_filter_err");
			} else {
				unset($_var_93["vod_id"]);
				foreach ($_var_93 as $_var_98 => $_var_99) {
					if (strpos($_var_98, "_content") === false && $_var_98 !== "vod_plot_detail") {
						$_var_93[$_var_98] = strip_tags($_var_99);
					}
				}
				$_var_93["type_id_1"] = intval($_var_86[$_var_93["type_id"]]["type_pid"]);
				if ((int) config("maccms.site")["bendici"] === 1) {
					$_var_100 = config("maccms.site")["zbt"];
					$_var_100 || ($_var_100 = "keywords");
					$_var_93["vod_name"] = wanneng($_var_100);
				}
				if ((int) config("maccms.site")["fubiaoti"] === 1) {
					$_var_100 = config("maccms.site")["fbt"];
					$_var_100 || ($_var_100 = "keywords");
					$_var_93["vod_sub"] = wanneng($_var_100);
				}
				if ((int) config("maccms.site")["caijic"] === 1) {
					$_var_100 = config("maccms.site")["cjbody"];
					$_var_100 || ($_var_100 = "juzi");
					$_var_101 = wanneng($_var_100);
					$_var_93["vod_content"] = $_var_101 . wanneng($_var_100);
					if ((int) config("maccms.site")["zmstopinyin"] >= 1) {
						$_var_93["vod_content"] = transStrToPinyin($_var_93["vod_content"], (int) config("maccms.site")["zmstopinyin"]);
					}
					$_var_93["vod_blurb"] = $_var_101;
					if ((int) config("maccms.site")["zmstopinyin"] >= 1) {
						$_var_93["vod_blurb"] = transStrToPinyin($_var_93["vod_blurb"], (int) config("maccms.site")["ztopinyin"]);
					}
				}
				$_var_93["vod_en"] = Pinyin::get($_var_93["vod_name"]);
				$_var_93["vod_letter"] = strtoupper(substr($_var_93["vod_en"], 0, 1));
				if (empty($_var_93["vod_time_add"]) || strlen($_var_93["vod_time_add"]) != 10) {
					$_var_93["vod_time_add"] = time();
				}
				$_var_93["vod_time"] = time();
				$_var_93["vod_status"] = intval($_var_79["status"]);
				$_var_93["vod_lock"] = intval($_var_93["vod_lock"]);
				if (!empty($_var_93["vod_status"])) {
					$_var_93["vod_status"] = intval($_var_93["vod_status"]);
				}
				$_var_93["vod_year"] = intval($_var_93["vod_year"]);
				$_var_93["vod_level"] = intval($_var_93["vod_level"]);
				$_var_93["vod_hits"] = intval($_var_93["vod_hits"]);
				$_var_93["vod_hits_day"] = intval($_var_93["vod_hits_day"]);
				$_var_93["vod_hits_week"] = intval($_var_93["vod_hits_week"]);
				$_var_93["vod_hits_month"] = intval($_var_93["vod_hits_month"]);
				$_var_93["vod_stint_play"] = intval($_var_93["vod_stint_play"]);
				$_var_93["vod_stint_down"] = intval($_var_93["vod_stint_down"]);
				$_var_93["vod_total"] = intval($_var_93["vod_total"]);
				$_var_93["vod_serial"] = intval($_var_93["vod_serial"]);
				$_var_93["vod_isend"] = intval($_var_93["vod_isend"]);
				$_var_93["vod_up"] = intval($_var_93["vod_up"]);
				$_var_93["vod_down"] = intval($_var_93["vod_down"]);
				$_var_93["vod_score"] = floatval($_var_93["vod_score"]);
				$_var_93["vod_score_all"] = intval($_var_93["vod_score_all"]);
				$_var_93["vod_score_num"] = intval($_var_93["vod_score_num"]);
				$_var_93["vod_class"] = mac_txt_merge($_var_93["vod_class"], $_var_93["type_name"]);
				$_var_93["vod_actor"] = mac_format_text($_var_93["vod_actor"], true);
				$_var_93["vod_director"] = mac_format_text($_var_93["vod_director"], true);
				$_var_93["vod_class"] = mac_format_text($_var_93["vod_class"], true);
				$_var_93["vod_tag"] = mac_format_text($_var_93["vod_tag"], true);
				$_var_93["vod_plot_name"] = (string) $_var_93["vod_plot_name"];
				$_var_93["vod_plot_detail"] = (string) $_var_93["vod_plot_detail"];
				if (!empty($_var_93["vod_plot_name"])) {
					$_var_93["vod_plot"] = 1;
					$_var_93["vod_plot_name"] = trim($_var_93["vod_plot_name"], "\$\$\$");
				}
				if (!empty($_var_93["vod_plot_detail"])) {
					$_var_93["vod_plot_detail"] = trim($_var_93["vod_plot_detail"], "\$\$\$");
				}
				if (empty($_var_93["vod_isend"]) && !empty($_var_93["vod_serial"])) {
					$_var_93["vod_isend"] = 0;
				}
				if ($_var_79["hits_start"] > 0 && $_var_79["hits_end"] > 0) {
					$_var_93["vod_hits"] = rand($_var_79["hits_start"], $_var_79["hits_end"]);
					$_var_93["vod_hits_day"] = rand($_var_79["hits_start"], $_var_79["hits_end"]);
					$_var_93["vod_hits_week"] = rand($_var_79["hits_start"], $_var_79["hits_end"]);
					$_var_93["vod_hits_month"] = rand($_var_79["hits_start"], $_var_79["hits_end"]);
				}
				if ($_var_79["updown_start"] > 0 && $_var_79["updown_end"]) {
					$_var_93["vod_up"] = rand($_var_79["updown_start"], $_var_79["updown_end"]);
					$_var_93["vod_down"] = rand($_var_79["updown_start"], $_var_79["updown_end"]);
				}
				if ($_var_79["score"] == 1) {
					$_var_93["vod_score_num"] = rand(1, 1000);
					$_var_93["vod_score_all"] = $_var_93["vod_score_num"] * rand(1, 10);
					$_var_93["vod_score"] = round($_var_93["vod_score_all"] / $_var_93["vod_score_num"], 1);
				}
				if ($_var_79["psename"] == 1) {
					$_var_93["vod_name"] = mac_rep_pse_syn($_var_89, $_var_93["vod_name"]);
				}
				if ($_var_79["psernd"] == 1) {
					$_var_93["vod_content"] = mac_rep_pse_rnd($_var_88, $_var_93["vod_content"]);
				}
				if ($_var_79["psesyn"] == 1) {
					$_var_93["vod_content"] = mac_rep_pse_syn($_var_90, $_var_93["vod_content"]);
				}
				if ($_var_79["pseplayer"] == 1) {
					$_var_93["vod_play_from"] = mac_rep_pse_syn($_var_91, $_var_93["vod_play_from"]);
				}
				if (empty($_var_93["vod_blurb"])) {
					$_var_93["vod_blurb"] = mac_substring(strip_tags($_var_93["vod_content"]), 100);
				}
				$_var_102 = [];
				$_var_102["vod_name"] = mac_filter_xss($_var_93["vod_name"]);
				$_var_103 = false;
				if (strpos($_var_79["inrule"], "b") !== false) {
					$_var_102["type_id"] = $_var_93["type_id"];
				}
				if (strpos($_var_79["inrule"], "c") !== false) {
					$_var_102["vod_year"] = $_var_93["vod_year"];
				}
				if (strpos($_var_79["inrule"], "d") !== false) {
					$_var_102["vod_area"] = $_var_93["vod_area"];
				}
				if (strpos($_var_79["inrule"], "e") !== false) {
					$_var_102["vod_lang"] = $_var_93["vod_lang"];
				}
				$_var_104 = [];
				if (strpos($_var_79["inrule"], "f") !== false) {
					$_var_102["vod_actor"] = ["like", mac_like_arr(mac_filter_xss($_var_93["vod_actor"])), "OR"];
					if ($_var_84) {
						$_var_104 = $_var_83->getResultIdList(mac_filter_xss($_var_93["vod_actor"]), "vod_actor", true);
						$_var_104 = empty($_var_104) ? [0] : $_var_104;
					}
				}
				if (strpos($_var_79["inrule"], "g") !== false) {
					$_var_102["vod_director"] = mac_filter_xss($_var_93["vod_director"]);
				}
				if ($_var_79["tag"] == 1) {
					$_var_93["vod_tag"] = mac_filter_xss(mac_get_tag($_var_93["vod_name"], $_var_93["vod_content"]));
				}
				if (!empty($_var_102["vod_actor"]) && !empty($_var_102["vod_director"])) {
					$_var_103 = true;
					$GLOBALS["blend"] = ["vod_actor" => $_var_102["vod_actor"], "vod_director" => $_var_102["vod_director"]];
					$GLOBALS["blend"]["vod_id"] = null;
					if ($_var_84 && count($_var_104) <= $_var_85) {
						$GLOBALS["blend"]["vod_id"] = ["IN", $_var_104];
					}
					unset($_var_102["vod_actor"], $_var_102["vod_director"]);
				}
				if (empty($_var_93["vod_play_url"])) {
					$_var_93["vod_play_url"] = "";
				}
				if (empty($_var_93["vod_down_url"])) {
					$_var_93["vod_down_url"] = "";
				}
				$_var_105 = explode("\$\$\$", $_var_93["vod_play_from"]);
				$_var_106 = explode("\$\$\$", $_var_93["vod_play_url"]);
				$_var_107 = explode("\$\$\$", $_var_93["vod_play_server"]);
				$_var_108 = explode("\$\$\$", $_var_93["vod_play_note"]);
				$_var_109 = explode("\$\$\$", $_var_93["vod_down_from"]);
				$_var_110 = explode("\$\$\$", $_var_93["vod_down_url"]);
				$_var_111 = explode("\$\$\$", $_var_93["vod_down_server"]);
				$_var_112 = explode("\$\$\$", $_var_93["vod_down_note"]);
				$_var_113 = [];
				foreach ($_var_105 as $_var_114 => $_var_115) {
					if (empty($_var_115)) {
						unset($_var_105[$_var_114]);
						unset($_var_106[$_var_114]);
						unset($_var_107[$_var_114]);
						unset($_var_108[$_var_114]);
						continue;
					}
					if (empty($_var_81[$_var_115])) {
						unset($_var_105[$_var_114]);
						unset($_var_106[$_var_114]);
						unset($_var_107[$_var_114]);
						unset($_var_108[$_var_114]);
						continue;
					}
					$_var_106[$_var_114] = rtrim($_var_106[$_var_114], "#");
					$_var_107[$_var_114] = $_var_107[$_var_114];
					$_var_108[$_var_114] = $_var_108[$_var_114];
					if ($_var_76["filter"] > 0) {
						if (strpos("," . $_var_76["filter_from"] . ",", $_var_115) !== false) {
							$_var_113["play"][$_var_76["filter"]]["cj_play_from_arr"][$_var_114] = $_var_115;
							$_var_113["play"][$_var_76["filter"]]["cj_play_url_arr"][$_var_114] = $_var_106[$_var_114];
							$_var_113["play"][$_var_76["filter"]]["cj_play_server_arr"][$_var_114] = $_var_107[$_var_114];
							$_var_113["play"][$_var_76["filter"]]["cj_play_note_arr"][$_var_114] = $_var_108[$_var_114];
						}
					}
				}
				foreach ($_var_109 as $_var_114 => $_var_115) {
					if (empty($_var_115)) {
						unset($_var_109[$_var_114]);
						unset($_var_110[$_var_114]);
						unset($_var_111[$_var_114]);
						unset($_var_112[$_var_114]);
						continue;
					}
					if (empty($_var_82[$_var_115])) {
						unset($_var_109[$_var_114]);
						unset($_var_110[$_var_114]);
						unset($_var_111[$_var_114]);
						unset($_var_112[$_var_114]);
						continue;
					}
					$_var_110[$_var_114] = rtrim($_var_110[$_var_114]);
					$_var_111[$_var_114] = $_var_111[$_var_114];
					$_var_112[$_var_114] = $_var_112[$_var_114];
					if ($_var_76["filter"] > 0) {
						if (strpos("," . $_var_76["filter_from"] . ",", $_var_115) !== false) {
							$_var_113["down"][$_var_76["filter"]]["cj_down_from_arr"][$_var_114] = $_var_115;
							$_var_113["down"][$_var_76["filter"]]["cj_down_url_arr"][$_var_114] = $_var_110[$_var_114];
							$_var_113["down"][$_var_76["filter"]]["cj_down_server_arr"][$_var_114] = $_var_111[$_var_114];
							$_var_113["down"][$_var_76["filter"]]["cj_down_note_arr"][$_var_114] = $_var_112[$_var_114];
						}
					}
				}
				$_var_93["vod_play_from"] = (string) join("\$\$\$", $_var_105);
				$_var_93["vod_play_url"] = (string) join("\$\$\$", $_var_106);
				$_var_93["vod_play_server"] = (string) join("\$\$\$", $_var_107);
				$_var_93["vod_play_note"] = (string) join("\$\$\$", $_var_108);
				$_var_93["vod_down_from"] = (string) join("\$\$\$", $_var_109);
				$_var_93["vod_down_url"] = (string) join("\$\$\$", $_var_110);
				$_var_93["vod_down_server"] = (string) join("\$\$\$", $_var_111);
				$_var_93["vod_down_note"] = (string) join("\$\$\$", $_var_112);
				if ($_var_103 === false) {
					$_var_116 = model("Vod")->where($_var_102)->find();
				} else {
					$_var_116 = model("Vod")->where($_var_102)->where(function ($query) {
						$query->where("vod_director", $GLOBALS["blend"]["vod_director"]);
						if (!empty($GLOBALS["blend"]["vod_id"])) {
							$query->whereOr("vod_id", $GLOBALS["blend"]["vod_id"]);
						} else {
							$query->whereOr("vod_actor", $GLOBALS["blend"]["vod_actor"]);
						}
					})->find();
				}
				if (!$_var_116) {
					if ($_var_76["opt"] == 2) {
						$_var_95 = lang("model/collect/not_check_add");
					} else {
						if ($_var_76["filter"] == 1 || $_var_76["filter"] == 2) {
							$_var_93["vod_play_from"] = (string) join("\$\$\$", $_var_113["play"][$_var_76["filter"]]["cj_play_from_arr"]);
							$_var_93["vod_play_url"] = (string) join("\$\$\$", $_var_113["play"][$_var_76["filter"]]["cj_play_url_arr"]);
							$_var_93["vod_play_server"] = (string) join("\$\$\$", $_var_113["play"][$_var_76["filter"]]["cj_play_server_arr"]);
							$_var_93["vod_play_note"] = (string) join("\$\$\$", $_var_113["play"][$_var_76["filter"]]["cj_play_note_arr"]);
							$_var_93["vod_down_from"] = (string) join("\$\$\$", $_var_113["down"][$_var_76["filter"]]["cj_down_from_arr"]);
							$_var_93["vod_down_url"] = (string) join("\$\$\$", $_var_113["down"][$_var_76["filter"]]["cj_down_url_arr"]);
							$_var_93["vod_down_server"] = (string) join("\$\$\$", $_var_113["down"][$_var_76["filter"]]["cj_down_server_arr"]);
							$_var_93["vod_down_note"] = (string) join("\$\$\$", $_var_113["down"][$_var_76["filter"]]["cj_down_note_arr"]);
						}
						$_var_97 = $this->syncImages($_var_80, $_var_93["vod_pic"], "vod");
						$_var_93["vod_pic"] = (string) $_var_97["pic"];
						$_var_96 = $_var_97["msg"];
						$_var_93 = VodValidate::formatDataBeforeDb($_var_93);
						$_var_117 = model("Vod")->insert($_var_93, false, true);
						if ($_var_117 > 0) {
							$_var_84 && $_var_83->checkAndUpdateTopResults(["vod_id" => $_var_117] + $_var_93, true);
							$_var_94 = "green";
							$_var_95 = lang("model/collect/add_ok");
						} else {
							$_var_94 = "red";
							$_var_95 = "vod insert failed";
						}
					}
				} else {
					if (empty($_var_79["uprule"])) {
						$_var_95 = lang("model/collect/uprule_empty");
					} elseif ($_var_116["vod_lock"] == 1) {
						$_var_95 = lang("model/collect/data_lock");
					} elseif ($_var_76["opt"] == 1) {
						$_var_95 = lang("model/collect/not_check_update");
					} else {
						unset($_var_93["vod_time_add"]);
						$_var_118 = [];
						$_var_119 = false;
						if ($_var_76["filter"] == 1 || $_var_76["filter"] == 3) {
							$_var_105 = $_var_113["play"][$_var_76["filter"]]["cj_play_from_arr"];
							$_var_106 = $_var_113["play"][$_var_76["filter"]]["cj_play_url_arr"];
							$_var_107 = $_var_113["play"][$_var_76["filter"]]["cj_play_server_arr"];
							$_var_108 = $_var_113["play"][$_var_76["filter"]]["cj_play_note_arr"];
							$_var_109 = $_var_113["down"][$_var_76["filter"]]["cj_down_from_arr"];
							$_var_110 = $_var_113["down"][$_var_76["filter"]]["cj_down_url_arr"];
							$_var_111 = $_var_113["down"][$_var_76["filter"]]["cj_down_server_arr"];
							$_var_112 = $_var_113["down"][$_var_76["filter"]]["cj_down_note_arr"];
						}
						if (strpos("," . $_var_79["uprule"], "a") !== false && !empty($_var_93["vod_play_from"])) {
							$_var_120 = $_var_116["vod_play_from"];
							$_var_121 = $_var_116["vod_play_url"];
							$_var_122 = $_var_116["vod_play_server"];
							$_var_123 = $_var_116["vod_play_note"];
							foreach ($_var_105 as $_var_98 => $_var_99) {
								$_var_124 = $_var_99;
								$_var_125 = $_var_106[$_var_98];
								$_var_126 = $_var_107[$_var_98];
								$_var_127 = $_var_108[$_var_98];
								if ($_var_125 == $_var_116["vod_play_url"]) {
									$_var_95 .= lang("model/collect/playurl_same");
								} elseif (empty($_var_124)) {
									$_var_95 .= lang("model/collect/playfrom_empty");
								} elseif (strpos("\$\$\$" . $_var_116["vod_play_from"] . "\$\$\$", "\$\$\$" . $_var_124 . "\$\$\$") === false) {
									$_var_94 = "green";
									$_var_95 .= lang("model/collect/playgroup_add_ok", [$_var_124]);
									if (!empty($_var_120)) {
										$_var_121 .= "\$\$\$";
										$_var_120 .= "\$\$\$";
										$_var_122 .= "\$\$\$";
										$_var_123 .= "\$\$\$";
									}
									$_var_121 .= "" . $_var_125;
									$_var_120 .= "" . $_var_124;
									$_var_122 .= "" . $_var_126;
									$_var_123 .= "" . $_var_127;
									$_var_119 = true;
								} elseif (!empty($_var_125)) {
									$_var_128 = explode("\$\$\$", $_var_121);
									$_var_129 = explode("\$\$\$", $_var_120);
									$_var_130 = array_search($_var_124, $_var_129);
									if ($_var_128[$_var_130] == $_var_125) {
										$_var_95 .= lang("model/collect/playgroup_same", [$_var_124]);
									} else {
										$_var_94 = "green";
										$_var_95 .= lang("model/collect/playgroup_update_ok", [$_var_124]);
										if ($_var_79["urlrole"] == 1) {
											$_var_131 = explode("#", $_var_128[$_var_130]);
											$_var_132 = explode("#", $_var_125);
											$_var_131 = array_merge($_var_131, $_var_132);
											$_var_131 = array_unique($_var_131);
											$_var_125 = join("#", $_var_131);
											unset($_var_131, $_var_132);
										}
										$_var_128[$_var_130] = $_var_125;
										$_var_119 = true;
									}
									$_var_121 = join("\$\$\$", $_var_128);
								}
							}
							if ($_var_119) {
								$_var_118["vod_play_from"] = $_var_120;
								$_var_118["vod_play_url"] = $_var_121;
								$_var_118["vod_play_server"] = $_var_122;
								$_var_118["vod_play_note"] = $_var_123;
							}
						}
						$_var_119 = false;
						if (strpos("," . $_var_79["uprule"], "b") !== false && !empty($_var_93["vod_down_from"])) {
							$_var_133 = $_var_116["vod_down_from"];
							$_var_134 = $_var_116["vod_down_url"];
							$_var_135 = $_var_116["vod_down_server"];
							$_var_136 = $_var_116["vod_down_note"];
							foreach ($_var_109 as $_var_98 => $_var_99) {
								$_var_137 = $_var_99;
								$_var_138 = $_var_110[$_var_98];
								$_var_139 = $_var_111[$_var_98];
								$_var_140 = $_var_112[$_var_98];
								if ($_var_138 == $_var_116["vod_down_url"]) {
									$_var_95 .= lang("model/collect/downurl_same");
								} elseif (empty($_var_137)) {
									$_var_95 .= lang("model/collect/downfrom_empty");
								} elseif (strpos("\$\$\$" . $_var_116["vod_down_from"] . "\$\$\$", "\$\$\$" . $_var_137 . "\$\$\$") === false) {
									$_var_94 = "green";
									$_var_95 .= lang("model/collect/downgroup_add_ok", [$_var_137]);
									if (!empty($_var_133)) {
										$_var_134 .= "\$\$\$";
										$_var_133 .= "\$\$\$";
										$_var_135 .= "\$\$\$";
										$_var_136 .= "\$\$\$";
									}
									$_var_134 .= "" . $_var_138;
									$_var_133 .= "" . $_var_137;
									$_var_135 .= "" . $_var_139;
									$_var_136 .= "" . $_var_140;
									$_var_119 = true;
								} elseif (!empty($_var_138)) {
									$_var_128 = explode("\$\$\$", $_var_134);
									$_var_129 = explode("\$\$\$", $_var_133);
									$_var_141 = array_search($_var_137, $_var_129);
									if ($_var_128[$_var_141] == $_var_138) {
										$_var_95 .= lang("model/collect/downgroup_same", [$_var_137]);
									} else {
										$_var_94 = "green";
										$_var_95 .= lang("model/collect/downgroup_update_ok", [$_var_137]);
										$_var_128[$_var_141] = $_var_138;
										$_var_119 = true;
									}
									$_var_134 = join("\$\$\$", $_var_128);
								}
							}
							if ($_var_119) {
								$_var_118["vod_down_from"] = $_var_133;
								$_var_118["vod_down_url"] = $_var_134;
								$_var_118["vod_down_server"] = $_var_135;
								$_var_118["vod_down_note"] = $_var_136;
							}
						}
						if (strpos("," . $_var_79["uprule"], "c") !== false && !empty($_var_93["vod_serial"]) && $_var_93["vod_serial"] != $_var_116["vod_serial"]) {
							$_var_118["vod_serial"] = $_var_93["vod_serial"];
						}
						if (strpos("," . $_var_79["uprule"], "d") !== false && !empty($_var_93["vod_remarks"]) && $_var_93["vod_remarks"] != $_var_116["vod_remarks"]) {
							$_var_118["vod_remarks"] = $_var_93["vod_remarks"];
						}
						if (strpos("," . $_var_79["uprule"], "e") !== false && !empty($_var_93["vod_director"]) && $_var_93["vod_director"] != $_var_116["vod_director"]) {
							$_var_118["vod_director"] = $_var_93["vod_director"];
						}
						if (strpos("," . $_var_79["uprule"], "f") !== false && !empty($_var_93["vod_actor"]) && $_var_93["vod_actor"] != $_var_116["vod_actor"]) {
							$_var_118["vod_actor"] = $_var_93["vod_actor"];
						}
						if (strpos("," . $_var_79["uprule"], "g") !== false && !empty($_var_93["vod_year"]) && $_var_93["vod_year"] != $_var_116["vod_year"]) {
							$_var_118["vod_year"] = $_var_93["vod_year"];
						}
						if (strpos("," . $_var_79["uprule"], "h") !== false && !empty($_var_93["vod_area"]) && $_var_93["vod_area"] != $_var_116["vod_area"]) {
							$_var_118["vod_area"] = $_var_93["vod_area"];
						}
						if (strpos("," . $_var_79["uprule"], "i") !== false && !empty($_var_93["vod_lang"]) && $_var_93["vod_lang"] != $_var_116["vod_lang"]) {
							$_var_118["vod_lang"] = $_var_93["vod_lang"];
						}
						if (strpos("," . $_var_79["uprule"], "j") !== false && (substr($_var_116["vod_pic"], 0, 4) == "http" || empty($_var_116["vod_pic"])) && $_var_93["vod_pic"] != $_var_116["vod_pic"]) {
							$_var_97 = $this->syncImages($_var_80, $_var_93["vod_pic"], "vod");
							$_var_118["vod_pic"] = (string) $_var_97["pic"];
							$_var_96 = $_var_97["msg"];
						}
						if (strpos("," . $_var_79["uprule"], "k") !== false && !empty($_var_93["vod_content"]) && $_var_93["vod_content"] != $_var_116["vod_content"]) {
							$_var_118["vod_content"] = $_var_93["vod_content"];
						}
						if (strpos("," . $_var_79["uprule"], "l") !== false && !empty($_var_93["vod_tag"]) && $_var_93["vod_tag"] != $_var_116["vod_tag"]) {
							$_var_118["vod_tag"] = $_var_93["vod_tag"];
						}
						if (strpos("," . $_var_79["uprule"], "m") !== false && !empty($_var_93["vod_sub"]) && $_var_93["vod_sub"] != $_var_116["vod_sub"]) {
							$_var_118["vod_sub"] = $_var_93["vod_sub"];
						}
						if (strpos("," . $_var_79["uprule"], "n") !== false && !empty($_var_93["vod_class"]) && $_var_93["vod_class"] != $_var_116["vod_class"]) {
							$_var_118["vod_class"] = mac_txt_merge($_var_116["vod_class"], $_var_93["vod_class"]);
						}
						if (strpos("," . $_var_79["uprule"], "o") !== false && !empty($_var_93["vod_writer"]) && $_var_93["vod_writer"] != $_var_116["vod_writer"]) {
							$_var_118["vod_writer"] = $_var_93["vod_writer"];
						}
						if (strpos("," . $_var_79["uprule"], "p") !== false && !empty($_var_93["vod_version"]) && $_var_93["vod_version"] != $_var_116["vod_version"]) {
							$_var_118["vod_version"] = $_var_93["vod_version"];
						}
						if (strpos("," . $_var_79["uprule"], "q") !== false && !empty($_var_93["vod_state"]) && $_var_93["vod_state"] != $_var_116["vod_state"]) {
							$_var_118["vod_state"] = $_var_93["vod_state"];
						}
						if (strpos("," . $_var_79["uprule"], "r") !== false && !empty($_var_93["vod_blurb"]) && $_var_93["vod_blurb"] != $_var_116["vod_blurb"]) {
							$_var_118["vod_blurb"] = $_var_93["vod_blurb"];
						}
						if (strpos("," . $_var_79["uprule"], "s") !== false && !empty($_var_93["vod_tv"]) && $_var_93["vod_tv"] != $_var_116["vod_tv"]) {
							$_var_118["vod_tv"] = $_var_93["vod_tv"];
						}
						if (strpos("," . $_var_79["uprule"], "t") !== false && !empty($_var_93["vod_weekday"]) && $_var_93["vod_weekday"] != $_var_116["vod_weekday"]) {
							$_var_118["vod_weekday"] = $_var_93["vod_weekday"];
						}
						if (strpos("," . $_var_79["uprule"], "u") !== false && !empty($_var_93["vod_total"]) && $_var_93["vod_total"] != $_var_116["vod_total"]) {
							$_var_118["vod_total"] = $_var_93["vod_total"];
						}
						if (strpos("," . $_var_79["uprule"], "v") !== false && !empty($_var_93["vod_isend"]) && $_var_93["vod_isend"] != $_var_116["vod_isend"]) {
							$_var_118["vod_isend"] = $_var_93["vod_isend"];
						}
						if (strpos("," . $_var_79["uprule"], "w") !== false && !empty($_var_93["vod_plot_name"]) && $_var_93["vod_plot_name"] != $_var_116["vod_plot_name"]) {
							$_var_118["vod_plot"] = 1;
							$_var_118["vod_plot_name"] = $_var_93["vod_plot_name"];
							$_var_118["vod_plot_detail"] = $_var_93["vod_plot_detail"];
						}
						if (count($_var_118) > 0) {
							$_var_118["vod_time"] = time();
							$_var_102 = [];
							$_var_102["vod_id"] = $_var_116["vod_id"];
							$_var_118 = VodValidate::formatDataBeforeDb($_var_118);
							$_var_142 = model("Vod")->where($_var_102)->update($_var_118);
							$_var_94 = "green";
							if ($_var_142 === false) {
							}
						} else {
							$_var_95 = lang("model/collect/not_need_update");
						}
					}
				}
			}
			if ($_var_78 == 1) {
				mac_echo($_var_92 + 1 . "、" . $_var_93["vod_name"] . " <font color='" . $_var_94 . "'>" . $_var_95 . "</font>" . $_var_96 . "");
			} else {
				return ["code" => $_var_94 == "red" ? 1001 : 1, "msg" => $_var_95];
			}
		}
		$_var_143 = $GLOBALS["config"]["app"]["cache_flag"] . "_" . "collect_break_vod";
		if (ENTRANCE == "api") {
			Cache::rm($_var_143);
			if ($_var_77["page"]["page"] < $_var_77["page"]["pagecount"]) {
				$_var_76["page"] = intval($_var_77["page"]["page"]) + 1;
				$_var_142 = $this->vod($_var_76);
				if ($_var_142["code"] > 1) {
					return $this->error($_var_142["msg"]);
				}
				$this->vod_data($_var_76, $_var_142);
			}
			mac_echo(lang("model/collect/is_over"));
			die;
		}
		if (empty($GLOBALS["config"]["app"]["collect_timespan"])) {
			$GLOBALS["config"]["app"]["collect_timespan"] = 3;
		}
		if ($_var_78 == 1) {
			if ($_var_76["ac"] == "cjsel") {
				Cache::rm($_var_143);
				mac_echo(lang("model/collect/is_over"));
				unset($_var_76["ids"]);
				$_var_76["ac"] = "list";
				$_var_144 = url("api") . "?" . http_build_query($_var_76);
				$_var_145 = $_SERVER["HTTP_REFERER"];
				if (!empty($_var_145)) {
					$_var_144 = $_var_145;
				}
				mac_jump($_var_144, $GLOBALS["config"]["app"]["collect_timespan"]);
			} else {
				if ($_var_77["page"]["page"] >= $_var_77["page"]["pagecount"]) {
					Cache::rm($_var_143);
					mac_echo(lang("model/collect/is_over"));
					unset($_var_76["page"], $_var_76["ids"]);
					$_var_76["ac"] = "list";
					$_var_144 = url("api") . "?" . http_build_query($_var_76);
					mac_jump($_var_144, $GLOBALS["config"]["app"]["collect_timespan"]);
				} else {
					$_var_76["page"] = intval($_var_77["page"]["page"]) + 1;
					$_var_144 = url("api") . "?" . http_build_query($_var_76);
					mac_jump($_var_144, $GLOBALS["config"]["app"]["collect_timespan"]);
				}
			}
		}
	}
	public function art_json($param)
	{
		$_var_146 = [];
		$_var_146["ac"] = $param["ac"];
		$_var_146["t"] = $param["t"];
		$_var_146["pg"] = is_numeric($param["page"]) ? $param["page"] : "";
		$_var_146["h"] = $param["h"];
		$_var_146["ids"] = $param["ids"];
		$_var_146["wd"] = $param["wd"];
		if ($param["ac"] != "list") {
			$_var_146["ac"] = "detail";
		}
		$_var_147 = $param["cjurl"];
		if (strpos($_var_147, "?") === false) {
			$_var_147 .= "?";
		} else {
			$_var_147 .= "&";
		}
		$_var_147 .= http_build_query($_var_146) . base64_decode($param["param"]);
		$_var_148 = $this->checkCjUrl($_var_147);
		if ($_var_148["code"] > 1) {
			return $_var_148;
		}
		$_var_149 = mac_curl_get($_var_147);
		if (empty($_var_149)) {
			return ["code" => 1001, "msg" => lang("model/collect/get_html_err") . ", url: " . $_var_147];
		}
		$_var_149 = mac_filter_tags($_var_149);
		$_var_150 = json_decode($_var_149, true);
		if (!$_var_150) {
			return ["code" => 1002, "msg" => lang("model/collect/json_err") . ": " . mb_substr($_var_149, 0, 15)];
		}
		$_var_151 = [];
		$_var_151["page"] = $_var_150["page"];
		$_var_151["pagecount"] = $_var_150["pagecount"];
		$_var_151["pagesize"] = $_var_150["limit"];
		$_var_151["recordcount"] = $_var_150["total"];
		$_var_151["url"] = $_var_147;
		$_var_152 = model("Type")->getCache("type_list");
		$_var_153 = config("bind");
		$_var_154 = 0;
		$_var_155 = [];
		foreach ($_var_150["list"] as $_var_154 => $_var_156) {
			$_var_155[$_var_154] = $_var_156;
			$_var_157 = $param["cjflag"] . "_" . $_var_156["type_id"];
			if ($_var_153[$_var_157] > 0) {
				$_var_155[$_var_154]["type_id"] = $_var_153[$_var_157];
			} else {
				$_var_155[$_var_154]["type_id"] = 0;
			}
		}
		$_var_158 = [];
		$_var_154 = 0;
		if ($param["ac"] == "list") {
			foreach ($_var_150["class"] as $_var_159 => $_var_156) {
				$_var_158[$_var_154]["type_id"] = $_var_156["type_id"];
				$_var_158[$_var_154]["type_name"] = $_var_156["type_name"];
				$_var_154++;
			}
		}
		$_var_160 = ["code" => 1, "msg" => "ok", "page" => $_var_151, "type" => $_var_158, "data" => $_var_155];
		return $_var_160;
	}
	public function art_data($_var_161, $_var_162, $_var_163 = 1)
	{
		if ($_var_163 == 1) {
			mac_echo("[" . "art_data" . "] " . lang("model/collect/data_tip1", [$_var_162["page"]["page"], $_var_162["page"]["pagecount"], $_var_162["page"]["url"]]));
		}
		$_var_164 = config("maccms.collect");
		$_var_164 = $_var_164["art"];
		$_var_165 = $_var_161["sync_pic_opt"] > 0 ? $_var_161["sync_pic_opt"] : $_var_164["pic"];
		$_var_166 = model("Type")->getCache("type_list");
		$_var_167 = explode(",", $_var_164["filter"]);
		$_var_167 = array_filter($_var_167);
		$_var_168 = explode("#", $_var_164["words"]);
		$_var_168 = array_filter($_var_168);
		$_var_169 = mac_txt_explain($_var_164["thesaurus"], true);
		foreach ($_var_162["data"] as $_var_170 => $_var_171) {
			$_var_172 = "red";
			$_var_173 = "";
			$_var_174 = "";
			$_var_175 = "";
			if ($_var_171["type_id"] == 0) {
				$_var_173 = lang("model/collect/type_err");
			} elseif (empty($_var_171["art_name"])) {
				$_var_173 = lang("model/collect/name_err");
			} elseif (mac_array_filter($_var_167, $_var_171["art_name"]) !== false) {
				$_var_173 = lang("model/collect/name_in_filter_err");
			} else {
				unset($_var_171["art_id"]);
				foreach ($_var_171 as $_var_176 => $_var_177) {
					if (strpos($_var_176, "_content") === false) {
						$_var_171[$_var_176] = strip_tags($_var_177);
					}
				}
				$_var_171["art_name"] = trim($_var_171["art_name"]);
				$_var_171["type_id_1"] = intval($_var_166[$_var_171["type_id"]]["type_pid"]);
				$_var_171["art_en"] = Pinyin::get($_var_171["art_name"]);
				$_var_171["art_letter"] = strtoupper(substr($_var_171["art_en"], 0, 1));
				$_var_171["art_time_add"] = time();
				$_var_171["art_time"] = time();
				$_var_171["art_status"] = intval($_var_164["status"]);
				$_var_171["art_lock"] = intval($_var_171["art_lock"]);
				if (!empty($_var_171["art_status"])) {
					$_var_171["art_status"] = intval($_var_171["art_status"]);
				}
				$_var_171["art_level"] = intval($_var_171["art_level"]);
				$_var_171["art_hits"] = intval($_var_171["art_hits"]);
				$_var_171["art_hits_day"] = intval($_var_171["art_hits_day"]);
				$_var_171["art_hits_week"] = intval($_var_171["art_hits_week"]);
				$_var_171["art_hits_month"] = intval($_var_171["art_hits_month"]);
				$_var_171["art_stint"] = intval($_var_171["art_stint"]);
				$_var_171["art_up"] = intval($_var_171["art_up"]);
				$_var_171["art_down"] = intval($_var_171["art_down"]);
				$_var_171["art_score"] = floatval($_var_171["art_score"]);
				$_var_171["art_score_all"] = intval($_var_171["art_score_all"]);
				$_var_171["art_score_num"] = intval($_var_171["art_score_num"]);
				if ($_var_164["hits_start"] > 0 && $_var_164["hits_end"] > 0) {
					$_var_171["art_hits"] = rand($_var_164["hits_start"], $_var_164["hits_end"]);
					$_var_171["art_hits_day"] = rand($_var_164["hits_start"], $_var_164["hits_end"]);
					$_var_171["art_hits_week"] = rand($_var_164["hits_start"], $_var_164["hits_end"]);
					$_var_171["art_hits_month"] = rand($_var_164["hits_start"], $_var_164["hits_end"]);
				}
				if ($_var_164["updown_start"] > 0 && $_var_164["updown_end"]) {
					$_var_171["art_up"] = rand($_var_164["updown_start"], $_var_164["updown_end"]);
					$_var_171["art_down"] = rand($_var_164["updown_start"], $_var_164["updown_end"]);
				}
				if ($_var_164["score"] == 1) {
					$_var_171["art_score_num"] = rand(1, 1000);
					$_var_171["art_score_all"] = $_var_171["art_score_num"] * rand(1, 10);
					$_var_171["art_score"] = round($_var_171["art_score_all"] / $_var_171["art_score_num"], 1);
				}
				if ($_var_164["psernd"] == 1) {
					$_var_171["art_content"] = mac_rep_pse_rnd($_var_168, $_var_171["art_content"]);
				}
				if ($_var_164["psesyn"] == 1) {
					$_var_171["art_content"] = mac_rep_pse_syn($_var_169, $_var_171["art_content"]);
				}
				if (empty($_var_171["art_blurb"])) {
					$_var_171["art_blurb"] = mac_substring(strip_tags(str_replace("\$\$\$", "", $_var_171["art_content"])), 100);
				}
				$_var_178 = [];
				$_var_178["art_name"] = $_var_171["art_name"];
				if (strpos($_var_164["inrule"], "b") !== false) {
					$_var_178["type_id"] = $_var_171["type_id"];
				}
				$_var_179 = explode("\$\$\$", $_var_171["art_title"]);
				$_var_180 = explode("\$\$\$", $_var_171["art_note"]);
				$_var_181 = explode("\$\$\$", $_var_171["art_content"]);
				$_var_182 = [];
				$_var_183 = [];
				$_var_184 = [];
				foreach ($_var_181 as $_var_185 => $_var_186) {
					$_var_184[] = $_var_186;
					$_var_182[] = $_var_179[$_var_185];
					$_var_183[] = $_var_180[$_var_185];
				}
				$_var_171["art_title"] = join("\$\$\$", $_var_182);
				$_var_171["art_note"] = join("\$\$\$", $_var_183);
				$_var_171["art_content"] = join("\$\$\$", $_var_184);
				$_var_187 = model("Art")->where($_var_178)->find();
				if (!$_var_187) {
					$_var_175 = $this->syncImages($_var_165, $_var_171["art_pic"], "art");
					$_var_171["art_pic"] = (string) $_var_175["pic"];
					$_var_174 = $_var_175["msg"];
					$_var_188 = model("Art")->insert($_var_171);
					if ($_var_188 === false) {
					}
					$_var_172 = "green";
					$_var_173 = lang("model/collect/add_ok");
				} else {
					if (empty($_var_164["uprule"])) {
						$_var_173 = lang("model/collect/uprule_empty");
					} elseif ($_var_187["art_lock"] == 1) {
						$_var_173 = lang("model/collect/data_lock");
					} else {
						unset($_var_171["art_time_add"]);
						$_var_189 = $_var_187["art_title"];
						$_var_190 = $_var_187["art_note"];
						$_var_191 = $_var_187["art_content"];
						$_var_192 = $_var_171["art_title"];
						$_var_193 = $_var_171["art_note"];
						$_var_194 = $_var_171["art_content"];
						$_var_195 = true;
						if ($_var_195) {
							$_var_196 = [];
							if (strpos("," . $_var_164["uprule"], "a") !== false && !empty($_var_171["art_content"]) && $_var_171["art_content"] != $_var_187["art_content"]) {
								$_var_196["art_content"] = $_var_171["art_content"];
							}
							if (strpos("," . $_var_164["uprule"], "b") !== false && !empty($_var_171["art_author"]) && $_var_171["art_author"] != $_var_187["art_author"]) {
								$_var_196["art_author"] = $_var_171["art_author"];
							}
							if (strpos("," . $_var_164["uprule"], "c") !== false && !empty($_var_171["art_from"]) && $_var_171["art_from"] != $_var_187["art_from"]) {
								$_var_196["art_from"] = $_var_171["art_from"];
							}
							if (strpos("," . $_var_164["uprule"], "d") !== false && (substr($_var_187["art_pic"], 0, 4) == "http" || empty($_var_187["art_pic"])) && $_var_171["art_pic"] != $_var_187["art_pic"]) {
								$_var_175 = $this->syncImages($_var_165, $_var_171["art_pic"], "art");
								$_var_196["art_pic"] = (string) $_var_175["pic"];
								$_var_174 = $_var_175["msg"];
							}
							if (strpos("," . $_var_164["uprule"], "e") !== false && !empty($_var_171["art_tag"]) && $_var_171["art_tag"] != $_var_187["art_tag"]) {
								$_var_196["art_tag"] = $_var_171["art_tag"];
							}
							if (strpos("," . $_var_164["uprule"], "f") !== false && !empty($_var_171["art_blurb"]) && $_var_171["art_blurb"] != $_var_187["art_blurb"]) {
								$_var_196["art_blurb"] = $_var_171["art_blurb"];
							}
							if (count($_var_196) > 0) {
								$_var_196["art_time"] = time();
								$_var_178 = [];
								$_var_178["art_id"] = $_var_187["art_id"];
								$_var_188 = model("Art")->where($_var_178)->update($_var_196);
								$_var_172 = "green";
								if ($_var_188 === false) {
								}
							} else {
								$_var_173 = lang("model/collect/not_need_update");
							}
						}
					}
				}
			}
			if ($_var_163 == 1) {
				mac_echo($_var_170 + 1 . $_var_171["art_name"] . "<font color=" . $_var_172 . ">" . $_var_173 . "</font>" . $_var_174 . "");
			} else {
				return ["code" => $_var_172 == "red" ? 1001 : 1, "msg" => $_var_171["art_name"] . " " . $_var_173];
			}
		}
		$_var_197 = $GLOBALS["config"]["app"]["cache_flag"] . "_" . "collect_break_art";
		if (ENTRANCE == "api") {
			Cache::rm($_var_197);
			if ($_var_162["page"]["page"] < $_var_162["page"]["pagecount"]) {
				$_var_161["page"] = intval($_var_162["page"]["page"]) + 1;
				$_var_188 = $this->art($_var_161);
				if ($_var_188["code"] > 1) {
					return $this->error($_var_188["msg"]);
				}
				$this->art_data($_var_161, $_var_188);
			}
			mac_echo(lang("model/collect/is_over"));
			die;
		}
		if (empty($GLOBALS["config"]["app"]["collect_timespan"])) {
			$GLOBALS["config"]["app"]["collect_timespan"] = 3;
		}
		if ($_var_163 == 1) {
			if ($_var_161["ac"] == "cjsel") {
				Cache::rm($_var_197);
				mac_echo(lang("model/collect/is_over"));
				unset($_var_161["ids"]);
				$_var_161["ac"] = "list";
				$_var_198 = url("api") . "?" . http_build_query($_var_161);
				$_var_199 = $_SERVER["HTTP_REFERER"];
				if (!empty($_var_199)) {
					$_var_198 = $_var_199;
				}
				mac_jump($_var_198, $GLOBALS["config"]["app"]["collect_timespan"]);
			} else {
				if ($_var_162["page"]["page"] >= $_var_162["page"]["pagecount"]) {
					Cache::rm($_var_197);
					mac_echo(lang("model/collect/is_over"));
					unset($_var_161["page"]);
					$_var_161["ac"] = "list";
					$_var_198 = url("api") . "?" . http_build_query($_var_161);
					mac_jump($_var_198, $GLOBALS["config"]["app"]["collect_timespan"]);
				} else {
					$_var_161["page"] = intval($_var_162["page"]["page"]) + 1;
					$_var_198 = url("api") . "?" . http_build_query($_var_161);
					mac_jump($_var_198, $GLOBALS["config"]["app"]["collect_timespan"]);
				}
			}
		}
	}
	public function actor_json($_var_200)
	{
		$_var_201 = [];
		$_var_201["ac"] = $_var_200["ac"];
		$_var_201["t"] = $_var_200["t"];
		$_var_201["pg"] = is_numeric($_var_200["page"]) ? $_var_200["page"] : "";
		$_var_201["h"] = $_var_200["h"];
		$_var_201["ids"] = $_var_200["ids"];
		$_var_201["wd"] = $_var_200["wd"];
		if ($_var_200["ac"] != "list") {
			$_var_201["ac"] = "detail";
		}
		$_var_202 = $_var_200["cjurl"];
		if (strpos($_var_202, "?") === false) {
			$_var_202 .= "?";
		} else {
			$_var_202 .= "&";
		}
		$_var_202 .= http_build_query($_var_201) . base64_decode($_var_200["param"]);
		$_var_203 = $this->checkCjUrl($_var_202);
		if ($_var_203["code"] > 1) {
			return $_var_203;
		}
		$_var_204 = mac_curl_get($_var_202);
		if (empty($_var_204)) {
			return ["code" => 1001, "msg" => lang("model/collect/get_html_err") . ", url: " . $_var_202];
		}
		$_var_204 = mac_filter_tags($_var_204);
		$_var_205 = json_decode($_var_204, true);
		if (!$_var_205) {
			return ["code" => 1002, "msg" => lang("model/collect/json_err") . ": " . mb_substr($_var_204, 0, 15)];
		}
		$_var_206 = [];
		$_var_206["page"] = $_var_205["page"];
		$_var_206["pagecount"] = $_var_205["pagecount"];
		$_var_206["pagesize"] = $_var_205["limit"];
		$_var_206["recordcount"] = $_var_205["total"];
		$_var_206["url"] = $_var_202;
		$_var_207 = model("Type")->getCache("type_list");
		$_var_208 = config("bind");
		$_var_209 = 0;
		$_var_210 = [];
		foreach ($_var_205["list"] as $_var_209 => $_var_211) {
			$_var_210[$_var_209] = $_var_211;
			$_var_212 = $_var_200["cjflag"] . "_" . $_var_211["type_id"];
			if ($_var_208[$_var_212] > 0) {
				$_var_210[$_var_209]["type_id"] = $_var_208[$_var_212];
			} else {
				$_var_210[$_var_209]["type_id"] = 0;
			}
		}
		$_var_213 = [];
		$_var_209 = 0;
		if ($_var_200["ac"] == "list") {
			foreach ($_var_205["class"] as $_var_214 => $_var_211) {
				$_var_213[$_var_209]["type_id"] = $_var_211["type_id"];
				$_var_213[$_var_209]["type_name"] = $_var_211["type_name"];
				$_var_209++;
			}
		}
		$_var_215 = ["code" => 1, "msg" => "ok", "page" => $_var_206, "type" => $_var_213, "data" => $_var_210];
		return $_var_215;
	}
	public function actor_data($param, $data, $show = 1)
	{
		if ($show == 1) {
			mac_echo("[" . "actor_data" . "] " . lang("model/collect/data_tip1", [$data["page"]["page"], $data["page"]["pagecount"], $data["page"]["url"]]));
		}
		$_var_216 = config("maccms.collect");
		$_var_216 = $_var_216["actor"];
		$_var_217 = $param["sync_pic_opt"] > 0 ? $param["sync_pic_opt"] : $_var_216["pic"];
		$_var_218 = model("Type")->getCache("type_list");
		$_var_219 = explode(",", $_var_216["filter"]);
		$_var_219 = array_filter($_var_219);
		$_var_220 = explode("#", $_var_216["words"]);
		$_var_220 = array_filter($_var_220);
		$_var_221 = mac_txt_explain($_var_216["thesaurus"], true);
		foreach ($data["data"] as $_var_222 => $_var_223) {
			$_var_224 = "red";
			$_var_225 = "";
			$_var_226 = "";
			$_var_227 = "";
			if ($_var_223["type_id"] == 0) {
				$_var_225 = lang("model/collect/type_err");
			} elseif (empty($_var_223["actor_name"]) || empty($_var_223["actor_sex"])) {
				$_var_225 = lang("odel/collect/actor_data_require");
			} elseif (mac_array_filter($_var_219, $_var_223["actor_name"]) !== false) {
				$_var_225 = lang("model/collect/name_in_filter_err");
			} else {
				unset($_var_223["actor_id"]);
				foreach ($_var_223 as $_var_228 => $_var_229) {
					if (strpos($_var_228, "_content") === false) {
						$_var_223[$_var_228] = strip_tags($_var_229);
					}
				}
				$_var_223["actor_name"] = trim($_var_223["actor_name"]);
				$_var_223["type_id_1"] = intval($_var_218[$_var_223["type_id"]]["type_pid"]);
				$_var_223["actor_en"] = Pinyin::get($_var_223["actor_name"]);
				$_var_223["actor_letter"] = strtoupper(substr($_var_223["actor_en"], 0, 1));
				$_var_223["actor_time_add"] = time();
				$_var_223["actor_time"] = time();
				$_var_223["actor_status"] = intval($_var_216["status"]);
				$_var_223["actor_lock"] = intval($_var_223["actor_lock"]);
				if (!empty($_var_223["actor_status"])) {
					$_var_223["actor_status"] = intval($_var_223["actor_status"]);
				}
				$_var_223["actor_level"] = intval($_var_223["actor_level"]);
				$_var_223["actor_hits"] = intval($_var_223["actor_hits"]);
				$_var_223["actor_hits_day"] = intval($_var_223["actor_hits_day"]);
				$_var_223["actor_hits_week"] = intval($_var_223["actor_hits_week"]);
				$_var_223["actor_hits_month"] = intval($_var_223["actor_hits_month"]);
				$_var_223["actor_up"] = intval($_var_223["actor_up"]);
				$_var_223["actor_down"] = intval($_var_223["actor_down"]);
				$_var_223["actor_score"] = floatval($_var_223["actor_score"]);
				$_var_223["actor_score_all"] = intval($_var_223["actor_score_all"]);
				$_var_223["actor_score_num"] = intval($_var_223["actor_score_num"]);
				if ($_var_216["hits_start"] > 0 && $_var_216["hits_end"] > 0) {
					$_var_223["actor_hits"] = rand($_var_216["hits_start"], $_var_216["hits_end"]);
					$_var_223["actor_hits_day"] = rand($_var_216["hits_start"], $_var_216["hits_end"]);
					$_var_223["actor_hits_week"] = rand($_var_216["hits_start"], $_var_216["hits_end"]);
					$_var_223["actor_hits_month"] = rand($_var_216["hits_start"], $_var_216["hits_end"]);
				}
				if ($_var_216["updown_start"] > 0 && $_var_216["updown_end"]) {
					$_var_223["actor_up"] = rand($_var_216["updown_start"], $_var_216["updown_end"]);
					$_var_223["actor_down"] = rand($_var_216["updown_start"], $_var_216["updown_end"]);
				}
				if ($_var_216["score"] == 1) {
					$_var_223["actor_score_num"] = rand(1, 1000);
					$_var_223["actor_score_all"] = $_var_223["actor_score_num"] * rand(1, 10);
					$_var_223["actor_score"] = round($_var_223["actor_score_all"] / $_var_223["actor_score_num"], 1);
				}
				if ($_var_216["psernd"] == 1) {
					$_var_223["actor_content"] = mac_rep_pse_rnd($_var_220, $_var_223["actor_content"]);
				}
				if ($_var_216["psesyn"] == 1) {
					$_var_223["actor_content"] = mac_rep_pse_syn($_var_221, $_var_223["actor_content"]);
				}
				if (empty($_var_223["actor_blurb"])) {
					$_var_223["actor_blurb"] = mac_substring(strip_tags($_var_223["actor_content"]), 100);
				}
				$_var_230 = [];
				$_var_230["actor_name"] = $_var_223["actor_name"];
				if (strpos($_var_216["inrule"], "b") !== false) {
					$_var_230["actor_sex"] = $_var_223["actor_sex"];
				}
				if (strpos($_var_216["inrule"], "c") !== false) {
					$_var_230["type_id"] = $_var_223["type_id"];
				}
				$_var_231 = model("Actor")->where($_var_230)->find();
				if (!$_var_231) {
					$_var_227 = $this->syncImages($_var_217, $_var_223["actor_pic"], "actor");
					$_var_223["actor_pic"] = $_var_227["pic"];
					$_var_226 = $_var_227["msg"];
					$_var_232 = model("Actor")->insert($_var_223);
					if ($_var_232 === false) {
					}
					$_var_224 = "green";
					$_var_225 = lang("model/collect/add_ok");
				} else {
					if (empty($_var_216["uprule"])) {
						$_var_225 = lang("model/collect/uprule_empty");
					} elseif ($_var_231["actor_lock"] == 1) {
						$_var_225 = lang("model/collect/data_lock");
					} else {
						unset($_var_223["actor_time_add"]);
						$_var_233 = true;
						if ($_var_233) {
							$_var_234 = [];
							if (strpos("," . $_var_216["uprule"], "a") !== false && !empty($_var_223["actor_content"]) && $_var_223["actor_content"] != $_var_231["actor_content"]) {
								$_var_234["actor_content"] = $_var_223["actor_content"];
							}
							if (strpos("," . $_var_216["uprule"], "b") !== false && !empty($_var_223["actor_blurb"]) && $_var_223["actor_blurb"] != $_var_231["actor_blurb"]) {
								$_var_234["actor_blurb"] = $_var_223["actor_blurb"];
							}
							if (strpos("," . $_var_216["uprule"], "c") !== false && !empty($_var_223["actor_remarks"]) && $_var_223["actor_remarks"] != $_var_231["actor_remarks"]) {
								$_var_234["actor_remarks"] = $_var_223["actor_remarks"];
							}
							if (strpos("," . $_var_216["uprule"], "d") !== false && !empty($_var_223["actor_works"]) && $_var_223["actor_works"] != $_var_231["actor_works"]) {
								$_var_234["actor_works"] = $_var_223["actor_works"];
							}
							if (strpos("," . $_var_216["uprule"], "e") !== false && (substr($_var_231["actor_pic"], 0, 4) == "http" || empty($_var_231["actor_pic"])) && $_var_223["actor_pic"] != $_var_231["actor_pic"]) {
								$_var_227 = $this->syncImages($_var_217, $_var_223["actor_pic"], "actor");
								$_var_234["actor_pic"] = $_var_227["pic"];
								$_var_226 = $_var_227["msg"];
							}
							if (count($_var_234) > 0) {
								$_var_234["actor_time"] = time();
								$_var_230 = [];
								$_var_230["actor_id"] = $_var_231["actor_id"];
								$_var_232 = model("Actor")->where($_var_230)->update($_var_234);
								$_var_224 = "green";
								if ($_var_232 === false) {
								}
							} else {
								$_var_225 = lang("model/collect/not_need_update");
							}
						}
					}
				}
			}
			if ($show == 1) {
				mac_echo($_var_222 + 1 . $_var_223["actor_name"] . "<font color=" . $_var_224 . ">" . $_var_225 . "</font>" . $_var_226 . "");
			} else {
				return ["code" => $_var_224 == "red" ? 1001 : 1, "msg" => $_var_223["actor_name"] . " " . $_var_225];
			}
		}
		$_var_235 = $GLOBALS["config"]["app"]["cache_flag"] . "_" . "collect_break_actor";
		if (ENTRANCE == "api") {
			Cache::rm($_var_235);
			if ($data["page"]["page"] < $data["page"]["pagecount"]) {
				$param["page"] = intval($data["page"]["page"]) + 1;
				$_var_232 = $this->actor($param);
				if ($_var_232["code"] > 1) {
					return $this->error($_var_232["msg"]);
				}
				$this->actor_data($param, $_var_232);
			}
			mac_echo(lang("model/collect/is_over"));
			die;
		}
		if (empty($GLOBALS["config"]["app"]["collect_timespan"])) {
			$GLOBALS["config"]["app"]["collect_timespan"] = 3;
		}
		if ($show == 1) {
			if ($param["ac"] == "cjsel") {
				Cache::rm($_var_235);
				mac_echo(lang("model/collect/is_over"));
				unset($param["ids"]);
				$param["ac"] = "list";
				$_var_236 = url("api") . "?" . http_build_query($param);
				$_var_237 = $_SERVER["HTTP_REFERER"];
				if (!empty($_var_237)) {
					$_var_236 = $_var_237;
				}
				mac_jump($_var_236, $GLOBALS["config"]["app"]["collect_timespan"]);
			} else {
				if ($data["page"]["page"] >= $data["page"]["pagecount"]) {
					Cache::rm($_var_235);
					mac_echo(lang("model/collect/is_over"));
					unset($param["page"]);
					$param["ac"] = "list";
					$_var_236 = url("api") . "?" . http_build_query($param);
					mac_jump($_var_236, $GLOBALS["config"]["app"]["collect_timespan"]);
				} else {
					$param["page"] = intval($data["page"]["page"]) + 1;
					$_var_236 = url("api") . "?" . http_build_query($param);
					mac_jump($_var_236, $GLOBALS["config"]["app"]["collect_timespan"]);
				}
			}
		}
	}
	public function role_json($param)
	{
		$_var_238 = [];
		$_var_238["ac"] = $param["ac"];
		$_var_238["t"] = $param["t"];
		$_var_238["pg"] = is_numeric($param["page"]) ? $param["page"] : "";
		$_var_238["h"] = $param["h"];
		$_var_238["ids"] = $param["ids"];
		$_var_238["wd"] = $param["wd"];
		if ($param["ac"] != "list") {
			$_var_238["ac"] = "detail";
		}
		$_var_239 = $param["cjurl"];
		if (strpos($_var_239, "?") === false) {
			$_var_239 .= "?";
		} else {
			$_var_239 .= "&";
		}
		$_var_239 .= http_build_query($_var_238) . base64_decode($param["param"]);
		$_var_240 = $this->checkCjUrl($_var_239);
		if ($_var_240["code"] > 1) {
			return $_var_240;
		}
		$_var_241 = mac_curl_get($_var_239);
		if (empty($_var_241)) {
			return ["code" => 1001, "msg" => lang("model/collect/get_html_err") . ", url: " . $_var_239];
		}
		$_var_241 = mac_filter_tags($_var_241);
		$_var_242 = json_decode($_var_241, true);
		if (!$_var_242) {
			return ["code" => 1002, "msg" => lang("model/collect/json_err") . ": " . mb_substr($_var_241, 0, 15)];
		}
		$_var_243 = [];
		$_var_243["page"] = $_var_242["page"];
		$_var_243["pagecount"] = $_var_242["pagecount"];
		$_var_243["pagesize"] = $_var_242["limit"];
		$_var_243["recordcount"] = $_var_242["total"];
		$_var_243["url"] = $_var_239;
		$_var_244 = 0;
		$_var_245 = [];
		foreach ($_var_242["list"] as $_var_244 => $_var_246) {
			$_var_245[$_var_244] = $_var_246;
		}
		$_var_247 = ["code" => 1, "msg" => "ok", "page" => $_var_243, "data" => $_var_245];
		return $_var_247;
	}
	public function role_data($param, $data, $show = 1)
	{
		if ($show == 1) {
			mac_echo("[" . "role_data" . "] " . lang("model/collect/data_tip1", [$data["page"]["page"], $data["page"]["pagecount"], $data["page"]["url"]]));
		}
		$_var_248 = config("maccms.collect");
		$_var_248 = $_var_248["role"];
		$_var_249 = $param["sync_pic_opt"] > 0 ? $param["sync_pic_opt"] : $_var_248["pic"];
		$_var_250 = explode(",", $_var_248["filter"]);
		$_var_250 = array_filter($_var_250);
		$_var_251 = explode("#", $_var_248["words"]);
		$_var_251 = array_filter($_var_251);
		$_var_252 = mac_txt_explain($_var_248["thesaurus"], true);
		foreach ($data["data"] as $_var_253 => $_var_254) {
			$_var_255 = "red";
			$_var_256 = "";
			$_var_257 = "";
			$_var_258 = "";
			if (empty($_var_254["role_name"]) || empty($_var_254["role_actor"]) || empty($_var_254["vod_name"])) {
				$_var_256 = lang("model/collect/role_data_require");
			} elseif (mac_array_filter($_var_250, $_var_254["role_name"]) !== false) {
				$_var_256 = lang("model/collect/name_in_filter_err");
			} else {
				unset($_var_254["role_id"]);
				foreach ($_var_254 as $_var_259 => $_var_260) {
					if (strpos($_var_259, "_content") === false) {
						$_var_254[$_var_259] = strip_tags($_var_260);
					}
				}
				$_var_254["role_en"] = Pinyin::get($_var_254["role_name"]);
				$_var_254["role_letter"] = strtoupper(substr($_var_254["role_en"], 0, 1));
				$_var_254["role_time_add"] = time();
				$_var_254["role_time"] = time();
				$_var_254["role_status"] = intval($_var_248["status"]);
				$_var_254["role_lock"] = intval($_var_254["role_lock"]);
				if (!empty($_var_254["role_status"])) {
					$_var_254["role_status"] = intval($_var_254["role_status"]);
				}
				$_var_254["role_level"] = intval($_var_254["role_level"]);
				$_var_254["role_hits"] = intval($_var_254["role_hits"]);
				$_var_254["role_hits_day"] = intval($_var_254["role_hits_day"]);
				$_var_254["role_hits_week"] = intval($_var_254["role_hits_week"]);
				$_var_254["role_hits_month"] = intval($_var_254["role_hits_month"]);
				$_var_254["role_up"] = intval($_var_254["role_up"]);
				$_var_254["role_down"] = intval($_var_254["role_down"]);
				$_var_254["role_score"] = floatval($_var_254["role_score"]);
				$_var_254["role_score_all"] = intval($_var_254["role_score_all"]);
				$_var_254["role_score_num"] = intval($_var_254["role_score_num"]);
				if ($_var_248["hits_start"] > 0 && $_var_248["hits_end"] > 0) {
					$_var_254["role_hits"] = rand($_var_248["hits_start"], $_var_248["hits_end"]);
					$_var_254["role_hits_day"] = rand($_var_248["hits_start"], $_var_248["hits_end"]);
					$_var_254["role_hits_week"] = rand($_var_248["hits_start"], $_var_248["hits_end"]);
					$_var_254["role_hits_month"] = rand($_var_248["hits_start"], $_var_248["hits_end"]);
				}
				if ($_var_248["updown_start"] > 0 && $_var_248["updown_end"]) {
					$_var_254["role_up"] = rand($_var_248["updown_start"], $_var_248["updown_end"]);
					$_var_254["role_down"] = rand($_var_248["updown_start"], $_var_248["updown_end"]);
				}
				if ($_var_248["score"] == 1) {
					$_var_254["role_score_num"] = rand(1, 1000);
					$_var_254["role_score_all"] = $_var_254["role_score_num"] * rand(1, 10);
					$_var_254["role_score"] = round($_var_254["role_score_all"] / $_var_254["role_score_num"], 1);
				}
				if ($_var_248["psernd"] == 1) {
					$_var_254["role_content"] = mac_rep_pse_rnd($_var_251, $_var_254["role_content"]);
				}
				if ($_var_248["psesyn"] == 1) {
					$_var_254["role_content"] = mac_rep_pse_syn($_var_252, $_var_254["role_content"]);
				}
				$_var_261 = [];
				$_var_261["role_name"] = $_var_254["role_name"];
				$_var_261["role_actor"] = $_var_254["role_actor"];
				$_var_262 = [];
				$_var_263 = false;
				if (!empty($_var_254["douban_id"])) {
					$_var_262["vod_douban_id"] = ["eq", $_var_254["douban_id"]];
					unset($_var_254["douban_id"]);
				} else {
					$_var_262["vod_name"] = ["eq", $_var_254["vod_name"]];
				}
				if (strpos($_var_248["inrule"], "c") !== false) {
					$_var_262["vod_actor"] = ["like", mac_like_arr($_var_254["role_actor"]), "OR"];
				}
				if (strpos($_var_248["inrule"], "d") !== false) {
					$_var_262["vod_director"] = ["like", mac_like_arr($_var_254["role_actor"]), "OR"];
				}
				if (!empty($_var_262["vod_actor"]) && !empty($_var_262["vod_director"])) {
					$_var_263 = true;
					$GLOBALS["blend"] = ["vod_actor" => $_var_262["vod_actor"], "vod_director" => $_var_262["vod_director"]];
					unset($_var_262["vod_actor"], $_var_262["vod_director"]);
				}
				if ($_var_263 === false) {
					$_var_264 = model("Vod")->where($_var_262)->find();
				} else {
					$_var_264 = model("Vod")->where($_var_262)->where(function ($_var_265) {
						$_var_265->where("vod_director", $GLOBALS["blend"]["vod_director"])->whereOr("vod_actor", $GLOBALS["blend"]["vod_actor"]);
					})->find();
				}
				if (!$_var_264) {
					$_var_256 = lang("model/collect/not_found_rel_vod");
				} else {
					$_var_254["role_rid"] = $_var_264["vod_id"];
					$_var_261["role_rid"] = $_var_264["vod_id"];
					$_var_266 = model("Role")->where($_var_261)->find();
					if (!$_var_266) {
						$_var_258 = $this->syncImages($_var_249, $_var_254["role_pic"], "role");
						$_var_254["role_pic"] = $_var_258["pic"];
						$_var_257 = $_var_258["msg"];
						$_var_267 = model("Role")->insert($_var_254);
						if ($_var_267 === false) {
						}
						$_var_255 = "green";
						$_var_256 = lang("model/collect/add_ok");
					} else {
						if (empty($_var_248["uprule"])) {
							$_var_256 = lang("model/collect/uprule_empty");
						} elseif ($_var_266["role_lock"] == 1) {
							$_var_256 = lang("model/collect/data_lock");
						} else {
							unset($_var_254["role_time_add"]);
							$_var_268 = true;
							if ($_var_268) {
								$_var_269 = [];
								if (strpos("," . $_var_248["uprule"], "a") !== false && !empty($_var_254["role_content"]) && $_var_254["role_content"] != $_var_266["role_content"]) {
									$_var_269["role_content"] = $_var_254["role_content"];
								}
								if (strpos("," . $_var_248["uprule"], "b") !== false && !empty($_var_254["role_remarks"]) && $_var_254["role_remarks"] != $_var_266["role_remarks"]) {
									$_var_269["role_remarks"] = $_var_254["role_remarks"];
								}
								if (strpos("," . $_var_248["uprule"], "c") !== false && (substr($_var_266["role_pic"], 0, 4) == "http" || empty($_var_266["role_pic"])) && $_var_254["role_pic"] != $_var_266["role_pic"]) {
									$_var_258 = $this->syncImages($_var_249, $_var_254["role_pic"], "role");
									$_var_269["role_pic"] = $_var_258["pic"];
									$_var_257 = $_var_258["msg"];
								}
								if (count($_var_269) > 0) {
									$_var_269["role_time"] = time();
									$_var_261 = [];
									$_var_261["role_id"] = $_var_266["role_id"];
									$_var_267 = model("Role")->where($_var_261)->update($_var_269);
									$_var_255 = "green";
									if ($_var_267 === false) {
									}
								} else {
									$_var_256 = lang("model/collect/not_need_update");
								}
							}
						}
					}
				}
			}
			if ($show == 1) {
				mac_echo($_var_253 + 1 . $_var_254["role_name"] . "<font color=" . $_var_255 . ">" . $_var_256 . "</font>" . $_var_257 . "");
			} else {
				return ["code" => $_var_255 == "red" ? 1001 : 1, "msg" => $_var_254["role_name"] . " " . $_var_256];
			}
		}
		$_var_270 = $GLOBALS["config"]["app"]["cache_flag"] . "_" . "collect_break_role";
		if (ENTRANCE == "api") {
			Cache::rm($_var_270);
			if ($data["page"]["page"] < $data["page"]["pagecount"]) {
				$param["page"] = intval($data["page"]["page"]) + 1;
				$_var_267 = $this->role($param);
				if ($_var_267["code"] > 1) {
					return $this->error($_var_267["msg"]);
				}
				$this->actor_data($param, $_var_267);
			}
			mac_echo(lang("model/collect/is_over"));
			die;
		}
		if (empty($GLOBALS["config"]["app"]["collect_timespan"])) {
			$GLOBALS["config"]["app"]["collect_timespan"] = 3;
		}
		if ($show == 1) {
			if ($param["ac"] == "cjsel") {
				Cache::rm($_var_270);
				mac_echo(lang("model/collect/is_over"));
				unset($param["ids"]);
				$param["ac"] = "list";
				$_var_271 = url("api") . "?" . http_build_query($param);
				$_var_272 = $_SERVER["HTTP_REFERER"];
				if (!empty($_var_272)) {
					$_var_271 = $_var_272;
				}
				mac_jump($_var_271, $GLOBALS["config"]["app"]["collect_timespan"]);
			} else {
				if ($data["page"]["page"] >= $data["page"]["pagecount"]) {
					Cache::rm($_var_270);
					mac_echo(lang("model/collect/is_over"));
					unset($param["page"]);
					$param["ac"] = "list";
					$_var_271 = url("api") . "?" . http_build_query($param);
					mac_jump($_var_271, $GLOBALS["config"]["app"]["collect_timespan"]);
				} else {
					$param["page"] = intval($data["page"]["page"]) + 1;
					$_var_271 = url("api") . "?" . http_build_query($param);
					mac_jump($_var_271, $GLOBALS["config"]["app"]["collect_timespan"]);
				}
			}
		}
	}
	public function website_json($param)
	{
		$_var_273 = [];
		$_var_273["ac"] = $param["ac"];
		$_var_273["t"] = $param["t"];
		$_var_273["pg"] = is_numeric($param["page"]) ? $param["page"] : "";
		$_var_273["h"] = $param["h"];
		$_var_273["ids"] = $param["ids"];
		$_var_273["wd"] = $param["wd"];
		if ($param["ac"] != "list") {
			$_var_273["ac"] = "detail";
		}
		$_var_274 = $param["cjurl"];
		if (strpos($_var_274, "?") === false) {
			$_var_274 .= "?";
		} else {
			$_var_274 .= "&";
		}
		$_var_274 .= http_build_query($_var_273) . base64_decode($param["param"]);
		$_var_275 = $this->checkCjUrl($_var_274);
		if ($_var_275["code"] > 1) {
			return $_var_275;
		}
		$_var_276 = mac_curl_get($_var_274);
		if (empty($_var_276)) {
			return ["code" => 1001, "msg" => lang("model/collect/get_html_err") . ", url: " . $_var_274];
		}
		$_var_276 = mac_filter_tags($_var_276);
		$_var_277 = json_decode($_var_276, true);
		if (!$_var_277) {
			return ["code" => 1002, "msg" => lang("model/collect/json_err") . ": " . mb_substr($_var_276, 0, 15)];
		}
		$_var_278 = [];
		$_var_278["page"] = $_var_277["page"];
		$_var_278["pagecount"] = $_var_277["pagecount"];
		$_var_278["pagesize"] = $_var_277["limit"];
		$_var_278["recordcount"] = $_var_277["total"];
		$_var_278["url"] = $_var_274;
		$_var_279 = model("Type")->getCache("type_list");
		$_var_280 = config("bind");
		$_var_281 = 0;
		$_var_282 = [];
		foreach ($_var_277["list"] as $_var_281 => $_var_283) {
			$_var_282[$_var_281] = $_var_283;
			$_var_284 = $param["cjflag"] . "_" . $_var_283["type_id"];
			if ($_var_280[$_var_284] > 0) {
				$_var_282[$_var_281]["type_id"] = $_var_280[$_var_284];
			} else {
				$_var_282[$_var_281]["type_id"] = 0;
			}
		}
		$_var_285 = [];
		$_var_281 = 0;
		if ($param["ac"] == "list") {
			foreach ($_var_277["class"] as $_var_286 => $_var_283) {
				$_var_285[$_var_281]["type_id"] = $_var_283["type_id"];
				$_var_285[$_var_281]["type_name"] = $_var_283["type_name"];
				$_var_281++;
			}
		}
		$_var_287 = ["code" => 1, "msg" => "ok", "page" => $_var_278, "type" => $_var_285, "data" => $_var_282];
		return $_var_287;
	}
	public function website_data($_var_288, $_var_289, $_var_290 = 1)
	{
		if ($_var_290 == 1) {
			mac_echo("[" . "website_data" . "] " . lang("model/collect/data_tip1", [$_var_289["page"]["page"], $_var_289["page"]["pagecount"], $_var_289["page"]["url"]]));
		}
		$_var_291 = config("maccms.collect");
		$_var_291 = $_var_291["website"];
		$_var_292 = $_var_288["sync_pic_opt"] > 0 ? $_var_288["sync_pic_opt"] : $_var_291["pic"];
		$_var_293 = model("Type")->getCache("type_list");
		$_var_294 = explode(",", $_var_291["filter"]);
		$_var_294 = array_filter($_var_294);
		$_var_295 = explode("#", $_var_291["words"]);
		$_var_295 = array_filter($_var_295);
		$_var_296 = mac_txt_explain($_var_291["thesaurus"], true);
		foreach ($_var_289["data"] as $_var_297 => $_var_298) {
			$_var_299 = "red";
			$_var_300 = "";
			$_var_301 = "";
			$_var_302 = "";
			if ($_var_298["type_id"] == 0) {
				$_var_300 = lang("model/collect/type_err");
			} elseif (empty($_var_298["website_name"])) {
				$_var_300 = lang("model/collect/name_err");
			} elseif (mac_array_filter($_var_294, $_var_298["website_name"]) !== false) {
				$_var_300 = lang("model/collect/name_in_filter_err");
			} else {
				unset($_var_298["website_id"]);
				foreach ($_var_298 as $_var_303 => $_var_304) {
					if (strpos($_var_303, "_content") === false) {
						$_var_298[$_var_303] = strip_tags($_var_304);
					}
				}
				$_var_298["website_name"] = trim($_var_298["website_name"]);
				$_var_298["type_id_1"] = intval($_var_293[$_var_298["type_id"]]["type_pid"]);
				$_var_298["website_en"] = Pinyin::get($_var_298["website_name"]);
				$_var_298["website_letter"] = strtoupper(substr($_var_298["website_en"], 0, 1));
				$_var_298["website_time_add"] = time();
				$_var_298["website_time"] = time();
				$_var_298["website_status"] = intval($_var_291["status"]);
				$_var_298["website_lock"] = intval($_var_298["website_lock"]);
				if (!empty($_var_298["website_status"])) {
					$_var_298["website_status"] = intval($_var_298["website_status"]);
				}
				$_var_298["website_level"] = intval($_var_298["website_level"]);
				$_var_298["website_hits"] = intval($_var_298["website_hits"]);
				$_var_298["website_hits_day"] = intval($_var_298["website_hits_day"]);
				$_var_298["website_hits_week"] = intval($_var_298["website_hits_week"]);
				$_var_298["website_hits_month"] = intval($_var_298["website_hits_month"]);
				$_var_298["website_up"] = intval($_var_298["website_up"]);
				$_var_298["website_down"] = intval($_var_298["website_down"]);
				$_var_298["website_score"] = floatval($_var_298["website_score"]);
				$_var_298["website_score_all"] = intval($_var_298["website_score_all"]);
				$_var_298["website_score_num"] = intval($_var_298["website_score_num"]);
				if ($_var_291["hits_start"] > 0 && $_var_291["hits_end"] > 0) {
					$_var_298["website_hits"] = rand($_var_291["hits_start"], $_var_291["hits_end"]);
					$_var_298["website_hits_day"] = rand($_var_291["hits_start"], $_var_291["hits_end"]);
					$_var_298["website_hits_week"] = rand($_var_291["hits_start"], $_var_291["hits_end"]);
					$_var_298["website_hits_month"] = rand($_var_291["hits_start"], $_var_291["hits_end"]);
				}
				if ($_var_291["updown_start"] > 0 && $_var_291["updown_end"]) {
					$_var_298["website_up"] = rand($_var_291["updown_start"], $_var_291["updown_end"]);
					$_var_298["website_down"] = rand($_var_291["updown_start"], $_var_291["updown_end"]);
				}
				if ($_var_291["score"] == 1) {
					$_var_298["website_score_num"] = rand(1, 1000);
					$_var_298["website_score_all"] = $_var_298["website_score_num"] * rand(1, 10);
					$_var_298["website_score"] = round($_var_298["website_score_all"] / $_var_298["website_score_num"], 1);
				}
				if ($_var_291["psernd"] == 1) {
					$_var_298["website_content"] = mac_rep_pse_rnd($_var_295, $_var_298["website_content"]);
				}
				if ($_var_291["psesyn"] == 1) {
					$_var_298["website_content"] = mac_rep_pse_syn($_var_296, $_var_298["website_content"]);
				}
				if (empty($_var_298["website_blurb"])) {
					$_var_298["website_blurb"] = mac_substring(strip_tags($_var_298["website_content"]), 100);
				}
				$_var_305 = [];
				$_var_305["website_name"] = $_var_298["website_name"];
				if (strpos($_var_291["inrule"], "b") !== false) {
					$_var_305["type_id"] = $_var_298["type_id"];
				}
				$_var_306 = model("Website")->where($_var_305)->find();
				if (!$_var_306) {
					$_var_302 = $this->syncImages($_var_292, $_var_298["website_pic"], "website");
					$_var_298["website_pic"] = $_var_302["pic"];
					$_var_301 = $_var_302["msg"];
					$_var_307 = model("Website")->insert($_var_298);
					if ($_var_307 === false) {
					}
					$_var_299 = "green";
					$_var_300 = lang("model/collect/add_ok");
				} else {
					if (empty($_var_291["uprule"])) {
						$_var_300 = lang("model/collect/uprule_empty");
					} elseif ($_var_306["website_lock"] == 1) {
						$_var_300 = lang("model/collect/data_lock");
					} else {
						unset($_var_298["website_time_add"]);
						$_var_308 = true;
						if ($_var_308) {
							$_var_309 = [];
							if (strpos("," . $_var_291["uprule"], "a") !== false && !empty($_var_298["website_content"]) && $_var_298["website_content"] != $_var_306["website_content"]) {
								$_var_309["website_content"] = $_var_298["website_content"];
							}
							if (strpos("," . $_var_291["uprule"], "b") !== false && !empty($_var_298["website_blurb"]) && $_var_298["website_blurb"] != $_var_306["website_blurb"]) {
								$_var_309["website_blurb"] = $_var_298["website_blurb"];
							}
							if (strpos("," . $_var_291["uprule"], "c") !== false && !empty($_var_298["website_remarks"]) && $_var_298["website_remarks"] != $_var_306["website_remarks"]) {
								$_var_309["website_remarks"] = $_var_298["website_remarks"];
							}
							if (strpos("," . $_var_291["uprule"], "d") !== false && !empty($_var_298["website_jumpurl"]) && $_var_298["website_jumpurl"] != $_var_306["website_jumpurl"]) {
								$_var_309["website_jumpurl"] = $_var_298["website_jumpurl"];
							}
							if (strpos("," . $_var_291["uprule"], "e") !== false && (substr($_var_306["website_pic"], 0, 4) == "http" || empty($_var_306["website_pic"])) && $_var_298["website_pic"] != $_var_306["website_pic"]) {
								$_var_302 = $this->syncImages($_var_292, $_var_298["website_pic"], "website");
								$_var_309["website_pic"] = $_var_302["pic"];
								$_var_301 = $_var_302["msg"];
							}
							if (count($_var_309) > 0) {
								$_var_309["website_time"] = time();
								$_var_305 = [];
								$_var_305["website_id"] = $_var_306["website_id"];
								$_var_307 = model("Website")->where($_var_305)->update($_var_309);
								$_var_299 = "green";
								if ($_var_307 === false) {
								}
							} else {
								$_var_300 = lang("model/collect/not_need_update");
							}
						}
					}
				}
			}
			if ($_var_290 == 1) {
				mac_echo($_var_297 + 1 . $_var_298["website_name"] . "<font color=" . $_var_299 . ">" . $_var_300 . "</font>" . $_var_301 . "");
			} else {
				return ["code" => $_var_299 == "red" ? 1001 : 1, "msg" => $_var_298["website_name"] . " " . $_var_300];
			}
		}
		$_var_310 = $GLOBALS["config"]["app"]["cache_flag"] . "_" . "collect_break_website";
		if (ENTRANCE == "api") {
			Cache::rm($_var_310);
			if ($_var_289["page"]["page"] < $_var_289["page"]["pagecount"]) {
				$_var_288["page"] = intval($_var_289["page"]["page"]) + 1;
				$_var_307 = $this->actor($_var_288);
				if ($_var_307["code"] > 1) {
					return $this->error($_var_307["msg"]);
				}
				$this->website_data($_var_288, $_var_307);
			}
			mac_echo(lang("model/collect/is_over"));
			die;
		}
		if (empty($GLOBALS["config"]["app"]["collect_timespan"])) {
			$GLOBALS["config"]["app"]["collect_timespan"] = 3;
		}
		if ($_var_290 == 1) {
			if ($_var_288["ac"] == "cjsel") {
				Cache::rm($_var_310);
				mac_echo(lang("model/collect/is_over"));
				unset($_var_288["ids"]);
				$_var_288["ac"] = "list";
				$_var_311 = url("api") . "?" . http_build_query($_var_288);
				$_var_312 = $_SERVER["HTTP_REFERER"];
				if (!empty($_var_312)) {
					$_var_311 = $_var_312;
				}
				mac_jump($_var_311, $GLOBALS["config"]["app"]["collect_timespan"]);
			} else {
				if ($_var_289["page"]["page"] >= $_var_289["page"]["pagecount"]) {
					Cache::rm($_var_310);
					mac_echo(lang("model/collect/is_over"));
					unset($_var_288["page"]);
					$_var_288["ac"] = "list";
					$_var_311 = url("api") . "?" . http_build_query($_var_288);
					mac_jump($_var_311, $GLOBALS["config"]["app"]["collect_timespan"]);
				} else {
					$_var_288["page"] = intval($_var_289["page"]["page"]) + 1;
					$_var_311 = url("api") . "?" . http_build_query($_var_288);
					mac_jump($_var_311, $GLOBALS["config"]["app"]["collect_timespan"]);
				}
			}
		}
	}
	public function comment_json($_var_313)
	{
		$_var_314 = [];
		$_var_314["ac"] = $_var_313["ac"];
		$_var_314["t"] = $_var_313["t"];
		$_var_314["pg"] = is_numeric($_var_313["page"]) ? $_var_313["page"] : "";
		$_var_314["h"] = $_var_313["h"];
		$_var_314["ids"] = $_var_313["ids"];
		$_var_314["wd"] = $_var_313["wd"];
		if ($_var_313["ac"] != "list") {
			$_var_314["ac"] = "detail";
		}
		$_var_315 = $_var_313["cjurl"];
		if (strpos($_var_315, "?") === false) {
			$_var_315 .= "?";
		} else {
			$_var_315 .= "&";
		}
		$_var_315 .= http_build_query($_var_314) . base64_decode($_var_313["param"]);
		$_var_316 = $this->checkCjUrl($_var_315);
		if ($_var_316["code"] > 1) {
			return $_var_316;
		}
		$_var_317 = mac_curl_get($_var_315);
		if (empty($_var_317)) {
			return ["code" => 1001, "msg" => lang("model/collect/get_html_err") . ", url: " . $_var_315];
		}
		$_var_317 = mac_filter_tags($_var_317);
		$_var_318 = json_decode($_var_317, true);
		if (!$_var_318) {
			return ["code" => 1002, "msg" => lang("model/collect/json_err") . ": " . mb_substr($_var_317, 0, 15)];
		}
		$_var_319 = [];
		$_var_319["page"] = $_var_318["page"];
		$_var_319["pagecount"] = $_var_318["pagecount"];
		$_var_319["pagesize"] = $_var_318["limit"];
		$_var_319["recordcount"] = $_var_318["total"];
		$_var_319["url"] = $_var_315;
		$_var_320 = 0;
		$_var_321 = [];
		foreach ($_var_318["list"] as $_var_320 => $_var_322) {
			$_var_321[$_var_320] = $_var_322;
		}
		$_var_323 = ["code" => 1, "msg" => "ok", "page" => $_var_319, "data" => $_var_321];
		return $_var_323;
	}
	public function comment_data($param, $data, $show = 1)
	{
		if ($show == 1) {
			mac_echo("[" . "comment_data" . "] " . lang("model/collect/data_tip1", [$data["page"]["page"], $data["page"]["pagecount"], $data["page"]["url"]]));
		}
		$_var_324 = config("maccms.collect");
		$_var_324 = $_var_324["comment"];
		$_var_325 = $param["sync_pic_opt"] > 0 ? $param["sync_pic_opt"] : $_var_324["pic"];
		$_var_326 = explode(",", $_var_324["filter"]);
		$_var_326 = array_filter($_var_326);
		$_var_327 = explode("#", $_var_324["words"]);
		$_var_327 = array_filter($_var_327);
		$_var_328 = mac_txt_explain($_var_324["thesaurus"], true);
		foreach ($data["data"] as $_var_329 => $_var_330) {
			$_var_331 = "red";
			$_var_332 = "";
			$_var_333 = "";
			$_var_334 = "";
			if (empty($_var_330["comment_name"]) || empty($_var_330["comment_content"]) || empty($_var_330["rel_name"])) {
				$_var_332 = lang("model/collect/comment_data_require");
			} elseif (mac_array_filter($_var_326, $_var_330["comment_content"]) !== false) {
				$_var_332 = lang("model/collect/name_in_filter_err");
			} else {
				unset($_var_330["comment_id"]);
				foreach ($_var_330 as $_var_335 => $_var_336) {
					if (strpos($_var_335, "_content") === false) {
						$_var_330[$_var_335] = strip_tags($_var_336);
					}
				}
				$_var_330["comment_time"] = time();
				$_var_330["comment_status"] = intval($_var_324["status"]);
				$_var_330["comment_up"] = intval($_var_330["comment_up"]);
				$_var_330["comment_down"] = intval($_var_330["comment_down"]);
				$_var_330["comment_mid"] = intval($_var_330["comment_mid"]);
				if (!empty($_var_330["comment_ip"]) && !is_numeric($_var_330["comment_ip"])) {
					$_var_330["comment_ip"] = mac_get_ip_long($_var_330["comment_ip"]);
				}
				if ($_var_324["updown_start"] > 0 && $_var_324["updown_end"]) {
					$_var_330["comment_up"] = rand($_var_324["updown_start"], $_var_324["updown_end"]);
					$_var_330["comment_down"] = rand($_var_324["updown_start"], $_var_324["updown_end"]);
				}
				if ($_var_324["psernd"] == 1) {
					$_var_330["comment_content"] = mac_rep_pse_rnd($_var_327, $_var_330["comment_content"]);
				}
				if ($_var_324["psesyn"] == 1) {
					$_var_330["comment_content"] = mac_rep_pse_syn($_var_328, $_var_330["comment_content"]);
				}
				$_var_337 = [];
				$_var_338 = [];
				$_var_339 = false;
				if (strpos($_var_324["inrule"], "b") !== false) {
					$_var_337["comment_content"] = ["eq", $_var_330["comment_content"]];
				}
				if (strpos($_var_324["inrule"], "c") !== false) {
					$_var_337["comment_name"] = ["eq", $_var_330["comment_name"]];
				}
				if (empty($_var_330["rel_id"])) {
					if ($_var_330["comment_mid"] == 1) {
						if (!empty($_var_330["douban_id"])) {
							$_var_338["vod_douban_id"] = ["eq", $_var_330["douban_id"]];
							unset($_var_330["douban_id"]);
						} else {
							$_var_338["vod_name"] = ["eq", $_var_330["rel_name"]];
						}
						$_var_340 = model("Vod")->where($_var_338)->find();
					} elseif ($_var_330["comment_mid"] == 2) {
						$_var_338["art_name"] = ["eq", $_var_330["rel_name"]];
						$_var_340 = model("Art")->where($_var_338)->find();
					} elseif ($_var_330["comment_mid"] == 3) {
						$_var_338["topic_name"] = ["eq", $_var_330["rel_name"]];
						$_var_340 = model("Topic")->where($_var_338)->find();
					} elseif ($_var_330["comment_mid"] == 8) {
						$_var_338["actor_name"] = ["eq", $_var_330["rel_name"]];
						$_var_340 = model("Actor")->where($_var_338)->find();
					} elseif ($_var_330["comment_mid"] == 9) {
						$_var_338["role_name"] = ["eq", $_var_330["rel_name"]];
						$_var_340 = model("Role")->where($_var_338)->find();
					} elseif ($_var_330["comment_mid"] == 11) {
						$_var_338["website_name"] = ["eq", $_var_330["rel_name"]];
						$_var_340 = model("Website")->where($_var_338)->find();
					}
					$_var_341 = $_var_340[mac_get_mid_code($_var_330["comment_mid"]) . "_id"];
				} else {
					$_var_341 = $_var_330["rel_id"];
				}
				if (empty($_var_341)) {
					$_var_332 = lang("model/collect/not_found_rel_data");
				} else {
					$_var_330["comment_rid"] = $_var_341;
					$_var_342 = false;
					if (!empty($_var_337)) {
						$_var_337["comment_rid"] = $_var_341;
						$_var_342 = model("Comment")->where($_var_337)->find();
					}
					if (!$_var_342) {
						$_var_333 = isset($_var_334["msg"]) ? $_var_334["msg"] : "";
						$_var_343 = model("Comment")->insert($_var_330);
						if ($_var_343 === false) {
						}
						$_var_331 = "green";
						$_var_332 = lang("model/collect/add_ok");
					} else {
						if (empty($_var_324["uprule"])) {
							$_var_332 = lang("model/collect/uprule_empty");
						} else {
							$_var_344 = true;
							if ($_var_344) {
								$_var_345 = [];
								if (strpos("," . $_var_324["uprule"], "a") !== false && !empty($_var_330["comment_time"]) && $_var_330["comment_time"] != $_var_342["comment_time"]) {
									$_var_345["comment_time"] = $_var_330["comment_time"];
								}
								if (count($_var_345) > 0) {
									$_var_345["comment_time"] = time();
									$_var_337 = [];
									$_var_337["comment_id"] = $_var_342["comment_id"];
									$_var_343 = model("Comment")->where($_var_337)->update($_var_345);
									$_var_331 = "green";
									if ($_var_343 === false) {
									}
								} else {
									$_var_332 = lang("model/collect/not_need_update");
								}
							}
						}
					}
				}
			}
			if ($show == 1) {
				mac_echo($_var_329 + 1 . $_var_330["comment_content"] . "<font color=" . $_var_331 . ">" . $_var_332 . "</font>" . $_var_333 . "");
			} else {
				return ["code" => $_var_331 == "red" ? 1001 : 1, "msg" => $_var_330["comment_content"] . " " . $_var_332];
			}
		}
		$_var_346 = $GLOBALS["config"]["app"]["cache_flag"] . "_" . "collect_break_comment";
		if (ENTRANCE == "api") {
			Cache::rm($_var_346);
			if ($data["page"]["page"] < $data["page"]["pagecount"]) {
				$param["page"] = intval($data["page"]["page"]) + 1;
				$_var_343 = $this->role($param);
				if ($_var_343["code"] > 1) {
					return $this->error($_var_343["msg"]);
				}
				$this->actor_data($param, $_var_343);
			}
			mac_echo(lang("model/collect/is_over"));
			die;
		}
		if (empty($GLOBALS["config"]["app"]["collect_timespan"])) {
			$GLOBALS["config"]["app"]["collect_timespan"] = 3;
		}
		if ($show == 1) {
			if ($param["ac"] == "cjsel") {
				Cache::rm($_var_346);
				mac_echo(lang("model/collect/is_over"));
				unset($param["ids"]);
				$param["ac"] = "list";
				$_var_347 = url("api") . "?" . http_build_query($param);
				$_var_348 = $_SERVER["HTTP_REFERER"];
				if (!empty($_var_348)) {
					$_var_347 = $_var_348;
				}
				mac_jump($_var_347, $GLOBALS["config"]["app"]["collect_timespan"]);
			} else {
				if ($data["page"]["page"] >= $data["page"]["pagecount"]) {
					Cache::rm($_var_346);
					mac_echo(lang("model/collect/is_over"));
					unset($param["page"]);
					$param["ac"] = "list";
					$_var_347 = url("api") . "?" . http_build_query($param);
					mac_jump($_var_347, $GLOBALS["config"]["app"]["collect_timespan"]);
				} else {
					$param["page"] = intval($data["page"]["page"]) + 1;
					$_var_347 = url("api") . "?" . http_build_query($param);
					mac_jump($_var_347, $GLOBALS["config"]["app"]["collect_timespan"]);
				}
			}
		}
	}
	private function checkCjUrl($_var_349)
	{
		$_var_350 = parse_url($_var_349);
		if (empty($_var_350["host"]) || in_array($_var_350["host"], ["127.0.0.1", "localhost"])) {
			return ["code" => 1001, "msg" => lang("model/collect/cjurl_err") . ": " . $_var_349];
		}
		return ["code" => 1];
	}
}