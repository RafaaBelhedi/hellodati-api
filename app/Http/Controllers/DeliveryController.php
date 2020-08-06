<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\DeliveryPlace;
use App\Http\Resources\DeliveryPlaceTemplate;

/**
 * @group delivery
 *
 * APIs for managing templates of posts
 */
class DeliveryController extends LoggedController
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
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }
    $request_data = $this->extractRequestParams($request_data);

    $Deliverys = DeliveryPlace::Finder($request_data);

    $res = DeliveryPlaceTemplate::collection($Deliverys);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of DeliveryPlace.
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
    $res = DeliveryPlace::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new DeliveryPlace.
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
    $Delivery = new DeliveryPlace;
    $Delivery->setSelf($new_data);
    if ($Delivery->isValide()) {
      $Delivery->save();
      $res = new DeliveryPlaceTemplate($Delivery);
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
   * Display the specified DeliveryPlace by {id}.
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
      $Delivery = DeliveryPlace::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($tourist->hotel_id, $this->privileges["hotel_id"]))
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      $res = DeliveryPlaceTemplate::collection(collect([$Delivery]));
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
   * Edit properties of existing DeliveryPlace.
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
      $Delivery = DeliveryPlace::findOrFail($id);
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
      $res = new DeliveryPlaceTemplate($Delivery);
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
   * Remove a DeliveryPlace.
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
      $Delivery = DeliveryPlace::findOrFail($id);
      $Delivery->delete();
      $res = ['delele post review ' . $id => 'success'];
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  //Added by Rafaa
  public function getDeliveryPlaceById(Request $request)
  {
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $deliveryPlace = DeliveryPlace::find($request->delivery_place_id);
    $res = DeliveryPlaceTemplate::collection(collect([$deliveryPlace]));
    return $res;
  }
}
