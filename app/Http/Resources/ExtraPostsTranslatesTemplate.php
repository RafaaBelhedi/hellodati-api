<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExtraPostsTranslatesTemplate extends JsonResource
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
            'post_id'=>$this->post_id,
            'lang_iso'=>$this->lang_iso,
            'title'=>$this->title,
            'summery'=>$this->summery,
            'description'=>$this->description,
            'addition_data_1_text'=>$this->addition_data_1_text,
            "created_at"=>$this->created_at->timestamp,
            "updated_at"=>$this->updated_at->timestamp
        ];
    }
}
