<?php

namespace AscentCreative\Lectionary\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
use Illuminate\Database\Eloquent\Model;

use AscentCreative\CMS\Bible\BibleReferenceParser;
use AscentCreative\CMS\Bible\Excpetions\BibleReferenceParserException;

use AscentCreative\Lectionary\Models\Date;
use AscentCreative\Lectionary\Models\Week;
use AscentCreative\BibleRef\Models\BibleRef;

class LectionaryDataController extends Controller {

    public function fordate($dateStr) {
       
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

        return response()->json($date);

    }


}