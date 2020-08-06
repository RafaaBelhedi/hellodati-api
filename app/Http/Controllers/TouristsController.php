<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Auth;
use App\Http\Requests;
use App\Http\Utils;
use App\Tourist;
use App\Device;
use App\DeviceRoom;
use App\Stay;
use App\PermissionGroup;
use App\Http\Resources\TouristTemplate;
use App\StaysHistory;

use Illuminate\Support\Facades\Hash;

/**
 * @group Tourists
 *
 * APIs for managing tourists
 */
class TouristsController extends LoggedController
{
  /**
   * Index
   * Display a listing of tourists.
   * To filter tourists, add any of the Tourist object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 71;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }
    $p_g = PermissionGroup::find($this->privileges['permission_group_id']);


    if($p_g != null){
      foreach($p_g->permissions as $permission){

        if($permission->text=="Tourists" && $permission->level==0)
        return response()->json(['data'=>[]]);
      }
    }
    $request_data = $this->extractRequestParams($request_data);

    $tourist = Tourist::Finder($request_data);

    $res = TouristTemplate::collection($tourist);
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Find tourist using request parameters
   */

  public function find(Request $request)
  {
    $reqExecTimeId = 71;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }
    $request_data = $this->extractRequestParams($request_data);

    $tourist = Tourist::Find($request_data);

    $res = TouristTemplate::collection($tourist);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of Tourist.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    $reqExecTimeId = 70;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = Tourist::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new Tourist.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 73;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $tourist = new Tourist;
    $tourist->setSelf($request->all());
    if ($tourist->isValide()) {
      $tourist->save(); 
      $res = TouristTemplate::collection(collect([$tourist]));

      if ($request->input('room_id')) {
        $deviceRoom = DeviceRoom::where(['room_id' => $request->input('room_id')])->first();
        if ($deviceRoom) {
          $d = new DeviceController();
          $request->request->add(['tourist_id'=>$tourist->id]);
          $d->switchTourist($request,$deviceRoom->device_id);
          $device = Device::findOrFail($deviceRoom->device_id);
          $device->status = 1;
          $device->save();
        }

      }
    } else {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Show
   * Display the specified Tourist by {id}.
   * */
  public function show(Request $request, $id)
  {
    $reqExecTimeId = 72;
    try {
      $tourist = Tourist::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($tourist->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
        if ($id != $this->privileges["tourist_id"])
          return response()->json([
            'message' => 'Forbidden'
          ], 403);
      }


      $res = TouristTemplate::collection(collect([$tourist]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  public function edit($id)
  {
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    //
  }

  /**
   * Update
   * Edit properties of existing Tourist.
   * */
  public function update(Request $request, $id)
  {
    $reqExecTimeId = 74;
    Log::debug($request->all());
    try {
      $tourist = Tourist::findOrFail($id);
      $request_data = $this->extractRequestParams($request->all());

      if ($this->privileges["role"] == 2 && !in_array($tourist->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
        $device = Device::findByImei($this->privileges["device_imei"]);
        if ($id != $device->device_room->stay->tourist->id)
          return response()->json([
            'message' => 'Forbidden'
          ], 403);
      }
      if ($this->privileges["role"] == 0 && $tourist->password != null && !Hash::check($request_data['password'], $tourist->password)) {
        return response()->json([
          'message' => 'Wrong password'
        ], 500);
      }
      if ($this->privileges["role"] == 0 && $tourist->password == null)
        unset($request_data['password']);

      if ($request->input('image')) {
        $data = base64_decode($request->input('image'));
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $data, FILEINFO_MIME_TYPE);
        $mime_type = explode('/', $mime_type)[1];
        finfo_close($f);
        $filename = str_random(15) . '.' . $mime_type;
        Storage::disk('public_uploads')->put("tourist_images/" . $filename, $data);
        $request_data['image'] = asset("uploads/tourist_images/" . $filename);
      }

      if ($request->file('image')) {
        $path = Storage::disk('public_uploads')->putFile('tourist_images', $request->file('image'));
        $request_data['image'] = asset('uploads/' . $path);
      }

      if (isset($request_data['new_password']) && $request_data['new_password'] != null) {
        if (!preg_match('/[0-9]{4,4}/', $request_data['new_password']))
          return response()->json([
            'message' => 'Password must be 4 digits'
          ], 500);
        $request_data['password'] = Hash::make($request_data['new_password']);
      } else
        unset($request_data['password']);

      $request_data = array_filter($request_data, function ($x) {
        return $x;
      });
      $tourist->setSelf($request_data);
      if ($tourist->isValide()) {
        $tourist->save();
      }
      $res = TouristTemplate::collection(collect([$tourist]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Destroy
   * Remove a Stay.
   * */
  public function destroy($id)
  {
    $reqExecTimeId = 75;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $this->workEnd($reqExecTimeId);
    
    $guest  = Tourist::findOrFail($id);
    $stay = Stay::where('tourist_id', $id)->first();
    if($stay !== null)
    {
      $deviceRoom = DeviceRoom::where('stay_id', $stay->id)->first();
      $deviceRoom->stay_id = null;
      $deviceRoom->save();
      $stay->device_room_id = null;
      $stay->save();
      $guest->delete();
      $stay->delete();
      return 'OK';
    }
    else {
      $guest->delete();
    }
    
  }

  public function extractRequestParams($request_data)
  {
    if (array_key_exists("languages", $request_data)) {
      $request_data['languages'] = Utils::MultiSearchStringArray($request_data["languages"]);
    }

    // if (array_key_exists("born", $request_data)) {
    //   $request_data['born'] = Utils::SearchRang($request_data["born"]);
    // }
    return parent::extractRequestParams($request_data);
  }

  public function getGuests(Request $request) {
    $guests = Tourist::where('hotel_id', $request->hotel_id)->where('check_out', '<', date('Y-m-d'))->where('stay_id', '!=', null)->where('leaved', 0)->get();
    foreach($guests as $guest) {
        $deviceRoom = DeviceRoom::where('stay_id', $guest->stay_id)->first();
        $device = Device::findOrFail($deviceRoom->device_id);
        $staysHistory = new StaysHistory;
        $staysHistory->tourist_id = $guest->id;
        $staysHistory->hotel_id = $guest->hotel_id;
        $staysHistory->room_id = $deviceRoom->room_id;
        $staysHistory->device_id = $deviceRoom->device_id;
        $staysHistory->save();
        $stay = Stay::find($guest->stay_id);
        $deviceRoom->update(['stay_id' => null]);
        $guest->update(['stay_id' => null, 'leaved' => 1]);
        $device->status = 0;
        $device->save();
        $stay->delete();
    }
    $guests = Tourist::where('hotel_id', $request->hotel_id)->where('leaved', 0)->get();
    return TouristTemplate::collection($guests);
  }

  // public function test(Request $request) {
  //   $guests = Tourist::where('hotel_id', $request->hotel_id)->orderBy('created_at', 'desc')->get();
  //   return TouristTemplate::collection($guests);
  // }
}
