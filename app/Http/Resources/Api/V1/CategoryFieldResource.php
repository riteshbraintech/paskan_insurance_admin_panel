<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryFieldResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'htmlName'      => $this->name,
            'htmlType'          => $this->type,
            'htmlIsRequired'   => $this->is_required,
            'htmlSortOrder'    => $this->sort_order,
            'htmlLabel'         => $this->translation->label ?? $this->label,
            'htmlPlaceHolder'  => $this->translation->place_holder ?? $this->place_holder,
            // 'Options'       => $this->translation->options ?? $this->options ?? [],
            'htmlOptions'       => CategoryFieldOptionResource::collection($this->translation->options ?? $this->options ?? []),
        ];
    }
}





