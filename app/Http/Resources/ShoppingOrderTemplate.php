<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DeviceTemplate;
use Exception;

class ShoppingOrderTemplate extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    if ($request->input('d_lang')) {
      $d_lang = $request->input('d_lang');
    } else {
      $d_lang = "en";
    }
    $translation = $this->post->getTranslations($d_lang);
    $room = null;
    $room_number = null;
    $imei = null;
    try {
      $imei = $this->tourist->stay->device_room->device->imei;
      $room = $this->tourist->stay->device_room->room_id;
      $room_number = $this->tourist->stay->device_room->room->room_number;
    } catch (Exception $e) { 
      print_r($e);
    }
    //return parent::toArray($request);
    return [
      "id" => $this->id,
      "seen" => $this->seen,
      "reason" => $this->reason,
      "tourist_full_name" => $this->tourist->first_name . ' ' . $this->tourist->last_name,
      "tourist" => [
        "id" => $this->tourist->id,
        "name" => $this->tourist->first_name . ' ' . $this->tourist->last_name
      ],
      "device_imei" => $imei,
      "room" => $room,
      "room_number" => $room_number,
      "post_id" => $this->post_id,
      "post" => [
        "id" => $this->post->id,
        "title" => $translation->title,
        "categories" => array_filter(explode(',', $this->post->categories)),
        "has_price" => true,
        "price" => $this->post->price,
        "price_promo" => $this->post->price_promo
      ],
      "qt" => $this->qt,
      "comment" => $this->comment,
      "status" => $this->status,
      "hotel_id" => $this->hotel_id,
      "delay" => $this->delay,
      "delivery_place_id" => $this->delivery_place_id,
      "reservation" => $this->reservation,
      "reservation_time" => $this->reservation_time,
      "created_at" => $this->created_at->timestamp,
      "updated_at" => $this->updated_at->timestamp
    ];
  }
}
