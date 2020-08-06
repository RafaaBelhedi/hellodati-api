<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Mail;

class UserController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ResetsPasswords;

  /**
   * Send password reset link
   */

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
   * Returns password reset link
   */

  public function requestResetLink(Request $request)
  {
    $request->validate([
      'email' => 'required|string|email|exists:users,email'
    ]);
    dd($request->all());
    $user = User::where('email', request('email'))->first();
    if ($user == null) return;
    return UserController::sendResetLink($user, request('redirect_after_reset'));
  }

  /**
   * Change password after all verifications.
   */

  public function setupNewPassword(Request $request)
  {
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
        header("Location: " . $request->input("redirect_after_reset"));
        die();
      }
      return response()->json([
        'message' => 'Success'
      ], 200);
    } else {
      $this->sendResetFailedResponse($request, $response);
    }
  }
}
