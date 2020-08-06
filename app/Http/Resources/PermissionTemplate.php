<?php

namespace App\Http\Resources;

use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionTemplate extends JsonResource
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
      "text" => $this->text,
      "id" => $this->id,
      "level" => $this->level,
      "permission_group_id" => $this->permission_group_id,
      "permission_id" => $this->permission_id,
      "created_at" => $this->created_at->timestamp,
      "updated_at" => $this->updated_at->timestamp
    ];
  }
}
