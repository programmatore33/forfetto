<?php

namespace App\Services;

use App\Dtos\Input\InputIndexDto;
use App\Dtos\InvoiceDto;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;

/**
 * Invoice-specific index service with custom filtering
 */
class InvoiceIndexService extends IndexService
{
    /**
     * Get invoices with pagination, search and sorting
     */
    public function getInvoices(InputIndexDto $input): array
    {
        $query = Invoice::query()->with(['customer', 'atecoCode']);

        // Define searchable and sortable fields for invoices
        $searchableFields = [
            'invoice_number',
            'customer_business_name',
            'description',
            'customer_vat_number',
            'customer_tax_code',
        ];

        $sortableFields = [
            'invoice_number',
            'customer_business_name',
            'issue_date',
            'payment_date',
            'amount',
            'is_paid',
            'created_at',
            'updated_at',
        ];

        // Build query with base functionality
        $paginator = $this->buildQuery(
            $query,
            $input,
            $searchableFields,
            $sortableFields,
            'issue_date',
            'desc'
        );

        // Transform to InvoiceDto collection
        $invoiceData = InvoiceDto::collect($paginator->items());

        return [
            'data' => $invoiceData,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'filters' => [
                'search' => $input->search,
                'per_page' => $input->per_page,
                'sort_field' => $input->sort_field,
                'sort_direction' => $input->sort_direction,
            ] + $input->filters,
        ];
    }

    /**
     * Apply invoice-specific filters
     */
    protected function applyCustomFilters(Builder $query, array $filters): void
    {
        // Filter by issue date range
        if (! empty($filters['issue_date_from'])) {
            $query->whereDate('issue_date', '>=', $filters['issue_date_from']);
        }

        if (! empty($filters['issue_date_to'])) {
            $query->whereDate('issue_date', '<=', $filters['issue_date_to']);
        }

        // Filter by payment date range
        if (! empty($filters['payment_date_from'])) {
            $query->whereDate('payment_date', '>=', $filters['payment_date_from']);
        }

        if (! empty($filters['payment_date_to'])) {
            $query->whereDate('payment_date', '<=', $filters['payment_date_to']);
        }

        // Filter by paid status
        if (isset($filters['is_paid'])) {
            $query->where('is_paid', (bool) $filters['is_paid']);
        }

        // Filter by payment method
        if (! empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        // Filter by customer
        if (! empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        // Filter by ATECO code
        if (! empty($filters['ateco_code_id'])) {
            $query->where('ateco_code_id', $filters['ateco_code_id']);
        }
    }
}
