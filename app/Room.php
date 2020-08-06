<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class Room extends Model
{
    public function hotel()
    {
        return $this->belongsTo('App\Hotel')->withDefault();
    }

    public function device_rooms()
    {
        return $this->hasMany('App\DeviceRoom');
    }

    public function demands()
    {
        return $this->hasMany('App\Demand');
    }

    public static function Find($request_data){
        $result = DB::table('devices')
                  ->join('device_room', 'devices.id', '=', 'device_room.device_id')
                  ->join('rooms', 'rooms.id', '=', 'device_room.room_id')
                  ->join('stays', 'stays.device_room_id', '=', 'device_room.id')
                  ->join('tourists', 'tourists.id', '=', 'stays.tourist_id');

        if(array_key_exists('device_imei',$request_data) && $request_data["device_imei"]!=null){
            $result = $result->orWhere('imei', $request_data["device_imei"]);
        }

        if(array_key_exists('last_name',$request_data) && $request_data["cp"]!=null){
            $result = $result->orWhere('last_name', $request_data["last_name"]);
        }

        if(array_key_exists('room_number',$request_data) && $request_data["room_number"]!=null){
            $result = $result->orWhere('room_number', $request_data["room_number"]);
        }

        $result = $result->select('rooms.*')->get();
        $list = collect();
        foreach($result as $room){
            $d = Room::findOrFail($room->id);
            $list->push($d);
        }
        $result = $list;
        return $result;
    }

    public function setSelf($request){
        if(array_key_exists ("hotel_id",$request) && $this->hotel_id==null){
            
            try
            {
                $hotel = Hotel::findOrFail(intval($request["hotel_id"]));
                $hotel_id=$hotel->id;
            }
            catch(ModelNotFoundException $e)
            {
                $hotel_id = null;
            }
            $this->hotel_id=$hotel_id;
            
        }
        if(array_key_exists ("room_number",$request)){
            $this->room_number=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["room_number"]);
        }
        if(array_key_exists ("section",$request)){
            $this->section=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["section"]);
        }
        if(array_key_exists ("floor",$request)){
            $this->floor=intval($request["floor"]);
        }
        if(array_key_exists ("status",$request)){
            $this->status=intval($request["status"]);
        }
        if(array_key_exists ("linked_to_device",$request)){
          $this->linked_to_device = $request["linked_to_device"];
      }
        if(array_key_exists ("capacity",$request)){
            $this->capacity=intval($request["capacity"]);
            if($this->capacity<=0){
                $this->capacity=1;
            }else if($this->capacity>9){
                $this->capacity=1;
            }
        }
    }

    public function isValide(){
        if( $this->room_number!=null &&
            $this->hotel_id!=null
        ){
            return true;
        }
        else{
            return false;
        }
    }

    public static function getEditableColumns(){
        $readOnlyColumns=array('id','created_at','updated_at','room_renting_id');

        $result=[];

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(Room::class)->getTable().''));
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
        $room=new Room();
        if(array_key_exists('room_number',$request_data) && $request_data["room_number"]!==null){
            $room =  $room->where('room_number','REGEXP', $request_data["room_number"]);
        }
        
        if(array_key_exists('hotel_id',$request_data) && $request_data["hotel_id"]!==null){
            if(is_integer($request_data["hotel_id"]) || is_string($request_data["hotel_id"])){
                $room = $room->where('hotel_id', $request_data["hotel_id"]);
            }else if(is_array($request_data["hotel_id"])){
                $room = $room->wherein('hotel_id', array_values($request_data["hotel_id"]));
            }
        }

        if(array_key_exists('device_room_id',$request_data) && $request_data["device_room_id"]!==null){
            $room = $room->where('device_room_id', $request_data["device_room_id"]);
        }

        if(array_key_exists('section',$request_data) && $request_data["section"]!==null){
            $room = $room->where('section', $request_data["section"]);
        }

        if(array_key_exists('floor',$request_data) && $request_data["floor"]!==null){
            $room = $room->where('floor', $request_data["floor"]);
        }

        if(array_key_exists('capacity',$request_data) && $request_data["capacity"]['min']!=null){
            $item = $item->where('capacity','>=', $request_data["capacity"]['min']);
        }

        if(array_key_exists('capacity',$request_data) && $request_data["capacity"]['max']!=null){
            $item = $item->where('capacity','<=', $request_data["capacity"]['max']);
        }


        if(array_key_exists('orderby',$request_data) && $request_data["orderby"]!=null){
            if($request_data["nulls_last"]===true){
                $room = $room->orderByRaw("-".$request_data["orderby"]." DESC");
            }else{
                $room = $room->orderBy($request_data["orderby"],$request_data["orderby_direction"]);
            }
            
        }

        if(array_key_exists('paginate',$request_data) && $request_data["paginate"]!=null){
            $room = $room->paginate($request_data["paginate"]);
        }else{
            $room = $room->get();
        }
        
        return $room;
    }
}
