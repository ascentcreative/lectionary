<?php

namespace AscentCreative\Lectionary\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
use Illuminate\Database\Eloquent\Model;

use AscentCreative\CMS\Bible\BibleReferenceParser;
use AscentCreative\CMS\Bible\Excpetions\BibleReferenceParserException;

use AscentCreative\Lectionary\Models\Week;


use AscentCreative\Lectionary\Lectionary;

class LectionaryDataController extends Controller {

    public function fordate($dateStr) {
       
       $data = Lectionary::forDate($dateStr);
        return response()->json($data);

    }

    public function forweek(Week $week, $year) {
        $data = Lectionary::forWeek($week, $year);
        return response()->json($data);
    }


}