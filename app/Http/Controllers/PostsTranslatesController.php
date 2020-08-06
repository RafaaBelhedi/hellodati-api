<?php

namespace App\Http\Controllers;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\PostsTranslate;
use App\PostType;
use App\Post;
use App\Http\Resources\PostsTranslatesTemplate;

/**
 * @group PostsTranslates
 *
 * APIs for managing translations of PostTranslates that belongs in the hotels
 */

class PostsTranslatesController extends LoggedController
{
  /**
   * Index
   * Display a listing of posts translates.
   * To filter posts translate, add any of the PostTranslate object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
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

    $postsTranslate = PostsTranslate::Finder($request_data);

    return PostsTranslatesTemplate::collection($postsTranslate);
  }


  /**
   * Columns
   * Display the possible fields of PostsTranslate.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    return PostsTranslate::getEditableColumns();
  }

  /**
   * Store
   * Create a new PostsTranslate.
   * */
  public function store(Request $request)
  {
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }

    $postsTranslate = new PostsTranslate;
    $postsTranslate->setSelf($request->all());
    try {
      $postType = PostType::findOrFail($postsTranslate->post->type);
      $post = Post::findOrFail($request->input('post_id'));
      $validations = $post->isValideByType($postType);
    } catch (ModelNotFoundException $e) {
      $postType = null;
      $validations = false;
    }

    if ($validations === true) {
      $postsTranslate->save();
      $res = PostsTranslatesTemplate::collection(collect([$postsTranslate]));
    } else {
      if (is_array($validations)) {
        $res = response()->json([
          'Invalid_or_missing_fields' => $validations
        ], 500);
      } else {
        $res = response()->json([
          'message' => 'invalide entrie(s)'
        ], 500);
      }
    }
    return $res;
  }

  /**
   * Show
   * Display the specified PostTranslate by {id}.
   * */
  public function show($id)
  {
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $postsTranslate = PostsTranslate::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($postsTranslate->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $res = PostsTranslatesTemplate::collection(collect([$postsTranslate]));
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
   * Edit properties of existing PostTranslates.
   * */
  public function update(Request $request, $id)
  {
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $postsTranslate = PostsTranslate::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($postsTranslate->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $postsTranslate->setSelf($request->all());
      try {
        $postType = PostType::findOrFail($postsTranslate->post->type);
        $validations = $postsTranslate->isValideByType($postType);
      } catch (ModelNotFoundException $e) {
        $postType = null;
        $validations = false;
      }

      if ($validations === true) {
        $postsTranslate->save();
        $res = PostsTranslatesTemplate::collection(collect([$postsTranslate]));
      } else {
        if (is_array($validations)) {
          $res = response()->json([
            'Invalid_or_missing_fields' => $validations
          ], 500);
        } else {
          $res = response()->json([
            'message' => 'invalide entrie(s)'
          ], 500);
        }
      }
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'message' => 'invalide entrie(s)'
      ], 500);
    }

    return $res;
  }

  /**
   * Destroy
   * Remove a PostTranslate.
   * */
  public function destroy($id)
  {
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $postsTranslate = PostsTranslate::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($postsTranslate->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $postsTranslate->delete();
      $res = ['delele post_translate ' . $id => 'success'];
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    return $res;
  }
}
