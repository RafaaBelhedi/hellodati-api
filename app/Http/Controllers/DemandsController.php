<?php


namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Demand;
use App\Room;
use App\Http\Resources\DemandResource;

class DemandsController extends LoggedController
{
  public function index() {
    $demands = Demand::all();
    return DemandResource::collection($demands);
  }

  public function store(Request $request) {
    $newDemand = new Demand();
    $newDemand->type = $request->type;
    $newDemand->status = $request->status;
    $newDemand->comment = $request->comment;
    $newDemand->room_id = $request->room_id;
    $newDemand->save();
    return response()->json('New demand has been created successfully');
  }

  public function show($id) {
    $demand = Demand::find($id);
    return DemandResource::collection(collect([$demand]));
  }

  public function update(Request $request, $id) {
    $demand = Demand::find($id);
    $demand->status = $request->status;
    $demand->save();
    return response()->json('The specified demand has been updated successfully');
  }

  
}
