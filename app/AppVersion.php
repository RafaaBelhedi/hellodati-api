<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class AppVersion extends Model
{
    public function app()
    {
        return $this->belongsTo('App\App')->withDefault();
    }

    public function setSelf($request){

        if($this->app_id==null && array_key_exists ("app_id",$request)){
            try
            {
                $app = App::findOrFail(intval($request["app_id"]));
                $app_id=$app->id;
            }
            catch(ModelNotFoundException $e)
            {
                $app_id = null;
            }
            $this->app_id=$app_id;
        }

        if(array_key_exists ("version_name",$request)){
            $this->version_name=preg_replace('[a-zA-Z0-9.-]', '',$request["version_name"]);
        }

        if(array_key_exists ("version_code",$request)){
            $this->version_code=intval($request["version_code"]);
        }

        if(array_key_exists ("install_url",$request)){
            $this->install_url=filter_var($request["install_url"], FILTER_SANITIZE_URL);
        }

        if(array_key_exists ("is_live",$request)){
            if($request["is_live"]===true || $request["is_live"]==="true" || $request["is_live"]==1){
                $this->is_live=1;
            }else if($request["is_live"]===false || $request["is_live"]==="false"  || $request["is_live"]==0){
                $this->is_live=0;
            }
        }

    }

    public function isValide(){
        if($this->app_id!=null &&
            $this->version_name!=null &&
            $this->version_code!=null &&
            $this->install_url!=null
        ){
            return true;
        }
        else{
            return false;
        }
    }

    public static function getEditableColumns(){
        $readOnlyColumns=array('id','created_at','updated_at');

        $result=[];

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(AppVersion::class)->getTable().''));
        $table_info_columns=json_decode(json_encode($table_info_columns), true);

        foreach($table_info_columns as $column){
            unset($column["Collation"],$column["Key"],$column["Extra"],$column["Privileges"]);
            if(!in_array($column['Field'],$readOnlyColumns)){
                if($column['Null']=="NO" && $column['Default']==null){
                    $result['required'][$column['Field']]=$column;
                }else{
                    $result['possible'][$column['Field']]=$column;
                }
            }else{
                $result['read_only'][$column['Field']]=$column;
            }
        }

        
        return $result;
    }

    public static function Finder($request_data){
        $item=new AppVersion();
        if(array_key_exists('app_id',$request_data) && $request_data["app_id"]!=null){
            $item =  $item->where('app_id', $request_data["app_id"]);
        }
        if(array_key_exists('version_name',$request_data) && $request_data["version_name"]!=null){
            $item =  $item->where('version_name', $request_data["version_name"]);
        }
        if(array_key_exists('version_code',$request_data) && $request_data["version_code"]!=null){
            $item =  $item->where('version_code', $request_data["version_code"]);
        }
        if(array_key_exists('install_url',$request_data) && $request_data["install_url"]!=null){
            $item =  $item->where('install_url', $request_data["install_url"]);
        }
        if(array_key_exists ("is_live",$request_data)){
            if($request_data["is_live"]===true || $request_data["is_live"]==="true"  || $request_data["is_live"]==1){
                $item =  $item->where('is_live', 1);
            }else if($request_data["is_live"]===false || $request_data["is_live"]==="false"  || $request_data["is_live"]==0){
                $item =  $item->where('is_live', 0);
            }
        }


        if(array_key_exists('orderby',$request_data) && $request_data["orderby"]!=null){
            if($request_data["nulls_last"]===true){
                $item = $item->orderByRaw("-".$request_data["orderby"]." DESC");
            }else{
                $item = $item->orderBy($request_data["orderby"],$request_data["orderby_direction"]);
            }
            
        }
        if(array_key_exists('paginate',$request_data) && $request_data["paginate"]!=null){
            $item = $item->paginate($request_data["paginate"]);
        }else{
            $item = $item->get();
        }
        
        return $item;
    }
}
