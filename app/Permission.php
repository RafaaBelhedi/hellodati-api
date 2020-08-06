<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class Permission extends Model
{
    public function permission_group()
    {
        return $this->belongsTo('App\PermissionGroup');
    }

    public function setSelf($request){
        if(array_key_exists ("hotel_id",$request)){
            $this->hotel_id=$request["hotel_id"];
        }
        if(array_key_exists ("permission_group_id",$request)){
          $this->permission_group_id=$request["permission_group_id"];
        }
        if(array_key_exists ("permission_id",$request)){
          $this->permission_id=$request["permission_id"];
        }
        if(array_key_exists ("level",$request)){
          $this->level=$request["level"];
        }
        if(array_key_exists ("text",$request)){
          $this->text=$request["text"];
        }      
    }

    public function isValide(){
        if( $this->hotel_id!=null 
        && $this->permission_group_id !=null
        && $this->level !=null
        && $this->level !=''
        && $this->text !=null
        && $this->text !=''
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

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(Permission::class)->getTable().''));
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
        $permission_group=new Permission();
        if(array_key_exists('permission_group_id',$request_data) && $request_data["permission_group_id"]!=null){
            $permission_group = $permission_group->where('permission_group_id', $request_data["permission_group_id"]);
        }
        if(array_key_exists('text',$request_data) && $request_data["text"]!=null){
          $permission_group = $permission_group->where('text', $request_data["text"]);
      }
      if(array_key_exists('user_id',$request_data) && $request_data["user_id"]!=null){
        $p = new PermissionGroup();
        $p = $p->Finder(['user_id'=>$request_data["user_id"]])->get()[0];
        $permission_group = $permission_group->where('permission_group_id', $p->id);
    }
      
        return $permission_group->get();
    }
}
