<?php
 error_reporting(0);
//------------------这个文件是输出详细蜘蛛访问地址的文件 我把它当成D区代码-------------------------------
		$req=$_SERVER['QUERY_STRING'];
		$req=explode("--",$req);
		$zhizhutimes=$req[1];
		$spider=$req[0];
		$page=$req[3];
		$Sogouspiders = str_replace('\\','/',__DIR__).'/tongji/Sogou/';
		$baiduspiders = str_replace('\\','/',__DIR__).'/tongji/Baiduspider/';
		$Googlebots = str_replace('\\','/',__DIR__).'/tongji/Googlebot/';
		$liuSpiders = str_replace('\\','/',__DIR__).'/tongji/360Spider/';
		$Yisouspiders = str_replace('\\','/',__DIR__).'/tongji/Yisouspider/';
		$Bytespiders = str_replace('\\','/',__DIR__).'/tongji/Bytespider/';
		//@符控制不输出错误，没有数据file_get_contents会报错 给php开发者一个大赞
		@$Sogouspiderss = file_get_contents($Sogouspiders.$zhizhutimes.'.log');
		@$baiduspiderss = file_get_contents($baiduspiders.$zhizhutimes.'.log');
		@$Googlebotss =file_get_contents($Googlebots.$zhizhutimes.'.log');
		@$liuSpiderss = file_get_contents($liuSpiders.$zhizhutimes.'.log');
		@$Yisouspiderss = file_get_contents($Yisouspiders.$zhizhutimes.'.log');
		@$Bytespiderss= file_get_contents($Bytespiders.$zhizhutimes.'.log');
		$Sogouspidersss =preg_match_all('/\n/',$Sogouspiderss,$Sogouspidersss);
		$baiduspidersss =preg_match_all('/\n/',$baiduspiderss,$baiduspidersss);
		$Googlebotsss =preg_match_all('/\n/',$Googlebotss,$Googlebotsss);
		$liuSpidersss =preg_match_all('/\n/',$liuSpiderss,$liuSpidersss);
		$Yisouspidersss =preg_match_all('/\n/',$Yisouspiderss,$Yisouspidersss);
		$Bytespidersss =preg_match_all('/\n/',$Bytespiderss,$Bytespidersss);
		//每个蜘蛛的总数统计 和全部蜘蛛的统计
		$zongs=$Sogouspidersss+$baiduspidersss+$Googlebotsss+$liuSpidersss+$Yisouspidersss+$Bytespidersss ;
		$Sogouspidersa +=$Sogouspidersss;
		$baiduspidersa +=$baiduspidersss;
		$Googlebotsa +=$Googlebotsss;
		$liuSpidersa +=$liuSpidersss;
		$Yisouspidersa +=$Yisouspidersss;
		$Bytespidersa +=$Bytespidersss;
		//构造数据输出给前端的 133行
		$scount=[
		[
		'key'=>'null',
		'name'=>'全部',
		'count'=>$zongs,
		'url'=>"5000.php?zong--$zhizhutimes--p--1"
		],
		[
		'key'=>'Baiduspider',
		'name'=>'百度',
		'count'=>$baiduspidersa,
		'url'=>"5000.php?Baiduspider--$zhizhutimes--p--1"
		],
		[
		'key'=>'googlebot',
		'name'=>'Google',
		'count'=>$Googlebotsa,
		'url'=>"5000.php?Googlebot--$zhizhutimes--p--1"
		],
		[
		'key'=>'Sogou',
		'name'=>'搜狗',
		'count'=>$Sogouspidersa,
		'url'=>"5000.php?Sogou--$zhizhutimes--p--1"
		],
		[
		'key'=>'360Spider',
		'name'=>'360蜘蛛',
		'count'=>$liuSpidersa,
		'url'=>"5000.php?360Spider--$zhizhutimes--p--1"
		],
		[
		'key'=>'Yisouspider',
		'name'=>'神马',
		'count'=>$Yisouspidersa,
		'url'=>"5000.php?Yisouspider--$zhizhutimes--p--1"
		],
		[
		'key'=>'Bytespider',
		'name'=>'今日头条',
		'count'=>$Bytespidersa,
		'url'=>"5000.php?Bytespider--$zhizhutimes--p--1"
		]
		];
//-------------------这里是判断如果查的是全部的蜘蛛代码 $spider是前端传进来的数据 变量在第6行赋值----------------
		if($spider==='zong'){
		@$Sogou = file_get_contents($Sogouspiders.$zhizhutimes.'.log');
		@$baidu = file_get_contents($baiduspiders.$zhizhutimes.'.log');
        @$Google =file_get_contents($Googlebots.$zhizhutimes.'.log');
        @$liu = file_get_contents($liuSpiders.$zhizhutimes.'.log');
        @$Yisou = file_get_contents($Yisouspiders.$zhizhutimes.'.log');
        @$Byte = file_get_contents($Bytespiders.$zhizhutimes.'.log');
		//按照行分割成数组 array_filter删除空数组 至于为什么会有空的数组 我在统计蜘蛛的代码里加了每次统计插入个\n
		$exp=explode("\n",$Sogou);
		$exp=array_filter($exp);
		$exp1 =explode("\n",$baidu);
		$exp1= array_filter($exp1);
		$exp2=explode("\n",$Google);
		$exp2=array_filter($exp2);
		$exp3 =explode("\n",$liu);
		$exp3= array_filter($exp3);
		$exp4=explode("\n",$Byte);
		$exp4=array_filter($exp4);
		$exp5 =explode("\n",$Yisou);
		$exp5= array_filter($exp5);
		//定义一个空数组切割到的数据保存在这个数组里
		$arr=[];
		//array_merge数组合并所有的蜘蛛
		$dclinks=array_merge($exp,$exp1,$exp2,$exp3,$exp4,$exp5);
		//循环按照--切割 具体看统计文件夹的蜘蛛文本
		foreach($dclinks as $k=>$v){
			$arr[$k]=explode("--",$v);
									}
		//定义一个空数组做时间的排序处理
		$ctime_str = array();
		//循环把数组里面再加个时间戳--理解难度；10星
		foreach($arr as $key=>$v){
		$arr[$key]['ctime_str'] = strtotime($v['0']);
		$ctime_str[] = $arr[$key]['ctime_str'];
								}
		//所有的数据按照时间排序(如果你看完我的所有代码注释你会发现这是我第二种处理时间排序方式)
		array_multisort($ctime_str,SORT_DESC,$arr); 
		//$pag:第一页是（1-0)*10=0 第二页是(2-1)*10=20
		$pag=($page-1)*10;
		//array_slice函数是从数据的下标起始：$page 结束位置:16，取数据。16代表是16条数据
		$list=array_slice($arr,$pag,10,true );
		//$counts总数 分页用到
		$counts = count($arr);
		$lists=[];
		//循环array_slice函数得到的数据
		foreach($list as $k=>$v){		
		$lists[]=[
		'id'=>"$k",
		'name'=>"$v[2]", 
		'ip'=>"$v[1]", 
		'city'=>"<font color=\"green\">中国</font>",
		'time'=>"<font color=red>$v[0]</font>",
		'typename'=> "文章新闻",
		'url'=>"<a target=_blank title='打开此链接' href='$v[3]'>$v[3]</a>"
				];
						}
		//$counts所有蜘蛛的总数 分页处理用到				
		$counts = count($arr);
		if($arr){
		//分页用到$phpfile，具体看函数里面是调用来干嘛的
		$phpfile = '5000.php';
		//调用分页函数进行分页-分页函数在最后面的197行
		$getpageinfo = page($page,$counts,$phpfile,$spider,$req[1]);
		//构造一下要json的数据
		$a=['list'=>$lists,'pages'=>$getpageinfo['pagecode'],'spider'=>$spider,'total'=>$counts,'scount'=>$scount];
		//输出给前段
		exit(json_encode($a));
			    }
		else{
		$listsx[]=[
		'id'=>"---",
		'name'=>"---", 
		'ip'=>"---", 
		'city'=>"---",
		'time'=>"---",
		'typename'=> "---",
		'url'=>'<font color="red">错误，未生成该蜘蛛的记录文件！请等待~</font>'
				  ];
		$xxnigexx=['list'=>$listsx,'pages'=>'','spider'=>$spider,'total'=>'0','scount'=>$scount];
		exit(json_encode($xxnigexx));
			}
						  }
//-----------------------每个蜘蛛的详细数据-----------------------------------
		$cache_path = str_replace('\\','/',__DIR__).'/tongji/'.$spider.'/';	
		if(is_file($cache_path.$zhizhutimes.'.log'))
        {
        $html = file_get_contents($cache_path.$zhizhutimes.'.log');
        $jinri =preg_match_all('/\n/',$html,$jinri);
		$dclink=explode("\n",$html);		
		$dclinks=[];
        //删除空数组		
		unset($dclink[$jinri]);	
		//数组倒叙
		$dclinks=array_reverse($dclink);
		//循环按照---切割
		foreach($dclinks as $k=>$v){
			$dclinkss[$k]=explode("--",$v);
									}
		//$pag:第一页是（1-0)*10=0 第二页是(2-1)*10=20
		$pag=($page-1)*10;
		//array_slice函数是从数据的下标起始：$page 结束位置:16，取数据。16代表是16条数据
		$list=array_slice($dclinkss,$pag,16,true );
		//$counts每个蜘蛛的总数 分页用到
		$counts = count($dclinkss);
		$lists=[];
		//循环array_slice函数得到的数据
		foreach($list as $k=>$v){		
		$lists[]=[
		'id'=>"$k",
		'name'=>"$v[2]", 
		'ip'=>"$v[1]", 
		'city'=>"<font color=\"green\">中国</font>",
		'time'=>"<font color=red>$v[0]</font>",
		'typename'=> "文章新闻",
		'url'=>"<a target=_blank title='打开此链接' href='$v[3]'>$v[3]</a>"
				];
								}
	//分页用到$phpfile就在这里定义一下吧
	$phpfile = '5000.php';
	//调用分页函数进行分页-分页函数在197行
	$getpageinfo = page($page,$counts,$phpfile,$spider,$req[1]);
	//构造一下要json的数据
	$a=['list'=>$lists,'pages'=>$getpageinfo['pagecode'],'spider'=>$spider,'total'=>$counts,'scount'=>$scount];
	exit(json_encode($a));		
	}else{
	$lists[]=[
	'id'=>"---",
	'name'=>"---", 
	'ip'=>"---", 
	'city'=>"---",
	'time'=>"---",
	'typename'=> "---",
	'url'=>'<font color="red">错误，未生成该蜘蛛的记录文件！请等待~</font>'
	];
	$a=['list'=>$lists,'pages'=>'','spider'=>$spider,'total'=>'0','scount'=>$scount];
	exit(json_encode($a));
		 }
//分页函数-具体自己研究，什么都注释我不想干这事（留个坑） 哈哈
	function page($page,$total,$phpfile,$spider,$time,$pagesize=9,$pagelen=9){ 
    $pagecode = '';
    $page = intval($page);
    $total = intval($total);
    if(!$total) return array();
    $pages = ceil($total/$pagesize);
    if($page<1) $page = 1; 
    if($page>$pages) $page = $pages; 
    $offset = $pagesize*($page-1); 
    $init = 1;
    $max = $pages;
    $pagelen = ($pagelen%2)?$pagelen:$pagelen+1;
    $pageoffset = ($pagelen-1)/2;
    $pagecode=""; 
    if($page!=1){ 
    $pagecode.="<a href=".$phpfile."?".$spider."--".$time."--p--1>首页</a>";//第一页 
    $pagecode.="<a class=\"pre\" href=".$phpfile."?".$spider."--".$time."--p--".($page-1).">上一页</a>";//上一页 "<a href=\"{$phpfile}?page=".($page-1)."\">上一页</a>"
				} 
    if($pages>$pagelen){ 
    if($page<=$pageoffset){ 
    $init=1; 
    $max = $pagelen; 
						  }
	else{
    if($page+$pageoffset>=$pages+1){ 
    $init = $pages-$pagelen+1; 
									}
	else{ 
    $init = $page-$pageoffset; 
     $max = $page+$pageoffset; 
         } 
        } 
						} 
    for($i=$init;$i<=$max;$i++){ 
    if($i==$page){ 
	$pagecode.="<a class=\"current\">$i</a>";
				} 
	else { 
    $pagecode.="<a href=".$phpfile."?".$spider."--".$time."--p--".$i.">$i</a>";
        } 
								} 
    if($page!=$pages){ 
    $pagecode.="<a class=\"next\" href=".$phpfile."?".$spider."--".$time."--p--".($page+1).">下一页</a>";//下一页 
    $pagecode.="<a href=".$phpfile."?".$spider."--".$time."--p--".$pages.">末页</a>";//最后一页  $pagecode.="<a href=".$phpfile."?".$spider."--".$time."--p--".$pages.">末页</a>";
					} 
    return array('pagecode'=>$pagecode,'sqllimit'=>' limit '.$offset.','.$pagesize); 
}		
