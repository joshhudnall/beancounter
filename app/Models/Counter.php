<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
  
  const CounterTypeCount = 1;
  const CounterTypeValue = 2;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id', 'name', 'type',
  ];
  
  public function getValueAttribute($value) {
    if ($this->type == static::CounterTypeCount) {
      return $this->beans()->sum('value');
    } else {
      return $this->beans()->avg('value');
    }
  }

  /**
   * Factory Methods
   */
	public static function find($id, $columns = array('counters.*'))
	{
  	if (is_numeric($id)) {
    	return parent::find($id, $columns);
  	}
  	
		return static::query()->where('name', $id)->first($columns);
	}
  
  /**
   * Relations
   */
  public function user() {
    return $this->belongsTo('\App\Models\User');
  }
  
  public function beans() {
    return $this->hasMany('\App\Models\Bean');
  }
  
}

?>



