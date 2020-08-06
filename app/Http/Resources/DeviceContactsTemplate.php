<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeviceContactsTemplate extends JsonResource
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
            "hotel_id"=>$this->hotel_id,
            "phone"=>$this->phone,
            "number"=>$this->number,
            "intra_flotte"=>$this->intra_flotte,
            "device_room"=>[
                "id"=>$this->device_room->id,
                "room"=>[
                    "id"=>$this->device_room->room->id,
                    "room_number"=>$this->device_room->room->room_number,
                ]
            ],
            "created_at"=>$this->created_at->timestamp,
            "updated_at"=>$this->updated_at->timestamp
        ];
    }
}
