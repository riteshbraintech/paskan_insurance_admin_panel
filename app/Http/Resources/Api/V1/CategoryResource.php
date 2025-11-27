<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request; // ✅ Add this line
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array // ✅ keep it generic (no namespace path)
    {
        // get image url


        return [
            'id'          => $this->id,
            'title'       => $this->translation?->title ?? $this->title,
            'slug'        => $this->slug,
            'image_url'   => $this->image_url ?? null,
            // 'is_active'   => (bool) $this->is_active,
            // 'description' => $this->translation?->description,
            'meta' => [
                'title'       => $this->translation?->meta_title,
                'description' => $this->translation?->meta_description,
                'keywords'    => $this->translation?->meta_keywords,
            ],
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
