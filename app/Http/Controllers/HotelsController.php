<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\Hotel;
use App\Post;
use App\Http\Resources\HotelTemplate;

/**
 * @group Hotel
 *
 * APIs for managing hotels
 */

class HotelsController extends LoggedController
{
  /**
   * Index
   * Display a listing of hotels.
   * To filter hotels, add any of the Hotel object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 53;
    if ($this->privileges["role"] == 3) {
      if (gettype($request->getContent()) == "object") {
        $request_data = json_decode($request->getContent(), true);
      } else {
        $request_data = $request->all();
      }
      $request_data = $this->extractRequestParams($request_data);
      $Hotels = Hotel::Finder($request_data);
      $res = HotelTemplate::collection($Hotels);
    } else if ($this->privileges["role"] == 2) {
      if (gettype($request->getContent()) == "object") {
        $request_data = json_decode($request->getContent(), true);
      } else {
        $request_data = $request->all();
      }
      $request_data = $this->extractRequestParams($request_data);
      $request_data['id'] = $this->privileges["hotel_id"];
      $Hotels = Hotel::Finder($request_data);
      $res = HotelTemplate::collection($Hotels);
    } else {
      $res = response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of Hotel.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    $reqExecTimeId = 52;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = Hotel::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new Hotel.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 55;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }
    $Hotel = new Hotel;
    $Hotel->setSelf($new_data);
    if ($Hotel->isValide()) {
      $Hotel->save();
      //create root post
      // $postController = new PostController();
      // $postController->cloneHotelPosts(43, $Hotel->id, $Hotel->hotel_name);
      $post = new Post();
      $post->hotel_id = $Hotel->id;
      $post->categories = 'rootCategoryHotel';
      $post->image = $request->image;
      $post->cover = $request->cover;
      $post->save();

      $res = new HotelTemplate($Hotel);
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
    $reqExecTimeId = 54;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2 && $this->privileges["role"] != 0) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    if ($this->privileges["role"] == 2 && !in_array($id, $this->privileges["hotel_id"])) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $Hotel = Hotel::findOrFail($id);
      if ($this->privileges["role"] == 0 && $this->privileges["hotel_id"] != $id)
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      $res = HotelTemplate::collection(collect([$Hotel]));
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
   * Edit properties of existing DeviceRoom.
   * */
  public function update(Request $request, $id)
  {
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $Hotel = Hotel::findOrFail($id);
      $new_data = json_decode($request->getContent(), true);
      if ($new_data == null) {
        $new_data = $request->all();
      }
      $Hotel_new = $Hotel;
      $Hotel_new->setSelf($new_data);
      $post = Post::where('hotel_id', $id)->where('parent_id', null)->first();
      if ($request->image != null) 
        $post->image = $request->image;
      if ($request->cover != null)
        $post->cover = $request->cover;
      $post->save();
      if ($Hotel_new->isValide()) {
        $Hotel_new->save();
        $res = new HotelTemplate($Hotel_new);
      } else {
        $res = new HotelTemplate($Hotel);
      }
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    return $res;
  }

  /**
   * Destroy
   * Remove an DeviceRoom.
   * */
  public function destroy($id)
  {
    $reqExecTimeId = 57;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $Hotel = Hotel::findOrFail($id);
      $Hotel->delete();
      $res = ['delele hotel ' . $id => 'success'];
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }
}
