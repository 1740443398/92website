<?php
session_start();
// 验证登录状态
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 读取通知数据
$noticesFile = '../data/notices.json';
$notices = [];
if (file_exists($noticesFile)) {
    $notices = json_decode(file_get_contents($noticesFile), true) ?: [];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>通知管理 - 九（2）班后台</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        :root { --primary: #2A5EA8; }
        .nav-active { background-color: rgba(42, 94, 168, 0.1); border-left: 4px solid var(--primary); color: var(--primary); }
        .type-notice { border-left: 4px solid #f59e0b; }
        .type-success { border-left: 4px solid #22c55e; }
        .type-secondary { border-left: 4px solid #3b82f6; }
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
                <a href="feedbackList.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-comments w-6 text-center mr-3"></i>
                    <span>反馈管理</span>
                </a>
                <a href="honorManager.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-award w-6 text-center mr-3"></i>
                    <span>荣誉管理</span>
                </a>
                <a href="noticeManager.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50 nav-active">
                    <i class="fas fa-bell w-6 text-center mr-3"></i>
                    <span>通知管理</span>
                </a>
            </div>
        </aside>

        <!-- 右侧内容 -->
        <main class="flex-1 p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">通知公告管理</h2>
                    <button id="addNoticeBtn" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary/90 flex items-center">
                        <i class="fas fa-plus mr-2"></i> 新增通知
                    </button>
                </div>

                <!-- 通知列表 -->
                <div id="noticeList" class="space-y-4">
                    <?php if (empty($notices)): ?>
                        <div class="text-center py-10 text-gray-500">
                            <i class="fas fa-bell text-4xl mb-3 text-gray-300"></i>
                            <p>暂无通知数据，点击"新增通知"添加</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notices as $notice): ?>
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow type-<?= $notice['type'] ?>" data-id="<?= $notice['id'] ?>">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-800"><?= htmlspecialchars($notice['title']) ?></h3>
                                        <p class="text-gray-500 text-sm mt-1">
                                            <?= htmlspecialchars($notice['date']) ?> 
                                            <?php if (!empty($notice['source'])): ?>
                                                - <?= htmlspecialchars($notice['source']) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div>
                                        <?php if ($notice['type'] === 'notice'): ?>
                                            <span class="px-2 py-1 bg-amber-100 text-amber-800 rounded text-xs">普通通知</span>
                                        <?php elseif ($notice['type'] === 'success'): ?>
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">喜报通知</span>
                                        <?php elseif ($notice['type'] === 'secondary'): ?>
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">重要通知</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p class="text-gray-600 mb-4 whitespace-pre-line"><?= htmlspecialchars($notice['content']) ?></p>
                                <div class="flex justify-end space-x-3">
                                    <button class="editNoticeBtn text-blue-600 hover:text-blue-800 flex items-center" data-notice='<?= json_encode($notice, JSON_UNESCAPED_UNICODE) ?>'>
                                        <i class="fas fa-edit mr-1"></i> 编辑
                                    </button>
                                    <button class="deleteNoticeBtn text-red-600 hover:text-red-800 flex items-center" data-id="<?= $notice['id'] ?>">
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

    <!-- 通知编辑弹窗 -->
    <div id="noticeModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-800">新增通知</h3>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="noticeForm">
                <input type="hidden" id="noticeId" value="">
                <div class="mb-4">
                    <label for="noticeTitle" class="block text-sm font-medium text-gray-700 mb-1">通知标题 <span class="text-red-500">*</span></label>
                    <input type="text" id="noticeTitle" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" required>
                </div>
                <div class="mb-4">
                    <label for="noticeType" class="block text-sm font-medium text-gray-700 mb-1">通知类型</label>
                    <select id="noticeType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        <option value="notice">普通通知（黄色）</option>
                        <option value="success">喜报通知（绿色）</option>
                        <option value="secondary">重要通知（蓝色）</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="noticeDate" class="block text-sm font-medium text-gray-700 mb-1">通知日期 <span class="text-red-500">*</span></label>
                    <input type="date" id="noticeDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" required>
                </div>
                <div class="mb-4">
                    <label for="noticeSource" class="block text-sm font-medium text-gray-700 mb-1">通知来源</label>
                    <input type="text" id="noticeSource" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" placeholder="如：班主任/教务处">
                </div>
                <div class="mb-6">
                    <label for="noticeContent" class="block text-sm font-medium text-gray-700 mb-1">通知内容 <span class="text-red-500">*</span></label>
                    <textarea id="noticeContent" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" placeholder="换行请直接按Enter键" required></textarea>
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
        const noticeModal = document.getElementById('noticeModal');
        const modalTitle = document.getElementById('modalTitle');
        const noticeForm = document.getElementById('noticeForm');
        const noticeId = document.getElementById('noticeId');
        const noticeTitle = document.getElementById('noticeTitle');
        const noticeType = document.getElementById('noticeType');
        const noticeDate = document.getElementById('noticeDate');
        const noticeSource = document.getElementById('noticeSource');
        const noticeContent = document.getElementById('noticeContent');

        // 打开新增弹窗
        document.getElementById('addNoticeBtn').addEventListener('click', () => {
            modalTitle.textContent = '新增通知';
            noticeForm.reset();
            noticeId.value = '';
            // 默认选中今天日期
            const today = new Date().toISOString().split('T')[0];
            noticeDate.value = today;
            noticeModal.classList.remove('hidden');
        });

        // 关闭弹窗
        document.getElementById('closeModalBtn').addEventListener('click', () => {
            noticeModal.classList.add('hidden');
        });
        document.getElementById('cancelBtn').addEventListener('click', () => {
            noticeModal.classList.add('hidden');
        });

        // 点击编辑按钮
        document.querySelectorAll('.editNoticeBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const notice = JSON.parse(btn.getAttribute('data-notice'));
                modalTitle.textContent = '编辑通知';
                noticeId.value = notice.id;
                noticeTitle.value = notice.title;
                noticeType.value = notice.type;
                noticeDate.value = notice.date;
                noticeSource.value = notice.source || '';
                noticeContent.value = notice.content;
                noticeModal.classList.remove('hidden');
            });
        });

        // 提交表单
        noticeForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const noticeData = {
                id: noticeId.value,
                title: noticeTitle.value,
                type: noticeType.value,
                date: noticeDate.value,
                source: noticeSource.value,
                content: noticeContent.value
            };

            try {
                const response = await axios.post('../api/saveNotice.php', noticeData);
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

        // 删除通知
        document.querySelectorAll('.deleteNoticeBtn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.getAttribute('data-id');
                if (!confirm('确定要删除这条通知吗？')) return;

                try {
                    const response = await axios.post('../api/deleteNotice.php', { id });
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
            if (e.target === noticeModal) {
                noticeModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>