<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostReviewTemplate extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->tourist->last_name)
        $name = $this->tourist->first_name . " " . $this->tourist->last_name;
        else $name = $this->tourist->first_name;
        //return parent::toArray($request);
        return [
            "id"=>$this->id,
            "post_id"=>$this->post_id,
            "tourist_id"=>$this->tourist_id,
            "tourist_name"=>$name,
            "tourist_country"=>$this->tourist->country,
            "tourist_image"=>$this->tourist->image,
            "rating"=>$this->rating,
            "comment"=>$this->comment,
            "hotel_id"=>$this->hotel_id,
            "created_at"=>$this->created_at->timestamp,
            "updated_at"=>$this->updated_at->timestamp,
            "date"=>$this->created_at->format('jS F Y h:i:s A'),
        ];
    }
}
