<?php
$pageTitle = 'IBP Technology - Trang chủ';
require_once '../layouts/header.php';
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
                    <li class="product-category-item">
                        <a href="#">
                            <span class="category-dot"></span>
                            <span class="category-name">Máy Lọc Nước</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="product-category-item">
                        <a href="#">
                            <span class="category-dot"></span>
                            <span class="category-name">Máy Nước Nóng</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="product-category-item">
                        <a href="#">
                            <span class="category-dot"></span>
                            <span class="category-name">Máy lọc không khí</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="product-category-item">
                        <a href="#">
                            <span class="category-dot"></span>
                            <span class="category-name">Máy Lọc Nước Đầu Nguồn</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="product-category-item">
                        <a href="#">
                            <span class="category-dot"></span>
                            <span class="category-name">Lõi Lọc</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="product-category-item">
                        <a href="#">
                            <span class="category-dot"></span>
                            <span class="category-name">Máy Lạnh</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="product-category-item">
                        <a href="#">
                            <span class="category-dot"></span>
                            <span class="category-name">Phụ Kiện</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </aside>
        <!-- BANNER CHÍNH -->
        <div class="main-banner-group">
            <div class="main-banner">
                <div class="swiper main-banner-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="<?= BASE_PATH ?>/assets/images/Banner-mobile.png" alt="Giải pháp nước sạch IBP Technology" class="main-banner-image">
                        </div>
                        <div class="swiper-slide">
                            <img src="<?= BASE_PATH ?>/assets/images/Banner-mobile.png" alt="Giải pháp nước sạch IBP Technology" class="main-banner-image">
                        </div>
                        <div class="swiper-slide">
                            <img src="<?= BASE_PATH ?>/assets/images/Banner-mobile.png" alt="Giải pháp nước sạch IBP Technology" class="main-banner-image">
                        </div>
                    </div>
                    <div class="swiper-pagination main-banner-pagination"></div>
                </div>
            </div>
            <div class="main-banner-tabs" role="group" aria-label="Chọn nội dung banner">
                <button type="button" class="is-active" data-banner-slide="0" aria-pressed="true">Giới thiệu IBP</button>
                <button type="button" data-banner-slide="1" aria-pressed="false">Tiêu đề Slider</button>
                <button type="button" data-banner-slide="2" aria-pressed="false">Tiêu đề Slider</button>
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
    <div class="feature-bar">
        <div class="feature-bar-inner">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <span class="feature-text">Giao hàng toàn quốc</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-award"></i>
                </div>
                <span class="feature-text">Bảo hành lâu dài</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-tags"></i>
                </div>
                <span class="feature-text">Ưu đãi thường xuyên</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <span class="feature-text">Đổi trả miễn phí</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-headset"></i>
                </div>
                <span class="feature-text">Dịch vụ tư vấn 24/7</span>
            </div>
        </div>
    </div>
    <!--==================== DANH MỤC NỔI BẬT =====================-->
    <section class="featured-category">
        <div class="featured-category-inner">
            <div class="featured-category-left">
                <h2 class="featured-category-title">Danh mục nổi bật</h2>
                <a href="#" class="featured-category-btn">XEM NGAY
                    <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
            <div class="featured-category-list">
                <a href="#" class="featured-category-item">
                    <div class="featured-category-icon">
                        <img src="<?= BASE_PATH ?>/assets/images/danhmuc.png" alt="danh muc sp">
                    </div>
                    <span class="featured-category-name">Tên sản phẩm</span>
                </a>
                <a href="#" class="featured-category-item">
                    <div class="featured-category-icon">
                        <img src="<?= BASE_PATH ?>/assets/images/danhmuc.png" alt="danh muc sp">
                    </div>
                    <span class="featured-category-name">Tên sản phẩm</span>
                </a>
                <a href="#" class="featured-category-item">
                    <div class="featured-category-icon">
                        <img src="<?= BASE_PATH ?>/assets/images/danhmuc.png" alt="danh muc sp">
                    </div>
                    <span class="featured-category-name">Tên sản phẩm</span>
                </a>
                <a href="#" class="featured-category-item">
                    <div class="featured-category-icon">
                        <img src="<?= BASE_PATH ?>/assets/images/danhmuc.png" alt="danh muc sp">
                    </div>
                    <span class="featured-category-name">Tên sản phẩm</span>
                </a>
                <a href="#" class="featured-category-item">
                    <div class="featured-category-icon">
                        <img src="<?= BASE_PATH ?>/assets/images/danhmuc.png" alt="danh muc sp">
                    </div>
                    <span class="featured-category-name">Tên sản phẩm</span>
                </a>
                <a href="#" class="featured-category-item">
                    <div class="featured-category-icon">
                        <img src="<?= BASE_PATH ?>/assets/images/danhmuc.png" alt="danh muc sp">
                    </div>
                    <span class="featured-category-name">Tên sản phẩm</span>
                </a>
                <a href="#" class="featured-category-item">
                    <div class="featured-category-icon">
                        <img src="<?= BASE_PATH ?>/assets/images/danhmuc.png" alt="danh muc sp">
                    </div>
                    <span class="featured-category-name">Tên sản phẩm</span>
                </a>
            </div>
            <div class="featured-category-scrollbar" aria-hidden="true">
                <span class="featured-category-scrollbar-thumb"></span>
            </div>
        </div>
    </section>
    <!--==================== SẢN PHẨM ƯU ĐÃI =====================-->
    <section class="product-promo" data-product-section data-default-category="may-loc-nuoc">
        <div class="product-promo-inner">
            <div class="product-promo-header">
                <div class="product-promo-title-wrap">
                    <h2 class="product-promo-title">Sản Phẩm Ưu Đãi</h2>
                    <span class="product-promo-underline"></span>
                </div>
                <div class="product-promo-tabs">
                    <button type="button" class="promo-tab is-active" data-category="may-loc-nuoc">Máy Lọc Nước</button>
                    <span class="promo-dot"></span>
                    <button type="button" class="promo-tab" data-category="may-nuoc-nong">Máy Nước Nóng</button>
                    <span class="promo-dot"></span>
                    <button type="button" class="promo-tab" data-category="may-loc-nuoc-dau-nguon">Máy Lọc Nước Đầu Nguồn</button>
                    <span class="promo-dot"></span>
                    <button type="button" class="promo-tab" data-category="may-loc-khong-khi">Máy Lọc Không Khí</button>
                    <span class="promo-dot"></span>
                    <button type="button" class="promo-tab" data-category="loi-loc">Lõi Lọc</button>
                    <span class="promo-dot"></span>
                </div>
            </div>
            <div class="product-slider-wrap">
                <div class="swiper product-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="2"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="2"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/a.o.smith-mini2.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith A2</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">9,200,000₫</span>
                                        <span class="price-old">10,400,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.9</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1">
                                        <button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/a.o.smith-mini2.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith A2</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">9,200,000₫</span>
                                        <span class="price-old">10,400,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.9</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/a.o.smith-mini2.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith A2</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">9,200,000₫</span>
                                        <span class="price-old">10,400,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.9</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/a.o.smith-mini2.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith A2</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">9,200,000₫</span>
                                        <span class="price-old">10,400,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.9</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/a.o.smith-mini2.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith A2</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">9,200,000₫</span>
                                        <span class="price-old">10,400,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.9</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="product-empty-state" hidden>Danh mục này hiện chưa có sản phẩm.</div>
                <button class="product-prev" type="button">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="product-next" type="button">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            <!-- Nuts xem tat ca -->
            <div class="product-promo-footer">
                <button type="button" class="btn-view-all" data-products-toggle>XEM TẤT CẢ <i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </section>
    <!-- ==================== 2 AD ==================== -->
    <section class="dual-promo-banner">
        <div class="dual-promo-inner">
            <a href="#" class="dual-promo-item">
                <img src="<?= BASE_PATH ?>/assets/images/promotion1.png" alt="Loa Samsung Q600C" class="dual-promo-image">
            </a>
            <a href="#" class="dual-promo-item">
                <img src="<?= BASE_PATH ?>/assets/images/promotion2.png" alt="Loa Samsung Q600C" class="dual-promo-image">
            </a>
        </div>
    </section>
    <!--==================== SẢN PHẨM NỔI BẬT =====================-->
    <section class="product-promo" data-product-section data-default-category="may-loc-nuoc">
        <div class="product-promo-inner">
            <div class="product-promo-header">
                <div class="product-promo-title-wrap">
                    <h2 class="product-promo-title">Sản Phẩm Nổi Bật</h2>
                    <span class="product-promo-underline"></span>
                </div>
                <div class="product-promo-tabs">
                    <button type="button" class="promo-tab is-active" data-category="may-loc-nuoc">Máy Lọc Nước</button>
                    <span class="promo-dot"></span>
                    <button type="button" class="promo-tab" data-category="may-nuoc-nong">Máy Nước Nóng</button>
                    <span class="promo-dot"></span>
                    <button type="button" class="promo-tab" data-category="may-loc-nuoc-dau-nguon">Máy Lọc Nước Đầu Nguồn</button>
                    <span class="promo-dot"></span>
                    <button type="button" class="promo-tab" data-category="may-loc-khong-khi">Máy Lọc Không Khí</button>
                    <span class="promo-dot"></span>
                    <button type="button" class="promo-tab" data-category="loi-loc">Lõi Lọc</button>
                    <span class="promo-dot"></span>
                </div>
            </div>
            <div class="product-slider-wrap">
                <div class="swiper product-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" data-category="may-loc-nuoc">
                            <div class="product-card">
                                <div class="product-card-image">
                                    <img src="<?= BASE_PATH ?>/assets/images/sanpham.png" alt="sản phẩm">
                                    <span class="badge-hot">HOT</span>
                                    <span class="badge-discount"></span>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-name">Máy Lọc Nước A. O. Smith ROSS™ ECO-AOC75PUR</h3>
                                    <div class="product-card-price">
                                        <span class="price-current">12,150,000₫</span>
                                        <span class="price-old">14,500,000₫</span>
                                    </div>
                                    <div class="product-card-desc">
                                        Lorem ipsum dolor sit amet, consctetur adipiscing elit, dolor do eiusmod adipiscing text of the printing and typesetting industry.
                                    </div>
                                    <div class="product-card-meta">
                                        <span class="meta-item"><i class="bi bi-eye"></i> 100</span>
                                        <span class="meta-item rating">
                                            <strong>4.8</strong> <i class="bi bi-star-fill"></i>
                                            <span class="review-count">(28)</span>
                                        </span>
                                        <span class="meta-item"><i class="bi bi-heart"></i> (342)</span>
                                    </div>
                                    <div class="product-card-actions">
                                        <form method="post" action="<?= BASE_PATH ?>/cart/add.php" class="card-cart-form"><input type="hidden" name="product_id" value="1"><button type="submit" class="btn-add-cart">Thêm vào giỏ</button></form>
                                        <a href="<?= BASE_PATH ?>/product/detail.php" class="btn-buy-now">Mua ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="product-empty-state" hidden>Danh mục này hiện chưa có sản phẩm.</div>
                <button class="product-prev" type="button">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="product-next" type="button">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            <!-- Nuts xem tat ca -->
            <div class="product-promo-footer">
                <button type="button" class="btn-view-all" data-products-toggle>XEM TẤT CẢ <i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </section>
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
                        <div class="review-card">
                            <div class="review-header">
                                <img src="<?= BASE_PATH ?>/assets/images/customer1.png" alt="avatar" class="review-avatar">
                                <div class="review-info">
                                    <h4 class="review-name">Machic</h4>
                                    <span class="review-badge">Featured</span>
                                    <div class="review-rating">
                                        <div class="stars">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                        </div>
                                        <span class="rating-score">4.1</span>
                                    </div>
                                </div>
                            </div>
                            <p class="review-text">Good quality product can only be found in good stores</p>
                        </div>
                        <div class="review-card">
                            <div class="review-header">
                                <img src="<?= BASE_PATH ?>/assets/images/customer1.png" alt="avatar" class="review-avatar">
                                <div class="review-info">
                                    <h4 class="review-name">Blonwe</h4>
                                    <span class="review-badge">Featured</span>
                                    <div class="review-rating">
                                        <div class="stars">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                        </div>
                                        <span class="rating-score">4.1</span>
                                    </div>
                                </div>
                            </div>
                            <p class="review-text">All kinds of grocery products are available in our store.</p>
                        </div>
                        <div class="review-card">
                            <div class="review-header">
                                <img src="<?= BASE_PATH ?>/assets/images/customer-africa.png" alt="avatar" class="review-avatar">
                                <div class="review-info">
                                    <h4 class="review-name">Bacola</h4>
                                    <span class="review-badge">Featured</span>
                                    <div class="review-rating">
                                        <div class="stars">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-half"></i>
                                        </div>
                                        <span class="rating-score">4.5</span>
                                    </div>
                                </div>
                            </div>
                            <p class="review-text">Our work can definitely support the local economy.</p>
                        </div>
                        <div class="review-card">
                            <div class="review-header">
                                <img src="<?= BASE_PATH ?>/assets/images/customer-africa.png" alt="avatar" class="review-avatar">
                                <div class="review-info">
                                    <h4 class="review-name">Medibazar</h4>
                                    <span class="review-badge">Featured</span>
                                    <div class="review-rating">
                                        <div class="stars">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-half"></i>
                                        </div>
                                        <span class="rating-score">4.2</span>
                                    </div>
                                </div>
                            </div>
                            <p class="review-text">Save your time – save your money – shop from our grocery store.</p>
                        </div>
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
                                <p>Phí giao hàng phụ thuộc vào sản phẩm và địa chỉ nhận hàng. Nhân viên sẽ xác nhận phí và thời gian dự kiến với bạn khi tiếp nhận đơn.</p>
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
                                <p>Bạn có thể dùng mục “Kiểm tra đơn hàng” trên đầu trang hoặc liên hệ IBP và cung cấp mã đơn hàng để được hỗ trợ.</p>
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
                        <a href="#" class="btn-shop-now">Shop Now <i class="bi bi-arrow-right"></i></a>
                        <a href="#" class="btn-contact-us">Contact Us <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <!-- FORM ĐĂNG KÝ BÁO GIÁ -->
                <div class="quote-form">
                    <h3 class="form-title">Đăng Ký Nhận Báo Giá</h3>
                    <form action="<?= BASE_PATH ?>/contact.php" method="post" class="form-body">
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
                        <textarea name="note" placeholder="Ghi chú" class="form-textarea" rows="4"></textarea>
                        <button type="submit" class="btn-submit">ĐĂNG KÝ</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- ==================== TIN TỨC & SỰ KIỆN ==================== -->
    <section class="news-section">
        <div class="news-inner">
            <div class="news-title-wrap">
                <h2 class="news-title">Tin tức & Sự kiện</h2>
                <span class="underline"></span>
            </div>
            <div class="news-grid" id="newsGrid">
                <article class="news-card">
                    <a href="#" class="news-card-image">
                        <img src="<?= BASE_PATH ?>/assets/images/new1.png" alt="new 1">
                    </a>
                    <div class="news-card-body">
                        <span class="news-date">Aug 19, 2025</span>
                        <h3 class="news-card-title">
                            <a href="#">Housing Markets That Changed the Most This Week</a>
                        </h3>
                        <a href="#" class="news-read-more"> Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
                <article class="news-card">
                    <a href="#" class="news-card-image">
                        <img src="<?= BASE_PATH ?>/assets/images/new2.png" alt="new 1">
                    </a>
                    <div class="news-card-body">
                        <span class="news-date">Aug 19, 2025</span>
                        <h3 class="news-card-title">
                            <a href="#">Read Unveils the Best Canadian Cities for Biking</a>
                        </h3>
                        <a href="#" class="news-read-more"> Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
                <article class="news-card">
                    <a href="#" class="news-card-image">
                        <img src="<?= BASE_PATH ?>/assets/images/new3.png" alt="new 1">
                    </a>
                    <div class="news-card-body">
                        <span class="news-date">Aug 19, 2025</span>
                        <h3 class="news-card-title">
                            <a href="#">10 Walkable Cities Where You Can Live Affordably</a>
                        </h3>
                        <a href="#" class="news-read-more"> Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
                <article class="news-card">
                    <a href="#" class="news-card-image">
                        <img src="<?= BASE_PATH ?>/assets/images/new4.png" alt="new 1">
                    </a>
                    <div class="news-card-body">
                        <span class="news-date">Aug 19, 2025</span>
                        <h3 class="news-card-title">
                            <a href="#">New Apartment Nice in the Best Canadian Cities</a>
                        </h3>
                        <a href="#" class="news-read-more"> Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
                <article class="news-card news-card-extra"> <!--bị ẩn-->
                    <a href="#" class="news-card-image">
                        <img src="<?= BASE_PATH ?>/assets/images/new4.png" alt="new 1">
                    </a>
                    <div class="news-card-body">
                        <span class="news-date">Aug 19, 2025</span>
                        <h3 class="news-card-title">
                            <a href="#">New Apartment Nice in the Best Canadian Cities</a>
                        </h3>
                        <a href="#" class="news-read-more"> Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
                <article class="news-card news-card-extra"> 
                    <a href="#" class="news-card-image">
                        <img src="<?= BASE_PATH ?>/assets/images/new4.png" alt="new 1">
                    </a>
                    <div class="news-card-body">
                        <span class="news-date">Aug 19, 2025</span>
                        <h3 class="news-card-title">
                            <a href="#">New Apartment Nice in the Best Canadian Cities</a>
                        </h3>
                        <a href="#" class="news-read-more"> Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
                <article class="news-card news-card-extra"> 
                    <a href="#" class="news-card-image">
                        <img src="<?= BASE_PATH ?>/assets/images/new4.png" alt="new 1">
                    </a>
                    <div class="news-card-body">
                        <span class="news-date">Aug 19, 2025</span>
                        <h3 class="news-card-title">
                            <a href="#">New Apartment Nice in the Best Canadian Cities</a>
                        </h3>
                        <a href="#" class="news-read-more"> Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
                <article class="news-card news-card-extra"> 
                    <a href="#" class="news-card-image">
                        <img src="<?= BASE_PATH ?>/assets/images/new4.png" alt="new 1">
                    </a>
                    <div class="news-card-body">
                        <span class="news-date">Aug 19, 2025</span>
                        <h3 class="news-card-title">
                            <a href="#">New Apartment Nice in the Best Canadian Cities</a>
                        </h3>
                        <a href="#" class="news-read-more"> Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
            </div>
            <div class="news-footer">
                <a href="#" id="btnToggleNews" class="btn-view-all-news">XEM TẤT CẢ <i class="bi bi-chevron-right"></i></a>
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
<?php require '../layouts/footer.php'; ?>
