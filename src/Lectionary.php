<?php
namespace AscentCreative\Lectionary;

use AscentCreative\Lectionary\Models\Date;
use AscentCreative\Lectionary\Models\Week;
use AscentCreative\BibleRef\Models\BibleRef;

class Lectionary {

    static function forDate($dateStr) {

        // do we have this date listed:
        $date = Date::where("date", $dateStr)->first();

        if($date) {
            $weeks = Week::forDate($dateStr)->get();

            foreach($weeks as $week) {
                // dump($date->year);
                $week->readings = $week->readings($date->year)->get();
            }
            // dd($weeks);

            $date->weeks = $weeks;

        }

        return $date;

    }


}