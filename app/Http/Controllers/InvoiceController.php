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
use App\Services\InvoiceNumberService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceIndexService $invoiceIndexService,
        private InvoiceNumberService $invoiceNumberService,
        private SettingService $settingService,
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
        $settings = $this->settingService->getActiveSettings(auth()->user());
        $customers = Customer::query()
            ->select('id', 'business_name', 'vat_number', 'tax_code')
            ->orderBy('business_name')
            ->get();

        // Get ATECO codes for the authenticated user (automatically filtered by HasUserScope)
        $atecoCodes = AtecoCode::query()
            ->select('id', 'ateco_code', 'description', 'profitability_coeff', 'is_primary')
            ->orderBy('is_primary', 'desc')
            ->orderBy('ateco_code')
            ->get();

        return Inertia::render('Invoices/Create', [
            'customers' => $customers,
            'atecoCodes' => $atecoCodes,
            'settings' => $settings,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InputInvoiceDto $inputInvoiceDto): RedirectResponse
    {
        DB::transaction(function () use ($inputInvoiceDto) {
            $invoice = Invoice::create($inputInvoiceDto->toModelArray());

            // Create invoice items
            foreach ($inputInvoiceDto->items as $index => $itemDto) {
                $itemData = $itemDto->toModelArray();
                $itemData['sort_order'] = $index;
                $invoice->items()->create($itemData);
            }
        });

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Fattura creata con successo');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): Response
    {
        $invoice->load(['customer', 'atecoCode', 'items.product']);
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
        $settings = $this->settingService->getActiveSettings(auth()->user());

        $customers = Customer::query()
            ->select('id', 'business_name', 'vat_number', 'tax_code')
            ->orderBy('business_name')
            ->get();

        // Get ATECO codes for the authenticated user (automatically filtered by HasUserScope)
        $atecoCodes = AtecoCode::query()
            ->select('id', 'ateco_code', 'description', 'profitability_coeff', 'is_primary')
            ->orderBy('is_primary', 'desc')
            ->orderBy('ateco_code')
            ->get();

        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoiceDto,
            'customers' => $customers,
            'atecoCodes' => $atecoCodes,
            'atecoCode' => $invoice->atecoCode,
            'settings' => $settings,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InputInvoiceDto $inputInvoiceDto, Invoice $invoice): RedirectResponse
    {
        DB::transaction(function () use ($inputInvoiceDto, $invoice) {
            $invoice->update($inputInvoiceDto->toModelArray());

            // Delete old items and create new ones
            $invoice->items()->delete();

            foreach ($inputInvoiceDto->items as $index => $itemDto) {
                $itemData = $itemDto->toModelArray();
                $itemData['sort_order'] = $index;
                $invoice->items()->create($itemData);
            }
        });

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
        $invoiceNumber = $this->invoiceNumberService->generateNextNumber($request->user(), (int) $year);

        return response()->json([
            'invoice_number' => $invoiceNumber,
        ]);
    }
}
