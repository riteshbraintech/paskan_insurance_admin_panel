<?php

namespace App\Http\Resources\Api\v1;

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
            'title'          => $this->title,
            'sort_order'    => $this->sort_order,

            'image_url'   => $this->image_url ?? null,


            'translation' => $this->whenLoaded('translation', function () {
                return [
                    'title'         => $this->translation->title ?? '',
                    'description'  => $this->translation->description ?? '',
                ];
            }),
        ];
    }
}
