<?php
/**
 * 系统设置页面
 */
require_once 'common.php';

// 处理表单提交
$message = '';
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 保存设置
    $popup_url = isset($_POST['popup_url']) ? $_POST['popup_url'] : '';
    $site_name = isset($_POST['site_name']) ? $_POST['site_name'] : '';
    $site_description = isset($_POST['site_description']) ? $_POST['site_description'] : '';
    
    // 更新设置
    $result1 = update_setting('popup_url', $popup_url);
    $result2 = update_setting('site_name', $site_name);
    $result3 = update_setting('site_description', $site_description);
    
    if ($result1 && $result2 && $result3) {
        $message = '设置保存成功';
        $type = 'success';
    } else {
        $message = '部分或全部设置保存失败';
        $type = 'error';
    }
}

// 获取当前设置
$popup_url = get_setting('popup_url', 'https://www.baidu.com/');
$site_name = get_setting('site_name', '视频站');
$site_description = get_setting('site_description', '免费视频站');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>系统设置 - 视频站后台管理系统</title>
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
                        <i class="layui-icon layui-icon-set"></i>
                        系统设置
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
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                <i class="layui-icon layui-icon-set"></i>
                                系统设置
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-tab" lay-filter="setting-tabs">
                                    <ul class="layui-tab-title">
                                        <li class="layui-this">基本设置</li>
                                        <li>弹窗设置</li>
                                        <li>其他设置</li>
                                    </ul>
                                    <div class="layui-tab-content">
                                        <div class="layui-tab-item layui-show">
                                            <form class="layui-form" action="settings.php" method="post">
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">网站名称</label>
                                                    <div class="layui-input-block">
                                                        <input type="text" name="site_name" value="<?php echo htmlspecialchars($site_name); ?>" required lay-verify="required" placeholder="请输入网站名称" autocomplete="off" class="layui-input">
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">网站描述</label>
                                                    <div class="layui-input-block">
                                                        <input type="text" name="site_description" value="<?php echo htmlspecialchars($site_description); ?>" placeholder="请输入网站描述" autocomplete="off" class="layui-input">
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">弹窗跳转链接</label>
                                                    <div class="layui-input-block">
                                                        <input type="text" name="popup_url" value="<?php echo htmlspecialchars($popup_url); ?>" required lay-verify="required|url" placeholder="请输入弹窗跳转链接" autocomplete="off" class="layui-input">
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <div class="layui-input-block">
                                                        <button class="layui-btn" lay-submit>保存设置</button>
                                                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="layui-tab-item">
                                            <div class="layui-form">
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">弹窗跳转链接</label>
                                                    <div class="layui-input-block">
                                                        <input type="text" id="popup_url_preview" value="<?php echo htmlspecialchars($popup_url); ?>" class="layui-input" readonly>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item">
                                                    <label class="layui-form-label">测试链接</label>
                                                    <div class="layui-input-block">
                                                        <a href="<?php echo htmlspecialchars($popup_url); ?>" target="_blank" class="layui-btn layui-btn-sm">打开链接</a>
                                                        <a href="#" id="test_popup" class="layui-btn layui-btn-sm layui-btn-normal">测试弹窗</a>
                                                    </div>
                                                </div>
                                                <div class="layui-form-item layui-form-text">
                                                    <label class="layui-form-label">前台引用代码</label>
                                                    <div class="layui-input-block">
                                                        <textarea class="layui-textarea" readonly rows="10">
// 修改main.js中的跳转链接
const weekPurchaseUrl = '<?php echo htmlspecialchars($popup_url); ?>'; 

// 也可以直接添加到前台页面
<script>
document.querySelectorAll('.purchase-option').forEach(option => {
    option.addEventListener('click', function() {
        window.location.href = '<?php echo htmlspecialchars($popup_url); ?>';
    });
});
</script>
                                                        </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-tab-item">
                                            <div class="layui-card">
                                                <div class="layui-card-header">其他设置</div>
                                                <div class="layui-card-body">
                                                    <p>这里可以放置其他设置项，如安全设置、缓存设置等。</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

    <!-- 弹窗测试 -->
    <div id="popup_test_container" style="display: none;">
        <div class="layui-card" style="margin: 0;">
            <div class="layui-card-header">模拟前台弹窗</div>
            <div class="layui-card-body">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h3>获取《测试视频》</h3>
                    <p style="color: #999; margin-bottom: 20px;">立即获取，畅享海量内容</p>
                </div>
                <div style="background: #f2f2f2; padding: 15px; border-radius: 5px; cursor: pointer; text-align: center; margin-bottom: 15px;">
                    <h4 style="color: #FF5722;">免费获取</h4>
                    <div style="font-size: 18px; font-weight: bold; margin: 5px 0;">简单几步 直接打开</div>
                    <div style="font-size: 12px; color: #999;">还有100G网盘资源限时分享</div>
                </div>
                <div style="margin-top: 10px; text-align: center;">
                    <p>点击上方选项将跳转到:</p>
                    <p style="word-break: break-all; color: #1E9FFF;"><?php echo htmlspecialchars($popup_url); ?></p>
                </div>
            </div>
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
                url: [
                    /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/,
                    '请输入有效的URL'
                ]
            });
            
            // 测试弹窗
            $('#test_popup').on('click', function(e){
                e.preventDefault();
                
                layer.open({
                    type: 1,
                    title: false,
                    closeBtn: 1,
                    shadeClose: true,
                    area: ['400px', 'auto'],
                    content: $('#popup_test_container'),
                    success: function(layero, index){
                        // 点击弹窗内容区域跳转
                        $(layero).find('.layui-card-body div:eq(1)').on('click', function(){
                            layer.confirm('确定要打开链接吗？', {
                                btn: ['确定', '取消']
                            }, function(){
                                window.open($('#popup_url_preview').val());
                                layer.closeAll();
                            });
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
