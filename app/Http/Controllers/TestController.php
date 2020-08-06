<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\ExtraPost;
use App\Post;
use App\PostsTranslate;
use App\Http\Resources\HotelTemplate;

class TestController extends LoggedController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function test(Request $request)
    {   
        $reqExecTimeId=102;
        
        
        /*$postController=new PostController();
        $postController->cloneHotelPosts(31,35,"hotel of ahmed");*/

        $this->workEnd($reqExecTimeId);
        if(isset($res))
        return $res; 
    }

}
