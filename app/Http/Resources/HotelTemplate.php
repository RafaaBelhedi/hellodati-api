<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HotelTemplate extends JsonResource
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
      "id" => $this->id,
      'user_id' => $this->user_id,
      'hotel_name' => $this->hotel_name,
      'city' => $this->city,
      'country' => $this->country,
      'phone' => $this->phone,
      'stars' => $this->stars,
      'tokens' => $this->tokens,
      'facebook' => $this->facebook,
      'twitter' => $this->twitter,
      'check_in' => $this->check_in,
      'check_out' => $this->check_out,
      'address' => $this->address,
      'youtube' => $this->youtube,
      'chain' => $this->chain,
      'region' => $this->region,
      'continent' => $this->continent,
      'email' => $this->email,
      'trip_advisor_url' => $this->trip_advisor_url,
      'root_post_id' => $this->root_post_id,
      "created_at" => $this->created_at->timestamp,
      "updated_at" => $this->updated_at->timestamp,
      "adress_print" => $this->adress_print
    ];
  }
}
