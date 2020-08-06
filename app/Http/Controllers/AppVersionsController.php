<?php

namespace App\Http\Controllers;

use App\App;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use ApkParser\Parser;
use App\Http\Requests;
use App\Http\Utils;
use App\AppVersion;
use App\Http\Resources\AppVersionTemplate;
use Illuminate\Support\Facades\Storage;

/**
 * @group app version
 *
 * APIs for managing apps versions
 */
class AppVersionsController extends LoggedController
{
  /**
   * Index
   * Display a listing of apps versions.
   * To filter apps versions, add any of the AppVersion object properties to the querry ?{property}={value}
   *
   * 
   * */
  public function index(Request $request)
  {
    $reqExecTimeId = 95;
    if (gettype($request->getContent()) == "object") {
      $request_data = json_decode($request->getContent(), true);
    } else {
      $request_data = $request->all();
    }
    $request_data = $this->extractRequestParams($request_data);

    $appVersions = AppVersion::Finder($request_data);

    $res = AppVersionTemplate::collection($appVersions);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of AppVersion.
   * These fields can also be used to filter the search.
   * */
  public function create()
  {
    $reqExecTimeId = 94;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $res = AppVersion::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new AppVersion.
   * */
  public function store(Request $request)
  {
    $reqExecTimeId = 97;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    $appVersion = new AppVersion;
    $appVersion->setSelf($request->all());
    if ($appVersion->isValide()) {
      $appVersion->save();
      $this->pushNotifUpdate($request, $appVersion);
      $res = AppVersionTemplate::collection(collect([$appVersion]));
    } else {
      $res = response()->json([
        'Invalid_or_missing_fields' => "error"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  function pushNotifUpdate(Request $request, $appVersion)
  {
    $notif = array(
      'title' => 'Dati update',
      'summery' => 'version ' . $appVersion->version_code . ' is now available',
      'update_request' => array(
        'app_id' => $appVersion->app_id
      )
    );
    $request->request->add(['data' => json_encode($notif)]);
    $notifyDevicesController = new NotifyDevicesController();
    return $notifyDevicesController->notifyHotelsDevices($request);
  }

  /**
   * Show
   * Display the specified AppVersion by {id}.
   * */
  public function show($id)
  {
    $reqExecTimeId = 96;
    try {
      $appVersion = AppVersion::findOrFail($id);
      $res = AppVersionTemplate::collection(collect([$appVersion]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'Invalid_or_missing_fields' => "error"
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
   * Edit properties of existing AppVersion.
   * */
  public function update(Request $request, $id)
  {
    $reqExecTimeId = 98;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $appVersion = AppVersion::findOrFail($id);
      $appVersion->setSelf($request->all());
      if ($appVersion->isValide()) {
        $appVersion->save();
      }
      $res = AppVersionTemplate::collection(collect([$appVersion]));
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'Invalid_or_missing_fields' => "error"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Destroy
   * Remove an AppVersion.
   * */
  public function destroy($id)
  {
    $reqExecTimeId = 99;
    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    try {
      $appVersion = AppVersion::findOrFail($id);
      $appVersion->delete();
      $res = ['delele AppVersion ' . $id => 'success'];
    } catch (ModelNotFoundException $e) {
      $res = response()->json([
        'Invalid_or_missing_fields' => "error"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Handles the upload of the APK and all the necessary validations
   */

  public function sendApk()
  {

    if ($this->privileges["role"] != 3) {
      return response()->json([
        'message' => 'Forbidden'
      ], 403);
    }
    request()->validate([
      'version_file' => 'required|file',
      'app_id' => 'required|int'
    ]);

    $app = App::find(request()->app_id);
    $app_versions = AppVersion::Finder(['app_id' => request()->app_id]);
    $file_type = request()->version_file->getClientOriginalExtension();

    if ($file_type !== "apk" && $file_type !== "APK") {
      return ("this is not an Apk file!");
    }

    if ($app == null) {
      return "app doesn't exist!";
    }

    $apk = new Parser(request()->version_file);
    $manifest = $apk->getManifest();
    $package_name = $manifest->getPackageName();
    $version_code = $manifest->getVersionCode();
    $version_name = $manifest->getVersionName();

    if ($app["package_name"] != $package_name) {

      return "uploaded file has different package name!";
    }

    $lastVersion = 0;
    for ($i = 0; $i < count($app_versions); $i++) {

      if (intval($app_versions[$i]["version_code"]) > $lastVersion) {

        $lastVersion = intval($app_versions[$i]["version_code"]);
      }
    }
    // if ($lastVersion >= $version_code) {

    //   return "version code must be grater than last version ( " . $lastVersion . " )!";
    // }
   
    $fileNameToStore= time().'.'.$file_type;
    // Upload Image
    $path = request()->version_file->storeAs('apk', $fileNameToStore,'public_uploads');
    


    //$path = Storage::disk('public_uploads')->put('apk/'.$fileName, request()->version_file);
    request()->merge(['version_code' => $version_code, 'version_name' => $version_name, 'install_url' => asset('uploads/' . $path)]);
    return $this->store(request());
    // $request->version_file->storeAs('apps_store', $fileName);
  }
}
