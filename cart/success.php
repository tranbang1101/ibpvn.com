<?php
require_once __DIR__ . '/../config/store.php';
$orderCode = $_SESSION['last_order'] ?? '';
$orderTotal = (float)($_SESSION['last_order_total'] ?? 0);
$pageTitle = 'Đặt hàng thành công | IBP Technology';
require_once __DIR__ . '/../layouts/header.php';
?>
<main class="shop-page order-success-page">
    <section class="detail-card order-success-card">
        <span class="success-check">
            <i class="bi bi-check2"></i>
        </span>
        <span class="section-kicker">IBP TECHNOLOGY</span>
        <h1>Cảm ơn bạn đã đặt hàng!</h1>
        <p>Đơn hàng của bạn đã được ghi nhận. Đội ngũ IBP sẽ liên hệ xác nhận và sắp xếp giao hàng sớm nhất.</p>
        <?php if ($orderCode): ?>
            <div class="order-code">
                <span>Mã đơn hàng</span>
                <strong><?= e($orderCode) ?></strong>
            </div>
        <?php endif; ?>
        <?php if ($orderTotal > 0): ?>
            <div class="order-code">
                <span>Tổng thanh toán</span>
                <strong><?= money($orderTotal) ?></strong>
            </div>
        <?php endif; ?>
        <a class="button-primary" href="<?= BASE_PATH ?>/home/home.php">
            Tiếp tục mua sắm <i class="bi bi-arrow-right"></i>
        </a>
        <div class="success-support">
            Cần hỗ trợ? Gọi <b>0983 537 155</b>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
