<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      // Enable CORS
      $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
      header("Access-Control-Allow-Origin: $http_origin");
      header('Access-Control-Allow-Credentials: true');
      
      if (\Request::getMethod() == "OPTIONS") {
        // The client-side application can set only headers allowed in Access-Control-Allow-Headers
        $headers = [
          'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
          'Access-Control-Allow-Headers'=> 'Origin,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Authorization,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,User-Agent,Referer,Accept,Authorization'
        ];
        return \Response::make('You are connected to the API', 200, $headers);
      }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
