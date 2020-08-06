<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class PermissionGroup extends Model
{
    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function permissions()
    {
        return $this->hasMany('App\Permission');
    }

    public function setSelf($request){
        if(array_key_exists ("hotel_id",$request)){
            $this->hotel_id=$request["hotel_id"];
        }
    }

    public function isValide(){
        if( $this->hotel_id!=null ){
            return true;
        }
        else{
            return false;
        }
    }

    public static function getEditableColumns(){
        $readOnlyColumns=array('id','created_at','updated_at');

        $result=[];

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(PermissionGroup::class)->getTable().''));
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
        $permission_group=new PermissionGroup();
        if(array_key_exists('hotel_id',$request_data) && $request_data["hotel_id"]!=null){
            $permission_group = $permission_group->where('hotel_id', $request_data["hotel_id"]);
        }
        
        return $permission_group;
    }
}
