<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class Stay extends Model
{
  public function hotel()
  {
    return $this->belongsTo('App\Hotel')->withDefault();
  }

  public function tourist()
  {
    return $this->belongsTo('App\Tourist')->withDefault();
  }

  public function device_room()
  {
    return $this->belongsTo('App\DeviceRoom')->withDefault();
  }

  public function setSelf($request)
  {

    if (array_key_exists("tourist_id", $request)) {

      try {
        $tourist = Tourist::findOrFail(intval($request["tourist_id"]));
        $tourist_id = $tourist->id;
      } catch (ModelNotFoundException $e) {
        $tourist_id = null;
      }
      $this->tourist_id = $tourist_id;
    }

    if (array_key_exists("device_room_id", $request)) {

      try {
        $device_room = DeviceRoom::findOrFail(intval($request["device_room_id"]));
        if ($device_room->stay != null) {
          $device_room_id = $device_room->id;
        } else {
          $device_room_id = null;
        }
        $hotel_id = $device_room->hotel_id;
      } catch (ModelNotFoundException $e) {
        $device_room_id = null;
        $hotel_id = null;
      }
      $this->device_room_id = $device_room_id;
      $this->hotel_id = $hotel_id;
    }

    if (array_key_exists("reserved_time_from", $request) && $this->reserved_time_from == null) {
      $this->reserved_time_from = intval($request["reserved_time_from"]);
    }

    if (array_key_exists("reserved_time_to", $request)  && $this->reserved_time_to == null) {
      $this->reserved_time_to = intval($request["reserved_time_to"]);
    }

    if (array_key_exists("join_time", $request)) {
      $this->join_time = intval($request["join_time"]);
    }

    if (array_key_exists("leave_time", $request)) {
      $this->leave_time = intval($request["leave_time"]);
    }
  }

  public function isValide()
  {
    if (
      $this->hotel_id != null &&
      $this->tourist_id != null &&
      $this->device_room_id != null
    ) {
      return true;
    } else {
      return false;
    }
  }

  public static function getEditableColumns()
  {
    $readOnlyColumns = array('id', 'created_at', 'updated_at');

    $result = [];

    $table_info_columns  = (array) DB::select(DB::raw('SHOW FULL COLUMNS FROM ' . app(Stay::class)->getTable() . ''));
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

  public static function Finder($request_data)
  {
    $item = new Stay();
    if (array_key_exists('tourist_id', $request_data) && $request_data["tourist_id"] != null) {
      $item =  $item->where('tourist_id', $request_data["tourist_id"]);
    }

    if (array_key_exists('hotel_id', $request_data) && $request_data["hotel_id"] !== null) {
      if (is_integer($request_data["hotel_id"]) || is_string($request_data["hotel_id"])) {
        $item = $item->where('hotel_id', $request_data["hotel_id"]);
      } else if (is_array($request_data["hotel_id"])) {
        $item = $item->wherein('hotel_id', array_values($request_data["hotel_id"]));
      }
    }

    if (array_key_exists('device_room_id', $request_data) && $request_data["device_room_id"] != null) {
      $item =  $item->where('device_room_id', $request_data["device_room_id"]);
    }

    if (array_key_exists('reserved_time_from', $request_data) && $request_data["reserved_time_from"]['min'] != null) {
      $item = $item->where('reserved_time_from', '>=', $request_data["reserved_time_from"]['min']);
    }

    if (array_key_exists('reserved_time_from', $request_data) && $request_data["reserved_time_from"]['max'] != null) {
      $item = $item->where('reserved_time_from', '<=', $request_data["reserved_time_from"]['max']);
    }

    if (array_key_exists('reserved_time_to', $request_data) && $request_data["reserved_time_to"]['min'] != null) {
      $item = $item->where('reserved_time_to', '>=', $request_data["reserved_time_to"]['min']);
    }

    if (array_key_exists('reserved_time_to', $request_data) && $request_data["reserved_time_to"]['max'] != null) {
      $item = $item->where('reserved_time_to', '<=', $request_data["reserved_time_to"]['max']);
    }

    if (array_key_exists('join_time', $request_data) && $request_data["join_time"]['min'] != null) {
      $item = $item->where('join_time', '>=', $request_data["join_time"]['min']);
    }

    if (array_key_exists('join_time', $request_data) && $request_data["join_time"]['max'] != null) {
      $item = $item->where('join_time', '<=', $request_data["join_time"]['max']);
    }

    if (array_key_exists('leave_time', $request_data) && $request_data["leave_time"]['min'] != null) {
      $item = $item->where('leave_time', '>=', $request_data["leave_time"]['min']);
    }

    if (array_key_exists('leave_time', $request_data) && $request_data["leave_time"]['max'] != null) {
      $item = $item->where('leave_time', '<=', $request_data["leave_time"]['max']);
    }



    if (array_key_exists('orderby', $request_data) && $request_data["orderby"] != null) {
      if ($request_data["nulls_last"] === true) {
        $item = $item->orderByRaw("-" . $request_data["orderby"] . " DESC");
      } else {
        $item = $item->orderBy($request_data["orderby"], $request_data["orderby_direction"]);
      }
    }


    if (array_key_exists('paginate', $request_data) && $request_data["paginate"] != null) {
      $item = $item->paginate($request_data["paginate"]);
    } else {
      $item = $item->get();
    }

    return $item;
  }
}
