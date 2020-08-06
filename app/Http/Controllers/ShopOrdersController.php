<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Utils;
use App\ShoppingOrder;
use App\Device;
use App\Tourist;
use App\Post;
use App\Http\Resources\ShoppingOrderTemplate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * @group ShoppingOrders
 *
 * APIs for managing shopping orders
 */

class ShopOrdersController extends LoggedController
{
  /**
   * Index
   * Display a listing of shopping orders.
   * To filter shopping orders, add any of the ShoppingOrder object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 83;
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }

    $request_data = $this->extractRequestParams($request_data);
    if ($this->privileges["role"] == 0) {
      $request_data['tourist_id'] = Device::findByImei($this->privileges["device_imei"])->device_room->stay->tourist->id;
    }
    $shoppingOrders = ShoppingOrder::Finder($request_data);

    $res = ShoppingOrderTemplate::collection($shoppingOrders);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of ShoppingOrder.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    $reqExecTimeId = 82;
    if ($this->privileges["role"] != 3  && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = ShoppingOrder::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new ShoppingOrder.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 85;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 0) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }

    $new_data['status'] = 0;

    $shoppingOrder = new ShoppingOrder;
    $shoppingOrder->setSelf($new_data);
    if ($shoppingOrder->isValide()) {
      $shoppingOrder->save();
      $post = Post::findOrFail($shoppingOrder->post_id);
      if ($shoppingOrder->reservation === 1)
        $post->number_of_reservations++; 
      else 
        $post->number_of_orders += $shoppingOrder->qt; 
      $post->save();
      $res = ShoppingOrderTemplate::collection(collect([$shoppingOrder]));
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
    $reqExecTimeId = 84;

    try {

      $shoppingOrder = ShoppingOrder::findOrFail($id);
      if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2 && ($this->privileges["role"] == 0 && $this->privileges["tourist_id"] != $shoppingOrder->tourist_id)) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $res = ShoppingOrderTemplate::collection(collect([$shoppingOrder]));
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
    $reqExecTimeId = 86;
    if ($this->privileges["role"] == 1) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }


    try {


      /**
       * Shopping order status update must be controlled:
       * If status is 0 it can only be changed to 1
       * If status is 1 it can only be changed to 2 or 3
       * If status is 2 it can only be be changedd to 4
       * 0: Pending, 1: Sent, 2: Accepted, 3: Refused, 4:Delivered
       */

      $device = Device::findByImei($this->privileges["device_imei"]);
      $shoppingOrder = ShoppingOrder::findOrFail($id);
      if ($this->privileges["role"] == 2 && !in_array($shoppingOrder->hotel_id, $this->privileges["hotel_id"]))
        return response()->json([
          'message' => 'Forbidden'
        ], 403);

      if ($this->privileges["role"] == 0 && $shoppingOrder->tourist_id != $device->device_room->stay->tourist->id)
        return response()->json([
          'message' => 'Forbidden'
        ], 403);

      if ($this->privileges["role"] == 0) {
        if ($shoppingOrder->status == 1 && isset($new_data['status']) && $new_data['status'] != null) {
          return response()->json([
            'error' => "Cannot update status"
          ], 500);
        }

        if (isset($new_data['status'])) {
          if ($new_data['status'] != 1)
            return response()->json([
              'error' => "Cannot update status"
            ], 500);
          else
                if (isset($new_data['password']) && Hash::check($new_data['password'] == $shoppingOrder->tourist->password)) {
            $shoppingOrder->setSelf(array_filter($new_data));
            if ($shoppingOrder->isValide()) {
              $shoppingOrder->save();
              return ShoppingOrderTemplate::collection(collect([$shoppingOrder]));
            }
          } else
            return response()->json([
              'error' => "Wrong password"
            ], 403);
        }
      }

      if ($this->privileges["role"] == 2) {
        if ($shoppingOrder->status == 1 && isset($new_data['status']) && $new_data['status'] == 2) {
          $shoppingOrder->setSelf(['status' => $new_data['status']]);
          $shoppingOrder->save();
          return ShoppingOrderTemplate::collection(collect([$shoppingOrder]));
        }
        if ($shoppingOrder->status == 1 && isset($new_data['status']) && $new_data['status'] == 3) {
          $shoppingOrder->setSelf(['status' => $new_data['status']]);
          $shoppingOrder->save();
          return ShoppingOrderTemplate::collection(collect([$shoppingOrder]));
        }
        if ($shoppingOrder->status == 2 && isset($new_data['status']) && $new_data['status'] == 4) {
          $shoppingOrder->setSelf(['status' => $new_data['status']]);
          $shoppingOrder->save();
          return ShoppingOrderTemplate::collection(collect([$shoppingOrder]));
        }
        return response()->json([
          'error' => "Cannot update shopping order"
        ], 500);
      }
      $shoppingOrder->setSelf($new_data);
      if ($shoppingOrder->isValide()) {
        $shoppingOrder->save();
      }
      $res = ShoppingOrderTemplate::collection(collect([$shoppingOrder]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   *  Confirm all shopping orders from one tourist 
   */

  public function confirmAll(Request $request)
  {
    Log::debug($request->all());
    $data = json_decode($request->getContent(), true);
    if ($data == null) {
      $data = $request->all();
    }
    if (array_key_exists('tourist_id', $data) && $data["tourist_id"] !== null) {
      $tourist_id =  $data["tourist_id"];
    }
    if (array_key_exists('reservation', $data) && $data["reservation"] !== null) {
      $isReservation =  $data["reservation"];
    }
    if (array_key_exists('password', $data) && $data["password"] !== null) {
      $password =  $data["password"];
    }
    if ($tourist_id > 0) {
      try {
        $tourist = Tourist::findOrFail(intval($tourist_id));
        if (!Hash::check($password, $tourist->password)) {
          return response()->json([
            'error' => "wrong password"
          ], 500);
        }
        $tourist_id = $tourist->id;
      } catch (ModelNotFoundException $e) {
        return response()->json([
          'error' => "Unvalid tourist id"
        ], 500);
      }
    } else {
      return response()->json([
        'error' => "Unvalid tourist id"
      ], 500);
    }
    $isReservation = intval($isReservation);
    if ($isReservation != 1 && $isReservation != 0) {
      return response()->json([
        'error' => "reservation must be one of the following [0,1]"
      ], 500);
    }
    ShoppingOrder::confirmAll($tourist_id, $isReservation);
    return response()->json([
      'message' => "successfully confirmed all items of tourist " . $tourist_id . "!"
    ], 200);
  }

  /**
   * Destroy
   * Remove a ShoppingOrder.
   * */
  public function destroy($id)
  {
    $reqExecTimeId = 87;
    try {
      if ($this->privileges["role"] != 3 && $this->privileges["role"] != 0) {
        return response()->json([
          'message' => 'Forbidden'
        ], 403);
      }
      $shoppingOrder = ShoppingOrder::findOrFail($id);
      if ($this->privileges["role"] == 0 && $shoppingOrder->status != 0 && $this->privileges["tourist_id"] != $shoppingOrder->tourist_id)
        return response()->json([
          'message' => 'Forbidden'
        ], 403);

      if ($shoppingOrder->status != 0) {
        return response()->json([
          'message' => 'only draft shopping items can be deleted'
        ], 403);
      }
      $shoppingOrder->delete();

      $res = ['delele shopping Order ' . $id => 'success'];
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Accept shopping order with delay for delivery
   */
  public function accept($id)
  {
    try {
      $order = ShoppingOrder::findOrFail($id);
      if ($this->privileges["role"] != 2 && $this->privileges["role"] != 3) {
        return response()->json([
          'error' => 'forbidden'
        ], 403);
      }
      if ($order->status != 1) {
        return response()->json([
          'error' => 'Wrong order status'
        ], 500);
      }
      $order->status = 2;
      $order->delay = request()->input('delay');
      // dd($order->device()->imei);
      $order->save();

      return $this->pushNotifOrder([
        'title' => 'Order accepted',
        'summery' => 'Your order for ' . $order->post->translates[0]->title  . ' has been accepted and should be delivered in approximately ' . $order->delay . ' minutes.',
        'imei' => $order->device()->imei
      ]);
      // dd($order->device()->imei);
      return ShoppingOrderTemplate::collection(collect([$order]));
    } catch (ModelNotFoundException $e) {
      return response()->json([
        'error' => 'No shopping order with that ID'
      ], 500);
    }
  }

  public function acceptFromDashboard($id)
  {
    try {
      $order = ShoppingOrder::findOrFail($id);
      if ($this->privileges["role"] != 2 && $this->privileges["role"] != 3) {
        return response()->json([
          'error' => 'forbidden'
        ], 403);
      }
      if ($order->status != 1) {
        return response()->json([
          'error' => 'Wrong order status'
        ], 500);
      }
      $order->status = 5;
      $order->save();

      return $this->pushNotifOrder([
        'title' => 'Order accepted',
        'summery' => 'Your order for ' . $order->post->translates[0]->title  . ' has been accepted and should be delivered in approximately ' . $order->delay . ' minutes.',
        'imei' => $order->device()->imei
      ]);
      return ShoppingOrderTemplate::collection(collect([$order]));
    } catch (ModelNotFoundException $e) {
      return response()->json([
        'error' => 'No shopping order with that ID'
      ], 500);
    }
  }

  /**
   * Deny shopping order with reason
   */
  public function deny(Request $request, $id)
  {
    try {
      $order = ShoppingOrder::findOrFail($id);
      if ($this->privileges["role"] != 2 && $this->privileges["role"] != 3) {
        return response()->json([
          'error' => 'forbidden'
        ], 403);
      }
      if ($order->status !== 1 && $order->status !== 2) {
        return response()->json([
          'error' => 'Wrong order/reservation status'
        ], 500);
      }
      $order->status = 3;
      $order->reason = $request->reason;
      // $order->delay = request()->input('delay');

      $order->save();
      $this->pushNotifOrder([
        'title' => 'Order denied',
        'summery' => 'Your order for ' . $order->post->translates[0]->title . ' has been denied for the following reason: ' . request()->input('reason'),
        'imei' => $order->device()->imei
      ]);

      return $order;
      // return ShoppingOrderTemplate::collection($order);
    } catch (ModelNotFoundException $e) {
      return response()->json([
        'error' => 'No shopping order with that ID'
      ], 500);
    }
  }

  /**
   * put the order as ready
   */
  public function ready($id)
  {
    try {
      $order = ShoppingOrder::findOrFail($id);
      if ($this->privileges["role"] != 2 && $this->privileges["role"] != 3) {
        return response()->json([
          'error' => 'forbidden'
        ], 403);
      }
      if ($order->status != 2) {
        return response()->json([
          'error' => 'Wrong order status'
        ], 500);
      }
      $order->status = 4;
      // $order->delay = request()->input('delay');
      $order->save();
      $this->pushNotifOrder([
        'title' => 'Order is ready',
        'summery' => 'Your order for ' . $order->post->translates[0]->title . ' is ready and should to be delivered.',
        'imei' => $order->device()->imei
      ]);

      return $order;
      return ShoppingOrderTemplate::collection($order);
    } catch (ModelNotFoundException $e) {
      return response()->json([
        'error' => 'No shopping order with that ID'
      ], 500);
    }
  }

  /**
   * put the order as delivered
   */
  public function delivered($id)
  {
    try {
      $order = ShoppingOrder::findOrFail($id);
      if ($this->privileges["role"] != 2 && $this->privileges["role"] != 3) {
        return response()->json([
          'error' => 'forbidden'
        ], 403);
      }
      if ($order->status != 4) {
        return response()->json([
          'error' => 'Wrong order status'
        ], 500);
      }
      $order->status = 5;
      $order->save();
      $this->pushNotifOrder([
        'title' => 'Order is delivered',
        'summery' => 'Your order for ' . $order->post->translates[0]->title . ' has been delivered.',
        'imei' => $order->device()->imei
      ]);

      return $order;
      return ShoppingOrderTemplate::collection($order);
    } catch (ModelNotFoundException $e) {
      return response()->json([
        'error' => 'No shopping order with that ID'
      ], 500);
    }
  }

  /**
   * put the order as confirmed
   */
  public function confirmed($id)
  {
    try {
      $order = ShoppingOrder::findOrFail($id);
      if ($this->privileges["role"] != 2 && $this->privileges["role"] != 3) {
        return response()->json([
          'error' => 'forbidden'
        ], 403);
      }
      if ($order->status != 2) {
        return response()->json([
          'error' => 'Wrong order status'
        ], 500);
      }
      $order->status = 5;
      $order->save();
      $this->pushNotifOrder([
        'title' => 'Order is confirmed',
        'summery' => 'Your order for ' . $order->post->translates[0]->title . ' is confirmed.',
        'imei' => $order->device()->imei
      ]);

      return $order;
      return ShoppingOrderTemplate::collection($order);
    } catch (ModelNotFoundException $e) {
      return response()->json([
        'error' => 'No shopping order with that ID'
      ], 500);
    }
  }
  public function pushNotifOrder($request)
  {
    $notif = array(
      'title' => $request['title'],
      'summery' => $request['summery'],
    );
    // Log::debug($request);
    if(!$request['imei'])
      return response()->json(['error'=>'No IMEI passed to notification controller']);
    $notifyDevicesController = new NotifyDevicesController();
    return $notifyDevicesController->notifyHotelsDevices(new Request(['data' => json_encode($notif), 'imei' => $request['imei']]));
  }

  public function extractRequestParams($request_data)
  {
    if (array_key_exists("status", $request_data)) {
      if (gettype($request_data["status"]) == "array") {
        $request_data['status'] = preg_replace('/\D/', '', $request_data["status"]);
      } else if (gettype($request_data["status"]) == "object") {
        $request_data['status'] = preg_replace('/\D/', '', json_decode($request_data["status"], true));
      } else  if (gettype($request_data["status"]) == "string") {
        $request_data['status'] = preg_replace('/[^0-9,]/', '', $request_data["status"]);
        $request_data['status'] = explode(',', $request_data['status']);
      } else if (gettype($request_data["status"]) == "integer" || gettype($request_data["status"]) == "double") {
        $request_data['status'] = [intval($request_data['status'])];
      } else {
        $request_data['status'] = null;
      }
    } else {
      $request_data['status'] = null;
    }
    return parent::extractRequestParams($request_data);
  }

  /*
  *
  * Return the list of shopping orders without any criteria or according to many criteria like [status or reservation]
  */
  public function getShoppingOrders(Request $request)
  {
    if ($this->privileges["role"] != 3  && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $shoppingOrder = new ShoppingOrder;

    if (isset($request->hotel_id) && $request->hotel_id !== null)
      $shoppingOrder = $shoppingOrder->where('hotel_id', $request->hotel_id);

    if (isset($request->first_status) && $request->first_status !== null) {
      if (isset($request->second_status) && $request->second_status !== null) {
        $shoppingOrder = $shoppingOrder->where(function ($query) use($request){
          $query->where('status', $request->first_status)->orWhere('status', $request->second_status);
        });
      } else
        $shoppingOrder = $shoppingOrder->where('status', $request->first_status);
    }
    if (isset($request->reservation) && $request->reservation !== null)
      $shoppingOrder = $shoppingOrder->where('reservation', $request->reservation);
    $shoppingOrder = $shoppingOrder->get();
    $res = ShoppingOrderTemplate::collection($shoppingOrder);
    return $res;
  }

  public function seen(Request $request) {
    ShoppingOrder::where('hotel_id', $request->hotel_id)->update(['seen' => 1]);
    return 'Done';
  }
}
