<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class Hotel extends Model
{
  public function posts()
  {
    return $this->hasMany('App\Post');
  }

  public function rooms()
  {
    return $this->hasMany('App\Room');
  }

  public function shopping_orders()
  {
    return $this->hasMany('App\ShoppingOrder');
  }

  public function devices()
  {
    return $this->hasMany('App\Device');
  }

  public function tourists()
  {
    return $this->hasMany('App\Tourist');
  }

  public function setSelf($request)
  {
    if (array_key_exists("hotel_name", $request)) {
      $this->hotel_name = preg_replace(Utils::$preg_replace['paragraphe'], '', $request["hotel_name"]);
    }
    if (array_key_exists("city", $request)) {
      $this->city = preg_replace(Utils::$preg_replace['paragraphe'], '', $request["city"]);
    }
    if (array_key_exists("country", $request)) {
      $this->country = preg_replace(Utils::$preg_replace['paragraphe'], '', $request["country"]);
    }
    if (array_key_exists("phone", $request)) {
      $this->phone = preg_replace(Utils::$preg_replace['phone'], '', $request["phone"]);
    }
    if (array_key_exists("stars", $request)) {
      $this->stars = $request["stars"];
    }
    if (array_key_exists("tokens", $request)) {
      $this->tokens = $request["tokens"];
    }
    if (array_key_exists("trip_advisor_url", $request)) {
      $this->trip_advisor_url = $request["trip_advisor_url"];
    }
    if (array_key_exists("email", $request)) {
      $this->email = $request["email"];
    }
    if (array_key_exists("adress_print", $request)) {
      $this->adress_print = $request["adress_print"];
    }
    if (array_key_exists("facebook", $request)) {
      $this->facebook = $request["facebook"];
    }
    if (array_key_exists("twitter", $request)) {
      $this->twitter = $request["twitter"];
    }
    if (array_key_exists("youtube", $request)) {
      $this->youtube = $request["youtube"];
    }
    if (array_key_exists("address", $request)) {
      $this->address = $request["address"];
    }
    if (array_key_exists("check_in", $request)) {
      $this->check_in = $request["check_in"];
    }
    if (array_key_exists("check_out", $request)) {
      $this->check_out = $request["check_out"];
    }
    if (array_key_exists("region", $request)) {
      $this->region = $request["region"];
    }
    if (array_key_exists("continent", $request)) {
      $this->continent = $request["continent"];
    }
    if (array_key_exists("chain", $request)) {
      $this->chain = $request["chain"];
    }
    if (array_key_exists("user_id", $request)) {
      $this->user_id = intval($request["user_id"]);

      try {
        $user = User::findOrFail(intval($request["user_id"]));
        $user_id = $user->id;
      } catch (ModelNotFoundException $e) {
        $user_id = null;
      }
      $this->user_id = $user_id;
    }
  }

  public function isValide()
  {
    if (
      $this->hotel_name != null
      && $this->city != null
      && $this->country != null
      && $this->stars >= 0
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

    $table_info_columns  = (array) DB::select(DB::raw('SHOW FULL COLUMNS FROM ' . app(Hotel::class)->getTable() . ''));
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
    $hotel = new Hotel();
    if (array_key_exists('user_id', $request_data) && $request_data["user_id"] != null) {
      $hotel = $hotel->where('user_id', $request_data["user_id"]);
    }
    if (array_key_exists('adress_print', $request_data) && $request_data["adress_print"] != null) {
      $hotel = $hotel->where('adress_print', $request_data["adress_print"]);
    }
    if (array_key_exists('hotel_name', $request_data) && $request_data["hotel_name"] != null) {
      $hotel =  $hotel->where('hotel_name', 'REGEXP', $request_data["hotel_name"]);
    }

    if (array_key_exists('stars', $request_data) && $request_data["stars"] != null) {
      $hotel = $hotel->where('stars', $request_data["stars"]);
    }

    if (array_key_exists('city', $request_data) && $request_data["city"] != null) {
      $hotel = $hotel->where('city', $request_data["city"]);
    }

    if (array_key_exists('country', $request_data) && $request_data["country"] != null) {
      $hotel = $hotel->where('country', $request_data["country"]);
    }

    if (array_key_exists('phone', $request_data) && $request_data["phone"] != null) {
      $hotel = $hotel->where('phone', $request_data["phone"]);
    }

    if (array_key_exists('orderby', $request_data) && $request_data["orderby"] != null) {
      if ($request_data["nulls_last"] === true) {
        $hotel = $hotel->orderByRaw("-" . $request_data["orderby"] . " DESC");
      } else {
        $hotel = $hotel->orderBy($request_data["orderby"], $request_data["orderby_direction"]);
      }
    }

    if (array_key_exists('paginate', $request_data) && $request_data["paginate"] != null) {
      $hotel = $hotel->paginate($request_data["paginate"]);
    } else {
      $hotel = $hotel->get();
    }

    return $hotel;
  }
}
