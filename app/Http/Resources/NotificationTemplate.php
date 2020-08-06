<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationTemplate extends JsonResource
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
            "image"=>$this->image,
            "begin_date"=>$this->begin_date,
            "end_date"=>$this->end_date,
            "dashboard"=>$this->dashboard,
            "description"=>$this->description,
            "color"=>$this->color,
            "created_at"=>$this->created_at->timestamp,
            "updated_at"=>$this->updated_at->timestamp
        ];
    }
}
