<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\DeviceRoom;
use App\Stay;
use App\Http\Utils;
use DB;

class Device extends Model
{
  public function hotel()
  {
    return $this->belongsTo('App\Hotel')->withDefault();
  }

  public function device_room()
  {
    return $this->hasOne('App\DeviceRoom')->withDefault();
  }

  public function setSelf($request)
  {
    if (array_key_exists("imei", $request)) {
      $this->imei = Utils::validate_imei($request["imei"]);
    }
    if (array_key_exists("phone", $request)) {
      $this->phone = preg_replace(Utils::$preg_replace['phone'], '', $request["phone"]);
    }
    if (array_key_exists("fcm_token", $request)) {
      $this->fcm_token = $request["fcm_token"];
    }
    if (array_key_exists("data_limit", $request)) {
      $this->data_limit = $request["data_limit"];
    }
    if (array_key_exists("token", $request)) {
      $this->token = $request["token"];
    }
    if (array_key_exists("data_time", $request)) {
      $this->data_time = $request["data_time"];
    }
    if (array_key_exists("number", $request)) {
      $this->number = $request["number"];
    }
    if (array_key_exists("call_time", $request)) {
      $this->call_time = $request["call_time"];
    }
    if (array_key_exists("intra_flotte", $request)) {
      $this->intra_flotte = $request["intra_flotte"];
    }
    if (array_key_exists("app_version_code", $request)) {
      $this->app_version_code = $request["app_version_code"];
    }
    if (array_key_exists("app_lang", $request)) {
      $this->app_lang = $request["app_lang"];
    }
    if (array_key_exists("call_limit", $request)) {
      $this->call_limit = $request["call_limit"];
    }
    if (array_key_exists("status", $request)) {
      $this->status = intval($request["status"]);
      $this->time_last_activity = time();
    }

    if (array_key_exists("hotel_id", $request)) {
      try {
        $hotel = Hotel::findOrFail(intval($request["hotel_id"]));
        $hotel_id = $hotel->id;
      } catch (ModelNotFoundException $e) {
        $hotel_id = null;
      }
      $this->hotel_id = $hotel_id;
    }
  }

  public function isValide()
  {
    if (
      Utils::validate_imei($this->imei) != Utils::getInvalidIMEI()
    ) {
      return true;
    } else {
      return false;
    }
  }

  public static function getEditableColumns()
  {
    $readOnlyColumns = array('id', 'created_at', 'updated_at', 'device_renting_id');

    $result = [];

    $table_info_columns  = (array) DB::select(DB::raw('SHOW FULL COLUMNS FROM ' . app(Device::class)->getTable() . ''));
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

  public static function findByImei($imei)
  {
    if ($imei != null && $imei != '') {
      $device = new Device();
      $device =  $device->where('imei', $imei)
        ->limit(1)
        ->first();
      return $device;
    }

    return [];
  }

  /**
   * Find device using IMEI, tourist ID or room ID
   */

  public static function Find($request_data)
  {
    $result = DB::table('devices')
      ->join('device_room', 'devices.id', '=', 'device_room.device_id')
      ->join('rooms', 'rooms.id', '=', 'device_room.room_id')
      ->join('stays', 'stays.device_room_id', '=', 'device_room.id')
      ->join('tourists', 'tourists.id', '=', 'stays.tourist_id');

    if (array_key_exists('device_imei', $request_data) && $request_data["device_imei"] != null) {
      $result = $result->orWhere('imei', $request_data["device_imei"]);
    }

    if (array_key_exists('last_name', $request_data) && $request_data["cp"] != null) {
      $result = $result->orWhere('last_name', $request_data["last_name"]);
    }

    if (array_key_exists('room_number', $request_data) && $request_data["room_number"] != null) {
      $result = $result->orWhere('room_number', $request_data["room_number"]);
    }

    $result = $result->select('devices.*')->get();
    $list = collect();
    foreach ($result as $device) {
      $d = Device::findOrFail($device->id);
      $list->push($d);
    }
    $result = $list;
    return $result;
  }

  public static function Finder($request_data)
  {
    $device = new Device();
    if (array_key_exists('imei', $request_data) && $request_data["imei"] != null) {
      $device =  $device->where('imei', $request_data["imei"]);
    }
    if (array_key_exists('fcm_token', $request_data) && $request_data["fcm_token"] != null) {
      $device =  $device->where('fcm_token', $request_data["fcm_token"]);
    }
    if (array_key_exists('app_version_code', $request_data) && $request_data["app_version_code"] != null) {
      $device =  $device->where('app_version_code', $request_data["app_version_code"]);
    }
    if (array_key_exists('app_lang', $request_data) && $request_data["app_lang"] != null) {
      $device =  $device->where('app_lang', $request_data["app_lang"]);
    }
    if (array_key_exists('phone', $request_data) && $request_data["phone"] != null) {
      $device =  $device->where('phone', $request_data["phone"]);
    }
    if (array_key_exists('token', $request_data) && $request_data["token"] != null) {
      $device =  $device->where('token', $request_data["token"]);
    }
    if (array_key_exists('number', $request_data) && $request_data["number"] != null) {
      $device =  $device->where('number', $request_data["number"]);
    }
    if (array_key_exists('status', $request_data) && $request_data["status"] !== null) {
      $device = $device->whereIn('status', $request_data["status"]);
    }
    if (array_key_exists('hotel_id', $request_data) && $request_data["hotel_id"] !== null) {
      if (is_integer($request_data["hotel_id"]) || is_string($request_data["hotel_id"])) {
        $device = $device->where('hotel_id', $request_data["hotel_id"]);
      } else if (is_array($request_data["hotel_id"])) {
        $device = $device->wherein('hotel_id', array_values($request_data["hotel_id"]));
      }
    }
    if (array_key_exists('device_room_id', $request_data) && $request_data["device_room_id"] !== null) {
      $device = $device->where('device_room_id', $request_data["device_room_id"]);
    }
    if (array_key_exists('has_room', $request_data) && $request_data["has_room"] !== null) {
      if ($request_data["has_room"] == 0 || $request_data["has_room"] === false || $request_data["has_room"] === 'false') {
        $device = $device->where('device_room_id', null);
      } else if ($request_data["has_room"] == 1 || $request_data["has_room"] === true || $request_data["has_room"] === 'true') {
        $device = $device->where('device_room_id', '<>', null);
      }
    }

    if (array_key_exists('orderby', $request_data) && $request_data["orderby"] != null) {
      if ($request_data["nulls_last"] === true) {
        $device = $device->orderByRaw("-" . $request_data["orderby"] . " DESC");
      } else {
        $device = $device->orderBy($request_data["orderby"], $request_data["orderby_direction"]);
      }
    }
    if (array_key_exists('paginate', $request_data) && $request_data["paginate"] != null) {
      $device = $device->paginate($request_data["paginate"]);
    } else {
      $device = $device->get();
    }

    return $device;
  }
}
