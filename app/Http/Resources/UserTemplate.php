<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\AdminController;

class UserTemplate extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    $userController = new AdminController();
    if(count($this->hotels)) {
      return [
        "id" => $this->id,
        "name" => $this->name,
        "email" => $this->email,
        "approved" => $this->approved,
        "role" => $this->role,
        "hotels" => $this->hotels,
        "sidebar_access" => $this->SideBarAccess,
        "post_access" =>$this->postaccess,
        "created_at" => $this->created_at->timestamp,
        "updated_at" => $this->updated_at->timestamp,
        "prefix" => $this->prefix,
        "gender" => $this->gender,
        "born" => $this->born,
        "phone_number" => $this->phone_number,
        "cin" => $this->cin,
        "country" => $this->country,
        "city" => $this->city,
        "zip_code" => $this->zip_code,
        "adress" => $this->adress,
        "hidden" => $this->hidden
      ];
    }
    else {
      return [
        "id" => $this->id,
        "name" => $this->name,
        "email" => $this->email,
        "approved" => $this->approved,
        "role" => $this->role,
        "hotels" => [$this->hotel],
        "sidebar_access" => $this->SideBarAccess,
        "post_access" =>$userController->getPostAccess($this->id),
        "created_at" => $this->created_at->timestamp,
        "updated_at" => $this->updated_at->timestamp,
        "prefix" => $this->prefix,
        "gender" => $this->gender,
        "born" => $this->born,
        "phone_number" => $this->phone_number,
        "cin" => $this->cin,
        "country" => $this->country,
        "city" => $this->city,
        "zip_code" => $this->zip_code,
        "adress" => $this->adress,
        "hidden" => $this->hidden
      ];
    }

    
  }
}
