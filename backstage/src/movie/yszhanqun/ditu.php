<?php 
                                   
$url123="http://".$_SERVER ['HTTP_HOST'];
 preg_match("#http://(.*?)\.#i",$url123,$match);//获取二级域名
 $domin = $match[1];


function random_str($length)

{

//生成一个包含 大写英文字母, 小写英文字母, 数字 的数组

$arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

$str = '';

$arr_len = count($arr);

for ($i = 0; $i < $length; $i++)

{

$rand = mt_rand(0, $arr_len-1);

$str.=$arr[$rand];

}

return $str.'<br>';

}




		   for($i=0;$i<49999;$i++){
			  
$a= random_str(18);

$a=substr(md5(date('YmdHis').rand(1000,9999)),   8,   16);

$date = date('Ymd');
			   
	 echo "http://".$_SERVER ['HTTP_HOST']."/".$date."".rand(100,999999).".html" . "\r\n"; 
		

		
	
		} 
		
		


    