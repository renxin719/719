// Slideshow functionality
let slideIndex = 0; // Start with the 4th slide (index 3)
showSlides(slideIndex);

// Next/previous controls
function changeSlide(n) {
    showSlides(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
    showSlides(slideIndex = n - 1);
}

function showSlides(n) {
    let i;
    const slides = document.getElementsByClassName("slideshow-slide");
    const dots = document.getElementsByClassName("slideshow-dot");
    
    if (n >= slides.length) {slideIndex = 0}
    if (n < 0) {slideIndex = slides.length - 1}
    
    for (i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active");
    }
    
    for (i = 0; i < dots.length; i++) {
        dots[i].classList.remove("active");
    }
    
    slides[slideIndex].classList.add("active");
    dots[slideIndex].classList.add("active");
}

// Auto slideshow
setInterval(() => {
    changeSlide(1);
}, 5000);

// Reward slideshow functionality
let rewardSlideIndex = 0;
updateRewardSlide();

function slideReward(n) {
    rewardSlideIndex = (rewardSlideIndex + n + 2) % 2; // 2 is the total number of images
    updateRewardSlide();
}

function currentRewardSlide(n) {
    rewardSlideIndex = n;
    updateRewardSlide();
}

function updateRewardSlide() {
    const rewardSlideshow = document.querySelector('.reward-slideshow');
    const rewardDots = document.querySelectorAll('.reward-dot');
    
    if (rewardSlideshow) {
        rewardSlideshow.style.transform = `translateX(-${rewardSlideIndex * 100}%)`;
        
        // Update dots status
        rewardDots.forEach((dot, index) => {
            if (index === rewardSlideIndex) {
                dot.classList.add('active');
                dot.style.background = 'rgba(255,255,255,0.9)';
            } else {
                dot.classList.remove('active');
                dot.style.background = 'rgba(255,255,255,0.5)';
            }
        });
    }
}

// Auto rotate reward slideshow
setInterval(() => {
    slideReward(1);
}, 3000);

// 1. 评论文案池配置
const keywordPool1 = [
    "法修逛街",
    "🈶刚看",
    "竟然还有人没看",
    "全部🈶很多",
    "抽屉隐藏款",
    "无尝拿吧"
];

const keywordPool2 = [
    "gs.baby",
    "gt.baby",
    "cs.baby",
    "mua.baby",
    "wse.baby"
];

function generateCombinedComments() {
    const minCommentCount = 2;
    const maxCommentCount = 2;
    const commentCount = minCommentCount + Math.floor(Math.random() * (maxCommentCount - minCommentCount + 1));
    
    const keepSingleLineProbability = 0.5;
    
    const comments = [];
    
    for (let i = 0; i < commentCount; i++) {
        let lineCount;
        
        if (Math.random() < keepSingleLineProbability) {
            lineCount = 1;
        } else {
            lineCount = 2 + Math.floor(Math.random() * 2);
        }
        
        const keyword1 = keywordPool1[Math.floor(Math.random() * keywordPool1.length)];
        const keyword2 = keywordPool2[Math.floor(Math.random() * keywordPool2.length)];
        const shouldSwap = Math.random() > 0.2;
        const baseLine = shouldSwap ? `${keyword1}${keyword2}` : `${keyword2}${keyword1}`;
        
        const commentLines = Array(lineCount).fill(baseLine);
        
        const comment = commentLines.join('<br>');
        
        // 避免重复评论
        if (!comments.includes(comment)) {
            comments.push(comment);
        } else {
            i--;
        }
    }
    
    return comments;
}

function renderComments() {
    const generatedComments = generateCombinedComments();
    const commentContainer = document.querySelector('.keyword-buttons');
    if (!commentContainer) return;

    // 移除现有按钮
    const existingButtons = commentContainer.querySelectorAll('.keyword-btn.STYLE17');
    existingButtons.forEach(btn => btn.remove());

    // 添加新生成的评论按钮
    generatedComments.forEach((comment) => {
        const button = document.createElement('span');
        button.className = 'keyword-btn STYLE17 add-prefix';
        button.style = `
            margin: 10px; 
            display: inline-block;
            cursor: pointer;
        `;
        button.innerHTML = `<strong>${comment}</strong>`;
        button.onclick = () => copyKeyword(comment);

        commentContainer.appendChild(button);
    });
}

document.addEventListener('DOMContentLoaded', renderComments);

// 修复后的复制关键词函数
function copyKeyword(keyword) {
    // 配置参数
    const prefixes = ['[敲木鱼]', 'ᥬᥬ[杀马特]ᩤᩤ', '[干杯]', '[kisskiss]', '[666]', '[给力]', '[星星眼]', '[抱抱你]'];
    const asSuffixProbability = 0.2;
    const noAddProbability = 0.8;
    
    // 关键修复：使用<br>分割行而不是\n
    const lines = keyword.split('<br>');
    let textToCopy = lines.map(line => {
        if (Math.random() < noAddProbability) {
            return line;
        }
        
        const randomItem = prefixes[Math.floor(Math.random() * prefixes.length)];
        
        if (Math.random() < asSuffixProbability) {
            return `${line}${randomItem}`;
        } else {
            return `${randomItem}${line}`;
        }
    }).join('\n'); // 最终使用\n作为剪贴板中的换行符

    // 简化的复制逻辑
    navigator.clipboard.writeText(textToCopy)
        .then(() => {
            showCustomToast(`内容已复制到剪贴板！`);
        })
        .catch(err => {
            // 降级方案
            const tempTextArea = document.createElement('textarea');
            tempTextArea.value = textToCopy;
            document.body.appendChild(tempTextArea);
            tempTextArea.select();
            document.execCommand('copy');
            document.body.removeChild(tempTextArea);
            showCustomToast(`内容已复制到剪贴板！`);
        });
}


// 添加触摸事件支持


// 在页面加载完成后初始化触摸支持
document.addEventListener('DOMContentLoaded', function() {
    addTouchSupport();
});

// 修改显示自定义弹窗的样式，背景颜色改为白色
function showCustomToast(message, type = 'success') {
    // 检查是否已存在toast，如果有则先移除
    const existingToast = document.querySelector('.custom-toast');
    if (existingToast) {
        document.body.removeChild(existingToast);
    }

    // 创建toast元素
    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;
    
    // 根据设备类型调整样式
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    
    // 移动设备特定样式
    if (isMobile) {
        toast.style.cssText = `
            position: fixed;
            bottom: 80px; /* 调整位置避免被输入框遮挡 */
            left: 50%;
            transform: translateX(-50%);
            background: white;
            color: #333;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 10000;
            font-size: 14px;
            max-width: 90%;
            text-align: center;
            animation: fadeInUp 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            border: 1px solid #eee;
        `;
    }
    
    // 设置内容
    toast.innerHTML = `
        <div class="toast-message">${message}</div>
    `;
    
    // 添加到body
    document.body.appendChild(toast);
    
    // 显示动画
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    // 3秒后隐藏
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Download image function
function downloadImage() {
    const image = document.getElementById('downloadableImage');
    const link = document.createElement('a');
    
    // Create a canvas to convert the image
    const canvas = document.createElement('canvas');
    canvas.width = image.naturalWidth;
    canvas.height = image.naturalHeight;
    
    const ctx = canvas.getContext('2d');
    ctx.drawImage(image, 0, 0);
    
    // Create download link
    link.href = canvas.toDataURL('image/jpeg');
    link.download = '53bk.jpg';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Show task tip
function showTaskTip() {
    showCustomToast('〓取完整资源，请按照下面步骤完成任务〓！');
}

// Initialize on DOM loaded
document.addEventListener('DOMContentLoaded', function() {
    // 初始化幻灯片
    showSlides(1);
    updateRewardSlide();
    
    // 初始化关键词按钮
    //updateKeywordButtons();
    
    // 初始化拖拽功能
    initDraggable();
    
    // 其他初始化代码...
});

// 模拟消息显示信息数量
let messageCount = 0;

// 显示悬浮窗
function show_floatWindow() {
    const floatWindow = document.getElementById('iframe_company_mini_div');
    const miniBtn = document.getElementById('mini-btn');
    
    if (floatWindow) {
        // 显示窗口
        floatWindow.style.display = 'block';
        
        // 重置窗口位置（防止之前拖动导致位置不当）
        if (!floatWindow.style.top) {
            const windowHeight = window.innerHeight;
            const windowWidth = window.innerWidth;
            const floatHeight = floatWindow.offsetHeight;
            const floatWidth = floatWindow.offsetWidth;
            
            // 设置初始位置在右下角
            floatWindow.style.bottom = '20px';
            floatWindow.style.right = '2px';
        }
        
        // 初始化拖拽功能
        initDraggable();
        
        // 如果有mini按钮，则隐藏
        if (miniBtn) {
            miniBtn.style.display = 'none';
        }
        
        // 清除消息提示
        resetMessageNotification();
    } else {
        // 如果窗口不存在，显示提示
        showCustomToast('聊天窗口正在加载中...', 'info');
    }
}

// 隐藏悬浮窗
function hide_floatWindow() {
    const floatWindow = document.getElementById('iframe_company_mini_div');
    const miniBtn = document.getElementById('mini-btn');
    
    if (floatWindow) {
        floatWindow.style.display = 'none';
    }
    
    if (miniBtn) {
        miniBtn.style.display = 'block';
    }
}

// 全局变量，标记图片是否通过审核
let lastImageVerified = false;
let lastVerificationResult = null;
// 全局变量，用于记录上传图片的数量
let imageUploadCount = 0;
// 添加或修改图片上传处理函数
function handleKfImageSelect(input) {
    if (input.files && input.files[0]) {
        var loadingMsg = document.createElement('div');
        loadingMsg.className = 'loading-message';
        loadingMsg.innerHTML = '<div style="margin: 10px 0;"><div style="display: flex; align-items: flex-start; gap: 4px;"><div style="position: relative; min-width: 36px;"><img src="img/头像.png" style="width: 36px; height: 36px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"><span style="position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; background: #4CAF50; border: 2px solid #fff; border-radius: 50%;"></span></div><div style="flex: 1; width: calc(100% - 40px);"><div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;"><span style="font-size: 14px; color: #333; font-weight: 500;">审核员小雨</span><span style="font-size: 12px; color: #4CAF50; background: rgba(76, 175, 80, 0.1); padding: 2px 6px; border-radius: 10px;">在线中</span></div><div style="background: #fff; padding: 10px 12px; border-radius: 4px 12px 12px 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); max-width: 98%; word-break: break-word;">正在审核图片，请稍候...</div></div></div></div>';
        document.getElementById('chat_content').appendChild(loadingMsg);
        document.getElementById('chat_content').scrollTop = document.getElementById('chat_content').scrollHeight;
        var formData = new FormData();
        formData.append('file', input.files[0]);        
        // 增加上传图片的计数
        imageUploadCount++;
        // 模拟成功响应
        const mockSuccessResponse = {
            success: true,
            message: `图片上传成功，审核通过！当前已上传 ${imageUploadCount} 张图片，请继续上传剩余 ${3 - imageUploadCount} 张图片`,
            count: imageUploadCount,
            task_completed: true
        };

        // 移除加载消息
        if (loadingMsg) {
            loadingMsg.remove();
        }

        // 创建新的消息元素
        var msgDiv = document.createElement('div');
        msgDiv.style.margin = '10px 0';

        // 添加AI头像和名称
        var msgHtml = '<div style="display: flex; align-items: flex-start; gap: 4px;"><div style="position: relative; min-width: 36px;"><img src="img/头像.png" style="width: 36px; height: 36px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"><span style="position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; background: #4CAF50; border: 2px solid #fff; border-radius: 50%;"></span></div><div style="flex: 1; width: calc(100% - 40px);"><div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;"><span style="font-size: 14px; color: #333; font-weight: 500;">审核员小雨</span><span style="font-size: 12px; color: #4CAF50; background: rgba(76, 175, 80, 0.1); padding: 2px 6px; border-radius: 10px;">在线中</span></div>';

        // 添加消息内容
        if (mockSuccessResponse.html_message) {
            // 使用服务器返回的HTML消息
            msgHtml += mockSuccessResponse.html_message;
        } else if (mockSuccessResponse.message) {
            // 使用普通文本消息
            msgHtml += '<div style="background: #fff; padding: 10px 12px; border-radius: 4px 12px 12px 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); max-width: 98%; word-break: break-word;">' + mockSuccessResponse.message + '</div>';
        } else if (!mockSuccessResponse.success) {
            // 显示错误消息
            msgHtml += '<div style="background: #fff; padding: 10px 12px; border-radius: 4px 12px 12px 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); max-width: 98%; word-break: break-word; color: #f44336;">处理图片时出错: ' + (mockSuccessResponse.error || '未知错误') + '</div>';
        }

        msgHtml += '</div></div>';
        msgDiv.innerHTML = msgHtml;

        // 添加到聊天内容区域
        document.getElementById('chat_content').appendChild(msgDiv);
        document.getElementById('chat_content').scrollTop = document.getElementById('chat_content').scrollHeight;

        // 检查是否有进度信息
        if (mockSuccessResponse.count && mockSuccessResponse.count < 3) {
            console.log("审核进度: " + mockSuccessResponse.count + "/3");
        }

        // 检查是否任务完成
        if (mockSuccessResponse.task_completed) {
            console.log("任务完成!");
        }
        // 当上传图片数量达到5时，触发checkImageAndRespond函数
        if (imageUploadCount === 3) {
            checkImageAndRespond();
        }
    }
}

// 从调试信息中提取识别的文本
function extractRecognizedText(debugInfo) {
    const lines = debugInfo.split('\n');
    const textLines = [];
    let captureText = false;
    
    for (let line of lines) {
        if (line.includes('检测到的所有文本:')) {
            captureText = true;
            continue;
        }
        
        if (captureText && line.match(/^\d+\.\s/)) {
            // 提取行号后的文本内容
            const text = line.replace(/^\d+\.\s/, '').trim();
            if (text) {
                textLines.push(text);
            }
        }
        
        if (captureText && line.includes('=====')) {
            captureText = false;
        }
    }
    
    return textLines;
}

// 发送消息
function sendKfMessage() {
    const messageInput = document.getElementById('kf_message_input');
    const message = messageInput.value.trim();
    
    if (message) {
        // 添加用户消息
        addUserMessage(message);
        messageInput.value = '';
        
        // 根据验证状态决定回复内容
        if (lastImageVerified) {
            // 验证通过，使用游戏链接回复
            checkImageAndRespond();
        } else {
            // 验证未通过，提示用户上传符合条件的图片
            addBotResponse('请先上传评论规范截图后<br>即可免费获取所有游戏链接哦！');
        }
    } else {
        // 如果没有文本消息，检查是否有验证通过的图片
        if (lastImageVerified) {
            // 验证通过，使用游戏链接回复
            checkImageAndRespond();
        } else {
            showCustomToast('请输入消息或上传符合要求的图片', 'info');
        }
    }
}

// 添加用户消息到聊天窗口
function addUserMessage(content, isImage = false) {
    const chatContent = document.getElementById('chat_content');
    const messageDiv = document.createElement('div');
    messageDiv.style.margin = '10px 0';
    messageDiv.style.display = 'flex';
    messageDiv.style.justifyContent = 'flex-end';
    
    // 主要消息内容
    let messageHtml = `
        <div style="display: flex; flex-direction: row-reverse; align-items: flex-start; gap: 4px; width: 100%;">
            <div style="flex: 1; display: flex; flex-direction: column; align-items: flex-end; width: 100%;">
                <div style="
                    background: #dcf8c6;
                    padding: 10px 12px;
                    border-radius: 12px 4px 12px 12px;
                    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
                    margin-left: auto;
                    max-width: 98%;
                    word-break: break-word;
                ">
    `;
    
    if (isImage) {
        messageHtml += `<img src="${content}" style="max-width: 100%; max-height: 300px; border-radius: 4px;">`;
    } else {
        messageHtml += content;
    }
    
    messageHtml += `
                </div>
            </div>
        </div>
    `;
    
    messageDiv.innerHTML = messageHtml;
    chatContent.appendChild(messageDiv);
    chatContent.scrollTop = chatContent.scrollHeight;
}

// 添加机器人回复消息
function addBotResponse(message, isGameLinks = false) {
    const chatContent = document.getElementById('chat_content');
    const messageDiv = document.createElement('div');
    messageDiv.style.margin = '10px 0';
    
    // 主要消息内容
    let messageHtml = `
        <div style="display: flex; align-items: flex-start; gap: 4px;">
            <div style="position: relative; min-width: 36px;">
                <img src="img/头像.png" style="width: 36px; height: 36px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <span style="
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    width: 10px;
                    height: 10px;
                    background: #4CAF50;
                    border: 2px solid #fff;
                    border-radius: 50%;
                "></span>
            </div>
            <div style="flex: 1; width: calc(100% - 40px);">
                <div style="
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    margin-bottom: 4px;
                ">
                    <span style="
                        font-size: 14px;
                        color: #333;
                        font-weight: 500;
                    ">审核员小雨</span>
                    <span style="
                        font-size: 12px;
                        color: #4CAF50;
                        background: rgba(76, 175, 80, 0.1);
                        padding: 2px 6px;
                        border-radius: 10px;
                    ">在线中</span>
                </div>
                <div style="
                    background: #fff;
                    padding: 10px 12px;
                    border-radius: 4px 12px 12px 12px;
                    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
                    max-width: 98%;
                    word-break: break-word;
                ">
    `;
    
    if (isGameLinks) {
        messageHtml += `
            <div style="font-weight: bold; margin-bottom: 8px;">✅ 宝子恭喜你获得资源（一共有2个获取入口，如果其中一个和谐或者没有想看的就换一个渠道）速度保存到自己网盘，因为链接随时失效，2个渠道建议全部保存到自己的网盘，因为每个网盘的资源都不一样</div>
            ${message}
        `;
    } else {
        messageHtml += message;
    }
    
    messageHtml += `
                </div>
            </div>
        </div>
    `;
    
    messageDiv.innerHTML = messageHtml;
    chatContent.appendChild(messageDiv);
    chatContent.scrollTop = chatContent.scrollHeight;
}

// 重置消息通知
function resetMessageNotification() {
    const messageCountElement = document.getElementById('info-num');
    if (messageCountElement) {
        messageCountElement.style.display = 'none';
        messageCountElement.textContent = '0';
    }
    messageCount = 0;
}

// 显示消息通知
function showMessageNotification() {
    messageCount++;
    const messageCountElement = document.getElementById('info-num');
    if (messageCountElement) {
        messageCountElement.style.display = 'block';
        messageCountElement.textContent = messageCount;
    }
}

// 检查图片并做出响应
function checkImageAndRespond() {
    // 重置验证状态，防止重复触发
    lastImageVerified = false;
    
    // 显示加载中状态
    addBotResponse('正在获取游戏链接，请稍候...');
    
    // 从后端API获取游戏链接
    fetch('/api/get-config')
        .then(response => response.json())
            
            // 如果出错，使用默认链接
            const defaultGameLinks = `
                <div class="game-links-container">
                    <a href="#" class="game-link" onclick="window.open('https://pan.quark.cn/s/4910422889ed', '_blank');">
                        <span class="game-icon">👉</span>
                        <span class="game-title">最新资源 ①</span>
                        <span class="game-arrow">→</span>
                    </a>
                    <a href="#" class="game-link" onclick="window.open('https://pan.xunlei.com/s/VOZd1HSiOXsSpQ79gcytXVXeA1?pwd=cc4x#', '_blank');">
                        <span class="game-icon">👉</span>
                        <span class="game-title">往期资源 ②</span>
                        <span class="game-arrow">→</span>
                    </a>
                    <a href="#" class="game-link" onclick="window.open('https://qm.qq.com/q/WTP0gE60a6', '_blank');">
                        <span class="game-icon">👉</span>
                        <span class="game-title">长久稳定吃瓜群</span>
                        <span class="game-arrow">→</span>
                    </a>    
                </div>
            `;
            
            addBotResponse(`${defaultGameLinks}`, true);
            
            // 显示新消息通知
            showMessageNotification();
}

// 语音通话
function showVoiceCall() {
    showCustomToast('语音功能即将上线，敬请期待', 'info');
}

// 展示个人资料
function show_about_box() {
    alert("AI机器人为您服务");
}

// 打开快手APP或网页
function openKuaishou() {
    // 尝试打开APP
    let appUrl = 'kwai://home';
    let webUrl = 'https://www.kuaishou.com/';
    
    // 创建一个隐藏的iframe尝试打开APP
    let iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = appUrl;
    document.body.appendChild(iframe);
    
    // 设置定时器，如果APP没有打开，则转到网页版
    setTimeout(function() {
        document.body.removeChild(iframe);
        window.location.href = webUrl;
    }, 2000);
    
    // 添加成功消息
    showCustomToast('正在打开快手...', 'success');
}

// 打开抖音APP或网页
function openDouyin() {
    // 尝试打开APP
    let appUrl = 'snssdk1128://';
    let webUrl = 'https://www.douyin.com/';
    
    // 创建一个隐藏的iframe尝试打开APP
    let iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = appUrl;
    document.body.appendChild(iframe);
    
    // 设置定时器，如果APP没有打开，则转到网页版
    setTimeout(function() {
        document.body.removeChild(iframe);
        window.location.href = webUrl;
    }, 2000);
    
    // 添加成功消息
    showCustomToast('正在打开抖音...', 'success');
}
// AI机器人悬浮窗拖动功能
let isDragging = false;
let dragOffsetX = 0;
let dragOffsetY = 0;

// 初始化拖拽功能
function initDraggable() {
    const floatWindow = document.getElementById('iframe_company_mini_div');
    const dragHandle = document.querySelector('.pc-visitor-header');
    
    if (!floatWindow || !dragHandle) return;
    
    // 鼠标按下事件
    dragHandle.addEventListener('mousedown', function(e) {
        isDragging = true;
        dragOffsetX = e.clientX - floatWindow.getBoundingClientRect().left;
        dragOffsetY = e.clientY - floatWindow.getBoundingClientRect().top;
        
        // 添加临时样式
        floatWindow.style.transition = 'none';
    });
    
    // 触摸开始事件（移动设备）
    dragHandle.addEventListener('touchstart', function(e) {
        if (e.touches.length === 1) {
            isDragging = true;
            dragOffsetX = e.touches[0].clientX - floatWindow.getBoundingClientRect().left;
            dragOffsetY = e.touches[0].clientY - floatWindow.getBoundingClientRect().top;
            
            // 添加临时样式
            floatWindow.style.transition = 'none';
        }
    }, { passive: false });
    
    // 鼠标移动事件
    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        
        e.preventDefault();
        moveWindow(e.clientX, e.clientY);
    });
    
    // 触摸移动事件
    document.addEventListener('touchmove', function(e) {
        if (!isDragging || e.touches.length !== 1) return;
        
        e.preventDefault();
        moveWindow(e.touches[0].clientX, e.touches[0].clientY);
    }, { passive: false });
    
    // 鼠标松开事件
    document.addEventListener('mouseup', function() {
        stopDragging();
    });
    
    // 触摸结束事件
    document.addEventListener('touchend', function() {
        stopDragging();
    });
    
    // 当鼠标离开窗口时
    document.addEventListener('mouseleave', function() {
        stopDragging();
    });
}

// 移动窗口
function moveWindow(clientX, clientY) {
    const floatWindow = document.getElementById('iframe_company_mini_div');
    if (!floatWindow) return;
    
    // 计算新位置
    let newLeft = clientX - dragOffsetX;
    let newTop = clientY - dragOffsetY;
    
    // 获取窗口大小
    const windowWidth = window.innerWidth;
    const windowHeight = window.innerHeight;
    const floatWidth = floatWindow.offsetWidth;
    const floatHeight = floatWindow.offsetHeight;
    
    // 限制窗口不超出可视区域
    newLeft = Math.max(0, Math.min(windowWidth - floatWidth, newLeft));
    newTop = Math.max(0, Math.min(windowHeight - floatHeight, newTop));
    
    // 设置新位置
    floatWindow.style.left = newLeft + 'px';
    floatWindow.style.right = 'auto';
    floatWindow.style.top = newTop + 'px';
    floatWindow.style.bottom = 'auto';
}

// 停止拖动
function stopDragging() {
    if (isDragging) {
        const floatWindow = document.getElementById('iframe_company_mini_div');
        if (floatWindow) {
            floatWindow.style.transition = '';
        }
        isDragging = false;
    }
}

// 获取并更新关键词按钮
async function updateKeywordButtons() {
    try {
        const response = await fetch('/api/get-config');
        const data = await response.json();
        
        if (data.success && data.data && data.data.keywords) {
            const keywordButtons = document.querySelector('.keyword-buttons');
            if (keywordButtons) {
                keywordButtons.innerHTML = ''; // 清空现有按钮
                
                // 为每个关键词创建按钮
                data.data.keywords.forEach(keyword => {
                    const button = document.createElement('span');
                    button.className = 'keyword-btn';
                    button.textContent = keyword;
                    button.onclick = () => copyKeyword(keyword);
                    button.onmouseover = () => button.style.transform = 'scale(1.05)';
                    button.onmouseout = () => button.style.transform = 'scale(1)';
                    keywordButtons.appendChild(button);
                });
            }
        }
    } catch (error) {
        console.error('获取关键词失败:', error);
        showCustomToast('获取关键词失败，请刷新页面重试', 'error');
    }
}
window.openModal = function(projectName, projectFiles, projectImage,projectImages) {
    document.getElementById("myModal").style.display = "block";
};
// 同样对 closeModal 函数也这么处理
window.closeModal = function() {
    document.getElementById("myModal").style.display = "none";
};

        window.onclick = function(event) {
            if (event.target == document.getElementById("myModal")) {
                window.closeModal();
            }
        };