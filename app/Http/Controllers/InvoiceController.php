<?php

namespace App\Http\Controllers;

use App\Dtos\Input\InputIndexDto;
use App\Dtos\Input\InputInvoiceDto;
use App\Dtos\InvoiceDto;
use App\Dtos\PaginatedResponseDto;
use App\Models\AtecoCode;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\InvoiceIndexService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceIndexService $invoiceIndexService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(InputIndexDto $inputIndexDto): Response
    {
        $result = $this->invoiceIndexService->getInvoices($inputIndexDto);

        return Inertia::render('Invoices/Index', [
            'invoices' => PaginatedResponseDto::fromServiceResult($result),
            'filters' => $result['filters'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $customers = Customer::query()
            ->select('id', 'business_name', 'vat_number', 'tax_code')
            ->orderBy('business_name')
            ->get();

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $atecoCodes = AtecoCode::query()
            ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
            ->select('id', 'code', 'description', 'profitability_coefficient')
            ->get()
            ->map(function ($ateco) use ($user) {
                $ateco->is_primary = $user
                    ->atecoCodes()
                    ->where('ateco_codes.id', $ateco->id)
                    ->first()
                    ?->pivot
                    ->is_primary ?? false;

                return $ateco;
            });

        return Inertia::render('Invoices/Create', [
            'customers' => $customers,
            'atecoCodes' => $atecoCodes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InputInvoiceDto $inputInvoiceDto): RedirectResponse
    {
        Invoice::create($inputInvoiceDto->toModelArray());

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Fattura creata con successo');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): Response
    {
        $invoice->load(['customer', 'atecoCode']);
        $invoiceDto = InvoiceDto::from($invoice);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoiceDto,
            'atecoCode' => $invoice->atecoCode,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice): Response
    {
        $invoice->load(['customer', 'atecoCode']);
        $invoiceDto = InvoiceDto::from($invoice);

        $customers = Customer::query()
            ->select('id', 'business_name', 'vat_number', 'tax_code')
            ->orderBy('business_name')
            ->get();

        /** @var \App\Models\User $editUser */
        $editUser = auth()->user();

        $atecoCodes = AtecoCode::query()
            ->whereHas('users', fn ($q) => $q->where('users.id', $editUser->id))
            ->select('id', 'code', 'description', 'profitability_coefficient')
            ->get()
            ->map(function ($ateco) use ($editUser) {
                $ateco->is_primary = $editUser
                    ->atecoCodes()
                    ->where('ateco_codes.id', $ateco->id)
                    ->first()
                    ?->pivot
                    ->is_primary ?? false;

                return $ateco;
            });

        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoiceDto,
            'customers' => $customers,
            'atecoCodes' => $atecoCodes,
            'atecoCode' => $invoice->atecoCode,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InputInvoiceDto $inputInvoiceDto, Invoice $invoice): RedirectResponse
    {
        $invoice->update($inputInvoiceDto->toModelArray());

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Fattura aggiornata con successo');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Fattura eliminata con successo');
    }

    /**
     * Get next available invoice number
     */
    public function getNextInvoiceNumber(Request $request): JsonResponse
    {
        $year = $request->input('year', date('Y'));

        // Get max invoice number for current user and year
        $maxInvoice = Invoice::query()
            ->where('invoice_number', 'LIKE', "{$year}/%")
            ->orderByRaw('CAST(SUBSTRING_INDEX(invoice_number, "/", -1) AS UNSIGNED) DESC')
            ->first();

        if ($maxInvoice) {
            // Extract number part and increment
            $parts = explode('/', $maxInvoice->invoice_number);
            $number = isset($parts[1]) ? (int) $parts[1] : 0;
            $nextNumber = $number + 1;
        } else {
            $nextNumber = 1;
        }

        $invoiceNumber = sprintf('%s/%03d', $year, $nextNumber);

        return response()->json([
            'invoice_number' => $invoiceNumber,
        ]);
    }
}
