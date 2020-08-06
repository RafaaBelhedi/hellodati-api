<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use DB;

class PostReview extends Model
{
    protected $table = 'post_reviews';

    public function post(){
        return $this->belongsTo('App\Post')->withDefault();
    }

    public function tourist(){
        return $this->belongsTo('App\Tourist')->withDefault();
    }

    public function setSelf($request){
        /*if($this->id==null && array_key_exists ("id",$request)){
            $this->id=intval($request["id"]);
        }*/
        
        if(array_key_exists ("hotel_id",$request)){
          $this->hotel_id=$request["hotel_id"];
        }
        
        if(array_key_exists ("tourist_id",$request)){
            
            try
            {
                $tourist = Tourist::findOrFail(intval($request["tourist_id"]));
                $tourist_id=$tourist->id;
                $hotel_id = $tourist->hotel_id;
                
            }
            catch(ModelNotFoundException $e)
            {
                $tourist = null;
                $hotel_id = null;
            }
            $this->tourist_id=$tourist_id;
            $this->hotel_id=$hotel_id;
        }

        if(array_key_exists ("post_id",$request)){
            
            try
            {
                $post = Post::findOrFail(intval($request["post_id"]));
                $post_id=$post->id;
                if($this->hotel_id!=$post->hotel_id){
                    $this->hotel_id=null;
                }
                
            }
            catch(ModelNotFoundException $e)
            {
                $post_id = null;
            }
            $this->post_id=$post_id;
           
        }

        if(array_key_exists ("required_colomns",$request)){
            $this->required_colomns=Utils::ArrayToFiltredStringOfArray($request["required_colomns"]);
        }

        if(array_key_exists ("optional_columns",$request)){
            $this->optional_columns=Utils::ArrayToFiltredStringOfArray($request["optional_columns"]);
        }

        if(array_key_exists ("rating",$request)){
            $this->rating=intval($request["rating"]);
        }

        if(array_key_exists ("comment",$request)){
            $this->comment=$request["comment"];
        }
    }

    public function isValide(){
        if($this->tourist_id!=null && 
            $this->post_id!=null &&
            $this->hotel_id!=null &&
            ($this->rating>=1 && $this->rating<=5)
        ){
            return true;
        }
        else{
            return false;
        }
    }
    public function exists($post_id, $tourist_id){
        $postReview = new PostReview;
        $postReview = $postReview->where('post_id', $post_id);
        $postReview = $postReview->where('tourist_id', $tourist_id);
        $postReview = $postReview->get();
        if($postReview->isEmpty())
            return false;
        return true;
    }
  
    public static function getEditableColumns(){
        $readOnlyColumns=array('id','created_at','updated_at');

        $result=[];

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(PostReview::class)->getTable().''));
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
        $postType=new PostReview();
        if(array_key_exists('post_id',$request_data) && $request_data["post_id"]!=".*" && $request_data["post_id"]!=null){
            $postType = $postType->where('post_id', $request_data["post_id"]);
        }

        if(array_key_exists('hotel_id',$request_data) && $request_data["hotel_id"]!=null){
            $postType = $postType->where('hotel_id', $request_data["hotel_id"]);
        }

        if(array_key_exists('tourist_id',$request_data) && $request_data["tourist_id"]!=null){
            $postType = $postType->where('tourist_id', $request_data["tourist_id"]);
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
