<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Notification extends Model
{
    protected $table = 'notification';

    public function hotel()
    {
        return $this->belongsTo('App\Hotel')->withDefault();
    }

    public function setSelf($request){

        if(array_key_exists ("hotel_id",$request)){
            $this->hotel_id=$request['hotel_id'];
        }

        if(array_key_exists ("title",$request)){
            $this->title=$request['title'];
        }

        if(array_key_exists ("image",$request)){
            $this->image=$request['image'];
        }
        
        if(array_key_exists ("dashboard",$request)){
            $this->image=$request['dashboard'];
        }
        
        if(array_key_exists ("begin_date",$request)){
            $this->begin_date=$request['begin_date'];
        }
        
        if(array_key_exists ("end_date",$request)){
            $this->end_date=$request['end_date'];
        }
        
        if(array_key_exists ("description",$request)){
            $this->description=$request['description'];
        }
        
        if(array_key_exists ("color",$request)){
            $this->color=$request['color'];
        }
    }

    public function isValide(){
        if($this->hotel_id!=null &&
            $this->title!=null &&
            $this->description!=null &&
            $this->image!=null
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

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(DeviceRoom::class)->getTable().''));
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
        $item = new Notification;
        if(array_key_exists('room_id',$request_data) && $request_data["room_id"]!=null){
            $item =  $item->where('room_id', $request_data["room_id"]);
        }

        if(array_key_exists('hotel_id',$request_data) && $request_data["hotel_id"]!==null){
            if(is_integer($request_data["hotel_id"]) || is_string($request_data["hotel_id"])){
                $item = $item->where('hotel_id', $request_data["hotel_id"]);
            }else if(is_array($request_data["hotel_id"])){
                $item = $item->wherein('hotel_id', array_values($request_data["hotel_id"]));
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
