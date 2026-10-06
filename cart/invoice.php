<?php
require_once __DIR__ . '/../config/store.php';

// Bắt buộc người dùng phải đăng nhập trước khi vào trang hóa đơn / thanh toán
if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_PATH . '/auth/login.php?redirect=' . urlencode(BASE_PATH . '/cart/invoice.php') . '&msg=checkout_required');
    exit;
}

$buyNow = $_SESSION['buy_now'] ?? null;
$cart = $buyNow
    ? [(int)$buyNow['product_id'] => max(1, min(99, (int)$buyNow['quantity']))]
    : ($_SESSION['cart'] ?? []);
$products = $buyNow ? [] : cart_products();
if ($buyNow && db()) {
    $stmt = db()->prepare(
        'SELECT id, ten, gia, gia_khuyen_mai, anh_chinh, so_luong_ton '
        . 'FROM product WHERE id = ? AND hien_thi = 1'
    );
    $stmt->execute([(int)$buyNow['product_id']]);
    $row = $stmt->fetch();
    if ($row) {
        $products[(int)$row['id']] = $row;
    }
}
if (!$cart || !$products) {
    $destination = BASE_PATH . '/cart/index.php';
    if ($buyNow && !db()) {
        $destination .= '?error=database';
    }
    header('Location: ' . $destination);
    exit;
}

$lines = [];
$subtotal = 0.0;
foreach ($products as $id => $product) {
    $quantity = max(1, min(99, (int)($cart[$id] ?? 1)));
    $selection = $buyNow && (int)$buyNow['product_id'] === (int)$id
        ? $buyNow
        : ($_SESSION['cart_options'][$id] ?? []);
    $variant = cart_variant((int)$id, (int)($selection['variant_id'] ?? 0));
    $addons = cart_addons((int)$id, $selection['addon_ids'] ?? []);
    $variantPrice = (!empty($variant) && !empty($variant['gia'])) ? (float)$variant['gia'] : 0.0;
    $unitPrice = $variantPrice > 0 ? $variantPrice : (float)(!empty($product['gia_khuyen_mai']) ? $product['gia_khuyen_mai'] : $product['gia']);
    $subtotal += $unitPrice * $quantity;
    foreach ($addons as $addon) {
        $subtotal += (float)($addon['gia_khuyen_mai'] ?: $addon['gia_goc']) * $quantity;
    }
    $lines[(int)$id] = compact('product', 'quantity', 'selection', 'variant', 'addons', 'unitPrice');
}
$shipping = 0.0;
$total = $subtotal + $shipping;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $address === '') {
        $error = 'Vui lòng điền đầy đủ và chính xác thông tin nhận hàng.';
    } elseif (!db()) {
        $error = 'Chưa kết nối được cơ sở dữ liệu. Hãy import database/ibpvn.sql trong phpMyAdmin trước.';
    } else {
        try {
            db()->beginTransaction();
            $stockCheck = db()->prepare(
                'SELECT so_luong_ton FROM product WHERE id = ? AND hien_thi = 1 FOR UPDATE'
            );
            foreach ($lines as $id => $line) {
                $stockCheck->execute([$id]);
                $available = $stockCheck->fetchColumn();
                if ($available === false || $line['quantity'] > (int)$available) {
                    throw new RuntimeException('Insufficient stock for product ' . $id);
                }
            }

            $code = 'IBP' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));
            $stmt = db()->prepare(
                'INSERT INTO don_hang '
                . '(ma_don, user_id, ten_nguoi_nhan, email, so_dien_thoai, dia_chi, '
                . 'tam_tinh, phi_giao_hang, tong_tien, ghi_chu) '
                . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $code,
                $_SESSION['user']['id'] ?? null,
                $name,
                $email,
                $phone,
                $address,
                $subtotal,
                $shipping,
                $total,
                trim($_POST['note'] ?? ''),
            ]);
            $orderId = (int)db()->lastInsertId();
            $insertLine = db()->prepare(
                'INSERT INTO don_hang_chi_tiet '
                . '(order_id, product_id, ten_san_pham, so_luong, don_gia, thanh_tien) '
                . 'VALUES (?, ?, ?, ?, ?, ?)'
            );
            $updateStock = db()->prepare(
                'UPDATE product '
                . 'SET so_luong_ton = so_luong_ton - ?, '
                . 'da_ban = da_ban + ? '
                . 'WHERE id = ? AND so_luong_ton >= ?'
            );
            foreach ($lines as $id => $line) {
                $productName = $line['product']['ten'];
                if ($line['variant']) {
                    $productName .= ' — ' . $line['variant']['ten_phien_ban'];
                }

                $insertLine->execute([
                    $orderId,
                    $id,
                    $productName,
                    $line['quantity'],
                    $line['unitPrice'],
                    $line['unitPrice'] * $line['quantity'],
                ]);
                foreach ($line['addons'] as $addon) {
                    $addonPrice = (float)($addon['gia_khuyen_mai'] ?: $addon['gia_goc']);
                    $insertLine->execute([
                        $orderId,
                        null,
                        'Phụ kiện: ' . $addon['ten'],
                        $line['quantity'],
                        $addonPrice,
                        $addonPrice * $line['quantity'],
                    ]);
                }

                // Cập nhật số lượng tồn kho và lượt đã bán
                $updateStock->execute([
                    $line['quantity'],
                    $line['quantity'],
                    $id,
                    $line['quantity'],
                ]);
                if ($updateStock->rowCount() !== 1) {
                    throw new RuntimeException('Stock changed while placing order for product ' . $id);
                }
            }

            db()->commit();
            if ($buyNow) {
                unset($_SESSION['buy_now']);
            } else {
                $_SESSION['cart'] = [];
                $_SESSION['cart_options'] = [];
            }
            $_SESSION['last_order'] = $code;
            $_SESSION['last_order_total'] = $total;
            header('Location: ' . BASE_PATH . '/cart/success.php');
            exit;
        } catch (Throwable $exception) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            $error = 'Chưa thể tạo đơn hàng. Vui lòng thử lại hoặc liên hệ IBP.';
        }
    }
}

$pageTitle = 'Hóa đơn & thông tin giao hàng | IBP Technology';
require_once __DIR__ . '/../layouts/header.php';
?>
<main class="shop-page checkout-page invoice-page">
    <div class="shop-container">
        <nav class="shop-breadcrumb">
            <a href="<?= BASE_PATH ?>/home/home.php">Trang chủ</a>
            <i class="bi bi-chevron-right"></i>
            <a href="<?= BASE_PATH ?>/cart/index.php">Giỏ hàng</a>
            <i class="bi bi-chevron-right"></i>
            <span>Hóa đơn</span>
        </nav>
        <div class="section-heading">
            <div>
                <span class="section-kicker">ĐƠN HÀNG CỦA BẠN</span>
                <h1>Thông tin hóa đơn</h1>
            </div>
        </div>
        <div class="checkout-layout">
            <section class="detail-card checkout-form-card">
                <h2>Thông tin nhận hàng</h2>
                <p>Kiểm tra sản phẩm và nhập địa chỉ để IBP xác nhận đơn hàng.</p>
                <?php if ($error): ?>
                    <div class="form-alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>
                <form method="post" class="checkout-form">
                    <label>
                        Họ và tên
                        <input
                            name="name"
                            value="<?= e($_POST['name'] ?? ($_SESSION['user']['name'] ?? '')) ?>"
                            autocomplete="name"
                            required
                        >
                    </label>
                    <div class="checkout-two-col">
                        <label>
                            Email
                            <input
                                type="email"
                                name="email"
                                value="<?= e($_POST['email'] ?? ($_SESSION['user']['email'] ?? '')) ?>"
                                autocomplete="email"
                                required
                            >
                        </label>
                        <label>
                            Số điện thoại
                            <input
                                type="tel"
                                name="phone"
                                value="<?= e($_POST['phone'] ?? '') ?>"
                                autocomplete="tel"
                                required
                            >
                        </label>
                    </div>
                    <label>
                        Địa chỉ nhận hàng
                        <input
                            name="address"
                            value="<?= e($_POST['address'] ?? '') ?>"
                            placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành"
                            autocomplete="street-address"
                            required
                        >
                    </label>
                    <label>
                        Ghi chú đơn hàng <span>(không bắt buộc)</span>
                        <textarea name="note" rows="3" placeholder="Hướng dẫn giao hàng hoặc lắp đặt"><?= e($_POST['note'] ?? '') ?></textarea>
                    </label>
                    <button class="button-primary checkout-button" type="submit">
                        Xác nhận đặt hàng · <?= money($total) ?> <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
                <div class="checkout-privacy">
                    <i class="bi bi-lock"></i>
                    Thông tin của bạn được bảo mật và chỉ dùng để xử lý đơn hàng.
                </div>
            </section>
            <aside class="detail-card checkout-summary">
                <h2>Chi tiết hóa đơn</h2>
                <?php foreach ($lines as $id => $line): ?>
                    <div class="checkout-product">
                        <img
                            src="<?= e($line['product']['anh_chinh'] ?: BASE_PATH . '/assets/images/sanpham.png') ?>"
                            alt=""
                        >
                        <span>
                            <?= e($line['product']['ten']) ?>
                            <?php if ($line['variant']): ?>
                                <small>Phiên bản: <?= e($line['variant']['ten_phien_ban']) ?></small>
                            <?php endif; ?>
                            <small>Số lượng: <?= $line['quantity'] ?></small>
                        </span>
                        <b><?= money($line['unitPrice'] * $line['quantity']) ?></b>
                    </div>
                    <?php foreach ($line['addons'] as $addon): ?>
                        <?php $addonPrice = (float)($addon['gia_khuyen_mai'] ?: $addon['gia_goc']); ?>
                        <div class="checkout-product invoice-addon">
                            <img
                                src="<?= e($addon['image_url'] ?: BASE_PATH . '/assets/images/loiloc.png') ?>"
                                alt=""
                            >
                            <span>
                                + <?= e($addon['ten']) ?>
                                <small>Số lượng: <?= $line['quantity'] ?></small>
                            </span>
                            <b><?= money($addonPrice * $line['quantity']) ?></b>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <b><?= money($subtotal) ?></b>
                </div>
                <div class="summary-row">
                    <span>Giao hàng</span>
                    <b class="free-shipping">Miễn phí</b>
                </div>
                <div class="summary-row summary-total">
                    <span>Tổng cộng</span>
                    <strong><?= money($total) ?></strong>
                </div>
                <div class="secure-note">
                    <i class="bi bi-shield-check"></i>
                    Thanh toán an toàn và bảo mật
                </div>
            </aside>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
