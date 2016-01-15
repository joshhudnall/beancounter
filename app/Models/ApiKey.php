<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
  
  /**
   * Factory Methods
   */
	public static function find($id, $columns = array('api_keys.*'))
	{
  	if (is_numeric($id)) {
    	return parent::find($id, $columns);
  	}
  	
		return static::query()->where('api_key', $id)->first($columns);
	}

  public static function generate() {
    if ( ! \Auth::check()) return NULL;
    
    $apiKey = new ApiKey;
    $apiKey->user_id = \Auth::user()->id;
    $apiKey->api_key = \App\Helpers\Text::randomCryptoString();
    $apiKey->save();
    
    return $apiKey;
  }
  
  /**
   * Relations
   */
  public function user() {
    return $this->belongsTo('\App\Models\User');
  }
  
}

?>



