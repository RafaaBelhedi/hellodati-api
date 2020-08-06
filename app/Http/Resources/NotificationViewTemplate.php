<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationViewTemplate extends JsonResource
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
            "notification_id"=>$this->notification_id,
            "device_id"=>$this->device_id,
            'device'=>[
                'id'=>$this->device->id,
                'imei'=>$this->device->imei
            ],
            "hotel_id"=>$this->hotel_id,
            "hotel"=>[
                "id"=>$this->hotel->id,
                "hotel_name"=>$this->hotel->hotel_name,
                'phone'=>$this->hotel->phone,
            ],
            "created_at"=>$this->created_at->timestamp,
            "updated_at"=>$this->updated_at->timestamp
        ];
    }
}
