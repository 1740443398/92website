<?php
session_start();
// 验证登录状态
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
// 读取荣誉数据
$honorsFile = '../data/honors.json';
$honors = [];
if (file_exists($honorsFile)) {
    $honors = json_decode(file_get_contents($honorsFile), true) ?: [];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>荣誉管理 - 九（2）班后台</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        :root { --primary: #2A5EA8; }
        .nav-active { background-color: rgba(42, 94, 168, 0.1); border-left: 4px solid var(--primary); color: var(--primary); }
        .color-amber { border-left: 4px solid #f59e0b; }
        .color-blue { border-left: 4px solid #3b82f6; }
        .color-green { border-left: 4px solid #22c55e; }
        .color-red { border-left: 4px solid #ef4444; }
        .color-purple { border-left: 4px solid #a855f7; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- 顶部导航（与通知管理完全一致） -->
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
        <!-- 左侧菜单（荣誉管理设为激活） -->
        <aside class="w-64 bg-white shadow-sm h-[calc(100vh-4rem)] sticky top-16">
            <div class="py-4">
                <a href="index.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-tachometer-alt w-6 text-center mr-3"></i>
                    <span>后台首页</span>
                </a>
                <a href="feedbackList.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-comments w-6 text-center mr-3"></i>
                    <span>反馈管理</span>
                </a>
                <a href="honorManager.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50 nav-active">
                    <i class="fas fa-award w-6 text-center mr-3"></i>
                    <span>荣誉管理</span>
                </a>
                <a href="noticeManager.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-bell w-6 text-center mr-3"></i>
                    <span>通知管理</span>
                </a>
            </div>
        </aside>
        <!-- 右侧荣誉管理内容 -->
        <main class="flex-1 p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">班级荣誉管理</h2>
                    <button id="addHonorBtn" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary/90 flex items-center">
                        <i class="fas fa-plus mr-2"></i> 新增荣誉
                    </button>
                </div>
                <!-- 荣誉列表 -->
                <div id="honorList" class="space-y-4">
                    <?php if (empty($honors)): ?>
                        <div class="text-center py-10 text-gray-500">
                            <i class="fas fa-trophy text-4xl mb-3 text-gray-300"></i>
                            <p>暂无荣誉数据，点击"新增荣誉"添加</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($honors as $honor): ?>
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow color-<?= $honor['color'] ?>" data-id="<?= $honor['id'] ?>">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-800 flex items-center">
                                            <i class="fas fa-<?= $honor['icon'] ?> mr-2 text-<?= $honor['color'] ?>-500"></i>
                                            <?= htmlspecialchars($honor['title']) ?>
                                        </h3>
                                        <p class="text-gray-500 text-sm mt-1"><?= htmlspecialchars($honor['date']) ?></p>
                                    </div>
                                    <div>
                                        <span class="px-2 py-1 bg-<?= $honor['color'] ?>-100 text-<?= $honor['color'] ?>-800 rounded text-xs">
                                            <?= [
                                                'amber'=>'琥珀色',
                                                'blue'=>'蓝色',
                                                'green'=>'绿色',
                                                'red'=>'红色',
                                                'purple'=>'紫色'
                                            ][$honor['color']] ?? '默认色' ?>
                                        </span>
                                    </div>
                                </div>
                                <p class="text-gray-600 mb-4"><?= htmlspecialchars($honor['description']) ?></p>
                                <div class="flex justify-end space-x-3">
                                    <button class="editHonorBtn text-blue-600 hover:text-blue-800 flex items-center" data-honor='<?= json_encode($honor, JSON_UNESCAPED_UNICODE) ?>'>
                                        <i class="fas fa-edit mr-1"></i> 编辑
                                    </button>
                                    <button class="deleteHonorBtn text-red-600 hover:text-red-800 flex items-center" data-id="<?= $honor['id'] ?>">
                                        <i class="fas fa-trash mr-1"></i> 删除
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <!-- 荣誉编辑弹窗 -->
    <div id="honorModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-800">新增荣誉</h3>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="honorForm">
                <input type="hidden" id="honorId" value="">
                <div class="mb-4">
                    <label for="honorTitle" class="block text-sm font-medium text-gray-700 mb-1">荣誉标题 <span class="text-red-500">*</span></label>
                    <input type="text" id="honorTitle" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" required>
                </div>
                <div class="mb-4">
                    <label for="honorColor" class="block text-sm font-medium text-gray-700 mb-1">荣誉配色</label>
                    <select id="honorColor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        <option value="amber">琥珀色（默认）</option>
                        <option value="blue">蓝色</option>
                        <option value="green">绿色</option>
                        <option value="red">红色</option>
                        <option value="purple">紫色</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="honorIcon" class="block text-sm font-medium text-gray-700 mb-1">荣誉图标（FontAwesome）</label>
                    <input type="text" id="honorIcon" placeholder="如：award/trophy/medal" value="award" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                    <p class="text-xs text-gray-400 mt-1">图标名称参考：<a href="https://fontawesome.com/icons" target="_blank" class="text-primary">FontAwesome</a></p>
                </div>
                <div class="mb-4">
                    <label for="honorDate" class="block text-sm font-medium text-gray-700 mb-1">获取日期 <span class="text-red-500">*</span></label>
                    <input type="date" id="honorDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" required>
                </div>
                <div class="mb-6">
                    <label for="honorDesc" class="block text-sm font-medium text-gray-700 mb-1">荣誉描述 <span class="text-red-500">*</span></label>
                    <textarea id="honorDesc" rows="4" placeholder="如：2025-2026学年第一学期校级文明班级" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelBtn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">取消</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90">保存</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        // 全局变量
        const honorModal = document.getElementById('honorModal');
        const modalTitle = document.getElementById('modalTitle');
        const honorForm = document.getElementById('honorForm');
        const honorId = document.getElementById('honorId');
        const honorTitle = document.getElementById('honorTitle');
        const honorColor = document.getElementById('honorColor');
        const honorIcon = document.getElementById('honorIcon');
        const honorDate = document.getElementById('honorDate');
        const honorDesc = document.getElementById('honorDesc');
        // 打开新增弹窗
        document.getElementById('addHonorBtn').addEventListener('click', () => {
            modalTitle.textContent = '新增荣誉';
            honorForm.reset();
            honorId.value = '';
            honorIcon.value = 'award';
            // 默认选中今天日期
            const today = new Date().toISOString().split('T')[0];
            honorDate.value = today;
            honorModal.classList.remove('hidden');
        });
        // 关闭弹窗
        document.getElementById('closeModalBtn').addEventListener('click', () => {
            honorModal.classList.add('hidden');
        });
        document.getElementById('cancelBtn').addEventListener('click', () => {
            honorModal.classList.add('hidden');
        });
        // 点击编辑按钮
        document.querySelectorAll('.editHonorBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const honor = JSON.parse(btn.getAttribute('data-honor'));
                modalTitle.textContent = '编辑荣誉';
                honorId.value = honor.id;
                honorTitle.value = honor.title;
                honorColor.value = honor.color;
                honorIcon.value = honor.icon;
                honorDate.value = honor.date;
                honorDesc.value = honor.description;
                honorModal.classList.remove('hidden');
            });
        });
        // 提交表单（调用saveHonor接口）
        honorForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const honorData = {
                id: honorId.value,
                title: honorTitle.value,
                color: honorColor.value,
                icon: honorIcon.value,
                date: honorDate.value,
                description: honorDesc.value
            };
            try {
                const response = await axios.post('../api/saveHonor.php', honorData);
                if (response.data.success) {
                    alert('保存成功！');
                    window.location.reload();
                } else {
                    alert('保存失败：' + response.data.message);
                }
            } catch (error) {
                alert('保存失败：网络错误');
                console.error(error);
            }
        });
        // 删除荣誉（调用deleteHonor接口）
        document.querySelectorAll('.deleteHonorBtn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.getAttribute('data-id');
                if (!confirm('确定要删除这项荣誉吗？')) return;
                try {
                    const response = await axios.post('../api/deleteHonor.php', { id });
                    if (response.data.success) {
                        alert('删除成功！');
                        window.location.reload();
                    } else {
                        alert('删除失败：' + response.data.message);
                    }
                } catch (error) {
                    alert('删除失败：网络错误');
                    console.error(error);
                }
            });
        });
        // 点击空白处关闭弹窗
        window.addEventListener('click', (e) => {
            if (e.target === honorModal) {
                honorModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>