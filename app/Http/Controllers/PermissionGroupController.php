<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\PermissionGroup;
use App\Http\Resources\PermissionGroupTemplate;

/**
 * @group PermissionGroup
 *
 * APIs for managing templates of ads
 */
class PermissionGroupController extends LoggedController
{
  /**
   * Index
   * Display a listing of permission_group templates.
   * To filter permission_group templates, add any of the PermissionGroup object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
    if($this->privileges['role']<2)
      return response()->json([
        'error'=>'forbidden'
      ]);
    $reqExecTimeId = 47;
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }
    $request_data = $this->extractRequestParams($request_data);

    $PermissionGroups = PermissionGroup::Finder($request_data);

    $res = PermissionGroupTemplate::collection(collect($PermissionGroups));
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of PermissionGroup.
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
    $res = PermissionGroup::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new PermissionGroup.
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
    $PermissionGroup = new PermissionGroup;
    $PermissionGroup->setSelf($new_data);
    if ($PermissionGroup->isValide()) {
      $PermissionGroup->save();
      $res = new PermissionGroupTemplate($PermissionGroup);
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
      $PermissionGroup = PermissionGroup::findOrFail($id);
      $res = PermissionGroupTemplate::collection(collect([$PermissionGroup]));
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
   * Edit properties of existing PermissionGroup.
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
      $PermissionGroup = PermissionGroup::findOrFail($id);

      $PermissionGroup->setSelf($new_data);
      if ($PermissionGroup->isValide()) {
        $PermissionGroup->save();
      }
      $res = new PermissionGroupTemplate($PermissionGroup);
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
   * Remove a PermissionGroup.
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
      $PermissionGroup = PermissionGroup::findOrFail($id);
      $PermissionGroup->delete();
      $res = ['delele permission group ' . $id => 'success'];
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }
}
