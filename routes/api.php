<?php

use Illuminate\Http\Request;

//use Illuminate\Support\Facades\Route;

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

Route::prefix('/v1/')->group(function () {
    Route::post('login', 'api\ApiController@login');
    Route::post('dashboard', 'api\ApiController@dashboard');
    Route::post('forgot_password', 'api\ApiController@forgot_password');
    Route::post('profile_update', 'api\ApiController@profile_update');
    Route::post('registration', 'api\ApiController@registration');
    Route::post('otp_verification', 'api\ApiController@otp_verification');
    Route::post('admin_ambassador_approval', 'api\ApiController@admin_ambassador_approval');
    Route::post('ambassador_document_upload', 'api\ApiController@ambassador_document_upload');
    Route::post('admin_view_document', 'api\ApiController@admin_view_document');
    Route::post('admin_document_approval', 'api\ApiController@admin_document_approval');
    Route::post('reset_password', 'api\ApiController@reset_password');
    
    
});
