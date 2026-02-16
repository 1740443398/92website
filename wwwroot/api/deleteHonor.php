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
if (empty($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID不能为空']);
    exit;
}
// 读取现有荣誉
$honors = [];
if (file_exists($honorsFile)) {
    $honors = json_decode(file_get_contents($honorsFile), true) ?: [];
}
// 过滤掉要删除的荣誉
$newHonors = array_filter($honors, function($honor) use ($data) {
    return $honor['id'] !== $data['id'];
});
// 重新索引数组
$newHonors = array_values($newHonors);
// 保存文件
file_put_contents($honorsFile, json_encode($newHonors, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo json_encode(['success' => true, 'message' => '删除成功']);
?>