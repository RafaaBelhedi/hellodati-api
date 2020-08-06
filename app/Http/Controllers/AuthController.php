<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\Device;
use App\ReqExecTime;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Route;
use Mail;

/**
 * @group Authentification
 *
 * APIs for managing authentification
 */
class AuthController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
  protected $startExecitionTime;
  public function __construct()
  {
    $this->startExecitionTime = microtime(true);
  }

  public function workEnd($reqExecTime_id)
  {
    try {
      $reqExecTime = ReqExecTime::findOrFail($reqExecTime_id);
      $execution_time = round((microtime(true) - $this->startExecitionTime), 2);
      if ($execution_time > $reqExecTime->execution_time) {

        $reqExecTime->execution_time = $execution_time;
        $reqExecTime->save();
      }
    } catch (ModelNotFoundException $e) { }
  }
  /**
   * Login
   * login user and create access token
   * @bodyParam  email string required The e-mail of the user.
   * @bodyParam  password string required The password of the user.
   * @bodyParam  client_id integer required The id of used platform.
   * @bodyParam  client_secret string required The secret pass code of used platform.
   * @bodyParam  device_imei string optional Only if the user is not an admin.
   * @response {
   *     "token_type": "Bearer",
   *     "expires_in": "Integer",
   *     "access_token": "String",
   *     "refresh_token": "String"
   * }
   */
  public function login(Request $request)
  {

    $reqExecTimeId = 10;
    if (request("email") == null || request("password") == null || request("client_id") == null || request("client_secret") == null) {
      return response()->json([
        'message' => 'Missing fields'
      ], 401);
    }
    $request->validate([
      'email' => 'string|email',
      'password' => 'string',
      'remember_me' => 'boolean',
      'client_id' => 'integer',
      'client_secret' => 'string',
      'device_imei' => 'string'
    ]);


    $credentials = request(['email', 'password']);

    if (!Auth::attempt($credentials))
      return response()->json([
        'message' => 'Email or password incorrect'
      ]);

    $user = $request->user();

    if ($user->approved != 1) {
      return response()->json([
        'message' => 'User not yet approved'
      ], 401);
    };

    if ($user->role == 0 || $user->role == 1) {
      if (request('client_id') != 3 || request("device_imei") == null) {
        return response()->json([
          'message' => 'No device IMEI'
        ], 401);
      }
      $request_data['imei'] = request('device_imei');

      $devices = Device::Finder($request_data);
      if (count($devices) != 1) {
        return response()->json([
          'message' => 'Invalid IMEI'
        ], 401);
      }
    }

    $params = [
      'grant_type' => 'password',
      'client_id' => request('client_id'),
      'client_secret' => request('client_secret'),
      'username' => request('email'),
      'password' => request('password'),
      'scope' => '*'
    ];
    $request->request->add($params);
    $proxy = Request::create('oauth/token', 'POST');
    $res = Route::dispatch($proxy);
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Refresh
   * Extend the duration of authentification sessions
   * @bodyParam  refresh_token string required The refresh token provided when logged-in.
   * @bodyParam  client_id integer required The id of used platform.
   * @bodyParam  client_secret string required The secret pass code of used platform.
   * @response {
   *     "token_type": "Bearer",
   *     "expires_in": "Integer",
   *     "access_token": "String",
   *     "refresh_token": "String"
   * }
   */
  public function refresh(Request $request)
  {
    $reqExecTimeId = 11;
    if (request("refresh_token") == null || request("client_id") == null || request("client_secret") == null) {
      return response()->json([
        'message' => 'Unauthorized'
      ], 401);
    }
    $this->validate($request, [
      'refresh_token' => 'string',
      'client_id' => 'integer',
      'client_secret' => 'string'
    ]);

    $params = [
      'grant_type' => 'refresh_token',
      'refresh_token' => request('refresh_token'),
      'client_id' => request('client_id'),
      'client_secret' => request('client_secret'),
      'scope' => '*',
    ];

    $request->request->add($params);
    $proxy = Request::create('oauth/token', 'POST');
    $res = Route::dispatch($proxy);
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Logout
   * Revoke the access token
   * @response {"message":"Successfully logged out"}
   */
  public function logout(Request $request)
  {
    $reqExecTimeId = 13;
    $request->user()->token()->revoke();

    return response()->json([
      'message' => 'Successfully logged out'
    ]);
  }

  /**
   * Account
   * Get the authenticated User
   *
   * @response {
   *  "id":"integer",
   *  "name":"string",
   *  "email":"string",
   *  "approved":"integer",
   *  "role":"integer",
   *  "created_at":"Timestamp",
   *  "updated_at":"Timestamp"
   * }
   */
  public function user(Request $request)
  {
    $reqExecTimeId = 14;

    $res = response()->json($request->user());
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Privileges
   * Get the privileges of the authenticated User
   *
   * @response {
   *    "user_id": "integer",
   *    "role": "integer",
   *    "client_id": "integer",
   *    "hotel_id": "array",
   *    "device_imei": "string"
   *}
   */
  public function privileges()
  {
    $reqExecTimeId = 15;
    $res = response()->json(\Auth::user()->getPrivileges());
    $this->workEnd($reqExecTimeId);
    return $res;
  }
}
