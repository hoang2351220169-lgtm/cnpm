<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_product_management(): void
    {
        $this->get(route('admin.products.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_customer_cannot_manage_products(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_a_product(): void
    {
        $this->actingAs(User::factory()->state(['is_admin' => true])->create());
        $category = Category::create([
            'name' => 'Áo',
            'description' => 'Các loại áo',
        ]);
        $productData = [
            'name' => 'Áo thun',
            'category_id' => $category->id,
            'description' => 'Áo cotton',
            'price' => 199000,
            'quantity' => 12,
            'image' => 'https://example.com/ao-thun.jpg',
        ];

        $this->post(route('admin.products.store'), $productData)
            ->assertRedirect(route('admin.products.index'));

        $product = Product::firstOrFail();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Áo thun']);

        $updatedData = $productData;
        $updatedData['name'] = 'Áo thun mới';
        $this->put(route('admin.products.update', $product), $updatedData)
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Áo thun mới']);

        $this->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
