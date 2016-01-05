<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
  
  /**
   * Relations
   */
  public function assets() {
    return $this->morphedByMany('App\Models\Media', 'taggable')->orderBy('created_at', 'DESC');
  }
  
  public static function assetTags() {
    return static::select('tags.*')
                 ->join('taggables', 'taggables.tag_id', '=', 'tags.id')
                 ->where('taggables.taggable_type', 'App\Models\Media')
                 ->groupBy('tags.id')
                 ->lists('name', 'slug');
  }
  
}

?>



