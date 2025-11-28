<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->translation->title ?? $this->title,
            'subtitle'      => $this->translation->subtitle ?? $this->subtitle,
            // 'meta_title'      => $this->translation->meta_title ?? $this->meta_title,
            // 'meta_description'      => $this->translation->meta_description ?? $this->meta_description,
            // 'meta_keywords'      => $this->translation->meta_keywords ?? $this->meta_keywords,
            'image_url'     => $this->image_url ?? null,
            'created_at' => $this->created_at?->format('d-M-Y'),
        ];
    }
}
