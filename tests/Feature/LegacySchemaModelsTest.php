<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CustomerConsultation;
use App\Models\IncludedProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductDetail;
use App\Models\ProductFaq;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacySchemaModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_models_map_to_legacy_tables_and_relationships(): void
    {
        $this->createLegacySchema();

        $user = User::factory()->create();
        $product = Product::create([
            'slug' => 'filter-a2',
            'ten' => 'Water filter A2',
            'gia' => 1200000,
            'so_luong_ton' => 4,
        ]);

        ProductDetail::create([
            'product_id' => $product->id,
            'thong_so_ky_thuat' => ['origin' => 'US'],
            'phi_giao_hang' => 50000,
            'giao_hang_mien_phi' => true,
        ]);
        ProductImage::create(['product_id' => $product->id, 'image_url' => '/a2.png']);
        ProductVariant::create(['product_id' => $product->id, 'ten_phien_ban' => 'A2']);
        IncludedProduct::create(['product_id' => $product->id, 'ten' => 'Filter cartridge']);
        ProductAddon::create(['product_id' => $product->id, 'ten' => 'Replacement filter']);
        ProductFaq::create(['product_id' => $product->id, 'cau_hoi' => 'Question', 'cau_tra_loi' => 'Answer']);
        Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'ten_hien_thi' => $user->ho_ten,
            'so_sao' => 5,
        ]);
        CustomerConsultation::create([
            'product_id' => $product->id,
            'ho_ten' => $user->ho_ten,
            'so_dien_thoai' => '0900000000',
        ]);

        $cart = Cart::create(['user_id' => $user->id, 'session_key' => 'local-session']);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'so_luong' => 2,
            'don_gia' => 1200000,
        ]);

        $order = Order::create([
            'ma_don' => 'LOCAL-ORDER-1',
            'user_id' => $user->id,
            'ten_nguoi_nhan' => $user->ho_ten,
            'email' => $user->email,
            'so_dien_thoai' => '0900000000',
            'dia_chi' => 'Local test address',
            'tam_tinh' => 1200000,
            'tong_tien' => 1200000,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'ten_san_pham' => $product->ten,
            'so_luong' => 1,
            'don_gia' => 1200000,
            'thanh_tien' => 1200000,
        ]);

        $product = Product::query()
            ->with(['detail', 'images', 'variants', 'includedItems', 'addons', 'faqs', 'reviews', 'consultations'])
            ->findOrFail($product->id);

        $this->assertSame('productdetail', (new ProductDetail)->getTable());
        $this->assertSame('product_id', (new ProductDetail)->getKeyName());
        $this->assertSame('nguoidung', $user->getTable());
        $this->assertSame('mat_khau', $user->getAuthPasswordName());
        $this->assertTrue(Hash::check('password', $user->mat_khau));
        $this->assertTrue(Auth::validate(['email' => $user->email, 'password' => 'password']));
        $this->assertSame('US', $product->detail->thong_so_ky_thuat['origin']);
        $this->assertSame('50000.00', $product->detail->phi_giao_hang);
        $this->assertCount(1, $product->images);
        $this->assertCount(1, $product->variants);
        $this->assertCount(1, $product->includedItems);
        $this->assertCount(1, $product->addons);
        $this->assertCount(1, $product->faqs);
        $this->assertCount(1, $product->reviews);
        $this->assertCount(1, $product->consultations);
        $this->assertCount(1, $user->carts()->firstOrFail()->items);
        $this->assertSame($product->id, $user->orders()->firstOrFail()->items->firstOrFail()->product_id);
    }

    public function test_home_catalog_and_product_detail_render_with_the_legacy_schema(): void
    {
        $this->createLegacySchema();
        $product = Product::create([
            'slug' => 'filter-a2',
            'ten' => 'Water filter A2',
            'danh_muc' => 'Máy lọc nước',
            'thuong_hieu' => 'A. O. Smith',
            'gia' => 1200000,
            'gia_khuyen_mai' => 1100000,
            'mo_ta_ngan' => 'A sample water filter',
            'anh_chinh' => '/assets/images/sanpham.png',
            'rating' => 4.8,
            'so_danh_gia' => 5,
            'hien_thi' => true,
        ]);
        ProductDetail::create([
            'product_id' => $product->id,
            'mo_ta_chi_tiet' => 'Detailed product description',
            'thong_so_ky_thuat' => ['Xuất xứ' => 'Hoa Kỳ', 'Công suất' => '100 W'],
            'bao_hanh' => '12 tháng',
        ]);
        ProductImage::create(['product_id' => $product->id, 'image_url' => '/assets/images/sanpham.png']);
        ProductVariant::create(['product_id' => $product->id, 'ten_phien_ban' => 'A2', 'image_url' => '/assets/images/sanpham.png']);
        ProductAddon::create([
            'product_id' => $product->id,
            'ten' => 'Replacement filter',
            'image_url' => '/assets/images/loiloc.png',
            'gia_goc' => 300000,
            'gia_khuyen_mai' => 250000,
        ]);
        ProductFaq::create([
            'product_id' => $product->id,
            'cau_hoi' => 'How long is the warranty?',
            'cau_tra_loi' => '12 months',
        ]);
        Review::create([
            'product_id' => $product->id,
            'ten_hien_thi' => 'Test customer',
            'so_sao' => 5,
            'noi_dung' => 'Works well',
            'trang_thai' => true,
        ]);

        $this->get(route('home'))->assertOk()->assertSee('Danh mục nổi bật');
        $this->get(route('products.index', ['category' => 'may-loc-nuoc']))
            ->assertOk()
            ->assertSee('Water filter A2');
        $this->get(route('products.show', ['slug' => 'filter-a2']))
            ->assertOk()
            ->assertSee('Water filter A2')
            ->assertSee('Xem chi tiết thông số kỹ thuật')
            ->assertSee('Tặng thiết bị kiểm tra độ tinh khiết của nước TDS')
            ->assertSee('Replacement filter')
            ->assertSee('CÂU HỎI THƯỜNG GẶP')
            ->assertSee('How long is the warranty?')
            ->assertSee('ĐĂNG KÝ TƯ VẤN')
            ->assertSee('SẢN PHẨM ĐÃ XEM')
            ->assertSee(route('products.consultation', ['slug' => 'filter-a2']))
            ->assertSee('href="'.route('products.show', ['slug' => 'filter-a2']).'"', false);
        $this->assertSame(1, $product->fresh()->luot_xem);
    }

    public function test_product_detail_shows_category_faqs_when_no_product_faqs_exist(): void
    {
        $this->createLegacySchema();

        foreach (config('store.categories') as $category) {
            $product = Product::create([
                'slug' => 'faq-'.substr(sha1($category), 0, 10),
                'ten' => 'FAQ '.$category,
                'danh_muc' => $category,
                'hien_thi' => true,
            ]);

            $response = $this->get(route('products.show', ['slug' => $product->slug]))
                ->assertOk()
                ->assertSee('CÂU HỎI THƯỜNG GẶP');
            $this->assertSame(5, substr_count($response->getContent(), 'class="faq-item"'));
        }

        $products = collect([
            ['slug' => 'water-faq', 'ten' => 'Water filter', 'danh_muc' => 'Máy lọc nước'],
            ['slug' => 'air-faq', 'ten' => 'Air purifier', 'danh_muc' => 'Máy lọc không khí'],
            ['slug' => 'unknown-faq', 'ten' => 'Other product', 'danh_muc' => 'Khác'],
        ])->map(fn (array $attributes) => Product::create($attributes + ['hien_thi' => true]));

        $this->get(route('products.show', ['slug' => 'water-faq']))
            ->assertOk()
            ->assertSee('Bao lâu cần thay lõi lọc?')
            ->assertSee('Sản phẩm này phù hợp với nguồn nước');
        $this->get(route('products.show', ['slug' => 'air-faq']))
            ->assertOk()
            ->assertSee('Máy phù hợp với diện tích phòng bao nhiêu?')
            ->assertSee('vệ sinh hoặc thay bộ lọc');
        $this->get(route('products.show', ['slug' => 'unknown-faq']))
            ->assertOk()
            ->assertSee('Làm sao để chọn sản phẩm phù hợp?');

        $this->assertDatabaseCount('product_faq', 0);
    }

    public function test_recently_viewed_products_follow_visit_order_and_keep_the_latest_four(): void
    {
        $this->createLegacySchema();
        $products = collect(range(1, 5))->map(fn (int $number) => Product::create([
            'slug' => 'viewed-filter-'.$number,
            'ten' => 'Viewed filter '.$number,
            'gia' => $number * 1000,
            'hien_thi' => true,
        ]));

        foreach ($products as $product) {
            $this->get(route('products.show', ['slug' => $product->slug]))->assertOk();
        }

        $latestResponse = $this->get(route('products.show', ['slug' => 'viewed-filter-3']))
            ->assertOk()
            ->assertSee('SẢN PHẨM ĐÃ XEM');
        $content = $latestResponse->getContent();
        $sectionStart = strpos($content, '<section class="detail-card pd-viewed"');
        $sectionEnd = strpos($content, '</section>', $sectionStart);
        $recentSection = substr($content, $sectionStart, $sectionEnd - $sectionStart);
        $positions = array_map(
            static fn (int $number): int|false => strpos($recentSection, 'Viewed filter '.$number),
            [3, 5, 4, 2],
        );

        $this->assertSame([true, true, true, true], array_map(
            static fn (int|false $position): bool => $position !== false,
            $positions,
        ));
        $this->assertLessThan($positions[1], $positions[0]);
        $this->assertLessThan($positions[2], $positions[1]);
        $this->assertLessThan($positions[3], $positions[2]);
        $this->assertStringNotContainsString('Viewed filter 1', $recentSection);
    }

    public function test_product_consultation_is_saved_for_the_product_and_returns_to_its_detail(): void
    {
        $this->createLegacySchema();
        $product = Product::create([
            'slug' => 'consult-filter',
            'ten' => 'Consult filter',
            'hien_thi' => true,
        ]);

        $this->post(route('products.consultation', ['slug' => $product->slug]), [
            'name' => 'Test customer',
            'phone' => '0900000000',
            'email' => 'test@example.com',
            'message' => 'Please advise on this product',
        ])->assertRedirect(route('products.show', ['slug' => $product->slug]))
            ->assertSessionHas('contact_sent', true);

        $consultation = CustomerConsultation::query()->firstOrFail();
        $this->assertSame($product->id, $consultation->product_id);
        $this->assertSame('Test customer', $consultation->ho_ten);
        $this->assertStringContainsString('Sản phẩm quan tâm: Consult filter', $consultation->noi_dung);
        $this->assertStringContainsString('Please advise on this product', $consultation->noi_dung);
    }

    public function test_demo_product_consultation_never_stores_submitted_personal_data(): void
    {
        config()->set('store.demo_mode', true);
        $this->createLegacySchema();
        $product = Product::create([
            'slug' => 'demo-consult-filter',
            'ten' => 'Demo consult filter',
            'hien_thi' => true,
        ]);

        $this->post(route('products.consultation', ['slug' => $product->slug]), [
            'name' => 'Private demo customer',
            'phone' => '0900000000',
            'email' => 'private@example.com',
        ])->assertRedirect(route('products.show', ['slug' => $product->slug]))
            ->assertSessionHas('contact_demo_notice', true);

        $this->assertDatabaseCount('customer_consultations', 0);
    }

    public function test_product_detail_returns_not_found_for_hidden_or_missing_products(): void
    {
        $this->createLegacySchema();
        Product::create([
            'slug' => 'hidden-filter',
            'ten' => 'Hidden filter',
            'hien_thi' => false,
        ]);

        $this->get(route('products.show', ['slug' => 'missing-filter']))->assertNotFound();
        $this->get(route('products.show', ['slug' => 'hidden-filter']))->assertNotFound();
    }

    public function test_contact_requests_are_validated_and_saved_to_the_legacy_table(): void
    {
        $this->createLegacySchema();

        $this->get(route('contact'))->assertOk()->assertSee('Liên hệ và đăng ký tư vấn');
        $this->get(route('news.index'))->assertOk()->assertSee('Nội dung tin tức đang được cập nhật');
        $this->from(route('contact'))
            ->post(route('contact'), [
                'name' => 'Test customer',
                'phone' => 'not-a-phone',
            ])
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors('phone');

        $this->post(route('contact', ['topic' => 'warranty']), [
            'name' => 'Test customer',
            'phone' => '+84 983 537 155',
            'email' => 'test@example.com',
            'product' => 'may-loc-nuoc',
            'message' => 'Please call me',
        ])->assertRedirect(route('contact', ['topic' => 'warranty']))
            ->assertSessionHas('contact_sent', true);

        $consultation = CustomerConsultation::query()->firstOrFail();
        $this->assertSame('Test customer', $consultation->ho_ten);
        $this->assertSame('+84 983 537 155', $consultation->so_dien_thoai);
        $this->assertStringContainsString('Danh mục quan tâm: Máy lọc nước', $consultation->noi_dung);
        $this->assertStringContainsString('Chính sách bảo hành', $consultation->noi_dung);
        $this->assertStringContainsString('Please call me', $consultation->noi_dung);
    }

    public function test_login_and_registration_use_the_legacy_user_table(): void
    {
        $this->createLegacySchema();
        $user = User::create([
            'ho_ten' => 'Existing Customer',
            'email' => 'existing@example.com',
            'mat_khau' => 'existing-password',
            'trang_thai' => true,
        ]);

        $this->get(route('login'))->assertOk()->assertSee('Chào mừng trở lại');
        $this->post(route('login'), [
            'username' => $user->email,
            'password' => 'existing-password',
        ])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        Auth::logout();
        $this->post(route('register'), [
            'username' => 'New Customer',
            'email' => 'new@example.com',
            'phone' => '0900000000',
            'password' => 'new-password',
            'confirm_password' => 'new-password',
            'terms' => '1',
        ])->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('nguoidung', [
            'ho_ten' => 'New Customer',
            'email' => 'new@example.com',
        ]);
        $this->assertTrue(Hash::check('new-password', User::query()->where('email', 'new@example.com')->value('mat_khau')));
    }

    public function test_login_rejects_external_redirect_destinations(): void
    {
        $this->createLegacySchema();
        $user = User::factory()->create();

        $this->post(route('login'), [
            'username' => $user->email,
            'password' => 'password',
            'redirect' => 'https://example.com/account',
        ])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_cart_add_update_and_remove_validate_stock_and_preserve_options(): void
    {
        $this->createLegacySchema();
        $product = Product::create([
            'slug' => 'filter-a2',
            'ten' => 'Water filter A2',
            'gia' => 1200000,
            'gia_khuyen_mai' => 1000000,
            'so_luong_ton' => 5,
            'hien_thi' => true,
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'ten_phien_ban' => 'A2 Plus',
            'gia' => 1100000,
        ]);
        $addon = ProductAddon::create([
            'product_id' => $product->id,
            'ten' => 'Replacement filter',
            'gia_goc' => 300000,
            'gia_khuyen_mai' => 250000,
        ]);

        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
            'variant_id' => 1,
            'addon_ids' => [$addon->id],
            'selection_present' => '1',
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHas('cart', [$product->id => 2]);

        $this->get(route('cart.index'))->assertOk()->assertSee('Water filter A2')->assertSee('Replacement filter');
        $this->post(route('cart.update'), [
            'action' => 'update',
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHas('cart', [$product->id => 3]);

        $this->post(route('cart.update'), [
            'action' => 'update',
            'product_id' => $product->id,
            'quantity' => 6,
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHas('cart_error');

        $this->post(route('cart.update'), [
            'action' => 'remove',
            'product_id' => $product->id,
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHas('cart', []);
    }

    public function test_order_tracking_requires_matching_order_code_and_phone(): void
    {
        $this->createLegacySchema();
        $user = User::factory()->create();
        $product = Product::create([
            'slug' => 'filter-a2',
            'ten' => 'Water filter A2',
            'gia' => 1200000,
        ]);
        $order = Order::create([
            'ma_don' => 'LOCAL-ORDER-1',
            'user_id' => $user->id,
            'ten_nguoi_nhan' => 'Test customer',
            'email' => $user->email,
            'so_dien_thoai' => '0900000000',
            'dia_chi' => 'Local test address',
            'tam_tinh' => 1200000,
            'tong_tien' => 1200000,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'ten_san_pham' => $product->ten,
            'so_luong' => 1,
            'don_gia' => 1200000,
            'thanh_tien' => 1200000,
        ]);

        $this->get(route('orders.track'))->assertOk();
        $this->post(route('orders.track.lookup'), [
            'order_code' => 'local-order-1',
            'phone' => '0900000000',
        ])->assertRedirect(route('orders.track'))
            ->assertSessionHas('tracked_order');
        $this->get(route('orders.track'))->assertOk()->assertSee('LOCAL-ORDER-1')->assertSee('Water filter A2');

        $this->post(route('orders.track.lookup'), [
            'order_code' => 'LOCAL-ORDER-1',
            'phone' => '0900000001',
        ])->assertRedirect(route('orders.track'))
            ->assertSessionHas('tracking_error', 'Không tìm thấy đơn hàng với thông tin đã nhập.');
    }

    public function test_checkout_requires_login_and_creates_order_with_stock_updates(): void
    {
        $this->createLegacySchema();
        $user = User::factory()->create();
        $product = Product::create([
            'slug' => 'filter-a2',
            'ten' => 'Water filter A2',
            'gia' => 1200000,
            'gia_khuyen_mai' => 1000000,
            'so_luong_ton' => 5,
            'hien_thi' => true,
        ]);
        ProductDetail::create([
            'product_id' => $product->id,
            'phi_giao_hang' => 50000,
            'giao_hang_mien_phi' => false,
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'ten_phien_ban' => 'A2 Plus',
            'gia' => 1100000,
        ]);
        $addon = ProductAddon::create([
            'product_id' => $product->id,
            'ten' => 'Replacement filter',
            'gia_goc' => 300000,
            'gia_khuyen_mai' => 250000,
        ]);

        $this->get(route('cart.checkout'))
            ->assertRedirect(route('login', [
                'redirect' => route('cart.checkout', [], false),
                'msg' => 'checkout_required',
            ]));
        $this->get(route('login', [
            'redirect' => route('cart.checkout', [], false),
            'msg' => 'checkout_required',
        ]))->assertOk()->assertSee('tiếp tục thanh toán đơn hàng');
        $this->post(route('login'), [
            'username' => $user->email,
            'password' => 'password',
            'redirect' => route('cart.checkout', [], false),
        ])->assertRedirect(route('cart.checkout'));

        $this->actingAs($user)->withSession([
            'cart' => [$product->id => 2],
            'cart_options' => [$product->id => [
                'variant_id' => 1,
                'addon_ids' => [$addon->id],
            ]],
        ])->get(route('cart.checkout'))
            ->assertOk()
            ->assertSee('Thông tin hóa đơn')
            ->assertSee('Replacement filter');

        $this->post(route('cart.checkout.store'), [
            'name' => 'Test customer',
            'email' => 'test@example.com',
            'phone' => '0900000000',
            'address' => 'Test address',
            'note' => 'Call first',
        ])->assertRedirect(route('orders.success'))
            ->assertSessionHas('cart', [])
            ->assertSessionHas('cart_options', []);

        $order = Order::query()->firstOrFail();
        $this->assertSame('Test customer', $order->ten_nguoi_nhan);
        $this->assertSame('2750000.00', $order->tong_tien);
        $this->assertSame(3, $product->fresh()->so_luong_ton);
        $this->assertSame(2, $product->fresh()->da_ban);
        $this->assertDatabaseCount('don_hang_chi_tiet', 2);
        $this->get(route('orders.success'))->assertOk()->assertSee($order->ma_don);
        $this->get(route('orders.success'))->assertNotFound();
    }

    public function test_demo_checkout_is_guest_accessible_and_never_writes_orders_or_stock(): void
    {
        config()->set('store.demo_mode', true);
        $this->createLegacySchema();
        $product = Product::create([
            'slug' => 'demo-filter',
            'ten' => 'Demo filter',
            'gia' => 1200000,
            'so_luong_ton' => 5,
            'da_ban' => 2,
            'hien_thi' => true,
        ]);
        ProductDetail::create([
            'product_id' => $product->id,
            'giao_hang_mien_phi' => true,
        ]);

        $this->withSession(['cart' => [$product->id => 2]])
            ->get(route('cart.checkout'))
            ->assertOk()
            ->assertSee('không tạo đơn thật')
            ->assertDontSee('name="address"');

        $this->post(route('cart.checkout.store'), [
            'name' => 'Should not be stored',
            'email' => 'private@example.com',
            'phone' => '0900000000',
            'address' => 'Private address',
        ])->assertRedirect(route('orders.success'))
            ->assertSessionHas('demo_order', true)
            ->assertSessionHas('cart', []);

        $this->assertDatabaseCount('don_hang', 0);
        $this->assertDatabaseCount('don_hang_chi_tiet', 0);
        $this->assertSame(5, $product->fresh()->so_luong_ton);
        $this->assertSame(2, $product->fresh()->da_ban);
        $this->get(route('orders.success'))
            ->assertOk()
            ->assertSee('Đặt hàng thử nghiệm hoàn tất')
            ->assertSee('không có đơn hàng nào được lưu')
            ->assertDontSee('Private address')
            ->assertDontSee('private@example.com');
    }

    public function test_demo_mode_disables_account_and_order_lookup_routes(): void
    {
        config()->set('store.demo_mode', true);

        $this->get(route('login'))->assertNotFound();
        $this->post(route('login'), [])->assertNotFound();
        $this->get(route('register'))->assertNotFound();
        $this->post(route('register'), [])->assertNotFound();
        $this->get(route('orders.track'))->assertNotFound();
        $this->post(route('orders.track.lookup'), [])->assertNotFound();
    }

    public function test_demo_contact_form_does_not_validate_or_store_submitted_personal_data(): void
    {
        config()->set('store.demo_mode', true);
        $this->createLegacySchema();

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Biểu mẫu tư vấn đã tắt')
            ->assertDontSee('name="phone"');

        $this->post(route('contact'), [
            'name' => 'Should not be stored',
            'phone' => '0900000000',
            'email' => 'private@example.com',
            'message' => 'Private request',
        ])->assertRedirect(route('contact'))
            ->assertSessionHas('contact_demo_notice', true)
            ->assertSessionMissing('_old_input');

        $this->assertDatabaseCount('customer_consultations', 0);
    }

    public function test_demo_product_views_do_not_change_product_view_counts(): void
    {
        config()->set('store.demo_mode', true);
        $this->createLegacySchema();
        $product = Product::create([
            'slug' => 'demo-view-count',
            'ten' => 'Demo view count',
            'hien_thi' => true,
        ]);

        $this->get(route('products.show', ['slug' => $product->slug]))->assertOk();

        $this->assertSame(0, $product->fresh()->luot_xem);
    }

    private function createLegacySchema(): void
    {
        Schema::create('nguoidung', function (Blueprint $table): void {
            $table->id();
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('so_dien_thoai')->nullable();
            $table->string('mat_khau');
            $table->string('vai_tro')->default('customer');
            $table->boolean('trang_thai')->default(true);
            $table->timestamps();
        });

        Schema::create('product', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('ten');
            $table->string('danh_muc')->nullable();
            $table->string('thuong_hieu')->nullable();
            $table->decimal('gia', 13, 2)->default(0);
            $table->decimal('gia_khuyen_mai', 13, 2)->nullable();
            $table->text('mo_ta_ngan')->nullable();
            $table->string('anh_chinh')->nullable();
            $table->unsignedBigInteger('luot_xem')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->unsignedInteger('so_danh_gia')->default(0);
            $table->unsignedBigInteger('luot_yeu_thich')->default(0);
            $table->unsignedInteger('so_luong_ton')->default(0);
            $table->unsignedBigInteger('da_ban')->default(0);
            $table->boolean('hien_thi')->default(true);
            $table->timestamps();
        });

        Schema::create('productdetail', function (Blueprint $table): void {
            $table->unsignedBigInteger('product_id')->primary();
            $table->text('mo_ta_chi_tiet')->nullable();
            $table->json('thong_so_ky_thuat')->nullable();
            $table->string('bao_hanh')->nullable();
            $table->decimal('phi_giao_hang', 12, 2)->default(0);
            $table->boolean('giao_hang_mien_phi')->default(true);
            $table->text('thong_tin_uu_dai')->nullable();
        });

        Schema::create('product_images', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('image_url');
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('thu_tu')->default(0);
        });

        Schema::create('product_variants', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('ten_phien_ban');
            $table->string('image_url')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->decimal('gia', 13, 2)->nullable();
        });

        Schema::create('included', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('ten');
            $table->string('anh')->nullable();
            $table->decimal('gia', 13, 2)->default(0);
        });

        Schema::create('gio_hang', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_key')->nullable();
            $table->string('trang_thai')->default('active');
            $table->timestamps();
        });

        Schema::create('gio_hang_chi_tiet', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('so_luong')->default(1);
            $table->decimal('don_gia', 13, 2);
        });

        Schema::create('don_hang', function (Blueprint $table): void {
            $table->id();
            $table->string('ma_don')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ten_nguoi_nhan');
            $table->string('email');
            $table->string('so_dien_thoai');
            $table->text('dia_chi');
            $table->decimal('tam_tinh', 13, 2)->default(0);
            $table->decimal('phi_giao_hang', 12, 2)->default(0);
            $table->decimal('tong_tien', 13, 2)->default(0);
            $table->text('ghi_chu')->nullable();
            $table->string('trang_thai')->default('pending');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('don_hang_chi_tiet', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('ten_san_pham');
            $table->unsignedInteger('so_luong');
            $table->decimal('don_gia', 13, 2);
            $table->decimal('thanh_tien', 13, 2);
        });

        Schema::create('danh_gia', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ten_hien_thi');
            $table->unsignedTinyInteger('so_sao');
            $table->text('noi_dung')->nullable();
            $table->boolean('trang_thai')->default(false);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('product_addons', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('ten');
            $table->string('image_url')->nullable();
            $table->decimal('gia_goc', 13, 2)->default(0);
            $table->decimal('gia_khuyen_mai', 13, 2)->nullable();
            $table->unsignedInteger('thu_tu')->default(0);
            $table->boolean('hien_thi')->default(true);
        });

        Schema::create('product_faq', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('cau_hoi');
            $table->text('cau_tra_loi');
            $table->unsignedInteger('thu_tu')->default(0);
            $table->boolean('hien_thi')->default(true);
        });

        Schema::create('customer_consultations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('ho_ten');
            $table->string('so_dien_thoai');
            $table->string('email')->nullable();
            $table->text('noi_dung')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }
}
