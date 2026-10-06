<?php
require_once __DIR__ . '/config/database.php';

$categories = [
    'may-loc-nuoc' => 'Máy lọc nước',
    'may-nuoc-nong' => 'Máy nước nóng',
    'may-loc-khong-khi' => 'Máy lọc không khí',
    'may-loc-nuoc-dau-nguon' => 'Máy lọc nước đầu nguồn',
    'loi-loc' => 'Lõi lọc',
    'may-lanh' => 'Máy lạnh',
    'phu-kien' => 'Phụ kiện',
];
$topics = [
    'warranty' => 'Chính sách bảo hành',
    'insurance' => 'Chính sách bảo hiểm',
    'refund' => 'Chính sách hoàn tiền',
    'promotion' => 'Chính sách khuyến mãi',
    'dealer' => 'Đăng ký làm đại lý',
];
$sent = ($_GET['sent'] ?? '') === '1' && !empty($_SESSION['contact_sent']);
unset($_SESSION['contact_sent']);
$error = '';
$name = is_string($_POST['name'] ?? null) ? trim($_POST['name']) : '';
$phone = is_string($_POST['phone'] ?? null) ? trim($_POST['phone']) : '';
$email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
$messageValue = $_POST['message'] ?? ($_POST['note'] ?? '');
$message = is_string($messageValue) ? trim($messageValue) : '';
$productIdValue = $_POST['product_id'] ?? null;
$productId = is_string($productIdValue) || is_int($productIdValue)
    ? filter_var($productIdValue, FILTER_VALIDATE_INT)
    : false;
$productCategory = is_string($_POST['product'] ?? null) ? $_POST['product'] : '';
$topic = is_string($_GET['topic'] ?? null) ? $_GET['topic'] : '';
$selectedTopic = $topics[$topic] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $error = 'Phiên liên hệ đã hết hạn. Vui lòng tải lại trang và thử lại.';
    } elseif ($name === '') {
        $error = 'Vui lòng nhập họ tên và số điện thoại để IBP có thể liên hệ.';
    } elseif (text_length($name) > 150) {
        $error = 'Họ tên không được vượt quá 150 ký tự.';
    } elseif (!valid_phone($phone)) {
        $error = 'Vui lòng nhập số điện thoại gồm 7–25 ký tự hợp lệ.';
    } elseif ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || text_length($email) > 190)) {
        $error = 'Vui lòng kiểm tra lại địa chỉ email.';
    } elseif (!db()) {
        $error = 'Chưa kết nối được cơ sở dữ liệu. Vui lòng gọi 0983 537 155 để được tư vấn.';
    } else {
        try {
            $validProductId = null;
            if ($productId && $productId > 0) {
                $productCheck = db()->prepare('SELECT id FROM product WHERE id = ? AND hien_thi = 1');
                $productCheck->execute([$productId]);
                $validProductId = $productCheck->fetchColumn() ?: null;
            }

            $statement = db()->prepare(
                'INSERT INTO customer_consultations '
                . '(product_id, ho_ten, so_dien_thoai, email, noi_dung) VALUES (?, ?, ?, ?, ?)'
            );
            $requestDetails = [];
            if (isset($categories[$productCategory])) {
                $requestDetails[] = 'Danh mục quan tâm: ' . $categories[$productCategory];
            }
            if ($selectedTopic !== '') {
                $requestDetails[] = 'Chủ đề: ' . $selectedTopic;
            }
            if ($message !== '') {
                $requestDetails[] = $message;
            }
            $statement->execute([
                $validProductId,
                $name,
                $phone,
                $email !== '' ? $email : null,
                $requestDetails ? implode("\n\n", $requestDetails) : null,
            ]);
            $_SESSION['contact_sent'] = true;
            $destination = BASE_PATH . '/contact.php?sent=1';
            if ($selectedTopic !== '') {
                $destination .= '&topic=' . rawurlencode($topic);
            }
            header('Location: ' . $destination);
            exit;
        } catch (PDOException $exception) {
            error_log('Contact request failed: ' . $exception->getMessage());
            $error = 'Chưa thể lưu yêu cầu lúc này. Vui lòng gọi 0983 537 155 để được hỗ trợ.';
        }
    }
}

$pageTitle = $sent ? 'Đã nhận yêu cầu | IBP Technology' : 'Liên hệ IBP Technology';
require_once __DIR__ . '/layouts/header.php';
?>
<main class="shop-page">
    <div class="shop-container">
        <nav class="shop-breadcrumb" aria-label="Đường dẫn">
            <a href="<?= BASE_PATH ?>/home/home.php">Trang chủ</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span>Liên hệ</span>
        </nav>
        <div class="section-heading">
            <div>
                <span class="section-kicker">IBP TECHNOLOGY</span>
                <h1><?= $sent ? 'Đã nhận yêu cầu tư vấn' : 'Liên hệ và đăng ký tư vấn' ?></h1>
            </div>
        </div>
        <?php if ($sent): ?>
            <section class="detail-card order-success-card">
                <p>Chuyên viên IBP sẽ liên hệ với bạn trong thời gian sớm nhất.</p>
                <a class="button-primary" href="<?= BASE_PATH ?>/product/catalog.php">Xem sản phẩm</a>
            </section>
        <?php else: ?>
            <section class="detail-card checkout-form-card">
                <p>Gửi thông tin để đội ngũ IBP liên hệ tư vấn. Bạn cũng có thể gọi <a href="tel:0983537155">0983 537 155</a>.</p>
                <?php if ($selectedTopic !== ''): ?>
                    <p>Chủ đề yêu cầu: <strong><?= e($selectedTopic) ?></strong></p>
                <?php endif; ?>
                <?php if ($error !== ''): ?>
                    <div class="form-alert" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>
                <form action="<?= BASE_PATH ?>/contact.php<?= $selectedTopic !== '' ? '?topic=' . rawurlencode($topic) : '' ?>" method="post" class="checkout-form">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="product_id" value="<?= $productId && $productId > 0 ? (int)$productId : 0 ?>">
                    <label>
                        Họ và tên
                        <input name="name" value="<?= e($name) ?>" autocomplete="name" required>
                    </label>
                    <div class="checkout-two-col">
                        <label>
                            Số điện thoại
                            <input type="tel" name="phone" value="<?= e($phone) ?>" autocomplete="tel" required>
                        </label>
                        <label>
                            Email (không bắt buộc)
                            <input type="email" name="email" value="<?= e($email) ?>" autocomplete="email">
                        </label>
                    </div>
                    <label>
                        Danh mục quan tâm
                        <select name="product">
                            <option value="">Chưa chọn</option>
                            <?php foreach ($categories as $key => $label): ?>
                                <option value="<?= e($key) ?>" <?= $productCategory === $key ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        Nội dung
                        <textarea name="message" rows="4"><?= e($message) ?></textarea>
                    </label>
                    <button class="button-primary" type="submit">Gửi yêu cầu</button>
                </form>
            </section>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>
