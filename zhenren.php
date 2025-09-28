<?php 
include_once 'config.php'; // 包含配置文件以使用get_setting函数
$popup_url = get_setting('popup_url'); // 获取后台设置的弹窗URL
include_once 'header.php'; 
?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background: #000;
            color: #f5f5f5;
            min-height: 100vh;
            padding: 10px;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 10px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            margin-bottom: 15px;
            border-bottom: 1px solid #333;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 1px;
            background: linear-gradient(90deg, #e50914, #f5c518);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 12px;
        }
        
        nav a {
            color: #aaa;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s;
            padding: 5px 10px;
            border-radius: 20px;
        }
        
        nav a:hover, nav a.active {
            color: #fff;
            background: rgba(229, 9, 20, 0.2);
        }
        
        .section-title {
            text-align: center;
            margin: 15px 0 20px;
            font-size: 1.8rem;
            background: linear-gradient(90deg, #e50914, #f5c518);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            padding-bottom: 10px;
            border-bottom: 1px solid #333;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg, #e50914, #f5c518);
        }
        
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 18px;
            flex-grow: 1;
        }
        
        .video-card {
            background: rgba(30, 30, 30, 0.9);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            position: relative;
            aspect-ratio: 16/9;
        }
        
        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(229, 9, 20, 0.3);
        }
        
        .premium-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #e50914;
            color: white;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: bold;
            z-index: 2;
        }
        
        .thumbnail {
            height: 100%;
            width: 100%;
            background-size: cover;
            background-position: center;
            position: relative;
            border-bottom: 1px solid #333;
        }
        
        .play-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            z-index: 2;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .play-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-35%, -50%);
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-left: 20px solid #333;
            border-bottom: 10px solid transparent;
        }
        
        .video-card:hover .play-icon {
            opacity: 1;
            background: #e50914;
        }
        
        .video-card:hover .play-icon::before {
            border-left-color: white;
        }
        
        .duration {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            z-index: 2;
        }
        
        .video-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 12px;
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            z-index: 1;
        }
        
        .video-info h3 {
            font-size: 1rem;
            margin-bottom: 6px;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .meta {
            display: flex;
            justify-content: space-between;
            color: #fff;
            font-size: 0.75rem;
        }
        
        .meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .load-more {
            text-align: center;
            margin: 25px 0;
        }
        
        .load-more-btn {
            background: rgba(229, 9, 20, 0.2);
            color: #f5f5f5;
            border: 1px solid #e50914;
            padding: 12px 40px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .load-more-btn:hover {
            background: rgba(229, 9, 20, 0.4);
            box-shadow: 0 0 15px rgba(229, 9, 20, 0.3);
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .purchase-modal {
            background: #111;
            border-radius: 12px;
            width: 90%;
            max-width: 450px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 0 30px rgba(229, 9, 20, 0.3);
            border: 1px solid #333;
            transform: scale(0.9);
            transition: transform 0.4s;
            position: relative;
        }
        
        .modal-overlay.active .purchase-modal {
            transform: scale(1);
        }
        
        .purchase-modal h2 {
            font-size: 1.4rem;
            margin-bottom: 12px;
            color: #fff;
        }
        
        .purchase-modal .subtitle {
            color: #aaa;
            font-size: 0.95rem;
            margin-bottom: 20px;
        }
        
        .purchase-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin: 20px 0;
        }
        
        .purchase-option {
            background: rgba(40, 40, 40, 0.9);
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            border: 1px solid #333;
            position: relative;
            overflow: hidden;
        }
        
        .purchase-option:hover {
            background: rgba(229, 9, 20, 0.15);
            border-color: #e50914;
        }
        
        .purchase-option h3 {
            font-size: 1.2rem;
            margin-bottom: 6px;
            color: #e50914;
        }
        
        .purchase-option .price {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 6px;
            color: #f1c40f;
        }
        
        .purchase-option .desc {
            color: #aaa;
            font-size: 0.85rem;
        }
        
        .close-modal {
            background: rgba(60, 60, 60, 0.9);
            color: #ddd;
            border: 1px solid #444;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 8px;
            width: 100%;
        }
        
        .close-modal:hover {
            background: rgba(229, 9, 20, 0.2);
            color: #fff;
        }
        
        .loading-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background: #e50914;
            width: 0%;
            transition: width 0.5s;
        }
        
        footer {
            text-align: center;
            padding: 25px 0 12px;
            color: #666;
            font-size: 0.8rem;
            border-top: 1px solid #222;
            margin-top: 15px;
        }
        
        .float-btns {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 9999;
        }
        
        .float-btn {
            padding: 12px 25px;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            white-space: nowrap;
            text-align: center;
            min-width: 120px;
        }
        
        .float-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0,0,0,0.4);
        }
        
        .float-btn:active {
            transform: scale(0.95);
        }
        
        .btn-payment {
            background: #ff4757;
            border: 2px solid #ff6b81;
        }
        
        .btn-refund {
            background: #2ed573;
            border: 2px solid #7bed9f;
        }
        
        .btn-complaint {
            background: #3742fa;
            border: 2px solid #5352ed;
        }
                        /* 操作按钮容器 */
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            gap: 10px;
        }
        .btn-payment {
            background-color: #ff6b6b;
            color: white;
        }
        .action-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 12px;
            }
            
            nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 8px;
            }
            
            .video-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 14px;
            }
            
            .section-title {
                font-size: 1.6rem;
            }
            
            .float-btns {
                bottom: 15px;
                right: 15px;
                gap: 10px;
            }
            
            .float-btn {
                padding: 10px 20px;
                font-size: 14px;
                min-width: 100px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 8px;
            }
            
            .container {
                padding: 8px;
            }
            
            header {
                padding: 10px 0;
                margin-bottom: 12px;
            }
            
            .logo h1 {
                font-size: 1.3rem;
            }
            
            nav a {
                font-size: 0.85rem;
                padding: 4px 8px;
            }
            
            .section-title {
                font-size: 1.4rem;
                margin: 10px 0 15px;
            }
            
            .video-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .video-card {
                border-radius: 6px;
            }
            
            .premium-badge {
                top: 6px;
                right: 6px;
                padding: 2px 5px;
                font-size: 0.65rem;
            }
            
            .play-icon {
                width: 40px;
                height: 40px;
            }
            
            .play-icon::before {
                border-top: 8px solid transparent;
                border-left: 16px solid #333;
                border-bottom: 8px solid transparent;
                transform: translate(-35%, -50%);
            }
            
            .video-info {
                padding: 8px;
            }
            
            .video-info h3 {
                font-size: 0.9rem;
            }
            
            .load-more-btn {
                padding: 10px 30px;
                font-size: 1rem;
            }
            
            .purchase-modal {
                padding: 18px;
                max-width: 95%;
            }
            
            .purchase-modal h2 {
                font-size: 1.2rem;
            }
            
            .purchase-options {
                gap: 10px;
                margin: 15px 0;
            }
            
            .purchase-option {
                padding: 12px;
            }
            
            .purchase-option h3 {
                font-size: 1.1rem;
            }
            
            .purchase-option .price {
                font-size: 1.3rem;
            }
            
            .purchase-option .desc {
                font-size: 0.8rem;
            }
            
            .close-modal {
                padding: 7px 14px;
                font-size: 0.85rem;
            }
            
            footer {
                padding: 20px 0 10px;
                font-size: 0.75rem;
            }
        }
    </style>
<style>.Po4BvhR1CK2tJaywJ6AN path {
  fill: var(--icon-path-fill);
}
.Oz4yDjua3Qe6thqkZYf_ path {
  transition: 0.2s all;
}
.Oz4yDjua3Qe6thqkZYf_:hover path {
  fill: var(--icon-hover-fill);
}
</style><style>/* ！！！切勿直接改动该文件，该文件由 generator.ts 自动生成！！！ */
/* !!! DONT MODIFY THIS FILE DIRECTLY, THIS FILE IS GENERATED BY generator.ts AUTOMATICALLY !!! */
.ibW4Oa5B7s2zJKKZ4pCg {
  user-select: none;
}
.AtqKyJetjrG4smuk35Np {
  max-width: 346px;
  width: auto;
  height: 36px;
  background-color: var(--quark-style-white-color, #fff);
  padding-left: 10px;
  padding-right: 4px;
  display: flex;
  align-items: center;
  box-sizing: border-box;
  border: 1px solid var(--quark-style-gray-20-color, rgba(6, 10, 38, 0.12));
  box-shadow: 0 12px 32px -6px var(--quark-style-gray-30-fixed-color, rgba(6, 10, 38, 0.24));
  border-radius: 10px;
}
.ibW4Oa5B7s2zJKKZ4pCg .g6iGsZa_KHMeW2yICzQQ {
  height: 28px;
  display: flex;
  align-items: center;
  margin-right: 6px;
}
.ibW4Oa5B7s2zJKKZ4pCg .e4UXx38MPgfHdym_Lzt0 {
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  height: 28px;
  padding: 0 6px;
  margin-right: 2px;
  border-radius: 6px;
  column-gap: 4px;
}
.ibW4Oa5B7s2zJKKZ4pCg .e4UXx38MPgfHdym_Lzt0:hover:not(.ibW4Oa5B7s2zJKKZ4pCg .kNOcXLDT_cCrcoY8LTm8) {
  background: var(--quark-style-gray-10-color, rgba(6, 10, 38, 0.06));
}
.ibW4Oa5B7s2zJKKZ4pCg .kNOcXLDT_cCrcoY8LTm8 {
  cursor: default;
}
.ibW4Oa5B7s2zJKKZ4pCg .kNOcXLDT_cCrcoY8LTm8 .Va3czASiR9Zztyl_lD4M {
  color: var(--quark-style-gray-40-color, rgba(6, 10, 38, 0.4)) !important;
}
.ibW4Oa5B7s2zJKKZ4pCg .e4UXx38MPgfHdym_Lzt0 .Va3czASiR9Zztyl_lD4M {
  font-size: 12px;
  color: var(--quark-style-gray-color, #060A26);
  line-height: 16px;
  white-space: nowrap;
  position: relative;
}
.ibW4Oa5B7s2zJKKZ4pCg .llw0qsmiI_08u93bFdNg {
  position: relative;
  width: 28px;
  height: 28px;
  overflow: visible !important;
}
.ibW4Oa5B7s2zJKKZ4pCg .LEo8kpqIERehkv8AhAfG {
  width: 28px;
  height: 28px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  border-radius: 6px;
  overflow: visible !important;
}
.ibW4Oa5B7s2zJKKZ4pCg .LEo8kpqIERehkv8AhAfG:hover {
  background: var(--quark-style-gray-10-color, rgba(6, 10, 38, 0.06));
}
.ibW4Oa5B7s2zJKKZ4pCg .zoNmooxAnbLEJSN8m1Jg {
  box-sizing: border-box;
  position: absolute;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 110px;
  max-height: 136px;
  height: auto;
  top: 36px;
  right: -5px;
  padding: 4px 0;
  background-color: var(--quark-style-white-color, #fff);
  border: 1px solid var(--quark-style-gray-20-color, rgba(6, 10, 38, 0.12));
  box-shadow: 0 4px 16px -6px var(--quark-style-gray-20-fixed-color, rgba(6, 10, 38, 0.12));
  border-radius: 8px;
  overflow: visible !important;
  row-gap: 4px;
}
.ibW4Oa5B7s2zJKKZ4pCg .O1imPofna4elG_8NcQnR {
  top: -77px;
}
.ibW4Oa5B7s2zJKKZ4pCg .mdH0IY7jS3Swn5vdX6tz {
  width: 102px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  cursor: pointer;
  column-gap: 8px;
  border-radius: 6px;
  padding: 0 6px;
  box-sizing: border-box;
}
.ibW4Oa5B7s2zJKKZ4pCg .mdH0IY7jS3Swn5vdX6tz:hover:not(.ibW4Oa5B7s2zJKKZ4pCg .dEdHLVmn_L2GAzb_cmwt) {
  background: var(--quark-style-gray-10-color, rgba(6, 10, 38, 0.06));
}
.ibW4Oa5B7s2zJKKZ4pCg .dEdHLVmn_L2GAzb_cmwt {
  cursor: default;
}
.ibW4Oa5B7s2zJKKZ4pCg .dEdHLVmn_L2GAzb_cmwt .zEraruudgjR2MToGu4Kw {
  color: var(--quark-style-gray-40-color, rgba(6, 10, 38, 0.4)) !important;
}
.ibW4Oa5B7s2zJKKZ4pCg .XfCMwvO0DsqFCeyzPYP2 {
  width: 16px;
  height: 16px;
}
.ibW4Oa5B7s2zJKKZ4pCg .zEraruudgjR2MToGu4Kw {
  font-size: 12px;
  color: var(--quark-style-gray-color, #060A26);
}
.ibW4Oa5B7s2zJKKZ4pCg .KZeoAuXbMIkWzOT4PcH5 {
  width: 100%;
  height: 1px;
  background: var(--quark-style-gray-10-color, rgba(6, 10, 38, 0.06));
}
.ZL32C_XdLL8UQRZ3zObd {
  display: flex;
  align-items: center;
}
.u5llx7cIQZLdrjP5Vag1 {
  width: 28px;
  height: 28px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 16px;
  cursor: pointer;
  margin-right: 12px;
  background: var(--quark-style-gray-60-color, rgba(6, 10, 38, 0.6));
}
.ZL32C_XdLL8UQRZ3zObd .LEo8kpqIERehkv8AhAfG {
  border-radius: 16px !important;
  background: var(--quark-style-gray-60-color, rgba(6, 10, 38, 0.6)) !important;
}
.ZL32C_XdLL8UQRZ3zObd .zoNmooxAnbLEJSN8m1Jg {
  right: 0 !important;
}
.ZL32C_XdLL8UQRZ3zObd {
  overflow: visible !important;
}
</style></head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <h1>公益网站100%免费</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">动漫</a></li>
                    <li><a href="manhua.php">漫画</a></li>
                    <li><a href="youxi.php">游戏</a></li>
                    <li><a href="zhenren.php" class="active">真人</a></li>
                    <li><a href="xiezhen.php">写真</a></li>
                </ul>
            </nav>
        </header>
        
        <h2 class="section-title">视频库</h2>
        
        <section class="video-grid" id="videoGrid"><div class="video-card" data-video="1">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img12.360buyimg.com/ddimg/jfs/t1/300782/29/12573/94387/6875e873F4f5cca4f/dbcf24bdaaf7a947.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">1:23:45</div>
                                <div class="video-info">
                                    <h3>吊带黑丝女仆上门服务</h3>
                                    <div class="meta">
                                        <span>12.5万播放</span>
                                        <span>9.8分</span>
                                    </div>
                                </div>
                            </div>
                        </div><div class="video-card" data-video="2">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img14.360buyimg.com/ddimg/jfs/t1/318404/13/16239/63276/6875e877F463e208a/c1be48d6db5295a7.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">58:32</div>
                                <div class="video-info">
                                    <h3>经典裤袜水手服 白给半夜的夜袭</h3>
                                    <div class="meta">
                                        <span>8.7万播放</span>
                                        <span>9.7分</span>
                                    </div>
                                </div>
                            </div>
                        </div><div class="video-card" data-video="3">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img11.360buyimg.com/ddimg/jfs/t1/302232/9/19805/78332/6875e87aF76080926/30fa6e47234b66af.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">1:45:21</div>
                                <div class="video-info">
                                    <h3>猫系女仆家政服务，黑丝过膝袜女仆上门</h3>
                                    <div class="meta">
                                        <span>15.2万播放</span>
                                        <span>9.5分</span>
                                    </div>
                                </div>
                            </div>
                        </div><div class="video-card" data-video="4">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img10.360buyimg.com/ddimg/jfs/t1/318601/28/16240/46118/6875e880F008d518e/5a4ef55371e17c0f.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">1:12:38</div>
                                <div class="video-info">
                                    <h3>穿着JK黑丝勾引玩游戏的男朋友</h3>
                                    <div class="meta">
                                        <span>22.8万播放</span>
                                        <span>9.6分</span>
                                    </div>
                                </div>
                            </div>
                        </div><div class="video-card" data-video="5">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img13.360buyimg.com/ddimg/jfs/t1/307432/39/17132/41151/6875e887F7b6b8c11/59f64aaf08dcba77.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">45:12</div>
                                <div class="video-info">
                                    <h3>原神刻晴同人护士Ver Cosplay</h3>
                                    <div class="meta">
                                        <span>18.3万播放</span>
                                        <span>9.9分</span>
                                    </div>
                                </div>
                            </div>
                        </div><div class="video-card" data-video="6">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img13.360buyimg.com/ddimg/jfs/t1/305334/16/18153/79248/6875e891F55b5b000/8e96c81d42c02605.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">1:30:45</div>
                                <div class="video-info">
                                    <h3>高跟袜 15 黑色高跟凉鞋</h3>
                                    <div class="meta">
                                        <span>16.7万播放</span>
                                        <span>9.4分</span>
                                    </div>
                                </div>
                            </div>
                        </div><div class="video-card" data-video="7">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img14.360buyimg.com/ddimg/jfs/t1/296863/7/23304/84874/6875e895Fb49cda0e/4d538ddc3960d6f9.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">1:15:22</div>
                                <div class="video-info">
                                    <h3>调皮加奈欲擒故纵</h3>
                                    <div class="meta">
                                        <span>28.9万播放</span>
                                        <span>9.3分</span>
                                    </div>
                                </div>
                            </div>
                        </div><div class="video-card" data-video="8">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img11.360buyimg.com/ddimg/jfs/t1/307493/19/16859/81274/6875e9b5F400af79c/a0f9e1ac5b68c549.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">1:08:56</div>
                                <div class="video-info">
                                    <h3>原神甘雨cos+黑色过膝袜足交</h3>
                                    <div class="meta">
                                        <span>14.5万播放</span>
                                        <span>9.7分</span>
                                    </div>
                                </div>
                            </div>
                        </div><div class="video-card" data-video="9">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img13.360buyimg.com/ddimg/jfs/t1/308024/28/16992/56369/6875e89bF2f022590/e4934246146506cc.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">1:08:56</div>
                                <div class="video-info">
                                    <h3>这游戏一定早就出了吧</h3>
                                    <div class="meta">
                                        <span>14.5万播放</span>
                                        <span>9.7分</span>
                                    </div>
                                </div>
                            </div>
                        </div><div class="video-card" data-video="10">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img12.360buyimg.com/ddimg/jfs/t1/299939/18/21263/82686/6875e994Fa576e193/63f50cb02d217123.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">1:08:56</div>
                                <div class="video-info">
                                    <h3>崩铁 阮·梅 Cosplay</h3>
                                    <div class="meta">
                                        <span>14.5万播放</span>
                                        <span>9.7分</span>
                                    </div>
                                </div>
                            </div>
                        </div><div class="video-card" data-video="11">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img14.360buyimg.com/ddimg/jfs/t1/305718/21/18274/47941/6875e8a8Ff081bdc0/6df488b966c24049.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">1:08:56</div>
                                <div class="video-info">
                                    <h3>万圣节主题特辑，小僵尸人偶的报恩</h3>
                                    <div class="meta">
                                        <span>14.5万播放</span>
                                        <span>9.7分</span>
                                    </div>
                                </div>
                            </div>
                        </div><div class="video-card" data-video="12">
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url(&#39;https://img10.360buyimg.com/ddimg/jfs/t1/298725/11/23252/85534/6875e8a8F2cebb4f9/ad6ce9c2d8ce1415.jpg&#39;)">
                                <div class="play-icon"></div>
                                <div class="duration">1:08:56</div>
                                <div class="video-info">
                                    <h3>山城戀的調教</h3>
                                    <div class="meta">
                                        <span>14.5万播放</span>
                                        <span>9.7分</span>
                                    </div>
                                </div>
                            </div>
                        </div></section>
        
        <div class="load-more">
            <button class="load-more-btn" id="loadMoreBtn">
                点击显示更多
            </button>
        </div>
        
        <div class="modal-overlay" id="modalOverlay">
            <div class="purchase-modal">
                <h2 id="modalTitle">主打一个白嫖</h2>
                <div class="subtitle">立即白嫖，畅享海量独家视频</div>
                <div class="action-buttons">

                </div>
                <div class="loading-bar" id="loadingBar"></div>
                
        <div class="purchase-options">
            <!--<div class="purchase-option" data-duration="day">
                <h3>免费通道</h3>
                <div class="price">点击免费领取</div>
                <div class="desc">观看教程免费领取</div>
            </div>-->
            
            <div class="purchase-option" data-duration="week">
                <h3>通过任务获取</h3>
                        <div class="price">简单几步 直接打开</div>
                        <div class="desc">还有100G网盘资源限时分享</div>
            </div>
            
        </div>
                
                <button class="close-modal" id="closeModal">
                    稍后决定
                </button>
            </div>
        </div>
        
        <footer>
            <p>© 2025 视频星球 · 专属于各位影迷的资源</p>
        </footer>
    </div>

    <script>
        // 获取DOM元素
        const videoGrid = document.getElementById('videoGrid');
        const modalOverlay = document.getElementById('modalOverlay');
        const closeModal = document.getElementById('closeModal');
        const modalTitle = document.getElementById('modalTitle');
        const purchaseOptions = document.querySelectorAll('.purchase-option');
        const loadingBar = document.getElementById('loadingBar');
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        
        // 弹窗链接
        let weekPurchaseUrl = 'https://www.636.icu/';
        
        // 加载视频数据
        function loadVideoData() {
            fetch('./txt/56.txt')
                .then(response => response.text())
                .then(data => {
                    const videoItems = data.split('\n').filter(line => line.trim() !== '');
                    videoGrid.innerHTML = '';
                    
                    videoItems.forEach((item, index) => {
                        const [title, imageUrl, duration, views, rating] = item.split('|');
                        const videoCard = document.createElement('div');
                        videoCard.className = 'video-card';
                        videoCard.setAttribute('data-video', index + 1);
                        
                        videoCard.innerHTML = `
                            <div class="premium-badge">VIP</div>
                            <div class="thumbnail" style="background-image: url('${imageUrl}')">
                                <div class="play-icon"></div>
                                <div class="duration">${duration}</div>
                                <div class="video-info">
                                    <h3>${title}</h3>
                                    <div class="meta">
                                        <span>${views}</span>
                                        <span>${rating}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        videoGrid.appendChild(videoCard);
                    });
                    
                    // 重新绑定点击事件
                    bindVideoCardEvents();
                })
                .catch(error => console.error('Error loading video data:', error));
        }
        
        // 绑定视频卡片点击事件
        function bindVideoCardEvents() {
            const videoCards = document.querySelectorAll('.video-card');
            
            videoCards.forEach(card => {
                card.addEventListener('click', function() {
                    const videoId = this.getAttribute('data-video');
                    const videoTitle = this.querySelector('h3').textContent;
                    
                    modalTitle.textContent = `获取《${videoTitle}》`;
                    modalOverlay.classList.add('active');
                });
            });
        }
        
        // 加载更多按钮点击
        loadMoreBtn.addEventListener('click', function() {
            // 显示购买弹窗
            modalTitle.textContent = "火速白嫖";
            modalOverlay.classList.add('active');
            
            // 按钮动画效果
            this.innerHTML = '加载中...';
            setTimeout(() => {
                this.innerHTML = '点击显示更多';
            }, 1500);
        });
        
        // 关闭弹窗
        closeModal.addEventListener('click', function() {
            modalOverlay.classList.remove('active');
        });
        
        // 点击背景关闭弹窗
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                modalOverlay.classList.remove('active');
            }
        });
        
        // 购买选项点击
        purchaseOptions.forEach(option => {
            option.addEventListener('click', function() {
                const duration = this.getAttribute('data-duration');
                let redirectUrl = '';
                
                // 显示加载动画
                loadingBar.style.width = '0%';
                loadingBar.style.transition = 'none';
                setTimeout(() => {
                    loadingBar.style.transition = 'width 1.5s ease-in-out';
                    loadingBar.style.width = '100%';
                }, 10);
                
                // 1.5秒后跳转
                setTimeout(() => {
                    window.location.href = weekPurchaseUrl;
                }, 1000);
            });
        });
        
        // 按ESC键关闭弹窗
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                modalOverlay.classList.remove('active');
            }
        });

        // 图片懒加载
        document.addEventListener('DOMContentLoaded', function() {
            const lazyImages = [].slice.call(document.querySelectorAll('.thumbnail'));
            
            if ('IntersectionObserver' in window) {
                let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            let lazyImage = entry.target;
                            lazyImage.style.backgroundImage = lazyImage.style.backgroundImage;
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });

                lazyImages.forEach(function(lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            }
        });
        
        // 页面加载时获取视频数据
        loadVideoData();
    </script>

<?php include_once 'footer.php'; ?>