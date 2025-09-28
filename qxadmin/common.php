<?php
/**
 * 后台公共文件
 */
session_start();

// 引入配置文件
require_once '../config.php';

// 检查是否已安装
if (!is_installed()) {
    header('Location: ../install.php');
    exit;
}

// 检查是否已登录
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

/**
 * 获取后台菜单
 */
function get_admin_menu() {
    return [
        [
            'name' => '仪表盘',
            'icon' => 'layui-icon-home',
            'url' => 'index.php',
            'active' => is_current_page('index.php')
        ],
        [
            'name' => '统计数据',
            'icon' => 'layui-icon-chart',
            'url' => 'statistics.php',
            'active' => is_current_page('statistics.php')
        ],
        [
            'name' => '系统设置',
            'icon' => 'layui-icon-set',
            'url' => 'settings.php',
            'active' => is_current_page('settings.php')
        ],
        [
            'name' => '管理员',
            'icon' => 'layui-icon-user',
            'url' => 'admin.php',
            'active' => is_current_page('admin.php')
        ],
        [
            'name' => '退出登录',
            'icon' => 'layui-icon-logout',
            'url' => 'logout.php',
            'active' => false
        ]
    ];
}

/**
 * 判断当前页面
 */
function is_current_page($page) {
    $current_page = basename($_SERVER['SCRIPT_NAME']);
    return $current_page === $page;
}

/**
 * 获取系统基础信息
 */
function get_system_info() {
    return [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? '',
        'mysql_version' => get_mysql_version(),
        'max_upload_size' => ini_get('upload_max_filesize'),
        'max_execution_time' => ini_get('max_execution_time') . '秒'
    ];
}

/**
 * 获取MySQL版本
 */
function get_mysql_version() {
    $db = get_db_connection();
    if (!$db) {
        return '未知';
    }
    
    try {
        return $db->getAttribute(PDO::ATTR_SERVER_VERSION);
    } catch (PDOException $e) {
        return '未知';
    }
}

/**
 * 获取统计数据
 */
function get_statistics_summary() {
    $db = get_db_connection();
    if (!$db) {
        return [
            'total_visits' => 0,
            'today_visits' => 0,
            'yesterday_visits' => 0,
            'unique_ips' => 0
        ];
    }
    
    try {
        // 总访问量
        $stmt = $db->query("SELECT COUNT(*) FROM statistics");
        $total_visits = (int)$stmt->fetchColumn();
        
        // 今日访问量
        $stmt = $db->query("SELECT COUNT(*) FROM statistics WHERE DATE(created_at) = CURDATE()");
        $today_visits = (int)$stmt->fetchColumn();
        
        // 昨日访问量
        $stmt = $db->query("SELECT COUNT(*) FROM statistics WHERE DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
        $yesterday_visits = (int)$stmt->fetchColumn();
        
        // 唯一IP数
        $stmt = $db->query("SELECT COUNT(DISTINCT ip) FROM statistics");
        $unique_ips = (int)$stmt->fetchColumn();
        
        return [
            'total_visits' => $total_visits,
            'today_visits' => $today_visits,
            'yesterday_visits' => $yesterday_visits,
            'unique_ips' => $unique_ips
        ];
    } catch (PDOException $e) {
        error_log("获取统计摘要失败: " . $e->getMessage());
        return [
            'total_visits' => 0,
            'today_visits' => 0,
            'yesterday_visits' => 0,
            'unique_ips' => 0
        ];
    }
}

/**
 * 获取最近的访问记录
 */
function get_recent_visits($limit = 10) {
    $db = get_db_connection();
    if (!$db) {
        return [];
    }
    
    try {
        $stmt = $db->prepare("SELECT * FROM statistics ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("获取最近访问记录失败: " . $e->getMessage());
        return [];
    }
}

/**
 * 获取每日访问统计
 */
function get_daily_stats($days = 7) {
    $db = get_db_connection();
    if (!$db) {
        return [];
    }
    
    try {
        $stmt = $db->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as visits,
                COUNT(DISTINCT ip) as unique_ips
            FROM statistics
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date
        ");
        $stmt->bindValue(1, $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("获取每日统计数据失败: " . $e->getMessage());
        return [];
    }
}

/**
 * 安全过滤输入
 */
function safe_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
