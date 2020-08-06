<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeviceRoomTemplate extends JsonResource
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
            //"room_id"=>$this->room_id,
            'room'=>[
                'id'=>$this->room->id,
                'room_number'=>$this->room->room_number,
            ],
            // "hotel_id"=>$this->hotel_id,
            "hotel"=>[
                "id"=>$this->hotel->id,
                "hotel_name"=>$this->hotel->hotel_name,
                'phone'=>$this->hotel->phone,
            ],
            //"device_id"=>$this->device_id,
            'device'=>[
                'id'=>$this->device->id,
                'imei'=>$this->device->imei
            ],
            'stay'=>[
                'id'=>$this->stay->id,
                'tourist'=>[
                    'id'=>$this->stay->tourist->id,
                    'first_name'=>$this->stay->tourist->first_name,
                ]
            ],
            // "created_at"=>$this->created_at->timestamp,
            // "updated_at"=>$this->updated_at->timestamp,
            "ended_at"=>$this->ended_at
        ];
    }
}
