<?php
session_start();

// 如果已登录，直接跳转到后台首页
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
// 处理登录提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单数据并过滤
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // 简单的表单验证
    if (empty($username) || empty($password)) {
        $error = '账号和密码不能为空';
    } else {
        // 简化版：直接核对明文账号密码（去掉小数点、去掉加密）
        $admin_user = 'Slate'; // 去掉小数点，账号改为Slate
        $admin_pass = '1234ABCD'; // 明文密码，不加密
        
        // 直接验证明文（无加密）
        if ($username === $admin_user && $password === $admin_pass) {
            // 登录成功，设置Session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['login_time'] = time(); // 记录登录时间
            
            // 跳转到后台首页
            header('Location: index.php');
            exit;
        } else {
            $error = '账号或密码错误';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台登录 - 九（2）班班级网站</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2A5EA8;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
        <div class="text-center">
            <div class="w-16 h-16 rounded-full bg-primary mx-auto flex items-center justify-center text-white text-2xl">
                <i class="fas fa-lock"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                后台管理登录
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                九（2）班班级网站管理系统
            </p>
        </div>
        
        <!-- 错误提示 -->
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">错误：</strong>
            <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>
        
        <!-- 登录表单 -->
        <form class="mt-8 space-y-6" method="POST" action="login.php">
            <input type="hidden" name="csrf_token" value="<?= md5(session_id() . time()) ?>">
            
            <div class="rounded-md -space-y-px">
                <div class="mb-3">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                        管理员账号
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input id="username" name="username" type="text" required 
                            class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                            placeholder="请输入账号">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        管理员密码
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required 
                            class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                            placeholder="请输入密码">
                    </div>
                </div>
            </div>
            
            <div>
                <button type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-white/50"></i>
                    </span>
                    登录
                </button>
            </div>
        </form>
        
        <div class="text-center text-xs text-gray-500">
            <p>© 2026 九（2）班班级网站 版权所有</p>
        </div>
    </div>
</body>
</html>