<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Room;


class DemandResource extends JsonResource
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
        "type" => $this->type,
        "status" => $this->status,
        "comment" => $this->comment,
        "room_number" => $this->room->room_number,
        "created_at" => $this->created_at,
        "updated_at" => $this->updated_at,
      ];
    } 
}
