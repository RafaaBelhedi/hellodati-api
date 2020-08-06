<?php


namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\User;
use App\SideBarAccess;
use App\Http\Resources\SideBarAccessResource;
/**
 * @group User
 *
 * APIs for managing sidebar access
 */
class SideBarAccessController extends LoggedController
{
  public function getAccess($userID) {
    $user = User::find($userID);
    $access = $user->SideBarAccess;
    return SideBarAccessResource::collection(collect([$access]));
  }
}
