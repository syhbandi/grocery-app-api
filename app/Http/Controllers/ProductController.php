<?php

namespace App\Http\Controllers;

use App\Http\Requests\product\StoreProductRequest;
use App\Http\Requests\product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size', 10);
        $page = $request->query('page', 1);

        $products = Product::with('categories')->paginate(page: $page, perPage: $size);
        return ProductResource::collection($products);
    }

    public function get($id)
    {
        $product = Product::with('categories')->find($id);

        if (!$product) {
            $this->notFound();
        }
        return new ProductResource($product);
    }

    public function store(StoreProductRequest $storeProductRequest): ProductResource
    {
        $product = new Product($storeProductRequest->all());
        $product->save();

        if ($storeProductRequest->has('category_ids')) {
            $product->categories()->sync($storeProductRequest->category_ids);
        }

        $product->categories()->sync($storeProductRequest->category_ids);

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $updateProductRequest, $id)
    {
        $data = $updateProductRequest->validated();
        $product = Product::find($id);

        if (!$product) {
            $this->notFound();
        }


        $product->update($data);

        if ($updateProductRequest->has('category_ids')) {
            $product->categories()->sync($updateProductRequest->category_ids);
        }

        return new ProductResource($product);
    }

    public function delete($id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            $this->notFound();
        }

        $product->delete();

        return response()->json(['message' => 'Deleted'], Response::HTTP_OK);
    }

    private function notFound()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Product not found',
        ], Response::HTTP_NOT_FOUND));
    }
}
