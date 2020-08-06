<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostTemplate extends JsonResource
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
     * Uses device lang and falls back to english by default
     */
    if ($request->input('d_lang')) {
      $d_lang = $request->input('d_lang');
    } else {
      $d_lang = "en";
    }
    $translation = $this->getTranslations($d_lang);
    return [
      "id" => $this->id,
      "hotel_id" => $this->hotel_id,
      "parent_id" => $this->parent_id,
      "role" => $this->role,
      "title" => $translation->title,
      "type" => $this->type,
      "nb_personne" => $this->nb_personne,
      "surface" => $this->surface,
      "theme_color" => $this->theme_color,
      "title_color" => $this->title_color,
      "summery_color" => $this->summery_color,
      "categories" => array_filter(explode(',', $this->categories)),
      "order_in_parent" => $this->order_in_parent,
      "contents_categories" => array_filter(explode(',', $this->contents_categories)),
      "content_manager" => $this->content_manager,
      "content_columns_count" => [
        $this->content_S_column_count,
        $this->content_M_column_count,
        $this->content_L_column_count,
        $this->content_XL_column_count
      ],
      //"icon"=>"",//must be removed (will not be used any more)
      "image" => $this->image,
      "cover" => $this->cover,
      //"images"=>array(),//must be removed (will not be used any more)
      "summery" => $translation->summery,
      "description" => $translation->description,
      "rate" => $this->rate,
      "has_location" => ($this->has_location == 1),
      "location" => $this->location,
      "reservation" => $this->reservation,
      "location_coordinates" => array_filter(explode(',', $this->location_coordinates)),
      "phone" => $this->phone,
      "has_opening_time" => ($this->has_opening_time == 1),
      "opening_time" => json_decode($this->opening_time, true),
      "has_price" => ($this->has_price == 1),
      "price" => $this->price,
      "price_promo" => $this->price_promo,
      "accept_reservation" => $this->accept_reservation,
      "accept_order" => $this->accept_order,
      "col_span" => $this->col_span,
      "aspect_ratio" => $this->aspect_ratio,
      "aspect_preserve" => $this->aspect_preserve,
      "addition_data_1_icon" => $this->addition_data_1_icon,
      "addition_data_1_text" => $translation->addition_data_1_text,
      "is_accessible" => ($this->is_accessible == 1),
      "has_head" => ($this->has_head == 1),
      "has_body" => ($this->has_body == 1),
      "has_footer" => ($this->has_footer == 1),
      "state" => $this->state,
      "pre_def_posts" => $this->pre_def_posts,
      "nbr_views" => $this->nbr_views,
      "layout_xml_template" => $this->layout_xml_template,
      "tripadvisor_id" => $this->tripadvisor_id,
      "tripadvisor_url" => intval($this->tripadvisor_id) > 0 ? "https://www.tripadvisor.com/UserReviewEdit-d" . $this->tripadvisor_id : "",
      "display_lang" => $translation->lang_iso,
      "review_average" => $this->review_average,
      "created_at" => $this->created_at->timestamp,
      "updated_at" => $this->updated_at->timestamp,
      "number_of_orders" => $this->number_of_orders,
      "number_of_reservations" => $this->number_of_reservations,
      "service_image" => $this->service_image,
      "promo_start_date" => $this->promo_start_date,
      "promo_end_date" => $this->promo_end_date,
      "promo_start_hour" => $this->promo_start_hour,
      "promo_end_hour" => $this->promo_end_hour,
      "post_logo" => $this->post_logo,
      "isChatAvailable" => $this->isChatAvailable,
      "ordered_or_reserved" => $this->ordered_or_reserved,
      "hidden" => $this->hidden
    ];
  }
}
