<?php
require_once __DIR__ . '/../config/store.php';

header('Cache-Control: no-store, private');
$order = null;
$orderItems = [];
$error = '';
$orderCode = is_string($_POST['order_code'] ?? null) ? strtoupper(trim($_POST['order_code'])) : '';
$phone = is_string($_POST['phone'] ?? null) ? trim($_POST['phone']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $error = 'Phiên tra cứu đã hết hạn. Vui lòng tải lại trang và thử lại.';
    } elseif ($orderCode === '' || text_length($orderCode) > 32 || !valid_phone($phone)) {
        $error = 'Nhập mã đơn hàng và số điện thoại đã dùng khi đặt hàng.';
    } elseif (!db()) {
        $error = 'Chưa kết nối được cơ sở dữ liệu. Vui lòng thử lại sau.';
    } else {
        try {
            $statement = db()->prepare(
                'SELECT id, ma_don, ten_nguoi_nhan, tam_tinh, '
                . 'phi_giao_hang, tong_tien, trang_thai, created_at '
                . 'FROM don_hang WHERE ma_don = ? AND so_dien_thoai = ? LIMIT 1'
            );
            $statement->execute([$orderCode, $phone]);
            $order = $statement->fetch() ?: null;
            if ($order) {
                $statement = db()->prepare(
                    'SELECT ten_san_pham, so_luong, don_gia, thanh_tien '
                    . 'FROM don_hang_chi_tiet WHERE order_id = ? ORDER BY id'
                );
                $statement->execute([(int)$order['id']]);
                $orderItems = $statement->fetchAll();
            } else {
                $error = 'Không tìm thấy đơn hàng với thông tin đã nhập.';
            }
        } catch (PDOException $exception) {
            error_log('Order lookup failed: ' . $exception->getMessage());
            $error = 'Chưa thể tra cứu đơn hàng lúc này. Vui lòng thử lại sau.';
        }
    }
}

$statuses = [
    'pending' => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận',
    'shipping' => 'Đang giao hàng',
    'completed' => 'Đã hoàn tất',
    'cancelled' => 'Đã hủy',
];
$pageTitle = 'Kiểm tra đơn hàng | IBP Technology';
require_once __DIR__ . '/../layouts/header.php';
?>
<main class="shop-page order-lookup-page">
    <div class="shop-container">
        <nav class="shop-breadcrumb" aria-label="Đường dẫn">
            <a href="<?= BASE_PATH ?>/home/home.php">Trang chủ</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span>Kiểm tra đơn hàng</span>
        </nav>
        <div class="section-heading">
            <div>
                <span class="section-kicker">IBP TECHNOLOGY</span>
                <h1>Kiểm tra đơn hàng</h1>
            </div>
        </div>
        <section class="detail-card checkout-form-card">
            <p>Nhập mã đơn hàng và số điện thoại đã cung cấp khi đặt hàng.</p>
            <?php if ($error !== ''): ?>
                <div class="form-alert" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>
            <form method="post" class="checkout-form">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                <label>
                    Mã đơn hàng
                    <input name="order_code" value="<?= e($orderCode) ?>" autocomplete="off" required>
                </label>
                <label>
                    Số điện thoại đặt hàng
                    <input type="tel" name="phone" value="<?= e($phone) ?>" autocomplete="tel" required>
                </label>
                <button class="button-primary" type="submit">Tra cứu đơn hàng</button>
            </form>
        </section>
        <?php if ($order): ?>
            <section class="detail-card checkout-summary" aria-labelledby="order-result-title">
                <h2 id="order-result-title">Đơn hàng <?= e($order['ma_don']) ?></h2>
                <p>
                    Trạng thái:
                    <strong><?= e($statuses[$order['trang_thai']] ?? 'Đang xử lý') ?></strong>
                </p>
                <p>Người nhận: <?= e($order['ten_nguoi_nhan']) ?></p>
                <p>Ngày đặt: <?= e(date('d-m-Y H:i', strtotime($order['created_at']))) ?></p>
                <h3>Sản phẩm</h3>
                <?php foreach ($orderItems as $item): ?>
                    <div class="summary-row">
                        <span><?= e($item['ten_san_pham']) ?> × <?= (int)$item['so_luong'] ?></span>
                        <b><?= money((float)$item['thanh_tien']) ?></b>
                    </div>
                <?php endforeach; ?>
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <b><?= money((float)$order['tam_tinh']) ?></b>
                </div>
                <div class="summary-row">
                    <span>Giao hàng</span>
                    <b><?= money((float)$order['phi_giao_hang']) ?></b>
                </div>
                <div class="summary-row summary-total">
                    <span>Tổng cộng</span>
                    <strong><?= money((float)$order['tong_tien']) ?></strong>
                </div>
            </section>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
