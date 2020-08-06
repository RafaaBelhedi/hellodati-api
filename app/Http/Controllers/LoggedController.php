<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Utils;
use Illuminate\Http\Request;
use App\ReqExecTime;

class LoggedController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  protected $startExecitionTime;
  protected $privileges;
  public function __construct()
  {
    $this->startExecitionTime = microtime(true);

    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
      $this->privileges = \Auth::user()->getPrivileges();
      return $next($request);
    });
    if (\Auth::user() != null) {
      $this->privileges = \Auth::user()->getPrivileges();
    }
  }

  protected function workEnd($reqExecTime_id)
  {
    try {
      $reqExecTime = ReqExecTime::findOrFail($reqExecTime_id);
      $execution_time = round((microtime(true) - $this->startExecitionTime), 2);
      $reqExecTime->avg_exec_time = round((($reqExecTime->avg_exec_time * $reqExecTime->nbr_req) + $execution_time) / ($reqExecTime->nbr_req + 1), 2);
      $reqExecTime->nbr_req++;
      if ($execution_time > $reqExecTime->execution_time) {
        $reqExecTime->execution_time = $execution_time;
      }
      $reqExecTime->save();
    } catch (ModelNotFoundException $e) {
      return;
    }
  }

  public function extractRequestParams($request_data)
  {
    if ($this->privileges['role'] == 3) {
      if (array_key_exists("hotel_id", $request_data)) {
        $request_data['hotel_id'] = $request_data["hotel_id"];
      }
    } elseif ($this->privileges['role'] == 2) {
      if (array_key_exists("hotel_id", $request_data) && is_string($request_data['hotel_id'])) {
        $request_data['hotel_id'] = array_intersect(explode(",", $request_data['hotel_id']), $this->privileges["hotel_id"]);
      } else {
        if (count($this->privileges["hotel_id"]) > 0) {
          $request_data['hotel_id'] = $this->privileges["hotel_id"];
        } else {
          $request_data['hotel_id'] = array();
        }
      }
    } elseif ($this->privileges['role'] == 1 || $this->privileges['role'] == 0) {
      if ($this->privileges["hotel_id"] != null) {
        $request_data['hotel_id'] = $this->privileges["hotel_id"];
      } else {
        $request_data['hotel_id'] = -1;
      }
    } else {
      $request_data['hotel_id'] = -1;
    }

    if (array_key_exists("orderby", $request_data)) {
      $request_data['orderby'] = preg_replace(Utils::$preg_replace['paragraphe'], '', $request_data["orderby"]);
    } else {
      $request_data['orderby'] = null;
    }

    if (array_key_exists("nulls_last", $request_data)) {
      $request_data['nulls_last'] = true;
    } else {
      $request_data['nulls_last'] = false;
    }

    if (array_key_exists("orderby_direction", $request_data)) {
      $request_data['orderby_direction'] = preg_replace(Utils::$preg_replace['paragraphe'], '', $request_data["orderby_direction"]);
      if ($request_data['orderby_direction'] != "desc" && $request_data['orderby_direction'] != "DESC") {
        $request_data['orderby_direction'] = "ASC";
      }
    } else {
      $request_data['orderby_direction'] = "ASC";
    }

    if (array_key_exists("paginate", $request_data)) {
      $request_data['paginate'] = intval($request_data["paginate"]);
      if ($request_data['paginate'] < 1) {
        $request_data['paginate'] = null;
      }
    } else {
      $request_data['paginate'] = null;
    }
    return $request_data;
  }
}
