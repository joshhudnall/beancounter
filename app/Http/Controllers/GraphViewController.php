<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class GraphViewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function getView()
    {
      $view = view('graph.view');
      
      $now = \Carbon\Carbon::now('America/Denver');
      
      $units = \App\Models\Counter::$units;
      
      $counters = \App\Models\Counter::whereIn('id', explode(',', \Request::get('counterIDs')))->get();
      
      if (\Request::has('interval')) {
        $interval = intval(\Request::get('interval'));
        $intervalUnit = substr(\Request::get('interval'), strlen($interval));
        if ( ! in_array($intervalUnit, \App\Models\Counter::$units)) $intervalUnit = 'w';
      } else {
        $interval = 1;
        $intervalUnit = 'w';
      }
      
      $rangeStart = \Request::has('start') ? \Carbon\Carbon::parse(\Request::get('start'), 'America/Denver') : $now->copy()->subDays(14);
      $rangeEnd = \Request::has('end') ? \Carbon\Carbon::parse(\Request::get('end'), 'America/Denver') : $now->copy();
      
      if ($rangeEnd > $now) $rangeEnd = $now->copy();
      if ($rangeEnd < $rangeStart) {
        $rangeStart = $rangeEnd->copy()->subDays(14);
      }
      
      $dataPoints = [];
      $currentInterval = $rangeStart->copy();
      foreach ($counters as $counter) {
        $dataPoints[$counter->name] = $counter->graphValues($rangeStart, $rangeEnd, $intervalUnit, $interval);
      }
//       dd($dataPoints);
      
      $labels = [];
      foreach (array_keys(array_first($dataPoints, function() {return true;})) as $k) {
        $labels[] = $k;
      }
      
      $colors = [
        'RGBA(220, 7, 35, {alpha})',
        'RGBA(54, 160, 177, {alpha})',
        'RGBA(32, 41, 70, {alpha})',
      ];
      
      $datasets = [];
      $colorIndex = 0;
      foreach ($dataPoints as $key => $point) {
        $dataset = [
          'label' => $key,
          
          'fillColor' => str_replace('{alpha}', '0.2', $colors[$colorIndex]),
          'strokeColor' => str_replace('{alpha}', '1', $colors[$colorIndex]),
          'pointColor' => str_replace('{alpha}', '1', $colors[$colorIndex]),
          'pointStrokeColor' => "#fff",
          'pointHighlightFill' => "#fff",
          'pointHighlightStroke' => "rgba(220,220,220,1)",

          'data' => [],
        ];
        foreach ($point as $value) {
          $dataset['data'][] = $value;
        }
        
        $datasets[] = $dataset;
        
        $colorIndex++;
      }
      
      $view->labels = $labels;
      $view->datasets = $datasets;
      
      return $view;
    }
}











