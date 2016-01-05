<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/', [
      'as' => 'home',
      function() {
        return \Redirect::route('assets.list');
      }
    ]);
    
    Route::get('asset', [
      'as' => 'assets.list',
      'uses' => 'AssetsController@getIndex'
    ]);
    Route::get('asset/download/{assetID}', [
      'as' => 'assets.download',
      'uses' => 'AssetsController@getDownload'
    ]);
    Route::get('asset/upload', [
      'as' => 'assets.upload',
      'uses' => 'AssetsController@getUpload'
    ]);
    Route::post('asset/upload', 'AssetsController@postUpload');
});
