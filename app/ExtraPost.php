<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Utils;
use App\OpenTimeWeek;
use DB;

class ExtraPost extends Model
{
    
    protected $table="extra_posts";

    public function translates()
    {
        return $this->hasMany('App\ExtraPostsTranslate','post_id');
    }

    public function getTranslations($lang){
        $translates=$this->translates;
        $englishTranslate=null;
        for($i=0;$i<count($translates);$i++){
            if($translates[$i]["lang_iso"]==$lang){
                return $translates[$i];
            }
            if($translates[$i]["lang_iso"]=="en"){
                $englishTranslate=$translates[$i];
            }
        }
        if($englishTranslate!=null){
            return $englishTranslate;
        }else if(count($translates)>0){
            return $translates[0];
        }else{
            return new ExtraPostsTranslate();
        }
    }

    public function setSelf($request){
       
        if(array_key_exists ("type",$request)){
            $this->type=intval($request["type"]);
        }
        if(array_key_exists ("categories",$request)){
            $this->categories=Utils::ArrayToFiltredStringOfArray($request["categories"]);
        }
        if(array_key_exists ("role",$request)){
            $this->role=intval($request["role"]);
        }
        if(array_key_exists ("parent_id",$request)){
            try
            {
                $parent = ExtraPost::findOrFail(intval($request["parent_id"]));
                $parent_id=$parent->id;
            }
            catch(ModelNotFoundException $e)
            {
                $parent_id = null;
            }
            $this->parent_id=$parent_id;
        }
        if(array_key_exists ("order_in_parent",$request)){
            $this->order_in_parent=intval($request["order_in_parent"]);
            if($this->order_in_parent<1){
                $this->order_in_parent=null;
            }
        }
        if(array_key_exists ("contents_categories",$request)){
            $this->contents_categories=Utils::ArrayToFiltredStringOfArray($request["contents_categories"]);
        }
        if(array_key_exists ("content_manager",$request)){
            $this->content_manager=intval($request["content_manager"]);
        }
        if(array_key_exists ("content_S_column_count",$request)){
            $this->content_S_column_count=intval($request["content_S_column_count"]);
        }
        if(array_key_exists ("flash_vente",$request)){
            $this->flash_vente=intval($request["flash_vente"]);
        }
        if(array_key_exists ("content_M_column_count",$request)){
            $this->content_M_column_count=intval($request["content_M_column_count"]);
        }
        if(array_key_exists ("content_L_column_count",$request)){
            $this->content_L_column_count=intval($request["content_L_column_count"]);
        }
        if(array_key_exists ("content_XL_column_count",$request)){
            $this->content_XL_column_count=intval($request["content_XL_column_count"]);
        }
        if(array_key_exists ("image",$request)){
            $this->image=filter_var($request["image"], FILTER_SANITIZE_URL);
        }
        if(array_key_exists ("cover",$request)){
            $this->cover=filter_var($request["cover"], FILTER_SANITIZE_URL);
        }
        if(array_key_exists ("video",$request)){
            $this->video=filter_var($request["video"], FILTER_SANITIZE_URL);
        }
        if(array_key_exists ("rate",$request)){
            $this->rate=floatval($request["rate"]);
        }
        if(array_key_exists ("has_location",$request)){
            if($request["has_location"]===true || $request["has_location"]==="true"  || $request["has_location"]==1){
                $this->has_location=1;
            }else if($request["has_location"]===false || $request["has_location"]==="false"  || $request["has_location"]==0){
                $this->has_location=0;
            }
        }
        if(array_key_exists ("location",$request)){
            $this->location=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["location"]);
        }
        if(array_key_exists ("location_coordinates",$request)){
            if(!is_array($request["location_coordinates"])){
                $location_coordinates=explode(',',$request["location_coordinates"]);
            }else{
                $location_coordinates=$request["location_coordinates"];
            }
            $location_coordinates=array_filter(preg_replace("/[^.[:alnum:]]/u", '',$location_coordinates));
            if(count($location_coordinates)==2 || count($location_coordinates)==0){
                $this->location_coordinates=implode(',',$location_coordinates);
            }
        }
        if(array_key_exists ("phone",$request)){
            $this->phone=preg_replace(Utils::$preg_replace['phone'], '',$request["phone"]);
        }
        if(array_key_exists ("has_price",$request)){
            if($request["has_price"]===true || $request["has_price"]==="true"  || $request["has_price"]==1){
                $this->has_price=1;
            }else if($request["has_price"]===false || $request["has_price"]==="false"  || $request["has_price"]==0){
                $this->has_price=0;
            }
        }
        if(array_key_exists ("price",$request)){
            $this->price=floatval($request["price"]);
        }
        if(array_key_exists ("price_promo",$request)){
            $this->price_promo=floatval($request["price_promo"]);
        }
        if(array_key_exists ("col_span",$request)){
            $this->col_span=intval($request["col_span"]);
        }
        if(array_key_exists ("aspect_ratio",$request)){
            $this->aspect_ratio=preg_replace("/[^:.[:alnum:]]/u", '',$request["aspect_ratio"]);
        }
        if(array_key_exists ("aspect_preserve",$request)){
            $this->aspect_preserve=preg_replace(Utils::$preg_replace['paragraphe'], '',$request["aspect_preserve"]);
        }
        if(array_key_exists ("addition_data_1_icon",$request)){
            $this->addition_data_1_icon=filter_var($request["addition_data_1_icon"], FILTER_SANITIZE_URL);
        }
        if(array_key_exists ("is_accessible",$request)){
            if($request["is_accessible"]===true || $request["is_accessible"]==="true" || $request["is_accessible"]==1){
                $this->is_accessible=1;
            }else if($request["is_accessible"]===false || $request["is_accessible"]==="false"  || $request["is_accessible"]==0){
                $this->is_accessible=0;
            }
        }
        if(array_key_exists ("has_head",$request)){
            if($request["has_head"]===true || $request["has_head"]==="true"  || $request["has_head"]==1){
                $this->has_head=1;
            }else if($request["has_head"]===false || $request["has_head"]==="false"  || $request["has_head"]==0){
                $this->has_head=0;
            }
        }
        if(array_key_exists ("has_body",$request)){
            if($request["has_body"]===true || $request["has_body"]==="true"  || $request["has_body"]==1){
                $this->has_body=1;
            }else if($request["has_body"]===false || $request["has_body"]==="false"  || $request["has_body"]==0){
                $this->has_body=0;
            }
        }
        if(array_key_exists ("has_footer",$request)){
            if($request["has_footer"]===true || $request["has_footer"]==="true"  || $request["has_footer"]==1){
                $this->has_footer=1;
            }else if($request["has_footer"]===false || $request["has_footer"]==="false"  || $request["has_footer"]==0){
                $this->has_footer=0;
            }
        }
        if(array_key_exists ("has_opening_time",$request)){
            if($request["has_opening_time"]===true || $request["has_opening_time"]==="true"  || $request["has_opening_time"]==1){
                $this->has_opening_time=1;
            }else if($request["has_opening_time"]===false || $request["has_opening_time"]==="false"  || $request["has_opening_time"]==0){
                $this->has_opening_time=0;
            }
        }
        if(array_key_exists ("opening_time",$request)){
            $openTime= new OpenTimeWeek();
            $openTime->setSelf($request["opening_time"]);
            if($openTime->isValide()){
                $this->opening_time=$openTime->getJson();
            }
        }

        if(array_key_exists ("state",$request)){
            $this->state=intval($request["state"]);
        }
        if(array_key_exists ("start_time",$request)){
            $this->start_time=intval($request["start_time"]);
        }
        if(array_key_exists ("end_time",$request)){
            $this->end_time=intval($request["end_time"]);
        }
        if(array_key_exists ("start_publish",$request)){
            $this->start_publish=intval($request["start_publish"]);
        }
        if(array_key_exists ("end_publish",$request)){
            $this->end_publish=intval($request["end_publish"]);
        }
        /*if(array_key_exists ("nbr_views",$request)){
            $this->nbr_views=intval($request["nbr_views"]);
        }*/
        if(array_key_exists ("max_possible_views",$request)){
            $this->max_possible_views=intval($request["max_possible_views"]);
        }
        /*if(array_key_exists ("nbr_clicks",$request)){
            $this->nbr_clicks=intval($request["nbr_clicks"]);
        }*/
        if(array_key_exists ("max_possible_clicks",$request)){
            $this->max_possible_clicks=intval($request["max_possible_clicks"]);
        }
        if(array_key_exists ("expiration_type",$request)){
            $this->expiration_type=intval($request["expiration_type"]);
        }
        if(array_key_exists ("tripadvisor_id",$request)){
            $this->tripadvisor_id=intval($request["tripadvisor_id"]);
        }
    }
    
    public function setRate($newRate){

    }

    public function incrimentViews(){
        $this->nbr_views++;
        if($this->state==1 && $this->expiration_type==2 && $this->nbr_views>=$this->max_possible_views){
            $this->state=2;
        }
    }


    public function incrimentClicks(){
        $this->nbr_clicks++;
        if($this->state==1 && $this->expiration_type==3 && $this->nbr_clicks>=$this->max_possible_clicks){
            $this->state=2;
        }
    }

    public function isValide(){
        try
        {
            $postType = PostType::findOrFail($this->type);
            return $this->isValideByType($postType);
        }
        catch(ModelNotFoundException $e)
        {
            return false;
        }

    }
    public function isValideByType(PostType $postType){
        $validations=[];
        $requiredFieldsMunallyChecked=["type","hotel_id","parent_id","role","title","summery","description","addition_data_1_text"];
        if( $this->type!==null &&
            ($this->role==4 || $this->parent_id!==null)
        ){
            $requiredFields=array_filter(explode(',',$postType->required_colomns));
            $thisArray=$this->toArray();
            foreach($requiredFields as $requiredField){
                if(!in_array($requiredField,$requiredFieldsMunallyChecked) &&
                    !array_key_exists ($requiredField,$thisArray) || 
                    (
                        array_key_exists ($requiredField,$thisArray) && 
                        (
                            $thisArray[$requiredField]===null || 
                            $thisArray[$requiredField]==="null" || 
                            $thisArray[$requiredField]===""
                        )
                    )
                )
                {
                    array_push($validations,$requiredField);
                }
            }
            if(count($validations)==0){
                return true;
            }else{
                return $validations;
            }
            
        }
        else{
            if($this->type==null)array_push($validations,"type");
            if($this->role!=4 && $this->parent_id==null)array_push($validations,"role","parent_id");
            return $validations;

        }
    }


    public static function getEditableColumns(){
        $readOnlyColumns=array('id','created_at','updated_at');
        $requiredAlways=array('type','categories');

        $result=[];

        $table_info_columns  = (array)DB::select( DB::raw('SHOW FULL COLUMNS FROM '.app(ExtraPost::class)->getTable().''));
        $table_info_columns=json_decode(json_encode($table_info_columns), true);

        foreach($table_info_columns as $column){
            unset($column["Collation"],$column["Key"],$column["Extra"],$column["Privileges"]);
            if(!in_array($column['Field'],$readOnlyColumns)){
                if(in_array($column['Field'],$requiredAlways) || ($column['Null']=="NO" && $column['Default']===null)){
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
        $extraPost=new ExtraPost();

        if(array_key_exists('categories',$request_data) && $request_data["categories"]!=".*" && $request_data["categories"]!=null){
            $extraPost = $extraPost->where('categories','REGEXP', $request_data["categories"]);
        }

        if(array_key_exists('role',$request_data) && $request_data["role"]!=null){
            $extraPost = $extraPost->where('role', $request_data["role"]);
        }
        if(array_key_exists('flash_vente',$request_data) && $request_data["flash_vente"]!=null){
            $extraPost = $extraPost->where('flash_vente', $request_data["flash_vente"]);
        }
        
        if(array_key_exists('parent_id',$request_data)){
            if($request_data["parent_id"]>0){
                $extraPost = $extraPost->where('parent_id', $request_data["parent_id"]);
            }else{
                $extraPost = $extraPost->whereNull('parent_id');
            }
        }
        

        if(array_key_exists('order_in_parent',$request_data) && $request_data["order_in_parent"]!==null){
            $extraPost = $extraPost->where('order_in_parent', $request_data["order_in_parent"]);
        }

        if(array_key_exists('contents_categories',$request_data) && $request_data["contents_categories"]!=".*" && $request_data["contents_categories"]!=null){
            $extraPost = $extraPost->where('contents_categories','REGEXP', $request_data["contents_categories"]);
        }

        if(array_key_exists('content_manager',$request_data) && $request_data["content_manager"]!==null){
            $extraPost = $extraPost->where('content_manager', $request_data["content_manager"]);
        }
        
        if(array_key_exists('location',$request_data) && $request_data["location"]!=".*" && $request_data["location"]!=null){
            $extraPost = $extraPost->where('location','REGEXP', $request_data["location"]);
        }

        if(array_key_exists('rate',$request_data) && $request_data["rate"]['min']!=null){
            $extraPost = $extraPost->where('rate','>=', $request_data["rate"]['min']);
        }

        if(array_key_exists('rate',$request_data) && $request_data["rate"]['max']!=null){
            $extraPost = $extraPost->where('rate','<=', $request_data["rate"]['max']);
        }

        if(array_key_exists('price',$request_data) && $request_data["price"]['min']!=null){
            $extraPost = $extraPost->where('price','>=', $request_data["price"]['min']);
        }

        if(array_key_exists('price',$request_data) && $request_data["price"]['max']!=null){
            $extraPost = $extraPost->where('price','<=', $request_data["price"]['max']);
        }

        if(array_key_exists('state',$request_data) && $request_data["state"]!==null){
            $extraPost = $extraPost->where('state', $request_data["state"]);
        }

        if(array_key_exists('ids',$request_data) && $request_data["ids"]!==null){
            $post = $post->whereIn('id', $request_data["ids"]);
        }



        if(array_key_exists('orderby',$request_data) && $request_data["orderby"]!=null){
            if($request_data["nulls_last"]===true){
                $extraPost = $extraPost->orderByRaw("-".$request_data["orderby"]." DESC");
            }else{
                $extraPost = $extraPost->orderBy($request_data["orderby"],$request_data["orderby_direction"]);
            }
            
        }

        if(array_key_exists('paginate',$request_data) && $request_data["paginate"]!=null){
            $extraPost = $extraPost->paginate($request_data["paginate"]);
        }else{
            $extraPost = $extraPost->get();
        }
        
        return $extraPost;
    }

    public static function ExpiredPostChecker(){
        $updater=DB::table(app(ExtraPost::class)->getTable())->where('state', '<>',2)
                                                            ->where(function ($query) {
                                                                $query->where(function ($query) {$query->where('expiration_type', '=', 1)->Where('end_time', '<', time());})
                                                                        ->orWhere(function ($query) {$query->where('expiration_type', '=', 2)->Where('nbr_views', '>=', DB::raw('max_possible_views'));})
                                                                        ->orWhere(function ($query) {$query->where('expiration_type', '=', 3)->Where('nbr_clicks', '>=', DB::raw('max_possible_clicks'));});
                                                            })
                                                            ->update(['state' => 2]);
    }
}
