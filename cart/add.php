<?php
require_once __DIR__ . '/../config/database.php';

$productId = max(1, (int)($_POST['product_id'] ?? 1));
$quantity = max(1, min(99, (int)($_POST['quantity'] ?? 1)));
$variantId = max(0, (int)($_POST['variant_id'] ?? 0));
$requestedAddonIds = array_values(array_unique(array_filter(array_map('intval', (array)($_POST['addon_ids'] ?? [])))));
$addonIds = [];
$previousOptions = $_SESSION['cart_options'][$productId] ?? [];

if (db()) {
    try {
        if ($variantId) {
            $stmt = db()->prepare('SELECT id FROM product_variants WHERE id = ? AND product_id = ?');
            $stmt->execute([$variantId, $productId]);
            if (!$stmt->fetchColumn()) $variantId = 0;
        }
        if ($requestedAddonIds) {
            $marks = implode(',', array_fill(0, count($requestedAddonIds), '?'));
            $stmt = db()->prepare("SELECT id FROM product_addons WHERE product_id = ? AND hien_thi = 1 AND id IN ($marks)");
            $stmt->execute(array_merge([$productId], $requestedAddonIds));
            $addonIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        }
    } catch (PDOException $exception) {
        $addonIds = [];
        $variantId = 0;
    }
}

if (!isset($_POST['variant_id']) && !isset($_POST['buy_now'])) {
    $variantId = (int)($previousOptions['variant_id'] ?? 0);
}
if (!isset($_POST['selection_present']) && !isset($_POST['buy_now'])) {
    $addonIds = array_values(array_filter(array_map('intval', $previousOptions['addon_ids'] ?? [])));
}

$configuration = ['variant_id' => $variantId, 'addon_ids' => $addonIds];
if (isset($_POST['buy_now'])) {
    $_SESSION['buy_now'] = ['product_id' => $productId, 'quantity' => $quantity] + $configuration;
    header('Location: ' . BASE_PATH . '/cart/invoice.php');
    exit;
}

$_SESSION['cart'][$productId] = min(99, (int)($_SESSION['cart'][$productId] ?? 0) + $quantity);
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
