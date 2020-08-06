<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StayTemplate extends JsonResource
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
            "tourist"=> [
                'id'=>$this->tourist->id,
                'first_name'=>$this->tourist->first_name
            ],
            //"device_room_id"=>$this->device_room_id,
            "device_room"=>[
                "id"=>$this->device_room->id,
                "room_id"=>$this->device_room->room_id,
                // 'created_at' => $this->device_room->created_at->timestamp
            ],
            'hotel' => [
                'id'=>$this->hotel->id,
                'hotel_name'=>$this->hotel->hotel_name
            ],
            "reserved_time_from"=>$this->reserved_time_from,
            "reserved_time_to"=>$this->reserved_time_to,
            "join_time"=>$this->join_time,
            "leave_time"=>$this->leave_time,
            // "created_at"=>$this->created_at->timestamp,
            // "updated_at"=>$this->updated_at->timestamp
         ];
    }
}
