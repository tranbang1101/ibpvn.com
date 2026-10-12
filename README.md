# IBP Technology

Website chạy bằng Laravel tại thư mục gốc dự án. Các trang PHP cũ đã được
thay bằng ứng dụng Laravel; ảnh và tài nguyên giao diện được giữ lại trong
`assets`.

## Dùng dữ liệu website hiện tại trên XAMPP

Ứng dụng dùng trực tiếp database MySQL `ibpvn` và các bảng/cột hiện có.
Mặc định `.env.example` phù hợp với XAMPP cài cục bộ (`127.0.0.1:3306`,
username `root`, mật khẩu trống). Nếu XAMPP của bạn dùng cấu hình khác, sửa
các biến `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` và `DB_PASSWORD`
trong `.env`.

Mở PowerShell tại thư mục gốc dự án:

```powershell
Copy-Item .env.example .env
php artisan key:generate
php artisan serve --host=127.0.0.1 --port=9000
```

Mở một trong hai địa chỉ `http://127.0.0.1:9000` hoặc
`http://localhost:9000`; cả hai dùng cùng ứng dụng Laravel. Để dùng URL
XAMPP cũ `http://localhost/ibpvn.com`, bật Apache, `mod_rewrite` và cho phép
`.htaccess` hoạt động. Không cần mở hai website hoặc chép dữ liệu sang một
database mới.

Ứng dụng không tự chạy migration khi khởi động. Không chạy
`php artisan migrate`, `migrate:fresh` hoặc import `database/ibpvn.sql` lên
database đang dùng cho website, vì database đó đã có dữ liệu.

## Hosting/VPS

Khi chuyển lên máy chủ sau này, cấu hình document root của website trỏ vào
thư mục `public`, cài dependencies bằng Composer và đặt cấu hình database
riêng trong file `.env` trên máy chủ. Không đưa `.env`, mật khẩu hoặc bản sao
database chứa thông tin cá nhân vào Git. Hãy sao lưu database và xác minh
phương án khôi phục trước khi dùng website trên production.

## Kiểm tra

```powershell
php artisan test
composer validate --strict
```
