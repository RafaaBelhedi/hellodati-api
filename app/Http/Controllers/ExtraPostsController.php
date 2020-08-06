<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

use App\Http\Requests;
use App\Http\Utils;
use App\PostType;
use App\ExtraPost;
use App\ExtraPostsTranslate;
use App\Http\Resources\ExtraPostTemplate;
use Exception;

/**
 * @group ExtraPosts
 *
 * APIs for managing posts outside hotels
 */

class ExtraPostsController extends LoggedController
{
  /**
   * Index
   * Display a listing Extra posts.
   * To filter Extra posts, add any of the ExtraPost object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 31;

    ExtraPost::ExpiredPostChecker();

    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }

    if (isset($request_data['special_order']) && $request_data['special_order'] == 1) {
      if (isset($request_data['order_type']))
        switch ($request_data['order_type']) {
          case 1:   //round robin order
            $extra_posts = ExtraPost::where('parent_id', '!=', 28)->where('parent_id', '!=', null)->get();
            $res = ExtraPostTemplate::collection($extra_posts);
            $counters = [];
            $sub_array = [];
            $categories = ExtraPost::where('parent_id', 28)->get();

            foreach ($categories as $categorie) {
              $sub_array[] = $extra_posts->filter(function ($x) use (&$categorie) {
                return $x->parent_id == $categorie->id;
              })->all();
              $counters[] = sizeof($sub_array[sizeof($sub_array) - 1]);
            }

            $max = 0;

            for ($i = 0; $i < sizeof($sub_array); $i++) {
              $max = max($max, sizeof($sub_array[$i]));
              $aux = [];
              foreach ($sub_array[$i] as $elem)
                $aux[] = $elem;
              $sub_array[$i] = $aux;
            }
            $aux = [];

            for ($i = 0; $i < $max; $i++) {
              for ($j = 0; $j < count($sub_array); $j++) {
                if (isset($sub_array[$j][$i])) {
                  $aux[] = $sub_array[$j][$i];
                }
              }
            }
            $res = $aux;
            // var_dump($counters);
            // return;
            break;

          case 2:   //order by order_in_home
            $extra_posts = ExtraPost::where('parent_id', '!=', 28)->where('parent_id', '!=', null)->get();
            $extra_posts_with_order = ExtraPost::where('order_in_home', '!=', null)->get()->sortBy('order_in_home');
            $extra_posts_without_order = $extra_posts->filter(function ($x) {
              return $x->order_in_home === null;
            });
            $res = $extra_posts_with_order->merge($extra_posts_without_order);
            break;
        } else {
        $extra_posts = ExtraPost::where('parent_id', '!=', 28)->where('parent_id', '!=', null)->get()->shuffle();
        $res = $extra_posts;
      }
      return ExtraPostTemplate::collection(collect($res));
    }

    $request_data = $this->extractRequestParams($request_data);
    $extraPosts = ExtraPost::Finder($request_data);
    foreach ($extraPosts as $item) { //this methode is slowing the respnse 132ms -> 972ms
      $this->seen($item->id);
    }

    if (array_key_exists("compact_res", $request_data) && ($request_data["compact_res"] === "true" || $request_data["compact_res"] === true)) {
      $res = PostMinTemplate::collection($posts);
    } else {
      $res = ExtraPostTemplate::collection($extraPosts);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of ExtraPost.
   * These fields can also be used to filter the search.
   * 
   * * <b>Please note: </b> the property 'post_type' refers to the template
   * used for the ExtraPost, which can override a ExtraPost.property's default
   * value or force it to be required.
   * */
  public function create()
  {
    $reqExecTimeId = 30;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = ExtraPost::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /*
 * Store
 * Create a new ExtraPost.
 * * <b>Please note: </b> 
 * - When creating new ExtraPost you can pass ExtraPostTranslate properties, this will create an english translation for the ExtraPost.
 * - Every ExtraPost has at least one translation so providing an english title is required.
 * - Required fields are defined by the chosen PostType, including the once that belong to the ExtraPostTranslate.
 * 
 * */
  public function store(Request $request)
  {
    $reqExecTimeId = 33;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $extraPost = new ExtraPost;
    $extraPost->setSelf($request->all());
    try {
      $postType = PostType::findOrFail($extraPost->type);
      $validations = $extraPost->isValideByType($postType);
    } catch (ModelNotFoundException $e) {
      $postType = null;
      $validations = false;
    }
    if ($validations === true) {
      $extraPost->save();

      $extraPostsTranslate = new ExtraPostsTranslate;
      $translatesData = $request->only(['title', 'summery', 'description', 'addition_data_1_text']);
      $translatesData["post_id"] = $extraPost->id;
      $translatesData["lang_iso"] = 'en';

      $extraPostsTranslate->setSelf($translatesData);
      $validations = $extraPostsTranslate->isValideByType($postType);
      if ($validations === true) {
        $extraPostsTranslate->save();
        $res = ExtraPostTemplate::collection(collect([$extraPost]));
      } else {
        $extraPost->delete();
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
    } else if (is_array($validations)) {
      $res = response()->json([
        'Invalid_or_missing_fields' => $validations
      ], 500);
    } else {
      $res = response()->json([
        'message' => 'invalide entrie(s)'
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Show
   * Display the specified ExtraPost by {id}.
   * */
  public function show($id)
  {
    $reqExecTimeId = 32;
    try {
      $extraPost = ExtraPost::findOrFail($id);
      $res = ExtraPostTemplate::collection(collect([$extraPost]));
      return $res;
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
   * Edit properties of existing ExtraPost.
   * */
  public function update(Request $request, $id)
  {
    $reqExecTimeId = 34;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $extraPost = ExtraPost::findOrFail($id);
      $extraPost->setSelf($request->all());
      try {
        $postType = PostType::findOrFail($extraPost->type);
        $validations = $extraPost->isValideByType($postType);
      } catch (ModelNotFoundException $e) {
        $postType = null;
        $validations = false;
      }
      if ($validations === true) {
        $extraPost->save();
        $request_data = [
          "post_id" => $extraPost->id,
          "lang_iso" => "en",
        ];
        $translatesData = $request->only(['title', 'summery', 'description', 'addition_data_1_text']);
        $extraPostsTranslate = ExtraPostsTranslate::Finder($request_data);
        if (count($extraPostsTranslate) > 0) {
          $extraPostsTranslate = $extraPostsTranslate[0];
        } else {
          $translatesData["post_id"] = $extraPost->id;
          $translatesData["lang_iso"] = 'en';
          $extraPostsTranslate = new ExtraPostsTranslate;
        }
        $extraPostsTranslate->setSelf($translatesData);
        $validations = $extraPostsTranslate->isValideByType($postType);
        if ($validations === true) {
          $extraPostsTranslate->save();
          $res = ExtraPostTemplate::collection(collect([$extraPost]));
        } else if (is_array($validations)) {
          $res = response()->json([
            'Invalid_or_missing_fields' => $validations
          ], 500);
        } else {
          $res = response()->json([
            'message' => 'invalide entrie(s)'
          ], 500);
        }
      } else if (is_array($validations)) {
        $res = response()->json([
          'Invalid_or_missing_fields' => $validations
        ], 500);
      } else {
        $res = response()->json([
          'message' => 'invalide entrie(s)'
        ], 500);
      }
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'message' => 'invalide entrie(s)'
      ], 500);
    }

    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Multi-Update
   * Edit properties of multiple existing ExtraPost.
   * */
  public function MultiUpdate(Request $request)
  {
    $reqExecTimeId = 35;
    if ($this->privileges["role"] != 3) {
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
        $extraPost = ExtraPost::findOrFail($updating_post['id']);
        if ($this->privileges["role"] == 2 && !in_array($extraPost->hotel_id, $this->privileges["hotel_id"])) {
          return response()->json([
            'message' => 'Forbidden'
          ], 403);
        }
        $extraPost->setSelf($updating_post);
        $validation = $extraPost->isValide();
        if ($validation === true) {
          $extraPost->save();
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
   * Rate
   * submit a rate for a resource in storage.
   */
  public function rate(Request $request, $id)
  {
    $reqExecTimeId = 37;
    if ($this->privileges["role"] != 0 && $this->privileges["role"] != 1) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $extraPost = ExtraPost::findOrFail($id);
      $extraPost->setRate(5);
      if ($extraPost->isValide()) {
        $extraPost->save();
      }
      $res = ExtraPostTemplate::collection(collect([$extraPost]));
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
   * Increment the property 'nbr_views' of an ExtraPost by 1.
   */
  public function seen($id)
  {
    $reqExecTimeId = 38;
    if ($this->privileges["role"] != 0 && $this->privileges["role"] != 1) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $extraPost = ExtraPost::findOrFail($id);
      $extraPost->incrimentViews();
      if ($extraPost->isValide()) {
        $extraPost->save();
      }
      $res = ExtraPostTemplate::collection(collect([$extraPost]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Click
   * Increment the property 'nbr_clicks' of an ExtraPost by 1.
   */
  public function click($id)
  {
    $reqExecTimeId = 39;
    if ($this->privileges["role"] != 0 && $this->privileges["role"] != 1) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $extraPost = ExtraPost::findOrFail($id);
      $extraPost->incrimentClicks();
      if ($extraPost->isValide()) {
        $extraPost->save();
      }
      $res = ExtraPostTemplate::collection(collect([$extraPost]));
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
   * Remove an ExtraPost.
   * */
  public function destroy($id)
  {
    $reqExecTimeId = 36;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $extraPost = ExtraPost::findOrFail($id);
      $data = ['state' => 0];
      $extraPost->setSelf($data);
      $extraPost->save();
      $res = ['delele extraPost ' . $id => 'success'];
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


    if (array_key_exists("ids", $request_data)) {
      if (!is_array($request_data["ids"])) {
        $request_data["ids"] = explode(',', $request_data["ids"]);
      }
      $request_data["ids"] = preg_replace("/[^0-9]/", '', $request_data["ids"]);
      $request_data["ids"] = array_filter($request_data["ids"]);
    }
    return parent::extractRequestParams($request_data);
  }
}
