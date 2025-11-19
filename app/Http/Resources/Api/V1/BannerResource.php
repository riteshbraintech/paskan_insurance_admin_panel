<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
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
            'sub_title'         => $this->translation->sub_title ?? $this->sub_title,
            'description'  => $this->translation->description ?? '',
            'sort_order'    => $this->sort_order,
            'image_url'   => $this->image_url ?? null,
        ];
    }
}
