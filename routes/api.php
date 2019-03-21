<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('registerApi', 'Api\RegisterController@register');
Route::get('test', 'Api\RegisterController@test');

Route::group(['prefix' => 'user', 'middleware' => 'multiauth:api'], function() 
	{
		Route::get('userInfoApi','Api\UserController@getUserInfo');
		Route::get('parkingApi','Api\UserController@getParkingArea');
		Route::post('scanApi','Api\UserController@scan');
	}
);