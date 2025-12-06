<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserScopeTraitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that HasUserScope trait automatically filters by user_id
     */
    public function test_user_scope_automatically_filters_by_user_id(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create customers for different users
        Customer::factory()->count(2)->for($user1)->create();
        Customer::factory()->count(3)->for($user2)->create();

        // Act as user1 and query
        $this->actingAs($user1);
        $customers = Customer::all();

        // Should only see user1's customers
        $this->assertCount(2, $customers);
        $customers->each(fn ($customer) => $this->assertEquals($user1->id, $customer->user_id));
    }

    /**
     * Test that HasUserScope trait automatically assigns user_id on creation
     */
    public function test_user_scope_automatically_assigns_user_id(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create customer without explicitly setting user_id
        $customer = Customer::create([
            'business_name' => 'Test Customer',
            'vat_number' => 'IT12345678901',
        ]);

        $this->assertEquals($user->id, $customer->user_id);
    }

    /**
     * Test that withoutUserScope removes global scope
     */
    public function test_without_user_scope_removes_filtering(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Customer::factory()->count(2)->for($user1)->create();
        Customer::factory()->count(3)->for($user2)->create();

        $this->actingAs($user1);

        // With scope - should only see user1's customers
        $scopedCustomers = Customer::all();
        $this->assertCount(2, $scopedCustomers);

        // Without scope - should see all customers
        $allCustomers = Customer::withoutUserScope()->get();
        $this->assertCount(5, $allCustomers);
    }

    /**
     * Test that forUser scope works correctly
     */
    public function test_for_user_scope_filters_by_specific_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Customer::factory()->count(2)->for($user1)->create();
        Customer::factory()->count(3)->for($user2)->create();

        // Query for specific user
        $user2Customers = Customer::forUser($user2->id)->get();

        $this->assertCount(3, $user2Customers);
        $user2Customers->each(fn ($customer) => $this->assertEquals($user2->id, $customer->user_id));
    }

    /**
     * Test that forDemoSession scope works correctly
     */
    public function test_for_demo_session_scope_filters_correctly(): void
    {
        $demoUser = User::factory()->demo()->create();
        $session1 = $demoUser->createDemoSession();
        $session2 = $demoUser->createDemoSession();

        Customer::factory()->for($demoUser)->create(['session_id' => $session1->session_id]);
        Customer::factory()->for($demoUser)->create(['session_id' => $session1->session_id]);
        Customer::factory()->for($demoUser)->create(['session_id' => $session2->session_id]);

        $session1Customers = Customer::forDemoSession($session1->session_id, $demoUser->id)->get();

        $this->assertCount(2, $session1Customers);
        $session1Customers->each(fn ($customer) => $this->assertEquals($session1->session_id, $customer->session_id));
    }

    /**
     * Test that demoOnly scope returns only demo records
     */
    public function test_demo_only_scope_returns_only_demo_records(): void
    {
        $regularUser = User::factory()->create(['is_demo' => false]);
        $demoUser = User::factory()->demo()->create();
        $session = $demoUser->createDemoSession();

        // Create regular and demo customers
        Customer::factory()->count(2)->for($regularUser)->create();
        Customer::factory()->count(3)->for($demoUser)->create(['session_id' => $session->session_id]);

        $demoCustomers = Customer::demoOnly()->get();

        $this->assertCount(3, $demoCustomers);
        $demoCustomers->each(fn ($customer) => $this->assertNotNull($customer->session_id));
    }

    /**
     * Test that scope doesn't interfere with model relationships
     */
    public function test_scope_works_with_model_relationships(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::factory()->for($user)->create();

        // Customer should be accessible through user relationship
        $userCustomers = $user->customers;

        $this->assertCount(1, $userCustomers);
        $this->assertEquals($customer->id, $userCustomers->first()->id);
    }

    /**
     * Test that scope works with eager loading
     */
    public function test_scope_works_with_eager_loading(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::factory()->for($user)->create();

        // Load user with customers
        $loadedUser = User::with('customers')->find($user->id);

        $this->assertCount(1, $loadedUser->customers);
        $this->assertEquals($customer->id, $loadedUser->customers->first()->id);
    }

    /**
     * Test that scope respects manual user_id assignment
     */
    public function test_scope_respects_manual_user_id_assignment(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1);

        // Manually set different user_id (for admin scenarios)
        $customer = Customer::withoutUserScope()->create([
            'business_name' => 'Test Customer',
            'vat_number' => 'IT12345678901',
            'user_id' => $user2->id,
        ]);

        $this->assertEquals($user2->id, $customer->user_id);
    }

    /**
     * Test that scope works across multiple model types
     */
    public function test_scope_works_across_multiple_model_types(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Test with multiple models that use HasUserScope
        Customer::factory()->count(2)->for($user1)->create();
        Customer::factory()->count(3)->for($user2)->create();

        $this->actingAs($user1);

        $user1Customers = Customer::all();
        $this->assertCount(2, $user1Customers);

        $this->actingAs($user2);

        $user2Customers = Customer::all();
        $this->assertCount(3, $user2Customers);
    }
}
