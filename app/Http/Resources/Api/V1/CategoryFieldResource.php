<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryFieldResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'category_id'   => $this->category_id,
            'name'          => $this->name,
            'type'          => $this->type,
            'is_required'   => $this->is_required,
            'sort_order'    => $this->sort_order,

            'label'         => $this->label,
            'place_holder'  => $this->place_holder,
            'options'       => $this->options ?? [],

            'images' => collect($this->images ?? [])
                ->map(fn($img) => asset('public/'.$img))
                ->toArray(),


            'translation' => $this->whenLoaded('translation', function () {
                return [
                    'label'         => $this->translation->label ?? '',
                    'place_holder'  => $this->translation->place_holder ?? '',
                    'options'       => json_decode($this->translation->options ?? '[]', true),
                ];
            }),
        ];
    }
}





