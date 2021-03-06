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
    
    public function getStatusBoardGraph(\Illuminate\Http\Request $request, $statName)
    {
      // Validate API Key
      $apiKey = \App\Models\ApiKey::where('api_key', $request->get('apiKey'))->where('active', 1)->first();
      if ( ! $apiKey) {
        return \Response::json(['status' => 'Error', 'error' => 'Invalid API Key.'], 401);
      }
      
      $user = $apiKey->user;
      
      $counter = $user->counters()->where('name', $statName)->first();
      if ( ! $counter) {
          return \Response::json(['status' => 'Error', 'error' => 'Counter not found'], 422);
      }
      
      // Get last 6 months' data
      $date = \Carbon\Carbon::now()->subMonths(6);
      $datapoints = [];
      for ($i = 0; $i < 6; $i++) {
        if ($i == 5) {
          $label = 'Now';
        } else {
          $label = '-'.(5-$i).' Mo';
        }
        
        $query = $counter->beans()->where('created_at', '<=', $date->addMonths(1));
        
        if ($counter->type == \App\Models\Counter::CounterTypeCount) {
          $value = $query->sum('value');
        } else {
          $value = $query->avg('value');
        }
        
        $datapoints[] = [
          'title' => $label,
          'value' => 1 * $value ?: 0,
        ];
      }
      
      $jsonData = [
        'graph' => [
          'title' => $counter->name,
          'type' => 'line',
          'datasequences' => [
            [
              'title' => $counter->name,
              'datapoints' => $datapoints,
            ],
          ],
        ],
      ];
      
      ob_clean();
      return \Response::json($jsonData, 200);
    }
}



