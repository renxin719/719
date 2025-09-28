<?php
/**
 * API接口文件
 */
require_once 'common.php';

// 设置JSON响应头
header('Content-Type: application/json');

// 获取请求参数
$action = $_GET['action'] ?? '';

// 处理不同的API请求
switch ($action) {
    case 'daily_stats':
        // 获取天数参数
        $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
        if ($days < 1) $days = 30;
        if ($days > 90) $days = 90;
        
        // 获取每日统计数据
        $db = get_db_connection();
        $stats = [];
        
        if ($db) {
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
                $stmt->execute([$days]);
                $stats = $stmt->fetchAll();
            } catch (PDOException $e) {
                echo json_encode([
                    'code' => 500,
                    'msg' => '获取统计数据失败: ' . $e->getMessage(),
                    'data' => []
                ]);
                exit;
            }
        }
        
        echo json_encode([
            'code' => 0,
            'msg' => 'success',
            'data' => $stats
        ]);
        break;
        
    case 'ip_stats':
        // 获取IP统计数据
        $db = get_db_connection();
        $stats = [];
        
        if ($db) {
            try {
                $stmt = $db->query("
                    SELECT 
                        ip, 
                        COUNT(*) as count,
                        MAX(created_at) as last_visit
                    FROM statistics 
                    GROUP BY ip 
                    ORDER BY count DESC, last_visit DESC
                    LIMIT 100
                ");
                $stats = $stmt->fetchAll();
            } catch (PDOException $e) {
                echo json_encode([
                    'code' => 500,
                    'msg' => '获取IP统计失败: ' . $e->getMessage(),
                    'data' => []
                ]);
                exit;
            }
        }
        
        echo json_encode([
            'code' => 0,
            'msg' => 'success',
            'data' => $stats
        ]);
        break;
        
    case 'update_setting':
        // 检查是否为POST请求
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'code' => 405,
                'msg' => '方法不允许'
            ]);
            exit;
        }
        
        // 获取参数
        $key = $_POST['key'] ?? '';
        $value = $_POST['value'] ?? '';
        
        if (empty($key)) {
            echo json_encode([
                'code' => 400,
                'msg' => '缺少参数'
            ]);
            exit;
        }
        
        // 更新设置
        $result = update_setting($key, $value);
        
        echo json_encode([
            'code' => $result ? 0 : 500,
            'msg' => $result ? 'success' : '更新设置失败'
        ]);
        break;
        
    default:
        echo json_encode([
            'code' => 404,
            'msg' => '未知的操作'
        ]);
        break;
}