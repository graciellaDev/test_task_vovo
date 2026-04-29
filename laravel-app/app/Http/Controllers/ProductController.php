<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage < 1) {
            $perPage = 1;
        } elseif ($perPage > 100) {
            $perPage = 100;
        }

        $name = trim((string) $request->query('name', ''));
        $priceFromRaw = $request->query('price_from');
        $priceToRaw = $request->query('price_to');

        $priceFrom = is_numeric($priceFromRaw) ? (float) $priceFromRaw : null;
        $priceTo = is_numeric($priceToRaw) ? (float) $priceToRaw : null;

        if ($priceFrom !== null && $priceFrom < 0) {
            $priceFrom = 0.0;
        }
        if ($priceTo !== null && $priceTo < 0) {
            $priceTo = 0.0;
        }
        if ($priceFrom !== null && $priceTo !== null && $priceFrom > $priceTo) {
            [$priceFrom, $priceTo] = [$priceTo, $priceFrom];
        }

        $isStock = $request->query('in_stock');

        $rating = $request->query('rating_from');

        $categoryId = $request->query('category_id');

        $sort = (string) $request->query('sort', 'newest');

        $products = Product::query()
            ->when($name !== '', function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            // ->when($name !== '', function ($query) use ($name) {
            //     $query->whereRaw('MATCH(name) AGAINST (? IN BOOLEAN MODE)', [$name]);
            // })
            ->when($priceFrom !== null, function ($query) use ($priceFrom) {
                $query->where('price', '>=', $priceFrom);
            })
            ->when($priceTo !== null, function ($query) use ($priceTo) {
                $query->where('price', '<=', $priceTo);
            })
            ->when($isStock !== null, function ($query) use ($isStock) {
                $query->where('in_stock', (bool)$isStock);
            })
            ->when($rating !== null, function ($query) use ($rating) {
                $query->where('rating', '>=', $rating);
            })
            ->when($categoryId !== null, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($sort === 'price_asc', function ($query) {
                $query->orderBy('price', 'asc')->orderByDesc('id');
            })
            ->when($sort === 'price_desc', function ($query) {
                $query->orderBy('price', 'desc')->orderByDesc('id');
            })
            ->when($sort === 'rating_desc', function ($query) {
                $query->orderBy('rating', 'desc')->orderByDesc('id');
            })
            ->when(
                !in_array($sort, ['price_asc', 'price_desc', 'rating_desc', 'newest'], true) || $sort === 'newest',
                function ($query) {
                    $query->orderByDesc('id');
                }
            )
            ->paginate($perPage)
            ->appends($request->query())
            ;

        return response()->json($products);
    }
}
