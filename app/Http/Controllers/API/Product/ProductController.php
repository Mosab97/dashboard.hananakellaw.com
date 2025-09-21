<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category_id = $request->get('category_id', null);
        $products = Product::where(['active' => true, 'restaurant_id' => getFirstRestaurant()->id])->when(isset($category_id), function ($query) use ($category_id) {
            $query->where('category_id', $category_id);
        })->get();

        return apiSuccess(ProductResource::collection($products));
    }

    public function show(Product $product)
    {
        return apiSuccess(new ProductResource($product->load('sizes')));
    }
}
