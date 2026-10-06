<?php
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Tin tức | IBP Technology';
require_once __DIR__ . '/../layouts/header.php';
?>
<main class="shop-page news-detail-page">
    <div class="shop-container">
        <nav class="shop-breadcrumb" aria-label="Đường dẫn">
            <a href="<?= BASE_PATH ?>/home/home.php">Trang chủ</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span>Tin tức</span>
        </nav>
        <section class="detail-card news-detail-message">
            <span class="section-kicker">IBP TECHNOLOGY</span>
            <h1>Nội dung tin tức đang được cập nhật</h1>
            <p>Danh sách bài viết sẽ sớm được bổ sung. Trong lúc chờ, bạn có thể xem các sản phẩm đang kinh doanh tại IBP.</p>
            <a class="button-primary" href="<?= BASE_PATH ?>/product/catalog.php">
                Xem sản phẩm <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        </section>
    </div>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
