<?php
require_once __DIR__ . '/../config/store.php';

$homeCategories = [
    'Máy lọc nước' => 'may-loc-nuoc',
    'Máy nước nóng' => 'may-nuoc-nong',
    'Máy lọc nước đầu nguồn' => 'may-loc-nuoc-dau-nguon',
    'Máy lọc không khí' => 'may-loc-khong-khi',
    'Lõi lọc' => 'loi-loc',
    'Máy lạnh' => 'may-lanh',
    'Phụ kiện' => 'phu-kien',
];
$promoProductCategories = [
    'Máy lọc nước' => 'may-loc-nuoc',
    'Máy nước nóng' => 'may-nuoc-nong',
    'Máy lọc nước đầu nguồn' => 'may-loc-nuoc-dau-nguon',
    'Máy lọc không khí' => 'may-loc-khong-khi',
    'Lõi lọc' => 'loi-loc',
];
$featuredProductCategories = $promoProductCategories + [
    'Máy lạnh' => 'may-lanh',
];
$carouselProductCategories = $featuredProductCategories;
$homeProducts = [];
$homeDiscountedProducts = [];
$homeProductError = '';
$availableHomeCategories = [];
$fillCarouselProducts = static function (array $products): array {
    $products = array_values($products);
    $productCount = count($products);
    for ($index = $productCount; $productCount > 0 && $index < 6; $index++) {
        $products[] = $products[$index % $productCount];
    }
    return $products;
};
try {
    $database = db();
    if (!$database) {
        $homeProductError = 'Chưa kết nối được cơ sở dữ liệu để tải sản phẩm.';
    } else {
        $statement = $database->prepare(
            'SELECT id, ten, danh_muc, gia, gia_khuyen_mai, mo_ta_ngan, anh_chinh, rating, so_danh_gia, da_ban '
            . 'FROM product WHERE hien_thi = 1 AND danh_muc = ? ORDER BY da_ban DESC, id DESC LIMIT 6'
        );
        $discountedStatement = $database->prepare(
            'SELECT id, ten, danh_muc, gia, gia_khuyen_mai, mo_ta_ngan, anh_chinh, rating, so_danh_gia, da_ban '
            . 'FROM product WHERE hien_thi = 1 AND danh_muc = ? '
            . 'AND gia_khuyen_mai > 0 AND gia_khuyen_mai < gia '
            . 'ORDER BY da_ban DESC, id DESC LIMIT 6'
        );
        foreach ($carouselProductCategories as $categoryName => $categoryKey) {
            $statement->execute([$categoryName]);
            $categoryProducts = $fillCarouselProducts($statement->fetchAll());
            if (!$categoryProducts) {
                continue;
            }

            $availableHomeCategories[$categoryName] = $categoryKey;
            if (isset($featuredProductCategories[$categoryName])) {
                $homeProducts = array_merge($homeProducts, $categoryProducts);
            }
            $discountedStatement->execute([$categoryName]);
            $discountedProducts = $fillCarouselProducts($discountedStatement->fetchAll());
            $homeDiscountedProducts = array_merge(
                $homeDiscountedProducts,
                $discountedProducts ?: $categoryProducts
            );
        }
    }
} catch (PDOException $exception) {
    error_log('Home product lookup failed: ' . $exception->getMessage());
    $homeProducts = [];
    $homeDiscountedProducts = [];
    $availableHomeCategories = [];
    $homeProductError = 'Chưa tải được danh sách sản phẩm. Vui lòng thử lại sau.';
}
$homeReviews = [];
if (db()) {
    try {
        $statement = db()->query(
            'SELECT r.ten_hien_thi, r.so_sao, r.noi_dung, p.ten AS ten_san_pham '
            . 'FROM danh_gia r INNER JOIN product p ON p.id = r.product_id '
            . 'WHERE r.trang_thai = 1 AND p.hien_thi = 1 '
            . 'ORDER BY r.created_at DESC, r.id DESC LIMIT 4'
        );
        $homeReviews = $statement->fetchAll();
    } catch (PDOException $exception) {
        error_log('Home review lookup failed: ' . $exception->getMessage());
        $homeReviews = [];
    }
}

$pageTitle = 'IBP Technology - Trang chủ';
require_once __DIR__ . '/../layouts/header.php';
?>
    <!-- ==================== DANH MỤC SẢN PHẨM ==================== -->
    <section class="home-banner-section">
        <div class="home-banner-container">
            <aside class="product-category">
                <div class="product-category-header">
                    <i class="bi bi-list"></i>
                    <span>DANH MỤC SẢN PHẨM</span>
                </div>
                <ul class="product-category-list">
                    <?php foreach ($homeCategories as $categoryName => $categoryKey): ?>
                        <li class="product-category-item">
                            <a href="<?= BASE_PATH ?>/product/catalog.php?category=<?= rawurlencode($categoryKey) ?>">
                                <span class="category-dot"></span>
                                <span class="category-name"><?= e($categoryName) ?></span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        <!-- BANNER CHÍNH -->
        <div class="main-banner-group">
            <div class="main-banner">
                <div class="swiper main-banner-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="<?= BASE_PATH ?>/assets/images/Banner.png" alt="Giải pháp nước sạch IBP Technology" class="main-banner-image">
                        </div>
                    </div>
                    <div class="swiper-pagination main-banner-pagination"></div>
                </div>
            </div>
        </div>
        <!-- BANNER PHỤ -->
        <div class="side-banners">
            <div class="side-banner">
                <div class="swiper side-banner-swiper" data-delay="4000">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="<?= BASE_PATH ?>/assets/images/banner2.png" alt="Side banner 1">
                        </div>
                        <div class="swiper-slide">
                            <img src="<?= BASE_PATH ?>/assets/images/banner2-1.png" alt="Side banner 2">
                        </div>
                    </div>
                </div>
            </div>
            <div class="side-banner">
                <div class="swiper side-banner-swiper" data-delay="4000">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="<?= BASE_PATH ?>/assets/images/banner3.png" alt="Ứng dụng 1">
                        </div>
                        <div class="swiper-slide">
                            <img src="<?= BASE_PATH ?>/assets/images/ungdung.png" alt="Ứng dụng 2">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FEATURE BAR -->
    <div class="feature-bar" id="about">
        <div class="feature-bar-inner">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <span class="feature-text">Hỗ trợ giao hàng theo khu vực</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-award"></i>
                </div>
                <span class="feature-text">Bảo hành theo chính sách sản phẩm</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-tags"></i>
                </div>
                <span class="feature-text">Ưu đãi theo chương trình</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <span class="feature-text">Hỗ trợ đổi trả theo chính sách</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-headset"></i>
                </div>
                <span class="feature-text">Tư vấn từ 8:00 đến 18:00</span>
            </div>
        </div>
    </div>
    <!--==================== DANH MỤC NỔI BẬT =====================-->
    <section class="featured-category">
        <div class="featured-category-inner">
            <div class="featured-category-left">
                <h2 class="featured-category-title">Danh mục nổi bật</h2>
                <a href="<?= BASE_PATH ?>/product/catalog.php" class="featured-category-btn">XEM NGAY
                    <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
            <div class="featured-category-list">
                <?php foreach ($availableHomeCategories as $categoryName => $categoryKey): ?>
                    <a href="<?= BASE_PATH ?>/product/catalog.php?category=<?= rawurlencode($categoryKey) ?>" class="featured-category-item">
                        <div class="featured-category-icon">
                            <img src="<?= BASE_PATH ?>/assets/images/danhmuc.png" alt="">
                        </div>
                        <span class="featured-category-name"><?= e($categoryName) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="featured-category-scrollbar" aria-hidden="true">
                <span class="featured-category-scrollbar-thumb"></span>
            </div>
        </div>
    </section>
    <?php
    $productSectionTitle = 'Sản Phẩm Ưu Đãi';
    $productSectionProducts = $homeDiscountedProducts;
    require __DIR__ . '/product-section.php';
    ?>
    <!-- ==================== 2 AD ==================== -->
    <section class="dual-promo-banner">
        <div class="dual-promo-inner">
            <a href="<?= BASE_PATH ?>/product/catalog.php" class="dual-promo-item">
                <img src="<?= BASE_PATH ?>/assets/images/promotion1.png" alt="Khám phá sản phẩm IBP" class="dual-promo-image">
            </a>
            <a href="<?= BASE_PATH ?>/product/catalog.php" class="dual-promo-item">
                <img src="<?= BASE_PATH ?>/assets/images/promotion2.png" alt="Khám phá sản phẩm IBP" class="dual-promo-image">
            </a>
        </div>
    </section>
    <?php
    $productSectionTitle = 'Sản Phẩm Nổi Bật';
    $productSectionProducts = $homeProducts;
    require __DIR__ . '/product-section.php';
    ?>
    <!-- ==================== HERO AD ==================== -->
    <section class="hero-banner">
        <div class="hero-banner-inner">
            <img src="<?= BASE_PATH ?>/assets/images/advertisement.png" alt="Japandi Style - All Fit Naturally" class="hero-banner-image">
        </div>
    </section>
    <!-- ==================== KHÁCH HÀNG VÀ LIÊN HỆ ==================== -->
    <section class="review-faq-section">
        <div class="water-splash-top"></div>
        <div class="water-bg-bottom"></div>
        <div class="review-faq-inner">
            <!-- KHACH HANG -->
            <div class="review-faq-top">
                <div class="customer-reviews">
                    <div class="section-title-wrap">
                        <h2 class="section-title">Khách hàng nói gì</h2>
                        <span class="product-promo-underline"></span>
                    </div>
                    <div class="reviews-grid">
                        <?php if ($homeReviews): ?>
                            <?php foreach ($homeReviews as $reviewIndex => $review): ?>
                                <?php
                                $rating = max(0, min(5, (float)$review['so_sao']));
                                $stars = (int)floor($rating);
                                $avatar = $reviewIndex < 2 ? 'customer1.png' : 'customer-africa.png';
                                ?>
                                <article class="review-card">
                                    <div class="review-header">
                                        <img
                                            class="review-avatar"
                                            src="<?= BASE_PATH ?>/assets/images/<?= e($avatar) ?>"
                                            alt=""
                                            loading="lazy"
                                        >
                                        <div class="review-info">
                                            <h3 class="review-name"><?= e($review['ten_hien_thi']) ?></h3>
                                            <span class="review-badge">Featured</span>
                                            <div class="review-rating">
                                                <div class="stars" aria-label="<?= e(number_format($rating, 1)) ?> trên 5 sao">
                                                    <?php for ($star = 1; $star <= 5; $star++): ?>
                                                        <i class="bi <?= $star <= $stars ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <span class="rating-score"><?= e(number_format($rating, 1)) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="review-text"><?= e($review['noi_dung'] ?: 'Đánh giá về ' . $review['ten_san_pham']) ?></p>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Chưa có đánh giá khách hàng được công khai.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- GÓC THẮC MẮC -->
                <div class="faq-section">
                    <div class="section-title-wrap">
                        <h2 class="section-title">Góc thắc mắc giải đáp</h2>
                        <span class="product-promo-underline"></span>
                    </div>
                    <ul class="faq-list">
                        <li class="faq-item">
                            <button class="faq-question" type="button" aria-expanded="false" aria-controls="faq-answer-1">
                                <i class="bi bi-plus-circle" aria-hidden="true"></i>
                                <span>Làm sao để chọn máy lọc nước phù hợp với gia đình?</span>
                            </button>
                            <div class="faq-answer" id="faq-answer-1" hidden>
                                <p>Hãy cân nhắc số người sử dụng, nguồn nước đầu vào và vị trí lắp đặt. Nếu chưa chắc nên chọn mẫu nào, bạn có thể liên hệ IBP để được tư vấn.</p>
                            </div>
                        </li>
                        <li class="faq-item">
                            <button class="faq-question" type="button" aria-expanded="false" aria-controls="faq-answer-2">
                                <i class="bi bi-plus-circle" aria-hidden="true"></i>
                                <span>Sản phẩm có được bảo hành chính hãng không?</span>
                            </button>
                            <div class="faq-answer" id="faq-answer-2" hidden>
                                <p>Sản phẩm được áp dụng chính sách bảo hành theo quy định của nhà sản xuất. Thời hạn và điều kiện bảo hành được ghi trên trang chi tiết của từng sản phẩm.</p>
                            </div>
                        </li>
                        <li class="faq-item">
                            <button class="faq-question" type="button" aria-expanded="false" aria-controls="faq-answer-3">
                                <i class="bi bi-plus-circle" aria-hidden="true"></i>
                                <span>Phí giao hàng được tính như thế nào?</span>
                            </button>
                            <div class="faq-answer" id="faq-answer-3" hidden>
                                <p>Phí giao hàng hiển thị trong giỏ hàng theo thông tin từng sản phẩm. Nếu đơn có nhiều sản phẩm, hệ thống áp dụng mức cao nhất của các sản phẩm không miễn phí, tính một lần cho đơn hàng.</p>
                            </div>
                        </li>
                        <li class="faq-item">
                            <button class="faq-question" type="button" aria-expanded="false" aria-controls="faq-answer-4">
                                <i class="bi bi-plus-circle" aria-hidden="true"></i>
                                <span>Tôi có thể nhờ tư vấn lắp đặt sản phẩm không?</span>
                            </button>
                            <div class="faq-answer" id="faq-answer-4" hidden>
                                <p>Có. Bạn có thể trao đổi nhu cầu lắp đặt với nhân viên khi đặt hàng để được hướng dẫn phương án phù hợp với vị trí sử dụng.</p>
                            </div>
                        </li>
                        <li class="faq-item">
                            <button class="faq-question" type="button" aria-expanded="false" aria-controls="faq-answer-5">
                                <i class="bi bi-plus-circle" aria-hidden="true"></i>
                                <span>Khi nào cần thay lõi lọc?</span>
                            </button>
                            <div class="faq-answer" id="faq-answer-5" hidden>
                                <p>Chu kỳ thay lõi tùy thuộc vào loại lõi, chất lượng nguồn nước và mức độ sử dụng. Hãy tham khảo hướng dẫn của nhà sản xuất hoặc liên hệ bộ phận hỗ trợ để được kiểm tra.</p>
                            </div>
                        </li>
                        <li class="faq-item">
                            <button class="faq-question" type="button" aria-expanded="false" aria-controls="faq-answer-6">
                                <i class="bi bi-plus-circle" aria-hidden="true"></i>
                                <span>Làm sao để kiểm tra đơn hàng của tôi?</span>
                            </button>
                            <div class="faq-answer" id="faq-answer-6" hidden>
                                <p>Tra cứu trạng thái bằng <a href="<?= BASE_PATH ?>/cart/track.php">mã đơn hàng và số điện thoại đặt hàng</a>.</p>
                            </div>
                        </li>
                        <li class="faq-item">
                            <button class="faq-question" type="button" aria-expanded="false" aria-controls="faq-answer-7">
                                <i class="bi bi-plus-circle" aria-hidden="true"></i>
                                <span>Tôi cần hỗ trợ thêm thì liên hệ bằng cách nào?</span>
                            </button>
                            <div class="faq-answer" id="faq-answer-7" hidden>
                                <p>Bạn có thể gọi hotline 0983537155 hoặc gửi yêu cầu qua trang Liên hệ để được nhân viên hỗ trợ.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- LIEN HE -->
            <div class="contact-form-bottom">
                <div class="contact-cta">
                    <h2 class="contact-heading">Liên hệ với chúng tôi<br>để có trải nghiệm mua hàng<br>tốt nhất</h2>
                    <div class="contact-buttons">
                        <a href="<?= BASE_PATH ?>/product/catalog.php" class="btn-shop-now">Shop Now <i class="bi bi-arrow-right"></i></a>
                        <a href="<?= BASE_PATH ?>/contact.php" class="btn-contact-us">Contact Us <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <!-- FORM ĐĂNG KÝ BÁO GIÁ -->
                <div class="quote-form">
                    <h3 class="form-title">Đăng Ký Nhận Báo Giá</h3>
                    <form action="<?= BASE_PATH ?>/contact.php" method="post" class="form-body">
                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                        <input type="text" name="name" placeholder="Tên quý khách" class="form-input" required>
                        <div class="form-row">
                            <input type="tel" name="phone" placeholder="Số điện thoại" class="form-input" required>
                            <input type="email" name="email" placeholder="Email" class="form-input">
                        </div>
                        <select name="product" class="form-select" required>
                            <option value="" disabled selected>Chọn sản phẩm</option>
                            <option value="may-loc-nuoc">Máy lọc nước</option>
                            <option value="may-nuoc-nong">Máy nước nóng</option>
                            <option value="may-loc-khong-khi">Máy lọc không khí</option>
                            <option value="may-loc-nuoc-dau-nguon">Máy lọc nước đầu nguồn</option>
                            <option value="loi-loc">Lõi lọc</option>
                        </select>
                        <textarea name="message" placeholder="Ghi chú" class="form-textarea" rows="4"></textarea>
                        <button type="submit" class="btn-submit">ĐĂNG KÝ</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- ==================== TIN TỨC & SỰ KIỆN ==================== -->
    <?php
    $homeNewsItems = [
        ['image' => 'new1.png', 'title' => 'Housing Markets That Changed the Most This Week'],
        ['image' => 'new2.png', 'title' => 'Read Unveils the Best Canadian Cities for Biking'],
        ['image' => 'new3.png', 'title' => '10 Walkable Cities Where You Can Live Affordably'],
        ['image' => 'new4.png', 'title' => 'New Apartment Nice in the Best Canadian Cities'],
    ];
    ?>
    <section class="news-section">
        <div class="news-inner">
            <div class="news-title-wrap">
                <h2 class="news-title">Tin tức & Sự kiện</h2>
                <span class="underline"></span>
            </div>
            <div class="news-grid" id="newsGrid">
                <?php foreach ($homeNewsItems as $newsItem): ?>
                    <article class="news-card">
                        <a href="<?= BASE_PATH ?>/news/detail.php" class="news-card-image">
                            <img src="<?= BASE_PATH ?>/assets/images/<?= e($newsItem['image']) ?>" alt="<?= e($newsItem['title']) ?>" loading="lazy">
                        </a>
                        <div class="news-card-body">
                            <span class="news-date">Aug 19, 2025</span>
                            <h3 class="news-card-title">
                                <a href="<?= BASE_PATH ?>/news/detail.php"><?= e($newsItem['title']) ?></a>
                            </h3>
                            <a href="<?= BASE_PATH ?>/news/detail.php" class="news-read-more">
                                Read More <i class="bi bi-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="detail-card news-detail-message" id="newsUpdateMessage" hidden aria-live="polite">
                <h3>Đang cập nhật nội dung</h3>
                <p>Các bài viết và sự kiện sẽ được đăng tại đây khi có nội dung chính thức.</p>
                <a href="<?= BASE_PATH ?>/news/detail.php">Xem thông tin tin tức</a>
            </div>
            <div class="news-footer">
                <button type="button" id="btnToggleNews" class="btn-view-all-news" aria-expanded="false" aria-controls="newsUpdateMessage">
                    XEM TẤT CẢ <i class="bi bi-chevron-right" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </section>
    <!-- ==================== HÌNH ẢNH THỰC TẾ ==================== -->
    <section class="real-images-section">
        <div class="real-images-inner">
            <div class="real-images-title-wrap">
                <h2 class="real-images-title">Hình Ảnh Thực Tế</h2>
                <span class="underline"></span>
            </div>
            <div class="real-images-grid">
                <div class="real-image-item">
                    <img src="<?= BASE_PATH ?>/assets/images/thucte1.png" alt="anh thuc te">
                </div>
                <div class="real-image-item">
                    <img src="<?= BASE_PATH ?>/assets/images/thucte2.png" alt="anh thuc te">
                </div>
                <div class="real-image-item">
                    <img src="<?= BASE_PATH ?>/assets/images/thucte3.png" alt="anh thuc te">
                </div>
                <div class="real-image-item">
                    <img src="<?= BASE_PATH ?>/assets/images/thucte4.png" alt="anh thuc te">
                </div>
            </div>
        </div>
    </section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
