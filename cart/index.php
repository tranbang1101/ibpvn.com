<?php
require_once __DIR__ . '/../config/store.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = max(1, (int)($_POST['product_id'] ?? 0));

    if ($action === 'remove') {
        unset($_SESSION['cart'][$id], $_SESSION['cart_options'][$id]);
    } elseif ($action === 'update' && isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = max(1, min(99, (int)($_POST['quantity'] ?? 1)));
    }

    header('Location: ' . BASE_PATH . '/cart/index.php');
    exit;
}
$products = cart_products();
$cart = $_SESSION['cart'] ?? [];
$subtotal = cart_total($products);
$pageTitle = 'Giỏ hàng | IBP Technology';
require_once __DIR__ . '/../layouts/header.php';
?>
<main class="shop-page cart-page">
    <div class="shop-container">
        <nav class="shop-breadcrumb">
            <a href="<?= BASE_PATH ?>/home/home.php">Trang chủ</a>
            <i class="bi bi-chevron-right"></i>
            <span>Giỏ hàng</span>
        </nav>
        <div class="section-heading cart-heading">
            <div>
                <span class="section-kicker">IBP TECHNOLOGY</span>
                <h1>Giỏ hàng của bạn</h1>
            </div>
            <span class="cart-item-count"><?= array_sum($cart) ?> sản phẩm</span>
        </div>
        <?php if (isset($_GET['added'])): ?>
            <div class="cart-notice">
                <i class="bi bi-check-circle-fill"></i>
                Sản phẩm đã được thêm vào giỏ hàng.
            </div>
        <?php endif; ?>
        <?php if (($_GET['error'] ?? '') === 'database'): ?>
            <div class="form-alert" role="alert">
                <i class="bi bi-exclamation-circle"></i>
                Cơ sở dữ liệu đang tạm thời không phản hồi nên chưa thể tạo hóa đơn. Vui lòng thử lại sau.
            </div>
        <?php endif; ?>
        <?php if (!$products): ?>
            <section class="empty-cart detail-card">
                <div class="empty-cart-icon"><i class="bi bi-bag"></i></div>
                <h2>Giỏ hàng đang trống</h2>
                <p>Khám phá các giải pháp chăm sóc nguồn nước phù hợp cho gia đình bạn.</p>
                <a class="button-primary" href="<?= BASE_PATH ?>/product/catalog.php">
                    Khám phá sản phẩm <i class="bi bi-arrow-right"></i>
                </a>
            </section>
        <?php else: ?>
            <div class="cart-layout">
                <section class="cart-items-panel detail-card">
                    <div class="cart-table-head">
                        <span>Sản phẩm</span>
                        <span>Đơn giá</span>
                        <span>Số lượng</span>
                        <span>Tạm tính</span>
                    </div>
                    <?php foreach ($products as $id => $product):
                        $qty = max(1, (int)($cart[$id] ?? 1));
                        $variant = cart_variant((int)$id);
                        $addons = cart_addons((int)$id);
                        $lineTotal = cart_product_total((int)$id, $product, $qty);
                    ?>
                        <article class="cart-line">
                            <div class="cart-product">
                                <a href="<?= BASE_PATH ?>/product/detail.php?id=<?= (int)$id ?>">
                                    <img
                                        src="<?= e($product['anh_chinh'] ?: BASE_PATH . '/assets/images/sanpham.png') ?>"
                                        alt="<?= e($product['ten']) ?>"
                                    >
                                </a>
                                <div>
                                    <a class="cart-product-name" href="<?= BASE_PATH ?>/product/detail.php?id=<?= (int)$id ?>">
                                        <?= e($product['ten']) ?>
                                    </a>
                                    <small>Chính hãng · Bảo hành theo nhà sản xuất</small>
                                    <?php if ($variant): ?>
                                        <small>Phiên bản: <?= e($variant['ten_phien_ban']) ?></small>
                                    <?php endif; ?>
                                    <?php foreach ($addons as $addon): ?>
                                        <small>
                                            + <?= e($addon['ten']) ?> ·
                                            <?= money((float)($addon['gia_khuyen_mai'] ?: $addon['gia_goc'])) ?>
                                        </small>
                                    <?php endforeach; ?>
                                    <form method="post">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?= (int)$id ?>">
                                        <button class="remove-item" type="submit">
                                            <i class="bi bi-trash3"></i> Xóa sản phẩm
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <strong class="cart-price"><?= money($lineTotal / $qty) ?></strong>
                            <form method="post" class="cart-quantity-form">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?= (int)$id ?>">
                                <div class="quantity-control">
                                    <button name="quantity" value="<?= max(1, $qty - 1) ?>" aria-label="Giảm">−</button>
                                    <input type="number" name="quantity" min="1" max="99" value="<?= $qty ?>" aria-label="Số lượng">
                                    <button name="quantity" value="<?= min(99, $qty + 1) ?>" aria-label="Tăng">+</button>
                                </div>
                            </form>
                            <strong class="cart-line-total"><?= money($lineTotal) ?></strong>
                        </article>
                    <?php endforeach; ?>
                    <a class="continue-shopping" href="<?= BASE_PATH ?>/product/detail.php">
                        <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                </section>
                <aside class="cart-summary detail-card">
                    <h2>Tóm tắt đơn hàng</h2>
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
                        <strong><?= money($subtotal) ?></strong>
                    </div>
                    <small class="vat-note">
                        Đã bao gồm VAT (nếu có). Phí giao hàng được xác nhận khi đặt hàng.
                    </small>
                    <a class="button-primary checkout-button" href="<?= BASE_PATH ?>/cart/invoice.php">
                        Tiến hành đặt hàng <i class="bi bi-arrow-right"></i>
                    </a>
                    <div class="secure-note">
                        <i class="bi bi-shield-check"></i> Thanh toán an toàn và bảo mật
                    </div>
                    <div class="cart-support">
                        <i class="bi bi-headset"></i>
                        <span>Cần hỗ trợ?<b> <a href="tel:0983537155">0983537155</a></b></span>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
