<?php
session_start();

// 引入配置文件
require_once '../config.php';

// 检查是否已安装
if (!is_installed()) {
    header('Location: ../install.php');
    exit;
}

// 处理登录请求
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = '请输入用户名和密码';
    } else {
        $db = get_db_connection();
        if (!$db) {
            $error = '数据库连接失败';
        } else {
            try {
                // 查询用户
                $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                if (!$user || !password_verify($password, $user['password'])) {
                    $error = '用户名或密码错误';
                } else {
                    // 登录成功，记录登录时间
                    $stmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                    $stmt->execute([$user['id']]);
                    
                    // 设置会话
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_loggedin'] = true;
                    
                    // 跳转到后台首页
                    header('Location: index.php');
                    exit;
                }
            } catch (PDOException $e) {
                $error = '登录失败: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>后台管理系统 - 登录</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../ass/layui/css/layui.css">
    <style>
        body {
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        .login-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 2px;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,.05);
        }
        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-header h2 {
            font-size: 24px;
            color: #333;
        }
        .captcha-container {
            display: flex;
        }
        .captcha-container .layui-input {
            width: 60%;
        }
        .captcha-container img {
            width: 40%;
            height: 38px;
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h2>视频站后台管理系统</h2>
            </div>
            
            <?php if ($error): ?>
                <div class="layui-row">
                    <div class="layui-col-xs12">
                        <div class="layui-card">
                            <div class="layui-card-body" style="color: #FF5722;">
                                <?php echo $error; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            <?php endif; ?>
            
            <form class="layui-form" action="login.php" method="post">
                <div class="layui-form-item">
                    <div class="layui-input-block" style="margin-left: 0;">
                        <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required lay-verify="required" placeholder="请输入用户名" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block" style="margin-left: 0;">
                        <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
                    </div>
                </div>
                
                <div class="layui-form-item">
                    <div class="layui-input-block" style="margin-left: 0;">
                        <button class="layui-btn layui-btn-fluid" lay-submit>登录</button>
                    </div>
                </div>
            </form>
        </div>
        <div style="text-align: center; margin-top: 15px; color: #999;">
            &copy; <?php echo date('Y'); ?> 视频站管理系统
        </div>
    </div>

    <script src="../ass/layui/layui.js"></script>
    <script>
        layui.use(['form', 'layer'], function(){
            var form = layui.form;
            var layer = layui.layer;
            
            // 表单验证
            form.verify({
                username: function(value, item){
                    if(!value) {
                        return '请输入用户名';
                    }
                },
                password: function(value, item){
                    if(!value) {
                        return '请输入密码';
                    }
                }
            });
            
            // 监听提交
            form.on('submit', function(data){
                return true;
            });
        });
    </script>
</body>
</html>
