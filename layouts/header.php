<?php
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if (preg_match('#/(product|about)$#', $base)) {
    $base = dirname($base);
}
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../config/config.php';
}
$base = ($base === '/' || $base === '\\') ? '' : $base;
$headerUser = $_SESSION['user'] ?? null;
$headerUsername = is_array($headerUser)
    ? ($headerUser['username'] ?? $headerUser['name'] ?? '')
    : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'IBP Technology - '?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/fancybox.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12.2.0/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/style.css?v=11">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/shop.css?v=10">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/style-responsive.css?v=16">
</head>
<body>
<!-- ==================== HEADER ==================== -->
    <header class="site-header">
        <div class="header-banner">
            <a href="<?= BASE_PATH ?>/product/catalog.php" class="header-banner-link">
                <img src="<?= BASE_PATH ?>/assets/images/header_promotion.png" alt="Đặt hàng Online - Nhận ngay ưu đãi" class="header-banner-image">
            </a>
        </div>
        <div class="header-top">
            <div class="header-top-inner">
                <div class="header-top-message">Đăng nhập để theo dõi đơn hàng và nhận hỗ trợ từ IBP</div>
                <div class="header-top-links">
                    <a href="mailto:info@ibpvn.com?subject=Dealer%20application" class="header-top-link">
                        <iconify-icon icon="fa7-solid:hands-holding-child" class="header-top-icon"></iconify-icon>
                        <span>Tuyển đại lý</span>
                    </a>
                    <a href="<?= BASE_PATH ?>/home/home.php#about" class="header-top-link">
                        <iconify-icon icon="solar:document-text-outline" class="header-top-icon"></iconify-icon>
                        <span>Giới thiệu</span>
                    </a>
                    <a href="<?= BASE_PATH ?>/contact.php" class="header-top-link">
                        <iconify-icon icon="solar:headphones-round-outline" class="header-top-icon"></iconify-icon>
                        <span>Liên hệ</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="header-main">
            <div class="header-main-inner">
                <div class="mobile-header-tools">
                    <button class="mobile-menu-toggle" type="button" aria-label="Mở danh mục sản phẩm" aria-controls="mobile-category-panel" aria-expanded="false">
                        <i class="bi bi-list" aria-hidden="true"></i>
                    </button>
                    <a class="mobile-shop-link" href="<?= BASE_PATH ?>/product/catalog.php"><i class="bi bi-shop" aria-hidden="true"></i><span>Xem Shop</span></a>
                </div>
                <a href="<?= BASE_PATH ?>/home/home.php" class="header-logo">
                    <img src="<?= BASE_PATH ?>/assets/images/logo_IBP.png" alt="IBP Technology" class="header-logo-image">
                </a>
                <form action="<?= BASE_PATH ?>/product/catalog.php" method="get" class="header-search">
                    <input
                        type="text"
                        name="keyword"
                        class="header-search-input"
                        placeholder="Tìm sản phẩm..."
                        value="<?= isset($_GET['keyword']) ? htmlspecialchars((string)$_GET['keyword'], ENT_QUOTES, 'UTF-8') : '' ?>"
                        autocomplete="off"
                    >
                    <button type="submit" class="header-search-button" aria-label="Tìm kiếm">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                <button class="mobile-search-toggle" type="button" aria-label="Tìm kiếm" aria-expanded="false"><i class="bi bi-search" aria-hidden="true"></i></button>
                <div class="header-actions">
                    <a href="<?= BASE_PATH ?>/cart/track.php" class="header-action header-order-check">
                        <i class="bi bi-file-earmark-text header-action-icon"></i>
                        <span class="header-action-text">Kiểm tra đơn hàng</span>
                    </a>
                    <?php if ($headerUsername !== ''): ?>
                        <div class="header-action header-account" style="display:inline-flex;align-items:center;gap:4px;">
                            <i class="bi bi-person-circle header-action-icon"></i>
                            <span class="header-action-text header-account-name" style="font-weight:600;">
                                <?= htmlspecialchars((string)$headerUsername, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <form action="<?= BASE_PATH ?>/auth/logout.php" method="post" style="display:inline;margin-left:4px;">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" style="padding:0;border:0;background:none;color:#d9534f;font-size:11px;text-decoration:none;font-weight:bold;cursor:pointer;" title="Đăng xuất">(Thoát)</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_PATH ?>/auth/login.php" class="header-action header-account">
                            <i class="bi bi-person-circle header-action-icon"></i>
                            <span class="header-action-text">Đăng nhập/Đăng ký</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?= BASE_PATH ?>/cart/index.php" class="header-action header-cart">
                        <span class="header-cart-icon-wrap">
                            <i class="bi bi-cart3 header-action-icon"></i>
                            <span class="cart-count"><?= array_sum($_SESSION['cart'] ?? []) ?></span>
                        </span>
                        <span class="header-action-text">Giỏ hàng</span>
                    </a>
                </div>
            </div>
        </div>
        <nav class="mobile-category-panel" id="mobile-category-panel" aria-label="Danh mục sản phẩm" hidden>
            <a href="<?= BASE_PATH ?>/product/catalog.php?category=may-loc-nuoc">Máy lọc nước <i class="bi bi-chevron-right"></i></a>
            <a href="<?= BASE_PATH ?>/product/catalog.php?category=may-nuoc-nong">Máy nước nóng <i class="bi bi-chevron-right"></i></a>
            <a href="<?= BASE_PATH ?>/product/catalog.php?category=may-loc-khong-khi">Máy lọc không khí <i class="bi bi-chevron-right"></i></a>
            <a href="<?= BASE_PATH ?>/product/catalog.php?category=may-loc-nuoc-dau-nguon">Máy lọc nước đầu nguồn <i class="bi bi-chevron-right"></i></a>
            <a href="<?= BASE_PATH ?>/product/catalog.php?category=loi-loc">Lõi lọc <i class="bi bi-chevron-right"></i></a>
            <a href="<?= BASE_PATH ?>/product/catalog.php?category=may-lanh">Máy lạnh <i class="bi bi-chevron-right"></i></a>
            <a href="<?= BASE_PATH ?>/product/catalog.php?category=phu-kien">Phụ kiện <i class="bi bi-chevron-right"></i></a>
        </nav>
        <!-- ==================== MENU NAV ==================== -->
        <nav class="header-navigation">
            <div class="header-navigation-inner">
                <a href="<?= BASE_PATH ?>/home/home.php#about" class="navigation-item"><span>Giới thiệu</span></a>
                <span class="navigation-divider"></span>
                <a href="<?= BASE_PATH ?>/product/catalog.php?category=may-loc-nuoc" class="navigation-item"><span>Máy lọc nước</span></a>
                <span class="navigation-divider"></span>
                <a href="<?= BASE_PATH ?>/product/catalog.php?category=may-nuoc-nong" class="navigation-item"><span>Máy nước nóng</span></a>
                <span class="navigation-divider"></span>
                <a href="<?= BASE_PATH ?>/product/catalog.php?category=may-loc-nuoc-dau-nguon" class="navigation-item"><span>Máy lọc nước đầu nguồn</span></a>
                <span class="navigation-divider"></span>
                <a href="<?= BASE_PATH ?>/product/catalog.php?category=may-loc-khong-khi" class="navigation-item"><span>Máy lọc không khí</span></a>
                <span class="navigation-divider"></span>
                <a href="<?= BASE_PATH ?>/product/catalog.php?category=loi-loc" class="navigation-item"><span>Lõi lọc</span></a>
                <span class="navigation-divider"></span>
                <a href="<?= BASE_PATH ?>/contact.php" class="navigation-item navigation-service"><span>Dịch vụ</span><i class="bi bi-chevron-down navigation-service-icon"></i></a>
                <span class="navigation-divider"></span>
                <a href="<?= BASE_PATH ?>/product/catalog.php?category=phu-kien" class="navigation-item"><span>Phụ kiện</span></a>
                <span class="navigation-divider"></span>
                <a href="<?= BASE_PATH ?>/news/detail.php" class="navigation-item"><span>Cẩm nang</span></a>
                <span class="navigation-divider"></span>
                <a href="<?= BASE_PATH ?>/contact.php" class="navigation-item"><span>Liên hệ</span></a>
            </div>
        </nav>
    </header>
