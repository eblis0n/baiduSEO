<?php include('zong.php');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>蜘蛛统计</title>
<link href="static/js/skin/WdatePicker.css" rel="stylesheet" type="text/css" />
<link href="static/css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="static/js/jquery.js"></script>
<script type="text/javascript" src="static/js/highcharts.js"></script>
<script type="text/javascript" src="static/js/lingduseo.js"></script>
<script type="text/javascript" src="static/js/DatePicker/WdatePicker.js"></script>
</head>
<body class="body-main">
<ul id="admin_sub_title">
	<li class="sub"><a href="javascript:">访问记录</a></li>
	<li class="tips"><a href="deltj.php" target="_blank" onclick="recount();" style="color:red">（清除全部蜘蛛）</a></li>
</ul>
<div id="admin_right_b">
<?php  ?>
<div style="height: 300px;">
	<div style="position: relative;">
		<div id="line_tab" class="chart_tab" style="margin-left:31%;">
			<span class="cur" data="0">今日</span>
			<span data="1">昨日</span>
			<span data="2">前日</span>
		</div>
		<div id="chart_line_day_box" class="chart_box"><?php echo $b ?></div>
	</div>
	<div style="position: relative;text-align:right">
		<div id="pie_tab" class="chart_tab" style="text-align: right;width: 30%;left: -10px;">
			<span class="cur" data="0">今日</span>
			<span data="1">昨日</span>
			<span data="7">7日</span>
			<span data="30">30日</span>
			<span data="365">1年</span>
		</div>
		<div id="chart_pie_day_box" class="chart_box"><?php echo $a; ?></div>
	</div>
</div>
<div style="position: relative;">
	<div class="type_tab" id="week_type_tab">
		<span class="cur" data="">全部</span>
	</div>
	<span id="week_type" data=""></span>
	<div id="week_tab" class="chart_tab" style="left:160px;">
		<span data="10" class="cur">近10日</span>
		<span data="30">近30日</span>
		<span data="365">近1年</span>
	</div>
	<div id="chart_line_week_box" class="chart_box"><?php echo $c; ?></div>
</div>
<br>
<script type="text/javascript">
$(function () {
	$('#pie_tab span').click(function(){
		$(this).siblings().removeClass('cur').end().addClass('cur');
		var gurl='./tongjis.php?'+'a-'+$(this).attr('data');
		$('#chart_pie_day_box .highcharts-container').css({ opacity: 0.3 });
		$('#chart_pie_day').append('<div class="loading">加载中...</div>');
		$.ajax({
			url:gurl,
			success:function(data){
				var data=JSON.parse(data);
				$('#chart_pie_day_box .highcharts-container').html(data.html).css({ opacity:1 });
			}
		});
	});
	$.ajax({
		url:'./tongjis.php?',
		success:function(data){
			$('#chart_line_day_box .highcharts-container').html(data.html);
		}
	});
	$('#line_tab span').click(function(){
		$(this).siblings().removeClass('cur').end().addClass('cur');
		var gurl='./tongjis.php?'+'b-'+$(this).attr('data');
		$('#chart_line_day_box .highcharts-container').css({ opacity: 0.3 });
		$('#chart_line_day').append('<div class="loading">加载中...</div>');
		$.ajax({
			url:gurl,
			success:function(data){
				var data=JSON.parse(data)
				$('#chart_line_day_box .highcharts-container').html(data.html).css({ opacity:1 });
			}
		});
	});
	$('#week_type_tab span').click(function(){
		$(this).siblings().removeClass('cur').end().addClass('cur');
		$('#week_type').attr('data',$(this).attr('data'));
		var gurl='./tongjis.php?'+'c-'+$('#week_type').attr('data');
		$('#chart_line_week_box .highcharts-container').css({ opacity: 0.3 });
		$('#chart_line_week').append('<div class="loading">加载中...</div>');
		$.ajax({
			url:gurl,
			success:function(data){
				var data=JSON.parse(data)
				$('#chart_line_week_box .highcharts-container').html(data.html).css({ opacity:1 });
			}
		});
	});
	$('#week_tab span').click(function(){
		$(this).siblings().removeClass('cur').end().addClass('cur');
		var gurl='./tongjis.php?'+'c-'+$(this).attr('data')+''+$('#week_type').attr('data');
		$('#chart_line_week_box .highcharts-container').css({ opacity: 0.3 });
		$('#chart_line_week').append('<div class="loading">加载中...</div>');
		$.ajax({
			url:gurl,
			success:function(data){
				var data=JSON.parse(data)
				$('#chart_line_week_box .highcharts-container').html(data.html).css({ opacity:1 });
			}
		});
	});
});
</script>

<table border="0" align="center" cellpadding="3" cellspacing="0" class="table_b" style="margin-top:10px">
	<tbody>
	<?php
	$zongshu=$Googlebotsa+$baiduspidersa+$liuSpidersa+$Sogouspidersa+$Yisouspidersa+$Bytespidersa;
	echo "
	<tr class='tdbg item_title'>
		<td colspan='6'>
			<i class=\"typcn typcn-cog\"></i> 蜘蛛访问明细&nbsp;<input id=\"sday\" type=\"text\" onclick=\"WdatePicker({ dateFmt:'yyyyMMdd'})\" class=\"input Wdate\" style=\"width:85px;\" value=\"$time\" >&nbsp;<button type=\"button\" class=\"button\" onclick=\"get_list('5000.php?zong--'+\$('#sday').val()+'--p--1');\">查看</button>&nbsp;<span id='scount'><span class='glist'><a href=''><font color='red'>全部($zongshu)</font></a>
			</span>&nbsp;<span class='glist'><a href='5000.php?Baiduspider--$time--p--1'><font>百度($baiduspidersa)</font></a></span>&nbsp;
			<span class='glist'><a href='5000.php?Googlebot--$time--p--1'><font>Google($Googlebotsa)</font></a></span>&nbsp;
			<span class='glist'><a href='5000.php?360Spider--$time--p--1'><font>360蜘蛛($liuSpidersa)</font></a>
			</span>&nbsp;<span class='glist'><a href='5000.php?Sogou--$time--p--1'><font>搜狗($Sogouspidersa)</font></a></span>&nbsp;
			<span class='glist'><a href='5000.php?Yisouspider--$time--p--1'><font>神马($Yisouspidersa)</font></a></span>&nbsp;
			<span class='glist'><a href='5000.php?Bytespider--$time--p--1'><font>今日头条($Bytespidersa)</font></a></span>&nbsp;</span></td>
	</tr>
	<tr>
	  <td width='50' align='center' class='title_bg'>id</td>
	  <td width='100' align='center' class='title_bg'>蜘蛛名称</td>
	  <td width='110' align='center' class='title_bg'>IP地址</td>
	   <td width='80' align='center' class='title_bg'>国家/城市</td>
      <td class='title_bg'>访问地址</td>
	  <td width='60' align='center' class='title_bg'>模型</td>
	  <td width='140' align='center' class='title_bg'>访问时间</td>
    </tr>
	</tbody>
	<tbody id='rlist'>
	";
	if($list){
	foreach($list as $k=>$v){
		echo "<tr class='tdbg'>
	<td align='center'>$k</td>
	<td align='center'>$v[2]</td>
	<td align='center'><a title='点击查询IP归属' href='https://www.ip138.com/iplookup.asp?ip=$v[1]&amp;action=2' target='_blank'>$v[1]</a></td>
	<td align='center'><font color='green'>中国</font></td>
	<td><a target='_blank' title='打开此链接' href='$v[3]'>$v[3]</a></td>
	<td align='center'>文章新闻</td>
	<td align='center'><font color='red'>$v[0]</font></td></tr>
		";
	}
	}else{
		echo "<tr bgcolor='#ffffff'>
			<td colspan='7' height='25' align='center'>暂无百度蜘蛛记录！</td>
		</tr>";
	}
	?>	
	</tbody>
	<tbody>
	<tr>
      <td colspan="7" class="tdbg content_page" align="center"><a>共 <font id="total"><?php echo $counts; ?></font> 条</a>&nbsp;<span class="glist" id="pages"><?php echo $paged ?></span></td>
	</tr>
	</tbody>
</table>
<script type="text/javascript">
bind_page();
function bind_page(){
	$('.glist a').click(function(){
		var href=$(this).attr('href');
		if(href){
			get_list(href);
			return false;
		}
	});
}
function get_list(url){
	
	$.ajax({
		url:url,
		success:function(data){
			var data=JSON.parse(data)
			
			$('#pages').html(data.pages);
			str='';
			$.each(data.list,function($n,$vo){
				str+='<tr class="tdbg">';
				str+='	<td align="center">'+$vo['id']+'</td>';
				str+='	<td align="center">'+$vo['name']+'</td>';
				str+='	<td align="center"><a title="点击查询IP归属" href="https://www.ip138.com/iplookup.asp?ip='+$vo['ip']+'&action=2" target="_blank">'+$vo['ip']+'</a></td>';
				str+='	<td align="center">'+$vo['city']+'</td>';
				str+='	<td>'+$vo['url']+'</td>';
				str+='	<td align="center">'+$vo['typename']+'</td>';
				str+='	<td align="center">'+$vo['time']+'</td>';
				str+='</tr>';
			});
			cstr='';
			$.each(data.scount,function($n,$vo){
				cstr+='<span class="glist"><a href="'+$vo['url']+'"><font '+($vo['key']==data.spider ? 'color="red"':'')+'>'+$vo['name']+'('+$vo['count']+')</font></a></span>&nbsp;';
			});
			$('#rlist').html(str);
			$('#scount').html(cstr);
			$('#total').html(data.total);
			
			bind_page();
		}
	});
}
</script>
<div class="runtime"></div>  
</div>
</body>
</html>