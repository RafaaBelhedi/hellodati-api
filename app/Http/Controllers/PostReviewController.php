<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\PostType;
use App\Post;
use App\Device;
use App\Http\Resources\PostReviewTemplate;
use App\Http\Resources\PostTypeTemplate;
use App\PostReview;
use App\Tourist;
use Illuminate\Support\Facades\Log;

/**
 * @group postReviews
 *
 * APIs for managing templates of posts
 */
class PostReviewController extends LoggedController
{
  /**
   * Index
   * Display a listing of posts templates.
   * To filter posts templates, add any of the postReview object properties to the querry ?{property}={value}
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

    $postReviews = PostReview::Finder($request_data);
    // $postReviews = PostReview::orderBy('created_at', 'DESC')->where('hotel_id', $request->hotel_id)->where('post_id', $request->post_id)->get();

    $res = PostReviewTemplate::collection($postReviews);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of postReview.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    $reqExecTimeId = 46;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden Create'
      ], 403);
    }
    $res = PostReview::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new postReview.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 49;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 0) {
      return response()->json([
        'message' => 'Forbidden Store'
      ], 403);
    }
    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }

    $postReview = new PostReview;
    Log::debug(print_r($new_data, true));
    $postReview->setSelf($new_data);
    if ($postReview->isValide()) {

      if ($postReview->exists($postReview->post_id, $postReview->tourist_id)) {
        $postReview = PostReview::where(['post_id' => $postReview->post_id, 'tourist_id' => $postReview->tourist_id])->first();
        return $this->update($request, $postReview->id);
      } else {
        $postReview->hotel_id = Post::findorfail($postReview->post_id)->hotel_id;
        $postReview->save();
        $res = new PostReviewTemplate($postReview);
      }
    } else {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 501);
    }

    Log::debug(print_r($res, true));

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
        'message' => 'Forbidden show'
      ], 403);
    }
    try {
      $postReview = PostReview::findOrFail($id);
      $res = PostReviewTemplate::collection(collect([$postReview]));
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
        'message' => 'Forbidden edit'
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
    $reqExecTimeId = 50;

    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 0) {
      return response()->json([
        'message' => 'Forbidden Update'
      ], 403);
    }

    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }

    try {
      $postReview = PostReview::findOrFail($id);
      $tourist = Tourist::findOrFail($postReview->tourist_id);
      if ($this->privileges["role"] != 3) {
        if ($this->privileges["tourist_id"] != $tourist->id)
          return response()->json([
            'message' => 'Forbidden'
          ], 403);
      }
      $postReview->setSelf($new_data);
      if ($postReview->isValide()) {
        $postReview->save();
      }
      $res = new PostReviewTemplate($postReview);
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 502);
    }
    $p = Post::find($postReview->post_id);
    if ($p != null)
      $p->updateRatings();

    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Destroy
   * Remove a PostReview.
   * */
  public function destroy($id)
  {
    $reqExecTimeId = 51;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 0) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $postReview = PostReview::findOrFail($id);
      $postReview->delete();
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
