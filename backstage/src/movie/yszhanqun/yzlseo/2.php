<?php
//-------------------------这是唯一进行蜘蛛统计的核心代码-----------------------------------------------------------		
		//$hour_path解释为 要进行分每小时存蜘蛛b区图表显示用到
		$hour_path=str_replace('\\','/',__DIR__).'/tongji/hour/';
		//1.1$cache_path1- $cache_path6看tongji/后面是啥就是啥--1.2//str_replace：替换函数， __DIR__：指向当前执行的PHP脚本所在的目录 具体百度或者打印一下。
        $cache_path1 = str_replace('\\','/',__DIR__).'/tongji/Sogou/';
		$cache_path2 = str_replace('\\','/',__DIR__).'/tongji/Baiduspider/';
		$cache_path3 = str_replace('\\','/',__DIR__).'/tongji/360Spider/';
		$cache_path4 = str_replace('\\','/',__DIR__).'/tongji/Googlebot/';
		$cache_path5 = str_replace('\\','/',__DIR__).'/tongji/Yisouspider/';
		$cache_path6 = str_replace('\\','/',__DIR__).'/tongji/Bytespider/';
		//这里打印一下:print_r($_SERVER);就会明明白白了
        $url = $_SERVER['REQUEST_URI'];
        $url= str_replace("/","",$url);
        $key= $_SERVER["HTTP_USER_AGENT"];
		//preg_match查找一下有没有这个字符 
        $Sogouspider =preg_match('/Sogou/', $key, $Sogouspider);
        $baiduspider =preg_match('/Baiduspider/', $key, $baiduspider);
        $Googlebot =preg_match('/Googlebot/', $key, $Googlebot);
        $bingbot =preg_match('/bingbot/', $key, $bingbot);
        $MJ12bot =preg_match('/MJ12bot/', $key, $MJ12bot);
		$liulingSpider=preg_match('/360Spider/',$key, $liulingSpider);
		$Yisouspiders=preg_match('/Yisouspider/',$key, $Yisouspider);
		$Bytespider=preg_match('/Bytespider/', $key, $Yisouspider);
		//获取访问的Ip
		$ip = $_SERVER["REMOTE_ADDR"];
		//获取访问的页面地址
		$urls=$_SERVER[REQUEST_SCHEME].'://'.$_SERVER[SERVER_NAME].$_SERVER[SCRIPT_NAME];
		//获取当前访问的时间2020-07-03 20：26：00
		$zhizhutime=date('Y-m-d H:i:s' );
		//获取今天的日期20200703
		$zhizhutimes=date('Ymd');
		//当前的几点钟 H是0-24点
		$hour=date('H');
		//构造一下数据 具体是怎么样的打印一下吧 ---\n很关键！----不然后端处理搞不了的
		$sogou='搜狗';
		$gouzi="$zhizhutime--$ip--$sogou--$urls\n";
		$baidu='百度';
		$baidus="$zhizhutime--$ip--$baidu--$urls\n";
		$google='谷歌';
		$googles="$zhizhutime--$ip--$google--$urls\n";
		$liuling='360';
		$liulingSpiders="$zhizhutime--$ip--$liuling--$urls\n";
		$yisou='神马';
		$Yisouspiders="$zhizhutime--$ip--$yisou--$urls\n";
		$toutiao='今日头条';
		$Bytespiders="$zhizhutime--$ip--$toutiao--$urls\n";
		//$dir=/tongji/hour/
		$dir = $hour_path.$zhizhutimes;
		//三元判断一下有没有今天的每小时时间有没有 没有就创建一个 有就不创建
        is_dir($dir)?:mkdir($dir,0777,true);
		//判断有没有蜘蛛有没有访问 有就写入 看不懂就去百度站长诊断（在线蜘蛛模拟）抓一下这个页面的代码然后打印6-24行 打印看看是什么原理
		if($Sogouspider){
			file_put_contents($cache_path1."$zhizhutimes.log",$gouzi,FILE_APPEND);
			file_put_contents($hour_path.$zhizhutimes.'/'.$hour.".log",'1',FILE_APPEND);
			} 
		if($baiduspider){
			file_put_contents($cache_path2."$zhizhutimes.log",$baidus,FILE_APPEND);
			file_put_contents($hour_path.$zhizhutimes.'/'.$hour.".log",'1',FILE_APPEND);
			}
		if($Googlebot){
			file_put_contents($cache_path4."$zhizhutimes.log",$googles,FILE_APPEND);
			file_put_contents($hour_path.$zhizhutimes.'/'.$hour.".log",'1',FILE_APPEND);
			} 
		if($liulingSpider){
			file_put_contents($cache_path3."$zhizhutimes.log",$liulingSpiders,FILE_APPEND);
			file_put_contents($hour_path.$zhizhutimes.'/'.$hour.".log",'1',FILE_APPEND);
			}
		if($Yisouspider){
			file_put_contents($cache_path5."$zhizhutimes.log",$Yisouspiders,FILE_APPEND);
			file_put_contents($hour_path.$zhizhutimes.'/'.$hour.".log",'1',FILE_APPEND);
			}
		if($Bytespider){
			file_put_contents($cache_path6."$zhizhutimes.log",$Bytespiders,FILE_APPEND);
			file_put_contents($hour_path.$zhizhutimes.'/'.$hour.".log",'1',FILE_APPEND);
			}	

		
