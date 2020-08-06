<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SideBarAccessResource extends JsonResource
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
            "id"=>$this->id,
            "user_id"=>$this->user_id,
            "services" => $this->services,
            "devices" => $this->devices,
            "statistics" => $this->statistics,
            "employees" => $this->employees,
            "guests" => $this->guests,
            "history" => $this->history,
            "orders_reservations" => $this->orders_reservations,
            "rooms" => $this->rooms,
            "notifications" => $this->notifications,
            "chat" => $this->chat,
            "created_at"=>$this->created_at->timestamp,
            "updated_at"=>$this->updated_at->timestamp,
            
        ];
    }
}
