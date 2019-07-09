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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('email/verify/{id}', 'VerificationApiController@verify')->name('verificationapi.verify');
Route::get('email/resend', 'VerificationApiController@resend')->name('verificationapi.resend');

Route::prefix('v1')->group(function(){
    Route::post('login', 'AuthController@login')->name('login');;
    Route::post('register', 'AuthController@register');

    Route::group(['middleware' => 'auth:api'], function(){
    Route::post('getUser', 'AuthController@getUser');
    Route::post('logout', 'AuthController@logout');
    });
   });

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact Backend team'], 404);
});
