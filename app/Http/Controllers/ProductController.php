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

        $query = Product::query();

        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }

        $products = $query->with('categories')->with('images')->paginate(page: $page, perPage: $size);
        return ProductResource::collection($products);
    }

    public function get($id)
    {
        $product = Product::with('categories')->with('images')->find($id);

        if (!$product) {
            $this->notFound();
        }
        return new ProductResource($product);
    }

    public function store(StoreProductRequest $storeProductRequest): ProductResource
    {
        $product = new Product($storeProductRequest->all());
        $product->save();

        if ($storeProductRequest->has('categories')) {
            $product->categories()->sync($storeProductRequest->categories);
        }

        if ($storeProductRequest->has('images')) {
            $product->images()->sync($storeProductRequest->images);
        }

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

        if ($updateProductRequest->has('categories')) {
            $product->categories()->sync($updateProductRequest->categories);
        }

        if ($updateProductRequest->has('images')) {
            $product->images()->sync($updateProductRequest->images);
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
