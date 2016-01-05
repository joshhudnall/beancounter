<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  protected $bodyClasses = [];
	
	public function __construct() {
  	// Set up some standard body classes for easy CSS targeting
  	$calledClass = strtolower(get_called_class());
  	$namespaces = str_ireplace('App\\Http\\Controllers\\', '', $calledClass);
  	$namespaces = explode('\\', $namespaces);
  	array_pop($namespaces);
  	
  	$this->bodyClasses += $namespaces;
  	$this->bodyClasses[] = str_ireplace('controller', '', class_basename($calledClass));
  	
  	$action = \Route::currentRouteAction();
  	$action = substr($action, strpos($action, "@") + 1);
  	$this->bodyClasses[] = \App\Helpers\Text::camelToDashes(str_ireplace(['get','post','put','delete'], '', $action));
  	
  	\View::share('bodyClass', implode(' ', $this->bodyClasses));

  	// The timezone that should be used when displaying information
  	$this->timezone = 'America/Denver';
  	\View::share('timezone', $this->timezone);
	}
	
	public function __destruct() {
	}
	
	protected function handleMediaUpload($request, $inputName) {
    if ($request->hasFile($inputName)) {
      $upload = $request->file($inputName);
      
      $folderName = date('Y/m');
      $dest = 'assets/uploads/'.$folderName.'/';
      if ( ! file_exists($dest)) {
        mkdir($dest, 0777, TRUE);
      }
      
      $extension = $upload->getClientOriginalExtension();
      $mime = $upload->getMimeType();
      $filename = md5(time().$upload->getClientOriginalName());
      
      $type = 'file';
      if ($mime == 'application/pdf') {
        $type = 'pdf';
      } else if (strpos($mime, 'image/') !== FALSE) {
        $type = 'image';
      }
      
      $status = $upload->move($dest, $filename.'_o.'.$extension);
      
      if ($status) {
        $media = new \App\Models\Media;
        $media->type = $type;
        $media->path = $dest;
        $media->name = $filename;
        $media->ext = $extension;
        $media->third_party_type = '';
        $media->third_party_thumbnail = '';
        $media->third_party_id = '';
        $media->save();
        
        if ($type == 'image') {
          // Add image to queue for processing
          \Queue::pushOn('media-fpassets-'.\App::environment(), new \App\Commands\ProcessImage($media));
        }
        
        return $media;
      }
    echo 'asdf';
    }
    
    return null;
	}

}
