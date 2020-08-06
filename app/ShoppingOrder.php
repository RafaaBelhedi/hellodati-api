<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class ShoppingOrder extends Model
{
  protected $table = 'shopping_orders';

  public function hotel()
  {
    return $this->belongsTo('App\Hotel')->withDefault();
  }

  public function device()
  {
    return Tourist::findOrFail($this->tourist->id)->stay->device_room->device;
  }

  public function post()
  {
    return $this->belongsTo('App\Post')->withDefault();
  }

  public function tourist()
  {
    return $this->belongsTo('App\Tourist')->withDefault();
  }

  public function setSelf($request)
  {
    if (array_key_exists("seen", $request)) {
      $this->seen = intval($request["seen"]);
    }

    if (array_key_exists("status", $request)) {
      $this->status = intval($request["status"]);
    }
    if (array_key_exists("delay", $request)) {
      $this->delay = intval($request["delay"]);
    }
    if (array_key_exists("reservation", $request)) {
      $this->reservation = intval($request["reservation"]);
    }
    if (array_key_exists("reservation_time", $request)) {
      $this->reservation_time = intval($request["reservation_time"]);
    }
    if (array_key_exists("delivery_place_id", $request)) {
      try {
        $delivery_place = DeliveryPlace::findOrFail(intval($request["delivery_place_id"]));
        $delivery_place_id = $delivery_place->id;
      } catch (ModelNotFoundException $e) {
        $delivery_place_id = null;
      }
      $this->delivery_place_id = $delivery_place_id;
    }
    if (array_key_exists("qt", $request)) {
      $this->qt = intval($request["qt"]);
    }
    if (array_key_exists("comment", $request)) {
      $this->comment = $request["comment"];
    }
    if (array_key_exists("post_id", $request)) {

      try {
        $post = Post::findOrFail(intval($request["post_id"]));
        $post_id = $post->id;
        $this->hotel_id = $post->hotel_id;
      } catch (ModelNotFoundException $e) {
        $post_id = null;
      }
      $this->post_id = $post_id;
    }
    if (array_key_exists("tourist_id", $request)) {

      try {
        $tourist = Tourist::findOrFail(intval($request["tourist_id"]));
        $tourist_id = $tourist->id;
        if ($tourist->stay == null || ($tourist->stay != null && $tourist->stay->hotel_id != $this->hotel_id)) {
          $this->hotel_id = null;
        }
      } catch (ModelNotFoundException $e) {
        $tourist_id = null;
      }
      $this->tourist_id = $tourist_id;
    }
  }

  public function isValide()
  {
    if (
      $this->tourist_id != null &&
      $this->post_id != null && ($this->qt > 0 || $this->reservation == 1) &&
      $this->hotel_id != null && (($this->reservation == 0 && $this->delivery_place_id != null) || ($this->reservation == 1 && $this->reservation_time != null))
    ) {
      return 'true';
    } else {
      return 'false';
    }
  }

  public static function getEditableColumns()
  {
    $readOnlyColumns = array('id', 'created_at', 'updated_at');

    $result = [];

    $table_info_columns  = (array) DB::select(DB::raw('SHOW FULL COLUMNS FROM ' . app(ShoppingOrder::class)->getTable() . ''));
    $table_info_columns = json_decode(json_encode($table_info_columns), true);

    foreach ($table_info_columns as $column) {
      unset($column["Collation"], $column["Key"], $column["Extra"], $column["Privileges"]);
      if (!in_array($column['Field'], $readOnlyColumns)) {
        if ($column['Null'] == "NO" && $column['Default'] == null) {
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

  public static function confirmAll($tourist_id, $isReservation)
  {
    $shoppingOrder = new ShoppingOrder();
    $shoppingOrder->where('tourist_id', $tourist_id)
      ->where('reservation', $isReservation)
      ->where('status', 0)
      ->update(['status' => 1]);
  }

  public static function Finder($request_data)
  {
    $shoppingOrder = new ShoppingOrder();
    if (array_key_exists('tourist_id', $request_data) && $request_data["tourist_id"] !== null) {
      $shoppingOrder =  $shoppingOrder->where('tourist_id', $request_data["tourist_id"]);
    }

    if (array_key_exists('post_id', $request_data) && $request_data["post_id"] !== null) {
      $shoppingOrder =  $shoppingOrder->where('post_id', $request_data["post_id"]);
    }

    if (array_key_exists('hotel_id', $request_data) && $request_data["hotel_id"] !== null) {
      if (is_integer($request_data["hotel_id"]) || is_string($request_data["hotel_id"])) {
        $shoppingOrder = $shoppingOrder->where('hotel_id', $request_data["hotel_id"]);
      } else if (is_array($request_data["hotel_id"])) {
        $shoppingOrder = $shoppingOrder->wherein('hotel_id', array_values($request_data["hotel_id"]));
      }
    }

    if (array_key_exists('reservation', $request_data) && ($request_data["reservation"] == 0 || $request_data["reservation"] == 1)) {
      $shoppingOrder =  $shoppingOrder->where('reservation', $request_data["reservation"]);
    }

    if (array_key_exists('status', $request_data) && $request_data["status"] !== null) {
      if (is_integer($request_data["status"]))
        $shoppingOrder = $shoppingOrder->where('status', $request_data["status"]);
      else
        $shoppingOrder = $shoppingOrder->whereIn('status', $request_data["status"]);
    }

    if (array_key_exists('orderby', $request_data) && $request_data["orderby"] != null) {
      if ($request_data["nulls_last"] === true) {
        $shoppingOrder = $shoppingOrder->orderByRaw("-" . $request_data["orderby"] . " DESC");
      } else {
        $shoppingOrder = $shoppingOrder->orderBy($request_data["orderby"], $request_data["orderby_direction"]);
      }
    }


    if (array_key_exists('paginate', $request_data) && $request_data["paginate"] != null) {
      $shoppingOrder = $shoppingOrder->paginate($request_data["paginate"]);
    } else {
      $shoppingOrder = $shoppingOrder->get()->reverse();
    }

    return $shoppingOrder;
  }
}
