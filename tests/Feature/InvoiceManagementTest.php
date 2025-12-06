<?php

namespace Tests\Feature;

use App\Enums\PaymentMethodEnum;
use App\Models\AtecoCode;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests cannot access invoice pages
     */
    public function test_guests_cannot_access_invoice_pages(): void
    {
        $response = $this->get(route('invoices.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('invoices.create'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated users can view invoices index
     */
    public function test_authenticated_users_can_view_invoices_index(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $this->actingAs($user);

        Invoice::factory()->count(3)->for($user)->for($customer)->create();

        $response = $this->get(route('invoices.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Invoices/Index')
            ->has('invoices.data', 3)
        );
    }

    /**
     * Test that users only see their own invoices
     */
    public function test_users_only_see_their_own_invoices(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $customer1 = Customer::factory()->for($user1)->create();
        $customer2 = Customer::factory()->for($user2)->create();

        Invoice::factory()->count(2)->for($user1)->for($customer1)->create();
        Invoice::factory()->count(3)->for($user2)->for($customer2)->create();

        $this->actingAs($user1);
        $response = $this->get(route('invoices.index'));
        $response->assertInertia(fn ($page) => $page
            ->has('invoices.data', 2)
        );
    }

    /**
     * Test that authenticated users can view create invoice page
     */
    public function test_authenticated_users_can_view_create_invoice_page(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('invoices.create'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Invoices/Create')
            ->has('customers')
            ->has('atecoCodes')
        );
    }

    /**
     * Test that authenticated users can create an invoice
     */
    public function test_authenticated_users_can_create_invoice(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $atecoCode = AtecoCode::factory()->for($user)->create();
        $this->actingAs($user);

        $invoiceData = [
            'invoice_number' => 'INV-001',
            'issue_date' => '2025-12-06',
            'customer_id' => $customer->id,
            'ateco_code_id' => $atecoCode->id,
            'payment_terms' => 30,
            'payment_method' => 'bank_transfer',
            'has_withholding_tax' => false,
            'has_contributo_integrativo' => false,
            'contributo_integrativo_percentage' => 0,
            'notes' => 'Test invoice',
            'items' => [
                [
                    'description' => 'Test Service',
                    'quantity' => 1,
                    'unit_price' => 1000,
                    'total' => 1000,
                ],
            ],
        ];

        $response = $this->post(route('invoices.store'), $invoiceData);
        $response->assertRedirect(route('invoices.index'));
        $response->assertSessionHas('success', 'Fattura creata con successo');

        $this->assertDatabaseHas('invoices', [
            'invoice_number' => 'INV-001',
            'customer_id' => $customer->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'description' => 'Test Service',
            'quantity' => 1,
            'unit_price' => 1000,
        ]);
    }

    /**
     * Test that invoice creation validates required fields
     */
    public function test_invoice_creation_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('invoices.store'), []);
        $response->assertSessionHasErrors(['issue_date']);
    }

    /**
     * Test invoice with contributo integrativo
     */
    public function test_invoice_with_contributo_integrativo(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $atecoCode = AtecoCode::factory()->for($user)->create();
        $this->actingAs($user);

        $invoiceData = [
            'invoice_number' => 'INV-003',
            'issue_date' => now()->format('Y-m-d'),
            'customer_id' => $customer->id,
            'ateco_code_id' => $atecoCode->id,
            'customer_business_name' => $customer->business_name,
            'description' => 'Servizio con contributo',
            'amount' => 1000,
            'contributo_integrativo_applied' => true,
            'contributo_integrativo_amount' => 40,
            'items' => [
                [
                    'description' => 'Servizio professionale',
                    'unit_price' => 1000,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->post(route('invoices.store'), $invoiceData);
        $response->assertRedirect(route('invoices.index'));

        $invoice = Invoice::where('invoice_number', 'INV-003')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(1, $invoice->contributo_integrativo_applied);
        $this->assertEquals(40, $invoice->contributo_integrativo_amount);
        $this->assertEquals(1040, $invoice->net_amount); // 1000 + 40
    }

    /**
     * Test that authenticated users can view an invoice
     */
    public function test_authenticated_users_can_view_invoice(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $invoice = Invoice::factory()->for($user)->for($customer)->create();
        $this->actingAs($user);

        $response = $this->get(route('invoices.show', $invoice));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Invoices/Show')
            ->has('invoice')
            ->where('invoice.invoice_number', $invoice->invoice_number)
        );
    }

    /**
     * Test that users cannot view other users' invoices
     */
    public function test_users_cannot_view_other_users_invoices(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $customer = Customer::factory()->for($user2)->create();
        $invoice = Invoice::factory()->for($user2)->for($customer)->create();

        $this->actingAs($user1);
        $response = $this->get(route('invoices.show', $invoice));
        $response->assertStatus(404);
    }

    /**
     * Test that authenticated users can view edit invoice page
     */
    public function test_authenticated_users_can_view_edit_invoice_page(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $invoice = Invoice::factory()->for($user)->for($customer)->create();
        $this->actingAs($user);

        $response = $this->get(route('invoices.edit', $invoice));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Invoices/Edit')
            ->has('invoice')
        );
    }

    /**
     * Test that authenticated users can update an invoice
     */
    public function test_authenticated_users_can_update_invoice(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $atecoCode = AtecoCode::factory()->for($user)->create();
        $invoice = Invoice::factory()->for($user)->for($customer)->create();
        $this->actingAs($user);

        $updateData = [
            'invoice_number' => $invoice->invoice_number,
            'issue_date' => $invoice->issue_date ? $invoice->issue_date->format('Y-m-d') : '2025-12-06',
            'customer_id' => $customer->id,
            'ateco_code_id' => $atecoCode->id,
            'payment_terms' => 60,
            'payment_method' => 'cash',
            'has_withholding_tax' => false,
            'has_contributo_integrativo' => false,
            'contributo_integrativo_percentage' => 0,
            'notes' => 'Updated notes',
            'items' => [
                [
                    'description' => 'Updated Service',
                    'quantity' => 2,
                    'unit_price' => 500,
                    'total' => 1000,
                ],
            ],
        ];

        $response = $this->put(route('invoices.update', $invoice), $updateData);
        $response->assertRedirect(route('invoices.show', $invoice));
        $response->assertSessionHas('success', 'Fattura aggiornata con successo');

        $invoice->refresh();
        $this->assertEquals(PaymentMethodEnum::CASH, $invoice->payment_method);
        $this->assertEquals('Updated notes', $invoice->notes);
    }

    /**
     * Test that users cannot update other users' invoices
     */
    public function test_users_cannot_update_other_users_invoices(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $customer = Customer::factory()->for($user2)->create();
        $invoice = Invoice::factory()->for($user2)->for($customer)->create();

        $this->actingAs($user1);
        $response = $this->put(route('invoices.update', $invoice), [
            'invoice_number' => 'HACKED',
        ]);
        $response->assertStatus(404);
    }

    /**
     * Test that authenticated users can delete an invoice
     */
    public function test_authenticated_users_can_delete_invoice(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $invoice = Invoice::factory()->for($user)->for($customer)->create();
        $this->actingAs($user);

        $response = $this->delete(route('invoices.destroy', $invoice));
        $response->assertRedirect(route('invoices.index'));
        $response->assertSessionHas('success', 'Fattura eliminata con successo');

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    /**
     * Test that users cannot delete other users' invoices
     */
    public function test_users_cannot_delete_other_users_invoices(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $customer = Customer::factory()->for($user2)->create();
        $invoice = Invoice::factory()->for($user2)->for($customer)->create();

        $this->actingAs($user1);
        $response = $this->delete(route('invoices.destroy', $invoice));
        $response->assertStatus(404);

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);
    }

    /**
     * Test get next invoice number endpoint
     */
    public function test_get_next_invoice_number(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->for($user)->create();
        $this->actingAs($user);

        // Create existing invoices
        Invoice::factory()->for($user)->for($customer)->create(['invoice_number' => '2025/001']);
        Invoice::factory()->for($user)->for($customer)->create(['invoice_number' => '2025/002']);

        $response = $this->get(route('invoices.next-number', ['year' => 2025]));
        $response->assertStatus(200);
        $response->assertJson(['invoice_number' => '2025/003']);
    }
}
