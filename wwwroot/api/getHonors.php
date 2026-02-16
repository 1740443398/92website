<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
// 读取荣誉JSON文件
$honorsFile = '../data/honors.json';
$honors = [];
if (file_exists($honorsFile)) {
    $honors = json_decode(file_get_contents($honorsFile), true) ?: [];
}
// 返回JSON数据
echo json_encode($honors);
?>