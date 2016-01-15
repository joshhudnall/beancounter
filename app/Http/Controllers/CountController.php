<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class CountController extends Controller
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
    public function getCount(\Illuminate\Http\Request $request)
    {
      // Validate API Key
      $apiKey = \App\Models\ApiKey::where('api_key', $request->get('apiKey'))->where('active', 1)->first();
      if ( ! $apiKey) {
        return \Response::json(['status' => 'Error', 'error' => 'Invalid API Key.'], 401);
      }
      
      // Validate Data
      if ( ! $request->has('name')) {
        return \Response::json(['status' => 'Error', 'error' => 'The name field is required.'], 400);
      }
      if ( ! $request->has('count') && ! $request->has('value')) {
        return \Response::json(['status' => 'Error', 'error' => 'Either the count or value field is required.'], 400);
      }
      
      $user = $apiKey->user;
      
      $counter = $user->counters()->where('name', $request->get('name'))->first();
      if ( ! $counter) {
        $counter = \App\Models\Counter::create([
          'user_id' => $user->id,
          'name' => $request->get('name'),
          'type' => $request->has('count') ? \App\Models\Counter::CounterTypeCount : \App\Models\Counter::CounterTypeValue,
        ]);
      } else {
        if ($counter->type == \App\Models\Counter::CounterTypeCount && ! $request->has('count')) {
          return \Response::json(['status' => 'Error', 'error' => 'Stat "'.$request->get('name').'" already exists and is a counter stat, but the "count" field was not included.'], 422);
        }
        if ($counter->type == \App\Models\Counter::CounterTypeValue && ! $request->has('value')) {
          return \Response::json(['status' => 'Error', 'error' => 'Stat "'.$request->get('name').'" already exists and is a value stat, but the "value" field was not included.'], 422);
        }
      }
      
      $bean = \App\Models\Bean::create([
        'counter_id' => $counter->id,
        'value' => $counter->type == \App\Models\Counter::CounterTypeCount ? $request->get('count') : $request->get('value'),
      ]);
      
      if ($request->has('timestamp')) {
        $bean->created_at = \Carbon\Carbon::createFromTimestampUTC($request->get('timestamp'));
        $bean->save();
      }
      
      return \Response::json(['status' => 'Success'], 200);
    }
}



