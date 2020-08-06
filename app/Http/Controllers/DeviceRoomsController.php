<?php



namespace App\Http\Controllers;



use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Schema;





use App\Http\Requests;

use App\Http\Utils;

use App\DeviceRoom;

use App\Room;

use App\Http\Resources\DeviceRoomTemplate;



/**

 * @group DeviceRooms

 *

 * APIs for managing relation between devices and rooms

 */



class DeviceRoomsController extends LoggedController

{

  /**

   * Index 

   * Display a listing of device-room relations.

   * To filter device rooms, add any of the DeviceRoom object properties to the querry ?{property}={value}

   *

   * */

  public function index(Request $request)

  {

    $reqExecTimeId = 65;

    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {

      return response()->json([

        'message' => 'Forbidden'

      ], 403);

    }

    if (gettype($request->getContent()) == "object") {

      $request_data = json_decode($request->getContent(), true);

    } else {

      $request_data = $request->all();

    }

    $request_data = $this->extractRequestParams($request_data);

    $deviceRoom = DeviceRoom::Finder($request_data);

    $res = DeviceRoomTemplate::collection($deviceRoom);

    $this->workEnd($reqExecTimeId);

    return $res;

  }





  /**

   * Columns

   * Display the possible fields of DeviceRoom.

   * These fields can also be used to filter the search.

   * */

  public function create()

  {

    $reqExecTimeId = 64;

    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {

      return response()->json([

        'message' => 'Forbidden'

      ], 403);

    }

    $res = DeviceRoom::getEditableColumns();

    $this->workEnd($reqExecTimeId);

    return $res;

  }



  /**

   * Store

   * Create a new DeviceRoom.

   * */

  public function store(Request $request)

  {

    $reqExecTimeId = 67;

    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {

      return response()->json([

        'message' => 'Forbidden'

      ], 403);

    }

    if ($this->privileges["role"] == 2 && !in_array(intval(request("hotel_id")), $this->privileges["hotel_id"])) {

      return response()->json([

        'message' => 'Forbidden'

      ], 403);

    }

    $request_data = $request->all();

    $deviceRoom = new DeviceRoom;

    $deviceRoom->setSelf($request_data);

    $room = new Room();

    $room = $room->findOrFail($deviceRoom->room_id);

    if (!$room->status) {

      return response()->json([

        'error' => 'Room is disabled'

      ], 406);

    }

    if ($deviceRoom->isValide()) {

      $deviceRoom->save();

      $res = DeviceRoomTemplate::collection(collect([$deviceRoom]));

      $this->workEnd($reqExecTimeId);

      return $res;

    } else {

      $this->workEnd($reqExecTimeId);

      return [];

    }

  }



  /**

   * Show

   * Display the specified App by {id}.

   * */

  public function show($id)

  {

    $reqExecTimeId = 66;

    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {

      return response()->json([

        'message' => 'Forbidden'

      ], 403);

    }

    try {

      $deviceRoom = DeviceRoom::findOrFail($id);

      if ($this->privileges["role"] == 2 && !in_array($deviceRoom->hotel_id, $this->privileges["hotel_id"])) {

        return response()->json([

          'message' => 'Forbidden'

        ], 403);

      }

      $res = DeviceRoomTemplate::collection(collect([$deviceRoom]));

    } catch (ModelNotFoundException $e) {

      $res = response()->json([

        'Invalid_or_missing_fields' => "error"

      ], 500);

    }

    $this->workEnd($reqExecTimeId);

    return $res;

  }



  public function edit($id)

  {

    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {

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

  public function update(Request $request, $id)

  {

    $reqExecTimeId = 69;

    if ($this->privileges["role"] != 3 && $this->privileges['role'] !== 2) {

      return response()->json([

        'message' => 'Forbidden'

      ], 403);

    }

    try {

      $deviceRoom = DeviceRoom::findOrFail($id);

      $room = new Room();

      $room = $room->findOrFail($deviceRoom->room_id);

      if (!$room->status) {

        return response()->json([

          'error' => 'Room is disabled'

        ], 406);

      }

      

      $deviceRoom->setSelf($request->all());

      if ($deviceRoom->isValide()) {

        $deviceRoom->save();

      }

      $res = DeviceRoomTemplate::collection(collect([$deviceRoom]));

    } catch (ModelNotFoundException $e) {

      $res = response()->json([

        'error' => "Invalid_or_missing_fields"

      ], 500);

    }

    $this->workEnd($reqExecTimeId);

    return $res;

  }



  /**

   * Destroy

   * Remove a DeviceRoom.

   * */

  public function destroy($id)

  {

    $reqExecTimeId = 68;

    if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {

      return response()->json([

        'message' => 'Forbidden'

      ], 403);

    }

    try {

      $deviceRoom = DeviceRoom::findOrFail($id);

      if ($this->privileges["role"] == 2 && !in_array($deviceRoom->hotel_id, $this->privileges["hotel_id"])) {

        return response()->json([

          'message' => 'Forbidden'

        ], 403);

      }

      $deviceRoom->delete();

      $res = ['delele device room' . $id => 'success'];

    } catch (ModelNotFoundException $e) {

      $res = response()->json([

        'Invalid_or_missing_fields' => "error"

      ], 500);

    }

    $this->workEnd($reqExecTimeId);

    return $res;

  }



  public function extractRequestParams($request_data)

  {

    if (array_key_exists("reserved_time_from", $request_data)) {

      $request_data['reserved_time_from'] = Utils::SearchRang($request_data["reserved_time_from"]);

    } else {

      $request_data['reserved_time_from']['min'] = null;

      $request_data['reserved_time_from']['max'] = null;

    }



    if (array_key_exists("reserved_time_to", $request_data)) {

      $request_data['reserved_time_to'] = Utils::SearchRang($request_data["reserved_time_to"]);

    } else {

      $request_data['reserved_time_to']['min'] = null;

      $request_data['reserved_time_to']['max'] = null;

    }



    if (array_key_exists("join_time", $request_data)) {

      $request_data['join_time'] = Utils::SearchRang($request_data["join_time"]);

    } else {

      $request_data['join_time']['min'] = null;

      $request_data['join_time']['max'] = null;

    }



    if (array_key_exists("leave_time", $request_data)) {

      $request_data['leave_time'] = Utils::SearchRang($request_data["leave_time"]);

    } else {

      $request_data['leave_time']['min'] = null;

      $request_data['leave_time']['max'] = null;

    }

    return parent::extractRequestParams($request_data);

  }



  public function getAvailableDeviceRooms(Request $request) {

    $deviceRooms = DeviceRoom::distinct()->where('hotel_id', $request->hotel_id)->where('stay_id', null)->get();
  
    return DeviceRoomTemplate::collection(collect($deviceRooms));
    
    // $room = new RoomsController();
  
    // $availableDeviceRooms = array();
  
    // foreach ($deviceRooms as $deviceRoom) {
    //   if($room->isFull($deviceRoom->room_id) == 0)
    //     array_push($availableDeviceRooms, $deviceRoom);
    // }
  
    
  
  }



public function getByDeviceId(Request $request) {

    

  if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {

    return response()->json([

      'message' => 'Forbidden'

    ], 403);

  }



  $deviceRoom = DeviceRoom::where('device_id', $request->device_id)->get();



  return  DeviceRoomTemplate::collection($deviceRoom);



}



public function getByRoomId($id) {

  if ($this->privileges["role"] != 3 && $this->privileges["role"] != 2) {

    return response()->json([

      'message' => 'Forbidden'

    ], 403);

  }



  $deviceRoom = DeviceRoom::where('room_id', $id)->get();



  return DeviceRoomTemplate::collection($deviceRoom);

}





public function attach(Request $request) {

  $deviceRoom = new DeviceRoom;



  $deviceRoom->hotel_id = $request->hotel_id;



  $deviceRoom->room_id = $request->room_id;



  $deviceRoom->device_id = $request->device_id;



  $deviceRoom->stay_id = null;



  $deviceRoom->save();



  return new DeviceRoomTemplate($deviceRoom);



}





/**

 * Link a room to a spicified device (available or unavailable device)

 * 

 */

public function attachDeviceToRoom(Request $request) {

  

  $room = new RoomsController;



  $roomRequest = new Request;

  

  if ($request->linked_to_device == 0) {



    if ($request->list_type === 'unavailable') {



      $deviceRoom = $this->getByDeviceId($request);

      

      $this->destroy($deviceRoom[0]->id);

  

      $roomRequest = $roomRequest->replace(['linked_to_device' => 0]);

  

      $room->update($roomRequest, $deviceRoom[0]->room_id);

  

    }

    

    $this->attach($request);

  

    $roomRequest = $roomRequest->replace(['linked_to_device' => 1]);

  

    $room->update($roomRequest, $request->room_id);

  

    return 'Done';

  }



else {

  

  $roomRequest = $roomRequest->replace(['device_id' => $request->device_id]);



  if ($request->list_type === 'available') {

    

    $deviceRoom = $this->getByRoomId($request->room_id);



    return $this->update($roomRequest, $deviceRoom[0]->id);



    return 'Done';

  }



  else {

    $firstDeviceRoom = DeviceRoom::where('room_id', $request->room_id)->first();

    $secondDeviceRoom = DeviceRoom::where('device_id', $request->device_id)->first();



    $firstAux = $firstDeviceRoom->device_id;

    $secondAux = $secondDeviceRoom->device_id;

    

    $firstDeviceRoom->device_id = null;

    $firstDeviceRoom->save();



    $secondDeviceRoom->device_id = null;

    $secondDeviceRoom->save();



    

    $firstDeviceRoom->device_id =$secondAux;

    $firstDeviceRoom->save();

    $secondDeviceRoom->device_id = $firstAux; 

    $secondDeviceRoom->save();



    return 'Success to switch';



  }

}

}



}

