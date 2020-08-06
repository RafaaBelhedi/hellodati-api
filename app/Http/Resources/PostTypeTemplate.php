<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostTypeTemplate extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            "id"=>$this->id,
            "name"=>$this->name,
            "required_colomns"=>array_filter(explode(',',$this->required_colomns)),
            "optional_columns"=>array_filter(explode(',',$this->optional_columns)),
            "preview_image"=>$this->preview_image,
            "category"=>$this->category,
            "created_at"=>$this->created_at->timestamp,
            "updated_at"=>$this->updated_at->timestamp
        ];
    }
}
