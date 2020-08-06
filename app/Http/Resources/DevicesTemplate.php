<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Exception;

class DevicesTemplate extends JsonResource
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
      'imei' => $this->imei,
      'fcm_token' => $this->fcm_token,
      'app_version_code' => $this->app_version_code,
      'app_lang' => $this->app_lang,
      "phone" => $this->phone,
      'status' => $this->status,
      'data_time' => $this->data_time,
      'data_limit' => $this->data_limit,
      'call_time' => $this->call_time,
      'intra_flotte' => $this->intra_flotte,
      'call_limit' => $this->call_limit,
      "hotel_id" => $this->hotel_id,
      'time_last_activity' => $this->time_last_activity,
      'device_info' => $this->device_info,
      "created_at" => $this->created_at,
      "updated_at" => $this->updated_at
    ];
  }
}
