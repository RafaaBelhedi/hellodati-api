<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Post;

class PlannedNotificationsTemplate extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {

    /**
     * If this notification is about a post determine the parent service
     */
     $post = Post::find($this->post_id);
    $title = '';
    if ($post) {
      while ($post->parent_id != null) {
        $post_aux = $post;
        $post = Post::find($post->parent_id);
      }
      $title = $post_aux->getTranslations($this->privileges['d_lang'])['title'];
    }

    return [
      "id" => $this->id,
      "imei" => $this->imei,
      "title" => $this->title,
      "summery" => $this->summery,
      "image_right_url" => $this->image_right_url,
      "post_id" => $this->post_id,
      "image" => $this->image,
      "pub" => $this->pub,
      "expandeble_image_url" => $this->expandeble_image_url,
      "expandeble_text" => $this->expandeble_text,
      "sound" => $this->sound,
      "promo" => $this->promo,
      "inform_no_context" => [
        "image" => $this->inform_no_context_image,
        "title" => $this->inform_no_context_title,
        "description" => $this->inform_no_context_description,
      ],
      "post_reference" => [
        "fragment" => $this->post_reference_fragment,
        "post_id" => $this->post_reference_post_id,
      ],
      "seen" => $this->seen,
      "promo" => $this->promo,
      "layout_xml_template" => $this->layout_xml_template,
      "parent_title" => $title,
      "created_at" => $this->created_at->timestamp,
      "updated_at" => $this->updated_at->timestamp
    ];
  }
}
