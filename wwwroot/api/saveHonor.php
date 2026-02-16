<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
// 验证登录状态
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => '未登录']);
    exit;
}
$honorsFile = '../data/honors.json';
$data = json_decode(file_get_contents('php://input'), true);
// 验证必填字段
if (empty($data['title']) || empty($data['date']) || empty($data['description'])) {
    echo json_encode(['success' => false, 'message' => '荣誉标题、日期、描述不能为空']);
    exit;
}
// 确保data目录存在
$dir = dirname($honorsFile);
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}
// 读取现有荣誉
$honors = [];
if (file_exists($honorsFile)) {
    $honors = json_decode(file_get_contents($honorsFile), true) ?: [];
}
// 新增/编辑荣誉
if (!empty($data['id'])) {
    // 编辑已有荣誉
    foreach ($honors as &$honor) {
        if ($honor['id'] === $data['id']) {
            $honor['title'] = $data['title'];
            $honor['color'] = $data['color'] ?: 'amber';
            $honor['icon'] = $data['icon'] ?: 'award';
            $honor['description'] = $data['description'];
            $honor['date'] = $data['date'];
            break;
        }
    }
} else {
    // 新增荣誉（同通知ID生成规则）
    $newId = 'honor_' . uniqid();
    $honors[] = [
        'id' => $newId,
        'title' => $data['title'],
        'color' => $data['color'] ?: 'amber',
        'icon' => $data['icon'] ?: 'award',
        'description' => $data['description'],
        'date' => $data['date']
    ];
}
// 保存文件
if (file_put_contents($honorsFile, json_encode($honors, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true, 'message' => '保存成功']);
} else {
    echo json_encode(['success' => false, 'message' => '文件写入失败，请检查目录权限']);
}
?>