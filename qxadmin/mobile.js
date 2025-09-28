/**
 * 后台手机自适应脚本
 */

// 添加菜单切换功能
layui.use(['jquery', 'element'], function(){
    var $ = layui.$;
    var element = layui.element;
    
    // 检查是否为移动设备
    var isMobile = function() {
        return window.innerWidth <= 768;
    };
    
    // 添加移动端菜单按钮
    if ($('.layui-header').length && $('.mobile-menu').length === 0) {
        $('.layui-layout-left').prepend(
            '<li class="layui-nav-item mobile-menu layui-hide-md">' +
            '<a href="javascript:;">' +
            '<i class="layui-icon layui-icon-spread-left"></i> 菜单' +
            '</a>' +
            '</li>'
        );
        
        // 刷新导航渲染
        element.render('nav');
    }
    
    // 绑定菜单点击事件
    $(document).on('click', '.mobile-menu', function() {
        toggleSidebar();
    });
    
    // 绑定遮罩层点击事件
    $(document).on('click', '.mobile-mask', function() {
        toggleSidebar();
    });
    
    // 点击侧边栏链接后关闭菜单
    $(document).on('click', '.layui-side a', function() {
        if (isMobile()) {
            toggleSidebar();
        }
    });
    
    // 切换侧边栏
    function toggleSidebar() {
        var $side = $('.layui-side');
        var $body = $('.layui-body');
        var $footer = $('.layui-footer');
        
        $side.toggleClass('show');
        $body.toggleClass('shrink');
        $footer.toggleClass('shrink');
        
        // 添加/删除遮罩层
        if ($side.hasClass('show')) {
            if ($('.mobile-mask').length === 0) {
                $('body').append('<div class="mobile-mask"></div>');
            }
        } else {
            $('.mobile-mask').remove();
        }
    }
    
    // 窗口大小改变时处理
    $(window).resize(function() {
        if (!isMobile()) {
            $('.layui-side').removeClass('show');
            $('.layui-body').removeClass('shrink');
            $('.layui-footer').removeClass('shrink');
            $('.mobile-mask').remove();
        }
    });
    
    // 初始化图表大小
    function resizeCharts() {
        if (window.echarts) {
            for (var chartName in window.chartInstances) {
                if (window.chartInstances.hasOwnProperty(chartName)) {
                    window.chartInstances[chartName].resize();
                }
            }
        }
    }
    
    // 储存图表实例
    window.chartInstances = {};
    
    // 注册resize事件
    $(window).resize(function() {
        resizeCharts();
    });
});
