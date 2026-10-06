-- IBP Technology | MySQL 8.0+ / MariaDB 10.4+
-- Import this file in phpMyAdmin. It creates the database and starter catalog.
CREATE DATABASE IF NOT EXISTS ibpvn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ibpvn;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS nguoidung (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    so_dien_thoai VARCHAR(25) NULL,
    mat_khau VARCHAR(255) NOT NULL,
    vai_tro ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    trang_thai TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(190) NOT NULL UNIQUE,
    ten VARCHAR(255) NOT NULL,
    danh_muc VARCHAR(120) NULL,
    thuong_hieu VARCHAR(120) NULL,
    gia DECIMAL(13,2) NOT NULL DEFAULT 0,
    gia_khuyen_mai DECIMAL(13,2) NULL,
    mo_ta_ngan TEXT NULL,
    anh_chinh VARCHAR(500) NULL,
    luot_xem BIGINT UNSIGNED NOT NULL DEFAULT 0,
    rating DECIMAL(2,1) NOT NULL DEFAULT 0,
    so_danh_gia INT UNSIGNED NOT NULL DEFAULT 0,
    luot_yeu_thich BIGINT UNSIGNED NOT NULL DEFAULT 0,
    so_luong_ton INT UNSIGNED NOT NULL DEFAULT 0,
    da_ban BIGINT UNSIGNED NOT NULL DEFAULT 0,
    hien_thi TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_product_category (danh_muc),
    INDEX idx_product_visible (hien_thi)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productdetail (
    product_id BIGINT UNSIGNED PRIMARY KEY,
    mo_ta_chi_tiet MEDIUMTEXT NULL,
    thong_so_ky_thuat JSON NULL,
    bao_hanh VARCHAR(160) NULL,
    phi_giao_hang DECIMAL(12,2) NOT NULL DEFAULT 0,
    giao_hang_mien_phi TINYINT(1) NOT NULL DEFAULT 1,
    thong_tin_uu_dai TEXT NULL,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255) NULL,
    thu_tu INT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE,
    INDEX idx_product_images (product_id, thu_tu)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_variants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    ten_phien_ban VARCHAR(150) NOT NULL,
    image_url VARCHAR(500) NULL,
    sku VARCHAR(100) NULL UNIQUE,
    gia DECIMAL(13,2) NULL,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS included (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    ten VARCHAR(255) NOT NULL,
    anh VARCHAR(500) NULL,
    gia DECIMAL(13,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS gio_hang (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    session_key VARCHAR(128) NULL,
    trang_thai ENUM('active','converted','abandoned') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES nguoidung(id) ON DELETE SET NULL,
    INDEX idx_cart_session (session_key, trang_thai)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS gio_hang_chi_tiet (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    so_luong INT UNSIGNED NOT NULL DEFAULT 1,
    don_gia DECIMAL(13,2) NOT NULL,
    FOREIGN KEY (cart_id) REFERENCES gio_hang(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(id),
    UNIQUE KEY uq_cart_product (cart_id, product_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS don_hang (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ma_don VARCHAR(32) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NULL,
    ten_nguoi_nhan VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    so_dien_thoai VARCHAR(25) NOT NULL,
    dia_chi TEXT NOT NULL,
    tam_tinh DECIMAL(13,2) NOT NULL DEFAULT 0,
    phi_giao_hang DECIMAL(12,2) NOT NULL DEFAULT 0,
    tong_tien DECIMAL(13,2) NOT NULL DEFAULT 0,
    ghi_chu TEXT NULL,
    trang_thai ENUM('pending','confirmed','shipping','completed','cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES nguoidung(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS don_hang_chi_tiet (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NULL,
    ten_san_pham VARCHAR(255) NOT NULL,
    so_luong INT UNSIGNED NOT NULL,
    don_gia DECIMAL(13,2) NOT NULL,
    thanh_tien DECIMAL(13,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES don_hang(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS danh_gia (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ten_hien_thi VARCHAR(150) NOT NULL,
    so_sao TINYINT UNSIGNED NOT NULL,
    noi_dung TEXT NULL,
    trang_thai TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES nguoidung(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO product (slug, ten, danh_muc, thuong_hieu, gia, gia_khuyen_mai, mo_ta_ngan, anh_chinh, rating, so_danh_gia, so_luong_ton, da_ban)
VALUES ('may-loc-nuoc-ao-smith-ross-eco-aoc75pur', 'Máy lọc nước A. O. Smith ROSS™ ECO-AOC75PUR', 'Máy lọc nước', 'A. O. Smith', 14500000, 12150000, 'Công nghệ lọc RO Side Stream, thiết kế tinh gọn cho nguồn nước an tâm mỗi ngày.', '/ibpvn.com/assets/images/sanpham.png', 4.8, 28, 12, 128)
ON DUPLICATE KEY UPDATE
    id = LAST_INSERT_ID(id),
    ten = VALUES(ten),
    danh_muc = VALUES(danh_muc),
    thuong_hieu = VALUES(thuong_hieu),
    gia = VALUES(gia),
    gia_khuyen_mai = VALUES(gia_khuyen_mai),
    mo_ta_ngan = VALUES(mo_ta_ngan),
    anh_chinh = VALUES(anh_chinh),
    rating = VALUES(rating),
    so_danh_gia = VALUES(so_danh_gia),
    so_luong_ton = VALUES(so_luong_ton),
    da_ban = VALUES(da_ban);

SET @sample_product_id = LAST_INSERT_ID();

INSERT INTO product (slug, ten, danh_muc, thuong_hieu, gia, gia_khuyen_mai, mo_ta_ngan, anh_chinh, rating, so_danh_gia, so_luong_ton, da_ban)
VALUES ('may-loc-nuoc-ao-smith-a2', 'Máy Lọc Nước A. O. Smith A2', 'Máy lọc nước', 'AO Smith', 12600000, 9200000, 'Máy lọc nước A. O. Smith A2 với công nghệ lọc tiên tiến, mang đến nguồn nước tinh khiết cho gia đình.', '/ibpvn.com/assets/images/maylocnuoc-a.o.smith.png', 4.9, 20, 12, 231)
ON DUPLICATE KEY UPDATE
    id = LAST_INSERT_ID(id), ten = VALUES(ten), danh_muc = VALUES(danh_muc), thuong_hieu = VALUES(thuong_hieu),
    gia = VALUES(gia), gia_khuyen_mai = VALUES(gia_khuyen_mai), mo_ta_ngan = VALUES(mo_ta_ngan), anh_chinh = VALUES(anh_chinh),
    rating = VALUES(rating), so_danh_gia = VALUES(so_danh_gia), so_luong_ton = VALUES(so_luong_ton), da_ban = VALUES(da_ban);
SET @sample_product_id = LAST_INSERT_ID();

INSERT INTO productdetail (product_id, mo_ta_chi_tiet, thong_so_ky_thuat, bao_hanh, giao_hang_mien_phi, thong_tin_uu_dai)
VALUES (@sample_product_id, 'Máy lọc nước A. O. Smith A2 kết hợp công nghệ lọc hiện đại và thiết kế gọn đẹp. Sản phẩm hỗ trợ nguồn nước sạch cho sinh hoạt hằng ngày.', '{"Mã sản phẩm":"TRIM ION US-100L","Xuất xứ":"Mỹ","Số cấp lọc":"7 cấp lọc","Chức năng":"Nước thường","Điện áp đầu vào":"AC 220V / 50Hz","Công suất (tổng)":"85 W","Áp suất nước đầu vào phù hợp":"0.1MPa ~ 0.35MPa","Nhiệt độ nước cấp":"5~38°C","Công suất lọc/phút":"1.1 L/phút","Phương pháp lọc rửa":"Tự động làm sạch"}', '24 tháng', 1, 'Lắp thêm lõi lọc nước ion kiềm alkaline hydrogen nhập khẩu Hàn Quốc chỉ 500.000đ; tặng thiết bị kiểm tra TDS và hỗ trợ lắp đặt.')
ON DUPLICATE KEY UPDATE mo_ta_chi_tiet = VALUES(mo_ta_chi_tiet), thong_so_ky_thuat = VALUES(thong_so_ky_thuat), bao_hanh = VALUES(bao_hanh), giao_hang_mien_phi = VALUES(giao_hang_mien_phi), thong_tin_uu_dai = VALUES(thong_tin_uu_dai);

-- Data supplement for the A. O. Smith A2 product detail page.
CREATE TABLE IF NOT EXISTS product_addons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    ten VARCHAR(180) NOT NULL,
    image_url VARCHAR(500) NULL,
    gia_goc DECIMAL(13,2) NOT NULL DEFAULT 0,
    gia_khuyen_mai DECIMAL(13,2) NULL,
    thu_tu INT UNSIGNED NOT NULL DEFAULT 0,
    hien_thi TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_product_addon (product_id, ten),
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_faq (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    cau_hoi VARCHAR(180) NOT NULL,
    cau_tra_loi TEXT NOT NULL,
    thu_tu INT UNSIGNED NOT NULL DEFAULT 0,
    hien_thi TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_product_faq (product_id, cau_hoi),
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customer_consultations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NULL,
    ho_ten VARCHAR(150) NOT NULL,
    so_dien_thoai VARCHAR(25) NOT NULL,
    email VARCHAR(190) NULL,
    noi_dung TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE SET NULL
) ENGINE=InnoDB;

DELETE FROM product_images
WHERE product_id = @sample_product_id
  AND image_url IN ('/ibpvn.com/assets/images/maylocnuoc-a.o.smith.png', '/ibpvn.com/assets/images/a.o.smith-mini1.png', '/ibpvn.com/assets/images/a.o.smith-mini2.png', '/ibpvn.com/assets/images/a.o.smith-mini3.png');
INSERT INTO product_images (product_id, image_url, alt_text, thu_tu) VALUES
(@sample_product_id, '/ibpvn.com/assets/images/maylocnuoc-a.o.smith.png', 'Máy lọc nước A. O. Smith A2', 0),
(@sample_product_id, '/ibpvn.com/assets/images/a.o.smith-mini1.png', 'Góc nghiêng máy lọc nước A. O. Smith A2', 1),
(@sample_product_id, '/ibpvn.com/assets/images/a.o.smith-mini2.png', 'Chi tiết máy lọc nước A. O. Smith A2', 2),
(@sample_product_id, '/ibpvn.com/assets/images/a.o.smith-mini3.png', 'Mặt trước máy lọc nước A. O. Smith A2', 3);

INSERT INTO product_variants (product_id, ten_phien_ban, image_url, sku, gia)
VALUES
(@sample_product_id, 'Denon HEOS 5 HS2', '/ibpvn.com/assets/images/a.o.smith-mini1.png', 'A2-HS2', NULL),
(@sample_product_id, 'Denon HEOS 6 KT3', '/ibpvn.com/assets/images/a.o.smith-mini2.png', 'A2-KT3', NULL),
(@sample_product_id, 'Denon HEOS 7 BK1', '/ibpvn.com/assets/images/a.o.smith-mini3.png', 'A2-BK1', NULL)
ON DUPLICATE KEY UPDATE product_id = VALUES(product_id), ten_phien_ban = VALUES(ten_phien_ban), image_url = VALUES(image_url), gia = VALUES(gia);

INSERT INTO product_addons (product_id, ten, image_url, gia_goc, gia_khuyen_mai, thu_tu)
VALUES
(@sample_product_id, 'Lõi lọc Slim - tiện nghi an tâm mỗi ngày 1', '/ibpvn.com/assets/images/loiloc.png', 350000, 300000, 1),
(@sample_product_id, 'Lõi lọc Slim - tiện nghi an tâm mỗi ngày 2', '/ibpvn.com/assets/images/loiloc.png', 350000, 300000, 2),
(@sample_product_id, 'Lõi lọc Slim - tiện nghi an tâm mỗi ngày 3', '/ibpvn.com/assets/images/loiloc.png', 350000, 300000, 3),
(@sample_product_id, 'Lõi lọc Slim - tiện nghi an tâm mỗi ngày 4', '/ibpvn.com/assets/images/loiloc.png', 350000, 300000, 4)
ON DUPLICATE KEY UPDATE image_url = VALUES(image_url), gia_goc = VALUES(gia_goc), gia_khuyen_mai = VALUES(gia_khuyen_mai), thu_tu = VALUES(thu_tu), hien_thi = 1;

INSERT INTO product_faq (product_id, cau_hoi, cau_tra_loi, thu_tu)
VALUES
(@sample_product_id, 'Sản phẩm được bảo hành trong bao lâu?', 'Máy lọc nước A. O. Smith A2 được bảo hành chính hãng 24 tháng theo điều kiện của nhà sản xuất.', 1),
(@sample_product_id, 'IBP có hỗ trợ lắp đặt tại nhà không?', 'Có. Kỹ thuật viên sẽ liên hệ xác nhận địa chỉ và thời gian lắp đặt thuận tiện cho bạn.', 2),
(@sample_product_id, 'Khi nào cần thay lõi lọc?', 'Chu kỳ thay lõi phụ thuộc vào chất lượng nguồn nước và lượng nước sử dụng. IBP hỗ trợ kiểm tra và nhắc lịch thay lõi.', 3),
(@sample_product_id, 'Sản phẩm có giao hàng miễn phí không?', 'IBP giao hàng miễn phí theo chương trình áp dụng và sẽ xác nhận phí phát sinh (nếu có) trước khi giao.', 4),
(@sample_product_id, 'Làm thế nào để được tư vấn chọn lõi lọc?', 'Gọi 0983 537 155 hoặc gửi câu hỏi qua Zalo để đội ngũ IBP tư vấn theo nhu cầu sử dụng.', 5)
ON DUPLICATE KEY UPDATE cau_tra_loi = VALUES(cau_tra_loi), thu_tu = VALUES(thu_tu), hien_thi = 1;

DELETE FROM danh_gia WHERE product_id = @sample_product_id AND user_id IS NULL AND ten_hien_thi IN ('Sơn Tùng', 'Nguyễn Quốc', 'Tiến Thịnh', 'Minh Anh', 'Thanh Hà', 'Quốc Bảo', 'Thu Trang', 'Hoàng Nam', 'Bảo Ngọc', 'Hữu Phước', 'Kim Oanh', 'Gia Huy', 'Tú Anh', 'Đình Khang', 'Ngọc Châu', 'Đức Anh', 'Phương Uyên', 'Hải Yến', 'Văn Long', 'Quỳnh Mai');
INSERT INTO danh_gia (product_id, user_id, ten_hien_thi, so_sao, noi_dung, trang_thai) VALUES
(@sample_product_id, NULL, 'Sơn Tùng', 5, 'Dịch vụ mua hàng nhanh chóng và hỗ trợ tốt.', 1),
(@sample_product_id, NULL, 'Nguyễn Quốc', 5, 'Máy chạy ổn định, nhân viên lắp đặt rất chu đáo.', 1),
(@sample_product_id, NULL, 'Tiến Thịnh', 5, 'Nguồn nước có vị dễ uống, tư vấn sau mua rất nhanh.', 1),
(@sample_product_id, NULL, 'Minh Anh', 5, 'Thiết kế gọn, hoạt động êm và dễ sử dụng.', 1),
(@sample_product_id, NULL, 'Thanh Hà', 5, 'Giao hàng đúng hẹn, kỹ thuật viên hướng dẫn rõ ràng.', 1),
(@sample_product_id, NULL, 'Quốc Bảo', 5, 'Sản phẩm chính hãng, đóng gói cẩn thận.', 1),
(@sample_product_id, NULL, 'Thu Trang', 5, 'Đội ngũ tư vấn nhiệt tình, hỗ trợ chọn máy phù hợp.', 1),
(@sample_product_id, NULL, 'Hoàng Nam', 5, 'Máy lọc nhanh, nước trong và không có mùi lạ.', 1),
(@sample_product_id, NULL, 'Bảo Ngọc', 5, 'Lắp đặt gọn gàng, nhân viên thân thiện.', 1),
(@sample_product_id, NULL, 'Hữu Phước', 5, 'Đã sử dụng một thời gian và rất hài lòng.', 1),
(@sample_product_id, NULL, 'Kim Oanh', 5, 'Mua hàng thuận tiện, được hướng dẫn bảo dưỡng đầy đủ.', 1),
(@sample_product_id, NULL, 'Gia Huy', 5, 'Máy đẹp, chạy êm, giao hàng nhanh.', 1),
(@sample_product_id, NULL, 'Tú Anh', 5, 'Nhân viên lắp đặt cẩn thận, hướng dẫn sử dụng dễ hiểu.', 1),
(@sample_product_id, NULL, 'Đình Khang', 5, 'Máy hoạt động tốt, tư vấn đúng nhu cầu gia đình.', 1),
(@sample_product_id, NULL, 'Ngọc Châu', 5, 'Nước uống ngon, giao hàng nhanh và đúng lịch hẹn.', 1),
(@sample_product_id, NULL, 'Đức Anh', 5, 'Thiết kế đẹp, dùng thuận tiện mỗi ngày.', 1),
(@sample_product_id, NULL, 'Phương Uyên', 5, 'Chất lượng tốt, đội ngũ hỗ trợ nhiệt tình.', 1),
(@sample_product_id, NULL, 'Hải Yến', 5, 'Lắp đặt nhanh, khu vực sử dụng được vệ sinh gọn gàng.', 1),
(@sample_product_id, NULL, 'Văn Long', 5, 'Máy chạy êm và dịch vụ sau mua chu đáo.', 1),
(@sample_product_id, NULL, 'Quỳnh Mai', 5, 'Tư vấn rõ ràng, hỗ trợ bảo hành nhanh.', 1);
