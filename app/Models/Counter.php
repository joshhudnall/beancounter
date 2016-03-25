<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
  
  const CounterTypeCount = 1;
  const CounterTypeValue = 2;
  
  public static $units = ['s','m','h','d','w','mo'];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id', 
    'name', 
    'type',
  ];
  
  protected $appends = [
    'value',
  ];
  
  protected $hidden = [
    'id',
    'user_id',
    'created_at',
    'updated_at',
  ];
  
  public function getValueAttribute($value) {
    if ($this->type == static::CounterTypeCount) {
      return $this->beans()->sum('value');
    } else {
      return $this->beans()->avg('value');
    }
  }
  
  public function valueForRange($start, $end) {
    $query = $this->beans()->where('created_at', '>=', $start->copy()->timezone('UTC'))->where('created_at', '<=', $end->copy()->timezone('UTC'));

    if ($this->type == static::CounterTypeCount) {
      return $query->sum('value') ?: 0;
    } else {
      return $query->avg('value') ?: 0;
    }
  }
  
  public function graphValues($start, $end, $intervalUnit = 'd', $interval = 1) {
    $underUnit = array_search($intervalUnit, static::$units) - 1;
    $underUnit = max(0, $underUnit);
    
    $dataPoints = [];
    $val = 0;
    while ($start < $end) {
      $intervalEnd = $this->addInterval($start, $interval, $intervalUnit);
      
      if ($this->type == static::CounterTypeCount) {
        $val += 1.0 * $this->valueForRange($start, $intervalEnd);
        $dataPoints[$this->labelForIntervalUnit($start, $intervalUnit)] = $val;
      } else {
        $dataPoints[$this->labelForIntervalUnit($start, $intervalUnit)] = 1.0 * $this->valueForRange($start, $intervalEnd);
      }
      
      $start = $this->addInterval($intervalEnd, 1, $underUnit);
    }
    
    return $dataPoints;
  }
  
  private function labelForIntervalUnit($time, $intervalUnit) {
    switch ($intervalUnit) {
      case 's':
        $label = $time->format('s');
        break;
      case 'm':
        $label = $time->format('g:ia');
        break;
      case 'h':
        $label = $time->format('g:00a');
        break;
      case 'd':
        $label = $time->format('D');
        break;
      case 'w':
        $label = $time->format('M j');
        break;
      case 'mo':
        $label = $time->format('M Y');
        break;
    }
    
    return $label;
  }
  
  private function addInterval($time, $interval, $intervalUnit) {
    switch ($intervalUnit) {
      case 's':
        $time = $time->copy()->addSeconds($interval);
        break;
      case 'm':
        $time = $time->copy()->addMinutes($interval);
        break;
      case 'h':
        $time = $time->copy()->addHours($interval);
        break;
      case 'd':
        $time = $time->copy()->addDays($interval);
        break;
      case 'w':
        $time = $time->copy()->addWeeks($interval);
        break;
      case 'mo':
        $time = $time->copy()->addMonths($interval);
        break;
    }
    
    return $time;
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



