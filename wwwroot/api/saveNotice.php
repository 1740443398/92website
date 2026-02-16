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

// 验证必填字段
if (empty($data['title']) || empty($data['date']) || empty($data['content'])) {
    echo json_encode(['success' => false, 'message' => '标题、日期、内容不能为空']);
    exit;
}

// 读取现有通知
$notices = [];
if (file_exists($noticesFile)) {
    $notices = json_decode(file_get_contents($noticesFile), true) ?: [];
}

// 新增/编辑通知
if (!empty($data['id'])) {
    // 编辑已有通知
    foreach ($notices as &$notice) {
        if ($notice['id'] === $data['id']) {
            $notice['title'] = $data['title'];
            $notice['type'] = $data['type'] ?: 'notice';
            $notice['date'] = $data['date'];
            $notice['content'] = $data['content'];
            $notice['source'] = $data['source'] ?: '';
            break;
        }
    }
} else {
    // 新增通知
    $newId = 'notice_' . uniqid();
    $notices[] = [
        'id' => $newId,
        'title' => $data['title'],
        'type' => $data['type'] ?: 'notice',
        'date' => $data['date'],
        'content' => $data['content'],
        'source' => $data['source'] ?: ''
    ];
}

// 保存文件
file_put_contents($noticesFile, json_encode($notices, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo json_encode(['success' => true, 'message' => '保存成功']);
?>