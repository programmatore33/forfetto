<?php

namespace App\Http\Controllers;

use App\Dtos\Input\InputIndexDto;
use App\Dtos\Input\InputProductDto;
use App\Dtos\PaginatedResponseDto;
use App\Dtos\ProductDto;
use App\Models\Product;
use App\Services\ProductIndexService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        private ProductIndexService $productIndexService
    ) {}

    /**
     * Display a listing of products
     */
    public function index(InputIndexDto $inputIndexDto): Response
    {
        $result = $this->productIndexService->getProducts($inputIndexDto);

        return Inertia::render('Products/Index', [
            'products' => PaginatedResponseDto::fromServiceResult($result),
            'filters' => $result['filters'],
        ]);
    }

    /**
     * Show the form for creating a new product
     */
    public function create(): Response
    {
        return Inertia::render('Products/Create');
    }

    /**
     * Store a newly created product
     */
    public function store(InputProductDto $dto)
    {
        $product = Product::create($dto->toModelArray());

        return redirect()
            ->route('products.index')
            ->with('success', 'Prodotto creato con successo.');
    }

    /**
     * Display the specified product
     */
    public function show(Product $product): Response
    {
        return Inertia::render('Products/Show', [
            'product' => ProductDto::fromModel($product),
        ]);
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('Products/Edit', [
            'product' => ProductDto::fromModel($product),
        ]);
    }

    /**
     * Update the specified product
     */
    public function update(InputProductDto $dto, Product $product)
    {
        $product->update($dto->toModelArray());

        return redirect()
            ->route('products.index')
            ->with('success', 'Prodotto aggiornato con successo.');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Prodotto eliminato con successo.');
    }

    /**
     * Search products for autocomplete
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        $products = Product::query()
            ->where(fn ($q) => $q->where('name', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%"))
            ->orderBy('name')
            ->limit(10)
            ->get();

        return ProductDto::collect($products);
    }
}
