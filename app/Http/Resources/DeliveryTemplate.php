<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryTemplate extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"=>$this->id,
            "hotel_id"=>$this->hotel_id,
            "hotel"=>[
                "id"=>$this->hotel->id,
                "hotel_name"=>$this->hotel->hotel_name,
                'phone'=>$this->hotel->phone,
            ],
            "title"=>$this->title,
            "created_at"=>$this->created_at->timestamp,
            "updated_at"=>$this->updated_at->timestamp
        ];
    }
}
