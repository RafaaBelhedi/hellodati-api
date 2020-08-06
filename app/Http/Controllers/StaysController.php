<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;


use App\Http\Requests;
use App\Http\Utils;
use App\Stay;
use App\Http\Resources\StayTemplate;

/**
 * @group Stays
 *
 * APIs for managing stays which represent the relation between a Tourist and DeviceRoom
 */

class StaysController extends LoggedController
{
  /**
   * Index
   * Display a listing of stays.
   * To filter stays, add any of the Stay object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 77;
    if ($this->privileges["role"] != 3  && $this->privileges["role"] != 2) {
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

    $stay = Stay::Finder($request_data);

    $res = StayTemplate::collection($stay);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of Stay.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    $reqExecTimeId = 76;
    if ($this->privileges["role"] != 3  && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = Stay::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new Stay.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 79;
    if ($this->privileges["role"] != 3  && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $stay = new Stay;
    $stay->setSelf($request->all());
    if ($stay->isValide()) {
      $stay->save();
      $res = StayTemplate::collection(collect([$stay]));
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
    $reqExecTimeId = 78;
    if ($this->privileges["role"] != 3  && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $stay = Stay::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($stay->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $res = StayTemplate::collection(collect([$stay]));
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
   * Edit properties of existing Stay.
   * */
  public function update(Request $request, $id)
  {
    $reqExecTimeId = 80;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $stay = Stay::findOrFail($id);
      $stay->setSelf($request->all());
      if ($stay->isValide()) {
        $stay->save();
      }
      $res = StayTemplate::collection(collect([$stay]));
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
    $reqExecTimeId = 81;
    if ($this->privileges["role"] != 3  && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $stay = Stay::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($stay->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $stay->device_room_id = null;
      $stay->save();
      $stay->delete();
      $res = ['delele stay ' . $id => 'success'];
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
    if (array_key_exists("reserved_time_from", $request_data)) {
      $request_data['reserved_time_from'] = Utils::SearchRang($request_data["reserved_time_from"]);
    } else {
      $request_data['reserved_time_from']['min'] = null;
      $request_data['reserved_time_from']['max'] = null;
    }

    if (array_key_exists("reserved_time_to", $request_data)) {
      $request_data['reserved_time_to'] = Utils::SearchRang($request_data["reserved_time_to"]);
    } else {
      $request_data['reserved_time_to']['min'] = null;
      $request_data['reserved_time_to']['max'] = null;
    }

    if (array_key_exists("join_time", $request_data)) {
      $request_data['join_time'] = Utils::SearchRang($request_data["join_time"]);
    } else {
      $request_data['join_time']['min'] = null;
      $request_data['join_time']['max'] = null;
    }

    if (array_key_exists("leave_time", $request_data)) {
      $request_data['leave_time'] = Utils::SearchRang($request_data["leave_time"]);
    } else {
      $request_data['leave_time']['min'] = null;
      $request_data['leave_time']['max'] = null;
    }
    return parent::extractRequestParams($request_data);
  }

  public function attachGuestToHotel(Request $request) {
    
    $stay = new Stay;
    $stay->tourist_id = $request->tourist_id;
    $stay->hotel_id = $request->hotel_id;
    $stay->save();

    return StayTemplate::collection(collect([$stay]));
  }

  public function switchRoom(Request $request) {
    $stay = Stay::where('tourist_id', $request->tourist_id)->first();
    $oldDeviceRoomID = $stay->device_room_id;
    $stay->device_room_id = null;

    $deviceRoom = DeviceRoom::findOrFail($oldDeviceRoomID);
    $deviceRoom->stay_id = null;
    $deviceRoom->save();
    
    $stay->device_room_id = $request->device_room_id;
    $stay->save();
    return 'Succes for the changes';
  }
}
