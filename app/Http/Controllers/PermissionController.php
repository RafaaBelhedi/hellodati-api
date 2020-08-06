<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Post;
use App\Http\Utils;
use App\Permission;
use App\Http\Resources\PermissionTemplate;
use App\Http\Resources\PostTemplate;
use App\User;

/**
 * @group Permission
 *
 * APIs for managing templates of ads
 */
class PermissionController extends LoggedController
{
  /**
   * Index
   * Display a listing of permission templates.
   * To filter permission templates, add any of the Permission object properties to the querry ?{property}={value}
   *
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 47;
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }
    $request_data = $this->extractRequestParams($request_data);

    $Permissions = Permission::Finder($request_data);
    $res = PermissionTemplate::collection($Permissions);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of Permission.
   * These fields can also be used to filter the search.
   * */
  public function create()
  { 
    $reqExecTimeId = 46;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = Permission::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new Permission.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 49;
    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $new_data = json_decode($request->getContent(), true);
    if ($new_data == null) {
      $new_data = $request->all();
    }
    $data = json_decode($new_data['data'],true);
    $Permission = new Permission;
    $Permission->setSelf($new_data);
    if ($Permission->isValide()) {
      $Permission->save();
      $res = new PermissionTemplate($Permission);
    } else {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  public function options(){
    $level = 2;
    // $arr = [];
    // $posts = Post::where(["parent_id"=>null,"hotel_id"=>$this->privileges["hotel_id"][0]])->get();
    // for($i = 0; $i < count($posts); $i++)
    //   $arr[] = $posts[$i]->id;
    // $posts = Post::whereIn("parent_id",$arr)->get();
    // for($i = 0; $i < count($posts); $i++)
    //   $arr[] = $posts[$i]->id;
    // $posts = Post::whereIn("parent_id",$arr)->where('state',1)->get();
    // $arr = [
    //   ['text'=>'Devices','id'=>'devices'], 
    //   ['text'=>'Notifications','id'=>'notifications'], 
    //   ['text'=>'Statistics','id'=>'statistics'], 
    //   ['text'=>'History','id'=>'history']
    // ];
    // for($i = 0; $i < count($posts); $i++)
    //   {
    //     $perm = Permission::where(['text'=>$arr[$i]]);
    //     $arr[] = ['id'=>$posts[$i]->id,'text'=>$posts[$i]->getTranslations('en')->title,'level'=>];
    //   }
    $u = User::find(request()->input('user_id'));
    // print_r($u);
    // print_r($u->permission_group->permissions);
      return PermissionTemplate::collection($u->permission_group->permissions);

    return response()->json([
      'data'=>$arr
    ]);
  }

  /**
   * Show
   * Display the specified App by {id}.
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
      $Permission = Permission::findOrFail($id);
      $res = PermissionTemplate::collection(collect([$Permission]));
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
   * Edit properties of existing Permission.
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
      $Permission = Permission::findOrFail($id);

      $Permission->setSelf($new_data);
      // if ($Permission->isValide()) {
        $Permission->save();
      // }
      $res = new PermissionTemplate($Permission);
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }

    $this->workEnd($reqExecTimeId);
    return $res;
  }

  public function new_permissions($user_id){
    $user = \App\PermissionGroup::find($user_id);
    $level = 2;
    $arr = [];
    $posts = Post::where(["parent_id"=>null,"hotel_id"=>$this->privileges["hotel_id"][0]])->get();
    for($i = 0; $i < count($posts); $i++)
      $arr[] = $posts[$i]->id;
    $posts = Post::whereIn("parent_id",$arr)->get();
    for($i = 0; $i < count($posts); $i++)
      $arr[] = $posts[$i]->id;
    $posts = Post::whereIn("parent_id",$arr)->where('state',1)->get();
    $arr = [
      ['text'=>'Devices','id'=>'devices'], 
      ['text'=>'Notifications','id'=>'notifications'], 
      ['text'=>'Statistics','id'=>'statistics'], 
      ['text'=>'Rooms','id'=>'rooms'], 
      ['text'=>'Tourists','id'=>'tourists'], 
      ['text'=>'History','id'=>'history']
    ];
    for($i = 0; $i < count($posts); $i++)
      $arr[] = ['id'=>$posts[$i]->id,'text'=>$posts[$i]->getTranslations('en')->title];

    // dd($arr);
    $permissions=[];
    for($i=0;$i<count($arr);$i++){
      $permission = new Permission();
        $permission->setSelf(['permission_group_id'=>$user->id,'level'=>2,'text'=>$arr[$i]['text'],'permission_id'=>$arr[$i]['id']]);
        $permission->save();
        $permissions[]=['permission_group_id'=>$user->id,'level'=>2,'text'=>$arr[$i]['text'],'permission_id'=>$arr[$i]['id']];
      }
    print_r($permissions);
      

    }

  /**
   * Destroy
   * Remove a Permission.
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
      $Permission = Permission::findOrFail($id);
      $Permission->delete();
      $res = ['delele permission ' . $id => 'success'];
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }
}