<?php

namespace App\Http\Resources;

use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'unit' => $this->unit,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
