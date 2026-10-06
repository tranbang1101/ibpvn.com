<?php
require_once __DIR__ . '/config/database.php';
$sent = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $productId = max(0, (int)($_POST['product_id'] ?? 0));
    $productCategory = trim((string)($_POST['product'] ?? ''));
    $categoryNames = [
        'may-loc-nuoc' => 'Máy lọc nước',
        'may-nuoc-nong' => 'Máy nước nóng',
        'may-loc-khong-khi' => 'Máy lọc không khí',
        'may-loc-nuoc-dau-nguon' => 'Máy lọc nước đầu nguồn',
        'loi-loc' => 'Lõi lọc',
    ];

    if ($productCategory !== '' && isset($categoryNames[$productCategory])) {
        $message = 'Danh mục quan tâm: ' . $categoryNames[$productCategory]
            . ($message !== '' ? "\n" . $message : '');
    }

    if ($name === '' || $phone === '') {
        $error = 'Vui lòng nhập họ tên và số điện thoại để IBP có thể liên hệ.';
    } elseif (!db()) {
        $error = 'Chưa kết nối được cơ sở dữ liệu. Vui lòng gọi 0983 537 155 để được tư vấn.';
    } else {
        try {
            if ($productId > 0) {
                $productCheck = db()->prepare('SELECT id FROM product WHERE id = ? LIMIT 1');
                $productCheck->execute([$productId]);
                if (!$productCheck->fetchColumn()) {
                    $productId = 0;
                }
            }

            $stmt = db()->prepare('INSERT INTO customer_consultations (product_id, ho_ten, so_dien_thoai, email, noi_dung) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$productId ?: null, $name, $phone, $email ?: null, $message ?: null]);
            $sent = true;
        } catch (PDOException $exception) {
            $error = 'Chưa thể lưu yêu cầu lúc này. Vui lòng gọi 0983 537 155 để được hỗ trợ.';
        }
    }
}
$pageTitle = 'Đăng ký tư vấn | IBP Technology';
require_once __DIR__ . '/layouts/header.php';
?>
<main class="shop-page order-success-page">
    <section class="detail-card order-success-card">
        <span class="success-check">
            <i class="bi bi-<?= $sent ? 'check2' : 'headset' ?>"></i>
        </span>
        <span class="section-kicker">IBP TECHNOLOGY</span>
        <h1><?= $sent ? 'Đã nhận yêu cầu tư vấn' : 'Đăng ký tư vấn' ?></h1>
        <p>
            <?= $sent
                ? 'Chuyên viên IBP sẽ liên hệ với bạn trong thời gian sớm nhất.'
                : e($error ?: 'Gửi yêu cầu tư vấn để đội ngũ IBP liên hệ hỗ trợ bạn.') ?>
        </p>
        <a class="button-primary" href="<?= BASE_PATH ?>/product/catalog.php">
            Xem sản phẩm <i class="bi bi-arrow-right"></i>
        </a>
        <div class="success-support">
            Cần hỗ trợ ngay? Gọi <b>0983 537 155</b>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>
