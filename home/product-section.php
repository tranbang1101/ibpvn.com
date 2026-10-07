<?php
$sectionCategoryOrder = $productSectionTitle === 'Sản Phẩm Nổi Bật'
    ? $featuredProductCategories
    : $promoProductCategories;
$sectionProducts = array_values(array_filter(
    $productSectionProducts,
    static fn(array $product): bool => isset($sectionCategoryOrder[trim((string)($product['danh_muc'] ?? ''))])
));
$sectionCategories = [];
foreach ($sectionCategoryOrder as $categoryName => $categoryKey) {
    foreach ($sectionProducts as $product) {
        if (trim((string)($product['danh_muc'] ?? '')) === $categoryName) {
            $sectionCategories[$categoryKey] = $categoryName;
            break;
        }
    }
}
$defaultCategory = (string)(array_key_first($sectionCategories) ?? '');
?>
<section class="product-promo" data-product-section data-default-category="<?= e($defaultCategory) ?>">
    <div class="product-promo-inner">
        <div class="product-promo-header">
            <div class="product-promo-title-wrap">
                <h2 class="product-promo-title"><?= e($productSectionTitle) ?></h2>
                <span class="product-promo-underline"></span>
            </div>
            <?php if ($sectionCategories): ?>
                <div class="product-promo-tabs">
                    <?php foreach ($sectionCategories as $categoryKey => $categoryName): ?>
                        <?php if ($categoryKey !== array_key_first($sectionCategories)): ?>
                            <span class="promo-dot"></span>
                        <?php endif; ?>
                        <button
                            type="button"
                            class="promo-tab <?= $categoryKey === $defaultCategory ? 'is-active' : '' ?>"
                            data-category="<?= e($categoryKey) ?>"
                            aria-pressed="<?= $categoryKey === $defaultCategory ? 'true' : 'false' ?>"
                        ><?= e($categoryName) ?></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="product-slider-wrap">
            <div class="swiper product-swiper" <?= !$sectionProducts ? 'hidden' : '' ?>>
                <div class="swiper-wrapper">
                    <?php foreach ($sectionProducts as $product): ?>
                        <?php
                        $productId = (int)$product['id'];
                        $categoryName = trim((string)($product['danh_muc'] ?? ''));
                        $categoryKey = $homeCategories[$categoryName] ?? ('category-' . $productId);
                        $currentPrice = (float)($product['gia_khuyen_mai'] ?: $product['gia']);
                        $oldPrice = (float)$product['gia'];
                        $discount = $oldPrice > $currentPrice
                            ? (int)round(($oldPrice - $currentPrice) / $oldPrice * 100)
                            : 0;
                        ?>
                        <div class="swiper-slide" data-category="<?= e($categoryKey) ?>">
                            <article class="product-card">
                                <a class="product-card-image" href="<?= BASE_PATH ?>/product/detail.php?id=<?= $productId ?>">
                                    <img
                                        src="<?= e(asset_url($product['anh_chinh'] ?? null) ?: BASE_PATH . '/assets/images/sanpham.png') ?>"
                                        alt="<?= e($product['ten']) ?>"
                                        loading="lazy"
                                    >
                                    <?php if ($discount > 0): ?>
                                        <span class="badge-hot">Ưu đãi</span>
                                        <span class="badge-discount" aria-label="Ưu đãi"></span>
                                    <?php endif; ?>
                                </a>
                                <div class="product-card-body">
                                    <a class="product-card-name" href="<?= BASE_PATH ?>/product/detail.php?id=<?= $productId ?>">
                                        <?= e($product['ten']) ?>
                                    </a>
                                    <div class="product-card-price">
                                        <span class="price-current"><?= money($currentPrice) ?></span>
                                        <?php if ($oldPrice > $currentPrice): ?>
                                            <del class="price-old"><?= money($oldPrice) ?></del>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-card-desc"><?= e($product['mo_ta_ngan'] ?? '') ?></div>
                                    <div class="product-card-meta">
                                        <span class="meta-item rating">
                                            <strong><?= number_format((float)$product['rating'], 1) ?></strong>
                                            <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(<?= (int)$product['so_danh_gia'] ?>)</span>
                                        </span>
                                        <span class="meta-item">Đã bán <?= number_format((int)$product['da_ban']) ?></span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form">
                                            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="product_id" value="<?= $productId ?>">
                                            <button type="submit" class="btn-add-cart">Thêm vào giỏ</button>
                                        </form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php?id=<?= $productId ?>" class="btn-buy-now">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="product-empty-state" <?= $sectionProducts ? 'hidden' : '' ?> role="status">
                <?= $homeProductError !== ''
                    ? e($homeProductError)
                    : ($productSectionTitle === 'Sản Phẩm Ưu Đãi'
                        ? 'Hiện chưa có sản phẩm ưu đãi.'
                        : 'Hiện chưa có sản phẩm đang kinh doanh.') ?>
            </div>
            <button class="product-prev" type="button" aria-label="Sản phẩm trước">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="product-next" type="button" aria-label="Sản phẩm tiếp theo">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
        <div class="product-promo-footer">
            <a class="btn-view-all" href="<?= BASE_PATH ?>/product/catalog.php">
                XEM TẤT CẢ <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>
</section>
