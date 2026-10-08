<?php
require_once __DIR__ . '/../config/store.php';

$pdo = db();
$requestedProductId = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
$productId = $requestedProductId === null ? 0 : (int)$requestedProductId;
$product = null;
$images = [];
$variants = [];
$addons = [];
$reviews = [];
$faqs = [];
$loadError = '';

if (!$pdo) {
    $loadError = 'Chưa kết nối được cơ sở dữ liệu. Vui lòng thử lại sau.';
} elseif ($requestedProductId !== null && $productId < 1) {
    http_response_code(404);
} else {
    try {
        if (!$productId) {
            $productId = (int)$pdo->query("SELECT id FROM product WHERE slug = 'may-loc-nuoc-ao-smith-a2' LIMIT 1")->fetchColumn();
        }
        if ($productId > 0) {
            $stmt = $pdo->prepare(
                'SELECT p.*, d.mo_ta_chi_tiet, d.thong_so_ky_thuat, d.bao_hanh, d.phi_giao_hang, ' .
                'd.giao_hang_mien_phi, d.thong_tin_uu_dai ' .
                'FROM product p LEFT JOIN productdetail d ON d.product_id = p.id ' .
                'WHERE p.id = ? AND p.hien_thi = 1'
            );
            $stmt->execute([$productId]);
            $product = $stmt->fetch() ?: null;
        }
        if ($product) {
            $pdo->prepare('UPDATE product SET luot_xem = luot_xem + 1 WHERE id = ?')->execute([$productId]);
            $stmt = $pdo->prepare('SELECT image_url, alt_text FROM product_images WHERE product_id = ? ORDER BY thu_tu, id');
            $stmt->execute([$productId]);
            $images = $stmt->fetchAll();
            $stmt = $pdo->prepare('SELECT id, ten_phien_ban, image_url, sku, gia FROM product_variants WHERE product_id = ? ORDER BY id');
            $stmt->execute([$productId]);
            $variants = $stmt->fetchAll();
            $stmt = $pdo->prepare('SELECT id, ten, image_url, gia_goc, gia_khuyen_mai FROM product_addons WHERE product_id = ? AND hien_thi = 1 ORDER BY thu_tu, id');
            $stmt->execute([$productId]);
            $addons = $stmt->fetchAll();
            $stmt = $pdo->prepare('SELECT ten_hien_thi, so_sao, noi_dung, created_at FROM danh_gia WHERE product_id = ? AND trang_thai = 1 ORDER BY created_at DESC, id DESC LIMIT 30');
            $stmt->execute([$productId]);
            $reviews = $stmt->fetchAll();
            $stmt = $pdo->prepare('SELECT cau_hoi, cau_tra_loi FROM product_faq WHERE product_id = ? AND hien_thi = 1 ORDER BY thu_tu, id');
            $stmt->execute([$productId]);
            $faqs = $stmt->fetchAll();
        }
    } catch (PDOException $exception) {
        error_log('Product detail lookup failed: ' . $exception->getMessage());
        $loadError = 'Chưa tải được thông tin sản phẩm. Vui lòng thử lại sau.';
    }
}

$asset = static fn(string $name): string => BASE_PATH . '/assets/images/' . $name;
$pageTitle = 'Sản phẩm | IBP Technology';
if ($loadError !== '' || !$product) {
    http_response_code($loadError !== '' ? 503 : 404);
    require_once __DIR__ . '/../layouts/header.php';
    ?>
    <main class="shop-page">
        <div class="shop-container">
            <section class="detail-card catalog-empty" role="status">
                <h1><?= $loadError !== '' ? 'Chưa thể tải sản phẩm' : 'Không tìm thấy sản phẩm' ?></h1>
                <p><?= e($loadError !== '' ? $loadError : 'Sản phẩm không tồn tại hoặc hiện không được kinh doanh.') ?></p>
                <a class="button-primary" href="<?= BASE_PATH ?>/product/catalog.php">Quay lại danh sách sản phẩm</a>
            </section>
        </div>
    </main>
    <?php
    require_once __DIR__ . '/../layouts/footer.php';
    exit;
}

$image = asset_url($product['anh_chinh'] ?? null) ?: $asset('sanpham.png');
$a2GalleryImages = [
    $asset('maylocnuoc-a.o.smith.png'),
    $asset('a.o.smith-mini1.png'),
    $asset('a.o.smith-mini2.png'),
    $asset('a.o.smith-mini3.png'),
];
if (!$images) {
    $images = [['image_url' => $image, 'alt_text' => $product['ten']]];
}
if (count($images) < 4) {
    foreach (array_slice($a2GalleryImages, 1) as $fallbackImage) {
        $images[] = [
            'image_url' => $fallbackImage,
            'alt_text' => 'Ảnh sản phẩm ' . (count($images) + 1),
        ];
        if (count($images) === 4) {
            break;
        }
    }
}
$rawSpecs = json_decode((string)($product['thong_so_ky_thuat'] ?? ''), true) ?: [];
$rawSpecs = is_array($rawSpecs) ? $rawSpecs : [];
$isA2Product = ($product['slug'] ?? '') === 'may-loc-nuoc-ao-smith-a2';
$productOrigin = $rawSpecs['Xuất xứ'] ?? ($isA2Product ? 'Mỹ' : '—');
$specs = $rawSpecs;
$a2DefaultSpecs = [
    'Số cấp lọc' => '7 cấp lọc',
    'Chức năng' => 'Nước thường',
    'Điện áp đầu vào' => 'AC 220V/ 50HZ',
    'Công suất (tổng)' => '85 W',
    'Áp suất nước cấp phù hợp' => '0.1MPa ~ 0.35MPa',
    'Nhiệt độ nước cấp' => '5~38°C',
    'Công suất lọc/phút' => '1.1 L/phút',
    'Phương pháp sục rửa' => 'Tự động làm sạch',
];
$defaultSpecs = $isA2Product
    ? $a2DefaultSpecs
    : [
        'Thương hiệu' => $product['thuong_hieu'] ?: 'Đang cập nhật',
        'Danh mục' => $product['danh_muc'] ?: 'Đang cập nhật',
        'Bảo hành' => $product['bao_hanh'] ?: 'Đang cập nhật',
        'Điện áp' => 'AC 220V / 50Hz',
        'Công suất' => 'Theo cấu hình sản phẩm',
        'Kích thước' => 'Vui lòng liên hệ tư vấn',
        'Trọng lượng' => 'Vui lòng liên hệ tư vấn',
        'Xuất xứ' => $productOrigin,
    ];
$modalSpecs = $isA2Product
    ? array_slice(array_replace($a2DefaultSpecs, $specs), 0, 8, true)
    : array_slice($specs + $defaultSpecs, 0, 8, true);
$visibleSpecs = $isA2Product
    ? [
        'Xuất xứ' => $productOrigin,
        'Số cấp lọc' => $specs['Số cấp lọc'] ?? $a2DefaultSpecs['Số cấp lọc'],
        'Chức năng' => $specs['Chức năng'] ?? $a2DefaultSpecs['Chức năng'],
        'Bảo hành' => $product['bao_hanh'] ?: '24 tháng',
    ]
    : array_slice($specs + $defaultSpecs, 0, 4, true);
$currentPrice = (float)($product['gia_khuyen_mai'] ?: $product['gia']);
$originalPrice = (float)$product['gia'];
$discountPercent = $originalPrice > $currentPrice ? (int)round(($originalPrice - $currentPrice) / $originalPrice * 100) : 0;
$rating = (float)($product['rating'] ?? 0);
$reviewCount = (int)($product['so_danh_gia'] ?? 0);
$baseImage = asset_url($images[0]['image_url'] ?? null) ?: $image;
$pageTitle = $product['ten'] . ' | IBP Technology';
$defaultVariantId = (int)($variants[0]['id'] ?? 0);
$promotionLines = [
    'LẮP Thêm LÕI LỌC NƯỚC ION KIỀM ALKALINE HYDROGEN - NHẬP KHẨU HÀN QUỐC chỉ 500.000Đ (Giá thị trường 950.000đ)',
    'Tặng thiết bị kiểm tra độ tinh khiết của nước TDS trị giá 150.000đ',
    'Tặng gói lắp đặt và phụ kiện trị giá 500.000đ',
    'Tặng 2.000.000đ đồng khi mua Lọc đầu nguồn và Heatpump A. O. Smith',
];
$storedRecentIds = $_SESSION['recently_viewed_products'] ?? [];
$recentProductIds = is_array($storedRecentIds)
    ? array_values(array_unique(array_filter(
        array_map('intval', $storedRecentIds),
        static fn(int $id): bool => $id > 0 && $id !== $productId
    )))
    : [];
$_SESSION['recently_viewed_products'] = array_slice(
    array_merge([$productId], $recentProductIds),
    0,
    4
);
$recentProducts = [];
$recentIds = $_SESSION['recently_viewed_products'];
if ($recentIds) {
    $placeholders = implode(',', array_fill(0, count($recentIds), '?'));
    try {
        $stmt = $pdo->prepare(
            "SELECT id, ten, gia, gia_khuyen_mai, anh_chinh FROM product "
            . "WHERE hien_thi = 1 AND id IN ($placeholders)"
        );
        $stmt->execute($recentIds);
        $productsById = [];
        foreach ($stmt->fetchAll() as $recentProduct) {
            $productsById[(int)$recentProduct['id']] = $recentProduct;
        }
        foreach ($recentIds as $recentId) {
            if (isset($productsById[$recentId])) {
                $recentProducts[] = $productsById[$recentId];
            }
        }
    } catch (PDOException $exception) {
        error_log('Recently viewed product lookup failed: ' . $exception->getMessage());
    }
}
$similarProducts = [];
try {
    $category = trim((string)($product['danh_muc'] ?? ''));
    $similarSql = 'SELECT id, ten, gia, gia_khuyen_mai, anh_chinh, mo_ta_ngan, '
        . 'rating, so_danh_gia, da_ban, luot_xem, luot_yeu_thich '
        . 'FROM product WHERE hien_thi = 1 AND id <> ?';
    $parameters = [$productId];
    if ($category !== '') {
        $similarSql .= ' AND danh_muc = ?';
        $parameters[] = $category;
    }
    $similarSql .= ' ORDER BY da_ban DESC, id DESC LIMIT 8';
    $stmt = $pdo->prepare($similarSql);
    $stmt->execute($parameters);
    $similarProducts = $stmt->fetchAll();
} catch (PDOException $exception) {
    error_log('Related product lookup failed: ' . $exception->getMessage());
    $similarProducts = [];
}
$pageTitle = $product['ten'] . ' | IBP Technology';
require_once __DIR__ . '/../layouts/header.php';
?>
<main class="shop-page product-page product-detail-redesign">
    <div class="shop-container product-detail-container">
        <nav class="shop-breadcrumb">
            <a href="<?= BASE_PATH ?>/home/home.php">Trang chủ</a>
            <i class="bi bi-chevron-right"></i>
            <a href="<?= BASE_PATH ?>/product/catalog.php"><?= e($product['danh_muc'] ?: 'Sản phẩm') ?></a>
            <i class="bi bi-chevron-right"></i>
            <span><?= e($product['ten']) ?></span>
        </nav>

        <form id="productDetailForm" method="post" action="<?= BASE_PATH ?>/cart/add.php" class="product-buy-form product-detail-form" data-product-detail-form>
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <input type="hidden" name="variant_id" value="<?= $defaultVariantId ?>" data-selected-variant>
            <input type="hidden" name="selection_present" value="1">
            <div data-selected-addons></div>
            <section class="pd-top-grid">
                <div class="pd-gallery">
                    <div class="pd-main-visual">
                        <button type="button" class="pd-gallery-arrow is-prev" data-gallery-prev aria-label="Ảnh trước"><i class="bi bi-chevron-left"></i></button>
                        <img id="productMainImage" src="<?= e($baseImage) ?>" alt="<?= e($product['ten']) ?>" data-main-image>
                        <button type="button" class="pd-gallery-arrow is-next" data-gallery-next aria-label="Ảnh tiếp theo"><i class="bi bi-chevron-right"></i></button>
                    </div>
                    <div class="pd-gallery-bottom">
                        <div class="pd-thumbnails" data-gallery-thumbnails>
                            <?php foreach ($images as $index => $galleryImage): ?>
                                <button
                                    type="button"
                                    class="pd-thumb <?= $index === 0 ? 'is-active' : '' ?>"
                                    data-gallery-image="<?= e(asset_url($galleryImage['image_url'] ?? null)) ?>"
                                    aria-label="Xem ảnh <?= $index + 1 ?>"
                                >
                                    <img
                                        src="<?= e(asset_url($galleryImage['image_url'] ?? null)) ?>"
                                        alt="<?= e($galleryImage['alt_text'] ?? ('Ảnh sản phẩm ' . ($index + 1))) ?>"
                                    >
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <a class="pd-spec-shortcut" href="#product-information" data-specs-shortcut>Xem thông số<br>kỹ thuật <i class="bi bi-arrow-down"></i></a>
                    </div>
                    <div class="pd-social">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode(BASE_URL . 'product/detail.php?id=' . $productId) ?>" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ qua Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://zalo.me/0983537155" target="_blank" rel="noopener noreferrer" aria-label="Tư vấn qua Zalo" class="zalo-mark">Zalo</a>
                        <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <span class="pd-social-qr" aria-hidden="true" title="Mã QR sản phẩm"><i class="bi bi-qr-code"></i></span>
                    </div>
                </div>

                <div class="pd-summary">
                    <div class="pd-title-row">
                        <div>
                            <h1><?= e($product['ten']) ?></h1>
                        </div>
                        <div class="pd-sold">
                            Đã bán: <b><?= number_format((int)$product['da_ban']) ?></b>
                            <span class="pd-top-stars">★</span>
                            (<?= number_format($rating, 1) ?>)
                        </div>
                    </div>
                    <div class="pd-brand-rating">
                        <span>Thương hiệu: <a href="#product-information"><?= e($product['thuong_hieu'] ?: '—') ?></a></span>
                        <i></i>
                        <span class="pd-stars"><?= str_repeat('★', (int)round($rating)) ?><?= str_repeat('☆', 5 - (int)round($rating)) ?></span>
                        <a href="#reviews"><?= $reviewCount ?> đánh giá</a>
                    </div>
                    <div class="pd-price-line">
                        <span>Giá:</span>
                        <strong data-unit-price="<?= (int)$currentPrice ?>"><?= money($currentPrice) ?></strong>
                        <?php if ($discountPercent): ?>
                            <del><?= money($originalPrice) ?></del>
                            <em>-<?= $discountPercent ?>%</em>
                            <span class="pd-price-savings" data-price-savings>
                                Tiết kiệm: <?= money($originalPrice - $currentPrice) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="pd-service-line">
                        <span>Bảo hành: <b><?= e($product['bao_hanh'] ?: '—') ?></b></span>
                        <span>Thương hiệu/Nơi sản xuất: <b><?= e($productOrigin) ?></b></span>
                        <span>Giao hàng: <b><?= !empty($product['giao_hang_mien_phi']) ? 'Miễn phí' : money((float)$product['phi_giao_hang']) ?></b></span>
                    </div>
                    <div class="pd-product-info">
                        <h2>THÔNG TIN SẢN PHẨM:</h2>
                        <p><?= e($product['mo_ta_chi_tiet'] ?: $product['mo_ta_ngan']) ?></p>
                    </div>
                    <?php if ($variants): ?>
                        <div class="pd-variants"><b>Phiên bản</b><div class="pd-variant-list">
                            <?php foreach ($variants as $index => $variant): ?>
                                <?php
                                $variantImage = asset_url($variant['image_url'] ?? null)
                                    ?: $a2GalleryImages[$index % count($a2GalleryImages)];
                                ?>
                                <button
                                    type="button"
                                    class="pd-variant <?= $index === 0 ? 'is-selected' : '' ?>"
                                    data-variant-id="<?= (int)$variant['id'] ?>"
                                    data-variant-image="<?= e($variantImage) ?>"
                                    data-variant-price="<?= (int)($variant['gia'] ?: $currentPrice) ?>"
                                >
                                    <span class="pd-variant-check"><i class="bi bi-check-lg"></i></span>
                                    <img src="<?= e($variantImage) ?>" alt="">
                                    <span><?= e($variant['ten_phien_ban']) ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div></div>
                    <?php endif; ?>
                    <?php if ($promotionLines): ?>
                        <div class="pd-promotion">
                            <h2><i class="bi bi-gift-fill"></i> ƯU ĐÃI KHI MUA HÀNG:</h2>
                            <ul>
                                <?php foreach ($promotionLines as $promotionLine): ?>
                                    <?php if (trim($promotionLine) !== ''): ?>
                                        <li><?= e(trim($promotionLine)) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="pd-buy-row">
                        <div class="quantity-control pd-quantity">
                            <button type="button" data-quantity="minus" aria-label="Giảm số lượng">−</button>
                            <input name="quantity" value="1" min="1" max="99" type="number" aria-label="Số lượng" data-product-quantity>
                            <button type="button" data-quantity="plus" aria-label="Tăng số lượng">+</button>
                        </div>
                        <button name="add_to_cart" value="1" class="pd-add-button">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                        <button name="buy_now" value="1" class="pd-buy-now">MUA NGAY</button>
                    </div>
                    <div class="pd-consult-links">
                        <a href="https://zalo.me/0983537155"><i class="bi bi-chat-dots-fill"></i> Tư vấn qua Zalo</a>
                        <a href="https://m.me/ibptechnology"><i class="bi bi-messenger"></i> Tư vấn qua Messenger</a>
                        <a href="tel:0983537155"><i class="bi bi-headset"></i> Tư vấn qua Hotline</a>
                    </div>
                    <p class="pd-hours">
                        Gọi đặt mua: <a href="tel:0983537155">0983.537.155</a> (8:00 - 18:00)
                    </p>
                </div>
            </section>
        </form>

            <section class="pd-mid-grid">
                <div class="pd-main-column">
                    <?php if ($addons): ?>
                    <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="pd-bundle-form product-buy-form" data-product-detail-form>
                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                        <input type="hidden" name="variant_id" value="<?= $defaultVariantId ?>" data-selected-variant>
                        <input type="hidden" name="selection_present" value="1">
                        <input type="hidden" name="quantity" value="1" data-bundle-quantity>
                        <div data-selected-addons></div>
                    <section class="detail-card pd-bundle-card" aria-labelledby="bundle-heading">
                        <div class="pd-section-title"><h2 id="bundle-heading">MUA KÈM GIÁ GIẢM</h2><span>Chọn phụ kiện bạn cần</span></div>
                        <div class="pd-addon-carousel">
                            <button class="pd-addon-arrow is-prev" type="button" data-addon-prev aria-label="Phụ kiện trước">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <div class="pd-addon-track" data-addon-track>
                            <?php foreach ($addons as $addon): ?>
                                <?php $addonPrice = (float)($addon['gia_khuyen_mai'] ?: $addon['gia_goc']); ?>
                                <article
                                    class="pd-addon"
                                    data-addon-card
                                    data-addon-id="<?= (int)$addon['id'] ?>"
                                    data-addon-price="<?= (int)$addonPrice ?>"
                                    data-addon-old-price="<?= (int)$addon['gia_goc'] ?>"
                                >
                                    <img src="<?= e(asset_url($addon['image_url'] ?? null) ?: $asset('loiloc.png')) ?>" alt="<?= e($addon['ten']) ?>">
                                    <b><?= e($addon['ten']) ?></b>
                                    <button type="button" class="pd-addon-toggle" aria-pressed="false">CHỌN</button>
                                    <strong><?= money($addonPrice) ?></strong>
                                </article>
                            <?php endforeach; ?>
                            </div>
                            <button class="pd-addon-arrow is-next" type="button" data-addon-next aria-label="Phụ kiện tiếp theo">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div class="pd-bundle-summary">
                            <div class="pd-addon-savings">
                                <span>Mua kèm chỉ còn: <b data-addon-total>0₫</b></span>
                                <small>Tạm tính riêng: <b data-bundle-total><?= money($currentPrice) ?></b></small>
                                <small class="pd-saving" data-savings>
                                    Tiết kiệm <?= money($originalPrice - $currentPrice) ?> · -<?= $discountPercent ?>%
                                </small>
                            </div>
                            <div class="pd-bundle-actions">
                                <button type="submit" name="buy_now" value="1" class="pd-bundle-buy">
                                    MUA NGAY <small>Giao hàng tận nơi</small>
                                </button>
                                <button type="submit" name="add_to_cart" value="1" class="pd-bundle-cart">
                                    <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </section>
                    </form>
                    <?php endif; ?>

                    <section class="detail-card pd-information-card" id="product-information">
                        <div class="pd-info-tabs" role="tablist">
                            <button type="button" role="tab" aria-selected="false" data-info-tab="product">
                                THÔNG TIN SẢN PHẨM
                            </button>
                            <button type="button" class="is-active" role="tab" aria-selected="true" data-info-tab="specs">
                                THÔNG SỐ KỸ THUẬT
                            </button>
                        </div>
                        <div class="pd-info-panel" data-info-panel="product" hidden>
                            <h2><?= e($product['ten']) ?></h2>
                            <p><?= e($product['mo_ta_chi_tiet'] ?: $product['mo_ta_ngan']) ?></p>
                            <?php if (!empty($product['thong_tin_uu_dai'])): ?>
                                <p><?= e($product['thong_tin_uu_dai']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="pd-info-panel is-visible" data-info-panel="specs" id="technical-specs">
                            <div class="pd-spec-table">
                                <?php foreach ($visibleSpecs as $label => $value): ?>
                                    <div>
                                        <span><?= e((string)$label) ?></span>
                                        <b><?= e((string)$value) ?></b>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="pd-more-specs" data-open-specs>
                                Xem chi tiết thông số kỹ thuật
                            </button>
                        </div>
                    </section>

                    <dialog class="pd-spec-dialog" data-spec-dialog aria-labelledby="pd-spec-dialog-title">
                        <div class="pd-spec-dialog-header">
                            <h2 id="pd-spec-dialog-title">THÔNG SỐ KỸ THUẬT</h2>
                            <button type="button" data-close-specs aria-label="Đóng thông số kỹ thuật">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="pd-spec-table">
                            <?php if ($modalSpecs): ?>
                                <?php foreach ($modalSpecs as $label => $value): ?>
                                    <div>
                                        <span><?= e((string)$label) ?></span>
                                        <b><?= e((string)$value) ?></b>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Thông số kỹ thuật của sản phẩm chưa được cập nhật.</p>
                            <?php endif; ?>
                        </div>
                    </dialog>

                    <section class="pd-reviews" id="reviews">
                        <div class="pd-review-heading"><h2>Reviews - <?= e($product['ten']) ?></h2></div>
                        <div class="pd-review-summary">
                            <div class="pd-review-score">
                                <strong><?= number_format($rating, 1) ?></strong>
                                <span><?= str_repeat('★', (int)round($rating)) ?><?= str_repeat('☆', 5 - (int)round($rating)) ?></span>
                                <small><?= $reviewCount ?> đánh giá</small>
                            </div>
                            <div class="pd-rating-bars">
                                <?php for ($stars = 5; $stars >= 1; $stars--): ?>
                                    <?php
                                    $ratingCount = count(array_filter(
                                        $reviews,
                                        static fn(array $review): bool => (int)$review['so_sao'] === $stars
                                    ));
                                    $ratingPercent = $reviews ? (int)round($ratingCount / count($reviews) * 100) : 0;
                                    ?>
                                    <div>
                                        <span><?= $stars ?>★</span>
                                        <i><b style="width:<?= $ratingPercent ?>%"></b></i>
                                        <small><?= $ratingCount ?> đánh giá</small>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <a href="<?= BASE_PATH ?>/auth/login.php" class="pd-review-write">
                                GỬI ĐÁNH GIÁ CỦA BẠN
                            </a>
                        </div>
                        <div class="pd-review-list" data-review-list>
                            <?php if ($reviews): ?>
                                <?php foreach ($reviews as $review): ?>
                                    <article class="pd-review-item">
                                        <span class="pd-avatar"><i class="bi bi-person-fill"></i></span>
                                        <div>
                                            <h3>
                                                <?= e($review['ten_hien_thi']) ?>
                                                <small>Khách hàng đánh giá</small>
                                            </h3>
                                            <span class="pd-review-stars">
                                                <?= str_repeat('★', max(1, min(5, (int)$review['so_sao']))) ?>
                                            </span>
                                            <p><?= e($review['noi_dung'] ?: 'Dịch vụ mua hàng nhanh chóng và hỗ trợ tốt.') ?></p>
                                            <a href="#reviews">Thảo luận</a>
                                            <time><?= e(date('d-m-Y H:i', strtotime($review['created_at']))) ?></time>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Chưa có đánh giá cho sản phẩm này.</p>
                            <?php endif; ?>
                        </div>
                        <nav class="pd-review-pagination" aria-label="Chuyển trang đánh giá">
                            <button type="button" data-review-page="first" aria-label="Trang đầu">«</button>
                            <button type="button" data-review-page="prev" aria-label="Trang trước">‹</button>
                            <span data-review-page-indicator>1 / 1</span>
                            <button type="button" data-review-page="next" aria-label="Trang tiếp">›</button>
                            <button type="button" data-review-page="last" aria-label="Trang cuối">»</button>
                        </nav>
                    </section>
                </div>
                <aside class="pd-sidebar">
                    <section class="detail-card pd-why">
                        <h2>VÌ SAO BẠN NÊN CHỌN IBP TECHNOLOGY</h2>
                        <ul>
                            <li><i class="bi bi-patch-check"></i> Sản phẩm chính hãng</li>
                            <li><i class="bi bi-tools"></i> Hỗ trợ kỹ thuật</li>
                            <li><i class="bi bi-shield-check"></i> Hỗ trợ bảo hành theo chính sách</li>
                            <li><i class="bi bi-truck"></i> Tư vấn giao hàng trên toàn quốc</li>
                        </ul>
                    </section>
                    <?php if ($faqs): ?>
                    <section class="detail-card pd-faq-card">
                        <h2>CÂU HỎI THƯỜNG GẶP</h2>
                        <div class="faq-list pd-faq-list">
                            <?php foreach ($faqs as $faqIndex => $faq): ?>
                                <?php $answerId = 'pd-faq-answer-' . $faqIndex; ?>
                                <article class="faq-item">
                                    <button
                                        class="faq-question"
                                        type="button"
                                        aria-expanded="false"
                                        aria-controls="<?= $answerId ?>"
                                    >
                                        <i class="bi bi-plus-circle"></i>
                                        <span><?= e($faq['cau_hoi']) ?></span>
                                    </button>
                                    <div class="faq-answer" id="<?= $answerId ?>" hidden>
                                        <p><?= e($faq['cau_tra_loi']) ?></p>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    <?php endif; ?>
                    <section class="pd-consult-card">
                        <h2>ĐĂNG KÝ TƯ VẤN</h2>
                        <form action="<?= BASE_PATH ?>/contact.php" method="post">
                            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                            <input name="name" placeholder="Họ và tên" required>
                            <input name="phone" placeholder="Số điện thoại*" required>
                            <input type="email" name="email" placeholder="Email (không bắt buộc)">
                            <textarea name="message" placeholder="Nội dung"></textarea>
                            <button type="submit">ĐĂNG KÝ</button>
                        </form>
                    </section>
                    <?php if ($recentProducts): ?>
                    <section class="detail-card pd-viewed">
                        <h2>SẢN PHẨM ĐÃ XEM</h2>
                        <?php foreach ($recentProducts as $recentProduct): ?>
                            <?php $recentPrice = (float)($recentProduct['gia_khuyen_mai'] ?: $recentProduct['gia']); ?>
                            <a href="<?= BASE_PATH ?>/product/detail.php?id=<?= (int)$recentProduct['id'] ?>">
                                <img
                                    src="<?= e(asset_url($recentProduct['anh_chinh'] ?? null) ?: $asset('sanpham.png')) ?>"
                                    alt=""
                                    loading="lazy"
                                >
                                <span>
                                    <?= e($recentProduct['ten']) ?>
                                    <b><?= money($recentPrice) ?></b>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </section>
                    <?php endif; ?>
                </aside>
            </section>

        <section class="pd-similar" aria-labelledby="similar-products-title">
            <div class="pd-section-title">
                <h2 id="similar-products-title">Các sản phẩm tương tự</h2>
                <a href="<?= BASE_PATH ?>/product/catalog.php">Xem tất cả <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="pd-similar-carousel">
                <button type="button" class="pd-similar-arrow is-prev" data-similar-prev aria-label="Sản phẩm trước"><i class="bi bi-chevron-left"></i></button>
                <div class="pd-similar-grid" data-similar-track aria-label="Danh sách sản phẩm tương tự">
                    <?php foreach ($similarProducts as $similarProduct): ?>
                        <?php
                        $similarPrice = (float)($similarProduct['gia_khuyen_mai'] ?: $similarProduct['gia']);
                        $similarOldPrice = (float)$similarProduct['gia'];
                        ?>
                        <article class="product-card pd-similar-card">
                            <a class="product-card-image" href="<?= BASE_PATH ?>/product/detail.php?id=<?= (int)$similarProduct['id'] ?>">
                                <img src="<?= e(asset_url($similarProduct['anh_chinh'] ?? null) ?: $asset('sanpham.png')) ?>" alt="<?= e($similarProduct['ten']) ?>">
                            </a>
                            <div class="product-card-body">
                                <a class="product-card-name" href="<?= BASE_PATH ?>/product/detail.php?id=<?= (int)$similarProduct['id'] ?>"><?= e($similarProduct['ten']) ?></a>
                                <div class="product-card-price">
                                    <span class="price-current"><?= money($similarPrice) ?></span>
                                    <?php if ($similarOldPrice > $similarPrice): ?>
                                        <del class="price-old"><?= money($similarOldPrice) ?></del>
                                    <?php endif; ?>
                                </div>
                                <div class="product-card-desc"><?= e($similarProduct['mo_ta_ngan'] ?? '') ?></div>
                                <div class="product-card-meta">
                                    <span class="meta-item"><i class="bi bi-eye"></i> <?= number_format((int)$similarProduct['luot_xem']) ?></span>
                                    <span class="meta-item rating"><strong><?= number_format((float)$similarProduct['rating'], 1) ?></strong> <i class="bi bi-star-fill"></i> <small>(<?= (int)$similarProduct['so_danh_gia'] ?>)</small></span>
                                    <span class="meta-item"><i class="bi bi-heart"></i> (<?= number_format((int)$similarProduct['luot_yeu_thich']) ?>)</span>
                                </div>
                                <div class="product-card-actions">
                                    <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form">
                                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="product_id" value="<?= (int)$similarProduct['id'] ?>">
                                        <button type="submit" class="btn-add-cart">Thêm vào giỏ</button>
                                    </form>
                                    <button type="button" class="btn-buy-now" data-similar-buy="<?= (int)$similarProduct['id'] ?>">
                                        Mua ngay
                                    </button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="pd-similar-arrow is-next" data-similar-next aria-label="Sản phẩm tiếp theo"><i class="bi bi-chevron-right"></i></button>
            </div>
        </section>
    </div>
</main>
<script src="<?= BASE_PATH ?>/assets/js/product-detail.js?v=6"></script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
