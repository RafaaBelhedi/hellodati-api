<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenTimeWeek extends Model
{
    private $valide=false;
    private $openTime=null;

    public function setSelf($request){
        if($request!=null && gettype($request)=="string"){
            $openTime=json_decode($request,true);
            if($openTime!=null && gettype($openTime)=="array" && count($openTime)==7){
                $this->openTime=$openTime;
            }
        }else if($request!=null && gettype($request)=="array" && count($request)==7){
            $this->openTime=$request;
        }

    }

    public function isValide(){
        if($this->openTime==null){return false;}
        else{
            foreach($this->openTime as $day){
                if(gettype($day)!="array"){return false;}
                else if(count($day)==0){return false;}
                else{
                    foreach($day as $shift){
                        if(gettype($shift)!="array"){return false;}
                        else if(count($shift)>3 || count($shift)==0){return false;}
                        else if($shift[0]!=1 && $shift[0]!=0){return false;}
                        else if($shift[0]==1 && count($shift)!=3){return false;}
                        else if($shift[0]==1){
                            for($i=1;$i<3;$i++){
                                $time=explode(':',$shift[$i]);
                                if(count($time)!=2){return false;}
                                else{
                                    if(preg_replace('/\D/', '', $time[0])!=$time[0]){return false;}
                                    if(preg_replace('/\D/', '', $time[1])!=$time[1]){return false;}
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    public function getJson(){
        return json_encode($this->openTime);
    }
}
