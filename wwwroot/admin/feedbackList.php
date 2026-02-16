<?php
session_start();

// 验证登录状态
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 可选：设置Session过期时间（30分钟）
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 1800)) {
    session_destroy();
    header('Location: login.php?expired=1');
    exit;
}
$_SESSION['login_time'] = time(); // 刷新登录时间

// 读取反馈数据
$feedbackFile = '../data/feedback.json';
$feedbackData = [];
if (file_exists($feedbackFile)) {
    $feedbackData = json_decode(file_get_contents($feedbackFile), true) ?: [];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>反馈管理 - 九（2）班后台</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary: #2A5EA8; }
        .nav-active { background-color: rgba(42, 94, 168, 0.1); border-left: 4px solid var(--primary); color: var(--primary); }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- 顶部导航 -->
    <nav class="bg-white shadow-sm sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-800">九（2）班后台管理系统</h1>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-4">欢迎，<?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                    <a href="logout.php" class="text-red-600 hover:text-red-800 text-sm">
                        <i class="fas fa-sign-out-alt mr-1"></i> 退出登录
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- 主体内容 -->
    <div class="flex flex-1">
        <!-- 左侧菜单 -->
        <aside class="w-64 bg-white shadow-sm h-[calc(100vh-4rem)] sticky top-16">
            <div class="py-4">
                <a href="index.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-tachometer-alt w-6 text-center mr-3"></i>
                    <span>后台首页</span>
                </a>
                <a href="feedbackList.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50 nav-active">
                    <i class="fas fa-comments w-6 text-center mr-3"></i>
                    <span>反馈管理</span>
                </a>
                <a href="honorManager.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-award w-6 text-center mr-3"></i>
                    <span>荣誉管理</span>
                </a>
                <a href="noticeManager.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-bell w-6 text-center mr-3"></i>
                    <span>通知管理</span>
                </a>
            </div>
        </aside>

        <!-- 右侧内容 -->
        <main class="flex-1 p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">意见反馈管理</h2>
                
                <?php if (empty($feedbackData)): ?>
                    <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p>暂无反馈数据</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">姓名</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">联系方式</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">反馈内容</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">提交时间</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($feedbackData as $item): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($item['id']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($item['contact']) ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-md"><?= nl2br(htmlspecialchars($item['message'])) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('Y-m-d H:i', strtotime($item['submitTime'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>