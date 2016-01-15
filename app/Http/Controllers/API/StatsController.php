<?php

namespace App\Http\Controllers\API;

use App\Http\Requests;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function getValue(\Illuminate\Http\Request $request, $statName)
    {
      // Validate API Key
      $apiKey = \App\Models\ApiKey::where('api_key', $request->get('apiKey'))->where('active', 1)->first();
      if ( ! $apiKey) {
        return \Response::json(['status' => 'Error', 'error' => 'Invalid API Key.'], 401);
      }
      
      $user = $apiKey->user;
      
      $counter = $user->counters()->where('name', $statName)->first();
      if ( ! $counter) {
          return \Response::json(['status' => 'Error', 'error' => 'Counter not found'], 404);
      }
      
      return \Response::json(['status' => 'Success', 'counter' => $counter], 200);
    }
    
    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function getStatusBoardCounter(\Illuminate\Http\Request $request, $statName)
    {
      // Validate API Key
      $apiKey = \App\Models\ApiKey::where('api_key', $request->get('apiKey'))->where('active', 1)->first();
      if ( ! $apiKey) {
        return \Response::json(['status' => 'Error', 'error' => 'Invalid API Key.'], 401);
      }
      
      $user = $apiKey->user;
      
      $counter = $user->counters()->where('name', $statName)->first();
      if ( ! $counter) {
          return \Response::json(['status' => 'Error', 'error' => 'Counter not found'], 404);
      }
      
      return view('api.statusboard.counter', ['counter' => $counter]);
    }
}



