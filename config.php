<?php
/**
 * 数据库配置文件
 * 安装时会自动修改此文件
 */

// 数据库连接配置
$db_config = [
    'host'      => 'localhost',      // 数据库主机
    'username'  => 'dyz_today',  // 数据库用户名
    'password'  => 'TBb9HxBEip7sYdKT',  // 数据库密码
    'database'  => 'dyz_today',  // 数据库名
    'port'      => '3306',      // 端口
    'charset'   => 'utf8mb4'         // 字符集
];

// 站点配置
$site_config = [
    'installed' => true,  // 安装状态，安装完成后会修改为true
    'version'   => '1.0.0' // 系统版本
];

// 获取数据库连接
function get_db_connection() {
    global $db_config;
    
    try {
        $dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};port={$db_config['port']};charset={$db_config['charset']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        return new PDO($dsn, $db_config['username'], $db_config['password'], $options);
    } catch (PDOException $e) {
        error_log("数据库连接失败: " . $e->getMessage());
        return false;
    }
}

// 检查是否已安装
function is_installed() {
    global $site_config;
    return $site_config['installed'] === true;
}

// 获取系统配置
function get_setting($key, $default = '') {
    $db = get_db_connection();
    if (!$db) {
        return $default;
    }
    
    try {
        $stmt = $db->prepare("SELECT value FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        return $result ? $result['value'] : $default;
    } catch (PDOException $e) {
        error_log("获取设置失败: " . $e->getMessage());
        return $default;
    }
}

// 更新系统配置
function update_setting($key, $value) {
    $db = get_db_connection();
    if (!$db) {
        return false;
    }
    
    try {
        // 检查配置是否存在
        $stmt = $db->prepare("SELECT COUNT(*) FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $exists = (int)$stmt->fetchColumn() > 0;
        
        if ($exists) {
            // 更新现有配置
            $stmt = $db->prepare("UPDATE settings SET value = ?, updated_at = NOW() WHERE `key` = ?");
            return $stmt->execute([$value, $key]);
        } else {
            // 插入新配置
            $stmt = $db->prepare("INSERT INTO settings (`key`, value, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            return $stmt->execute([$key, $value]);
        }
    } catch (PDOException $e) {
        error_log("更新设置失败: " . $e->getMessage());
        return false;
    }
}
