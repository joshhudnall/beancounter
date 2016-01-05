<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
  protected $table = 'media';
  
  protected $sizes = [];
  
  public function __construct() {
    parent::__construct();
    
    $this->sizes = array_keys(['o' => TRUE] + \Config::get('media.sizes'));
  }
  
  public function tags() {
    return $this->morphToMany('\App\Models\Tag', 'taggable');
  }
  
  public function usageCount()
  {
    return $this->events()->count();
  }
  
  public function server() {
    $servers = [
      'app' => url('/').'/',
    ];
    
    return $servers[$this->server];
  }
  
  public function filePath($size = 's') {
    return public_path().'/'.$this->path.$this->name.'_'.$size.'.'.$this->ext;
  }
  
  public function URL($maxSize = 's') {
    for ($i = array_search(strtolower($maxSize), $this->sizes); $i < count($this->sizes); $i++) {
      if ($this->hasSize($this->sizes[$i])) {
        return $this->server().$this->path.$this->name.'_'.$this->sizes[$i].'.'.$this->ext;
      }
    }
    
    return $this->server().$this->path.$this->name.'_o.'.$this->ext;
  }
  
  public function sizeURLs() {
    $sizes = [];
    
    foreach ($this->dimensions as $size => $dimension) {
      $sizes[$size] = [
        'url' => $this->URL($size),
        'dimensions' => $dimension,
      ];
    }
    
    return $sizes;
  }
  
  public function hasSize($size) {
    return is_array($this->dimensions) ? (in_array($size, array_keys($this->dimensions))) : FALSE;
  }
  
  public function toArray() {
    $data = parent::toArray();
    $data['sizes'] = $this->sizeURLs();
    $data['url'] = $this->URL('m');
    
    return $data;
  }
  
  /**
   * Getters and Setters
   */
  public function getDimensionsAttribute($value) {
    return unserialize($value);
  }
  
  public function setDimensionsAttribute($value) {
    $this->attributes['dimensions'] = serialize($value);
  }
  
}

?>



