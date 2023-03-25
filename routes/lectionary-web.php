<?php


Route::middleware('web')->group( function() {


    Route::get('/lectionary/fordate/{date}', [\AscentCreative\Lectionary\Controllers\LectionaryDataController::class, 'fordate']);



}); //->middleware('web');

