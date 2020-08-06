<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;


use App\Http\Requests;
use App\Http\Utils;
use App\PostType;
use App\Post;
use App\PostsTranslate;
use App\Http\Resources\PostTemplate;
use App\Http\Resources\PostMinTemplate;
use App\Http\Resources\PostsTranslatesTemplate;

/**
 * @group Posts
 *
 * APIs for managing posts that belongs in the hotels
 */

class PostController extends LoggedController
{
  /**
   * Index
   * Display a listing of posts.
   * To filter posts, add any of the Post object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 24;
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }

    $request_data = $this->extractRequestParams($request_data);
    
    $posts = Post::Finder($request_data);
    
    if (array_key_exists("compact_res", $request_data) && ($request_data["compact_res"] === "true" || $request_data["compact_res"] === true)) {
      $res = PostMinTemplate::collection($posts);
    } else {
      $res = PostTemplate::collection($posts);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of Post.
   * These fields can also be used to filter the search.
   * 
   * * <b>Please note: </b> the property 'post_type' refers to the template
   * used for the post, which can override a post.property's default
   * value or force it to be required.
   * */
  public function create()
  {
    $reqExecTimeId = 23;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = Post::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new Post.
   * <b>Please note: </b> 
   * - When creating new Post you can pass PostTranslate properties, this will create an english translation for the Post.
   * - Every Post has at least one translation so providing an english title is required.
   * - Required fields are defined by the chosen PostType, including the once that belong to the PostTranslate.
   * 
   * @transformerModel \App\Post
   * */
  public function store(Request $request)
  {
    
    $reqExecTimeId = 26;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    if ($this->privileges["role"] == 2 && !in_array(intval(request("hotel_id")), $this->privileges["hotel_id"])) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $post = new Post;
    $post->setSelf($request->all());
    try {
      $postType = PostType::findOrFail($post->type);
      $validations = $post->isValideByType($postType);
    } catch (ModelNotFoundException $e) {
      $postType = null;
      $validations = false;
    }
    if ($validations === true) {
      $post->save();
      $translatesData = $request->only(['title', 'summery', 'description', 'addition_data_1_text']);
      $translatesData["post_id"] = $post->id;
      $translatesData["lang_iso"] = 'en';

      $postsTranslate = new PostsTranslate;
      $postsTranslate->setSelf($translatesData);
      $validations = $postsTranslate->isValideByType($postType);
      if ($validations === true) {
        $postsTranslate->save();
        $res = PostTemplate::collection(collect([$post]));
        
        $l = new \App\Log();
        $l->setSelf(['user_id'=>$this->privileges["user_id"], 'text'=>'Created Post with Title '.$postsTranslate->title]);
      } else {
        $post->delete();
        if (is_array($validations)) {
          $res = response()->json([
            'Invalid_or_missing_fields' => $validations
          ], 500);
        } else {
          $res = response()->json([
            'message' => 'invalide entrie(s)2'
          ], 500);
        }
      }
    } else if (is_array($validations)) {
      $res = response()->json([
        'Invalid_or_missing_fields' => $validations
      ], 500);
    } else {
      $res = response()->json([
        'message' => 'invalide entrie(s)1'
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
    $reqExecTimeId = 25;
    try {
      $post = Post::findOrFail($id);
      if ($this->privileges["role"] == 3 || ($this->privileges["role"] == 2 && in_array($post->hotel_id, $this->privileges["hotel_id"])) || $post->hotel_id == $this->privileges["hotel_id"]) { } else {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $res = PostTemplate::collection(collect([$post]));
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
   * Edit properties of existing DeviceRoom.
   * */
  public function update(Request $request, $id)
  {
    $reqExecTimeId = 27;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $post = Post::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($post->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $post->setSelf($request->all());
      try {
        $postType = PostType::findOrFail($post->type);
        $validations = $post->isValideByType($postType);
      } catch (ModelNotFoundException $e) {
        $postType = null;
        $validations = false;
      }
      if ($validations === true) {
        
        $post->save();
        $request_data = [
          "post_id" => $post->id,
          "lang_iso" => "en",
        ];
        $translatesData = $request->only(['title', 'summery', 'description', 'addition_data_1_text']);
        $postsTranslate = PostsTranslate::Finder($request_data);
        if (count($postsTranslate) > 0) {
          $postsTranslate = $postsTranslate[0];
        } else {
          $translatesData["post_id"] = $post->id;
          $translatesData["lang_iso"] = 'en';
          $postsTranslate = new PostsTranslate;
        }
        $postsTranslate->setSelf($translatesData);
        $validations = $postsTranslate->isValideByType($postType);
        if (count($translatesData) == 0)
          $res = PostTemplate::collection(collect([$post]));
        else if ($validations === true) {
          $postsTranslate->save();
          $res = PostTemplate::collection(collect([$post]));
          
        $l = new \App\Log();
        $l->setSelf(['user_id'=>$this->privileges["user_id"], 'text'=>'Edited Post with Title '.$postsTranslate->title]);
        $l->save();
        } else if (is_array($validations)) {
          $res = response()->json([
            'Invalid_or_missing_fields' => $validations
          ], 500);
        } else {
          $res = response()->json([
            'message' => 'invalide entrie(s)1'
          ], 500);
        }
      } else if (is_array($validations)) {
        $res = response()->json([
          'Invalid_or_missing_fields' => $validations
        ], 500);
      } else {
        $res = response()->json([
          'message' => 'invalide entrie(s)2'
        ], 500);
      }
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'message' => 'invalide entrie(s)3'
      ], 500);
    }

    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Multi-Update
   * Edit properties of multiple existing Post.
   * */
  public function MultiUpdate(Request $request)
  {
    $reqExecTimeId = 28;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $success = [];
      $fail = [];
      $requests = $request->all();
      $updating_posts = json_decode($requests["posts"], true);
      if ($updating_posts == null) return [];
      foreach ($updating_posts as $updating_post) {
        $post = Post::findOrFail($updating_post['id']);
        if ($this->privileges["role"] == 2 && !in_array($post->hotel_id, $this->privileges["hotel_id"])) {
          return response()->json([
            'message' => 'Forbidden'
          ], 403);
        }
        $post->setSelf($updating_post);
        $validation = $post->isValide();
        if ($validation === true) {
          $post->save();
          array_push($success, $updating_post['id']);
        } else {
          $fail[$updating_post['id']] = $validation;
        }
      }
      $res = response()->json([
        'success' => $success,
        'fail' => $fail
      ], 200);
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Seen
   * Increment the property 'nbr_views' of an Post by 1.
   */
  public function seen($id)
  {
    $reqExecTimeId = 38;
    if ($this->privileges["role"] == 2 && !in_array($post->hotel_id, $this->privileges["hotel_id"])) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $post = Post::findOrFail($id);
      $post->incrimentViews();
      if ($post->isValide()) {
        $post->save();
      }
      $res = PostTemplate::collection(collect([$post]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  public function cloneHotelPosts($from_hotel, $to_hotel, $hotel_name)
  {
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }

    if ($from_hotel < 1 || $to_hotel < 1) return;
    $posts_data_search = [
      'hotel_id' => $from_hotel,
      'state' => 1
    ];
    $posts = Post::Finder($posts_data_search);
    $newPostsIds = [];
    for ($i = 0; $i < count($posts); $i++) {
      $postCopy = $posts[$i]->replicate();
      $postCopy->hotel_id = $to_hotel;

      if ($postCopy->parent_id != null && array_key_exists($postCopy->parent_id, $newPostsIds)) {
        $postCopy->parent_id = $newPostsIds[$postCopy->parent_id];
        $postCopy->save();
        $newPostsIds[$posts[$i]->id] = $postCopy->id;
      } else if ($postCopy->parent_id == null) {
        $postCopy->save();
        $newPostsIds[$posts[$i]->id] = $postCopy->id;
      }

      if ($postCopy->role == 1) {
        $newRootPostid = $postCopy->id;
      }
    }
    $posts_data_search = [
      'hotel_id' => $from_hotel,
    ];

    $postsTranslates = PostsTranslate::Finder($posts_data_search);
    for ($i = 0; $i < count($postsTranslates); $i++) {
      $postsTranslateCopy = $postsTranslates[$i]->replicate();
      $postsTranslateCopy->hotel_id = $to_hotel;
      if (array_key_exists($postsTranslateCopy->post_id, $newPostsIds)) {
        $postsTranslateCopy->post_id = $newPostsIds[$postsTranslateCopy->post_id];
        if (isset($newRootPostid) && $newRootPostid == $postsTranslateCopy->post_id) {
          $postsTranslateCopy->title = $hotel_name;
        }
        $postsTranslateCopy->save();
      }
    }
  }

  /**
   * Destroy
   * Remove a Post.
   * */
  public function destroy($id)
  {
    $reqExecTimeId = 29;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $post = Post::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($post->hotel_id, $this->privileges["hotel_id"])) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      //old code by Rafael
      // if ($post->shopping_orders()->get() !== null)
      //   $post->shopping_orders()->delete();
      // $post->translates()->delete();
      // $post->delete();
      $post->hidden = 1;
      $post->save();
      $res = ['delele post ' . $id => 'success'];
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

    if (array_key_exists("title", $request_data)) {
      $request_data['title'] = Utils::MultiSearchString($request_data["title"]);
    }

    if (array_key_exists("categories", $request_data)) {
      $request_data['categories'] = Utils::MultiSearchStringArray($request_data["categories"]);
    }

    if (array_key_exists("categorcontents_categoriesies", $request_data)) {
      $request_data['contents_categories'] = Utils::MultiSearchStringArray($request_data["contents_categories"]);
    }

    if (array_key_exists("summery", $request_data)) {
      $request_data['summery'] = Utils::MultiSearchStringArray($request_data["summery"]);
    }

    if (array_key_exists("description", $request_data)) {
      $request_data['description'] = Utils::MultiSearchStringArray($request_data["description"]);
    }

    if (array_key_exists("location", $request_data)) {
      $request_data['location'] = Utils::MultiSearchStringArray($request_data["location"]);
    }

  
    if (array_key_exists("addition_data_1_text", $request_data)) {
      $request_data['addition_data_1_text'] = Utils::MultiSearchStringArray($request_data["addition_data_1_text"]);
    }

    if (array_key_exists("rate", $request_data)) {
      $request_data['rate'] = Utils::SearchRang($request_data["rate"]);
    } else {
      $request_data['rate']['min'] = null;
      $request_data['rate']['max'] = null;
    }

    if (array_key_exists("price", $request_data)) {
      $request_data['price'] = Utils::SearchRang($request_data["price"]);
    } else {
      $request_data['price']['min'] = null;
      $request_data['price']['max'] = null;
    }
  
    if (array_key_exists("state", $request_data)) {
      $request_data['state'] = intval($request_data["state"]);
    }

    if (array_key_exists("ids", $request_data)) {
      if (!is_array($request_data["ids"])) {
        $request_data["ids"] = explode(',', $request_data["ids"]);
      }
      $request_data["ids"] = preg_replace("/[^0-9]/", '', $request_data["ids"]);
      $request_data["ids"] = array_filter($request_data["ids"]);
    }
    return parent::extractRequestParams($request_data); 
  }

  public function topFiveOrders(Request $request) {
        
    $orders = Post::orderBy('number_of_orders', 'DESC')
    ->where('hotel_id', $request->hotel_id)
    ->where('ordered_or_reserved', 1)
    ->where('number_of_orders', '>', 0)
    ->take(5)
    ->get();
    
    return PostTemplate::collection($orders);
  }

  public function topFiveReservations(Request $request) {
     
    $reservations = Post::orderBy('number_of_reservations', 'DESC')
    ->where('hotel_id', $request->hotel_id)
    ->where('ordered_or_reserved', 2)
    ->where('number_of_reservations', '>', 0)
    ->take(5)
    ->get();
    
    return PostTemplate::collection($reservations);
  }
  public function getTranslates($id) {
    $post = Post::find($id);
    $translates = $post->translates;
    return PostsTranslatesTemplate::collection($translates);
  }
}
