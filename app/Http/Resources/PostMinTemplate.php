<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostMinTemplate extends JsonResource
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
                    /*"hotel_id"=>$this->hotel_id,
                    "title"=>$this->title,
                    "type"=>$this->type,
                    "categories"=>array_filter(explode(',',$this->categories)),
                    "order_in_parent"=>$this->order_in_parent,
                    "contents_categories"=>array_filter(explode(',',$this->contents_categories)),
                    "content_manager"=>$this->content_manager,
                    "content_columns_count"=>[
                        $this->content_S_column_count,
                        $this->content_M_column_count,
                        $this->content_L_column_count,
                        $this->content_XL_column_count
                    ],
                    "icon"=>"",//must be removed (will not be used any more)
                    "image"=>$this->image,
                    "cover"=>$this->cover,
                    "images"=>array(),//must be removed (will not be used any more)
                    "summery"=>$this->summery,
                    "description"=>$this->description,
                    "rate"=>$this->rate,
                    "has_location"=>($this->has_location==1),
                    "location"=>$this->location,
                    "location_coordinates"=>array_filter(explode(',',$this->location_coordinates)),
                    "phone"=>$this->phone,
                    "has_opening_time"=>($this->has_opening_time==1),
                    "opening_time"=>json_decode($this->opening_time,true),
                    "has_price"=>($this->has_price==1),
                    "price"=>$this->price,
                    "price_promo"=>$this->price_promo,
                    "col_span"=>$this->col_span,
                    "aspect_ratio"=>$this->aspect_ratio,
                    "aspect_preserve"=>$this->aspect_preserve,
                    "addition_data_1_icon"=>$this->addition_data_1_icon,
                    "addition_data_1_text"=>$this->addition_data_1_text,
                    "is_accessible"=>($this->is_accessible==1),
                    "has_head"=>($this->has_head==1),
                    "has_body"=>($this->has_body==1),
                    "has_footer"=>($this->has_footer==1),
                    "state"=>$this->state,
                    "created_at"=>$this->created_at->timestamp,*/
                    "updated_at"=>$this->updated_at->timestamp
        ];
    }
}
