<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class PostsTranslate extends Model
{
    public function post()
    {
        return $this->belongsTo('App\Post')->withDefault();
    }

    public function setSelf($request){
        if(array_key_exists ("post_id",$request)){
            
            try
            {
                $post = Post::findOrFail(intval($request["post_id"]));
                $post_id=$post->id;
                $hotel_id=$post->hotel_id;
            }
            catch(ModelNotFoundException $e)
            {
                $post_id = null;
                $hotel_id = null;
            }
            $this->post_id=$post_id;
            $this->hotel_id=$hotel_id;
            
        }
        if(array_key_exists ("lang_iso",$request)){
            $this->lang_iso=$request["lang_iso"];
        }
        if(array_key_exists ("title",$request)){
            $this->title=$request["title"];
        }
        if(array_key_exists ("summery",$request) && $request["summery"]!=null){
            $this->summery=$request["summery"];
        }
        if(array_key_exists ("description",$request) && $request["description"]!=null){
            $this->description=$request["description"];
        }
        if(array_key_exists ("addition_data_1_text",$request) && $request["addition_data_1_text"]!=null){
            $this->addition_data_1_text=$request["addition_data_1_text"];
        }
    }

    public function isValide(){
        if( $this->lang_iso!=null
            && $this->post_id!=null
            && $this->hotel_id!=null
            && $this->title!=null
            && $this->title!=""
        ){
            return true;
        }
        else{
            return false;
        }
    }

    public function isValideByType(PostType $postType){
        $validations=[];
        $requiredFieldsMunallyChecked=["lang_iso","post_id","hotel_id"];
        if( $this->lang_iso!=null
            && $this->post_id!=null
            && $this->hotel_id!=null
        ){
            $requiredFields=array_filter(explode(',',$postType->required_colomns));
            $thisArray=$this->toArray();
            $bodyArray=["title","summery","description","addition_data_1_text"];
            foreach($requiredFields as $requiredField){
                if(!in_array($requiredField,$requiredFieldsMunallyChecked) && in_array($requiredField,$bodyArray) && (!array_key_exists ($requiredField,$thisArray) || array_key_exists ($requiredField,$thisArray) && ($thisArray[$requiredField]==null || $thisArray[$requiredField]=="null" || $thisArray[$requiredField]==""))){
                    array_push($validations,$requiredField);
                }
            }
            return true;
            if(count($validations)==0){
                return true;
            }else{
                return $validations;
            }
        }
        else{
            if($this->lang_iso==null)array_push($validations,"lang_iso");
            if($this->hotel_id==null || $this->hotel_id==0)array_push($validations,"hotel_id");
            if($this->post_id==null || $this->post_id==0)array_push($validations,"post_id");
            return $validations;
        }
        
    }

    public static function getEditableColumns(){
        $readOnlyColumns=array('id','created_at','updated_at');

        $result=[];

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(PostsTranslate::class)->getTable().''));
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
        $postTranslate=new PostsTranslate();
        if(array_key_exists('lang_iso',$request_data) && $request_data["lang_iso"]!=null){
            $postTranslate =  $postTranslate->where('lang_iso',$request_data["lang_iso"]);
        }
        
        if(array_key_exists('title',$request_data) && $request_data["title"]!=null){
            $postTranslate = $postTranslate->where('title', $request_data["title"]);
        }

        if(array_key_exists('summery',$request_data) && $request_data["summery"]!=null){
            $postTranslate = $postTranslate->where('summery', $request_data["summery"]);
        }

        if(array_key_exists('description',$request_data) && $request_data["description"]!=null){
            $postTranslate = $postTranslate->where('description', $request_data["description"]);
        }

        if(array_key_exists('addition_data_1_text',$request_data) && $request_data["addition_data_1_text"]!=null){
            $postTranslate = $postTranslate->where('addition_data_1_text', $request_data["addition_data_1_text"]);
        }

        if(array_key_exists('post_id',$request_data) && $request_data["post_id"]!=null){
            $postTranslate = $postTranslate->where('post_id', $request_data["post_id"]);
        }
        if(array_key_exists('hotel_id',$request_data) && $request_data["hotel_id"]!==null){
            if(is_integer($request_data["hotel_id"]) || is_string($request_data["hotel_id"])){
                $postTranslate = $postTranslate->where('hotel_id', $request_data["hotel_id"]);
            }else if(is_array($request_data["hotel_id"])){
                $postTranslate = $postTranslate->wherein('hotel_id', array_values($request_data["hotel_id"]));
            }
        }
        if(array_key_exists('paginate',$request_data) && $request_data["paginate"]!=null){
            $postTranslate = $postTranslate->paginate($request_data["paginate"]);
        }else{
            $postTranslate = $postTranslate->get();
        }
        return $postTranslate;
    }
}
