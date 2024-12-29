<?php
//error_reporting(0);
//-------------------------------三个图表的全部异步代码 以a（）,b（）,c（）函数区分-------------------------------------------------
//str_replace：替换函数， __DIR__：指向当前执行的PHP脚本所在的目录 具体百度或者打印一下。
$Sogouspiders = str_replace('\\', '/', __DIR__) . '/tongji/Sogou/';
$baiduspiders = str_replace('\\', '/', __DIR__) . '/tongji/Baiduspider/';
$Googlebots   = str_replace('\\', '/', __DIR__) . '/tongji/Googlebot/';
$liuSpiders   = str_replace('\\', '/', __DIR__) . '/tongji/360Spider/';
$Yisouspiders = str_replace('\\', '/', __DIR__) . '/tongji/Yisouspider/';
$Bytespiders  = str_replace('\\', '/', __DIR__) . '/tongji/Bytespider/';

//今天的日期格式是20200703
$time = date('Ymd');
//a区代码 也就是第一个图标 从上往下读理解不了就自行打印打开前段一个个点击F12
function a($req)
{
    $Sogouspiders = str_replace('\\', '/', __DIR__) . '/tongji/Sogou/';
    $baiduspiders = str_replace('\\', '/', __DIR__) . '/tongji/Baiduspider/';
    $Googlebots   = str_replace('\\', '/', __DIR__) . '/tongji/Googlebot/';
    $liuSpiders   = str_replace('\\', '/', __DIR__) . '/tongji/360Spider/';
    $Yisouspiders = str_replace('\\', '/', __DIR__) . '/tongji/Yisouspider/';
    $Bytespiders  = str_replace('\\', '/', __DIR__) . '/tongji/Bytespider/';
    //$time获取今天是时间戳
    $time = time();
    //$times这里是方面查询10天 30天 365天 前端传入当天的会是当前的时间戳减0 昨天的是减86400*1 前天是*2以此类催 看不懂就好好学一下数学的运算规则
    $times = $time - 86400 * $req;
    //命名一个时间的空数组 具体是怎么回事 在31行打印一下$t看看数据
    $t = [];
    for ($i = 0; $i <= $req; $i++) {
        $t[] = date("Ymd", strtotime("-$i day"));
    }
    if ($req === '1') {
        unset($t[0]);
    }
    $heji = [];
    //循环$t里面的时间根据时间去读取数据
    foreach ($t as $k => $v) {
        @$Sogouspiderss = file_get_contents($Sogouspiders . $v . '.log');
        @$baiduspiderss = file_get_contents($baiduspiders . $v . '.log');
        @$Googlebotss = file_get_contents($Googlebots . $v . '.log');
        @$liuSpiderss = file_get_contents($liuSpiders . $v . '.log');
        @$Yisouspiderss = file_get_contents($Yisouspiders . $v . '.log');
        @$Bytespiderss = file_get_contents($Bytespiders . $v . '.log');
        $Sogouspidersss = preg_match_all('/\n/', $Sogouspiderss, $Sogouspidersss);
        $baiduspidersss = preg_match_all('/\n/', $baiduspiderss, $baiduspidersss);
        $Googlebotsss   = preg_match_all('/\n/', $Googlebotss, $Googlebotsss);
        $liuSpidersss   = preg_match_all('/\n/', $liuSpiderss, $liuSpidersss);
        $Yisouspidersss = preg_match_all('/\n/', $Yisouspiderss, $Yisouspidersss);
        $Bytespidersss  = preg_match_all('/\n/', $Bytespiderss, $Bytespidersss);
        //每次循环都加上上一次循环的结果
        $Sogouspidersa += $Sogouspidersss;
        $baiduspidersa += $baiduspidersss;
        $Googlebotsa   += $Googlebotsss;
        $liuSpidersa   += $liuSpidersss;
        $Yisouspidersa += $Yisouspidersss;
        $Bytespidersa  += $Bytespidersss;
    }

    //这里构造前端需要的数据
    $text = "今日访问比率";
    if ($req == '1') {
        $text = '昨日访问比率';
    }
    if ($req == '7') {
        $text = '近七日内访问比率';
    }
    if ($req == '30') {
        $text = '近三十日内访问比率';
    }
    if ($req == '365') {
        $text = '近一年内访问比率';
    }
    $a = "<div id=\"chart_pie_day\" style=\"width: 30%; height: 300px; margin: 10px 0;float:left;position: relative;\"></div>
	<script type=\"text/javascript\">
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
			text: '" . $text . "',
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
					name: '百度 " . $baiduspidersa . "',
					y: 0,
					sliced: true,
					selected: true
				}
				,								['Google " . $Googlebotsa . "'," . $Googlebotsa . "],								['360蜘蛛 " . $liuSpidersa . "'," . $liuSpidersa . "],								['搜狗 " . $Sogouspidersa . "'," . $Sogouspidersa . "],								['神马 " . $Yisouspidersa . "'," . $Yisouspidersa . "],								['今日头条 " . $Bytespidersa . "'," . $Bytespidersa . "],								['其他 0',0]				]
		}]
	});
	</script>";
    //json输出
    exit(json_encode(array('html' => $a, 'msg' => '1')));
}

//b是第二个图表统计 for循环时间处理每小时的时间值得注意 因为这个B获取的数据是小时的数据跟ac函数不同的 其他原理跟上面一样，
function b($req)
{
    $time     = date('Ymd');
    $hotime   = date("Y-m-d", strtotime($time));
    $hotimes  = date("Ymd", strtotime("-$req day"));
    $hourtime = strtotime($hotime);
    $hour     = str_replace('\\', '/', __DIR__) . '/tongji/hour/';
    for ($i = 0; $i <= 23; $i++) {
        $hs[] = date("H", strtotime("+$i hour", $hourtime));
    }
    $hourss = [];
    foreach ($hs as $k => $v) {
        @$hours = file_get_contents($hour . $hotimes . '/' . $v . '.log');
        $hourss[$v] = strlen($hours);
    }
    //数组的所有值的和统计	--蜘蛛访问总数量
    $count       = array_sum($hourss);
    $categoriesb = implode(',', $hourss);
    $text        = '今日';
    if ($req == '1') {
        $text = '昨日';
    }
    if ($req == '2') {
        $text = '前日';
    }
    $b = "<div id='chart_line_day' style='width:69%;height: 300px;margin: 10px 0;float:right;position: relative;'></div>
	<script type='text/javascript'>
	$('#chart_line_day').highcharts({
		chart: {
			type: 'line'
		},
		credits:{
			enabled:false
		},
		title: {
			text: '" . $text . "蜘蛛时段走势图'
		},
		subtitle: {
			text: '" . $text . "蜘蛛访问数量：" . $count . "'
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
				data: [" . $categoriesb . "]
			}
		]
	});
	</script>";
    exit(json_encode(array('html' => $b, 'msg' => '1')));
}

//c是最后一个图表数据，注释看到这里就不介绍下面的了，原理一样的
function c($req)
{
    if ($req === '') {
        $req = 10;
    }
    $Sogouspiders = str_replace('\\', '/', __DIR__) . '/tongji/Sogou/';
    $baiduspiders = str_replace('\\', '/', __DIR__) . '/tongji/Baiduspider/';
    $Googlebots   = str_replace('\\', '/', __DIR__) . '/tongji/Googlebot/';
    $liuSpiders   = str_replace('\\', '/', __DIR__) . '/tongji/360Spider/';
    $Yisouspiders = str_replace('\\', '/', __DIR__) . '/tongji/Yisouspider/';
    $Bytespiders  = str_replace('\\', '/', __DIR__) . '/tongji/Bytespider/';
    $daytimes     = date('Ymd', $time);
    $times        = $time - 86400 * $req;
    $times        = date('Ymd', $times);
    $t            = [];
    for ($i = 0; $i <= $req; $i++) {
        $t[] = date("Ymd", strtotime("-$i day"));
    }
    $heji = [];
    foreach ($t as $k => $v) {
        @$Sogouspiderss = file_get_contents($Sogouspiders . $v . '.log');
        @$baiduspiderss = file_get_contents($baiduspiders . $v . '.log');
        @$Googlebotss = file_get_contents($Googlebots . $v . '.log');
        @$liuSpiderss = file_get_contents($liuSpiders . $v . '.log');
        @$Yisouspiderss = file_get_contents($Yisouspiders . $v . '.log');
        @$Bytespiderss = file_get_contents($Bytespiders . $v . '.log');
        $Sogouspidersss = preg_match_all('/\n/', $Sogouspiderss, $Sogouspidersss);
        $baiduspidersss = preg_match_all('/\n/', $baiduspiderss, $baiduspidersss);
        $Googlebotsss   = preg_match_all('/\n/', $Googlebotss, $Googlebotsss);
        $liuSpidersss   = preg_match_all('/\n/', $liuSpiderss, $liuSpidersss);
        $Yisouspidersss = preg_match_all('/\n/', $Yisouspiderss, $Yisouspidersss);
        $Bytespidersss  = preg_match_all('/\n/', $Bytespiderss, $Bytespidersss);
        $heji[]         = $Sogouspidersss + $baiduspidersss + $liuSpidersss + $Googlebotsss + $Yisouspidersss + $Bytespidersss;
    }
    $t           = array_reverse($t);
    $heji        = array_reverse($heji);
    $categoriesc = implode(',', $t);
    $count       = array_sum($heji);
    $seriesc     = implode(',', $heji);
    $c           = "<div id='chart_line_week' style='min-width: 310px; height: 300px; margin: 10px auto;position: relative;'></div>
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
		text: '近" . $req . "日蜘蛛走势图'
				},
		subtitle: {
		text: '近" . $req . "日蜘蛛数量：" . $count . "'
				  },
		xAxis: {
		categories: [" . $categoriesc . "]
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
		series: [ { name: '全部', data: [" . $seriesc . "] },  ]
				});
				</script>";
    exit(json_encode(array('html' => $c, 'msg' => '1')));
}

//接收前端3个图表展示点击的异步地址
$req = $_SERVER['QUERY_STRING'];
//进行切割调用函数:a(),b(),c()和相关的参数 具体可以在这里加入print_r($req)；exit();然后前端点击3个图表(我把它区分为a b c 三个区域)的F12查看;
$req    = explode("-", $req);
$req[0] = strtolower(strip_tags(trim($req[0])));
$fn     = in_array($req[0], ['a', 'b', 'c']) ? $req[0] : 'a';
$param  = empty($req[1]) ? 0 : (int)$req[1];
echo $fn($param);
?>
