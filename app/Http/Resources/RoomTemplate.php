<?php

namespace App\Http\Resources;

use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomTemplate extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $tourist_name="";
      $device_imei = "";
      try{
        $tourist_name=$this->device_rooms[0]->stay->tourist->first_name.' '.$this->device_rooms[0]->stay->tourist->last_name;
              $device_imei=$this->device_rooms[0]->device->imei;
          //return parent::toArray($request);

      }catch(Exception $e){

      }
        return [
            "id"=>$this->id,
            'room_number'=>$this->room_number,
            'section'=>$this->section,
            'floor'=>$this->floor,
            'capacity'=>$this->capacity,
            'status'=>$this->status,
            'linked_to_device'=>$this->linked_to_device,
            'hotel_id'=>$this->hotel_id,
            "hotel"=>[
                "id"=>$this->hotel->id,
                "hotel_name"=>$this->hotel->hotel_name
            ],
            "device_rooms"=>$this->device_rooms,
            "tourist_name"=>$tourist_name,
            "device_imei"=>$device_imei,
            "created_at"=>$this->created_at->timestamp,
            "updated_at"=>$this->updated_at->timestamp
        ];
    }
}
