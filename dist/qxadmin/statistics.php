<?php
/**
 * 统计数据页面
 */
require_once 'common.php';

// 获取分页参数
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// 获取统计数据
$db = get_db_connection();
$total = 0;
$stats = [];

if ($db) {
    try {
        // 获取总记录数
        $stmt = $db->query("SELECT COUNT(*) FROM statistics");
        $total = (int)$stmt->fetchColumn();
        
        // 获取当前页数据
        $stmt = $db->prepare("SELECT * FROM statistics ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $stats = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("获取统计数据失败: " . $e->getMessage());
    }
}

// 计算总页数
$total_pages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>统计数据 - 视频站后台管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="../ass/layui/css/layui.css">
    <link rel="stylesheet" href="mobile.css">
    <link rel="stylesheet" href="responsive.css">
    <script src="../ass/echarts.min.js"></script>
</head>
<body>
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <div class="layui-logo layui-hide-xs">视频站后台管理</div>
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layui-hide-xs">
                    <a href="javascript:;">
                        <i class="layui-icon layui-icon-chart"></i>
                        统计数据
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
                                访问统计数据
                            </div>
                            <div class="layui-card-body">
                                <div class="layui-tab" lay-filter="stat-tabs">
                                    <ul class="layui-tab-title">
                                        <li class="layui-this">访问记录</li>
                                        <li>每日统计</li>
                                        <li>IP分析</li>
                                    </ul>
                                    <div class="layui-tab-content">
                                        <div class="layui-tab-item layui-show">
                                            <div class="layui-table-responsive">
                                                <table class="layui-table">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>IP地址</th>
                                                            <th>访问页面</th>
                                                            <th>来源</th>
                                                            <th>用户代理</th>
                                                            <th>时间</th>
                                                        </tr> 
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($stats as $stat): ?>
                                                            <tr>
                                                                <td><?php echo $stat['id']; ?></td>
                                                                <td><?php echo htmlspecialchars($stat['ip']); ?></td>
                                                                <td><?php echo htmlspecialchars($stat['page']); ?></td>
                                                                <td><?php echo $stat['referer'] ? htmlspecialchars($stat['referer']) : '直接访问'; ?></td>
                                                                <td><?php echo htmlspecialchars(substr($stat['user_agent'], 0, 50) . (strlen($stat['user_agent']) > 50 ? '...' : '')); ?></td>
                                                                <td><?php echo $stat['created_at']; ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <!-- 分页 -->
                                            <?php if ($total_pages > 1): ?>
                                                <div id="pagination"></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="layui-tab-item">
                                            <div id="dailyChart" style="height: 400px;"></div>
                                        </div>
                                        <div class="layui-tab-item">
                                            <div id="ipChart" style="height: 400px;"></div>
                                            <div class="layui-table-responsive">
                                                <table class="layui-table" id="ipTable">
                                                    <thead>
                                                        <tr>
                                                            <th>IP地址</th>
                                                            <th>访问次数</th>
                                                            <th>最近访问</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- 动态加载 -->
                                                    </tbody>
                                                </table>
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

    <script src="../ass/layui/layui.js"></script>
    <script src="mobile.js"></script>
    <script>
        layui.use(['element', 'layer', 'laypage', 'jquery'], function(){
            var element = layui.element;
            var layer = layui.layer;
            var laypage = layui.laypage;
            var $ = layui.$;
            
            // 初始化分页
            <?php if ($total_pages > 1): ?>
            laypage.render({
                elem: 'pagination',
                count: <?php echo $total; ?>,
                limit: <?php echo $limit; ?>,
                curr: <?php echo $page; ?>,
                theme: '#009688',
                layout: ['prev', 'page', 'next', 'skip', 'count', 'limit'],
                jump: function(obj, first){
                    if(!first){
                        location.href = 'statistics.php?page=' + obj.curr;
                    }
                }
            });
            <?php endif; ?>
            
            // 监听选项卡切换
            element.on('tab(stat-tabs)', function(data){
                if(data.index === 1 && !window.dailyChartRendered){
                    loadDailyStats();
                    window.dailyChartRendered = true;
                } else if(data.index === 2 && !window.ipChartRendered){
                    loadIpStats();
                    window.ipChartRendered = true;
                }
            });
            
            // 页面加载完立即加载第一个图表
            loadDailyStats();
            window.dailyChartRendered = true;
            
            // 加载每日统计数据
            function loadDailyStats(){
                // 显示加载中
                layer.load(2);
                
                $.getJSON('api.php?action=daily_stats&days=30', function(res){
                    if(res.code === 0){
                        renderDailyChart(res.data);
                    } else {
                        layer.msg('加载每日统计数据失败');
                    }
                    layer.closeAll('loading');
                });
            }
            
            // 渲染每日统计图表
            function renderDailyChart(data){
                var dates = [];
                var visits = [];
                var ips = [];
                
                data.forEach(function(item){
                    dates.push(item.date);
                    visits.push(item.visits);
                    ips.push(item.unique_ips);
                });
                
                var dailyChart = echarts.init(document.getElementById('dailyChart'));
                var option = {
                    title: {
                        text: '30天访问趋势'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['总访问量', '独立IP']
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
                        data: dates
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            name: '总访问量',
                            type: 'line',
                            data: visits,
                            areaStyle: {},
                            smooth: true
                        },
                        {
                            name: '独立IP',
                            type: 'line',
                            data: ips,
                            areaStyle: {},
                            smooth: true
                        }
                    ]
                };
                
                dailyChart.setOption(option);
                
                // 保存图表实例
                window.chartInstances = window.chartInstances || {};
                window.chartInstances.dailyChart = dailyChart;
                
                window.addEventListener('resize', function(){
                    dailyChart.resize();
                });
            }
            
            // 加载IP统计数据
            function loadIpStats(){
                // 显示加载中
                layer.load(2);
                
                $.getJSON('api.php?action=ip_stats', function(res){
                    if(res.code === 0){
                        renderIpChart(res.data);
                        renderIpTable(res.data);
                    } else {
                        layer.msg('加载IP统计数据失败');
                    }
                    layer.closeAll('loading');
                });
            }
            
            // 渲染IP统计图表
            function renderIpChart(data){
                var chartData = [];
                
                // 只取前10个数据用于饼图展示
                var topData = data.slice(0, 10);
                
                topData.forEach(function(item){
                    chartData.push({
                        name: item.ip,
                        value: parseInt(item.count)
                    });
                });
                
                var ipChart = echarts.init(document.getElementById('ipChart'));
                var option = {
                    title: {
                        text: '访问IP统计',
                        left: 'center'
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: '{a} <br/>{b} : {c} ({d}%)'
                    },
                    legend: {
                        orient: 'vertical',
                        left: 'left',
                        data: topData.map(function(item){ return item.ip; })
                    },
                    series: [
                        {
                            name: '访问次数',
                            type: 'pie',
                            radius: '55%',
                            center: ['50%', '60%'],
                            data: chartData,
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            }
                        }
                    ]
                };
                
                ipChart.setOption(option);
                
                // 保存图表实例
                window.chartInstances = window.chartInstances || {};
                window.chartInstances.ipChart = ipChart;
                
                window.addEventListener('resize', function(){
                    ipChart.resize();
                });
            }
            
            // 渲染IP统计表格
            function renderIpTable(data){
                var html = '';
                
                data.forEach(function(item){
                    html += '<tr>';
                    html += '<td>' + item.ip + '</td>';
                    html += '<td>' + item.count + '</td>';
                    html += '<td>' + item.last_visit + '</td>';
                    html += '</tr>';
                });
                
                $('#ipTable tbody').html(html);
            }
        });
    </script>
</body>
</html>
