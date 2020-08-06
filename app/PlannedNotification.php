<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use App\OpenTimeWeek;
use DB;
use App\PostReview;

class PlannedNotification extends Model
{

  public function post()
    {
        return $this->belongsTo('App\Post')->withDefault();
    }
  public function setSelf($request)
  {
    // $this->inform_no_context = [];
    // $this->post_reference = [];
    if (array_key_exists("imei", $request)) {
      $this->imei = $request["imei"];
    }
    if (array_key_exists("layout_xml_template", $request)) {
      $this->layout_xml_template = $request["layout_xml_template"];
    }
    if (array_key_exists("title", $request)) {
      $this->title = $request["title"];
    }
    if (array_key_exists("summery", $request)) {
      $this->summery = $request["summery"];
    }
    if (array_key_exists("video", $request)) {
      $this->video = $request["video"];
    }
    if (array_key_exists("image", $request)) {
      $this->image = $request["image"];
    }
    if (array_key_exists("pub", $request)) {
      $this->pub = $request["pub"];
    }
    if (array_key_exists("duree", $request)) {
      $this->duree = $request["duree"];
		}
		if (array_key_exists("date", $request)) {
      $this->date = $request["date"];
		}
		if (array_key_exists("hour", $request)) {
      $this->hour = $request["hour"];
    }
    if (array_key_exists("post_id", $request)) {
      $this->post_id = intval($request["post_id"]);
    }
    if (array_key_exists("promo", $request)) {
      $this->promo = intval($request["promo"]);
    }
    if (array_key_exists("image_right_url", $request)) {
      $this->image_right_url = $request["image_right_url"];
    }
    if (array_key_exists("expandeble_image_url ", $request)) {
      $this->expandeble_image_url  = $request["expandeble_image_url "];
    }
    if (array_key_exists("parent_title", $request)) {
    $this->parent_title = $request["parent_title"];
    }
    if (array_key_exists("role", $request)) {
      $this->role = intval($request["role"]);
    }
    if (array_key_exists("expandeble_text", $request)) {
      $this->expandeble_text = $request["expandeble_text"];
    }
    if (array_key_exists("sound", $request)) {
      $this->sound = intval($request["sound"]);
    }
    if (array_key_exists("inform_no_context", $request)) {
      $this->inform_no_context_image  = $request["inform_no_context"]["image"];
      $this->inform_no_context_title = $request["inform_no_context"]["title"];
      $this->inform_no_context_description = $request["inform_no_context"]["description"];
    }
    if (array_key_exists("post_reference", $request)) {
      $this->post_reference_fragment =$request["post_reference"]["fragment"];
      $this->post_reference_post_id = intval($request["post_reference"]["post_id"]);
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

    $table_info_columns  = (array) DB::select(DB::raw('SHOW FULL COLUMNS FROM ' . app(PlannedNotification::class)->getTable() . ''));
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

  public function updateRatings()
  {
    $avg = DB::table('post_reviews')->where(["post_id" => $this->id])->avg("rating");
    if ($avg != null) {
      $this->rate = $avg;
      $this->save();
      return;
    }
    /*
        ** 
        ** This is used to make a parent post take the average rate of child posts
        **
        $avg = DB::table('posts')->where(["parent_id"=>$this->id])->avg("rate");
        $this->rate = $avg==null? 0 : $avg;
        $this->save();
        */
  }

  public static function Finder($request_data)
  {
    $post = new PlannedNotification();

    if (array_key_exists('imei', $request_data) && $request_data["imei"] !== null) {
      $post = $post->where('imei', $request_data["imei"]);
    }
    if (array_key_exists('hidden', $request_data) && $request_data["hidden"] !== null) {
      $post = $post->where('hidden', $request_data["hidden"]);
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
