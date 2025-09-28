// 1. 关键词池配置
const keywordPool = [
  { text: "抽屉变装隐藏款", id: "抽屉变装隐藏款", weight: 30, color: "#FF0000" },
  { text: "抽屉变装", id: "抽屉变装", weight: 20, color: "#FF0000" },     
  { text: "光剑变装", id: "光剑变装", weight: 20, color: "#FF0000" },
  { text: "法修散打隐藏款", id: "法修散打隐藏款", weight: 20, color: "#32CD32"  },
  { text: "一个板栗栗vs林黛玉", id: "一个板栗栗vs林黛玉", weight: 20, color: "#1E90FF" },
  { text: "一个板栗栗vs林黛玉", id: "一个板栗栗vs林黛玉", weight: 20, color: "#CC22AA" },
  { text: "抽屉变装隐藏款", id: "抽屉变装隐藏款", weight: 20, color: "#FF0000" },
  { text: "抽屉变装隐藏款", id: "抽屉变装隐藏款", weight: 30, color: "#FF0000" }
];

let domElements = {};
let currentKeyword = null; // 新增：跟踪当前选中的关键词对象

function initDOM() {
  domElements = {
    customInput: document.getElementById('customKeyword'),
    jumpDouyinBtn: document.getElementById('jumpDouyinBtn'),
    statusBox: document.getElementById('douyinStatus'),
    refreshBtn: document.getElementById('refreshKeywordBtn') 
  };
}

function showStatus(message, isSuccess = true) {
  const { statusBox } = domElements;
  statusBox.textContent = message;
  statusBox.className = `douyin-status ${isSuccess ? 'success' : 'error'}`;
  statusBox.style.display = 'block'; 
  
  statusBox.style.background = isSuccess ? '#E8F5E9' : '#FFEBEE';
  statusBox.style.color = isSuccess ? '#2E7D32' : '#C62828';
  
  setTimeout(() => {
    statusBox.style.display = 'none'; // 2秒后隐藏
  }, 2000);
}

function getRandomKeyword() {
  const weightedPool = [];
  keywordPool.forEach(item => {
    for (let i = 0; i < item.weight; i++) {
      weightedPool.push(item);
    }
  });
  const randomIndex = Math.floor(Math.random() * weightedPool.length);
  return weightedPool[randomIndex];
}

function fillRandomKeyword() {
  const { customInput } = domElements;
  currentKeyword = getRandomKeyword(); // 保存当前选中的关键词对象
  customInput.value = currentKeyword.text;
  customInput.style.color = currentKeyword.color || "#000";
}

function refreshKeyword() {
  fillRandomKeyword(); 
  showStatus('已刷新', true); 
  const { refreshBtn } = domElements;
  refreshBtn.querySelector('i').style.transform = 'rotate(360deg)';
  refreshBtn.querySelector('i').style.transition = 'transform 0.5s ease';
  setTimeout(() => {
    refreshBtn.querySelector('i').style.transform = 'rotate(0)';
  }, 500);
}

// 6. 抖音跳转功能（）
function jumpToDouyin() {
  // 使用当前选中的关键词对象中的id
  if (!currentKeyword || !currentKeyword.id) {
    showStatus('获取失败', false);
    return;
  }

  try {
    // 抖音作品详情页的正确scheme格式
    const scheme = `snssdk1128://search?keyword=${currentKeyword.id}`;
    
    window.location.href = scheme;
    setTimeout(() => {
      showStatus('若未跳转，请确保已安装抖音App', false);
    }, 1500);

  } catch (err) {
    showStatus('跳转失败，请重试', false);
  }
}

// 7. 事件绑定
function bindEvents() {
  const { jumpDouyinBtn, customInput, refreshBtn } = domElements;

  // 抖音跳转
  jumpDouyinBtn.addEventListener('click', jumpToDouyin);

  // 输入框聚焦全选
  customInput.addEventListener('focus', () => {
    customInput.select();
  });

  // 刷新按钮点击事件
  refreshBtn.addEventListener('click', refreshKeyword);

  // 鼠标悬停效果
  refreshBtn.addEventListener('mouseenter', () => {
    refreshBtn.style.color = '#FF2442'; // 抖音红
  });
  refreshBtn.addEventListener('mouseleave', () => {
    refreshBtn.style.color = '#666';
  });
}

// 8. 页面加载初始化
document.addEventListener('DOMContentLoaded', () => {
  initDOM();
  fillRandomKeyword(); // 初始化填充关键词
  bindEvents(); // 绑定所有事件
});