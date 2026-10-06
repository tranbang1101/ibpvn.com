<?php
require_once __DIR__ . '/../config/store.php';

$categories = [
    'may-loc-nuoc' => 'Máy lọc nước',
    'may-nuoc-nong' => 'Máy nước nóng',
    'may-loc-nuoc-dau-nguon' => 'Máy lọc nước đầu nguồn',
    'may-loc-khong-khi' => 'Máy lọc không khí',
    'loi-loc' => 'Lõi lọc',
    'may-lanh' => 'Máy lạnh',
    'phu-kien' => 'Phụ kiện',
];
$categoryValue = $_GET['category'] ?? '';
$categoryKey = is_string($categoryValue) ? trim($categoryValue) : '';
$categoryName = $categories[$categoryKey] ?? '';
$keywordValue = $_GET['keyword'] ?? '';
$keyword = is_string($keywordValue) ? trim($keywordValue) : '';
if (function_exists('mb_substr')) {
    $keyword = mb_substr($keyword, 0, 120, 'UTF-8');
} else {
    $keyword = substr($keyword, 0, 120);
}
$sortValue = $_GET['sort'] ?? 'popular';
$sort = is_string($sortValue) ? $sortValue : 'popular';
$sortOptions = [
    'popular' => 'Bán chạy',
    'price_asc' => 'Giá thấp đến cao',
    'price_desc' => 'Giá cao đến thấp',
];
if (!isset($sortOptions[$sort])) {
    $sort = 'popular';
}
$pageValue = $_GET['page'] ?? 1;
$pageNumber = is_string($pageValue) || is_int($pageValue)
    ? max(1, (int)(filter_var($pageValue, FILTER_VALIDATE_INT) ?: 1))
    : 1;
$pageSize = 12;
$totalProducts = 0;
$pageCount = 1;

$products = [];
$loadError = '';
$pdo = db();
if (!$pdo) {
    $loadError = 'Chưa kết nối được cơ sở dữ liệu. Hãy kiểm tra MySQL trong XAMPP.';
} else {
    $conditions = ['hien_thi = 1'];
    $parameters = [];

    if ($categoryName !== '') {
        $conditions[] = 'danh_muc = ?';
        $parameters[] = $categoryName;
    }
    if ($keyword !== '') {
        $conditions[] = '(ten LIKE ? OR thuong_hieu LIKE ? OR mo_ta_ngan LIKE ?)';
        $searchTerm = '%' . $keyword . '%';
        array_push($parameters, $searchTerm, $searchTerm, $searchTerm);
    }

    $orderBy = match ($sort) {
        'price_asc' => 'COALESCE(NULLIF(gia_khuyen_mai, 0), gia) ASC, id DESC',
        'price_desc' => 'COALESCE(NULLIF(gia_khuyen_mai, 0), gia) DESC, id DESC',
        default => 'da_ban DESC, id DESC',
    };

    try {
        $whereClause = ' WHERE ' . implode(' AND ', $conditions);
        $countStatement = $pdo->prepare('SELECT COUNT(*) FROM product' . $whereClause);
        $countStatement->execute($parameters);
        $totalProducts = (int)$countStatement->fetchColumn();
        $pageCount = max(1, (int)ceil($totalProducts / $pageSize));
        $pageNumber = min($pageNumber, $pageCount);

        $sql = 'SELECT id, slug, ten, danh_muc, thuong_hieu, gia, gia_khuyen_mai, '
            . 'mo_ta_ngan, anh_chinh, rating, so_danh_gia, da_ban '
            . 'FROM product' . $whereClause
            . ' ORDER BY ' . $orderBy . ' LIMIT ? OFFSET ?';
        $statement = $pdo->prepare($sql);
        foreach ($parameters as $index => $parameter) {
            $statement->bindValue($index + 1, $parameter, PDO::PARAM_STR);
        }
        $statement->bindValue(count($parameters) + 1, $pageSize, PDO::PARAM_INT);
        $statement->bindValue(count($parameters) + 2, ($pageNumber - 1) * $pageSize, PDO::PARAM_INT);
        $statement->execute();
        $products = $statement->fetchAll();
    } catch (PDOException $exception) {
        error_log('Product catalog lookup failed: ' . $exception->getMessage());
        $loadError = 'Chưa tải được danh sách sản phẩm. Vui lòng thử lại sau.';
    }
}

$pageTitle = ($categoryName ?: 'Sản phẩm') . ' | IBP Technology';
require_once __DIR__ . '/../layouts/header.php';
?>
<main class="shop-page catalog-page">
    <div class="shop-container">
        <nav class="shop-breadcrumb" aria-label="Đường dẫn">
            <a href="<?= BASE_PATH ?>/home/home.php">Trang chủ</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span><?= e($categoryName ?: 'Sản phẩm') ?></span>
        </nav>

        <header class="catalog-heading">
            <div>
                <span class="section-kicker">IBP TECHNOLOGY</span>
                <h1><?= e($categoryName ?: 'Danh sách sản phẩm') ?></h1>
                <?php if ($keyword !== ''): ?>
                    <p>Kết quả tìm kiếm cho “<?= e($keyword) ?>”</p>
                <?php else: ?>
                    <p>Chọn giải pháp phù hợp cho nhu cầu sử dụng của bạn.</p>
                <?php endif; ?>
            </div>
            <form class="catalog-sort" method="get">
                <?php if ($categoryKey !== ''): ?>
                    <input type="hidden" name="category" value="<?= e($categoryKey) ?>">
                <?php endif; ?>
                <?php if ($keyword !== ''): ?>
                    <input type="hidden" name="keyword" value="<?= e($keyword) ?>">
                <?php endif; ?>
                <label for="catalog-sort">Sắp xếp</label>
                <select id="catalog-sort" name="sort" onchange="this.form.submit()">
                    <?php foreach ($sortOptions as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $sort === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </header>

        <nav class="catalog-categories" aria-label="Danh mục sản phẩm">
            <a class="<?= $categoryKey === '' ? 'is-active' : '' ?>" href="<?= BASE_PATH ?>/product/catalog.php">Tất cả</a>
            <?php foreach ($categories as $key => $label): ?>
                <a
                    class="<?= $categoryKey === $key ? 'is-active' : '' ?>"
                    href="<?= BASE_PATH ?>/product/catalog.php?category=<?= rawurlencode($key) ?>"
                >
                    <?= e($label) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if ($loadError !== ''): ?>
            <div class="catalog-empty" role="status">
                <i class="bi bi-database-exclamation" aria-hidden="true"></i>
                <h2>Chưa thể hiển thị sản phẩm</h2>
                <p><?= e($loadError) ?></p>
            </div>
        <?php elseif (!$products): ?>
            <div class="catalog-empty" role="status">
                <i class="bi bi-box-seam" aria-hidden="true"></i>
                <h2>Chưa có sản phẩm trong danh mục này</h2>
                <p>Thử chọn danh mục khác hoặc tìm với từ khóa khác.</p>
                <a class="button-primary" href="<?= BASE_PATH ?>/product/catalog.php">Xem tất cả sản phẩm</a>
            </div>
        <?php else: ?>
            <?php
            $firstProductNumber = ($pageNumber - 1) * $pageSize + 1;
            $lastProductNumber = $firstProductNumber + count($products) - 1;
            $paginationQuery = array_filter([
                'category' => $categoryKey,
                'keyword' => $keyword,
                'sort' => $sort === 'popular' ? '' : $sort,
            ], static fn(string $value): bool => $value !== '');
            ?>
            <p class="catalog-result-count">
                Hiển thị <?= $firstProductNumber ?>–<?= $lastProductNumber ?> trong <?= $totalProducts ?> sản phẩm
            </p>
            <div class="catalog-grid">
                <?php foreach ($products as $product): ?>
                    <?php
                    $currentPrice = (float)($product['gia_khuyen_mai'] ?: $product['gia']);
                    $oldPrice = (float)$product['gia'];
                    $discount = $oldPrice > $currentPrice
                        ? (int)round(($oldPrice - $currentPrice) / $oldPrice * 100)
                        : 0;
                    ?>
                    <article class="product-card catalog-product-card">
                        <a class="product-card-image" href="<?= BASE_PATH ?>/product/detail.php?id=<?= (int)$product['id'] ?>">
                            <img
                                src="<?= e(asset_url($product['anh_chinh'] ?? null) ?: BASE_PATH . '/assets/images/sanpham.png') ?>"
                                alt="<?= e($product['ten']) ?>"
                                loading="lazy"
                            >
                            <?php if ($discount > 0): ?>
                                <span class="badge-hot">Ưu đãi</span>
                                <span class="badge-discount">-<?= $discount ?>%</span>
                            <?php endif; ?>
                        </a>
                        <div class="product-card-body">
                            <a class="product-card-name" href="<?= BASE_PATH ?>/product/detail.php?id=<?= (int)$product['id'] ?>">
                                <?= e($product['ten']) ?>
                            </a>
                            <div class="product-card-price">
                                <span class="price-current"><?= money($currentPrice) ?></span>
                                <?php if ($discount > 0): ?>
                                    <del class="price-old"><?= money($oldPrice) ?></del>
                                <?php endif; ?>
                            </div>
                            <div class="product-card-meta">
                                <span><i class="bi bi-star-fill"></i> <?= number_format((float)$product['rating'], 1) ?></span>
                                <span><?= (int)$product['so_danh_gia'] ?> đánh giá</span>
                            </div>
                            <div class="product-card-actions">
                                <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form">
                                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                    <button type="submit" class="btn-add-cart">Thêm vào giỏ</button>
                                </form>
                                <a class="btn-buy-now" href="<?= BASE_PATH ?>/product/detail.php?id=<?= (int)$product['id'] ?>">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($pageCount > 1): ?>
                <nav class="catalog-pagination" aria-label="Phân trang sản phẩm">
                    <?php if ($pageNumber > 1): ?>
                        <a href="<?= BASE_PATH ?>/product/catalog.php?<?= e(http_build_query($paginationQuery + ['page' => $pageNumber - 1])) ?>" rel="prev">Trước</a>
                    <?php endif; ?>
                    <span>Trang <?= $pageNumber ?> / <?= $pageCount ?></span>
                    <?php if ($pageNumber < $pageCount): ?>
                        <a href="<?= BASE_PATH ?>/product/catalog.php?<?= e(http_build_query($paginationQuery + ['page' => $pageNumber + 1])) ?>" rel="next">Tiếp</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
