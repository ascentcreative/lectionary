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

    public function fordate($date) {
       
        // do we have this date listed:
        $date = Date::where("date", $date)->first();

        return response()->json($date);

    }


}