<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// 读取通知JSON文件
$noticesFile = '../data/notices.json';
$notices = [];

if (file_exists($noticesFile)) {
    $notices = json_decode(file_get_contents($noticesFile), true) ?: [];
}

// 返回JSON数据
echo json_encode($notices);
?>