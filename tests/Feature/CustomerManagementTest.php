<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests cannot access customer pages
     */
    public function test_guests_cannot_access_customer_pages(): void
    {
        $response = $this->get(route('customers.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('customers.create'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated users can view customers index
     */
    public function test_authenticated_users_can_view_customers_index(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Customer::factory()->count(3)->for($user)->create();

        $response = $this->get(route('customers.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Customers/Index')
            ->has('customers.data', 3)
        );
    }

    /**
     * Test that users only see their own customers
     */
    public function test_users_only_see_their_own_customers(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Customer::factory()->count(2)->for($user1)->create();
        Customer::factory()->count(3)->for($user2)->create();

        $this->actingAs($user1);
        $response = $this->get(route('customers.index'));
        $response->assertInertia(fn ($page) => $page
            ->has('customers.data', 2)
        );
    }

    /**
     * Test that authenticated users can view create customer page
     */
    public function test_authenticated_users_can_view_create_customer_page(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('customers.create'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Customers/Create')
        );
    }

    /**
     * Test that authenticated users can create a customer
     */
    public function test_authenticated_users_can_create_customer(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $customerData = [
            'business_name' => 'Test Customer',
            'vat_number' => '12345678901',
            'tax_code' => 'RSSMRA80A01H501U',
            'email' => 'customer@example.com',
            'phone' => '+39 123 456 7890',
            'address' => 'Via Roma 1',
            'city' => 'Milano',
            'postal_code' => '20100',
            'province' => 'MI',
            'country' => 'IT',
        ];

        $response = $this->post(route('customers.store'), $customerData);
        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success', 'Cliente creato con successo');

        $this->assertDatabaseHas('customers', [
            'business_name' => 'Test Customer',
            'vat_number' => '12345678901',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test that customer creation validates required fields
     */
    public function test_customer_creation_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('customers.store'), []);
        $response->assertSessionHasErrors(['business_name']);
    }

    /**
     * Test that authenticated users can view a customer
     */
    public function test_authenticated_users_can_view_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $this->actingAs($user);

        $response = $this->get(route('customers.show', $customer));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Customers/Show')
            ->has('customer')
            ->where('customer.business_name', $customer->business_name)
        );
    }

    /**
     * Test that users cannot view other users' customers
     */
    public function test_users_cannot_view_other_users_customers(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $customer = Customer::factory()->for($user2)->create();

        $this->actingAs($user1);
        $response = $this->get(route('customers.show', $customer));
        $response->assertStatus(404);
    }

    /**
     * Test that authenticated users can view edit customer page
     */
    public function test_authenticated_users_can_view_edit_customer_page(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $this->actingAs($user);

        $response = $this->get(route('customers.edit', $customer));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Customers/Edit')
            ->has('customer')
        );
    }

    /**
     * Test that authenticated users can update a customer
     */
    public function test_authenticated_users_can_update_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $this->actingAs($user);

        $updateData = [
            'business_name' => 'Updated Customer',
            'vat_number' => $customer->vat_number,
            'email' => 'updated@example.com',
        ];

        $response = $this->put(route('customers.update', $customer), $updateData);
        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success', 'Cliente aggiornato con successo');

        $customer->refresh();
        $this->assertEquals('Updated Customer', $customer->business_name);
        $this->assertEquals('updated@example.com', $customer->email);
    }

    /**
     * Test that users cannot update other users' customers
     */
    public function test_users_cannot_update_other_users_customers(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $customer = Customer::factory()->for($user2)->create();

        $this->actingAs($user1);
        $response = $this->put(route('customers.update', $customer), [
            'business_name' => 'Hacked',
        ]);
        $response->assertStatus(404);
    }

    /**
     * Test that authenticated users can delete a customer
     */
    public function test_authenticated_users_can_delete_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $this->actingAs($user);

        $response = $this->delete(route('customers.destroy', $customer));
        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success', 'Cliente eliminato con successo');

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    /**
     * Test that users cannot delete other users' customers
     */
    public function test_users_cannot_delete_other_users_customers(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $customer = Customer::factory()->for($user2)->create();

        $this->actingAs($user1);
        $response = $this->delete(route('customers.destroy', $customer));
        $response->assertStatus(404);

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    /**
     * Test customer search functionality
     */
    public function test_customer_search_functionality(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Customer::factory()->for($user)->create(['business_name' => 'Acme Corp']);
        Customer::factory()->for($user)->create(['business_name' => 'Tech Solutions']);
        Customer::factory()->for($user)->create(['business_name' => 'Design Studio']);

        $response = $this->get(route('customers.index', ['search' => 'Tech']));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('customers.data', 1)
        );
    }
}
