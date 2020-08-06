<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\Room;
use App\Device;
use App\DeviceRoom;
use App\Hotel;
use App\Http\Resources\DevicesTemplate;
use App\Http\Resources\DeviceTemplate;
use App\Http\Resources\DeviceRoomTemplate;
use App\Http\Resources\DeviceContactsTemplate;
use App\PermissionGroup;
use App\Stay;
use App\PushNotification;
use Illuminate\Support\Facades\DB;

/**
 * @group Device
 *
 * APIs for managing device
 */
class DeviceController extends LoggedController
{
  /**
   * Index
   * Display a listing of device.
   * To filter devices, add any of the Device object properties to the querry ?{property}={value}
   *
   * 
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 17;
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {

      $request_data = $request->all();
    }
    $p_g = PermissionGroup::find($this->privileges['permission_group_id']);
    // dd($p_g->permissions);
    if ($p_g != null) {
      foreach ($p_g->permissions as $permission) {

        if ($permission->text == "Devices" && $permission->level == 0)
          return response()->json(['data' => []]);
      }
    }
    $request_data = $this->extractRequestParams($request_data);
    $Devices = Device::Finder($request_data);
    $res = DeviceTemplate::collection($Devices);
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  public function find(Request $request)
  {
    $reqExecTimeId = 17;
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {

      $request_data = $request->all();
    }
    $request_data = $this->extractRequestParams($request_data);
    $Devices = Device::Find($request_data);
    $res = DeviceTemplate::collection($Devices);
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Contacts
   * Display a listing of rooms and relative phone number within the same hotel.
   * To filter devices, add any of the Device object properties to the querry ?{property}={value}
   * */
  public function index_contacts(Request $request)
  {
    $reqExecTimeId = 18;
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }
    $request_data = $this->extractRequestParams($request_data);
    $request_data['imei'] = null;
    $request_data['has_room'] = true;
    $Devices = Device::Finder($request_data);
    $res = DeviceContactsTemplate::collection($Devices);
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Columns
   * Display the possible fields of Device.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    $reqExecTimeId = 16;
    if ($this->privileges["role"] != 3  && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = Device::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new Device.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 20;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $Device = null;
    try {
      $Device = Device::where('imei', Utils::validate_imei($request->input('imei')))->firstOrFail();
    } catch (ModelNotFoundException $e) {
      $Device = null;
    }


    if ($Device == null) {
      $new_data = json_decode($request->getContent(), true);
      if ($new_data == null) {
        $new_data = $request->all();
      }
      $Device = new Device;
      $Device->setSelf($new_data);
      if ($Device->isValide()) {
        $Device->save();
        $res = DeviceTemplate::collection(collect([$Device]));
        return response()->json('Device added successfully');
      } else {
        $res = response()->json([
          'error' => "Invalid_or_missing_fields"
        ], 500);
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
   * Display the specified App by {id}.
   * */
  public function show($id)
  {
    $reqExecTimeId = 19;
    try {
      $Device = Device::findOrFail($id);
      if (
        $this->privileges["role"] == 3 || ($this->privileges["role"] == 2 && in_array($Device->hotel_id, $this->privileges["hotel_id"])) ||
        $this->privileges["device_imei"] == $Device->imei
      ) {
        //allowed
      } else {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $res = DeviceTemplate::collection(collect([$Device]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  public function showByImeiLocal($imei)
  {
    return Device::findByImei($imei);
  }


  public function edit($id)
  {
    //
  }

  /**
   * Update
   * Edit properties of existing Device.
   * */
  public function update(Request $request, $id)
  {
    $reqExecTimeId = 21;
    try {
      $device = Device::findOrFail($id);
      if (
        $this->privileges["role"] == 3 || ($this->privileges["role"] == 2 && in_array($device->hotel_id, $this->privileges["hotel_id"])) ||
        $this->privileges["device_imei"] == $device->imei
      ) {
        //allowed
      } else {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      

      $device->setSelf(array_filter($request->all(), function ($value) {

        return !is_null($value);
      }));
      if ($device->isValide()) {
        $device->save();
        $res = DeviceTemplate::collection(collect([$device]));
      } else {
        $res = response()->json([
          'error' => "Invalid_or_missing_fields"
        ], 500);
      }
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Link to room
   * Switch the room to where a device is linked.
   * 
   * @bodyParam room_id integer If 'room_id' equales <b>Null</b> or <b>0</b> or if 'room_id' does not belong to same Hotel as the device, the result will be a device with no link to any room..
   * */
  function switchRoom(Request $request, $id)
  {
    $reqExecTimeId = 103;
    try {
      $device = Device::findOrFail($id);
      $room = Room::findOrFail($request->input('room_id'));
      if (
        $this->privileges["role"] == 3 || ($this->privileges["role"] == 2 && in_array($device->hotel_id, $this->privileges["hotel_id"])) ||
        $this->privileges["device_imei"] == $device->imei
      ) {
      } else {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $deviceRoomCtrl = new DeviceRoomsController();

      if ($device->hotel_id != $room->hotel_id) {
        $res = response()->json([
          'error' => "Device and room are in different hotels"
        ], 500);
        return $res;
      }
      if ($device->device_room != null && $device->device_room->id > 0) {
        if ($device->device_room->stay != null && $device->device_room->stay->id > 0) {
          $stayCtrl = new StaysController();
          $stayCtrl->destroy($device->device_room->stay->id);
        }
        $deviceRoomCtrl->destroy($device->device_room->id);
      }
      try {
        $request->request->add(['device_id' => $id]);
        $request->request->add(['hotel_id' => $device->hotel_id]);

        $deviceRoomCtrl->store($request);
      } catch (ModelNotFoundException $e) {
      }
      $device = Device::findOrFail($id);
      $res = DeviceTemplate::collection(collect([$device]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Link to tourist
   * Switch the tourist to where a device is linked.
   * 
   * @bodyParam tourist_id integer If 'tourist_id' equales <b>Null</b> or <b>0</b> or if 'tourist_id' does not belong to same Hotel as the device, the result will be a device with no link to any tourist..
   * */
  function switchTourist(Request $request, $id)
  {
    //$reqExecTimeId=103;
    try {
      $device = Device::findOrFail($id);
      $device->setSelf(['status' => 1]);
      $device->save();
      if (
        $this->privileges["role"] == 3 || ($this->privileges["role"] == 2 && in_array($device->hotel_id, $this->privileges["hotel_id"]))
      ) {
      } else {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }

      if ($device->device_room != null && $device->device_room->id > 0) {
        $stayCtrl = new StaysController();
        if ($device->device_room->stay != null && $device->device_room->stay->id > 0) {
          $stayCtrl->destroy($device->device_room->stay->id);
          $stays = Stay::where(['tourist_id' => $request->input('tourist_id')])->get();
          foreach ($stays as $stay) {
            $stayCtrl->destroy($stay->id);
          }
        }

        $request->request->add(['device_room_id' => $device->device_room->id]);
        PushNotification::where(['imei' => $device->imei])->update(['hidden' => 1]);
        $stayCtrl->store($request);
        $device = Device::findOrFail($id);

        $res = DeviceTemplate::collection(collect([$device]));
        if ($device->token == 1) {
          $hotel = Hotel::find($device->hotel_id);
          $hotel->tokens = $hotel->tokens - 1;
          $hotel->save();
        }
      } else {
        $res = response()->json([
          'error' => "device must be linked to a room"
        ], 500);
      }
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    //$this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Destroy
   * Remove a Device.
   * */
  public function destroy($id)
  {
    $reqExecTimeId = 22;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $Device = Device::findOrFail($id);
      if ($Device->device_room_id > 0) {
        return 0;
      }
      $Device->delete();
      $res = 1;
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  public function extractRequestParams($request_data)
  {

    if ($this->privileges['role'] == 3 || $this->privileges['role'] == 2) {
      if (array_key_exists("imei", $request_data)) {
        $request_data['imei'] = $request_data["imei"];
      }
    } elseif (($this->privileges['role'] == 1 || $this->privileges['role'] == 0) && $this->privileges["device_imei"] != null) {
      $request_data['imei'] = $this->privileges["device_imei"];
    } else {
      $request_data['imei'] = "unexisting imei";
    }

    if (array_key_exists("status", $request_data)) {
      if (gettype($request_data["status"]) == "array") {
        $request_data['status'] = preg_replace('/\D/', '', $request_data["status"]);
      } else if (gettype($request_data["status"]) == "object") {
        $request_data['status'] = preg_replace('/\D/', '', json_decode($request_data["status"], true));
      } else  if (gettype($request_data["status"]) == "string") {
        $request_data['status'] = preg_replace('/[^0-9,]/', '', $request_data["status"]);
        $request_data['status'] = explode(',', $request_data['status']);
      } else if (gettype($request_data["status"]) == "integer" || gettype($request_data["status"]) == "double") {
        $request_data['status'] = [intval($request_data['status'])];
      } else {
        $request_data['status'] = null;
      }
    } else {
      $request_data['status'] = null;
    }
    return parent::extractRequestParams($request_data);
  }

  public function getAvailableDevices(Request $request)
  {

    $availableDevices = DB::table("devices")->select('*')->where('hotel_id', $request->hotel_id)->whereNotIn('id', function ($query) {

      $query->select('device_id')->from('device_room');
    })->get();
    return DevicesTemplate::collection($availableDevices);
  }

  public function getUnAvailableDevices(Request $request)
  {

    $unAvailableDevices = DB::table("devices")->select('*')->where('hotel_id', $request->hotel_id)->whereIn('id', function ($query) {

      $query->select('device_id')->from('device_room');
    })->get();
    return DevicesTemplate::collection($unAvailableDevices);
  }


  public function clearData(Request $request){
    request()->validate([
      'imei' => 'required|string'
    ]);
    $notif = array(
      'title' => 'clear data',
      'summery' => 'Clearing device data',
      'clear_data' => true
    );
    $request->request->add(['data' => json_encode($notif)]);
    $notifyDevicesController = new NotifyDevicesController();
    return $notifyDevicesController->notifyHotelsDevices($request);
  }
}
