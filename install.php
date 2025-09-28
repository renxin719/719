<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 设置安装步骤
$steps = [
    1 => '环境检测',
    2 => '数据库配置',
    3 => '创建数据表',
    4 => '管理员设置',
    5 => '安装完成'
];

// 当前步骤
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
if ($step < 1 || $step > count($steps)) {
    $step = 1;
}

// 安装完成后跳转检测
if (file_exists('config.php')) {
    include 'config.php';
    if (is_installed()) {
        header('Location: index.php');
        exit;
    }
}

// 处理表单提交
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 2: // 数据库配置
            $db_host = $_POST['db_host'] ?? 'localhost';
            $db_port = $_POST['db_port'] ?? '3306';
            $db_username = $_POST['db_username'] ?? '';
            $db_password = $_POST['db_password'] ?? '';
            $db_database = $_POST['db_database'] ?? '';
            
            if (empty($db_username) || empty($db_database)) {
                $error = '请填写数据库用户名和数据库名';
                break;
            }
            
            try {
                // 测试连接
                $dsn = "mysql:host=$db_host;port=$db_port";
                $conn = new PDO($dsn, $db_username, $db_password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // 检查数据库是否存在，不存在则创建
                $conn->exec("CREATE DATABASE IF NOT EXISTS `$db_database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
                $conn = null;
                
                // 保存数据库配置到session
                $_SESSION['db_config'] = [
                    'host' => $db_host,
                    'port' => $db_port,
                    'username' => $db_username,
                    'password' => $db_password,
                    'database' => $db_database
                ];
                
                // 更新配置文件
                if (file_exists('config.php')) {
                    $config_content = file_get_contents('config.php');
                    $config_content = str_replace('{DB_HOST}', $db_host, $config_content);
                    $config_content = str_replace('{DB_PORT}', $db_port, $config_content);
                    $config_content = str_replace('{DB_USERNAME}', $db_username, $config_content);
                    $config_content = str_replace('{DB_PASSWORD}', $db_password, $config_content);
                    $config_content = str_replace('{DB_DATABASE}', $db_database, $config_content);
                    file_put_contents('config.php', $config_content);
                }
                
                // 成功则跳转到下一步
                header("Location: install.php?step=3");
                exit;
            } catch (PDOException $e) {
                $error = "数据库连接失败: " . $e->getMessage();
            }
            break;
            
        case 3: // 创建数据表
            if (!isset($_SESSION['db_config'])) {
                header("Location: install.php?step=2");
                exit;
            }
            
            $db_config = $_SESSION['db_config'];
            try {
                $dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};port={$db_config['port']}";
                $conn = new PDO($dsn, $db_config['username'], $db_config['password']);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // 创建管理员表
                $sql = "CREATE TABLE IF NOT EXISTS `admin_users` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `username` varchar(50) NOT NULL,
                    `password` varchar(255) NOT NULL,
                    `last_login` datetime DEFAULT NULL,
                    `created_at` datetime NOT NULL,
                    `updated_at` datetime NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `username` (`username`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                $conn->exec($sql);
                
                // 创建设置表
                $sql = "CREATE TABLE IF NOT EXISTS `settings` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `key` varchar(50) NOT NULL,
                    `value` text NOT NULL,
                    `created_at` datetime NOT NULL,
                    `updated_at` datetime NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `key` (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                $conn->exec($sql);
                
                // 创建统计表
                $sql = "CREATE TABLE IF NOT EXISTS `statistics` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `ip` varchar(45) NOT NULL,
                    `user_agent` varchar(255) DEFAULT NULL,
                    `page` varchar(255) DEFAULT NULL,
                    `referer` varchar(255) DEFAULT NULL,
                    `created_at` datetime NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `created_at` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                $conn->exec($sql);
                
                // 初始化设置
                $sql = "INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
                    ('popup_url', 'https://www.baidu.com/', NOW(), NOW()),
                    ('site_name', '视频站', NOW(), NOW()),
                    ('site_description', '免费视频站', NOW(), NOW())
                    ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), `updated_at`=NOW();";
                $conn->exec($sql);
                
                // 跳转到下一步
                header("Location: install.php?step=4");
                exit;
            } catch (PDOException $e) {
                $error = "创建数据表失败: " . $e->getMessage();
            }
            break;
            
        case 4: // 设置管理员
            if (!isset($_SESSION['db_config'])) {
                header("Location: install.php?step=2");
                exit;
            }
            
            $admin_username = $_POST['admin_username'] ?? 'admin';
            $admin_password = $_POST['admin_password'] ?? '123456';
            
            if (empty($admin_username) || empty($admin_password)) {
                $error = '请填写管理员用户名和密码';
                break;
            }
            
            $db_config = $_SESSION['db_config'];
            try {
                $dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};port={$db_config['port']}";
                $conn = new PDO($dsn, $db_config['username'], $db_config['password']);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // 创建管理员账号
                $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO `admin_users` (`username`, `password`, `created_at`, `updated_at`) VALUES 
                    (?, ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE `password`=VALUES(`password`), `updated_at`=NOW();";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$admin_username, $password_hash]);
                
                // 更新配置文件，标记为已安装
                if (file_exists('config.php')) {
                    $config_content = file_get_contents('config.php');
                    $config_content = str_replace("'installed' => false", "'installed' => true", $config_content);
                    file_put_contents('config.php', $config_content);
                }
                
                // 安装完成，跳转到最后一步
                header("Location: install.php?step=5");
                exit;
            } catch (PDOException $e) {
                $error = "创建管理员账号失败: " . $e->getMessage();
            }
            break;
    }
}

// 环境检测
function check_environment() {
    $requirements = [
        'PHP版本 >= 7.2' => version_compare(PHP_VERSION, '7.2.0') >= 0,
        'PDO扩展' => extension_loaded('PDO') && extension_loaded('pdo_mysql'),
        'JSON扩展' => extension_loaded('json'),
        'config.php可写' => is_writable('config.php') || (!file_exists('config.php') && is_writable('./')),
    ];
    
    return $requirements;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>安装向导 - 视频站管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="ass/layui/css/layui.css">
    <style>
        body {
            background-color: #f2f2f2;
            padding: 20px;
        }
        .install-box {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 2px;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,.05);
        }
        .install-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .install-steps {
            margin-bottom: 30px;
        }
        .install-content {
            padding: 20px 0;
        }
        .step-active {
            color: #009688;
            font-weight: bold;
        }
        .layui-form-label {
            width: 120px;
        }
        .layui-input-block {
            margin-left: 150px;
        }
    </style>
</head>
<body>
    <div class="install-box">
        <div class="install-header">
            <h1>视频站后台管理系统安装向导</h1>
        </div>
        
        <div class="install-steps layui-row">
            <?php foreach ($steps as $key => $val): ?>
                <div class="layui-col-xs<?php echo 12 / count($steps); ?> layui-col-sm<?php echo 12 / count($steps); ?>">
                    <div class="layui-card">
                        <div class="layui-card-body <?php echo $step == $key ? 'step-active' : ''; ?>" style="text-align: center;">
                            <?php echo $key . '. ' . $val; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="install-content">
            <?php if ($error): ?>
                <div class="layui-row">
                    <div class="layui-col-xs12">
                        <div class="layui-card">
                            <div class="layui-card-body" style="color: #FF5722;">
                                错误：<?php echo $error; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="layui-row">
                    <div class="layui-col-xs12">
                        <div class="layui-card">
                            <div class="layui-card-body" style="color: #009688;">
                                成功：<?php echo $success; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            <?php endif; ?>
            
            <?php if ($step == 1): // 环境检测 ?>
                <div class="layui-card">
                    <div class="layui-card-header">环境检测</div>
                    <div class="layui-card-body">
                        <table class="layui-table">
                            <colgroup>
                                <col width="150">
                                <col width="150">
                                <col>
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>检测项目</th>
                                    <th>结果</th>
                                    <th>说明</th>
                                </tr> 
                            </thead>
                            <tbody>
                                <?php foreach (check_environment() as $item => $result): ?>
                                    <tr>
                                        <td><?php echo $item; ?></td>
                                        <td>
                                            <?php if ($result): ?>
                                                <span style="color: #009688;">通过</span>
                                            <?php else: ?>
                                                <span style="color: #FF5722;">未通过</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!$result): ?>
                                                <span style="color: #FF5722;">该项检测未通过，可能会影响系统正常运行</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <?php
                    $can_continue = array_reduce(check_environment(), function ($carry, $item) {
                        return $carry && $item;
                    }, true);
                    ?>
                    
                    <?php if ($can_continue): ?>
                        <a href="install.php?step=2" class="layui-btn">下一步</a>
                    <?php else: ?>
                        <button class="layui-btn layui-btn-disabled">请解决以上问题后继续</button>
                    <?php endif; ?>
                </div>
            <?php elseif ($step == 2): // 数据库配置 ?>
                <div class="layui-card">
                    <div class="layui-card-header">数据库配置</div>
                    <div class="layui-card-body">
                        <form class="layui-form" action="install.php?step=2" method="post">
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据库主机</label>
                                <div class="layui-input-block">
                                    <input type="text" name="db_host" value="localhost" required lay-verify="required" placeholder="请输入数据库主机地址" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据库端口</label>
                                <div class="layui-input-block">
                                    <input type="text" name="db_port" value="3306" required lay-verify="required" placeholder="请输入数据库端口" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据库用户名</label>
                                <div class="layui-input-block">
                                    <input type="text" name="db_username" value="" required lay-verify="required" placeholder="请输入数据库用户名" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据库密码</label>
                                <div class="layui-input-block">
                                    <input type="password" name="db_password" value="" placeholder="请输入数据库密码" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据库名</label>
                                <div class="layui-input-block">
                                    <input type="text" name="db_database" value="" required lay-verify="required" placeholder="请输入数据库名" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button class="layui-btn" lay-submit>下一步</button>
                                    <a href="install.php?step=1" class="layui-btn layui-btn-primary">上一步</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php elseif ($step == 3): // 创建数据表 ?>
                <div class="layui-card">
                    <div class="layui-card-header">创建数据表</div>
                    <div class="layui-card-body">
                        <div class="layui-progress" lay-showpercent="true">
                            <div class="layui-progress-bar" lay-percent="100%"></div>
                        </div>
                        <div style="margin-top: 15px;">
                            <p>即将创建以下数据表：</p>
                            <ul>
                                <li>admin_users - 管理员表</li>
                                <li>settings - 系统设置表</li>
                                <li>statistics - 访问统计表</li>
                            </ul>
                        </div>
                        <form action="install.php?step=3" method="post" style="margin-top: 20px; text-align: center;">
                            <button class="layui-btn">开始创建</button>
                            <a href="install.php?step=2" class="layui-btn layui-btn-primary">上一步</a>
                        </form>
                    </div>
                </div>
            <?php elseif ($step == 4): // 管理员设置 ?>
                <div class="layui-card">
                    <div class="layui-card-header">管理员设置</div>
                    <div class="layui-card-body">
                        <form class="layui-form" action="install.php?step=4" method="post">
                            <div class="layui-form-item">
                                <label class="layui-form-label">管理员用户名</label>
                                <div class="layui-input-block">
                                    <input type="text" name="admin_username" value="admin" required lay-verify="required" placeholder="请输入管理员用户名" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">管理员密码</label>
                                <div class="layui-input-block">
                                    <input type="password" name="admin_password" value="123456" required lay-verify="required" placeholder="请输入管理员密码" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button class="layui-btn" lay-submit>完成安装</button>
                                    <a href="install.php?step=3" class="layui-btn layui-btn-primary">上一步</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php elseif ($step == 5): // 安装完成 ?>
                <div class="layui-card">
                    <div class="layui-card-header">安装完成</div>
                    <div class="layui-card-body">
                        <div style="text-align: center;">
                            <i class="layui-icon layui-icon-ok-circle" style="font-size: 100px; color: #009688;"></i>
                            <h2 style="margin: 20px 0;">系统安装成功！</h2>
                            <p>您可以开始使用系统了。</p>
                            <div style="margin-top: 30px;">
                                <a href="qxadmin/login.php" class="layui-btn">进入后台</a>
                                <a href="index.php" class="layui-btn layui-btn-primary">访问前台</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="ass/layui/layui.js"></script>
    <script>
        layui.use(['element', 'form'], function() {
            var element = layui.element;
            var form = layui.form;
            
            // 刷新渲染
            element.render();
            form.render();
        });
    </script>
</body>
</html>
