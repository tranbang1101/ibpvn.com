<?php
require_once __DIR__ . '/database.php';

function cart_products(): array
{
    $cart = $_SESSION['cart'] ?? [];
    $ids = array_values(array_filter(array_map('intval', array_keys($cart))));
    $database = db();

    if ($ids && $database) {
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $database->prepare(
            "SELECT p.id, p.ten, p.gia, p.gia_khuyen_mai, p.anh_chinh, p.so_luong_ton, "
            . "COALESCE(d.phi_giao_hang, 0) AS phi_giao_hang, "
            . "COALESCE(d.giao_hang_mien_phi, 1) AS giao_hang_mien_phi "
            . "FROM product p LEFT JOIN productdetail d ON d.product_id = p.id "
            . "WHERE p.id IN ($marks) AND p.hien_thi = 1"
        );
        $stmt->execute($ids);

        $products = [];
        $foundIds = [];
        foreach ($stmt->fetchAll() as $row) {
            $rowId = (int)$row['id'];
            $products[$rowId] = $row;
            $foundIds[] = $rowId;
        }

        // Dọn dẹp các sản phẩm không còn tồn tại hoặc đã bị ẩn khỏi giỏ hàng
        foreach ($ids as $reqId) {
            if (!in_array($reqId, $foundIds, true)) {
                unset($_SESSION['cart'][$reqId], $_SESSION['cart_options'][$reqId]);
            }
        }

        return $products;
    }

    return [];
}

function cart_addons(int $productId, ?array $optionIds = null): array
{
    $storedOptions = $_SESSION['cart_options'][$productId] ?? [];
    $storedAddonIds = is_array($storedOptions) ? ($storedOptions['addon_ids'] ?? []) : [];
    $ids = $optionIds ?? $storedAddonIds;
    if (!is_array($ids)) {
        return [];
    }
    $ids = array_values(array_unique(array_filter(array_map(
        static fn($id): int => is_scalar($id) ? (int)$id : 0,
        $ids
    ), static fn(int $id): bool => $id > 0)));

    if (!$ids || !db()) {
        return [];
    }

    $marks = implode(',', array_fill(0, count($ids), '?'));

    try {
        $stmt = db()->prepare(
            "SELECT id, ten, image_url, gia_goc, gia_khuyen_mai "
            . "FROM product_addons "
            . "WHERE product_id = ? AND id IN ($marks) AND hien_thi = 1"
        );
        $stmt->execute(array_merge([$productId], $ids));

        return $stmt->fetchAll();
    } catch (PDOException $exception) {
        error_log('Cart add-on lookup failed: ' . $exception->getMessage());
        throw $exception;
    }
}

function cart_variant(int $productId, ?int $variantId = null): ?array
{
    $storedOptions = $_SESSION['cart_options'][$productId] ?? [];
    $storedVariantId = is_array($storedOptions) ? ($storedOptions['variant_id'] ?? 0) : 0;
    $idValue = $variantId ?? $storedVariantId;
    $id = is_scalar($idValue) ? (int)$idValue : 0;

    if (!$id || !db()) {
        return null;
    }

    try {
        $stmt = db()->prepare(
            'SELECT id, ten_phien_ban, image_url, gia '
            . 'FROM product_variants WHERE id = ? AND product_id = ?'
        );
        $stmt->execute([$id, $productId]);

        return $stmt->fetch() ?: null;
    } catch (PDOException $exception) {
        error_log('Cart variant lookup failed: ' . $exception->getMessage());
        throw $exception;
    }
}

function cart_product_total(
    int $productId,
    array $product,
    int $quantity,
    ?array $optionIds = null
): float {
    $variant = cart_variant($productId);
    $variantPrice = (!empty($variant) && !empty($variant['gia'])) ? (float)$variant['gia'] : 0.0;
    $unitPrice = $variantPrice > 0 ? $variantPrice : (float)(!empty($product['gia_khuyen_mai']) ? $product['gia_khuyen_mai'] : $product['gia']);

    foreach (cart_addons($productId, $optionIds) as $addon) {
        $unitPrice += (float)($addon['gia_khuyen_mai'] ?: $addon['gia_goc']);
    }

    return $unitPrice * max(1, $quantity);
}

function cart_total(array $products): float
{
    $sum = 0;

    foreach ($products as $id => $product) {
        $quantity = max(1, (int)($_SESSION['cart'][$id] ?? 1));
        $sum += cart_product_total((int)$id, $product, $quantity);
    }

    return $sum;
}

function cart_shipping(array $products): float
{
    $shipping = 0.0;
    foreach ($products as $product) {
        if (empty($product['giao_hang_mien_phi'])) {
            $shipping = max($shipping, max(0.0, (float)($product['phi_giao_hang'] ?? 0)));
        }
    }

    return $shipping;
}

function money(float $amount): string
{
    return number_format($amount, 0, ',', '.') . '₫';
}
