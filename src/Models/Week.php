<?php

namespace AscentCreative\Lectionary\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use AscentCreative\BibleRef\Models\BibleRef;

class Week extends Model
{

    use HasFactory;

    protected $table = 'lectionary_weeks';
    protected $fillable = ['title'];


    protected $hidden = ['id', 'created_at', 'updated_at'];


    public function readings($year=null) {
        $q = $this->morphMany(BibleRef::class, 'biblerefable');
        if($year) {
            $q->whereIn('biblerefable_key', [$year, '*']);
        }
        return $q;
    }
    


}
 