<?php
require_once __DIR__ . '/database.php';

function cart_products(): array
{
    $cart = $_SESSION['cart'] ?? [];
    $ids = array_values(array_filter(array_map('intval', array_keys($cart))));

    if ($ids && db()) {
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $stmt = db()->prepare(
            "SELECT id, ten, gia, gia_khuyen_mai, anh_chinh, so_luong_ton "
            . "FROM product WHERE id IN ($marks) AND hien_thi = 1"
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

    $products = [];
    foreach ($ids as $id) {
        if ($id === 1) {
            $products[$id] = [
                'id' => 1,
                'ten' => 'Máy lọc nước A. O. Smith ROSS™ ECO-AOC75PUR',
                'gia' => 14500000,
                'gia_khuyen_mai' => 12150000,
                'anh_chinh' => BASE_PATH . '/assets/images/sanpham.png',
                'so_luong_ton' => 12,
            ];
        } else {
            $products[$id] = [
                'id' => $id,
                'ten' => 'Máy lọc nước A. O. Smith A2',
                'gia' => 12600000,
                'gia_khuyen_mai' => 9200000,
                'anh_chinh' => BASE_PATH . '/assets/images/maylocnuoc-a.o.smith.png',
                'so_luong_ton' => 12,
            ];
        }
    }

    return $products;
}

function cart_addons(int $productId, ?array $optionIds = null): array
{
    $ids = $optionIds ?? ($_SESSION['cart_options'][$productId]['addon_ids'] ?? []);
    $ids = array_values(array_filter(array_map('intval', $ids)));

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
        return [];
    }
}

function cart_variant(int $productId, ?int $variantId = null): ?array
{
    $id = $variantId ?? (int)($_SESSION['cart_options'][$productId]['variant_id'] ?? 0);

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
        return null;
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

function money(float $amount): string
{
    return number_format($amount, 0, ',', '.') . '₫';
}
