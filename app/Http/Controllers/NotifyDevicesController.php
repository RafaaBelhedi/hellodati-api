<?php

namespace App\Http\Controllers;

use App\Http\Resources\PushNotificationsTemplate;
use App\Post;
use App\PushNotification;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * @group Notifications
 *
 * APIs for managing notifications
 */

class NotifyDevicesController extends LoggedController
{

  /**
   * Columns
   * Display the possible fields of a Notification.
   * */
  public function create()
  {
    $reqExecTimeId = 100;

    $res = response()->json([
      "Notification" => [
        'title' => "string|required|max:50",
        'summery' => "string|required:max:200",
        'image_right_url' => "string|url",
        'video' => "string|url",
        'image' => "string|url",
        'pub' => "boolean",
        'duree' => "string",
        'expandeble_image_url' => "string|url",
        'expandeble_text' => "string|max:1000",
        'sound' => 'in:prism,ripple,water',
        'inform_no_context' => [
          'image' => "string|url",
          'title' => "string|max:50|required_with:inform_no_context",
          'description' => "string|max:1000|required_with:inform_no_context"
        ],
        'post_reference' => [
          'fragment' => 'in:hotel,extra|required_with:post_reference',
          'post_id' => "integer|required_with:post_reference|min:0"
        ],
        'update_request' => "boolean",
        'clear_data' => "boolean"
      ]
    ], 200);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Send a notification
   * Create a notification and send it to target Device(s).
   * Filtering target devices is exactly the same way as searching for devices using filters.
   * To filter target devices, add any of the Device object properties to the querry ?{property}={value}
   * */
  public function notifyHotelsDevices(Request $request)
  {
    $reqExecTimeId = 101;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $request->validate([
      'data' => "required"
    ]);
    $data = json_decode($request->input("data"), true);

    Validator::make($data, [
      'title' => "string|required|max:50",
      'summery' => "string|required:max:200",
      'post_id' => "nullable",
      'image_right_url' => "nullable|url",
      'video' => "nullable|url",
      'image' => "nullable|url",
      'pub' => "nullable",
      'duree' => "nullable",
      'expandeble_image_url' => "nullable|url",
      'expandeble_text' => "nullable|string|max:1000",
      'sound' => 'in:prism,ripple,water',
      'promo' => 'nullable',
      'inform_no_context.image' => "nullable|url",
      'inform_no_context.title' => "string|max:50|required_with:inform_no_context",
      'inform_no_context.description' => "string|max:1000|required_with:inform_no_context",
      'post_reference.fragment' => 'in:hotel,extra|required_with:post_reference',
      'post_reference.post_id' => "integer|required_with:post_reference|min:0"
    ])->validate();

    if (array_key_exists("post_id", $data)) {
      $post = Post::find($data['post_id']);

      if ($post) {
        while ($post->parent_id != null) {
          $post_aux = $post;
          $post = Post::find($post->parent_id);
        }
      }
    }
    $deviceController = new DeviceController();
    $devicesRelated = $deviceController->index($request)->jsonSerialize();
    // dd($devicesRelated);
    $devices_fcm_tokens = [];
    for ($i = 0; $i < count($devicesRelated); $i++) {
      array_push($devices_fcm_tokens, $devicesRelated[$i]['fcm_token']);
      $p = new PushNotification(); //Save notification data in the database to fetch later by device
      // dd($data);
      if (array_key_exists("post_id", $data)) {
        if (isset($post_aux->layout_xml_template))
          $data['layout_xml_template'] = $post_aux->layout_xml_template;
        $data['parent_title'] = $post_aux->getTranslations(request()->input('d_lang'))['title'];
      }
      $p->setSelf($data);
      $p->imei = $devicesRelated[$i]['imei'];
      
      $p->save();
    }
    $devices_fcm_tokens = array_filter($devices_fcm_tokens);



    
    $notification  = array(
      'registration_ids' => array_values($devices_fcm_tokens),
      'priority' => 'high',
      'data' => [
        'vibreur_numbre' => 2,
        'vibreur_delai' => 200,
        'vibreur_time' => 500,
        'flash_color' => 'green',
        'title' => array_key_exists('title', $data) ? $data['title'] : "",
        'summery' => array_key_exists('summery', $data) ? $data['summery'] : "",
        'image_right_url' => array_key_exists('image_right_url', $data) ? $data['image_right_url'] : "",
        'video' => array_key_exists('video', $data) ? $data['video'] : "",
        'image' => array_key_exists('image', $data) ? $data['image'] : "",
        'pub' => array_key_exists('pub', $data) ? $data['pub'] : 0,
        'duree' => array_key_exists('duree', $data) ? $data['duree'] : "",
        'expandeble_image_url' => array_key_exists('expandeble_image_url', $data) ? $data['expandeble_image_url'] : "",
        'expandeble_text' => array_key_exists('expandeble_text', $data) ? $data['expandeble_text'] : "",
        'sound' => array_key_exists('sound', $data) ? $data['sound'] : 'prism',
        'inform_no_context' => array_key_exists('inform_no_context', $data) ? $data['inform_no_context'] : null,
        'post_reference' => array_key_exists('post_reference', $data) ? $data['post_reference'] : null,
        'update_request' => array_key_exists('update_request', $data) ? $data['update_request'] : false,
        'clear_data' => array_key_exists('clear_data', $data) ? $data['clear_data'] : false,
        'post_id' => array_key_exists('post_id', $data) ? $data['post_id'] : -1,
        'layout_xml_template' => array_key_exists('post_id', $data) ? $post_aux->layout_xml_template : -1,
      ]
    );

    $res = NotifyDevicesController::push($notification);
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  public function pushNotifications()
  {
    Log::debug(request()->all());
    if ($this->privileges['role'] == 0) {
      return PushNotificationsTemplate::collection(PushNotification::Finder(['imei' => $this->privileges['device_imei'],'hidden'=>0]));
    }
    if ($this->privileges['role'] == 3)
      return PushNotificationsTemplate::collection(PushNotification::all());
  }

  public function pushNotification($id)
  {
    $pushNotification = pushNotification::find($id)->get();
    
    return json_encode($pushNotification);

    // return PushNotificationsTemplate::collection(collect([$pushNotification]));
  }

  public function pushNotificationsSeen($id)
  {
    if ($this->privileges['role'] != 3 && $this->privileges['role'] != 0)
      return response()->json([
        "error" => "Forbidden"
      ], 403);
    try {
      $notif = PushNotification::findOrFail($id);
      $notif->seen = 1;
      $notif->save();
      return PushNotificationsTemplate::collection(collect([$notif]));
    } catch (Exception $e) {
      return response()->json([
        "error" => "No notification with that id"
      ], 500);
    }
  }

  public static function push($notification)
  {
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    $headers = [
      'Authorization: key=' . config('app.firebase_key'),
      'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fcmUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return response($result, $httpcode);
  }
}
