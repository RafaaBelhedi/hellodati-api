<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class App extends Model
{
  public function app_versions()
  {
    return $this->hasMany('App\AppVersion');
  }

  public function setSelf($request)
  {

    if ($this->package_name == null && array_key_exists("package_name", $request)) {
      $this->package_name = preg_replace('[a-zA-Z0-9._]', '', $request["package_name"]);
      if ($this->package_name != $request["package_name"]) {
        $this->package_name = null;
      }
    }

    if (array_key_exists("name", $request)) {
      $this->name = preg_replace(Utils::$preg_replace['paragraphe'], '', $request["name"]);
    }

    if (array_key_exists("icon", $request)) {
      $this->icon = filter_var($request["icon"], FILTER_SANITIZE_URL);
    }
  }

  public function isValide()
  {
    if (
      $this->package_name != null &&
      $this->name != null &&
      $this->icon != null
    ) {
      return true;
    } else {
      return false;
    }
  }

  public static function getEditableColumns()
  {
    $readOnlyColumns = array('id', 'created_at', 'updated_at');

    $result = [];

    $table_info_columns  = (array) DB::select(DB::raw('SHOW FULL COLUMNS FROM ' . app(App::class)->getTable() . ''));
    $table_info_columns = json_decode(json_encode($table_info_columns), true);

    foreach ($table_info_columns as $column) {
      unset($column["Collation"], $column["Key"], $column["Extra"], $column["Privileges"]);
      if (!in_array($column['Field'], $readOnlyColumns)) {
        if ($column['Null'] == "NO" && $column['Default'] == null) {
          $result['required'][$column['Field']] = $column;
        } else {
          $result['possible'][$column['Field']] = $column;
        }
      } else {
        $result['read_only'][$column['Field']] = $column;
      }
    }


    return $result;
  }

  public static function Finder($request_data)
  {
    $item = new App();
    if (array_key_exists('package_name', $request_data) && $request_data["package_name"] != null) {
      $item =  $item->where('package_name', $request_data["package_name"]);
    }
    if (array_key_exists('name', $request_data) && $request_data["name"] != null) {
      $item =  $item->where('name', $request_data["name"]);
    }
    if (array_key_exists('icon', $request_data) && $request_data["icon"] != null) {
      $item =  $item->where('icon', $request_data["icon"]);
    }


    if (array_key_exists('orderby', $request_data) && $request_data["orderby"] != null) {
      if ($request_data["nulls_last"] === true) {
        $item = $item->orderByRaw("-" . $request_data["orderby"] . " DESC");
      } else {
        $item = $item->orderBy($request_data["orderby"], $request_data["orderby_direction"]);
      }
    }
    if (array_key_exists('paginate', $request_data) && $request_data["paginate"] != null) {
      $item = $item->paginate($request_data["paginate"]);
    } else {
      $item = $item->get();
    }

    return $item;
  }
}
