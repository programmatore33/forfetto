<?php

namespace App\Http\Controllers;

use App\Dtos\CustomerDto;
use App\Dtos\Input\InputCustomerDto;
use App\Dtos\Input\InputIndexDto;
use App\Dtos\PaginatedResponseDto;
use App\Models\Customer;
use App\Services\CustomerIndexService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerIndexService $customerIndexService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(InputIndexDto $inputIndexDto): Response
    {
        $result = $this->customerIndexService->getCustomers($inputIndexDto);

        return Inertia::render('Customers/Index', [
            'customers' => PaginatedResponseDto::fromServiceResult($result),
            'filters' => $result['filters'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Customers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InputCustomerDto $inputCustomerDto): RedirectResponse
    {
        Customer::create($inputCustomerDto->toModelArray());

        return redirect()
            ->route('customers.index')
            ->with('success', 'Cliente creato con successo');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): Response
    {
        $customerDto = CustomerDto::from($customer);

        return Inertia::render('Customers/Show', [
            'customer' => $customerDto,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): Response
    {
        $customerDto = CustomerDto::from($customer);

        return Inertia::render('Customers/Edit', [
            'customer' => $customerDto,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InputCustomerDto $inputCustomerDto, Customer $customer): RedirectResponse
    {
        $customer->update($inputCustomerDto->toModelArray());

        return redirect()
            ->route('customers.index')
            ->with('success', 'Cliente aggiornato con successo');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Cliente eliminato con successo');
    }
}
