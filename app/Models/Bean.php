<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bean extends Model
{
  
   /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'counter_id', 'value',
  ];

 /**
   * Relations
   */
  public function counter() {
    return $this->belongsTo('\App\Models\Counter');
  }
  
}

?>



