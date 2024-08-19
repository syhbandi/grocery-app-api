<?php

namespace App\Http\Controllers;

use App\Http\Requests\category\StoreCategoryRequest;
use App\Http\Requests\category\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Contracts\Service\Attribute\Required;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size', 10);
        $page = $request->query('page', 1);

        $query = Category::query();

        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }

        $categories = $request->has('no_paginate') ? $query->with('images')->get() : $query->with('images')->paginate(page: $page, perPage: $size);
        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $storeCategoryRequest): CategoryResource
    {
        $category = Category::create($storeCategoryRequest->all());

        if ($storeCategoryRequest->has('images')) {
            $category->images()->sync($storeCategoryRequest->images);
        }

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $updateCategoryRequest, $id): CategoryResource
    {
        $category = Category::find($id);

        if (!$category) {
            $this->notFound();
        }

        $category->update($updateCategoryRequest->all());

        if ($updateCategoryRequest->has('images')) {
            $category->images()->sync($updateCategoryRequest->images);
        }
        return new CategoryResource($category);
    }

    public function get($id): CategoryResource
    {
        $category = Category::with('images')->find($id);
        if (!$category) {
            $this->notFound();
        }
        return new CategoryResource($category);
    }

    public function products(Request $request, $id)
    {
        $size = $request->query('size', 10);
        $page = $request->query('page', 1);
        $category = Category::find($id);

        if (!$category) {
            $this->notFound();
        }

        $products = $category->products()->paginate(perPage: $size, page: $page);
        return ProductResource::collection($products);
    }

    public function delete($id)
    {
        $category = Category::find($id);
        if (!$category) $this->notFound();
        $category->delete();
        return response()->json(['message' => 'Deleted'], Response::HTTP_OK);
    }

    private function notFound()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'category not found',
        ], Response::HTTP_NOT_FOUND));
    }
}
