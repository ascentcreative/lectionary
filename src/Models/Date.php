<?php

namespace AscentCreative\Lectionary\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{

    use HasFactory;

    protected $table = 'lectionary_dates';
    protected $fillable = ['date', 'week_id', 'year'];

    protected $appends = ['week', 'readings'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'week_id'];


    static function build($week_id, $date, $year) {

        if($date instanceof \DateTime) {
            $date = date_format($date, 'Y-m-d');
        }

        self::create([
            'week_id'=>$week_id,
            'date'=>$date,
            'year'=>$year
        ]);
    }


    public function week() {
        return $this->belongsTo(Week::class);
    }

    public function getWeekAttribute() {
        return $this->week()->first();
    }

    public function readings() {
        return $this->week->readings($this->year)->get();
    }

    public function getReadingsAttribute() {
        return $this->readings();
    }


}
 