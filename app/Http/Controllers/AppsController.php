<?php

namespace App\Http\Controllers;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\App;
use App\Http\Resources\AppTemplate;

/**
 * @group apps
 *
 * APIs for managing apps
 */
class AppsController extends LoggedController
{
  /**
   * Index
   * Display a listing of apps.
   * To filter apps, add any of the apps object properties to the querry ?{property}={value}
   *
   * 
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 89;
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }
    $request_data = $this->extractRequestParams($request_data);

    $apps = App::Finder($request_data);

    $res = AppTemplate::collection($apps);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of App.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    $reqExecTimeId = 88;
    if ($this->privileges["role"] != 3  && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = App::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new App.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 91;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $app = new App;
    $app->setSelf($request->all());
    if ($app->isValide()) {
      $app->save();
      $res = AppTemplate::collection(collect([$app]));
    } else {
      $res = response()->json([
        'Invalid_or_missing_fields' => "error"
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
    $reqExecTimeId = 90;
    try {
      $app = App::findOrFail($id);
      $res = AppTemplate::collection(collect([$app]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'Invalid_or_missing_fields' => "error"
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
   * Edit properties of existing app.
   * */
  public function update(Request $request, $id)
  {
    $reqExecTimeId = 92;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $app = App::findOrFail($id);
      $app->setSelf($request->all());
      if ($app->isValide()) {
        $app->save();
      }
      $res = AppTemplate::collection(collect([$app]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'Invalid_or_missing_fields' => "error"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Destroy
   * Remove an app.
   * */
  public function destroy($id)
  {
    $reqExecTimeId = 93;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $app = App::findOrFail($id);
      $app->delete();
      $res = ['delele app ' . $id => 'success'];
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'Invalid_or_missing_fields' => "error"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }
}
