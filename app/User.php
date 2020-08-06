<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\Http\Controllers\DeviceController;
use DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Contracts\Auth\CanResetPassword as ContractsCanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as PasswordsCanResetPassword;

class User extends Authenticatable
{
  use Notifiable, HasApiTokens;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'password',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  public function hotels()
  {
    return $this->hasMany('App\Hotel');
  }

  public function tourist()
  {
    return $this->hasOne('App\Tourist');
  }

  public function permission_group()
  {
    return $this->belongsTo('App\PermissionGroup');
  }

  protected $privileges;

  function getPrivileges()
  {
    if ($this->privileges == null) {
      $client_id = $this->accessToken->client->id;
      $this->privileges = [
        'user_id' => $this->id,
        'role' => $this->role,
        'client_id' => $client_id,
        'hotel_id' => null,
        'device_imei' => null,
        'tourist_id' => null,
        'permission_group_id' => $this->permission_group_id,
      ];

      switch ($this->role) {
        case 0:
          $dCtrl = new DeviceController();
          $devices = $dCtrl->showByImeiLocal(request('device_imei'));
          if ($devices != null) {
            $this->privileges['hotel_id'] = $devices->hotel_id;
            $this->privileges['device_imei'] = $devices->imei;
            $this->privileges['tourist_id'] = $devices->device_room->stay->tourist->id;
          }

          break;
        case 1:
          if ($this->tourist != null) {
            $stay = $this->tourist->stay;
            if ($stay != null) {
              $this->privileges['hotel_id'] = $stay->hotel_id;
              $this->privileges['device_imei'] = $stay->device_room->device->imei;
            }
          }

          break;
        case 2:
          $this->privileges['hotel_id'] = [];
          if (count($this->hotels) > 0)
          {
            $hotels = $this->hotels;
            for ($i = 0; $i < count($hotels); $i++) 
            array_push($this->privileges['hotel_id'], $hotels[$i]->id);
          }     
          else {
            $hotels = $this->hotel;   
            array_push($this->privileges['hotel_id'], $hotels->id);
          }         
          if ($this->permission_group_id)
            array_push($this->privileges['hotel_id'], $this->permission_group->hotel_id);

          break;
      }
    }
    return $this->privileges;
  }

  public function setSelf($request)
  {
    if (array_key_exists("prefix", $request)) {
      $this->prefix = $request["prefix"];
    }
    if (array_key_exists("gender", $request)) {
      $this->gender = $request["gender"];
    }
    if (array_key_exists("phone_number", $request)) {
      $this->phone_number = $request["phone_number"];
    }
    if (array_key_exists("born", $request)) {
      $this->born = $request["born"];
    }
    if (array_key_exists("cin", $request)) {
      $this->cin = $request["cin"];
    }
    if (array_key_exists("country", $request)) {
      $this->country = $request["country"];
    }
    if (array_key_exists("city", $request)) {
      $this->city = $request["city"];
    }
    if (array_key_exists("zip_code", $request)) {
      $this->zip_code = $request["zip_code"];
    }
    if (array_key_exists("adress", $request)) {
      $this->adress = $request["adress"];
    }
    if (array_key_exists("hotel_id", $request)) {
      $this->hotel_id = $request["hotel_id"];
    }
    if (array_key_exists("name", $request)) {
      $this->name = $request["name"];
    }
    if (array_key_exists("email", $request)) {
      $this->email = $request["email"];
    }
    if (array_key_exists("password", $request)) {
      $this->password = bcrypt($request["password"]);
    }
    if (array_key_exists("role", $request)) {
      $this->role = intval($request["role"]);
    }
    if (array_key_exists("permission_group_id", $request)) {
      $this->permission_group_id = intval($request["permission_group_id"]);
    }
    if (array_key_exists("approved", $request)) {
      $this->approved = intval($request["approved"]);
      if (!$this->approved) {
        DB::table('oauth_access_tokens')
          ->where('user_id', $this->id)
          ->update([
            'revoked' => true
          ]);
      }
    }
  }

  public function isValide()
  {
    if (
      $this->name != null &&
      $this->email != null &&
      $this->role != null
    ) {
      return true;
    } else {
      return false;
    }
  }

  public static function Finder($request_data)
  {
    $item = new User();
    return $item->get();
    if (array_key_exists('name', $request_data) && $request_data["name"] != null) {
      $item =  $item->where('name', $request_data["name"]);
    }

    if (array_key_exists('email', $request_data) && $request_data["email"] != null) {
      $item =  $item->where('email', $request_data["email"]);
    }

    if (array_key_exists('approved', $request_data) && $request_data["approved"] != null) {
      $item =  $item->where('approved', $request_data["approved"]);
    }

    if (array_key_exists('role', $request_data) && $request_data["role"] != null) {
      $item =  $item->where('role', $request_data["role"]);
    }

    if (array_key_exists('orderby', $request_data) && $request_data["orderby"] != null) {
      if ($request_data["nulls_last"] === true) {
        $item = $item->orderByRaw("-" . $request_data["orderby"] . " DESC");
      } else {
        $item = $item->orderBy($request_data["orderby"], $request_data["orderby_direction"]);
      }
    }
    return $item->get();
  }

  public function SideBarAccess()
  {
      return $this->hasOne('App\SideBarAccess');
  }

  public function postAccess() {
    return $this->hasMany('App\PostAccess');
  }

  public function hotel() {
    return $this->belongsTo('App\Hotel');
  }
}
