<?php

namespace App\Http\Controllers;

use App\Dtos\InputIndexDto;
use App\Models\Customer;
use App\Services\CustomerIndexService;
use Illuminate\Http\Request;
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
            'customers' => [
                'data' => $result['data'],
                'total' => $result['meta']['total'],
                'per_page' => $result['meta']['per_page'],
                'current_page' => $result['meta']['current_page'],
                'last_page' => $result['meta']['last_page'],
                'from' => $result['meta']['from'],
                'to' => $result['meta']['to'],
            ],
            'filters' => $result['filters'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
