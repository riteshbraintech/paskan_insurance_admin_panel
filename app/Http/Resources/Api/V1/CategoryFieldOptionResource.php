<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryFieldOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $image_url  = $this->translation->image_url ?? null;
        // check if image url has default.png then return null
        if ($image_url && strpos($image_url, 'default.png') !== false) {
            $image_url = null;
        }

        return [
            'answerId'    => $this->id,
            'questionId'  => $this->field_id,
            'value'       => $this->value,
            'label'       => $this->translation->label ?? $this->label,
            'image_url'   => $image_url,
        ];
    }
}
