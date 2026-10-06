<?php
require_once __DIR__ . '/../config/database.php';

$redirect = $_GET['redirect'] ?? ($_POST['redirect'] ?? '');
if (!empty($_SESSION['user'])) {
    $dest = (!empty($redirect) && str_starts_with($redirect, BASE_PATH)) ? $redirect : (BASE_PATH . '/home/home.php');
    header('Location: ' . $dest);
    exit;
}

$error = '';
$username = trim($_POST['username'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$phone = trim($_POST['phone'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $passwordLength = function_exists('mb_strlen')
        ? mb_strlen($password, 'UTF-8')
        : strlen($password);
    $usernameLength = function_exists('mb_strlen')
        ? mb_strlen($username, 'UTF-8')
        : strlen($username);

    if ($username === '' || $usernameLength > 150) {
        $error = 'Tên người dùng không được để trống và tối đa 150 ký tự.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Vui lòng nhập địa chỉ email hợp lệ.';
    } elseif ($passwordLength < 8) {
        $error = 'Mật khẩu cần có ít nhất 8 ký tự.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Mật khẩu xác nhận chưa khớp.';
    } elseif (!db()) {
        $error = 'Chưa kết nối được cơ sở dữ liệu. Hãy kiểm tra database/ibpvn.sql trong phpMyAdmin.';
    } else {
        try {
            $database = db();
            $duplicateCheck = $database->prepare(
                'SELECT id FROM nguoidung WHERE ho_ten = ? OR email = ? LIMIT 1'
            );
            $duplicateCheck->execute([$username, $email]);

            if ($duplicateCheck->fetch()) {
                $error = 'Tên người dùng hoặc email này đã được đăng ký.';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $insertUser = $database->prepare(
                    'INSERT INTO nguoidung (ho_ten, email, so_dien_thoai, mat_khau) VALUES (?, ?, ?, ?)'
                );
                $insertUser->execute([
                    $username,
                    $email,
                    $phone !== '' ? $phone : null,
                    $passwordHash,
                ]);

                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => (int)$database->lastInsertId(),
                    'name' => $username,
                    'username' => $username,
                    'email' => $email,
                ];

                $dest = (!empty($redirect) && str_starts_with($redirect, BASE_PATH))
                    ? $redirect
                    : (BASE_PATH . '/home/home.php');

                header('Location: ' . $dest);
                exit;
            }
        } catch (PDOException $exception) {
            $error = $exception->getCode() === '23000'
                ? 'Tên người dùng hoặc email này đã được đăng ký.'
                : 'Không thể tạo tài khoản lúc này. Vui lòng thử lại.';
        }
    }
}

$pageTitle = 'Tạo tài khoản | IBP Technology';
require_once __DIR__ . '/../layouts/header.php';
?>
<main class="auth-page">
    <div class="auth-wrap">
        <aside class="auth-aside">
            <img src="<?= BASE_PATH ?>/assets/images/logo_IBP.png" alt="IBP Technology">
            <span class="auth-eyebrow">IBP MEMBER</span>
            <h1>Chăm sóc nguồn nước, chăm sóc tổ ấm.</h1>
            <p>Tạo tài khoản để nhận tư vấn chuyên sâu, theo dõi đơn hàng và mở khóa ưu đãi dành riêng cho bạn.</p>
            <div class="auth-aside-points">
                <span><i class="bi bi-patch-check"></i> Sản phẩm chính hãng</span>
                <span><i class="bi bi-truck"></i> Giao hàng tận tâm</span>
                <span><i class="bi bi-headset"></i> Hỗ trợ trọn vòng đời</span>
            </div>
            <div class="auth-aside-orb"></div>
        </aside>

        <section class="auth-card">
            <a class="auth-back" href="<?= BASE_PATH ?>/home/home.php">
                <i class="bi bi-arrow-left"></i> Về trang chủ
            </a>
            <span class="auth-eyebrow">BẮT ĐẦU CÙNG IBP</span>
            <h2>Tạo tài khoản</h2>
            <p class="auth-subtitle">Đăng ký nhanh để trải nghiệm mua sắm tốt hơn.</p>

            <?php if ($error !== ''): ?>
                <div class="form-alert" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="auth-form" autocomplete="off">
                <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
                <label>
                    Tên người dùng
                    <input
                        type="text"
                        name="username"
                        value="<?= e($username) ?>"
                        autocomplete="username"
                        maxlength="150"
                        placeholder="Ví dụ: minh anh"
                        required
                    >
                </label>
                <label>
                    Email
                    <input
                        type="email"
                        name="email"
                        value="<?= e($email) ?>"
                        autocomplete="email"
                        placeholder="ban@email.com"
                        required
                    >
                </label>
                <label>
                    Số điện thoại <span>(không bắt buộc)</span>
                    <input
                        type="tel"
                        name="phone"
                        value="<?= e($phone) ?>"
                        autocomplete="tel"
                        placeholder="090 123 4567"
                    >
                </label>
                <label>
                    Mật khẩu
                    <input
                        type="password"
                        name="password"
                        autocomplete="off"
                        minlength="8"
                        placeholder="Ít nhất 8 ký tự"
                        required
                    >
                </label>
                <label>
                    Nhập lại mật khẩu
                    <input
                        type="password"
                        name="confirm_password"
                        autocomplete="off"
                        minlength="8"
                        placeholder="Nhập lại mật khẩu"
                        required
                    >
                </label>
                <label class="auth-terms">
                    <input type="checkbox" required>
                    <span>Tôi đồng ý với <a href="<?= BASE_PATH ?>/contact.php">Điều khoản sử dụng</a> và chính sách bảo mật.</span>
                </label>
                <button class="button-primary auth-submit" type="submit">
                    Tạo tài khoản <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <p class="auth-switch">
                Bạn đã có tài khoản?
                <a href="<?= BASE_PATH ?>/auth/login.php<?= !empty($redirect) ? ('?redirect=' . urlencode($redirect)) : '' ?>">Đăng nhập</a>
            </p>
            <div class="auth-secure">
                <i class="bi bi-shield-lock"></i>
                Thông tin của bạn được bảo mật an toàn
            </div>
        </section>
    </div>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
