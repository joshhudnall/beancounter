<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class CounterController extends Controller
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
    public function getView($counterID)
    {
        $counter = \App\Models\Counter::where('id', $counterID)->first();
        
        return view('counter.view', ['counter' => $counter]);
    }
}
