<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class Tourist extends Model
{
    protected $guarded = [];
    
    public function hotel()
    {
        return $this->belongsTo('App\Hotel')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo('App\User')->withDefault();
    }

    public function stay()
    {
        return $this->hasOne('App\Stay')->withDefault();
    }

    

    public function getIsResidentAttribute()
    {
        return ($this->stay_id!=null);
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
        if(array_key_exists ("leaved",$request)){
            $this->leaved= $request["leaved"];
        }
        if(array_key_exists ("cin_number",$request)){
            $this->cin_number=intval($request["cin_number"]);
        }
        if(array_key_exists ("age",$request)){
            $this->age=$request["age"];
        }
        if(array_key_exists ("user_id",$request)){
            $this->cin_number=intval($request["user_id"]);
        }
        if(array_key_exists ("passport_number",$request)){
            $this->passport_number=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["passport_number"]);
        }
        if(array_key_exists ("first_name",$request)){
            $this->first_name=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["first_name"]);
        }
        if(array_key_exists ("image",$request)){
            $this->image=$request["image"];
        }
        if(array_key_exists ("last_name",$request)){
            $this->last_name=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["last_name"]);
        }
        if((array_key_exists ("prefix_name",$request) &&
            ($request["prefix_name"]=="Mr." || $request["prefix_name"]=="Ms." || $request["prefix_name"]=="Miss." || $request["prefix_name"]=="Mrs."))
        ){
            $this->prefix_name=$request["prefix_name"];
        }
        if(array_key_exists ("born",$request)){
            $this->born=$request["born"];
        }
        if(array_key_exists ("check_in",$request)){
            $this->check_in=$request["check_in"];
        }
        if(array_key_exists ("check_out",$request)){
            $this->check_out=$request["check_out"];
        }
        if(array_key_exists ("gender",$request)){
            $this->gender=intval($request["gender"]);
        }
        if(array_key_exists ("app_lang",$request)){
            $this->app_lang=$request["app_lang"];
        }
        if(array_key_exists ("languages",$request)){
            $this->languages=Utils::ArrayToFiltredStringOfArray($request["languages"]);
        }
        if(array_key_exists ("password",$request)){
            $this->password=$request["password"];
        }
        if(array_key_exists ("number",$request)){
          $this->number=$request["number"];
        }
        if(array_key_exists ("country",$request)){
            $this->country=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["country"]);
        }
        if(array_key_exists ("city",$request)){
            $this->city=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["city"]);
        }
        if(array_key_exists ("zip_code",$request)){
            $this->zip_code=intval($request["zip_code"]);
        }
        if(array_key_exists ("address_1",$request)){
            $this->address_1=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["address_1"]);
        }
        if(array_key_exists ("address_2",$request)){
            $this->address_2=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["address_2"]);
        }
        if(array_key_exists ("email",$request)){
            $this->email=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["email"]);
        }
        if(array_key_exists ("email_status",$request)){
            $this->email_status=intval($request["email_status"]);
        }
        if(array_key_exists ("phone_number",$request)){
            $this->phone_number=preg_replace(Utils::$preg_replace['phone'], '',$request["phone_number"]);
        }
        if(array_key_exists ("work_phone_number",$request)){
            $this->work_phone_number=preg_replace(Utils::$preg_replace['phone'], '',$request["work_phone_number"]);
        }
        if(array_key_exists ("company",$request)){
            $this->company=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["company"]);
        }
    }

    public function setNewPassword($request){
        if(array_key_exists ("old_password",$request) && $request["old_password"]===$this->password && array_key_exists ("new_password",$request)){
            $this->password=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["new_password"]);
            return true;
        }
        return false;
    }

    public function isValide(){
        if(
            $this->first_name!=null &&
            $this->hotel_id!=null && 
            ($this->cin_number!=null || $this->passport_number!=null)
        ){
            return true;
        }
        else{
            return false;
        }
    }

    public static function getEditableColumns(){
        $readOnlyColumns=array('id','created_at','updated_at','device_renting_id','stay_id');

        $result=[];

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(Tourist::class)->getTable().''));
        $table_info_columns=json_decode(json_encode($table_info_columns), true);

        foreach($table_info_columns as $column){
            unset($column["Collation"],$column["Key"],$column["Extra"],$column["Privileges"]);
            if(!in_array($column['Field'],$readOnlyColumns)){
                if($column['Null']=="NO" && $column['Default']===null){
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
        $item=new Tourist();
        if(array_key_exists('hotel_id',$request_data) && $request_data["hotel_id"]!==null){
            if(is_integer($request_data["hotel_id"]) || is_string($request_data["hotel_id"])){
                $item = $item->where('hotel_id', $request_data["hotel_id"]);
            }else if(is_array($request_data["hotel_id"])){
                $item = $item->wherein('hotel_id', array_values($request_data["hotel_id"]));
            }
        }

        if(array_key_exists('cin_number',$request_data) && $request_data["cin_number"]!=null){
            $item =  $item->where('cin_number', $request_data["cin_number"]);
        }
        if(array_key_exists('age',$request_data) && $request_data["age"]!=null){
            $item =  $item->where('age', $request_data["age"]);
        }

        if(array_key_exists('passport_number',$request_data) && $request_data["passport_number"]!=null){
            $item =  $item->where('passport_number', $request_data["passport_number"]);
        }

        if(array_key_exists('first_name',$request_data) && $request_data["first_name"]!=null){
            $item =  $item->where('first_name', $request_data["first_name"]);
        }

        if(array_key_exists('last_name',$request_data) && $request_data["last_name"]!=null){
            $item =  $item->where('last_name', $request_data["last_name"]);
        }

        if(array_key_exists('prefix_name',$request_data) && $request_data["prefix_name"]!=null){
            $item =  $item->where('prefix_name', $request_data["prefix_name"]);
        }

        if(array_key_exists('prefix_name',$request_data) && $request_data["prefix_name"]!=null){
            $item =  $item->where('prefix_name', $request_data["prefix_name"]);
        }

        if(array_key_exists('born',$request_data) && $request_data["born"]['min']!=null){
            $item = $item->where('born','>=', $request_data["born"]['min']);
        }

        if(array_key_exists('born',$request_data) && $request_data["born"]['max']!=null){
            $item = $item->where('born','<=', $request_data["born"]['max']);
        }

        if(array_key_exists('gender',$request_data) && $request_data["gender"]!=null){
            $item =  $item->where('gender', $request_data["gender"]);
        }

        if(array_key_exists('languages',$request_data) && $request_data["languages"]!=".*" && $request_data["languages"]!=null){
            $post = $post->where('languages','REGEXP', $request_data["languages"]);
        }

        if(array_key_exists('app_lang',$request_data) && $request_data["app_lang"]!=null){
            $item =  $item->where('app_lang', $request_data["app_lang"]);
        }

        if(array_key_exists('country',$request_data) && $request_data["country"]!=null){
            $item =  $item->where('country', $request_data["country"]);
        }

        if(array_key_exists('city',$request_data) && $request_data["city"]!=null){
            $item =  $item->where('city', $request_data["city"]);
        }

        if(array_key_exists('zip_code',$request_data) && $request_data["zip_code"]!=null){
            $item =  $item->where('zip_code', $request_data["zip_code"]);
        }

        if(array_key_exists('address_1',$request_data) && $request_data["address_1"]!=".*" && $request_data["address_1"]!=null){
            $item = $item->where('address_1','REGEXP', $request_data["address_1"]);
        }

        if(array_key_exists('address_2',$request_data) && $request_data["address_2"]!=".*" && $request_data["address_2"]!=null){
            $item = $item->where('address_2','REGEXP', $request_data["address_2"]);
        }

        if(array_key_exists('email',$request_data) && $request_data["email"]!=null){
            $item =  $item->where('email', $request_data["email"]);
        }

        if(array_key_exists('email_status',$request_data) && $request_data["email_status"]!=null){
            $item =  $item->where('email_status', $request_data["email_status"]);
        }

        if(array_key_exists('phone_number',$request_data) && $request_data["phone_number"]!=null){
            $item =  $item->where('phone_number', $request_data["phone_number"]);
        }

        if(array_key_exists('work_phone_number',$request_data) && $request_data["work_phone_number"]!=null){
            $item =  $item->where('work_phone_number', $request_data["work_phone_number"]);
        }

        if(array_key_exists('company',$request_data) && $request_data["company"]!=".*" && $request_data["company"]!=null){
            $item = $item->where('company','REGEXP', $request_data["company"]);
        }

        if(array_key_exists('stay_id',$request_data) && $request_data["stay_id"]!=null){
            $item =  $item->where('stay_id', $request_data["stay_id"]);
        }
        

        if(array_key_exists('is_resident',$request_data) && $request_data["is_resident"]!==null){
            if($request_data["is_resident"]==0 || $request_data["is_resident"]===false || $request_data["is_resident"]==='false'){
                $item = $item->where('stay_id', null);
            }else if($request_data["is_resident"]==1 || $request_data["is_resident"]===true || $request_data["is_resident"]==='true'){
                $item = $item->where('stay_id','<>', null);
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

    public static function Find($request_data){
        $b = 0;
        $result = new Tourist;
        if(array_key_exists('first_name',$request_data) && $request_data["first_name"]!=null){
            $result = $result->orWhere('first_name', $request_data["first_name"]);
        }

        if(array_key_exists('cp',$request_data) && $request_data["cp"]!=null){
            $result = $result->orWhere('cin_number', $request_data["cp"])->orWhere('passport_number', $request_data["cp"]);
        }

        if(array_key_exists('last_name',$request_data) && $request_data["last_name"]!=null){
            $result = $result->orWhere('last_name', $request_data["last_name"]);
        }

        if(array_key_exists('age',$request_data) && $request_data["age"]!=null){
            $result = $result->orWhere('age', $request_data["age"]);
        }

        $result = $result->get();

        return $result;
    }
}
