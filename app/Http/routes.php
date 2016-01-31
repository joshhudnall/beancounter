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

Route::get('count', [
  'as' => 'count',
  'uses' => 'CountController@getCount'
]);

Route::group(['prefix' => 'api/v1'], function () {
  Route::get('stats/{statName}', [
    'as' => 'api.stats.value',
    'uses' => 'API\StatsController@getValue'
  ]);
  Route::get('stats/{statName}/sb-counter', [
    'as' => 'api.stats.sbCounter',
    'uses' => 'API\StatsController@getStatusBoardCounter'
  ]);
  Route::get('stats/{statName}/sb-graph', [
    'as' => 'api.stats.sbGraph',
    'uses' => 'API\StatsController@getStatusBoardGraph'
  ]);
});

Route::group(['middleware' => 'web'], function () {
    
    Route::auth();

    Route::get('/', [
      'as' => 'home',
      'uses' => 'HomeController@getIndex'
    ]);

    Route::get('counter/{id}', [
      'as' => 'counter.view',
      'uses' => 'CounterController@getView'
    ]);

    Route::get('settings/api-keys', [
      'as' => 'settings.apiKeys.list',
      'uses' => 'SettingsController@getApiKeys'
    ]);
    Route::get('settings/api-keys/add', [
      'as' => 'settings.apiKeys.add',
      'uses' => 'SettingsController@getAddApiKey'
    ]);
    Route::get('settings/api-keys/{apiKey}/deactivate', [
      'as' => 'settings.apiKeys.deactivate',
      'uses' => 'SettingsController@getDeactivateApiKey'
    ]);
    
});
