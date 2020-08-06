<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\Notification;
use App\NotificationView;
use App\Http\Resources\NotificationTemplate;
use App\Http\Resources\NotificationViewTemplate;

/**
 * @group Notifications
 *
 * APIs for managing templates of notification
 */
class NotificationController extends LoggedController
{
  /**
   * Index
   * Display a listing of notification templates.
   * To filter notification templates, add any of the Notification object properties to the querry ?{property}={value}
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

    $Notifications = Notification::Finder($request_data);

    $res = NotificationTemplate::collection($Notifications);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of Notification.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    $reqExecTimeId = 46;
    // // if ($this->privileges["role"] != 3) {
    // //   return response()->json([
    // //     'message' => 'Forbidden'
    // //   ], 403);
    // }
    $res = Notification::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new Notification.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 49;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2 && $this->privileges["role"] != 1) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }

    $Notification = new Notification;
    $Notification->setSelf($new_data);
    if ($Notification->isValide()) {
      $Notification->save();
      $res = new NotificationTemplate($Notification);
    } else {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  public function view(Request $request, $id)
  {
    $reqExecTimeId = 48;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2 && $this->privileges["role"] != 1) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $Notification = Notification::findOrFail($id);
      $view = new NotificationView;
      $view->setSelf($request->all());
      $view->hotel_id = $Notification->hotel_id;
      $view->notification_id = $Notification->id;
      $res = NotificationViewTemplate::collection(collect([$view]));
      if (!$view->isValide())
        $res = response()->json([
          'error' => "Invalid_or_missing_fields"
        ], 500);
      else {
        $test = NotificationView::where(['device_id' => $view->device_id, 'notification_id' => $view->notification_id])->first();
        if ($test)
          $res = response()->json([
            'error' => "Invalid_or_missing_fields"
          ], 500);
        else
          $view->save();
      }
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Show
   * Display the specified Notification by {id}.
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
      $Notification = Notification::findOrFail($id);
      $res = NotificationTemplate::collection(collect([$Notification]));
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
   * Edit properties of existing Notification.
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
      $Notification = Notification::findOrFail($id);
      $Notification->setSelf($new_data);
      if ($Notification->isValide()) {
        $Notification->save();
      }
      $res = new NotificationTemplate($Notification);
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
   * Remove a Notification.
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
      $Notification = Notification::findOrFail($id);
      $Notification->delete();
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
