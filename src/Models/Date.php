<?php

namespace AscentCreative\Lectionary\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{

    use HasFactory;

    protected $table = 'lectionary_dates';
    protected $fillable = ['date', 'week_id', 'year'];


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


}
 