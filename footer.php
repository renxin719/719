<?php
/**
 * 网站底部文件
 */
// 如果$popup_url变量未定义，则获取弹窗跳转链接
if (!isset($popup_url)) {
    $popup_url = isset($GLOBALS['db_config']) ? get_setting('popup_url') : '';
}
?>
    <footer>
        <p>© <?php echo date('Y'); ?> <?php echo isset($site_name) ? htmlspecialchars($site_name) : '漫画星球'; ?> · <?php echo isset($site_description) ? htmlspecialchars($site_description) : '专属于各位漫画迷的资源'; ?></p>
    </footer>
</div>

<script type="text/javascript" src="./ass/jquery.min.js.下载"></script>
<script>
// 获取元素
const recruitBtn = document.getElementById('recruitBtn');
const imageModal = document.getElementById('imageModal');
const closeBtn = document.querySelector('.close-btn');

// 如果按钮存在则添加事件
if (recruitBtn) {
    recruitBtn.addEventListener('click', function() {
        imageModal.style.display = 'block';
    });
}

// 如果关闭按钮存在则添加事件
if (closeBtn) {
    closeBtn.addEventListener('click', function() {
        imageModal.style.display = 'none';
    });
}

// 点击弹窗外部区域关闭弹窗
window.addEventListener('click', function(event) {
    if (imageModal && event.target === imageModal) {
        imageModal.style.display = 'none';
    }
});
</script>

<script>
// 获取DOM元素
const videoCards = document.querySelectorAll('.video-card');
const modalOverlay = document.getElementById('modalOverlay');
const closeModal = document.getElementById('closeModal');
const modalTitle = document.getElementById('modalTitle');
const purchaseOptions = document.querySelectorAll('.purchase-option');
const loadingBar = document.getElementById('loadingBar');
const loadMoreButtons = document.querySelectorAll('.load-more-btn');

// 如果页面中没有定义weekPurchaseUrl变量，则在这里定义
if (typeof weekPurchaseUrl === 'undefined') {
    const weekPurchaseUrl = '<?php echo htmlspecialchars($popup_url); ?>'; 
}

// 视频点击事件
videoCards.forEach(card => {
    card.addEventListener('click', function() {
        const videoId = this.getAttribute('data-video');
        const videoTitle = this.querySelector('h3').textContent;
        
        modalTitle.textContent = `获取《${videoTitle}》`;
        modalOverlay.classList.add('active');
    });
});

// 加载更多按钮点击事件处理函数
loadMoreButtons.forEach(button => {
    button.addEventListener('click', function() {
        // 显示购买弹窗
        modalTitle.textContent = "解锁更多动漫内容";
        modalOverlay.classList.add('active');
        
        // 按钮动画效果
        const originalHTML = this.innerHTML;
        this.innerHTML = '加载中...';
        setTimeout(() => {
            this.innerHTML = originalHTML;
        }, 1500);
    });
});

// 关闭弹窗
if (closeModal) {
    closeModal.addEventListener('click', function() {
        modalOverlay.classList.remove('active');
    });
}

// 点击背景关闭弹窗
if (modalOverlay) {
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            modalOverlay.classList.remove('active');
        }
    });
}

// 购买选项点击
purchaseOptions.forEach(option => {
    option.addEventListener('click', function() {
        // 显示加载动画
        loadingBar.style.width = '0%';
        loadingBar.style.transition = 'none';
        setTimeout(() => {
            loadingBar.style.transition = 'width 1s ease-in-out';
            loadingBar.style.width = '100%';
        }, 10);
        
        // 1秒后跳转
        setTimeout(() => {
            window.location.href = weekPurchaseUrl;
        }, 1000);
    });
});

// 按ESC键关闭弹窗
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modalOverlay) {
        modalOverlay.classList.remove('active');
    }
});
</script>
</body>
</html>
