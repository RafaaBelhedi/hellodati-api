<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\PlannedNotification;
use App\Http\Resources\PlannedNotificationTemplate;

/**
 * @group delivery
 *
 * APIs for managing templates of posts
 */
class PlanController extends LoggedController
{
  /**
   * Index
   * Display a listing of delivery places.
   * To filter delivery places, add any of the Delivery object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 47;
    if($this->privileges['role']<2)
    return response()->json([
      'message' => 'Forbidden'
    ], 403);
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }
    $request_data = $this->extractRequestParams($request_data);

    $Deliverys = PlannedNotification::Finder($request_data);

    $res = PlannedNotificationTemplate::collection($Deliverys);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of PlannedNotification.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    $reqExecTimeId = 46;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = PlannedNotification::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new PlannedNotification.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 49;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }
    $Delivery = new PlannedNotification;
    $Delivery->setSelf($new_data);
      $Delivery->save();
      $res = new PlannedNotificationTemplate($Delivery);
    
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Show
   * Display the specified PlannedNotification by {id}.
   * */
  public function show($id)
  {
    $reqExecTimeId = 48;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $Delivery = PlannedNotification::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($tourist->hotel_id, $this->privileges["hotel_id"]))
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      $res = PlannedNotificationTemplate::collection(collect([$Delivery]));
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
   * Edit properties of existing PlannedNotification.
   * */
  public function update(Request $request, $id)
  {
    $reqExecTimeId = 50;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }

    try {
      $Delivery = PlannedNotification::findOrFail($id);
      if ($this->privileges["role"] != 3) {
        if ($this->privileges["role"] == 2 && !in_array($tourist->hotel_id, $this->privileges["hotel_id"]))
          return response()->json([
            'message' => 'Forbidden'
          ], 403);
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $Delivery->setSelf($new_data);
      if ($Delivery->isValide()) {
        $Delivery->save();
      }
      $res = new PlannedNotificationTemplate($Delivery);
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
   * Remove a PlannedNotification.
   * */
  public function destroy($id)
  {
    $reqExecTimeId = 51;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $Delivery = PlannedNotification::findOrFail($id);
      $Delivery->delete();
      $res = ['delele planned notification ' . $id => 'success'];
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }
}
