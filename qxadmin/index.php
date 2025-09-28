<?php
/**
 * 后台主页
 */
require_once 'common.php';

// 获取统计摘要
$stats = get_statistics_summary();

// 获取每日统计数据
$db = get_db_connection();
$daily_stats = [];

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
        $stmt->execute([7]);
        $daily_stats = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("获取每日统计数据失败: " . $e->getMessage());
    }
}

// 准备图表数据
$chart_dates = [];
$chart_visits = [];
$chart_ips = [];

foreach ($daily_stats as $stat) {
    $chart_dates[] = date('m-d', strtotime($stat['date']));
    $chart_visits[] = $stat['visits'];
    $chart_ips[] = $stat['unique_ips'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>仪表盘 - 视频站后台管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="../ass/layui/css/layui.css">
    <link rel="stylesheet" href="mobile.css">
    <link rel="stylesheet" href="responsive.css">
    <script src="../ass/echarts.min.js"></script>
    <style>
        .layui-card-header {
            display: flex;
            align-items: center;
        }
        .layui-card-header .layui-icon {
            margin-right: 8px;
            font-size: 18px;
        }
        .stat-card {
            text-align: center;
            padding: 10px 0;
        }
        .stat-card .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #009688;
            margin-bottom: 5px;
        }
        .stat-card .stat-title {
            font-size: 14px;
            color: #666;
        }
        .system-info-item {
            margin-bottom: 10px;
            display: flex;
        }
        .system-info-label {
            width: 120px;
            font-weight: bold;
            color: #333;
        }
        .system-info-value {
            flex: 1;
            color: #666;
        }
        .dashboard-chart {
            height: 300px;
        }
    </style>
</head>
<body>
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <div class="layui-logo layui-hide-xs">视频站后台管理</div>
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layui-hide-xs">
                    <a href="javascript:;">
                        <i class="layui-icon layui-icon-home"></i>
                        仪表盘
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
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                <i class="layui-icon layui-icon-chart"></i>
                                数据概览
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-row layui-col-space15">
                                    <div class="layui-col-md3 layui-col-sm6">
                                        <div class="stat-card">
                                            <div class="stat-number"><?php echo number_format($stats['total_visits']); ?></div>
                                            <div class="stat-title">总访问量</div>
                                        </div>
                                    </div>
                                    <div class="layui-col-md3 layui-col-sm6">
                                        <div class="stat-card">
                                            <div class="stat-number"><?php echo number_format($stats['today_visits']); ?></div>
                                            <div class="stat-title">今日访问</div>
                                        </div>
                                    </div>
                                    <div class="layui-col-md3 layui-col-sm6">
                                        <div class="stat-card">
                                            <div class="stat-number"><?php echo number_format($stats['yesterday_visits']); ?></div>
                                            <div class="stat-title">昨日访问</div>
                                        </div>
                                    </div>
                                    <div class="layui-col-md3 layui-col-sm6">
                                        <div class="stat-card">
                                            <div class="stat-number"><?php echo number_format($stats['unique_ips']); ?></div>
                                            <div class="stat-title">独立IP数</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="layui-col-md8">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                <i class="layui-icon layui-icon-chart-screen"></i>
                                访问统计趋势
                            </div>
                            <div class="layui-card-body">
                                <div id="visitChart" class="dashboard-chart"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="layui-col-md4">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                <i class="layui-icon layui-icon-engine"></i>
                                系统信息
                            </div>
                            <div class="layui-card-body">
                                <?php $system_info = get_system_info(); ?>
                                <div class="system-info-item">
                                    <div class="system-info-label">PHP版本：</div>
                                    <div class="system-info-value"><?php echo $system_info['php_version']; ?></div>
                                </div>
                                <div class="system-info-item">
                                    <div class="system-info-label">MySQL版本：</div>
                                    <div class="system-info-value"><?php echo $system_info['mysql_version']; ?></div>
                                </div>
                                <div class="system-info-item">
                                    <div class="system-info-label">服务器软件：</div>
                                    <div class="system-info-value"><?php echo $system_info['server_software']; ?></div>
                                </div>
                                <div class="system-info-item">
                                    <div class="system-info-label">上传限制：</div>
                                    <div class="system-info-value"><?php echo $system_info['max_upload_size']; ?></div>
                                </div>
                                <div class="system-info-item">
                                    <div class="system-info-label">执行时间限制：</div>
                                    <div class="system-info-value"><?php echo $system_info['max_execution_time']; ?></div>
                                </div>
                                <div class="system-info-item">
                                    <div class="system-info-label">当前时间：</div>
                                    <div class="system-info-value"><?php echo date('Y-m-d H:i:s'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                <i class="layui-icon layui-icon-log"></i>
                                最近访问记录
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-table-responsive">
                                    <table class="layui-table">
                                        <thead>
                                            <tr>
                                                <th>IP地址</th>
                                                <th>访问页面</th>
                                                <th>来源</th>
                                                <th>时间</th>
                                            </tr> 
                                        </thead>
                                        <tbody>
                                            <?php foreach (get_recent_visits(5) as $visit): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($visit['ip']); ?></td>
                                                    <td><?php echo htmlspecialchars($visit['page']); ?></td>
                                                    <td><?php echo $visit['referer'] ? htmlspecialchars($visit['referer']) : '直接访问'; ?></td>
                                                    <td><?php echo $visit['created_at']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="text-align: center;">
                                    <a href="statistics.php" class="layui-btn layui-btn-sm">查看更多记录</a>
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

    <script src="../ass/layui/layui.js"></script>
    <script src="mobile.js"></script>
    <script>
        layui.use(['element', 'layer', 'jquery'], function(){
            var element = layui.element;
            var layer = layui.layer;
            
            // 初始化图表
            var visitChart = echarts.init(document.getElementById('visitChart'));
            
            // 图表配置
            var option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['访问量', '独立IP']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: <?php echo json_encode($chart_dates); ?>
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                    {
                        name: '访问量',
                        type: 'line',
                        data: <?php echo json_encode($chart_visits); ?>,
                        areaStyle: {},
                        smooth: true
                    },
                    {
                        name: '独立IP',
                        type: 'line',
                        data: <?php echo json_encode($chart_ips); ?>,
                        areaStyle: {},
                        smooth: true
                    }
                ]
            };
            
            // 使用配置项和数据显示图表
            visitChart.setOption(option);
            
            // 保存图表实例
            window.chartInstances = window.chartInstances || {};
            window.chartInstances.visitChart = visitChart;
            
            // 窗口大小改变时重置图表大小
            window.addEventListener('resize', function() {
                visitChart.resize();
            });
        });
    </script>
</body>
</html>
