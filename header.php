<?php
/**
 * 网站头部文件
 */
// 引入配置文件
if (file_exists('config.php')) {
    include_once 'config.php';
    
    // 检查是否已安装
    if (is_installed()) {
        // 记录访问统计
        include_once 'tracker.php';
        
        // 获取设置
        $site_name = get_setting('site_name', '视频站');
        $site_description = get_setting('site_description', '免费视频站');
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="referrer" content="no-referrer">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <link rel="stylesheet" href="./ass/321.css">
    <title><?php echo isset($site_name) ? htmlspecialchars($site_name) : 'VIP视频库'; ?></title>
</head>
