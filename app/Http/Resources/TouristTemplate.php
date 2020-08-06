<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TouristTemplate extends JsonResource
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
            "cin_number"=>$this->cin_number,
            "passport_number"=>$this->passport_number,
            "first_name"=>$this->first_name,
            "last_name"=>$this->last_name,
            "age" => $this->age,
            "prefix_name"=>$this->prefix_name,
            "born"=>$this->born,
            "check_in"=>$this->check_in,
            "check_out"=>$this->check_out,
            "gender"=>$this->gender,
            "languages"=>array_filter(explode(',',$this->languages)),
            "app_lang"=>$this->app_lang,
            "country"=>$this->country,
            "city"=>$this->city,
            "zip_code"=>$this->zip_code,
            "address_1"=>$this->address_1,
            "address_2"=>$this->address_2,
            "email"=>$this->email,
            "image"=>$this->image,
            "email_status"=>$this->email_status,
            "phone_number"=>$this->phone_number,
            "work_phone_number"=>$this->work_phone_number,
            "company"=>$this->company,
            "user_id"=>$this->user_id,
            "password"=>$this->password,
            "is_resident"=>$this->is_resident,
            "stay"=>[
                'id' => $this->stay->id,
                'hotel' => [
                    'id'=>$this->stay->hotel->id,
                    'hotel_name'=>$this->stay->hotel->hotel_name
                ],
                'device_room' =>[
                    'id'=>$this->stay->device_room->id,
                    'device'=>[
                        'id'=>$this->stay->device_room->device->id,
                        'imei'=>$this->stay->device_room->device->imei
                    ],
                    'room'=>[
                        'id'=>$this->stay->device_room->room->id,
                        'room_number'=>$this->stay->device_room->room->room_number,
                    ]
                ],
                'reserved_time_from' => $this->stay->reserved_time_from,
                'reserved_time_to' => $this->stay->reserved_time_to,
            ],
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
            "check_in" => $this->check_in,
            "check_out" => $this->check_out,
            "leaved" => $this->leaved
        ];
    }
}
