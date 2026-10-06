<?php
require_once __DIR__ . '/../config/store.php';

$pdo = db();
$productId = isset($_GET['id']) ? max(1, (int)$_GET['id']) : 0;
$product = null;
$images = [];
$variants = [];
$addons = [];
$reviews = [];
$faqs = [];

if ($pdo) {
    try {
        if (!$productId) {
            $productId = (int)$pdo->query("SELECT id FROM product WHERE slug = 'may-loc-nuoc-ao-smith-a2' LIMIT 1")->fetchColumn();
        }
        $stmt = $pdo->prepare(
            'SELECT p.*, d.mo_ta_chi_tiet, d.thong_so_ky_thuat, d.bao_hanh, d.phi_giao_hang, ' .
            'd.giao_hang_mien_phi, d.thong_tin_uu_dai ' .
            'FROM product p LEFT JOIN productdetail d ON d.product_id = p.id ' .
            'WHERE p.id = ? AND p.hien_thi = 1'
        );
        $stmt->execute([$productId]);
        $product = $stmt->fetch() ?: null;
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
            $stmt = $pdo->prepare('SELECT ten_hien_thi, so_sao, noi_dung, created_at FROM danh_gia WHERE product_id = ? AND trang_thai = 1 ORDER BY created_at DESC, id DESC');
            $stmt->execute([$productId]);
            $reviews = $stmt->fetchAll();
            $stmt = $pdo->prepare('SELECT cau_hoi, cau_tra_loi FROM product_faq WHERE product_id = ? AND hien_thi = 1 ORDER BY thu_tu, id');
            $stmt->execute([$productId]);
            $faqs = $stmt->fetchAll();
        }
    } catch (PDOException $exception) {
        // The fallback data keeps the detail page usable until the updated SQL is imported.
    }
}

$asset = static fn(string $name): string => BASE_PATH . '/assets/images/' . $name;
$product = $product ?: [
    'id' => $productId ?: 2, 'ten' => 'Máy Lọc Nước A. O. Smith A2', 'thuong_hieu' => 'AO Smith',
    'danh_muc' => 'Máy lọc nước', 'gia' => 12600000, 'gia_khuyen_mai' => 9200000,
    'mo_ta_ngan' => 'Máy lọc nước A. O. Smith A2 với công nghệ lọc tiên tiến, mang đến nguồn nước tinh khiết cho gia đình.',
    'anh_chinh' => $asset('maylocnuoc-a.o.smith.png'), 'rating' => 4.9, 'so_danh_gia' => 20,
    'da_ban' => 231,
    'so_luong_ton' => 12,
    'mo_ta_chi_tiet' => 'Máy lọc nước A. O. Smith A2 kết hợp công nghệ lọc hiện đại và thiết kế gọn đẹp. Sản phẩm hỗ trợ nguồn nước sạch cho sinh hoạt hằng ngày.',
    'thong_so_ky_thuat' => json_encode([
        'Mã sản phẩm' => 'TRIM ION US-100L',
        'Xuất xứ' => 'Mỹ',
        'Số cấp lọc' => '7 cấp lọc',
        'Chức năng' => 'Nước thường',
        'Điện áp đầu vào' => 'AC 220V / 50Hz',
        'Công suất (tổng)' => '85 W',
        'Áp suất nước đầu vào phù hợp' => '0.1MPa ~ 0.35MPa',
        'Nhiệt độ nước cấp' => '5~38°C',
        'Công suất lọc/phút' => '1.1 L/phút',
        'Phương pháp lọc rửa' => 'Tự động làm sạch',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'bao_hanh' => '24 tháng', 'phi_giao_hang' => 0, 'giao_hang_mien_phi' => 1,
    'thong_tin_uu_dai' => 'Lắp thêm lõi lọc nước ion kiềm alkaline hydrogen, nhập khẩu Hàn Quốc chỉ 500.000đ (giá thị trường 950.000đ).'
];
$image = $product['anh_chinh'] ?: $asset('maylocnuoc-a.o.smith.png');
if (!$images) {
    $images = [
        ['image_url' => $image, 'alt_text' => $product['ten']],
        ['image_url' => $asset('a.o.smith-mini1.png'), 'alt_text' => 'Góc nghiêng máy lọc nước A. O. Smith'],
        ['image_url' => $asset('a.o.smith-mini2.png'), 'alt_text' => 'Chi tiết máy lọc nước A. O. Smith'],
        ['image_url' => $asset('a.o.smith-mini3.png'), 'alt_text' => 'Mặt trước máy lọc nước A. O. Smith'],
    ];
}
if (!$variants) {
    $variants = [
        ['id' => 1, 'ten_phien_ban' => 'Denon HEOS 5 HS2', 'image_url' => $asset('a.o.smith-mini1.png'), 'sku' => 'A2-HS2', 'gia' => null],
        ['id' => 2, 'ten_phien_ban' => 'Denon HEOS 6 KT3', 'image_url' => $asset('a.o.smith-mini2.png'), 'sku' => 'A2-KT3', 'gia' => null],
        ['id' => 3, 'ten_phien_ban' => 'Denon HEOS 7 BK1', 'image_url' => $asset('a.o.smith-mini3.png'), 'sku' => 'A2-BK1', 'gia' => null],
    ];
}
if (!$addons) {
    $addons = [];
    for ($i = 1; $i <= 4; $i++) {
        $addons[] = [
            'id' => $i,
            'ten' => 'Lõi lọc Slim - tiện nghi an tâm mỗi ngày',
            'image_url' => $asset('loiloc.png'),
            'gia_goc' => 350000,
            'gia_khuyen_mai' => 300000,
        ];
    }
}
if (!$faqs) {
    $faqs = [
        [
            'cau_hoi' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit?',
            'cau_tra_loi' => 'Đội ngũ IBP sẽ tư vấn cấu hình phù hợp với nguồn nước và nhu cầu sử dụng của gia đình bạn.',
        ],
        [
            'cau_hoi' => 'Đâu là ưu điểm của lõi lọc này?',
            'cau_tra_loi' => 'Lõi lọc chính hãng giúp duy trì hiệu suất lọc ổn định. IBP hỗ trợ thay lõi và nhắc lịch bảo dưỡng định kỳ.',
        ],
        ['cau_hoi' => 'Sản phẩm có được lắp đặt tại nhà không?', 'cau_tra_loi' => 'Có. Kỹ thuật viên sẽ liên hệ xác nhận địa chỉ và thời gian lắp đặt thuận tiện cho bạn.'],
        ['cau_hoi' => 'Thời gian bảo hành sản phẩm là bao lâu?', 'cau_tra_loi' => 'Sản phẩm được bảo hành chính hãng 24 tháng theo điều kiện của nhà sản xuất.'],
        [
            'cau_hoi' => 'Tôi cần thay lõi lọc sau bao lâu?',
            'cau_tra_loi' => 'Chu kỳ thay lõi phụ thuộc chất lượng nguồn nước và lượng nước sử dụng. Hãy liên hệ IBP để được kiểm tra và tư vấn.',
        ],
    ];
}
$rawSpecs = json_decode((string)($product['thong_so_ky_thuat'] ?? ''), true) ?: [];
$productOrigin = $rawSpecs['Xuất xứ'] ?? 'Mỹ';
$specFields = [
    'Số cấp lọc' => [['Số cấp lọc'], '7 cấp lọc'],
    'Chức năng' => [['Chức năng'], 'Nước thường'],
    'Điện áp đầu vào' => [['Điện áp đầu vào'], 'AC 220V/ 50HZ'],
    'Công suất (tổng)' => [['Công suất (tổng)'], '85 W'],
    'Áp suất nước cấp phù hợp' => [['Áp suất nước cấp phù hợp', 'Áp suất nước đầu vào phù hợp'], '0.1MPa ~ 0.35MPa'],
    'Nhiệt độ nước cấp' => [['Nhiệt độ nước cấp'], '5~38°C'],
    'Công suất lọc/phút' => [['Công suất lọc/phút'], '1.1 L/phút'],
    'Phương pháp súc rửa' => [['Phương pháp súc rửa', 'Phương pháp lọc rửa'], 'Tự động làm sạch'],
];
$specs = [];
foreach ($specFields as $label => [$sourceLabels, $fallbackValue]) {
    $value = $fallbackValue;
    foreach ($sourceLabels as $sourceLabel) {
        if (array_key_exists($sourceLabel, $rawSpecs)) {
            $value = $rawSpecs[$sourceLabel];
            break;
        }
    }
    $specs[$label] = $value;
}
$currentPrice = (float)($product['gia_khuyen_mai'] ?: $product['gia']);
$originalPrice = (float)$product['gia'];
$discountPercent = $originalPrice > $currentPrice ? (int)round(($originalPrice - $currentPrice) / $originalPrice * 100) : 0;
$baseImage = $images[0]['image_url'] ?? $image;
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
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <input type="hidden" name="variant_id" value="<?= (int)$variants[0]['id'] ?>" data-selected-variant>
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
                                    data-gallery-image="<?= e($galleryImage['image_url']) ?>"
                                    aria-label="Xem ảnh <?= $index + 1 ?>"
                                >
                                    <img
                                        src="<?= e($galleryImage['image_url']) ?>"
                                        alt="<?= e($galleryImage['alt_text'] ?? ('Ảnh sản phẩm ' . ($index + 1))) ?>"
                                    >
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <a class="pd-spec-shortcut" href="#product-information" data-specs-shortcut>Xem thông số<br>kỹ thuật <i class="bi bi-arrow-down"></i></a>
                    </div>
                    <div class="pd-social">
                        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="Zalo" class="zalo-mark">Zalo</a>
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" aria-label="Chia sẻ"><i class="bi bi-qr-code"></i></a>
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
                            (<?= e((string)$product['rating']) ?>)
                        </div>
                    </div>
                    <div class="pd-brand-rating">
                        <span>Thương hiệu: <a href="#product-information"><?= e($product['thuong_hieu'] ?: 'AO Smith') ?></a></span>
                        <i></i>
                        <span class="pd-stars">★★★★★</span>
                        <a href="#reviews"><?= (int)$product['so_danh_gia'] ?> đánh giá</a>
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
                        <span>Bảo hành: <b><?= e($product['bao_hanh'] ?: '24 tháng') ?></b></span>
                        <span>Thương hiệu/Nơi sản xuất: <b><?= e($productOrigin) ?></b></span>
                        <span>Giao hàng: <b><?= !empty($product['giao_hang_mien_phi']) ? 'Miễn phí' : money((float)$product['phi_giao_hang']) ?></b></span>
                    </div>
                    <div class="pd-product-info">
                        <h2>THÔNG TIN SẢN PHẨM:</h2>
                        <p><?= e($product['mo_ta_chi_tiet'] ?: $product['mo_ta_ngan']) ?></p>
                        <ul>
                            <li>Lựa chọn tin cậy cho gia đình hiện đại.</li>
                            <li>Thiết kế tinh gọn, dễ sử dụng và bảo dưỡng.</li>
                        </ul>
                    </div>
                    <div class="pd-variants"><b>Phiên bản</b><div class="pd-variant-list">
                        <?php foreach ($variants as $index => $variant): ?>
                            <?php $variantImage = $variant['image_url'] ?: $baseImage; ?>
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
                    <div class="pd-promotion">
                        <h2><i class="bi bi-gift-fill"></i> ƯU ĐÃI KHI MUA HÀNG:</h2>
                        <ul>
                            <li>Lắp thêm lõi lọc nước ion kiềm alkaline hydrogen nhập khẩu Hàn Quốc chỉ 500.000đ (giá thị trường 950.000đ).</li>
                            <li>Tặng thiết bị kiểm tra độ tinh khiết của nước TDS trị giá 150.000đ.</li>
                            <li>Tặng gói lắp đặt và phụ kiện trị giá 500.000đ.</li>
                            <li>Tặng 2.000.000đ khi mua lõi lọc/nguyên vật liệu A. O. Smith.</li>
                        </ul>
                    </div>
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
                    <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="pd-bundle-form product-buy-form" data-product-detail-form>
                        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                        <input type="hidden" name="variant_id" value="<?= (int)$variants[0]['id'] ?>" data-selected-variant>
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
                                    <img src="<?= e($addon['image_url'] ?: $asset('loiloc.png')) ?>" alt="<?= e($addon['ten']) ?>">
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
                            <h2>Máy lọc nước A. O. Smith A2</h2>
                            <p><?= e($product['mo_ta_chi_tiet'] ?: $product['mo_ta_ngan']) ?></p>
                            <p><?= e($product['thong_tin_uu_dai'] ?: 'Bảo hành chính hãng, hỗ trợ giao hàng và lắp đặt toàn quốc.') ?></p>
                        </div>
                        <div class="pd-info-panel is-visible" data-info-panel="specs" id="technical-specs">
                            <div class="pd-spec-table">
                                <?php foreach ($specs as $label => $value): ?>
                                    <div>
                                        <span><?= e((string)$label) ?></span>
                                        <b><?= e((string)$value) ?></b>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <a href="#technical-specs" class="pd-more-specs">
                                Xem chi tiết thông số kỹ thuật
                            </a>
                        </div>
                    </section>

                    <section class="pd-reviews" id="reviews">
                        <div class="pd-review-heading"><h2>Reviews - <?= e($product['ten']) ?></h2></div>
                        <div class="pd-review-summary">
                            <div class="pd-review-score">
                                <strong><?= number_format(round((float)$product['rating']), 1) ?></strong>
                                <span>★★★★★</span>
                                <small><?= (int)$product['so_danh_gia'] ?> đánh giá</small>
                            </div>
                            <div class="pd-rating-bars">
                                <?php for ($stars = 5; $stars >= 1; $stars--): ?>
                                    <?php $ratingCount = $stars === 5 ? (int)$product['so_danh_gia'] : 0; ?>
                                    <div>
                                        <span><?= $stars ?>★</span>
                                        <i><b style="width:<?= (int)($stars === 5 ? 100 : 0) ?>%"></b></i>
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
                                                <small><i class="bi bi-patch-check-fill"></i> Đã mua tại IBP TECHNOLOGY</small>
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
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <article class="pd-review-item">
                                        <span class="pd-avatar"><i class="bi bi-person-fill"></i></span>
                                        <div>
                                            <h3>
                                                <?= ['Sơn Tùng', 'Nguyễn Quốc', 'Tiến Thịnh'][$i % 3] ?>
                                                <small><i class="bi bi-patch-check-fill"></i> Đã mua tại IBP TECHNOLOGY</small>
                                            </h3>
                                            <span class="pd-review-stars">★★★★★</span>
                                            <p>Dịch vụ mua hàng nhanh chóng và hỗ trợ tốt.</p>
                                            <a href="#reviews">Thảo luận</a>
                                            <time>11-09-2025 10:20</time>
                                        </div>
                                    </article>
                                <?php endfor; ?>
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
                            <li><i class="bi bi-patch-check"></i> Hàng chính hãng, giá rẻ nhất Việt Nam</li>
                            <li><i class="bi bi-tools"></i> Hỗ trợ kỹ thuật trọn đời với đội ngũ kỹ thuật nhanh làm</li>
                            <li><i class="bi bi-shield-check"></i> Dịch vụ bảo hành, bảo trì sản phẩm nhanh chóng</li>
                            <li><i class="bi bi-truck"></i> Giao hàng nhanh chóng trên toàn quốc</li>
                        </ul>
                    </section>
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
                    <section class="pd-consult-card">
                        <h2>ĐĂNG KÝ TƯ VẤN</h2>
                        <form action="<?= BASE_PATH ?>/contact.php" method="post">
                            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                            <input name="name" placeholder="Họ và tên" required>
                            <input name="phone" placeholder="Số điện thoại*" required>
                            <input type="email" name="email" placeholder="Email*">
                            <textarea name="message" placeholder="Nội dung"></textarea>
                            <button type="submit">ĐĂNG KÝ</button>
                        </form>
                    </section>
                    <section class="detail-card pd-viewed">
                        <h2>SẢN PHẨM ĐÃ XEM</h2>
                        <?php for ($i = 0; $i < 4; $i++): ?>
                            <a href="<?= BASE_PATH ?>/product/detail.php?id=<?= (int)$product['id'] ?>">
                                <img src="<?= e($asset('maylocnuoc-a.o.smith.png')) ?>" alt="">
                                <span>
                                    Máy lọc nước A. O. Smith A2
                                    <b>9.200.000₫</b>
                                </span>
                            </a>
                        <?php endfor; ?>
                    </section>
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
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <article class="product-card pd-similar-card">
                            <a class="product-card-image" href="<?= BASE_PATH ?>/product/detail.php?id=<?= 1 ?>">
                                <img src="<?= $asset('sanpham.png') ?>" alt="Máy lọc nước A. O. Smith ROSS ECO-AOC75PUR">
                                <span class="badge-hot">HOT</span>
                                <span class="badge-discount"></span>
                            </a>
                            <div class="product-card-body">
                                <a class="product-card-name" href="<?= BASE_PATH ?>/product/detail.php?id=<?= 1 ?>">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</a>
                                <div class="product-card-price"><span class="price-current">12.150.000₫</span><del class="price-old">14.500.000₫</del></div>
                                <div class="product-card-desc">Giải pháp lọc nước tiện lợi, thiết kế phù hợp cho gia đình hiện đại.</div>
                                <div class="product-card-meta">
                                    <span><i class="bi bi-eye"></i> 100</span>
                                    <span class="rating"><strong>4.8</strong> ★ <small>(28)</small></span>
                                    <span><i class="bi bi-heart"></i> (342)</span>
                                </div>
                                <div class="product-card-actions">
                                    <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form">
                                        <input type="hidden" name="product_id" value="<?= 1 ?>">
                                        <button type="submit" class="btn-add-cart">Thêm vào giỏ</button>
                                    </form>
                                    <button type="button" class="btn-buy-now" data-similar-buy="<?= 1 ?>">
                                        Mua ngay
                                    </button>
                                </div>
                            </div>
                        </article>
                    <?php endfor; ?>
                </div>
                <button type="button" class="pd-similar-arrow is-next" data-similar-next aria-label="Sản phẩm tiếp theo"><i class="bi bi-chevron-right"></i></button>
            </div>
        </section>
    </div>
</main>
<script src="<?= BASE_PATH ?>/assets/js/product-detail.js?v=3"></script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
