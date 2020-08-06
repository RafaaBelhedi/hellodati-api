<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use App\OpenTimeWeek;
use DB;
use App\PostReview;

class Post extends Model
{

  public function hotel()
  {
    return $this->belongsTo('App\Hotel')->withDefault();
  }

  public function shopping_orders()
  {
    return $this->hasMany('App\ShoppingOrder');
  }

  public function translates()
  {
    return $this->hasMany('App\PostsTranslate');
  }

  public function getTranslations($lang)
  {
    $translates = $this->translates;
    $englishTranslate = null;
    for ($i = 0; $i < count($translates); $i++) {
      if ($translates[$i]["lang_iso"] == $lang) {
        return $translates[$i];
      }
      if ($translates[$i]["lang_iso"] == "en") {
        $englishTranslate = $translates[$i];
      }
    }
    if ($englishTranslate != null) {
      return $englishTranslate;
    } else if (count($translates) > 0) {
      return $translates[0];
    } else {
      return new PostsTranslate();
    }
  }

  public function setSelf($request)
  {

    if (array_key_exists("hotel_id", $request)) {

      try {
        $hotel = Hotel::findOrFail(intval($request["hotel_id"]));
        $hotel_id = $hotel->id;
      } catch (ModelNotFoundException $e) {
        $hotel_id = null;
      }
      $this->hotel_id = $hotel_id;
    }
    if (array_key_exists("parent_id", $request)) {
      $this->parent_id = intval($request["parent_id"]);

      try {
        $parent = Post::findOrFail(intval($request["parent_id"]));

        if ($parent->hotel_id == $this->hotel_id) {
          $parent_id = $parent->id;
        } else {
          $parent_id = null;
        }
      } catch (ModelNotFoundException $e) {
        $parent_id = null;
      }
      $this->parent_id = $parent_id;
    }
    if (array_key_exists("type", $request)) {
      $this->type = intval($request["type"]);
    }
    if (array_key_exists("nb_personne", $request)) {
      $this->nb_personne = intval($request["nb_personne"]);
    }
    if (array_key_exists("surface", $request)) {
      $this->surface = intval($request["surface"]);
    }
    if (array_key_exists("role", $request)) {
      $this->role = intval($request["role"]);
    }
    if (array_key_exists("accept_reservation", $request)) {
      $this->accept_reservation = intval($request["accept_reservation"]);
    }
    if (array_key_exists("accept_order", $request)) {
      $this->accept_order = intval($request["accept_order"]);
    }
    if (array_key_exists("reservation", $request)) {
      $this->reservation = intval($request["reservation"]);
    }
    if (array_key_exists("layout_xml_template", $request)) {
      $this->layout_xml_template = intval($request["layout_xml_template"]);
    }
    if (array_key_exists("title_color", $request)) {
      $this->title_color = $request["title_color"];
    }
    if (array_key_exists("promo_start_date", $request) && $request["promo_start_date"] != 'null') {
      $this->promo_start_date = $request["promo_start_date"];
    }
    if (array_key_exists("promo_end_date", $request) && $request["promo_end_date"] != 'null') {
      $this->promo_end_date = $request["promo_end_date"];
    }
    if (array_key_exists("promo_start_hour", $request)) {
      $this->promo_start_hour = $request["promo_start_hour"];
    }
    if (array_key_exists("promo_end_hour", $request)) {
      $this->promo_end_hour = $request["promo_end_hour"];
    }
    if (array_key_exists("summery_color", $request)) {
      $this->summery_color = intval($request["summery_color"]);
    }
    if (array_key_exists("categories", $request)) {
      $this->categories = Utils::ArrayToFiltredStringOfArray($request["categories"]);
    }
    if (array_key_exists("order_in_parent", $request)) {
      $this->order_in_parent = intval($request["order_in_parent"]);
      if ($this->order_in_parent < 1) {
        $this->order_in_parent = null;
      }
    }
    if (array_key_exists("contents_categories", $request)) {
      $this->contents_categories = Utils::ArrayToFiltredStringOfArray($request["contents_categories"]);
    }
    if (array_key_exists("content_manager", $request)) {
      $this->content_manager = intval($request["content_manager"]);
    }
    if (array_key_exists("content_S_column_count", $request)) {
      $this->content_S_column_count = intval($request["content_S_column_count"]);
    }
    if (array_key_exists("content_M_column_count", $request)) {
      $this->content_M_column_count = intval($request["content_M_column_count"]);
    }
    if (array_key_exists("content_L_column_count", $request)) {
      $this->content_L_column_count = intval($request["content_L_column_count"]);
    }
    if (array_key_exists("content_XL_column_count", $request)) {
      $this->content_XL_column_count = intval($request["content_XL_column_count"]);
    }
    if (array_key_exists("image", $request)) {
      $this->image = filter_var($request["image"], FILTER_SANITIZE_URL);
    }
    if (array_key_exists("cover", $request)) {
      $this->cover = filter_var($request["cover"], FILTER_SANITIZE_URL);
    }
    if (array_key_exists("rate", $request)) {
      $this->rate = floatval($request["rate"]);
    }
    if (array_key_exists("ordered_or_reserved", $request)) {
      $this->ordered_or_reserved = $request["ordered_or_reserved"];
    }
    if (array_key_exists("theme_color", $request)) {
      $this->theme_color = floatval($request["theme_color"]);
    }
    if (array_key_exists("has_location", $request)) {
      if ($request["has_location"] === true || $request["has_location"] === "true"  || $request["has_location"] == 1) {
        $this->has_location = 1;
      } else if ($request["has_location"] === false || $request["has_location"] === "false"  || $request["has_location"] == 0) {
        $this->has_location = 0;
      }
    }
    if (array_key_exists("location", $request)) {
      $this->location = preg_replace(Utils::$preg_replace['paragraphe'], '', $request["location"]);
    }
    if (array_key_exists("location_coordinates", $request)) {
      if (!is_array($request["location_coordinates"])) {
        $location_coordinates = explode(',', $request["location_coordinates"]);
      } else {
        $location_coordinates = $request["location_coordinates"];
      }
      $location_coordinates = array_filter(preg_replace("/[^.[:alnum:]]/u", '', $location_coordinates));
      if (count($location_coordinates) == 2 || count($location_coordinates) == 0) {
        $this->location_coordinates = implode(',', $location_coordinates);
      }
    }
    if (array_key_exists("phone", $request)) {
      $this->phone = preg_replace(Utils::$preg_replace['phone'], '', $request["phone"]);
    }
    if (array_key_exists("has_price", $request)) {
      if ($request["has_price"] === true || $request["has_price"] === "true"  || $request["has_price"] == 1) {
        $this->has_price = 1;
      } else if ($request["has_price"] === false || $request["has_price"] === "false"  || $request["has_price"] == 0) {
        $this->has_price = 0;
      }
    }
    if (array_key_exists("price", $request)) {
      $this->price = floatval($request["price"]);
    }
    if (array_key_exists("post_logo", $request)) {
      $this->post_logo = $request["post_logo"];
    }
    if (array_key_exists("price_promo", $request)) {
      $this->price_promo = floatval($request["price_promo"]);
    }
    if (array_key_exists("col_span", $request)) {
      $this->col_span = intval($request["col_span"]);
    }
    if (array_key_exists("aspect_ratio", $request)) {
      $this->aspect_ratio = preg_replace("/[^:.[:alnum:]]/u", '', $request["aspect_ratio"]);
    }
    if (array_key_exists("aspect_preserve", $request)) {
      $this->aspect_preserve = preg_replace(Utils::$preg_replace['paragraphe'], '', $request["aspect_preserve"]);
    }
    if (array_key_exists("addition_data_1_icon", $request)) {
      $this->addition_data_1_icon = filter_var($request["addition_data_1_icon"], FILTER_SANITIZE_URL);
    }
    if (array_key_exists("is_accessible", $request)) {
      if ($request["is_accessible"] === true || $request["is_accessible"] === "true" || $request["is_accessible"] == 1) {
        $this->is_accessible = 1;
      } else if ($request["is_accessible"] === false || $request["is_accessible"] === "false"  || $request["is_accessible"] == 0) {
        $this->is_accessible = 0;
      }
    }
    if (array_key_exists("has_head", $request)) {
      if ($request["has_head"] === true || $request["has_head"] === "true"  || $request["has_head"] == 1) {
        $this->has_head = 1;
      } else if ($request["has_head"] === false || $request["has_head"] === "false"  || $request["has_head"] == 0) {
        $this->has_head = 0;
      }
    }
    if (array_key_exists("has_body", $request)) {
      if ($request["has_body"] === true || $request["has_body"] === "true"  || $request["has_body"] == 1) {
        $this->has_body = 1;
      } else if ($request["has_body"] === false || $request["has_body"] === "false"  || $request["has_body"] == 0) {
        $this->has_body = 0;
      }
    }
    if (array_key_exists("has_footer", $request)) {
      if ($request["has_footer"] === true || $request["has_footer"] === "true"  || $request["has_footer"] == 1) {
        $this->has_footer = 1;
      } else if ($request["has_footer"] === false || $request["has_footer"] === "false"  || $request["has_footer"] == 0) {
        $this->has_footer = 0;
      }
    }
    if (array_key_exists("has_opening_time", $request)) {
      if ($request["has_opening_time"] === true || $request["has_opening_time"] === "true"  || $request["has_opening_time"] == 1) {
        $this->has_opening_time = 1;
      } else if ($request["has_opening_time"] === false || $request["has_opening_time"] === "false"  || $request["has_opening_time"] == 0) {
        $this->has_opening_time = 0;
      }
    }
    if (array_key_exists("opening_time", $request)) {
      $openTime = new OpenTimeWeek();
      $openTime->setSelf($request["opening_time"]);
      if ($openTime->isValide()) {
        $this->opening_time = $openTime->getJson();
      }
    }
    if (array_key_exists("state", $request)) {
      $this->state = intval($request["state"]);
    }

    if (array_key_exists("pre_def_posts", $request)) {
      $this->pre_def_posts = intval($request["pre_def_posts"]);
    }

    if (array_key_exists("tripadvisor_id", $request)) {
      $this->tripadvisor_id = intval($request["tripadvisor_id"]);
    }
  }

  public function incrimentViews()
  {
    $this->nbr_views++;
  }


  public function isValide()
  {
    try {
      $postType = PostType::findOrFail($this->type);
      return $this->isValideByType($postType);
    } catch (ModelNotFoundException $e) {
      return false;
    }
  }
  public function isValideByType(PostType $postType)
  {
    $validations = [];
    $requiredFieldsMunallyChecked = ["type", "hotel_id", "parent_id", "role", "title", "summery", "description", "addition_data_1_text"];
    if (
      $this->type !== null &&
      $this->hotel_id !== null && $this->hotel_id !== 0 && ($this->role == 1 || $this->parent_id !== null)
    ) {
      $requiredFields = array_filter(explode(',', $postType->required_colomns));
      $thisArray = $this->toArray();
      foreach ($requiredFields as $requiredField) {
        if (
          !in_array($requiredField, $requiredFieldsMunallyChecked) &&
          !array_key_exists($requiredField, $thisArray) || (array_key_exists($requiredField, $thisArray) && ($thisArray[$requiredField] === null ||
              $thisArray[$requiredField] === "null" ||
              $thisArray[$requiredField] === ""))
        ) {
          array_push($validations, $requiredField);
        }
      }
      if (count($validations) == 0) {
        return true;
      } else {
        return $validations;
      }
    } else {
      if ($this->type == null) array_push($validations, "type");
      if ($this->hotel_id == null || $this->hotel_id == 0) array_push($validations, "hotel_id");
      if ($this->role != 1 && $this->parent_id == null) array_push($validations, "role", "parent_id");
      return $validations;
    }
  }


  public static function getEditableColumns()
  {
    $readOnlyColumns = array('id', 'created_at', 'updated_at');
    $requiredAlways = array('hotel_id', 'type', 'categories', 'content_manager', 'state', 'image');

    $result = [];

    $table_info_columns  = (array) DB::select(DB::raw('SHOW FULL COLUMNS FROM ' . app(Post::class)->getTable() . ''));
    $table_info_columns = json_decode(json_encode($table_info_columns), true);

    foreach ($table_info_columns as $column) {
      unset($column["Collation"], $column["Key"], $column["Extra"], $column["Privileges"]);
      if (!in_array($column['Field'], $readOnlyColumns)) {
        if (in_array($column['Field'], $requiredAlways) || ($column['Null'] == "NO" && $column['Default'] === null)) {
          $result['required'][$column['Field']] = $column;
        } else {
          $result['possible'][$column['Field']] = $column;
        }
      } else {
        $result['read_only'][$column['Field']] = $column;
      }
    }


    return $result;
  }

  /**
   * Method to set post rating to the average of post reviews
   */

  public function updateRatings()
  {
    $avg = DB::table('post_reviews')->where(["post_id" => $this->id])->avg("rating");
    if ($avg != null) {
      $this->rate = $avg;
      $this->save();
      return;
    }
  }

  public static function Finder($request_data)
  {
    $post = new Post();

    $post = $post->where('hidden', 0);
    
    if (array_key_exists('type', $request_data) && $request_data["type"] !== null) {
      $post = $post->where('type', $request_data["type"]);
    }

    if (array_key_exists('isChatAvailable', $request_data) && $request_data["isChatAvailable"] !== null) {
      $post = $post->where('isChatAvailable', $request_data["isChatAvailable"]);
    }

    if (array_key_exists('hotel_id', $request_data) && $request_data["hotel_id"] !== null) {
      if (is_integer($request_data["hotel_id"]) || is_string($request_data["hotel_id"])) {
        $post = $post->where('hotel_id', $request_data["hotel_id"]);
      } else if (is_array($request_data["hotel_id"])) {
        $post = $post->wherein('hotel_id', array_values($request_data["hotel_id"]));
      }
    }

    if (array_key_exists('role', $request_data) && $request_data["role"] !== null) {
      $post = $post->where('role', $request_data["role"]);
    }

    if (array_key_exists('parent_id', $request_data)) {
      if ($request_data["parent_id"] > 0) {
        $post = $post->where('parent_id', $request_data["parent_id"]);
      } else {
        $post = $post->whereNull('parent_id');
      }
    }

    if (array_key_exists('categories', $request_data) && $request_data["categories"] != ".*" && $request_data["categories"] != null) {
      $post = $post->where('categories', 'REGEXP', $request_data["categories"]);
    }

    if (array_key_exists('order_in_parent', $request_data) && $request_data["order_in_parent"] !== null) {
      $post = $post->where('order_in_parent', $request_data["order_in_parent"]);
    }

    if (array_key_exists('contents_categories', $request_data) && $request_data["contents_categories"] != ".*" && $request_data["contents_categories"] != null) {
      $post = $post->where('contents_categories', 'REGEXP', $request_data["contents_categories"]);
    }

    if (array_key_exists('content_manager', $request_data) && $request_data["content_manager"] !== null) {
      $post = $post->where('content_manager', $request_data["content_manager"]);
    }

    if (array_key_exists('location', $request_data) && $request_data["location"] != ".*" && $request_data["location"] != null) {
      $post = $post->where('location', 'REGEXP', $request_data["location"]);
    }

    if (array_key_exists('rate', $request_data) && $request_data["rate"]['min'] != null) {
      $post = $post->where('rate', '>=', $request_data["rate"]['min']);
    }

    if (array_key_exists('rate', $request_data) && $request_data["rate"]['max'] != null) {
      $post = $post->where('rate', '<=', $request_data["rate"]['max']);
    }

    if (array_key_exists('price', $request_data) && $request_data["price"]['min'] != null) {
      $post = $post->where('price', '>=', $request_data["price"]['min']);
    }

    if (array_key_exists('state', $request_data) && $request_data["state"] !== null) {
      $post = $post->where('state', $request_data["state"]);
    }

    if (array_key_exists('pre_def_posts', $request_data) && $request_data["pre_def_posts"] !== null) {
      $post = $post->where('pre_def_posts', $request_data["pre_def_posts"]);
    }
    if (array_key_exists('ids', $request_data) && $request_data["ids"] !== null) {
      $post = $post->whereIn('id', $request_data["ids"]);
    }

    if (array_key_exists('orderby', $request_data) && $request_data["orderby"] != null) {
      if ($request_data["nulls_last"] === true) {
        $post = $post->orderByRaw("-" . $request_data["orderby"] . " DESC");
      } else {
        $post = $post->orderBy($request_data["orderby"], $request_data["orderby_direction"]);
      }
    }

    if (array_key_exists('paginate', $request_data) && $request_data["paginate"] != null) {
      $post = $post->paginate($request_data["paginate"]);
    } else {
      $post = $post->get();
    }

    

    return $post;
  }
}
