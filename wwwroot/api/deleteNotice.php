<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// 验证登录状态
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => '未登录']);
    exit;
}

$noticesFile = '../data/notices.json';
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID不能为空']);
    exit;
}

// 读取现有通知
$notices = [];
if (file_exists($noticesFile)) {
    $notices = json_decode(file_get_contents($noticesFile), true) ?: [];
}

// 过滤掉要删除的通知
$newNotices = array_filter($notices, function($notice) use ($data) {
    return $notice['id'] !== $data['id'];
});

// 重新索引数组
$newNotices = array_values($newNotices);

// 保存文件
file_put_contents($noticesFile, json_encode($newNotices, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo json_encode(['success' => true, 'message' => '删除成功']);
?>