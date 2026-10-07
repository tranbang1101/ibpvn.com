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
VALUES ('may-loc-nuoc-ao-smith-ross-eco-aoc75pur', 'Máy lọc nước A. O. Smith ROSS™ ECO-AOC75PUR', 'Máy lọc nước', 'A. O. Smith', 14500000, 12150000, 'Công nghệ lọc RO Side Stream, thiết kế tinh gọn cho nguồn nước an tâm mỗi ngày.', '/ibpvn.com/assets/images/sanpham.png', 0, 0, 12, 0)
ON DUPLICATE KEY UPDATE
    id = LAST_INSERT_ID(id),
    ten = VALUES(ten),
    danh_muc = VALUES(danh_muc),
    thuong_hieu = VALUES(thuong_hieu),
    gia = VALUES(gia),
    gia_khuyen_mai = VALUES(gia_khuyen_mai),
    mo_ta_ngan = VALUES(mo_ta_ngan),
    anh_chinh = VALUES(anh_chinh);

INSERT INTO product (
    slug, ten, danh_muc, thuong_hieu, gia, gia_khuyen_mai, mo_ta_ngan,
    anh_chinh, rating, so_danh_gia, so_luong_ton, da_ban
)
VALUES
    (
        'may-lanh-inverter-1hp-tiet-kiem-dien',
        'Máy lạnh Inverter 1 HP tiết kiệm điện',
        'Máy lạnh',
        NULL,
        6990000,
        NULL,
        'Máy lạnh Inverter công suất 1 HP, phù hợp cho phòng nhỏ.',
        '/ibpvn.com/assets/images/maylanh.png',
        0,
        0,
        12,
        0
    ),
    (
        'may-lanh-inverter-1-5hp-lam-lanh-nhanh',
        'Máy lạnh Inverter 1.5 HP làm lạnh nhanh',
        'Máy lạnh',
        NULL,
        9490000,
        NULL,
        'Máy lạnh Inverter công suất 1.5 HP, làm lạnh nhanh cho phòng vừa.',
        '/ibpvn.com/assets/images/maylanh.png',
        0,
        0,
        12,
        0
    ),
    (
        'may-lanh-inverter-2hp-van-hanh-em',
        'Máy lạnh Inverter 2 HP vận hành êm',
        'Máy lạnh',
        NULL,
        11990000,
        NULL,
        'Máy lạnh Inverter công suất 2 HP, vận hành êm ái.',
        '/ibpvn.com/assets/images/maylanh.png',
        0,
        0,
        12,
        0
    ),
    (
        'may-lanh-tiet-kiem-dien-1hp',
        'Máy lạnh tiết kiệm điện 1 HP',
        'Máy lạnh',
        NULL,
        7490000,
        NULL,
        'Máy lạnh công suất 1 HP, thiết kế gọn cho không gian gia đình.',
        '/ibpvn.com/assets/images/maylanh.png',
        0,
        0,
        12,
        0
    ),
    (
        'may-lanh-lam-lanh-nhanh-1-5hp',
        'Máy lạnh làm lạnh nhanh 1.5 HP',
        'Máy lạnh',
        NULL,
        8990000,
        NULL,
        'Máy lạnh công suất 1.5 HP, phù hợp phòng có diện tích vừa.',
        '/ibpvn.com/assets/images/maylanh.png',
        0,
        0,
        12,
        0
    ),
    (
        'may-lanh-cong-suat-lon-2hp',
        'Máy lạnh công suất lớn 2 HP',
        'Máy lạnh',
        NULL,
        12990000,
        NULL,
        'Máy lạnh công suất 2 HP, phù hợp phòng rộng.',
        '/ibpvn.com/assets/images/maylanh.png',
        0,
        0,
        12,
        0
    )
ON DUPLICATE KEY UPDATE
    ten = VALUES(ten),
    danh_muc = VALUES(danh_muc),
    thuong_hieu = VALUES(thuong_hieu),
    gia = VALUES(gia),
    gia_khuyen_mai = VALUES(gia_khuyen_mai),
    mo_ta_ngan = VALUES(mo_ta_ngan),
    anh_chinh = VALUES(anh_chinh);

INSERT INTO product (slug, ten, danh_muc, thuong_hieu, gia, gia_khuyen_mai, mo_ta_ngan, anh_chinh, rating, so_danh_gia, so_luong_ton, da_ban)
VALUES ('may-loc-nuoc-ao-smith-a2', 'Máy Lọc Nước A. O. Smith A2', 'Máy lọc nước', 'AO Smith', 12600000, 9200000, 'Máy lọc nước A. O. Smith A2 với công nghệ lọc tiên tiến, mang đến nguồn nước tinh khiết cho gia đình.', '/ibpvn.com/assets/images/maylocnuoc-a.o.smith.png', 0, 0, 12, 0)
ON DUPLICATE KEY UPDATE
    id = LAST_INSERT_ID(id), ten = VALUES(ten), danh_muc = VALUES(danh_muc), thuong_hieu = VALUES(thuong_hieu),
    gia = VALUES(gia), gia_khuyen_mai = VALUES(gia_khuyen_mai), mo_ta_ngan = VALUES(mo_ta_ngan), anh_chinh = VALUES(anh_chinh);
SET @sample_product_id = LAST_INSERT_ID();

INSERT INTO productdetail (product_id, mo_ta_chi_tiet, thong_so_ky_thuat, bao_hanh, giao_hang_mien_phi, thong_tin_uu_dai)
VALUES (@sample_product_id, 'Máy lọc nước A. O. Smith A2 với thiết kế dành cho nhu cầu sử dụng trong gia đình. Vui lòng liên hệ IBP để được xác nhận thông số, bảo hành và lắp đặt theo khu vực.', NULL, NULL, 1, NULL)
ON DUPLICATE KEY UPDATE
    mo_ta_chi_tiet = VALUES(mo_ta_chi_tiet),
    thong_so_ky_thuat = VALUES(thong_so_ky_thuat),
    bao_hanh = VALUES(bao_hanh),
    giao_hang_mien_phi = VALUES(giao_hang_mien_phi),
    thong_tin_uu_dai = VALUES(thong_tin_uu_dai);

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

DELETE FROM product_variants
WHERE product_id = @sample_product_id AND sku IN ('A2-HS2', 'A2-KT3', 'A2-BK1');

DELETE FROM product_addons
WHERE product_id = @sample_product_id
  AND ten IN (
      'Lõi lọc Slim - tiện nghi an tâm mỗi ngày 1',
      'Lõi lọc Slim - tiện nghi an tâm mỗi ngày 2',
      'Lõi lọc Slim - tiện nghi an tâm mỗi ngày 3',
      'Lõi lọc Slim - tiện nghi an tâm mỗi ngày 4'
  );

DELETE FROM product_faq
WHERE product_id = @sample_product_id
  AND cau_hoi IN (
      'Sản phẩm được bảo hành trong bao lâu?',
      'IBP có hỗ trợ lắp đặt tại nhà không?',
      'Khi nào cần thay lõi lọc?',
      'Sản phẩm có giao hàng miễn phí không?',
      'Làm thế nào để được tư vấn chọn lõi lọc?'
  );

DELETE FROM danh_gia WHERE product_id = @sample_product_id AND user_id IS NULL AND ten_hien_thi IN ('Sơn Tùng', 'Nguyễn Quốc', 'Tiến Thịnh', 'Minh Anh', 'Thanh Hà', 'Quốc Bảo', 'Thu Trang', 'Hoàng Nam', 'Bảo Ngọc', 'Hữu Phước', 'Kim Oanh', 'Gia Huy', 'Tú Anh', 'Đình Khang', 'Ngọc Châu', 'Đức Anh', 'Phương Uyên', 'Hải Yến', 'Văn Long', 'Quỳnh Mai');

INSERT INTO product (
    slug, ten, danh_muc, thuong_hieu, gia, gia_khuyen_mai, mo_ta_ngan,
    anh_chinh, rating, so_danh_gia, so_luong_ton, da_ban
)
VALUES
    (
        'may-loc-nuoc-dau-nguon-3-cap-loc-thong-minh',
        'Máy lọc nước đầu nguồn 3 cấp lọc thông minh',
        'Máy lọc nước đầu nguồn',
        NULL,
        2500000,
        NULL,
        'Máy lọc nước đầu nguồn 3 cấp lọc thông minh.',
        '/ibpvn.com/assets/images/may-loc-nuoc-dau-nguon1.png',
        0,
        0,
        0,
        0
    ),
    (
        'may-loc-nuoc-dau-nguon-karofi-ktf-333i',
        'Máy lọc nước đầu nguồn Karofi KTF-333I',
        'Máy lọc nước đầu nguồn',
        'Karofi',
        58950000,
        NULL,
        'Máy lọc nước đầu nguồn Karofi KTF-333I.',
        '/ibpvn.com/assets/images/may-loc-nuoc-dau-nguon-ktf-333I-1.png',
        0,
        0,
        0,
        0
    )
ON DUPLICATE KEY UPDATE
    ten = VALUES(ten),
    danh_muc = VALUES(danh_muc),
    thuong_hieu = VALUES(thuong_hieu),
    gia = VALUES(gia),
    gia_khuyen_mai = VALUES(gia_khuyen_mai),
    mo_ta_ngan = VALUES(mo_ta_ngan),
    anh_chinh = VALUES(anh_chinh);
