<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_log_in(): void
    {
        $this->post(route('register.post'), [
            'name' => 'Nguyen Van A',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('home'))
            ->assertSessionHas('success', 'Đăng ký thành công. Bạn đã được đăng nhập.');

        $this->assertDatabaseHas('users', ['email' => 'customer@example.com', 'is_admin' => false]);
        $this->assertAuthenticated();
        $this->post(route('logout'))->assertRedirect('/');

        $this->post(route('login.post'), [
            'email' => 'customer@example.com',
            'password' => 'password123',
        ])->assertRedirect('/')
            ->assertSessionHas('success', 'Đăng nhập thành công.');

        $this->assertAuthenticated();
    }

    public function test_admin_and_customer_accounts_use_separate_login_pages(): void
    {
        $customer = User::factory()->create(['email' => 'customer@example.com']);
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        $this->get(route('admin.products.index'))
            ->assertRedirect(route('admin.login'));

        $this->post(route('admin.login.post'), [
            'email' => $customer->email,
            'password' => 'password',
        ])->assertRedirect()
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post(route('login.post'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect()
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post(route('admin.login.post'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success', 'Đăng nhập quản trị thành công.');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_registration_shows_validation_errors_for_invalid_input(): void
    {
        $this->from(route('register'))
            ->post(route('register.post'), [
                'name' => '',
                'email' => 'not-an-email',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['name', 'email', 'password']);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Vui lòng kiểm tra lại thông tin trong biểu mẫu.')
            ->assertSee('aria-invalid="true"', false);
    }

    public function test_login_shows_error_for_invalid_credentials(): void
    {
        $this->from(route('login'))
            ->post(route('login.post'), [
                'email' => 'customer@example.com',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Thông tin đăng nhập không hợp lệ hoặc tài khoản không dành cho khách hàng.');
    }

    public function test_storefront_displays_products_with_a_fallback_image(): void
    {
        $category = Category::create(['name' => 'Áo']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Áo thun BAO',
            'description' => 'Item dễ phối mỗi ngày.',
            'price' => 199000,
            'quantity' => 3,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('images.unsplash.com/photo-1521572163474-6864f9cf17ab');

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Thêm vào giỏ hàng');
    }

    public function test_out_of_stock_product_cannot_be_added_to_the_cart(): void
    {
        $category = Category::create(['name' => 'Áo']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Áo thun hết hàng',
            'price' => 199000,
            'quantity' => 0,
        ]);

        $this->withSession(['cart' => []])
            ->post(route('cart.add', $product))
            ->assertRedirect()
            ->assertSessionHas('error', 'Sản phẩm hiện đã hết hàng.')
            ->assertSessionHas('cart', []);
    }

    public function test_cart_corrects_quantities_that_exceed_available_stock(): void
    {
        $category = Category::create(['name' => 'Áo']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Áo thun số lượng giới hạn',
            'price' => 199000,
            'quantity' => 1,
        ]);

        $this->withSession(['cart' => [$product->id => 3]])
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('value="1"', false)
            ->assertSessionHas('cart', [$product->id => 1]);
    }

    public function test_checkout_requires_the_cart_to_be_current_with_stock(): void
    {
        $customer = User::factory()->create();
        $category = Category::create(['name' => 'Áo']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Áo sắp hết',
            'price' => 199000,
            'quantity' => 1,
        ]);

        $this->actingAs($customer)
            ->withSession(['cart' => [$product->id => 2]])
            ->post(route('checkout.store'), [
                'phone' => '0900000000',
                'address' => '1 Nguyen Hue',
                'payment_method' => 'cod',
            ])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
        $this->get(route('cart.index'))->assertSessionHas('cart', [$product->id => 1]);
    }

    public function test_customer_can_only_see_their_own_order_history(): void
    {
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $orderData = [
            'total' => 100000,
            'status' => 'pending',
            'payment_method' => 'cod',
            'phone' => '0900000000',
            'address' => '1 Nguyen Hue',
        ];
        $ownOrder = Order::create($orderData + ['user_id' => $customer->id]);
        $otherOrder = Order::create($orderData + ['user_id' => $otherCustomer->id]);

        $this->actingAs($customer)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Mã đơn #' . $ownOrder->id)
            ->assertDontSee('Mã đơn #' . $otherOrder->id);

        $this->get(route('orders.show', $otherOrder))->assertForbidden();
    }

    public function test_admin_can_see_all_purchased_orders(): void
    {
        $customer = User::factory()->create(['name' => 'Khách mua hàng']);
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 100000,
            'status' => 'pending',
            'payment_method' => 'cod',
            'phone' => '0900000000',
            'address' => '1 Nguyen Hue',
        ]);
        $order->items()->create([
            'product_name' => 'Áo BAO',
            'quantity' => 2,
            'price' => 50000,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('Mã đơn', false)
            ->assertSee($customer->name)
            ->assertSee('Áo BAO')
            ->assertSee('0900000000')
            ->assertSee('Đang xử lý');

        $this->actingAs($customer)
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }

    public function test_each_admin_can_update_order_status_but_customers_cannot(): void
    {
        $customer = User::factory()->create();
        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 100000,
            'status' => 'pending',
            'payment_method' => 'cod',
            'phone' => '0900000000',
            'address' => '1 Nguyen Hue',
        ]);
        $firstAdmin = User::factory()->create(['is_admin' => true]);
        $secondAdmin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($firstAdmin)
            ->patch(route('admin.orders.update-status', $order), ['status' => 'processing'])
            ->assertRedirect(route('admin.orders.index'));
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'processing']);

        $this->actingAs($secondAdmin)
            ->patch(route('admin.orders.update-status', $order), ['status' => 'shipped'])
            ->assertRedirect(route('admin.orders.index'));
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'shipped']);

        $this->actingAs($customer)
            ->patch(route('admin.orders.update-status', $order), ['status' => 'delivered'])
            ->assertForbidden();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'shipped']);
    }

    public function test_order_dates_are_displayed_in_vietnam_time(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 100000,
            'status' => 'pending',
            'payment_method' => 'cod',
            'phone' => '0900000000',
            'address' => '1 Nguyen Hue',
        ]);
        $order->forceFill([
            'created_at' => Carbon::parse('2026-11-09 13:17:03', 'UTC'),
        ])->save();

        $this->actingAs($customer)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('09/11/2026 · 20:17');

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('09/11/2026 · 20:17');

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('09/11/2026 20:17');
    }

    public function test_customer_can_place_an_order_and_see_order_details(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Áo']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Áo khoác',
            'description' => 'Áo khoác nhẹ',
            'price' => 250000,
            'quantity' => 5,
        ]);
        $this->actingAs($user)->withSession(['cart' => [$product->id => 2]]);

        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Thanh toán');

        $this->post(route('checkout.store'), [
            'phone' => '0900000000',
            'address' => '1 Nguyen Hue, Quan 1',
            'payment_method' => 'cod',
        ])->assertRedirect();

        $order = Order::with('items')->firstOrFail();
        $this->assertSame('500000.00', $order->total);
        $this->assertSame('Áo khoác', $order->items->first()->product_name);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'quantity' => 3]);

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Áo khoác');

        $this->actingAs(User::factory()->state(['is_admin' => true])->create())
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => null,
            'product_name' => 'Áo khoác',
        ]);
    }
}
