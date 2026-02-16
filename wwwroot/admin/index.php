<?php
session_start();
// 验证登录状态
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理首页 - 九（2）班班级网站</title>
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
                <a href="index.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50 nav-active">
                    <i class="fas fa-tachometer-alt w-6 text-center mr-3"></i>
                    <span>后台首页</span>
                </a>
                <a href="feedbackList.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
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
                <h2 class="text-2xl font-bold mb-6 text-gray-800">后台管理首页</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- 反馈卡片 -->
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-lg text-gray-800">意见反馈</h3>
                            <i class="fas fa-comments text-xl text-blue-500"></i>
                        </div>
                        <p class="text-gray-600 mb-4">查看和管理用户提交的意见反馈</p>
                        <a href="feedbackList.php" class="text-blue-600 hover:text-blue-800 flex items-center">
                            <span>前往管理</span>
                            <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>

                    <!-- 荣誉卡片 -->
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-lg text-gray-800">班级荣誉</h3>
                            <i class="fas fa-award text-xl text-amber-500"></i>
                        </div>
                        <p class="text-gray-600 mb-4">新增、编辑、删除班级荣誉</p>
                        <a href="honorManager.php" class="text-blue-600 hover:text-blue-800 flex items-center">
                            <span>前往管理</span>
                            <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>

                    <!-- 通知卡片 -->
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-lg text-gray-800">通知公告</h3>
                            <i class="fas fa-bell text-xl text-green-500"></i>
                        </div>
                        <p class="text-gray-600 mb-4">发布、编辑、删除通知公告</p>
                        <a href="noticeManager.php" class="text-blue-600 hover:text-blue-800 flex items-center">
                            <span>前往管理</span>
                            <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>