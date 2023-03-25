<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lectionary_weeks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });


        // insert the data:
        DB::insert("INSERT INTO `lectionary_weeks` (`title`)
                    VALUES
                        ('First Sunday of Advent'),
                        ('Second Sunday of Advent'),
                        ('Third Sunday of Advent'),
                        ('Fourth Sunday of Advent'),
                        ('Christmas - Proper 1'),
                        ('Christmas - Proper 2'),
                        ('Christmas - Proper 3'),
                        ('First Sunday after Christmas Day'),
                        ('Holy Name of Jesus'),
                        ('New Year\'s Day'),
                        ('Second Sunday after Christmas Day'),
                        ('Epiphany of the Lord'),
                        ('Baptism of the Lord'),
                        ('Second Sunday after the Epiphany'),
                        ('Third Sunday after the Epiphany'),
                        ('Fourth Sunday after the Epiphany'),
                        ('Fifth Sunday after the Epiphany'),
                        ('Sixth Sunday after the Epiphany'),
                        ('Seventh Sunday after the Epiphany'),
                        ('Eighth Sunday after the Epiphany'),
                        ('Ninth Sunday after the Epiphany'),
                        ('Transfiguration Sunday'),
                        ('Ash Wednesday'),
                        ('First Sunday in Lent'),
                        ('Second Sunday in Lent'),
                        ('Third Sunday in Lent'),
                        ('Fourth Sunday in Lent'),
                        ('Fifth Sunday in Lent'),
                        ('Annunciation of the Lord'),
                        ('Liturgy of the Palms / Sixth Sunday in Lent'),
                        ('Liturgy of the Passion / Sixth Sunday in Lent'),
                        ('Monday of Holy Week'),
                        ('Tuesday of Holy Week'),
                        ('Wednesday of Holy Week'),
                        ('Maundy Thursday'),
                        ('Good Friday'),
                        ('Holy Saturday'),
                        ('Easter Vigil'),
                        ('Easter Day'),
                        ('Easter Evening'),
                        ('Second Sunday of Easter'),
                        ('Third Sunday of Easter'),
                        ('Fourth Sunday of Easter'),
                        ('Fifth Sunday of Easter'),
                        ('Sixth Sunday of Easter'),
                        ('Ascension of the Lord'),
                        ('Seventh Sunday of Easter'),
                        ('Day of Pentecost'),
                        ('Trinity Sunday'),
                        ('Proper 3, Ordinary/Lectionary 8'),
                        ('Proper 4, Ordinary/Lectionary 9'),
                        ('Proper 5, Ordinary/Lectionary 10'),
                        ('Proper 6, Ordinary/Lectionary 11'),
                        ('Proper 7, Ordinary/Lectionary 12'),
                        ('Proper 8, Ordinary/Lectionary 13'),
                        ('Proper 9, Ordinary/Lectionary 14'),
                        ('Proper 10, Ordinary/Lectionary 15'),
                        ('Proper 11, Ordinary/Lectionary 16'),
                        ('Proper 12, Ordinary/Lectionary 17'),
                        ('Proper 13, Ordinary/Lectionary 18'),
                        ('Proper 14, Ordinary/Lectionary 19'),
                        ('Proper 15, Ordinary/Lectionary 20'),
                        ('Proper 16, Ordinary/Lectionary 21'),
                        ('Proper 17, Ordinary/Lectionary 22'),
                        ('Proper 18, Ordinary/Lectionary 23'),
                        ('Proper 19, Ordinary/Lectionary 24'),
                        ('Holy Cross'),
                        ('Proper 20, Ordinary/Lectionary 25'),
                        ('Proper 21, Ordinary/Lectionary 26'),
                        ('Proper 22, Ordinary/Lectionary 27'),
                        ('Proper 23, Ordinary/Lectionary 28'),
                        ('Proper 24, Ordinary/Lectionary 29'),
                        ('Proper 25, Ordinary/Lectionary 30'),
                        ('Proper 26, Ordinary/Lectionary 31'),
                        ('All Saints\' Day'),
                        ('Proper 27, Ordinary/Lectionary 32'),
                        ('Proper 28, Ordinary/Lectionary 33'),
                        ('Reign of Christ / Proper 29, Ordinary/Lectionary 34'),
                        ('Visitation of Mary to Elizabeth'),
                        ('Presentation of The Lord'),
                        ('Thanksgiving Day');");

        Schema::create('lectionary_dates', function(Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->integer('week_id')->index();
            $table->char('year')->index();
            $table->timestamps();

            $table->index(['date', 'week_id', 'year']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lectionary_weeks');
        Schema::dropIfExists('lectionary_dates');
    }
    
};
