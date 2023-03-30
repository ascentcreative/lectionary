<?php


Route::middleware('web')->group( function() {


    Route::get('/lectionary/fordate/{date}', [\AscentCreative\Lectionary\Controllers\LectionaryDataController::class, 'fordate']);


    Route::get('/lectionary/cross-check/{year}', function($year) {

        $weeks = \AscentCreative\Lectionary\Models\Week::all();

        return view('lectionary::cross-check', ['weeks'=>$weeks, 'year'=>$year]);

    });

}); //->middleware('web');

