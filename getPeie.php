<?php
header("Content-type: text/html; charset=utf-8"); 
$url = 'http://techan.web05.idscn.com/index.php/Index/getpeie/';
$data = file_get_contents($url);

function get_preg_replace($str){
    $str = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", " ", strip_tags($str));
    
    $date = date("Y/m/d",time());
    $date_str_pos = strpos($date,"/");
    $date_str = substr($date,0, $date_str_pos+1);

    $start_pos = strpos($str, $date_str);
    $len = strlen($str);
    $str = substr($str,$start_pos);
    
    $a_start_pos = strpos($str,'dailyQuota');
    $str = substr($str,0,$a_start_pos);

    return $str;
}

$data = get_preg_replace($data);
$data_len = strlen($data);

$data = explode(" ",$data);

foreach($data as $key=>$value){
    if($value == ""){
        unset($data[$key]);
    }
}

$data = array_values($data);
//计算列数
$data_temp = $data;
$first_col_date = array_shift($data_temp);
$data_str = implode(' ',$data_temp);

$date = date("Y/m/d", time());
$date_str_pos = strpos($date,"/");
$date_str = substr($date, 0, $date_str_pos+1);
$temp_pos = strpos($data_str, $date_str);

$data_temp_str = substr($data_str, 0, $temp_pos-1);
$data_temp_str_arr = explode(" ",$data_temp_str);
$data_temp_str_arr_num = count($data_temp_str_arr);

//表格中总的列数
$col_num = $data_temp_str_arr_num+1;
unset($data_temp);

$table_data = array();
$col_data = array();
$data_count = count($data);

for($i=0; $i<=$data_count-1; $i+=$col_num){
    $col_data[] = array_slice($data,$i,$col_num);
}

//接收指定的列 传参 string
$custom_col = '1,8,9,10';
$custom_col_arr = explode(',', $custom_col);
$custom_col_num = count($custom_col_arr);
$custom_col_data = array();

foreach($col_data as $key=>$value){
    for($i=0; $i<$custom_col_num; $i++){
        $custom_col_data_key = $custom_col_arr[$i]-1;
        $col_data_key = $custom_col_data_key;
        if(array_key_exists($custom_col_data_key, $value)){
            $custom_col_data[$key][] = $value[$col_data_key];
        }
    }
}

//文件操作将数据写入本地文件
$fp = fopen('peie_data.php', 'w+') or die('peie_data.txt创建或打开失败！');
$fw = fwrite($fp,serialize($custom_col_data));
if(!$fw){
    echo '配额数据写入失败！';
}
fclose($fp);

//从本地文件读取数据展示到模板
$fg = file_get_contents('peie_data.php');
$peie_data = unserialize($fg);
include('./peie.html');


