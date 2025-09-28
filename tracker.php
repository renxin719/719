<?php
/**
 * 前台访问统计记录
 */

// 引入配置文件
if (file_exists('config.php')) {
    include_once 'config.php';
    
    // 检查是否已安装
    if (!is_installed()) {
        return;
    }
    
    // 记录访问统计
    function record_visit() {
        $db = get_db_connection();
        if (!$db) {
            return false;
        }
        
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $page = $_SERVER['REQUEST_URI'] ?? '';
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            
            $stmt = $db->prepare("INSERT INTO statistics (ip, user_agent, page, referer, created_at) VALUES (?, ?, ?, ?, NOW())");
            return $stmt->execute([$ip, $user_agent, $page, $referer]);
        } catch (PDOException $e) {
            error_log("记录访问统计失败: " . $e->getMessage());
            return false;
        }
    }
    
    // 记录访问
    record_visit();
}
