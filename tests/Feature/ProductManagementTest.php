<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests cannot access product pages
     */
    public function test_guests_cannot_access_product_pages(): void
    {
        $response = $this->get(route('products.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('products.create'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated users can view products index
     */
    public function test_authenticated_users_can_view_products_index(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Product::factory()->count(3)->for($user)->create();

        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Products/Index')
            ->has('products.data', 3)
        );
    }

    /**
     * Test that users only see their own products
     */
    public function test_users_only_see_their_own_products(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Product::factory()->count(2)->for($user1)->create();
        Product::factory()->count(3)->for($user2)->create();

        $this->actingAs($user1);
        $response = $this->get(route('products.index'));
        $response->assertInertia(fn ($page) => $page
            ->has('products.data', 2)
        );
    }

    /**
     * Test that authenticated users can view create product page
     */
    public function test_authenticated_users_can_view_create_product_page(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('products.create'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Products/Create')
        );
    }

    /**
     * Test that authenticated users can create a product
     */
    public function test_authenticated_users_can_create_product(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $productData = [
            'code' => 'PROD-001',
            'name' => 'Test Product',
            'description' => 'A test product',
            'unit_price' => 99.99,
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Prodotto creato con successo.');

        $this->assertDatabaseHas('products', [
            'code' => 'PROD-001',
            'name' => 'Test Product',
            'unit_price' => 99.99,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test that product creation validates required fields
     */
    public function test_product_creation_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('products.store'), []);
        $response->assertSessionHasErrors(['name', 'unit_price']);
    }

    /**
     * Test that unit price must be positive
     */
    public function test_product_unit_price_must_be_positive(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('products.store'), [
            'name' => 'Test Product',
            'unit_price' => -10,
        ]);
        $response->assertSessionHasErrors(['unit_price']);
    }

    /**
     * Test that users cannot view other users' products
     */
    public function test_users_cannot_view_other_users_products(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $product = Product::factory()->for($user2)->create();

        $this->actingAs($user1);
        $response = $this->get(route('products.show', $product));
        $response->assertStatus(404);
    }

    /**
     * Test that authenticated users can view edit product page
     */
    public function test_authenticated_users_can_view_edit_product_page(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->for($user)->create();
        $this->actingAs($user);

        $response = $this->get(route('products.edit', $product));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Products/Edit')
            ->has('product')
        );
    }

    /**
     * Test that authenticated users can update a product
     */
    public function test_authenticated_users_can_update_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->for($user)->create();
        $this->actingAs($user);

        $updateData = [
            'code' => 'PROD-002',
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'unit_price' => 149.99,
        ];

        $response = $this->put(route('products.update', $product), $updateData);
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Prodotto aggiornato con successo.');

        $product->refresh();
        $this->assertEquals('Updated Product', $product->name);
        $this->assertEquals(149.99, $product->unit_price);
    }

    /**
     * Test that users cannot update other users' products
     */
    public function test_users_cannot_update_other_users_products(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $product = Product::factory()->for($user2)->create();

        $this->actingAs($user1);
        $response = $this->put(route('products.update', $product), [
            'name' => 'Hacked',
            'unit_price' => 999,
        ]);
        $response->assertStatus(404);
    }

    /**
     * Test that authenticated users can delete a product
     */
    public function test_authenticated_users_can_delete_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->for($user)->create();
        $this->actingAs($user);

        $response = $this->delete(route('products.destroy', $product));
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Prodotto eliminato con successo.');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /**
     * Test that users cannot delete other users' products
     */
    public function test_users_cannot_delete_other_users_products(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $product = Product::factory()->for($user2)->create();

        $this->actingAs($user1);
        $response = $this->delete(route('products.destroy', $product));
        $response->assertStatus(404);

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    /**
     * Test product search functionality
     */
    public function test_product_search_functionality(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Product::factory()->for($user)->create(['name' => 'Consulting Service']);
        Product::factory()->for($user)->create(['name' => 'Web Development']);
        Product::factory()->for($user)->create(['name' => 'Mobile App Development']);

        $response = $this->get(route('products.search', ['q' => 'Development']));
        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    /**
     * Test product autocomplete search by code
     */
    public function test_product_search_by_code(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Product::factory()->for($user)->create(['code' => 'WEB-001', 'name' => 'Web Service']);
        Product::factory()->for($user)->create(['code' => 'MOB-001', 'name' => 'Mobile Service']);
        Product::factory()->for($user)->create(['code' => 'WEB-002', 'name' => 'Web Maintenance']);

        $response = $this->get(route('products.search', ['q' => 'WEB']));
        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    /**
     * Test product search returns limited results
     */
    public function test_product_search_returns_limited_results(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create 15 products
        Product::factory()->count(15)->for($user)->create(['name' => 'Service']);

        $response = $this->get(route('products.search', ['q' => 'Service']));
        $response->assertStatus(200);
        $response->assertJsonCount(10); // Limited to 10 results
    }

    /**
     * Test product search doesn't return other users' products
     */
    public function test_product_search_only_returns_own_products(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Product::factory()->for($user1)->create(['name' => 'My Service']);
        Product::factory()->for($user2)->create(['name' => 'Other Service']);

        $this->actingAs($user1);
        $response = $this->get(route('products.search', ['q' => 'Service']));
        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }
}
