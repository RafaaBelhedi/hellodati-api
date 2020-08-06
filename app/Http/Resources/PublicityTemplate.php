<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicityTemplate extends JsonResource
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
            "description"=>$this->description,
            "begin_date"=>$this->begin_date,
            "end_date"=>$this->end_date,
            "image"=>$this->image,
            "video"=>$this->video,
            "length"=>$this->length,
            "clicks"=>$this->clicks,
            "views"=>$this->views,
            "max_clicks"=>$this->max_clicks,
            "max_views"=>$this->max_views,
            "priority"=>$this->priority,
            "created_at"=>$this->created_at->timestamp,
            "updated_at"=>$this->updated_at->timestamp
        ];
    }
}
