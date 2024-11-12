<?php

namespace AscentCreative\Lectionary\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use AscentCreative\BibleRef\Models\BibleRef;

use AscentCreative\CMS\Traits\Autocompletable;

class Week extends Model
{

    use HasFactory, Autocompletable;

    protected $table = 'lectionary_weeks';
    protected $fillable = ['title'];

    protected $autocomplete_search = ['title'];


    protected $hidden = ['id', 'created_at', 'updated_at'];
   
    public function readings($year=null) {
        $q = $this->morphMany(BibleRef::class, 'biblerefable');
        if($year && $year != '*') {
            $q->whereIn('biblerefable_key', [$year, '*']);
        }
        return $q;
    }


    public function dates() {
        return $this->hasMany(Date::class);
    }

    public function scopeForDate($q, $date) {
        $q->whereHas('dates', function($q) use ($date) {
            $q->where('date', $date);
        });
    }
    


}
 