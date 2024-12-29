<?php	
error_reporting(0);
		//这个文件是默认打开tongji.php读取的数据的文件 如果你看完tongjis.php这个文件你就会发现我的代码简单了，先去看tongjis.php的注释吧
        $Sogouspiders = str_replace('\\','/',__DIR__).'/tongji/Sogou/';
        $baiduspiders = str_replace('\\','/',__DIR__).'/tongji/Baiduspider/';
        $Googlebots = str_replace('\\','/',__DIR__).'/tongji/Googlebot/';
        $liuSpiders = str_replace('\\','/',__DIR__).'/tongji/360Spider/';
        $Yisouspiders = str_replace('\\','/',__DIR__).'/tongji/Yisouspider/';
        $Bytespiders = str_replace('\\','/',__DIR__).'/tongji/Bytespider/';
		$time=date('Ymd' );
		$Sogouspiders = str_replace('\\','/',__DIR__).'/tongji/Sogou/';
		$baiduspiders = str_replace('\\','/',__DIR__).'/tongji/Baiduspider/';
		$Googlebots = str_replace('\\','/',__DIR__).'/tongji/Googlebot/';
		$liuSpiders = str_replace('\\','/',__DIR__).'/tongji/360Spider/';
		$Yisouspiders = str_replace('\\','/',__DIR__).'/tongji/Yisouspider/';
		$Bytespiders = str_replace('\\','/',__DIR__).'/tongji/Bytespider/';
		$daytimes=date('Ymd',$time);
		$times=$time-86400*10;
		$times=date('Ymd',$times);
		$t=[];
		for($i=0;$i<=10;$i++){
		$t[]=date("Ymd",strtotime("-$i day"));	  
							 }
		$heji=[];
		foreach($t as $k=>$v){
		@$Sogouspiderss = file_get_contents($Sogouspiders.$v.'.log');
		@$baiduspiderss = file_get_contents($baiduspiders.$v.'.log');
		@$Googlebotss =file_get_contents($Googlebots.$v.'.log');
		@$liuSpiderss = file_get_contents($liuSpiders.$v.'.log');
		@$Yisouspiderss = file_get_contents($Yisouspiders.$v.'.log');
		@$Bytespiderss= file_get_contents($Bytespiders.$v.'.log');
		$Sogouspidersss =preg_match_all('/\n/',$Sogouspiderss,$Sogouspidersss);
		$baiduspidersss =preg_match_all('/\n/',$baiduspiderss,$baiduspidersss);
		$Googlebotsss =preg_match_all('/\n/',$Googlebotss,$Googlebotsss);
		$liuSpidersss =preg_match_all('/\n/',$liuSpiderss,$liuSpidersss);
		$Yisouspidersss =preg_match_all('/\n/',$Yisouspiderss,$Yisouspidersss);
		$Bytespidersss =preg_match_all('/\n/',$Bytespiderss,$Bytespidersss);
		$heji[]=$Sogouspidersss+$baiduspidersss +$liuSpidersss +$Googlebotsss +$Yisouspidersss +$Bytespidersss ;	
							}
		$t = array_reverse($t);
		$heji=array_reverse($heji);
		$categoriesc=implode(',', $t);
		$countc=array_sum($heji);
		$seriesc=implode(',', $heji);			
		$daytime=date("Ymd",strtotime($time));
		@$Sogouspidersa = file_get_contents($Sogouspiders.$daytime.'.log');
		@$baiduspidersa = file_get_contents($baiduspiders.$daytime.'.log');
		@$Googlebotss =file_get_contents($Googlebots.$daytime.'.log');
		@$liuSpidersa = file_get_contents($liuSpiders.$daytime.'.log');
		@$Yisouspidersa = file_get_contents($Yisouspiders.$daytime.'.log');
		@$Bytespidersa= file_get_contents($Bytespiders.$daytime.'.log');
		$Sogouspidersa =preg_match_all('/\n/',$Sogouspidersa,$Sogouspidersa);
		$baiduspidersa =preg_match_all('/\n/',$baiduspidersa,$baiduspidersa);
		$Googlebotsa =preg_match_all('/\n/',$Googlebotss,$Googlebotsa);
		$liuSpidersa =preg_match_all('/\n/',$liuSpidersa,$liuSpidersa);
		$Yisouspidersa =preg_match_all('/\n/',$Yisouspidersa,$Yisouspidersa);
		$Bytespidersa =preg_match_all('/\n/',$Bytespidersa,$Bytespidersa);
		$hotime=date("Y-m-d",strtotime($time));
		$hotimes=date("Ymd",strtotime($time));
		$hourtime = strtotime($hotime);
		$hour = str_replace('\\','/',__DIR__).'/tongji/hour/';
		for($i=0;$i<=23;$i++){
			$hs[]=date("H",strtotime("+$i hour",$hourtime));	  
							 }
		$hourss=[];
		foreach($hs as $k=>$v){
		@$hours = file_get_contents($hour.$hotimes.'/'.$v.'.log');
		$hourss[$v]=strlen($hours);
							  }
		$countb=array_sum($hourss);
		$categoriesb=implode(',', $hourss);
		$a= "<div id='chart_pie_day' style='width: 30%; height: 300px; margin: 10px 0;float:left;position: relative;'></div>
		<script type='text/javascript'>
		$('#chart_pie_day').highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false
			},
			credits:{
				enabled:false
			},
			title: {
				text: '今日访问比率',
				align:'left',
			},
			legend: {
				layout: 'vertical',
				backgroundColor: '#FFFFFF',
				verticalAlign: 'middle',
				align: 'right'
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'	
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: false
					},
					showInLegend: true
				}
			},
			series: [{
				type: 'pie',
				name: '比例',
				data: [
				//数据
										{
						name: '百度 ".$baiduspidersa."',
						y: 0,
						sliced: true,
						selected: true
					}
					,								['Google ".$Googlebotsa."',".$Googlebotsa."],								['360蜘蛛 ".$liuSpidersa."',".$liuSpidersa."],								['搜狗 ".$Sogouspidersa."',".$Sogouspidersa."],								['神马 ".$Yisouspidersa."',".$Yisouspidersa."],								['今日头条 ".$Bytespidersa."',".$Bytespidersa."],								['其他 0',0]				]
			}]
		});
		</script>";
		$b= "<div id='chart_line_day' style='width:69%;height: 300px;margin: 10px 0;float:right;position: relative;'></div>
		<script type='text/javascript'>
		$('#chart_line_day').highcharts({
			chart: {
				type: 'line'
			},
			credits:{
				enabled:false
			},
			title: {
				text: '今日蜘蛛时段走势图'
			},
			subtitle: {
				text: '今日蜘蛛访问数量：".$countb."'
			},
			xAxis: {
				categories: ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
			},
			yAxis: {
				title: {
					text: ''
				}
			},
			plotOptions: {
				line: {
					dataLabels: {
						enabled: true
					},
					enableMouseTracking: false
				}
			},
			series: [
				{
					name: '蜘蛛访问次数（次/小时）',
					data: [".$categoriesb."]
				}
			]
		});
		</script>";
		$c="<div id='chart_line_week' style='min-width: 310px; height: 300px; margin: 10px auto;position: relative;'></div>
		<script type='text/javascript'>
		$('#chart_line_week').highcharts({
			chart: {
				type: 'line',
				style: {
						'border-top':'1px solid #40AA52'
					}
			},
			credits:{
				enabled:false
			},
			title: {
				text: '近10日蜘蛛走势图'
			},
			subtitle: {
				text: '近10日蜘蛛数量：".$countc."'
			},
			xAxis: {
							categories: [".$categoriesc."]
					},
			yAxis: {
				title: {
					text: ''
				}
			},
			legend: {
				layout: 'vertical',
				backgroundColor: '#FFFFFF',
				verticalAlign: 'middle',
				align: 'right'
			},
			plotOptions: {
				line: {
					dataLabels: {
						enabled: true
					},
					enableMouseTracking: false
				}
			},
			series: [ { name: '全部', data: [".$seriesc."] },  ]
		});
		</script>";	
		@$Sogou = file_get_contents($Sogouspiders.$time.'.log');
		@$baidu = file_get_contents($baiduspiders.$time.'.log');
        @$Google =file_get_contents($Googlebots.$time.'.log');
        @$liu = file_get_contents($liuSpiders.$time.'.log');
        @$Yisou = file_get_contents($Yisouspiders.$time.'.log');
        @$Byte = file_get_contents($Bytespiders.$time.'.log');
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
		$arr=[];
		$dclinks=array_merge($exp,$exp1,$exp2,$exp3,$exp4,$exp5);
		foreach($dclinks as $k=>$v){
		$arr[$k]=explode("--",$v);
  			  					   }
		$ctime_str = array();
		foreach($arr as $key=>$v){
		$arr[$key]['ctime_str'] = strtotime($v['0']);
		$ctime_str[] = $arr[$key]['ctime_str'];
								}
		array_multisort($ctime_str,SORT_DESC,$arr); 
		//蜘蛛统计第一页显示51-1行

		@$list=array_slice($arr,$pag,51,true );
		$counts = count($arr);
		$phpfile = '5000.php';
		$getpageinfo = page(0,$counts,$phpfile,'zong',$time);
		@$paged=$getpageinfo['pagecode'];
		function page($page,$total,$phpfile,$spider,$time,$pagesize=50,$pagelen=9){ 
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
		$pagecode.="<a class=\"next\" href=".$phpfile."?".$spider."--".$time."--p--".($page+1).">下一页</a>";
       $pagecode.="<a href=".$phpfile."?".$spider."--".$time."--p--".$pages.">末页</a>";
						} 
		return array('pagecode'=>$pagecode,'sqllimit'=>' limit '.$offset.','.$pagesize); 
}				
?>
