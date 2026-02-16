<?php
// 只允许 POST 请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => '仅支持POST请求']);
    exit;
}

// 获取POST JSON数据
$postData = json_decode(file_get_contents('php://input'), true);

// 验证必要字段
if (empty($postData['name']) || empty($postData['message'])) {
    echo json_encode(['success' => false, 'message' => '姓名和反馈内容不能为空']);
    exit;
}

// 定义反馈数据文件路径
$feedbackFile = '../data/feedback.json';

// 确保data目录存在
$dir = dirname($feedbackFile);
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// 读取现有反馈数据
$feedbacks = [];
if (file_exists($feedbackFile)) {
    $feedbacks = json_decode(file_get_contents($feedbackFile), true) ?: [];
}

// 生成唯一ID
$newId = count($feedbacks) > 0 ? max(array_column($feedbacks, 'id')) + 1 : 1;

// 构造反馈数据
$feedback = [
    'id' => $newId,
    'name' => trim($postData['name']),
    'contact' => trim($postData['contact'] ?? ''),
    'message' => trim($postData['message']),
    'submitTime' => $postData['submitTime'] ?? date('Y-m-d H:i:s')
];

// 添加到反馈列表
$feedbacks[] = $feedback;

// 保存数据到JSON文件
if (file_put_contents($feedbackFile, json_encode($feedbacks, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true, 'message' => '反馈提交成功']);
} else {
    echo json_encode(['success' => false, 'message' => '文件写入失败，请检查目录权限']);
}
?>