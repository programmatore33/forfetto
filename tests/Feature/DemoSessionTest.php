<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DemoSessionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that demo users can be created
     */
    public function test_demo_users_can_be_created(): void
    {
        $demoUser = User::factory()->demo()->create();

        $this->assertTrue($demoUser->is_demo);
        $this->assertNotNull($demoUser->email);
    }

    /**
     * Test that demo user can create a session
     */
    public function test_demo_user_can_create_session(): void
    {
        $demoUser = User::factory()->demo()->create();

        $session = $demoUser->createDemoSession();

        $this->assertInstanceOf(UserSession::class, $session);
        $this->assertEquals($demoUser->id, $session->user_id);
        $this->assertNotNull($session->session_id);
        $this->assertNotNull($session->expires_at);
    }

    /**
     * Test that demo session has correct expiration time
     */
    public function test_demo_session_has_correct_expiration(): void
    {
        $demoUser = User::factory()->demo()->create();
        $session = $demoUser->createDemoSession();

        $expectedExpiration = now()->addHours(24);
        $this->assertTrue($session->expires_at->diffInMinutes($expectedExpiration) < 1);
    }

    /**
     * Test that demo user can get active session
     */
    public function test_demo_user_can_get_active_session(): void
    {
        $demoUser = User::factory()->demo()->create();
        $session = $demoUser->createDemoSession();

        $activeSession = $demoUser->getActiveDemoSession();

        $this->assertNotNull($activeSession);
        $this->assertEquals($session->id, $activeSession->id);
    }

    /**
     * Test that expired sessions are not returned as active
     */
    public function test_expired_sessions_are_not_active(): void
    {
        $demoUser = User::factory()->demo()->create();
        $session = $demoUser->createDemoSession();

        // Manually expire the session
        $session->update(['expires_at' => now()->subHour()]);

        $activeSession = $demoUser->getActiveDemoSession();
        $this->assertNull($activeSession);
    }

    /**
     * Test that demo user data is isolated by session_id
     */
    public function test_demo_user_data_is_isolated_by_session(): void
    {
        $demoUser = User::factory()->demo()->create();

        // Create two sessions
        $session1 = $demoUser->createDemoSession();
        $session2 = $demoUser->createDemoSession();

        // Create customers for each session
        $customer1 = Customer::factory()->for($demoUser)->create(['session_id' => $session1->session_id]);
        $customer2 = Customer::factory()->for($demoUser)->create(['session_id' => $session2->session_id]);

        // Query for session 1 should only return customer1
        $session1Customers = Customer::forDemoSession($session1->session_id, $demoUser->id)->get();
        $this->assertCount(1, $session1Customers);
        $this->assertEquals($customer1->id, $session1Customers->first()->id);

        // Query for session 2 should only return customer2
        $session2Customers = Customer::forDemoSession($session2->session_id, $demoUser->id)->get();
        $this->assertCount(1, $session2Customers);
        $this->assertEquals($customer2->id, $session2Customers->first()->id);
    }

    /**
     * Test that regular users don't have session_id on their data
     */
    public function test_regular_users_data_has_no_session_id(): void
    {
        $regularUser = User::factory()->create(['is_demo' => false]);
        $this->actingAs($regularUser);

        $customer = Customer::factory()->for($regularUser)->create();

        $this->assertNull($customer->session_id);
    }

    /**
     * Test that demo-only scope filters correctly
     */
    public function test_demo_only_scope_filters_correctly(): void
    {
        $regularUser = User::factory()->create(['is_demo' => false]);
        $demoUser = User::factory()->demo()->create();
        $session = $demoUser->createDemoSession();

        // Create data for both users
        Customer::factory()->for($regularUser)->create();
        Customer::factory()->for($demoUser)->create(['session_id' => $session->session_id]);

        // Demo-only should only return demo user data
        $demoCustomers = Customer::demoOnly()->get();
        $this->assertCount(1, $demoCustomers);
        $this->assertEquals($demoUser->id, $demoCustomers->first()->user_id);
    }

    /**
     * Test that withoutUserScope removes global scope
     */
    public function test_without_user_scope_removes_filtering(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Customer::factory()->for($user1)->create();
        Customer::factory()->for($user2)->create();

        $this->actingAs($user1);

        // With scope, should only see own customers
        $scopedCustomers = Customer::all();
        $this->assertCount(1, $scopedCustomers);

        // Without scope, should see all
        $allCustomers = Customer::withoutUserScope()->get();
        $this->assertCount(2, $allCustomers);
    }

    /**
     * Test that expired demo sessions can be identified
     */
    public function test_expired_demo_sessions_can_be_identified(): void
    {
        $demoUser = User::factory()->demo()->create();

        // Create active and expired sessions
        $activeSession = $demoUser->createDemoSession();
        $expiredSession = UserSession::factory()->for($demoUser)->create([
            'expires_at' => now()->subHour(),
        ]);

        $expiredSessions = UserSession::where('expires_at', '<', now())->get();
        $this->assertCount(1, $expiredSessions);
        $this->assertEquals($expiredSession->id, $expiredSessions->first()->id);
    }

    /**
     * Test that demo session cleanup deletes related data
     */
    public function test_demo_session_cleanup_deletes_related_data(): void
    {
        $demoUser = User::factory()->demo()->create();
        $session = $demoUser->createDemoSession();
        $sessionId = $session->session_id;

        // Create demo data
        $customer = Customer::factory()->for($demoUser)->create(['session_id' => $sessionId]);
        $category = ExpenseCategory::factory()->create(['session_id' => $sessionId, 'user_id' => $demoUser->id]);
        $expense = Expense::factory()->for($demoUser)->for($category, 'expenseCategory')->create(['session_id' => $sessionId]);
        $invoice = Invoice::factory()->for($demoUser)->for($customer)->create(['session_id' => $sessionId]);

        // Verify data exists
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
        $this->assertDatabaseHas('expenses', ['id' => $expense->id]);
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);

        // Delete session and related data
        Customer::where('session_id', $sessionId)->delete();
        Expense::where('session_id', $sessionId)->delete();
        Invoice::where('session_id', $sessionId)->delete();
        ExpenseCategory::where('session_id', $sessionId)->delete();
        $session->delete();

        // Verify data is deleted
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseMissing('user_sessions', ['id' => $session->id]);
    }

    /**
     * Test that multiple demo users can have separate sessions
     */
    public function test_multiple_demo_users_can_have_separate_sessions(): void
    {
        $demoUser1 = User::factory()->create(['is_demo' => true, 'email' => 'demo1@test.com']);
        $demoUser2 = User::factory()->create(['is_demo' => true, 'email' => 'demo2@test.com']);

        $session1 = $demoUser1->createDemoSession();
        $session2 = $demoUser2->createDemoSession();

        $this->assertNotEquals($session1->session_id, $session2->session_id);
        $this->assertEquals($demoUser1->id, $session1->user_id);
        $this->assertEquals($demoUser2->id, $session2->user_id);
    }

    /**
     * Test that demo user check method works correctly
     */
    public function test_demo_user_check_method(): void
    {
        $regularUser = User::factory()->create(['is_demo' => false]);
        $demoUser = User::factory()->demo()->create();

        $this->assertFalse($regularUser->isDemoUser());
        $this->assertTrue($demoUser->isDemoUser());
    }

    /**
     * Test that session_id is UUID format
     */
    public function test_session_id_is_uuid_format(): void
    {
        $demoUser = User::factory()->demo()->create();
        $session = $demoUser->createDemoSession();

        $this->assertTrue(Str::isUuid($session->session_id));
    }

    /**
     * Test forUser scope works correctly
     */
    public function test_for_user_scope_works_correctly(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Customer::factory()->count(2)->for($user1)->create();
        Customer::factory()->count(3)->for($user2)->create();

        $user1Customers = Customer::forUser($user1->id)->get();
        $this->assertCount(2, $user1Customers);

        $user2Customers = Customer::forUser($user2->id)->get();
        $this->assertCount(3, $user2Customers);
    }
}
