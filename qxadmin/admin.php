<?php
/**
 * 管理员管理页面
 */
require_once 'common.php';

// 处理表单提交
$message = '';
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // 验证输入
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = '请填写所有密码字段';
        $type = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = '新密码与确认密码不匹配';
        $type = 'error';
    } elseif (strlen($new_password) < 6) {
        $message = '新密码长度不能少于6个字符';
        $type = 'error';
    } else {
        $db = get_db_connection();
        if (!$db) {
            $message = '数据库连接失败';
            $type = 'error';
        } else {
            try {
                // 查询当前用户
                $stmt = $db->prepare("SELECT * FROM admin_users WHERE id = ?");
                $stmt->execute([$_SESSION['admin_id']]);
                $user = $stmt->fetch();
                
                if (!$user || !password_verify($current_password, $user['password'])) {
                    $message = '当前密码不正确';
                    $type = 'error';
                } else {
                    // 更新密码
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE admin_users SET password = ?, updated_at = NOW() WHERE id = ?");
                    if ($stmt->execute([$password_hash, $_SESSION['admin_id']])) {
                        $message = '密码修改成功';
                        $type = 'success';
                    } else {
                        $message = '密码修改失败';
                        $type = 'error';
                    }
                }
            } catch (PDOException $e) {
                $message = '处理失败: ' . $e->getMessage();
                $type = 'error';
            }
        }
    }
}

// 获取当前管理员信息
$admin_info = [
    'username' => $_SESSION['admin_username'] ?? '',
    'last_login' => ''
];

$db = get_db_connection();
if ($db) {
    try {
        $stmt = $db->prepare("SELECT last_login FROM admin_users WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $result = $stmt->fetch();
        if ($result) {
            $admin_info['last_login'] = $result['last_login'];
        }
    } catch (PDOException $e) {
        // 忽略错误
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>管理员设置 - 视频站后台管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="../ass/layui/css/layui.css">
    <link rel="stylesheet" href="mobile.css">
    <link rel="stylesheet" href="responsive.css">
</head>
<body>
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <div class="layui-logo layui-hide-xs">视频站后台管理</div>
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layui-hide-xs">
                    <a href="javascript:;">
                        <i class="layui-icon layui-icon-user"></i>
                        管理员设置
                    </a>
                </li>
            </ul>
            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item layui-hide-xs">
                    <a href="../index.php" target="_blank">
                        <i class="layui-icon layui-icon-website"></i>
                        网站首页
                    </a>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <i class="layui-icon layui-icon-username"></i>
                        <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="admin.php">修改密码</a></dd>
                        <dd><a href="logout.php">退出登录</a></dd>
                    </dl>
                </li>
            </ul>
        </div>
        
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <ul class="layui-nav layui-nav-tree" lay-filter="test">
                    <?php foreach (get_admin_menu() as $menu): ?>
                        <li class="layui-nav-item <?php echo $menu['active'] ? 'layui-this' : ''; ?>">
                            <a href="<?php echo $menu['url']; ?>">
                                <i class="layui-icon <?php echo $menu['icon']; ?>"></i>
                                <?php echo $menu['name']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <div class="layui-body">
            <div style="padding: 15px;">
                <?php if ($message): ?>
                    <div class="layui-row">
                        <div class="layui-col-md12">
                            <div class="layui-card">
                                <div class="layui-card-body" style="color: <?php echo $type === 'success' ? '#009688' : '#FF5722'; ?>">
                                    <?php echo $message; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                <?php endif; ?>
                
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md6">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                <i class="layui-icon layui-icon-user"></i>
                                管理员信息
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-form">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">用户名</label>
                                        <div class="layui-input-block">
                                            <input type="text" value="<?php echo htmlspecialchars($admin_info['username']); ?>" class="layui-input" readonly>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">上次登录</label>
                                        <div class="layui-input-block">
                                            <input type="text" value="<?php echo $admin_info['last_login'] ? $admin_info['last_login'] : '无记录'; ?>" class="layui-input" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="layui-col-md6">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                <i class="layui-icon layui-icon-password"></i>
                                修改密码
                            </div>
                            <div class="layui-card-body">
                                <form class="layui-form" action="admin.php" method="post">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">当前密码</label>
                                        <div class="layui-input-block">
                                            <input type="password" name="current_password" required lay-verify="required" placeholder="请输入当前密码" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">新密码</label>
                                        <div class="layui-input-block">
                                            <input type="password" name="new_password" required lay-verify="required|pass" placeholder="请输入新密码" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">确认新密码</label>
                                        <div class="layui-input-block">
                                            <input type="password" name="confirm_password" required lay-verify="required|pass|confirmPass" placeholder="请再次输入新密码" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-input-block">
                                            <button class="layui-btn" lay-submit>修改密码</button>
                                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="layui-footer">
            © <?php echo date('Y'); ?> 视频站后台管理系统
        </div>
    </div>

    <script src="../ass/layui/layui.js"></script>
    <script src="mobile.js"></script>
    <script>
        layui.use(['element', 'layer', 'form', 'jquery'], function(){
            var element = layui.element;
            var layer = layui.layer;
            var form = layui.form;
            var $ = layui.$;
            
            // 表单验证
            form.verify({
                pass: [
                    /^[\S]{6,16}$/,
                    '密码必须6到16位，且不能出现空格'
                ],
                confirmPass: function(value) {
                    var password = $('input[name=new_password]').val();
                    if (password !== value) {
                        return '两次密码输入不一致';
                    }
                }
            });
        });
    </script>
</body>
</html>
