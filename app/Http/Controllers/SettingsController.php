<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class SettingsController extends Controller
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

    public function getApiKeys()
    {
        return view('settings.apiKeys.list', ['apiKeys' => \Auth::user()->apiKeys]);
    }

    public function getAddApiKey()
    {
        $apiKey = \App\Models\ApiKey::generate();
        return \Redirect::route('settings.apiKeys.list');
    }

    public function getDeactivateApiKey($key)
    {
        $apiKey = \App\Models\ApiKey::find($key);
        $apiKey->active = 0;
        $apiKey->save();
        
        return \Redirect::route('settings.apiKeys.list');
    }

}
