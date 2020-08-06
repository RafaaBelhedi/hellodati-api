<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

/**
 * This middleware is to setup CORS to access API functionalities from
 * the dashboard and also to log all user activity
 */
class Cors
{
  public function handle($request, Closure $next)
  {
    $arr = $request->all();
    $arr['__method__'] = $request->method();
    $arr['__url__'] = $request->url();
    $arr['__time__'] = date("r");
    Log::channel('daily')->info(json_encode($arr, JSON_PRETTY_PRINT));
    // dd('s');
    // ALLOW OPTIONS METHOD
    $headers = [
      'Access-Control-Allow-Origin' => '*',
      'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE, HEAD',
      'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization, Content-Type, Allow,access-control-allow-origin'
    ];
    if ($request->getMethod() == 'OPTIONS') {
      $response = $next($request);
    foreach ($headers as $key => $value)
      $response->header($key, $value);
      return $response;
    }

    $response = $next($request);
    foreach ($headers as $key => $value)
      $response->header($key, $value);
    return $response;
  }
}