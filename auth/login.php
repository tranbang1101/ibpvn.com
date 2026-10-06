<?php
require_once __DIR__ . '/../config/database.php';

$redirectValue = $_GET['redirect'] ?? ($_POST['redirect'] ?? '');
$redirect = is_string($redirectValue) ? $redirectValue : '';
if (!empty($_SESSION['user'])) {
    $dest = is_safe_local_redirect($redirect) ? $redirect : (BASE_PATH . '/home/home.php');
    header('Location: ' . $dest);
    exit;
}

$error = '';
$notice = '';
if (($_GET['msg'] ?? '') === 'checkout_required') {
    $notice = 'Quý khách vui lòng đăng nhập hoặc đăng ký tài khoản để tiếp tục thanh toán đơn hàng.';
}

$usernameValue = $_POST['username'] ?? '';
$username = is_string($usernameValue) ? trim($usernameValue) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passwordValue = $_POST['password'] ?? '';
    $password = is_string($passwordValue) ? $passwordValue : '';

    if (!csrf_valid()) {
        $error = 'Phiên đăng nhập đã hết hạn. Vui lòng tải lại trang và thử lại.';
    } elseif ($username === '' || text_length($username) > 190 || $password === '') {
        $error = 'Vui lòng nhập tên đăng nhập/email và mật khẩu hợp lệ.';
    } elseif (!db()) {
        $error = 'Chưa kết nối được cơ sở dữ liệu. Hãy kiểm tra database/ibpvn.sql trong phpMyAdmin.';
    } else {
        try {
            $findUsers = db()->prepare(
                'SELECT id, ho_ten, email, mat_khau FROM nguoidung WHERE (ho_ten = ? OR email = ?) AND trang_thai = 1 ORDER BY id'
            );
            $findUsers->execute([$username, strtolower($username)]);
            $users = $findUsers->fetchAll();
            $matchedUser = null;

            foreach ($users as $user) {
                if (password_verify($password, $user['mat_khau'])) {
                    $matchedUser = $user;
                    break;
                }
            }

            if ($matchedUser) {
                session_regenerate_id(true);
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['user'] = [
                    'id' => (int)$matchedUser['id'],
                    'name' => $matchedUser['ho_ten'],
                    'username' => $matchedUser['ho_ten'],
                    'email' => $matchedUser['email'],
                ];

                $dest = is_safe_local_redirect($redirect)
                    ? $redirect
                    : (BASE_PATH . '/home/home.php');

                header('Location: ' . $dest);
                exit;
            }

            $error = 'Tên người dùng hoặc mật khẩu chưa chính xác.';
        } catch (PDOException $exception) {
            error_log('Login failed: ' . $exception->getMessage());
            $error = 'Không thể đăng nhập lúc này. Vui lòng thử lại sau.';
        }
    }
}

$pageTitle = 'Đăng nhập | IBP Technology';
require_once __DIR__ . '/../layouts/header.php';
?>
<main class="auth-page">
    <div class="auth-wrap">
        <aside class="auth-aside">
            <img src="<?= BASE_PATH ?>/assets/images/logo_IBP.png" alt="IBP Technology">
            <span class="auth-eyebrow">WELCOME BACK</span>
            <h1>Giải pháp nước tốt hơn bắt đầu từ đây.</h1>
            <p>Đăng nhập để theo dõi đơn hàng và gửi yêu cầu hỗ trợ thuận tiện hơn.</p>
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
            <span class="auth-eyebrow">IBP MEMBER</span>
            <h2>Chào mừng trở lại</h2>
            <p class="auth-subtitle">Đăng nhập vào tài khoản IBP của bạn.</p>

            <?php if ($notice !== ''): ?>
                <div class="form-alert" role="alert" style="background:#e8f4fd;color:#0b5ed7;border-left:4px solid #0d6efd;margin-bottom:16px;">
                    <i class="bi bi-info-circle"></i>
                    <?= e($notice) ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="form-alert" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="auth-form" autocomplete="on">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
                <label>
                    Tên người dùng hoặc Email
                    <input
                        type="text"
                        name="username"
                        value="<?= e($username) ?>"
                        autocomplete="username"
                        placeholder="Tên người dùng hoặc email"
                        required
                    >
                </label>
                <label>
                    Mật khẩu
                    <input
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        placeholder="Nhập mật khẩu"
                        required
                    >
                </label>
                <div class="auth-options">
                    <a href="mailto:info@ibpvn.com?subject=Password%20recovery">Liên hệ để khôi phục mật khẩu</a>
                </div>
                <button class="button-primary auth-submit" type="submit">
                    Đăng nhập <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <p class="auth-switch">
                Chưa có tài khoản?
                <a href="<?= BASE_PATH ?>/auth/register.php<?= !empty($redirect) ? ('?redirect=' . urlencode($redirect)) : '' ?>">Đăng ký ngay</a>
            </p>
            <div class="auth-secure">
                <i class="bi bi-shield-lock"></i>
                Thông tin của bạn được bảo mật an toàn
            </div>
        </section>
    </div>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
