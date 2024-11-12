<?php
namespace AscentCreative\Lectionary;

use AscentCreative\Lectionary\Models\Date;
use AscentCreative\Lectionary\Models\Week;
use AscentCreative\BibleRef\Models\BibleRef;

class Lectionary {

    static function forDate($dateStr) {

        $output = [];

        // do we have this date listed:
        $date = Date::where("date", $dateStr)->first();

        if($date) {

            $weeks = Week::forDate($dateStr)->get();

            foreach($weeks as $week) {
                // dump($date->year);
                $output[] = [
                    'title' => $week->title,
                    'year' => $date->year,
                    'readings' => $week->readings($date->year)->get()
                ];
            
            }

        }

        return $output;

    }


    static function forWeek($week, $year) {

        $output = [];

        foreach($week->readings($year)->get()->sortBy('biblerefable_key')->groupBy('biblerefable_key') as $yr=>$readings) {

            $output[] = [
                'title' => $week->title,
                'year' => $yr,
                'readings' => $readings
            ];

        }

        return $output;

    }


}