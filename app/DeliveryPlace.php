<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class DeliveryPlace extends Model
{
    protected $table = 'delivery_places';

    public function hotel(){
        return $this->belongsTo('App\Hotel')->withDefault();
    }

    public function setSelf($request){
        if(array_key_exists ("title",$request)){
            $this->title=$request["title"];
        }

        if(array_key_exists ("hotel_id",$request)){
            $this->hotel_id=$request["hotel_id"];
        }
        
    }

    public function isValide(){
        if($this->title != null && 
            $this->hotel_id != null
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

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(DeliveryPlace::class)->getTable().''));
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
        $postType=new DeliveryPlace();
        
        if(array_key_exists('hotel_id',$request_data) && $request_data["hotel_id"]!=null){
            $postType = $postType->where('hotel_id', $request_data["hotel_id"]);
        }

        if(array_key_exists('title',$request_data) && $request_data["title"]!=null){
            $postType = $postType->where('title', $request_data["title"]);
        }
        
        if(array_key_exists('orderby',$request_data) && $request_data["orderby"]!=null){
            if($request_data["nulls_last"]===true){
                $postType = $postType->orderByRaw("-".$request_data["orderby"]." DESC");
            }else{
                $postType = $postType->orderBy($request_data["orderby"],$request_data["orderby_direction"]);
            }
            
        }

        if(array_key_exists('paginate',$request_data) && $request_data["paginate"]!=null){
            $postType = $postType->paginate($request_data["paginate"]);
        }else{
            $postType = $postType->get();
        }
        
        return $postType;
    }
}
