<?php

namespace App\Http\Controllers;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Utils;
use App\ExtraPostsTranslate;
use App\PostType;
use App\Http\Resources\ExtraPostsTranslatesTemplate;

/**
 * @group ExtraPostsTranslates
 *
 * APIs for managing translations of posts outside hotels
 */

class ExtraPostsTranslatesController extends LoggedController
{
    /**
     * Index
     * Display a listing of Extra posts translates.
     * To filter Extra posts translates, add any of the ExtraPostTranslate object properties to the querry ?{property}={value}
     *
     * */
    public function index(Request $request)
    {
        $reqExecTimeId=41;
        if($this->privileges["role"]!=3){
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        if(gettype($request->getContent())=="object"){
            $request_data=json_decode($request->getContent(),true);   
        }else{
            $request_data=$request->all();
        }
        $request_data=$this->extractRequestParams($request_data);
        
        $extraExtraPostsTranslate=ExtraPostsTranslate::Finder($request_data);
        
        $res = ExtraPostsTranslatesTemplate::collection($extraExtraPostsTranslate);
        $this->workEnd($reqExecTimeId);
        return $res;
    }


    /**
     * Columns
     * Display the possible fields of ExtraPostsTranslate.
     * These fields can also be used to filter the search.
     * */
    public function create()
    {
        $reqExecTimeId=40;
        if($this->privileges["role"]!=3  && $this->privileges["role"]!=2){
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        $res = ExtraPostsTranslate::getEditableColumns();
        $this->workEnd($reqExecTimeId);
        return $res;
    }

    /**
     * Store
     * Create a new ExtraPostsTranslate.
     * */
    public function store(Request $request)
    {
        $reqExecTimeId=43;
        if($this->privileges["role"]!=3){
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        $extraExtraPostsTranslate=new ExtraPostsTranslate;
        $extraExtraPostsTranslate->setSelf($request->all());



        try
        {
            $postType = PostType::findOrFail($extraExtraPostsTranslate->post->type);
            $validations = $post->isValideByType($postType);
        }
        catch(ModelNotFoundException $e)
        {
            $postType=null;
            $validations=false;
        }

        if($validations===true){
            $extraExtraPostsTranslate->save();
            $res= ExtraPostsTranslatesTemplate::collection(collect([$extraExtraPostsTranslate]));
        }else{
            if(is_array($validations)){
                $res = response()->json([
                    'Invalid_or_missing_fields' => $validations
                ], 500);
            }else{
                $res = response()->json([
                    'message' => 'invalide entrie(s)'
                ], 500);
            }
        } 
        $this->workEnd($reqExecTimeId);
        return $res;
    }

    /**
     * Show
     * Display the specified App by {id}.
     * */
    public function show($id)
    {
        $reqExecTimeId=42;
        if($this->privileges["role"]!=3){
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        try
        {
            $extraExtraPostsTranslate = ExtraPostsTranslate::findOrFail($id);
            $res = ExtraPostsTranslatesTemplate::collection(collect([$extraExtraPostsTranslate]));
        }
        catch(ModelNotFoundException $e)
        {
            $res = response()->json([
                'error' => "Invalid_or_missing_fields"
            ], 500);
        }
        $this->workEnd($reqExecTimeId);
        return $res;

        
    }

    public function edit($id)
    {
        if($this->privileges["role"]!=3){
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        //
    }

    /**
     * Update
     * Edit properties of existing DeviceRoom.
     * */
    public function update(Request $request,$id)
    {
        $reqExecTimeId=44;
        if($this->privileges["role"]!=3){
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        try
        {
            $extraExtraPostsTranslate = ExtraPostsTranslate::findOrFail($id);
            $extraExtraPostsTranslate->setSelf($request->all());
            try
            {
                $postType = PostType::findOrFail($extraExtraPostsTranslate->post->type);
                $validations = $extraExtraPostsTranslate->isValideByType($postType);
            }
            catch(ModelNotFoundException $e)
            {
                $postType=null;
                $validations=false;
            }

            if($validations===true){
                $extraExtraPostsTranslate->save();
                $res= ExtraPostsTranslatesTemplate::collection(collect([$extraExtraPostsTranslate]));
            }else{
                if(is_array($validations)){
                    $res = response()->json([
                        'Invalid_or_missing_fields' => $validations
                    ], 500);
                }else{
                    $res = response()->json([
                        'message' => 'invalide entrie(s)'
                    ], 500);
                }
            } 
        }
        catch(ModelNotFoundException $e)
        {
            $res = response()->json([
                'message' => 'invalide entrie(s)'
            ], 500);
        }
        $this->workEnd($reqExecTimeId);
        return $res;
    }

    /**
     * Destroy
     * Remove an DeviceRoom.
     * */
    public function destroy($id)
    {
        $reqExecTimeId=45;
        if($this->privileges["role"]!=3){
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        try
        {
            $extraExtraPostsTranslate = ExtraPostsTranslate::findOrFail($id);
            $extraExtraPostsTranslate->delete();
            $res=  ['delele DeviceRoom '.$id=>'success'];
        }
        catch(ModelNotFoundException $e)
        {
            $res = response()->json([
                'error' => "Invalid_or_missing_fields"
            ], 500);
        }
        $this->workEnd($reqExecTimeId);
        return $res;
    }
}
