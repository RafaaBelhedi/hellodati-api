<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Post;

class PasswordResetTemplate extends JsonResource
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
      "user_id"=>$this->user_id,
      "created_at" => $this->created_at->timestamp,
      "updated_at" => $this->updated_at->timestamp
    ];
  }
}
