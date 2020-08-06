<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class PostType extends Model
{
    protected $table = 'post_types';

    public function setSelf($request){
        /*if($this->id==null && array_key_exists ("id",$request)){
            $this->id=intval($request["id"]);
        }*/

        if(array_key_exists ("name",$request)){
            $this->name=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["name"]);
        }

        if(array_key_exists ("required_colomns",$request)){
            $this->required_colomns=Utils::ArrayToFiltredStringOfArray($request["required_colomns"]);
        }

        if(array_key_exists ("optional_columns",$request)){
            $this->optional_columns=Utils::ArrayToFiltredStringOfArray($request["optional_columns"]);
        }

        if(array_key_exists ("preview_image",$request)){
            $this->preview_image=filter_var($request["preview_image"], FILTER_SANITIZE_URL);
        }

        if(array_key_exists ("category",$request)){
            $this->category=intval($request["category"]);
        }
    }

    public function isValide(){
        if($this->name!=null && 
            $this->name!="" &&
            $this->required_colomns!=null && 
            $this->required_colomns!=""
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

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(PostType::class)->getTable().''));
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
        $postType=new PostType();
    
        if(array_key_exists('name',$request_data) && $request_data["name"]!=".*" && $request_data["name"]!=null){
            $postType = $postType->where('name','REGEXP', $request_data["name"]);
        }

        if(array_key_exists('required_colomns',$request_data) && $request_data["required_colomns"]!=".*" && $request_data["required_colomns"]!=null){
            $postType = $postType->where('required_colomns','REGEXP', $request_data["required_colomns"]);
        }

        if(array_key_exists('optional_columns',$request_data) && $request_data["optional_columns"]!=".*" && $request_data["optional_columns"]!=null){
            $postType = $postType->where('optional_columns','REGEXP', $request_data["optional_columns"]);
        }

        if(array_key_exists('category',$request_data) && $request_data["category"]!=null){
            $postType = $postType->where('category', $request_data["category"]);
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
