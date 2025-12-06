<?php

namespace App\Http\Controllers;

use App\Dtos\ExpenseDto;
use App\Dtos\Input\InputExpenseDto;
use App\Dtos\Input\InputIndexDto;
use App\Dtos\PaginatedResponseDto;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\ExpenseIndexService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function __construct(
        private ExpenseIndexService $expenseIndexService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(InputIndexDto $inputIndexDto): Response
    {
        $result = $this->expenseIndexService->getExpenses($inputIndexDto);

        return Inertia::render('Expenses/Index', [
            'expenses' => PaginatedResponseDto::fromServiceResult($result),
            'filters' => $result['filters'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $expenseCategories = ExpenseCategory::availableForUser(
            $user->id,
            $user->isDemoUser() ? session()->id() : null
        )
            ->orderBy('name')
            ->get();

        return Inertia::render('Expenses/Create', [
            'expenseCategories' => $expenseCategories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InputExpenseDto $inputExpenseDto): RedirectResponse
    {
        Expense::create($inputExpenseDto->toModelArray());

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Spesa creata con successo');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense): Response
    {
        $expense->load('expenseCategory');
        $expenseDto = ExpenseDto::from($expense);

        return Inertia::render('Expenses/Show', [
            'expense' => $expenseDto,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense): Response
    {
        $expense->load('expenseCategory');
        $expenseDto = ExpenseDto::from($expense);

        /** @var \App\Models\User $editUser */
        $editUser = auth()->user();

        $expenseCategories = ExpenseCategory::availableForUser(
            $editUser->id,
            $editUser->isDemoUser() ? session()->id() : null
        )
            ->orderBy('name')
            ->get();

        return Inertia::render('Expenses/Edit', [
            'expense' => $expenseDto,
            'expenseCategories' => $expenseCategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InputExpenseDto $inputExpenseDto, Expense $expense): RedirectResponse
    {
        $expense->update($inputExpenseDto->toModelArray());

        return redirect()
            ->route('expenses.show', $expense)
            ->with('success', 'Spesa aggiornata con successo');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Spesa eliminata con successo');
    }
}
