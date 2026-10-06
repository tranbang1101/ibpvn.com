<?php
require_once __DIR__ . '/../config/store.php';
$orderCode = $_SESSION['last_order'] ?? '';
$orderTotal = (float)($_SESSION['last_order_total'] ?? 0);
unset($_SESSION['last_order'], $_SESSION['last_order_total']);
$orderWasPlaced = is_string($orderCode) && $orderCode !== '';
$pageTitle = $orderWasPlaced ? 'Đặt hàng thành công | IBP Technology' : 'Không có đơn hàng mới | IBP Technology';
if (!$orderWasPlaced) {
    http_response_code(404);
}
require_once __DIR__ . '/../layouts/header.php';
?>
<main class="shop-page order-success-page">
    <section class="detail-card order-success-card">
        <span class="success-check">
            <i class="bi bi-<?= $orderWasPlaced ? 'check2' : 'info-circle' ?>"></i>
        </span>
        <span class="section-kicker">IBP TECHNOLOGY</span>
        <?php if ($orderWasPlaced): ?>
            <h1>Cảm ơn bạn đã đặt hàng!</h1>
            <p>Đơn hàng của bạn đã được ghi nhận. Đội ngũ IBP sẽ liên hệ xác nhận và sắp xếp giao hàng sớm nhất.</p>
            <div class="order-code">
                <span>Mã đơn hàng</span>
                <strong><?= e($orderCode) ?></strong>
            </div>
            <div class="order-code">
                <span>Tổng thanh toán</span>
                <strong><?= money($orderTotal) ?></strong>
            </div>
            <a class="button-primary" href="<?= BASE_PATH ?>/cart/track.php">
                Kiểm tra đơn hàng <i class="bi bi-arrow-right"></i>
            </a>
        <?php else: ?>
            <h1>Không có đơn hàng mới</h1>
            <p>Trang xác nhận chỉ hiển thị sau khi đặt hàng thành công.</p>
            <a class="button-primary" href="<?= BASE_PATH ?>/product/catalog.php">
                Xem sản phẩm <i class="bi bi-arrow-right"></i>
            </a>
        <?php endif; ?>
        <div class="success-support">
            Cần hỗ trợ? Gọi <b>0983 537 155</b>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
