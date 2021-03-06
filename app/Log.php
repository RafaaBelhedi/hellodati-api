<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class Log extends Model
{
    protected $table = 'log';

    public function hotel(){
        return $this->belongsTo('App\Hotel')->withDefault();
    }

    public function setSelf($request){
        if(array_key_exists ("text",$request)){
            $this->text=$request["text"];
        }

        if(array_key_exists ("user_id",$request)){
            $this->user_id=$request["user_id"];
        }
        
    }

    public function isValide(){
        if($this->text != null && 
            $this->user_id != null
            )
        {
            return true;
        }
        else{
            return false;
        }
    }
  
    public static function getEditableColumns(){
        $readOnlyColumns=array('id','created_at','updated_at');

        $result=[];

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(Log::class)->getTable().''));
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
        $postType=new Log();
        
        if(array_key_exists('user_id',$request_data) && $request_data["user_id"]!=null){
            $postType = $postType->where('user_id', $request_data["user_id"]);
        }

        if(array_key_exists('paginate',$request_data) && $request_data["paginate"]!=null){
            $postType = $postType->paginate($request_data["paginate"]);
        }else{
            $postType = $postType->get();
        }
        
        return $postType;
    }
}
