<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
  Route::post('auth/login', 'AuthController@login')->middleware(['cors']);
  Route::post('auth/refresh', 'AuthController@refresh')->middleware(['cors']);
  Route::get('dati_app_version', 'PublicController@datiAppVersions')->middleware(['cors']);
  Route::get('dati_last_version', 'PublicController@datiLastVersions')->middleware(['cors']);
  Route::post('/password/request', 'PublicController@requestResetLink')->middleware(['cors']);
  Route::post('/password/reset', 'PublicController@setupNewPassword')->middleware(['cors']);
Route::get('/file/{folder}/{link}', function ($folder, $link) {
  return response()->file(asset('storage/' . $folder . '/' . $link));
});

Route::options('/{any}', function () {
  return '';
})->where('any', '.*')->middleware(['cors']);

Route::group([
  'middleware' => ['auth:api', 'cors']
], function () {
  //auth
  Route::get('auth/logout', 'AuthController@logout');
  Route::get('auth/user', 'AuthController@user');
  Route::get('auth/privileges', 'AuthController@privileges');

  // users routing
  Route::get('/user/columns/', 'AdminController@create');
  Route::get('/users', 'AdminController@index');
  Route::get('/user/{id}', 'AdminController@show');
  Route::post('/user', 'AdminController@store');
  Route::put('/user/{id}', 'AdminController@update');
  Route::delete('/user/{id}', 'AdminController@destroy');
  Route::get('/user/permissions/{id}', 'AdminController@getPermissions');
  
  // device routing
  Route::get('/device/columns/', 'DeviceController@create');
  Route::get('/devices', 'DeviceController@index');
  Route::get('/devices/contacts', 'DeviceController@index_contacts');
  Route::get('/device/{id}', 'DeviceController@show');
  Route::get('/devices/find', 'DeviceController@find');
  Route::post('/device', 'DeviceController@store');
  Route::put('/device/{id}', 'DeviceController@update');
  Route::put('/deviceRoomSwitch/{id}', 'DeviceController@switchRoom');
  Route::put('/deviceTouristSwitch/{id}', 'DeviceController@switchTourist');
  Route::delete('/device/{id}', 'DeviceController@destroy');
  Route::get('/devices/available', 'DeviceController@getAvailableDevices');
  Route::get('/devices/unavailable', 'DeviceController@getUnAvailableDevices');
  Route::get('/devices/imei/', 'DeviceController@getByImei');
  Route::post('/device/cleardata', 'DeviceController@clearData');
  

  // post routing
  Route::get('/post/columns/', 'PostController@create');
  Route::get('/posts', 'PostController@index');
  Route::get('/post/{id}', 'PostController@show');
  Route::post('/post', 'PostController@store');
  Route::put('/post/{id}', 'PostController@update');
  Route::put('/posts', 'PostController@MultiUpdate');
  Route::put('/post/{id}/seen', 'PostController@seen');
  Route::get('/posts/top/orders', 'PostController@topFiveOrders');
  Route::get('/posts/top/reservations', 'PostController@topFiveReservations');
  Route::get('/posts/translates/{id}', 'PostController@getTranslates');
  Route::delete('/post/{id}', 'PostController@destroy');

  // delivery routing
  Route::get('/delivery_place/columns/', 'DeliveryController@create');
  Route::get('/delivery_places', 'DeliveryController@index');
  Route::get('/delivery_place/{id}', 'DeliveryController@show');
  Route::post('/delivery_place', 'DeliveryController@store');
  Route::put('/delivery_place/{id}', 'DeliveryController@update');
  Route::delete('/delivery_place/{id}', 'DeliveryController@destroy');
  Route::get('/delivery_place', 'DeliveryController@getDeliveryPlaceById');

  // log routing
  Route::get('/log/columns/', 'LogController@create');
  Route::get('/logs', 'LogController@index');

  //publicity routing
  Route::get('/publicity/columns/', 'PublicityController@create');
  Route::get('/publicities', 'PublicityController@index');
  Route::get('/publicity/{id}', 'PublicityController@show');
  Route::post('/publicity', 'PublicityController@store');
  Route::put('/publicity/{id}', 'PublicityController@update');
  Route::delete('/publicity/{id}', 'PublicityController@destroy');
  Route::post('/publicity/click/{id}', 'PublicityController@click');
  Route::post('/publicity/view/{id}', 'PublicityController@view');

  // notification routing
  Route::get('/notification/columns/', 'NotificationController@create');
  Route::get('/notifications', 'NotificationController@index');
  Route::get('/notification/{id}', 'NotificationController@show');
  Route::post('/notification', 'NotificationController@store');
  Route::post('/notification/view/{id}', 'NotificationController@view');
  Route::put('/notification/{id}', 'NotificationController@update');
  Route::delete('/notification/{id}', 'NotificationController@destroy');

  // post translate routing
  Route::get('/posts_translate/columns/', 'PostsTranslatesController@create');
  Route::get('/posts_translates', 'PostsTranslatesController@index');
  Route::get('/posts_translate/{id}', 'PostsTranslatesController@show');
  Route::post('/posts_translate', 'PostsTranslatesController@store');
  Route::put('/posts_translate/{id}', 'PostsTranslatesController@update');
  Route::delete('/posts_translate/{id}', 'PostsTranslatesController@destroy');

  // extra post routing
  Route::get('/extra_post/columns/', 'ExtraPostsController@create');
  Route::get('/extra_posts', 'ExtraPostsController@index');
  Route::get('/extra_post/{id}', 'ExtraPostsController@show');
  Route::post('/extra_post', 'ExtraPostsController@store');
  Route::put('/extra_post/{id}', 'ExtraPostsController@update');
  Route::put('/extra_posts', 'ExtraPostsController@MultiUpdate');
  Route::delete('/extra_post/{id}', 'ExtraPostsController@destroy');
  //Route::put('/extra_post/{id}/rate','ExtraPostsController@rate');
  Route::put('/extra_post/{id}/seen', 'ExtraPostsController@seen');
  Route::put('/extra_post/{id}/click', 'ExtraPostsController@click');

  // extra post translate routing
  Route::get('/extra_posts_translate/columns/', 'ExtraPostsTranslatesController@create');
  Route::get('/extra_posts_translates', 'ExtraPostsTranslatesController@index');
  Route::get('/extra_posts_translate/{id}', 'ExtraPostsTranslatesController@show');
  Route::post('/extra_posts_translate', 'ExtraPostsTranslatesController@store');
  Route::put('/extra_posts_translate/{id}', 'ExtraPostsTranslatesController@update');
  Route::delete('/extra_posts_translate/{id}', 'ExtraPostsTranslatesController@destroy');

  // post type routing
  Route::get('/post_type/columns/', 'PostTypesController@create');
  Route::get('/post_types', 'PostTypesController@index');
  Route::get('/post_type/{id}', 'PostTypesController@show');
  Route::post('/post_type', 'PostTypesController@store');
  Route::put('/post_type/{id}', 'PostTypesController@update');
  Route::delete('/post_type/{id}', 'PostTypesController@destroy');

  // Hotels routing
  Route::get('/hotel/columns/', 'HotelsController@create');
  Route::get('/hotels', 'HotelsController@index');
  Route::get('/hotel/{id}', 'HotelsController@show');
  Route::post('/hotel', 'HotelsController@store');
  Route::put('/hotel/{id}', 'HotelsController@update');
  Route::delete('/hotel/{id}', 'HotelsController@destroy');

  // Rooms routing
  Route::get('/room/columns/', 'RoomsController@create');
  Route::get('/rooms', 'RoomsController@index');
  Route::get('/room/{id}', 'RoomsController@show');
  Route::get('/rooms/find', 'RoomsController@find');
  Route::post('/room/toggle/{id}', 'RoomsController@toggle');
  Route::post('/room', 'RoomsController@store');
  Route::put('/room/{id}', 'RoomsController@update');
  Route::delete('/room/{id}', 'RoomsController@destroy');
  Route::get('rooms/available/', 'RoomsController@getAvailableRooms');
  Route::get('rooms/room_number/', 'RoomsController@getRoomByRoomNumber');
  Route::get('rooms/{id}/unlink', 'RoomsController@unlink');
  

  // device_room routing
  Route::get('/device_room/columns/', 'DeviceRoomsController@create');
  Route::get('/device_rooms', 'DeviceRoomsController@index');
  Route::get('/device_room/{id}', 'DeviceRoomsController@show');
  Route::post('/device_room', 'DeviceRoomsController@store');
  Route::put('/device_room/{id}', 'DeviceRoomsController@update');
  Route::delete('/device_room/{id}', 'DeviceRoomsController@destroy');
  Route::post('/device_room/attach', 'DeviceRoomsController@attachDeviceToRoom');
  Route::get('/device_rooms/available', 'DeviceRoomsController@getAvailableDeviceRooms');

  
  // Tourist routing
  Route::get('/tourist/columns/', 'TouristsController@create');
  Route::get('/tourists', 'TouristsController@index');
  Route::get('/tourist/{id}', 'TouristsController@show');
  Route::get('/tourists/find', 'TouristsController@find');
  Route::post('/tourist', 'TouristsController@store');
  Route::put('/tourist/{id}', 'TouristsController@update');
  Route::post('/tourist/{id}', 'TouristsController@update');
  Route::get('/tourists/get', 'TouristsController@index');
  Route::delete('/tourist/{id}', 'TouristsController@destroy');

  // room renting routing
  Route::get('/stay/columns/', 'StaysController@create');
  Route::get('/stays', 'StaysController@index');
  Route::get('/stay/{id}', 'StaysController@show');
  Route::post('/stay', 'StaysController@store');
  //Route::put('/stay/{id}','StaysController@update');
  Route::delete('/stay/{id}', 'StaysController@destroy');
  Route::put('/stays/switch/', 'StaysController@switchRoom');

  // shopping orders routing
  Route::get('/shopping_order/columns/', 'ShopOrdersController@create');
  Route::get('/shopping_orders', 'ShopOrdersController@index');
  Route::get('/shopping_order/{id}', 'ShopOrdersController@show');
  Route::post('/shopping_order', 'ShopOrdersController@store');
  Route::put('/shopping_order/{id}', 'ShopOrdersController@update');
  Route::post('/shopping_order/accept/{id}', 'ShopOrdersController@accept');
  Route::post('/shopping_order/accept/dashboard/{id}', 'ShopOrdersController@acceptFromDashboard');
  Route::post('/shopping_order/deny/{id}', 'ShopOrdersController@deny');
  Route::post('/shopping_order/ready/{id}', 'ShopOrdersController@ready');
  Route::post('/shopping_order/delivered/{id}', 'ShopOrdersController@delivered');
  Route::post('/shopping_order/confirmed/{id}', 'ShopOrdersController@confirmed');
  Route::post('/shopping_order/confirm_all', 'ShopOrdersController@confirmAll');
  Route::delete('/shopping_order/{id}', 'ShopOrdersController@destroy');
  Route::get('/shopping/orders', 'ShopOrdersController@getShoppingOrders');
  Route::post('/shopping/seen', 'ShopOrdersController@seen');

  // apps routing
  Route::get('/app/columns/', 'AppsController@create');
  Route::get('/apps', 'AppsController@index');
  Route::get('/app/{id}', 'AppsController@show');
  Route::post('/app', 'AppsController@store');
  Route::put('/app/{id}', 'AppsController@update');
  Route::delete('/app/{id}', 'AppsController@destroy');

  // permissions routing
  Route::get('/permission/columns/', 'PermissionController@create');
  Route::get('/permissions', 'PermissionController@index');
  Route::get('/permission_options', 'PermissionController@options');
  Route::get('/permission/{id}', 'PermissionController@show');
  Route::post('/permission', 'PermissionController@store');
  Route::put('/permission/{id}', 'PermissionController@update');
  Route::delete('/permission/{id}', 'PermissionController@destroy');

  // permission_groups routing
  Route::get('/permission_group/columns/', 'PermissionGroupController@create');
  Route::get('/permission_groups', 'PermissionGroupController@index');
  Route::get('/permission_group/{id}', 'PermissionGroupController@show');
  Route::post('/permission_group', 'PermissionGroupController@store');
  Route::put('/permission_group/{id}', 'PermissionGroupController@update');
  Route::delete('/permission_group/{id}', 'PermissionGroupController@destroy');

  // apps versions routing
  Route::get('/app_version/columns/', 'AppVersionsController@create');
  Route::get('/app_versions', 'AppVersionsController@index');
  Route::get('/app_version/{id}', 'AppVersionsController@show');
  Route::post('/app_version', 'AppVersionsController@store');
  Route::put('/app_version/{id}', 'AppVersionsController@update');
  Route::delete('/app_version/{id}', 'AppVersionsController@destroy');
  Route::post('/app_version/apk', 'AppVersionsController@sendApk');

  Route::get('/notif_structure', 'NotifyDevicesController@create');
  Route::post('/notif_hotels_devices', 'NotifyDevicesController@notifyHotelsDevices');

  // post review routing
  Route::get('/post_review/columns/', 'PostReviewController@create');
  Route::get('/post_reviews', 'PostReviewController@index');
  Route::get('/post_review/{id}', 'PostReviewController@show');
  Route::post('/post_review', 'PostReviewController@store');
  Route::put('/post_review/{id}', 'PostReviewController@update');
  Route::delete('/post_review/{id}', 'PostReviewController@destroy');

  Route::get('/push_notifications', 'NotifyDevicesController@pushNotifications');
  Route::get('/push_notification/{id}', 'NotifyDevicesController@pushNotification');
  Route::post('/push_notification/view/{id}', 'NotifyDevicesController@pushNotificationsSeen');

  //file upload
  Route::post('upload', function (Request $request) {
    
    if ($request->file('image')) {
      $path = Storage::disk('public_uploads')->putFile('tourist_images', $request->file('image'));
      return response()->json(['image' => asset('uploads/' . $path)]);
    }
  });

  //Sidebar access
  Route::get('/sideaccess/{id}', 'SideBarAccessController@getAccess');
  //Posts access
  Route::get('/postaccess/{id}', 'AccessPostController@getAccess');
  Route::get('/user/access/{id}', 'AdminController@getPostAccess');

  //Demands routes
  Route::get('/demands', 'DemandsController@index');
  Route::post('/demands', 'DemandsController@store');
  Route::get('/demands/{id}', 'DemandsController@show');
  Route::put('/demands/{id}', 'DemandsController@update');

});
