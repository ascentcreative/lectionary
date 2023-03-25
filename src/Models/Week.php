<?php

namespace AscentCreative\Lectionary\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Week extends Model
{

    use HasFactory;

    protected $table = 'lectionary_weeks';
    protected $fillable = ['title'];

}
 