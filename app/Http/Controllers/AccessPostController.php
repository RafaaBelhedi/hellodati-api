<?php


namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\User;
use App\Http\Resources\AccessPostResource;
/**
 * @group User
 *
 * APIs for managing sidebar access
 */
class AccessPostController extends LoggedController
{
  public function getAccess($userID) {
    $user = User::find($userID);
    $access = $user->postAccess;
    return AccessPostResource::collection($access);
  }
}
