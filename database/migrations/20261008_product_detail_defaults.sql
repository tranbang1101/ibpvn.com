-- Safe, repeatable defaults for existing product-detail pages.
USE ibpvn;

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

INSERT INTO product_variants (product_id, ten_phien_ban, image_url, sku, gia)
SELECT p.id,
       CONCAT('Phiên bản ', slots.slot),
       CONCAT('/ibpvn.com/assets/images/a.o.smith-mini', slots.slot, '.png'),
       CONCAT('DETAIL-', p.id, '-', slots.slot),
       COALESCE(p.gia_khuyen_mai, p.gia)
FROM product p
CROSS JOIN (
    SELECT 1 AS slot UNION ALL SELECT 2 UNION ALL SELECT 3
) AS slots
WHERE p.hien_thi = 1
  AND (SELECT COUNT(*) FROM product_variants v WHERE v.product_id = p.id) < 3
  AND NOT EXISTS (
      SELECT 1
      FROM product_variants v
      WHERE v.product_id = p.id
        AND v.sku = CONCAT('DETAIL-', p.id, '-', slots.slot)
  );

INSERT INTO product_addons (
    product_id, ten, image_url, gia_goc, gia_khuyen_mai, thu_tu, hien_thi
)
SELECT p.id,
       CONCAT('Phụ kiện đi kèm ', slots.slot),
       '/ibpvn.com/assets/images/loiloc.png',
       300000,
       300000,
       100 + slots.slot,
       1
FROM product p
CROSS JOIN (
    SELECT 1 AS slot UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
) AS slots
WHERE p.hien_thi = 1
  AND (SELECT COUNT(*) FROM product_addons a WHERE a.product_id = p.id AND a.hien_thi = 1) < 5
  AND NOT EXISTS (
      SELECT 1
      FROM product_addons a
      WHERE a.product_id = p.id
        AND a.ten = CONCAT('Phụ kiện đi kèm ', slots.slot)
  );
