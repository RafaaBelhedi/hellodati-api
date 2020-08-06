<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\Log;
use App\Http\Resources\LogTemplate;

/**
 * @group Log
 *
 * APIs for managing templates of ads
 */
class LogController extends LoggedController
{
  /**
   * Index
   * Display a listing of log templates.
   * To filter log templates, add any of the Log object properties to the querry ?{property}={value}
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

    $Logs = Log::Finder($request_data);

    $res = LogTemplate::collection($Logs);
    $this->workEnd($reqExecTimeId);
    return $res;
  }


  /**
   * Columns
   * Display the possible fields of Log.
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
    $res = Log::getEditableColumns();
    $this->workEnd($reqExecTimeId);
    return $res;
  }

  /**
   * Store
   * Create a new Log.
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
    $Log = new Log;
    $Log->setSelf($new_data);
    if ($Log->isValide()) {
      $Log->save();
      $res = new LogTemplate($Log);
    } else {
      $res = response()->json([
        'error' => "Invalid_or_missing_fields"
      ], 500);
    }
    $this->workEnd($reqExecTimeId);
    return $res;
  }
}
