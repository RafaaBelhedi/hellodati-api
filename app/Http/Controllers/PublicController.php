<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Mail;
use App\ReqExecTime;
use App\App;
use App\Http\Resources\AppTemplate;
use App\Http\Resources\AppVersionTemplate;

/**
 * @group Public
 *
 * APIs for managing requests that don't require authentification
 */

class PublicController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ResetsPasswords;

  protected $startExecitionTime;
  public function __construct()
  {
    $this->startExecitionTime = microtime(true);
  }


  protected function workEnd($reqExecTime_id)
  {
    try {
      $reqExecTime = ReqExecTime::findOrFail($reqExecTime_id);
      $execution_time = round((microtime(true) - $this->startExecitionTime), 2);
      if ($execution_time > $reqExecTime->execution_time) {

        $reqExecTime->execution_time = $execution_time;
        $reqExecTime->save();
      }
    } catch (ModelNotFoundException $e) {
      return;
    }
  }

  /**
   * Dati versions
   * Display all the verions of Dati App.
   * */
  public function datiAppVersions(Request $request)
  {
    $reqExecTimeId = 7;
    $app = App::findOrFail(3);
    $res = AppTemplate::collection(collect([$app]));
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Dati last version
   * Display only the last 'Live' verion of Dati App.
   * */

  public function datiLastVersions(Request $request)
  {

    $reqExecTimeId = 8;
    $app = App::findOrFail(3);
    $lastVersion = null;
    $versions = $app->app_versions;
    foreach ($versions as $version) {
      if ($lastVersion == null) {
        $lastVersion = $version;
      } else if ($version->is_live && $version->version_code > $lastVersion->version_code) {
        $lastVersion = $version;
      }
    }
    $res = AppVersionTemplate::collection(collect([$lastVersion]));
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  public static function sendResetLink(User $user, $redirect_after_reset)
  {
    $token = app('auth.password.broker')->createToken($user);
    Mail::send('emails.hotelOwnerRegistred', ['data' => ['user' => $user, 'token' => $token, 'redirect_after_reset' => $redirect_after_reset]], function ($m) use ($user) {
      $m->to($user->email, $user->name)->subject('Dati password reset');
    });

    return response()->json([
      'message' => 'Success'
    ], 200);
  }

  /**
   * Request password reset
   * Send a link to the specified email (if it is registered) allowing the user to reset his/her password.
   * @bodyParam  email string required The email address of the user that require password reset.
   * @bodyParam  redirect_after_reset URL This will tell the API where to redirect the user after successfully reseted password.
   * */
  public function requestResetLink(Request $request)
  {
    $request->validate([
      'email' => 'required|string|email|exists:users,email'
    ]);
    $user = User::where('email', request('email'))->first();
    if ($user == null) return;
    return PublicController::sendResetLink($user, request('redirect_after_reset'));
  }

  /**
   * Submit new password
   * Change the user password and redirect him/her to the specified link, or return {'message' => 'Success'} if no url is provided.
   * */
  public function setupNewPassword(Request $request)
  {
    $reqExecTimeId = 9;
    $this->validate($request, $this->rules(), $this->validationErrorMessages());

    // Here we will attempt to reset the user's password. If it is successful we
    // will update the password on an actual user model and persist it to the
    // database. Otherwise we will parse the error and return the response.
    $response = $this->broker()->reset(
      $this->credentials($request),
      function ($user, $password) {
        $this->resetPassword($user, $password);
      }
    );

    // If the password was successfully reset, we will redirect the user back to
    // the application's home authenticated view. If there is an error we can
    // redirect them back to where they came from with their error message.

    if ($response == Password::PASSWORD_RESET) {
      if ($request->has('clear_sessions') && $request->input("clear_sessions")) {
        $user = User::where('email', request('email'))->first();
        $userTokens = $user->tokens;
        foreach ($userTokens as $token) {
          $token->revoke();
        }
      }
      if ($request->has('redirect_after_reset') && $request->input("redirect_after_reset")) {
        $this->workEnd($reqExecTimeId);
        header("Location: " . $request->input("redirect_after_reset"));
        die();
      }
      $this->workEnd($reqExecTimeId);
      return response()->json([
        'message' => 'Success'
      ], 200);
    } else {
      $this->workEnd($reqExecTimeId);
      $this->sendResetFailedResponse($request, $response);
    }
  }
}
