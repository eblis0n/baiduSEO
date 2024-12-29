<?php


namespace app\index\controller;

if (time() > 1756537037) {
	define("XEND_PRO_SET1", 1);
	exit("//出错");
}
class Index extends Base
{
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$_var_0 = $this->label_fetch("index/index");
		$_var_0 = str_replace("<body", "\n<!-- 请勿将程序用于违法用途 -->\n<body", $_var_0);
		return $_var_0;
	}
}