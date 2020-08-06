<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\PostType;
use App\Http\Resources\PostTypeTemplate;

/**
 * @group PostTypes
 *
 * APIs for managing templates of posts
 */
class PostTypesController extends LoggedController
{
  /**
   * Index
   * Display a listing of posts templates.
   * To filter posts templates, add any of the PostType object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 47;
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

    $postTypes = PostType::Finder($request_data);

    $res = PostTypeTemplate::collection($postTypes);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of PostType.
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
    $res = PostType::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new PostType.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 49;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }

    $postType = new PostType;
    $postType->setSelf($new_data);
    if ($postType->isValide()) {
      $postType->save();
      $res = new PostTypeTemplate($postType);
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
   * Display the specified PostType by {id}.
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
      $postType = PostType::findOrFail($id);
      $res = PostTypeTemplate::collection(collect([$postType]));
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
   * Edit properties of existing PostType.
   * */
  public function update(Request $request, $id)
  {
    $reqExecTimeId = 50;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }


    try {
      $postType = PostType::findOrFail($id);
      $postType->setSelf($new_data);
      if ($postType->isValide()) {
        $postType->save();
      }
      $res = new PostTypeTemplate($postType);
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
   * Remove a PostType.
   * */
  public function destroy($id)
  {
    $reqExecTimeId = 51;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $postType = PostType::findOrFail($id);
      $postType->delete();
      $res = ['delele shopping Order ' . $id => 'success'];
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }
}
