<?php


Route::middleware('web')->group( function() {


    Route::get('/lectionary/fordate/{date}', [\AscentCreative\Lectionary\Controllers\LectionaryDataController::class, 'fordate']);

    Route::get('/lectionary/forweek/{week}/{year}', [\AscentCreative\Lectionary\Controllers\LectionaryDataController::class, 'forweek']);


    Route::get('/lectionary/cross-check/{year}', function($year) {

        $weeks = \AscentCreative\Lectionary\Models\Week::all();

        return view('lectionary::cross-check', ['weeks'=>$weeks, 'year'=>$year]);

    });

    Route::autocomplete('lectionary/week', \AscentCreative\Lectionary\Models\Week::class);



}); //->middleware('web');

