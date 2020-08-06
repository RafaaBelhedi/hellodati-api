<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppVersionTemplate extends JsonResource
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
            "app"=>[
                "id"=>$this->app->id,
                "name"=>$this->app->name,
                "package_name"=>$this->app->package_name,
                "icon"=>$this->app->icon
            ],
            "version_name"=>$this->version_name,
            "version_code"=>$this->version_code,
            "install_url"=>$this->install_url,
            "is_live"=>($this->is_live==1),
            "created_at"=>$this->created_at->timestamp,
            "updated_at"=>$this->updated_at->timestamp
        ];
    }
}
