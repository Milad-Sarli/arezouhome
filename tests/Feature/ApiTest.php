<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Comment;
use App\Models\DiscountCode;
use App\Models\InstallmentPlan;
use App\Models\Order;
use App\Models\Product;
use App\Models\Province;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private function seedCatalog(): Product
    {
        $brand = Brand::create(['name' => 'تست', 'slug' => 'test']);
        $category = Category::create(['title' => 'دسته تست', 'slug' => 'test-cat']);

        $product = Product::create([
            'name' => 'یخچال تست',
            'slug' => 'test-fridge',
            'brand_id' => $brand->id,
            'price_rial' => 1000000,
            'discount_rial' => 100000,
            'quantity' => 5,
            'status' => true,
        ]);
        $product->categories()->attach($category->id);

        Shipment::create(['title' => 'پیشتاز', 'is_default' => true]);

        return $product;
    }

    public function test_public_catalog_endpoints(): void
    {
        $this->seed(CategorySeeder::class);

        $this->getJson('/api/v1/categories')->assertOk()->assertJsonCount(8, 'data');
        $this->getJson('/api/v1/settings')->assertOk()->assertJsonPath('store_name', '');
    }

    public function test_provinces_with_cities(): void
    {
        $province = Province::create(['name' => 'تهران', 'status' => true]);
        City::create(['province_id' => $province->id, 'name' => 'تهران', 'status' => true]);
        City::create(['province_id' => $province->id, 'name' => 'غیرفعال', 'status' => false]);
        Province::create(['name' => 'مخفی', 'status' => false]);

        $this->getJson('/api/v1/provinces')->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'تهران')
            ->assertJsonCount(1, '0.cities')
            ->assertJsonPath('0.cities.0.name', 'تهران');
    }

    public function test_blogs_list_and_show(): void
    {
        $cat = BlogCategory::create(['title' => 'راهنما', 'slug' => 'guide', 'status' => true]);
        Blog::create([
            'title' => 'راهنمای خرید یخچال', 'slug' => 'fridge-guide',
            'blog_category_id' => $cat->id, 'description' => 'متن نمونه',
            'publish_at' => now(), 'status' => true,
        ]);
        Blog::create(['title' => 'پیش‌نویس', 'slug' => 'draft', 'status' => false]);

        $this->getJson('/api/v1/blogs')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/blogs/fridge-guide')->assertOk()
            ->assertJsonPath('category', 'راهنما');
        $this->getJson('/api/v1/blogs/draft')->assertNotFound();
    }

    public function test_shipments_list(): void
    {
        Shipment::create(['title' => 'پیشتاز', 'default_price_rial' => 500000, 'is_default' => true]);
        Shipment::create(['title' => 'غیرفعال', 'status' => false]);

        $this->getJson('/api/v1/shipments')->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.title', 'پیشتاز')
            ->assertJsonPath('0.default_price_rial', 500000);
    }

    public function test_installments_list(): void
    {
        InstallmentPlan::create([
            'title' => '۱۰ ماهه', 'down_payment_percent' => 30,
            'months' => 10, 'fee_percent' => 5, 'status' => true,
        ]);
        InstallmentPlan::create(['title' => 'غیرفعال', 'months' => 6, 'status' => false]);

        $this->getJson('/api/v1/installments')->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.months', 10);
    }

    public function test_tickets_flow(): void
    {
        $user = User::factory()->create(['phone' => '09120000000']);
        $token = $user->createToken('test')->plainTextToken;

        $this->postJson('/api/v1/tickets', ['subject' => 'سوال', 'message' => 'سلام'])->assertUnauthorized();

        $this->withToken($token)->postJson('/api/v1/tickets', ['subject' => '', 'message' => 'x'])->assertStatus(422);

        $res = $this->withToken($token)->postJson('/api/v1/tickets', ['subject' => 'سوال خرید', 'message' => 'آیا موجوده؟'])->assertCreated();
        $id = $res->json('id');

        $this->withToken($token)->getJson('/api/v1/tickets')->assertOk()->assertJsonCount(1, 'data');
        $this->withToken($token)->getJson("/api/v1/tickets/{$id}")->assertOk()->assertJsonPath('subject', 'سوال خرید');

        $this->withToken($token)->postJson("/api/v1/tickets/{$id}/reply", ['message' => 'پیگیری'])->assertOk();
    }

    public function test_tickets_cross_user_blocked(): void
    {
        $owner = User::factory()->create(['phone' => '09120000000']);
        $ticket = $owner->tickets()->create(['subject' => 'سوال', 'status' => 'open']);
        $ticket->messages()->create(['user_id' => $owner->id, 'message' => 'سلام']);
        $id = $ticket->id;

        $stranger = User::factory()->create(['phone' => '09129999999']);
        $st = $stranger->createToken('x')->plainTextToken;
        $this->withToken($st)->getJson("/api/v1/tickets/{$id}")->assertNotFound();
        $this->withToken($st)->postJson("/api/v1/tickets/{$id}/reply", ['message' => 'hack'])->assertNotFound();
    }

    public function test_user_invoice_access(): void
    {
        $user = User::factory()->create(['phone' => '09120000000']);
        $token = $user->createToken('test')->plainTextToken;
        $product = $this->seedCatalog();
        $order = $user->orders()->create(['ref_code' => 'AH-20990101-0001', 'status' => 'paid', 'final_price_rial' => 1000000]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1, 'unit_price_rial' => 1000000, 'total_rial' => 1000000]);

        $this->getJson('/api/v1/orders/AH-20990101-0001/invoice')->assertUnauthorized();
        $this->withToken($token)->get('/api/v1/orders/AH-20990101-0001/invoice')->assertOk()->assertSee('فاکتور');
    }

    public function test_wishlist_cloud_flow(): void
    {
        $user = User::factory()->create(['phone' => '09120000000']);
        $token = $user->createToken('test')->plainTextToken;
        $product = $this->seedCatalog();

        $this->getJson('/api/v1/wishlist')->assertUnauthorized();
        $this->withToken($token)->getJson('/api/v1/wishlist')->assertOk()->assertJsonPath('ids', []);

        $this->withToken($token)->postJson('/api/v1/wishlist/toggle', ['product_id' => $product->id])->assertCreated()->assertJsonPath('added', true);
        $this->withToken($token)->getJson('/api/v1/wishlist')->assertOk()->assertJsonPath('ids.0', $product->id);

        $this->withToken($token)->postJson('/api/v1/wishlist/toggle', ['product_id' => $product->id])->assertOk()->assertJsonPath('added', false);
        $this->withToken($token)->getJson('/api/v1/wishlist')->assertOk()->assertJsonPath('ids', []);

        // sync
        $p2 = Product::create(['name' => 'دوم', 'slug' => 'second', 'brand_id' => $product->brand_id, 'price_rial' => 1000000, 'quantity' => 5, 'status' => true]);
        $this->withToken($token)->postJson('/api/v1/wishlist/sync', ['ids' => [$product->id, $p2->id]])->assertOk()->assertJsonCount(2, 'ids');
        $this->withToken($token)->postJson('/api/v1/wishlist/sync', ['ids' => [$product->id]])->assertOk()->assertJsonCount(2, 'ids');
    }

    public function test_abandoned_cart_sync_and_clear(): void
    {
        $user = User::factory()->create(['phone' => '09120000000']);
        $token = $user->createToken('test')->plainTextToken;
        $product = $this->seedCatalog();

        $this->postJson('/api/v1/cart/sync', ['items' => [['product_id' => $product->id, 'quantity' => 2]]])->assertUnauthorized();
        $this->withToken($token)->postJson('/api/v1/cart/sync', ['items' => [['product_id' => $product->id, 'quantity' => 2]]])->assertOk();
        $this->assertDatabaseHas('abandoned_carts', ['user_id' => $user->id]);

        $this->withToken($token)->deleteJson('/api/v1/cart')->assertOk();
        $this->assertDatabaseMissing('abandoned_carts', ['user_id' => $user->id]);
    }

    public function test_stock_alert_flow(): void
    {
        $user = User::factory()->create(['phone' => '09120000000']);
        $token = $user->createToken('test')->plainTextToken;
        $product = $this->seedCatalog();
        $product->update(['quantity' => 0, 'in_stock' => false]);

        $this->postJson('/api/v1/stock-alerts', ['product_id' => $product->id])->assertUnauthorized();
        $this->withToken($token)->postJson('/api/v1/stock-alerts', ['product_id' => $product->id])->assertCreated();
        $this->withToken($token)->postJson('/api/v1/stock-alerts', ['product_id' => $product->id])->assertCreated();
        $this->assertDatabaseCount('stock_alerts', 1);

        $product->update(['quantity' => 5, 'in_stock' => true]);
        $this->withToken($token)->postJson('/api/v1/stock-alerts', ['product_id' => $product->id])->assertStatus(422);
    }

    public function test_cancel_pending_order(): void
    {
        $user = User::factory()->create(['phone' => '09120000000']);
        $token = $user->createToken('test')->plainTextToken;
        $product = $this->seedCatalog();
        $order = $user->orders()->create(['ref_code' => 'AH-20990101-0001', 'status' => 'pending', 'final_price_rial' => 1000000]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1, 'unit_price_rial' => 1000000, 'total_rial' => 1000000]);

        $this->patchJson('/api/v1/orders/AH-20990101-0001/cancel')->assertUnauthorized();
        $this->withToken($token)->patchJson('/api/v1/orders/AH-20990101-0001/cancel')->assertOk()->assertJsonPath('data.status', 'canceled');
        $this->withToken($token)->patchJson('/api/v1/orders/AH-20990101-0001/cancel')->assertStatus(422);

        $paid = $user->orders()->create(['ref_code' => 'AH-20990101-0002', 'status' => 'paid', 'final_price_rial' => 1000000]);
        $this->withToken($token)->patchJson('/api/v1/orders/AH-20990101-0002/cancel')->assertStatus(422);
    }

    public function test_user_invoice_cross_user_blocked(): void
    {
        $owner = User::factory()->create(['phone' => '09120000000']);
        $product = $this->seedCatalog();
        $order = $owner->orders()->create(['ref_code' => 'AH-20990101-0001', 'status' => 'paid', 'final_price_rial' => 1000000]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1, 'unit_price_rial' => 1000000, 'total_rial' => 1000000]);

        $other = User::factory()->create(['phone' => '09129999999']);
        $ot = $other->createToken('x')->plainTextToken;
        $this->withToken($ot)->get('/api/v1/orders/AH-20990101-0001/invoice')->assertNotFound();
    }

    public function test_returns_flow(): void
    {
        $user = User::factory()->create(['phone' => '09120000000']);
        $token = $user->createToken('test')->plainTextToken;
        $product = $this->seedCatalog();
        $order = $user->orders()->create(['ref_code' => 'AH-20990101-0001', 'status' => 'delivered', 'final_price_rial' => 1000000]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1, 'unit_price_rial' => 1000000, 'total_rial' => 1000000]);

        $this->postJson('/api/v1/returns', ['order_id' => $order->id, 'reason' => 'خراب'])->assertUnauthorized();

        $this->withToken($token)->postJson('/api/v1/returns', ['order_id' => 999999, 'reason' => 'x'])->assertStatus(422);

        $this->withToken($token)->postJson('/api/v1/returns', ['order_id' => $order->id, 'reason' => 'آسیب دیده'])->assertCreated();

        // pending order cannot be returned
        $pending = $user->orders()->create(['ref_code' => 'AH-20990101-0002', 'status' => 'pending', 'final_price_rial' => 1000000]);
        $this->withToken($token)->postJson('/api/v1/returns', ['order_id' => $pending->id, 'reason' => 'x'])->assertStatus(422);

        $this->withToken($token)->getJson('/api/v1/returns')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_profile_update_and_address_update(): void
    {
        $user = User::factory()->create(['phone' => '09120000000']);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->patchJson('/api/v1/me', ['name' => 'آرزو'])
            ->assertOk()->assertJsonPath('name', 'آرزو');
        $this->withToken($token)->patchJson('/api/v1/me', ['name' => ''])->assertStatus(422);

        $province = Province::create(['name' => 'تهران']);
        $city = City::create(['province_id' => $province->id, 'name' => 'تهران']);
        $other = City::create(['province_id' => $province->id, 'name' => 'دماوند']);
        $address = $user->addresses()->create([
            'province_id' => $province->id, 'city_id' => $city->id,
            'address' => 'قدیم', 'receiver_name' => 'تست', 'receiver_mobile' => '09120000000',
        ]);

        $this->withToken($token)->patchJson("/api/v1/addresses/{$address->id}", [
            'address' => 'جدید', 'is_default' => true,
        ])->assertOk();
        $this->assertSame('جدید', $address->fresh()->address);
        $this->assertTrue((bool) $address->fresh()->is_default);

        // mismatched city rejected
        $far = Province::create(['name' => 'مشهد']);
        $this->withToken($token)->patchJson("/api/v1/addresses/{$address->id}", [
            'province_id' => $far->id,
        ])->assertStatus(422);
    }

    public function test_address_cross_user_blocked(): void
    {
        $owner = User::factory()->create(['phone' => '09120000000']);
        $province = Province::create(['name' => 'تهران']);
        $city = City::create(['province_id' => $province->id, 'name' => 'تهران']);
        $address = $owner->addresses()->create([
            'province_id' => $province->id, 'city_id' => $city->id,
            'address' => 'خصوصی', 'receiver_name' => 'تست', 'receiver_mobile' => '09120000000',
        ]);

        $stranger = User::factory()->create(['phone' => '09129999999']);
        $st = $stranger->createToken('x')->plainTextToken;

        $this->withToken($st)->patchJson("/api/v1/addresses/{$address->id}", ['address' => 'hack'])
            ->assertNotFound();
        $this->withToken($st)->deleteJson("/api/v1/addresses/{$address->id}")
            ->assertNotFound();
        $this->assertSame('خصوصی', $address->fresh()->address);
    }

    public function test_guest_order_tracking(): void
    {
        $product = $this->seedCatalog();
        $user = User::factory()->create(['phone' => '09120000000']);
        $order = Order::create([
            'user_id' => $user->id,
            'ref_code' => 'AH-20990101-0001',
            'status' => 'shipped',
            'final_price_rial' => 900000,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price_rial' => 900000,
            'total_rial' => 900000,
        ]);

        // public, no token
        $this->getJson('/api/v1/track/AH-20990101-0001')->assertOk()
            ->assertJsonPath('data.status', 'shipped')
            ->assertJsonPath('data.items.0.product_name', $product->name)
            ->assertJsonMissing(['user_id', 'address']);

        $this->getJson('/api/v1/track/NOPE')->assertNotFound();
    }

    public function test_newsletter_subscribe(): void
    {
        $this->postJson('/api/v1/newsletter', ['phone' => '09121234567'])
            ->assertCreated()
            ->assertJsonPath('message', 'عضویتت ثبت شد؛ به‌زودی خبرهای خوب میاد 🎉');

        // idempotent + invalid rejected
        $this->postJson('/api/v1/newsletter', ['phone' => '09121234567'])->assertCreated();
        $this->postJson('/api/v1/newsletter', ['phone' => '123'])->assertStatus(422);
        $this->assertDatabaseCount('newsletter_subscriptions', 1);
    }

    public function test_product_questions_flow(): void
    {
        $product = $this->seedCatalog();
        $user = User::factory()->create(['phone' => '09120000000']);
        $token = $user->createToken('test')->plainTextToken;

        $this->getJson('/api/v1/products/'.$product->slug.'/questions')->assertOk()->assertJsonCount(0, 'data');
        $this->postJson('/api/v1/products/'.$product->slug.'/questions', ['question' => 'آیا موجوده؟'])->assertUnauthorized();
        $this->withToken($token)->postJson('/api/v1/products/'.$product->slug.'/questions', ['question' => ''])->assertStatus(422);
        $this->withToken($token)->postJson('/api/v1/products/'.$product->slug.'/questions', ['question' => 'گارانتی دارد؟'])->assertCreated()->assertJsonPath('message', 'سوالت ثبت شد؛ بعد از تأیید نمایش داده می‌شود.');
        $this->getJson('/api/v1/products/'.$product->slug.'/questions')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_product_comments_flow(): void
    {
        $product = $this->seedCatalog();
        $user = User::factory()->create(['phone' => '09120000000']);
        $token = $user->createToken('test')->plainTextToken;

        // guest cannot post (must run before withToken, which persists)
        $this->postJson('/api/v1/products/'.$product->slug.'/comments', [
            'rating' => 5, 'body' => 'x',
        ])->assertUnauthorized();

        // guest sees nothing pending
        $this->getJson('/api/v1/products/'.$product->slug.'/comments')->assertOk()
            ->assertJsonCount(0, 'data');

        // validation
        $this->withToken($token)->postJson('/api/v1/products/'.$product->slug.'/comments', [
            'rating' => 9, 'body' => 'x',
        ])->assertStatus(422);

        // submit goes to moderation, still invisible
        $this->withToken($token)->postJson('/api/v1/products/'.$product->slug.'/comments', [
            'rating' => 5, 'body' => 'عالی بود',
        ])->assertCreated();
        $this->getJson('/api/v1/products/'.$product->slug.'/comments')->assertOk()
            ->assertJsonCount(0, 'data');

        // approved shows up
        Comment::first()->update(['status' => true]);
        $this->getJson('/api/v1/products/'.$product->slug.'/comments')->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.rating', 5)
            ->assertJsonPath('data.0.verified', false)
            ->assertJsonPath('summary.count', 1)
            ->assertJsonPath('summary.avg', 5)
            ->assertJsonPath('summary.dist.5', 1);
    }

    public function test_products_filter_and_show(): void
    {
        $product = $this->seedCatalog();

        $this->assertSame(1, Product::count());
        $this->assertSame(1, Product::where('name', 'like', '%یخچال%')->count());
        $this->assertSame(1, Product::where('status', true)->count());

        $this->getJson('/api/v1/products')->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'test-fridge');

        $this->getJson('/api/v1/products?search='.urlencode('یخچال'))->assertOk()
            ->assertJsonCount(1, 'data');

        $this->getJson('/api/v1/products?ids='.$product->id.',999999')->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'test-fridge');

        $this->getJson('/api/v1/products/'.$product->slug)->dump()->assertOk()
            ->assertJsonPath('data.final_price_rial', 900000);

        $product->update(['status' => false]);
        $this->getJson('/api/v1/products/'.$product->slug)->assertNotFound();
    }

    public function test_otp_login_flow(): void
    {
        $this->postJson('/api/v1/auth/otp/send', ['phone' => '09121234567'])->dump()->assertOk()
            ->assertJsonMissing(['debug_code']);

        $code = Cache::get('otp:09121234567');
        $this->assertNotEmpty($code);

        $this->postJson('/api/v1/auth/otp/verify', ['phone' => '09121234567', 'code' => '00000'])
            ->assertStatus(422);

        $response = $this->postJson('/api/v1/auth/otp/verify', ['phone' => '09121234567', 'code' => (string) $code])
            ->assertOk();

        $this->assertNotEmpty($response->json('token'));
        $this->assertDatabaseHas('users', ['phone' => '09121234567']);
    }

    public function test_password_register_flow(): void
    {
        // invalid phone
        $this->postJson('/api/v1/auth/register', [
            'name' => 'تست', 'phone' => '123', 'password' => 'secret12', 'password_confirmation' => 'secret12',
        ])->assertStatus(422);

        // weak password
        $this->postJson('/api/v1/auth/register', [
            'name' => 'تست', 'phone' => '09129998888', 'password' => '123', 'password_confirmation' => '123',
        ])->assertStatus(422);

        // register ok
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'تست', 'phone' => '09129998888', 'password' => 'secret12', 'password_confirmation' => 'secret12',
        ])->assertCreated();

        $this->assertNotEmpty($response->json('token'));
        $this->assertDatabaseHas('users', ['phone' => '09129998888']);

        // duplicate phone
        $this->postJson('/api/v1/auth/register', [
            'name' => 'تست', 'phone' => '09129998888', 'password' => 'secret12', 'password_confirmation' => 'secret12',
        ])->assertStatus(422);
    }

    public function test_password_login_flow(): void
    {
        User::factory()->create(['phone' => '09129997777', 'password_set_at' => now()]);

        // wrong password
        $this->postJson('/api/v1/auth/login', ['phone' => '09129997777', 'password' => 'wrongpass'])
            ->assertStatus(401);

        // login ok (factory password is "password")
        $login = $this->postJson('/api/v1/auth/login', ['phone' => '09129997777', 'password' => 'password'])
            ->assertOk();

        $this->assertNotEmpty($login->json('token'));

        // user without a set password (e.g. factory default) gets guided to OTP
        User::factory()->create(['phone' => '09129996666']);
        $this->postJson('/api/v1/auth/login', ['phone' => '09129996666', 'password' => 'password'])
            ->assertStatus(401)
            ->assertJsonFragment(['message' => 'برای این شماره رمزی ثبت نشده؛ با کد پیامک وارد شو.']);
    }

    public function test_otp_user_can_set_password_then_login(): void
    {
        // guest cannot set password
        $this->postJson('/api/v1/me/password', [
            'password' => 'xy12345', 'password_confirmation' => 'xy12345',
        ])->assertUnauthorized();

        $this->postJson('/api/v1/auth/otp/send', ['phone' => '09127776666'])->assertOk();
        $code = Cache::get('otp:09127776666');
        $this->postJson('/api/v1/auth/otp/verify', ['phone' => '09127776666', 'code' => (string) $code])->assertOk();

        // OTP-only user cannot use password login yet
        $this->postJson('/api/v1/auth/login', ['phone' => '09127776666', 'password' => 'whatever1'])
            ->assertStatus(401)
            ->assertJsonFragment(['message' => 'برای این شماره رمزی ثبت نشده؛ با کد پیامک وارد شو.']);

        // set password, then login works
        $otpToken = User::where('phone', '09127776666')->firstOrFail()->createToken('test')->plainTextToken;
        $this->withToken($otpToken)->postJson('/api/v1/me/password', [
            'password' => 'newpass1', 'password_confirmation' => 'newpass1',
        ])->assertOk();
        $this->postJson('/api/v1/auth/login', ['phone' => '09127776666', 'password' => 'newpass1'])
            ->assertOk();
    }

    public function test_checkout_math_and_stock_rules(): void
    {
        Setting::set('payment_gateway', 'local');
        $product = $this->seedCatalog();
        $user = User::factory()->create(['phone' => '09120000000']);
        $token = $user->createToken('test')->plainTextToken;

        DiscountCode::create([
            'code' => 'OFF200', 'type' => 'fixed', 'value_rial' => 200000, 'is_active' => true,
        ]);

        $province = Province::create(['name' => 'تهران']);
        $city = City::create(['province_id' => $province->id, 'name' => 'تهران']);

        $address = $user->addresses()->create([
            'province_id' => $province->id,
            'city_id' => $city->id,
            'address' => 'خیابان تست',
            'receiver_name' => 'تست',
            'receiver_mobile' => '09120000000',
        ]);

        // guest blocked (must run before withToken, which persists for later calls)
        $this->postJson('/api/v1/checkout', [
            'address_id' => $address->id,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertUnauthorized();

        $response = $this->withToken($token)->postJson('/api/v1/checkout', [
            'address_id' => $address->id,
            'discount_code' => 'OFF200',
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ]);

        // items 1,800,000 + post 0 - discount 200,000 = 1,600,000
        $response->assertCreated()
            ->assertJsonPath('order.final_price_rial', 1600000)
            ->assertJsonPath('order.items.0.total_rial', 1800000);
        $this->assertNotEmpty($response->json('payment_url'));
        $this->assertSame('GET', $response->json('payment_form.method'));
        $this->assertNotEmpty($response->json('payment_form.inputs.Authority'));

        // stock untouched until payment verify
        $this->assertSame(5, $product->fresh()->quantity);

        // insufficient stock
        $this->withToken($token)->postJson('/api/v1/checkout', [
            'address_id' => $address->id,
            'items' => [['product_id' => $product->id, 'quantity' => 99]],
        ])->assertStatus(422);
    }
}
