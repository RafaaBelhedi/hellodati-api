<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\Room;
use App\Http\Resources\RoomTemplate;
use App\Http\Resources\DeviceRoomTemplate;
use App\DeviceRoom;
use App\Stay;
use App\StaysHistory;
use App\Tourist;
use App\PermissionGroup;

/**
 * @group Rooms
 *
 * APIs for managing rooms of hotels
 */
class RoomsController extends LoggedController
{
  /**
   * Index
   * Display a listing of rooms.
   * To filter rooms, add any of the Room object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 59;
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
    // dd($p_g->permissions);
    if($p_g != null){
      foreach($p_g->permissions as $permission){

        if($permission->text=="Rooms" && $permission->level==0)
        return response()->json(['data'=>[]]);
      }
    }
    $request_data = $this->extractRequestParams($request_data);
    $Rooms = Room::Finder($request_data);
    $res = RoomTemplate::collection($Rooms);
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  //Find room with request parameters

  public function find(Request $request)
  {
    $reqExecTimeId = 59;
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
    $Rooms = Room::Find($request_data);
    $res = RoomTemplate::collection($Rooms);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of Room.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    $reqExecTimeId = 58;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = Room::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new Room.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 61;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    if ($this->privileges["role"] == 2 && !in_array(intval(request("hotel_id")), $this->privileges["hotel_id"])) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }
    $Room = new Room;
    $Room->setSelf($new_data);
    if ($Room->isValide()) {
      $Room->save();
      $res = RoomTemplate::collection(collect([$Room]));
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
   * Display the specified Room by {id}.
   * */
  public function show($id)
  {
    $reqExecTimeId = 60;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $Room = Room::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($Room->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $res = RoomTemplate::collection(collect([$Room]));
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
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    //
  }

  /**
   * Update
   * Edit properties of existing Room.
   * */
  public function update(Request $request, $id)
  {
    $reqExecTimeId = 62;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $Room = Room::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($Room->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $Room->setSelf($request->all());
      if ($Room->isValide()) {
        $Room->save();
      }
      $res = RoomTemplate::collection(collect([$Room]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Toggle this room's active status. 
   * Active room becomes inactive.
   * Inactive room becomes active
   */

  public function toggle(Request $request, $id)
  {
    $reqExecTimeId = 62;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $Room = Room::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($Room->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $Room->status = $Room->status == 1 ? 0 : 1;
      if ($Room->isValide()) {
        $dr = DeviceRoom::where(['room_id' => $Room->id])->first();
        if ($dr && $Room->status == 0) {
          $stay = Stay::where(['device_room_id' => $dr->id]);
          if ($stay && $Room->status == 0)
            $stay->delete();
          $dr->delete();
        }
        $Room->save();
      }
      $res = RoomTemplate::collection(collect([$Room]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  

  public function getAvailableRooms() {

    $availabeRooms = Room::where('status', 1)->where('hotel_id', $this->privileges['hotel_id'])->get();
    
    return RoomTemplate::collection($availabeRooms);

  }

  public function getRoomByRoomNumber(Request $request) {

    if ($this->privileges["role"] !== 3 && $this->privileges["role"] !== 2) {
      return response()->json([
        'message' => 'Forbidden1'
      ], 403);
    }

    if (is_array($this->privileges['hotel_id'])) {
      if (!in_array($request->hotel_id, $this->privileges['hotel_id'])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
    }

    else {
      if($request->hotel_id !== $this->privileges['hotel_id'])
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }

    $room = new Room;
  
    $room = $room->where('hotel_id', $request->hotel_id);
  
    $room = $room ->where('room_number', $request->room_number)->get();

    return RoomTemplate::collection($room);
    
  }

  public function isFull($RoomID) {

    $room = Room::findOrFail($RoomID);
    $deviceRooms = DeviceRoom::where('room_id', $RoomID)->where('stay_id', '!=', null)->get();
    if ($deviceRooms->count() == $room->capacity)
      return 1;
    else
      return 0;
    
  }

  /**
   * Destroy
   * Remove a Room.
   * */
   public function destroy($id)
   {
     $room = Room::findOrFail($id);
     if (count($room->device_rooms) > 0) {
       return 0;
     }
     $room->delete();
     $res = 1;
   }

   public function unlink($id) {
     $room = Room::findOrFail($id);
     $deviceRoom = DeviceRoom::where('room_id', $id)->first();
     if ($deviceRoom->stay_id != null) {
       $stayID = $deviceRoom->stay_id;
       $deviceRoom->stay_id = null;
       $deviceRoom->save();
       $stay = Stay::findOrFail($stayID);
       $stay->device_room_id = null;
       $stay->save();
       $tourist = Tourist::where('stay_id', $stayID)->first();
       $tourist->stay_id = null;
       $tourist->save();
       $staysHistory = new StaysHistory();
       $staysHistory->tourist_id = $tourist->id;
       $staysHistory->hotel_id = $tourist->hotel_id;
       $staysHistory->room_id = $id;
       $staysHistory->device_id = $deviceRoom->device_id;
       $staysHistory->save();
       $deviceRoom->delete();
       $stay->delete();
     } else 
       $deviceRoom->delete();
     
     $room->linked_to_device = 0;
     $room->save();
     return response()->json('Room unlinked successfully');
   }

}
