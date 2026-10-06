<?php
require_once __DIR__ . '/../config/database.php';

function cart_add_error(string $code, string $message, int $status): void
{
    if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($code === 'csrf') {
        http_response_code($status);
        header('Content-Type: text/plain; charset=utf-8');
        exit($message);
    }

    http_response_code(303);
    header('Location: ' . BASE_PATH . '/cart/index.php?error=' . rawurlencode($code));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/product/catalog.php');
    exit;
}
if (!csrf_valid()) {
    cart_add_error('csrf', 'Phiên thao tác đã hết hạn. Vui lòng tải lại trang.', 403);
}

$productIdValue = $_POST['product_id'] ?? null;
$productId = is_string($productIdValue) || is_int($productIdValue)
    ? filter_var($productIdValue, FILTER_VALIDATE_INT)
    : false;
if (!$productId || $productId < 1) {
    cart_add_error('product', 'Sản phẩm không hợp lệ.', 400);
}

$quantityValue = $_POST['quantity'] ?? 1;
$quantity = is_scalar($quantityValue) ? max(1, min(99, (int)$quantityValue)) : 1;
$variantValue = $_POST['variant_id'] ?? 0;
$variantId = is_scalar($variantValue) ? max(0, (int)$variantValue) : 0;
$submittedAddonIds = $_POST['addon_ids'] ?? [];
$requestedAddonIds = [];
if (is_array($submittedAddonIds)) {
    foreach ($submittedAddonIds as $addonId) {
        if (is_string($addonId) || is_int($addonId)) {
            $validatedAddonId = filter_var($addonId, FILTER_VALIDATE_INT);
            if ($validatedAddonId !== false && $validatedAddonId > 0) {
                $requestedAddonIds[] = (int)$validatedAddonId;
            }
        }
    }
}
$requestedAddonIds = array_values(array_unique($requestedAddonIds));
$addonIds = [];
$previousOptions = $_SESSION['cart_options'][$productId] ?? [];
if (!is_array($previousOptions)) {
    $previousOptions = [];
}
$isBuyNow = isset($_POST['buy_now']);
$currentQuantity = $isBuyNow ? 0 : (int)($_SESSION['cart'][$productId] ?? 0);
if (!isset($_POST['variant_id']) && !$isBuyNow) {
    $variantId = (int)($previousOptions['variant_id'] ?? 0);
}
if (!isset($_POST['selection_present']) && !$isBuyNow) {
    $requestedAddonIds = array_values(array_unique(array_filter(array_map(
        'intval',
        $previousOptions['addon_ids'] ?? []
    ))));
}
$database = db();

if (!$database) {
    cart_add_error('database', 'Chưa kết nối được cơ sở dữ liệu. Vui lòng thử lại sau.', 503);
}

try {
    $stmt = $database->prepare('SELECT so_luong_ton FROM product WHERE id = ? AND hien_thi = 1');
    $stmt->execute([$productId]);
    $availableStock = $stmt->fetchColumn();
    if ($availableStock === false) {
        cart_add_error('product', 'Sản phẩm không tồn tại hoặc hiện không được kinh doanh.', 404);
    }

    if ($variantId) {
        $stmt = $database->prepare('SELECT id FROM product_variants WHERE id = ? AND product_id = ?');
        $stmt->execute([$variantId, $productId]);
        if (!$stmt->fetchColumn()) {
            cart_add_error('options', 'Phiên bản sản phẩm không hợp lệ. Vui lòng tải lại trang.', 422);
        }
    }
    if ($requestedAddonIds) {
        $marks = implode(',', array_fill(0, count($requestedAddonIds), '?'));
        $stmt = $database->prepare(
            "SELECT id FROM product_addons WHERE product_id = ? AND hien_thi = 1 AND id IN ($marks)"
        );
        $stmt->execute(array_merge([$productId], $requestedAddonIds));
        $addonIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        if (count($addonIds) !== count($requestedAddonIds)) {
            cart_add_error('options', 'Phụ kiện đã chọn không hợp lệ. Vui lòng tải lại trang.', 422);
        }
    }

    $maximumQuantity = min(99, (int)$availableStock);
    if ($quantity + $currentQuantity > $maximumQuantity) {
        cart_add_error('stock', 'Số lượng yêu cầu vượt quá giới hạn hoặc tồn kho hiện có.', 409);
    }
} catch (PDOException $exception) {
    error_log('Cart add validation failed: ' . $exception->getMessage());
    cart_add_error('database', 'Chưa thể kiểm tra sản phẩm lúc này. Vui lòng thử lại sau.', 503);
}

if ($currentQuantity > 0) {
    $existingAddonIds = array_values(array_map('intval', $previousOptions['addon_ids'] ?? []));
    sort($existingAddonIds);
    $selectedAddonIds = $addonIds;
    sort($selectedAddonIds);
    if (
        (int)($previousOptions['variant_id'] ?? 0) !== $variantId
        || $existingAddonIds !== $selectedAddonIds
    ) {
        cart_add_error(
            'options',
            'Giỏ hàng đã có sản phẩm với lựa chọn khác. Hãy xóa sản phẩm hiện tại trước khi thêm lựa chọn mới.',
            409
        );
    }
}

$configuration = ['variant_id' => $variantId, 'addon_ids' => $addonIds];
if ($isBuyNow) {
    $_SESSION['buy_now'] = [
        'product_id' => $productId,
        'quantity' => $quantity,
        'token' => bin2hex(random_bytes(16)),
        'created_at' => time(),
    ] + $configuration;
    header(
        'Location: ' . BASE_PATH . '/cart/invoice.php?source=buy-now&token='
        . rawurlencode($_SESSION['buy_now']['token'])
    );
    exit;
}

$_SESSION['cart'][$productId] = $currentQuantity + $quantity;
$_SESSION['cart_options'][$productId] = $configuration;

if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'count' => array_sum($_SESSION['cart']),
        'message' => 'Đã thêm vào giỏ hàng.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
header('Location: ' . BASE_PATH . '/cart/index.php?added=1');
exit;
