<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\Publicity;
use App\Http\Resources\PublicityTemplate;

/**
 * @group Publicity
 *
 * APIs for managing templates of ads
 */
class PublicityController extends LoggedController
{
  /**
   * Index
   * Display a listing of publicity templates.
   * To filter publicity templates, add any of the Publicity object properties to the querry ?{property}={value}
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

    $Publicitys = Publicity::Finder($request_data);

    $res = PublicityTemplate::collection($Publicitys);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of Publicity.
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
    $res = Publicity::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new Publicity.
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
    $Publicity = new Publicity;
    $Publicity->setSelf($new_data);
    if ($Publicity->isValide()) {
      $Publicity->save();
      $res = new PublicityTemplate($Publicity);
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
    $reqExecTimeId = 48;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $Publicity = Publicity::findOrFail($id);
      $res = PublicityTemplate::collection(collect([$Publicity]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  //Add one click to the specific publicity

  public function click($id)
  {
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 0) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $reqExecTimeId = 48;
    try {
      $Publicity = Publicity::findOrFail($id);
      $Publicity->clicks += 1;
      $Publicity->save();
      $res = PublicityTemplate::collection(collect([$Publicity]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  //Add one view to the specific publicity

  public function view($id)
  {
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 0) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $reqExecTimeId = 48;
    try {
      $Publicity = Publicity::findOrFail($id);
      $Publicity->views += 1;
      $Publicity->save();
      $res = PublicityTemplate::collection(collect([$Publicity]));
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
   * Edit properties of existing Publicity.
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
      $Publicity = Publicity::findOrFail($id);

      $Publicity->setSelf($new_data);
      if ($Publicity->isValide()) {
        $Publicity->save();
      }
      $res = new PublicityTemplate($Publicity);
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
   * Remove a Publicity.
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
      $Publicity = Publicity::findOrFail($id);
      $Publicity->delete();
      $res = ['delele post review ' . $id => 'success'];
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }
}
