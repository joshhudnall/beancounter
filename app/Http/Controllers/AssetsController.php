<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class AssetsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      parent::__construct();
      $this->middleware('auth');
    }

    public function getIndex()
    {
      $view = view('assets.list');
      
    	if (\Request::has('tags')) {
      	$tags = explode(',', \Request::get('tags'));
      	array_walk($tags, function(&$value, $key) {
        	$value = trim($value);
      	});
      	
      	// TODO: Make search tags as AND instead of OR
      	$assetIDs = \App\Models\Tag::select('media.id')
      	                       ->join('taggables', 'taggables.tag_id', '=', 'tags.id')
      	                       ->join('media', function($join) {
        	                       $join->on('media.id', '=', 'taggables.taggable_id')
        	                            ->where('taggables.taggable_type', '=', 'App\Models\Media');
      	                       })
      	                       ->whereIn('tags.name', $tags)
      	                       ->groupBy('media.id')
      	                       ->lists('id');
        
        $assets = \App\Models\Media::whereIn('id', $assetIDs);
        
        foreach ($tags as $tag) {
          //$assets = $assets->orWhere('name', 'LIKE', '%'.$tag.'%');
        }
        $view->assets = $assets->paginate(24);
    	} else {
        $view->assets = \App\Models\Media::paginate(24);
    	}
      
      return $view;
    }
    
    public function getDownload($assetID) {
      $asset = \App\Models\Media::find($assetID);
      
      if ( ! $asset) abort(404);
      
      ob_clean();
      return response()->download($asset->filePath('o'));
    }

    public function getUpload()
    {
      return view('assets.upload');
    }

    public function postUpload(Request $request) {
 
      $input = $request->all();

      $rules = array(
        'image' => 'image|max:204800',
      );

      $validation = \Validator::make($input, $rules);

      if ($validation->fails()) {
        return \Response::json([$validation->errors->first()], 400);
      }

      if (($media = $this->handleMediaUpload($request, 'image'))) {
        $tagIDs = [];
        if ($request->has('tags')) {
          $tagList = explode(',', $request->input('tags'));
          
          foreach ($tagList as $tag) {
            $tag = trim($tag);
            
            $tagORM = \App\Models\Tag::where('name', $tag)->first() ?: new \App\Models\Tag;
            
            $tagORM->name = $tag;
    
            if (empty($tagORM->slug)) {
              $tagORM->slug = \App\Helpers\Text::slugify($tagORM->name);
            }
            
            $tagORM->save();
            
            $tagIDs[] = $tagORM->id;
          }
          if (count($tagIDs)) $media->tags()->sync($tagIDs);
        }
        
        return \Response::json('success', 200);
      } else {
        return \Response::json('error', 400);
      }
    }
}
