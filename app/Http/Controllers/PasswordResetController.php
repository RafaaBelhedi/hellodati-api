<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use App\Http\Requests;
use App\Http\Utils;
use App\PasswordReset;
use App\User;
use App\Http\Resources\DeliveryPlaceTemplate;
use Exception;
use Illuminate\Support\Facades\Hash;


/**
 * @group delivery
 *
 * APIs for managing templates of posts
 */
class PasswordResetController extends BaseController
{
  public function add(){
		$pw = new PasswordReset();
		$user = User::where(['email'=>request()->input('email')])->first();
    try{
      if(PasswordReset::where(['user_id'=>$user->id])->get()->count()!=0)
      return response()->json(['error'=>'User has already request a password reset'],400);

      User::findOrFail($user->id);
      $pw->setSelf(['user_id'=>$user->id]);
      $pw->save();
    }catch(Exception $e){
			dd($e);
      return response()->json(['error'=>'User doesn\'t exist'],400);
    }
    return response()->json(['success'=>'User password reset request added.']);

  }
}