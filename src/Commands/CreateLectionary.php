<?php

namespace AscentCreative\Lectionary\Commands;

use Illuminate\Console\Command;
use AscentCreative\Lectionary\Models\Date;
use AscentCreative\BibleRef\Models\BibleRef;

class CreateLectionary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lectionary:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates Lectionary Dates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        BibleRef::where('biblerefable_type', 'AscentCreative\Lectionary\Models\Week')->delete();
        $this->buildbiblerefs();

        Date::truncate();
        for ($i = 2015; $i < 2100; $i++) {
			$this->createLectionaryDates($i);
		}

        return 0;

    }

    public function createLectionaryDates($year) {

        echo "Creating Dates for " . $year . "\n";

        $code = '';
		
		switch ($year%3) {
			case 1:
				$code = "A";
				break;
			case 2:
				$code = "B";
				break;
			case 0:
				$code = "C";
				break;
		
		}


        // Set Christmas (year-1 as we start in advent of the previous calendar year)
		Date::build(5, $year-1 . "-12-25", $code);
		Date::build(6, $year-1 . "-12-25", $code);
		Date::build(7, $year-1 . "-12-25", $code);
		
        // go back 4 sundays and populate advent:
		// - what day is Christmas? 
		$xmas = new \DateTime($year-1 . "-12-25");
		// echo date_format($xmas, 'N');
		// sun before:
		$sun4 = $xmas->modify('-' . date_format($xmas, 'N') . ' day');
		Date::build(4, $sun4, $code);
		Date::build(3, $sun4->modify('-7 day'), $code);
		Date::build(2, $sun4->modify('-7 day'), $code);
		Date::build(1, $sun4->modify('-7 day'), $code);
		
		// after xmas:
		$sun = new \DateTime($year-1 . "-12-25");
		$mod = 7 - date_format($sun, 'N');
		if ($mod == 0) {
			$mod = 7;
		}
		
		Date::build(8, $sun->modify( $mod . ' day'), $code);
		
		// new year
		$ny = new \DateTime($year . "-01-01");
		Date::build(9, $ny, $code);
		Date::build(10, $ny, $code);
		
		// 2nd sun after xmas
		// ** not always observed... only observed if falls BEFORE epiphany
		$sun->modify('7 day');
		$epi = new \DateTime($year . '-01-06');
		if($sun < $epi) {
			Date::build(11, $sun, $code);
		} else {
			// sunday not used - roll back
			$sun->modify('-7 day');
		}
		
		// epiphany
		Date::build(12, new \DateTime($year . "-01-06"), $code);
		// ** might be a weekday, but if it's a sunday, we need to increment again
		if (date_format(new \DateTime($year . "-01-06"), "N") == 7) {
		    $sun->modify('7 day');
		}
		
		
		// Baptism of the Lord
		Date::build(13, $sun->modify('7 day'), $code);
		
		
		// ok - now we need to get Easter, jump back and find the start of Lent and fill in upto lent with post-epiphany and transfig sundays.
		
		$easter = $this->calcEaster($year);
		Date::build(38, $easter, $code);
		Date::build(39, $easter, $code);
		Date::build(40, $easter, $code);
		Date::build(37, $easter->modify('-1 day'), $code); // holy sat
		Date::build(36, $easter->modify('-1 day'), $code); // good fri
		Date::build(35, $easter->modify('-1 day'), $code); // maundy thur
		Date::build(34, $easter->modify('-1 day'), $code); // weds of HW
		Date::build(33, $easter->modify('-1 day'), $code); // tues of HW
		Date::build(32, $easter->modify('-1 day'), $code); // mon of HW
		Date::build(31, $palm = $easter->modify('-1 day'), $code); // Passion Sun
		Date::build(30, $palm, $code); // Palm Sun
		
		Date::build(28, $lentsun = $palm->modify('-7 day'), $code); // 5th Sun in Lent
		Date::build(27, $lentsun->modify('-7 day'), $code); // 4th Sun in Lent
		Date::build(26, $lentsun->modify('-7 day'), $code); // 3rd Sun in Lent
		Date::build(25, $lentsun->modify('-7 day'), $code); // 2nd Sun in Lent
		Date::build(24, $lentsun->modify('-7 day'), $code); // 1st Sun in Lent
		
		Date::build(23, $ash = $lentsun->modify('-4 day'), $code); // Ash weds
		
		Date::build(22, $ash->modify('-3 day'), $code); // Transfiguration Sunday
		
		
		// ok go back and fill in Epiphany Sundays
		$id = 14;
		while ($sun < $lentsun) {
			Date::build($id, $sun->modify('7 day'), $code);
			$id++;
		}
		
		// sundays after easter:
		$sun = $this->calcEaster($year);
		Date::build(41, $sun->modify('7 day'), $code); // 2nd
		Date::build(42, $sun->modify('7 day'), $code); // 3rd
		Date::build(43, $sun->modify('7 day'), $code); // 4th
		Date::build(44, $sun->modify('7 day'), $code); // 5th
		Date::build(45, $sun->modify('7 day'), $code); // 6th
		Date::build(46, $sun->modify('7 day'), $code); // Ascension
		Date::build(47, $sun, $code); // 7th = ascension
		Date::build(48, $sun->modify('7 day'), $code); // Pentecost
		
		Date::build(49, $sun->modify('7 day'), $code); // Trinity
		
		// rest of the year:
		
		while ($sun < new \DateTime($year . '-11-20')) {
			
			$sun = $sun->modify('7 day');
			
			if($sun >= new \DateTime($year . '-05-24') and $sun <= new \DateTime($year . '-05-28') ) {
				Date::build(50, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-05-29') and $sun <= new \DateTime($year . '-06-04') ) {
				Date::build(51, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-06-05') and $sun <= new \DateTime($year . '-06-11') ) {
				Date::build(52, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-06-12') and $sun <= new \DateTime($year . '-06-18') ) {
				Date::build(53, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-06-19') and $sun <= new \DateTime($year . '-06-25') ) {
				Date::build(54, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-06-26') and $sun <= new \DateTime($year . '-07-02') ) {
				Date::build(55, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-07-03') and $sun <= new \DateTime($year . '-07-09') ) {
				Date::build(56, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-07-10') and $sun <= new \DateTime($year . '-07-16') ) {
				Date::build(57, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-07-17') and $sun <= new \DateTime($year . '-07-23') ) {
				Date::build(58, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-07-24') and $sun <= new \DateTime($year . '-07-30') ) {
				Date::build(59, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-07-31') and $sun <= new \DateTime($year . '-08-06') ) {
				Date::build(60, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-08-07') and $sun <= new \DateTime($year . '-08-13') ) {
				Date::build(61, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-08-14') and $sun <= new \DateTime($year . '-08-20') ) {
				Date::build(62, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-08-21') and $sun <= new \DateTime($year . '-08-27') ) {
				Date::build(63, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-08-28') and $sun <= new \DateTime($year . '-09-03') ) {
				Date::build(64, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-09-04') and $sun <= new \DateTime($year . '-09-10') ) {
				Date::build(65, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-09-11') and $sun <= new \DateTime($year . '-09-17') ) {
				Date::build(66, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-09-18') and $sun <= new \DateTime($year . '-09-24') ) {
				Date::build(68, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-09-25') and $sun <= new \DateTime($year . '-10-01') ) {
				Date::build(69, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-10-02') and $sun <= new \DateTime($year . '-10-08') ) {
				Date::build(70, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-10-09') and $sun <= new \DateTime($year . '-10-15') ) {
				Date::build(71, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-10-16') and $sun <= new \DateTime($year . '-10-22') ) {
				Date::build(72, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-10-23') and $sun <= new \DateTime($year . '-10-29') ) {
				Date::build(73, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-10-30') and $sun <= new \DateTime($year . '-11-05') ) {
				Date::build(74, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-11-06') and $sun <= new \DateTime($year . '-11-12') ) {
				Date::build(76, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-11-13') and $sun <= new \DateTime($year . '-11-19') ) {
				Date::build(77, $sun, $code);
			}
			if($sun >= new \DateTime($year . '-11-20') and $sun <= new \DateTime($year . '-11-26') ) {
				Date::build(78, $sun, $code);
			}
			
			
		}
		
		
		
		/** specials **/
		// presentation - 2nd feb
		Date::build(80, $year . '-02-02', $code);
		
		// annunciation - always march 25th
		Date::build(29, $year . '-03-25', $code);
		
		// visitation - 2nd feb
		Date::build(79, $year . '-05-31', $code);
		
		// Holy Cross - 2nd feb
		Date::build(67, $year . '-09-14', $code);
		
		// all saints
		Date::build(75, $year . '-11-01', $code);


    }


    private function calcEaster($year) {
		
		
		/* Using the Anonymous function (a.k.a Meeus/Jones/Butcher) */
		/*
		 a = Y mod 19	a = 4	a = 1
		b = floor (Y / 100)	b = 19	b = 20
		c = Y mod 100	c = 61	c = 15
		d = floor (b / 4)	d = 4	d = 5
		e = b mod 4	e = 3	e = 0
		f = floor ((b + 8) / 25)	f = 1	f = 1
		g = floor ((b − f + 1) / 3)	g = 6	g = 6
		h = (19a + b − d − g + 15) mod 30	h = 10	h = 13
		i = floor (c / 4)	i = 15	i = 3
		k = c mod 4	k = 1	k = 3
		L = (32 + 2e + 2i − h − k) mod 7
		m = floor ((a + 11h + 22L) / 451)	m = 0	m = 0
		month = floor ((h + L − 7m + 114) / 31)	month = 4 (April)	month = 4 (April)
		day = ((h + L − 7m + 114) mod 31) + 1	day = 2	day = 5
		Gregorian Easter	2 April 1961	5 April 2015
		*/
		
		$a = $year % 19;
		$b = floor ($year / 100);
		$c = $year % 100;
		$d = floor ($b / 4);
		$e = $b % 4;
		$f = floor (($b + 8) / 25);
		$g = floor (($b - $f +1) / 3);
		$h = ((19 * $a) + $b - $d - $g + 15) % 30;
		$i = floor ($c / 4);
		$k = $c % 4;
		$L = (32 + (2*$e) + (2*$i) - $h - $k) % 7;
		$m = floor (($a + (11*$h) + (22*$L)) / 451);
		
		$month = floor (($h + $L - (7*$m) + 114) / 31);
		
		$month = substr('00' . $month, -2);
		
		$day = (($h + $L - (7*$m) + 114) % 31) + 1;
		
		$day = substr('00' . $day, -2);
		
	
		/* debug  output 
		echo 'a = ' . $a . '<BR>';
		echo 'b = ' . $b . '<BR>';
		echo 'c = ' . $c . '<BR>';
		echo 'd = ' . $d . '<BR>';
		echo 'e = ' . $e . '<BR>';
		echo 'f = ' . $f . '<BR>';
		echo 'g = ' . $g . '<BR>';
		echo 'h = ' . $h . '<BR>';
		echo 'i = ' . $i . '<BR>';
		echo 'k = ' . $k . '<BR>';
		echo 'L = ' . $L . '<BR>';
		echo 'm = ' . $m . '<BR>';
		*/
		
		return new \DateTime($year . "-" . $month . "-" . $day);
		
		
	}



	
	public function buildbiblerefs() {
		
		// delete all - dev only:
		
		
		// Year A:
		$this->createReadings(1, 'A', array(
				"Is 2:1-5",
				"Ps 122",
				"Rom 13:11-14",
				"Matt 24:36-44"
		));
		
		$this->createReadings(1, 'B', array(
				"Isaiah 64:1-9",
				"Psalm 80:1-7",
				"Psalm 80:17-19",
				"1 Corinthians 1:3-9",
				"Mark 13:24-37"
		));
		
		$this->createReadings(1, 'C', array(
				"Jeremiah 33:14-16",
				"Psalm 25:1-10",
				"1 Thessalonians 3:9-13",
				"Luke 21:25-36"
		));
		
		
		
		$this->createReadings(2, 'A', array(
				"Isaiah 11:1-10",
                "Psalm 72:1-7",
                "Psalm 72:18-19",
                "Romans 15:4-13",
                "Matthew 3:1-12"
		));
		
		$this->createReadings(2, 'B', array(
				"Isaiah 40:1-11",
				"Psalm 85:1-2",
				"Psalm 85:8-13",
				"2 Peter 3:8-15a",
				"Mark 1:1-8"
		));
		
		$this->createReadings(2, 'C', array(
                "Baruch 5:1-9",
				"Malachi 3:1-4",
				"Luke 1:68-79",
				"Philippians 1:3-11",
				"Luke 3:1-6"
		));
		
		
		
		
		$this->createReadings(3, 'A', array(
				"Isaiah 35:1-10",
				"Psalm 146:5-10",
				"Luke 1:46b-55",
				"James 5:7-10",
				"Matthew 11:2-11"
		));
		
		$this->createReadings(3, 'B', array(
				"Isaiah 61:1-4",
				"Isaiah 61:8-11",
				"Psalm 126",
				"Luke 1:46b-55",
				"1 Thessalonians 5:16-24",
				"John 1:6-8",
				"John 1:19-28"
		));
		
		$this->createReadings(3, 'C', array(
				"Zephaniah 3:14-20",
				"Isaiah 12:2-6",
				"Philippians 4:4-7",
				"Luke 3:7-18"
		));
		
		
		
		
		
		
		
		$this->createReadings(4, 'A', array(
				"Isaiah 7:10-16",
				"Psalm 80:1-7",
				"Psalm 80:17-19",
				"Romans 1:1-7",
				"Matthew 1:18-25"
		));
		
		$this->createReadings(4, 'B', array(
				"2 Samuel 7:1-11",
				"2 Samuel 7:16",
				"Luke 1:46b-55",
				"Psalm 89:1-4",
				"Psalm 89:19-26",
				"Romans 16:25-27",
				"Luke 1:26-38"
		));
		
		$this->createReadings(4, 'C', array(
				"Micah 5:2-5a",
				"Luke 1:46b-55",
				"Psalm 80:1-7",
				"Hebrews 10:5-10",
				"Luke 1:39-55"
		));
		
		
		
		
		
		$this->createReadings(5, '*', array(
				"Isaiah 9:2-7",
				"Psalm 96",
				"Titus 2:11-14",
				"Luke 2:1-14",
				"Luke 2:15-20"
		));
		
		$this->createReadings(6, '*', array(
				"Isaiah 62:6-12",
				"Psalm 97",
				"Titus 3:4-7",
				"Luke 2:1-7",
				"Luke 2:8-20"
		));
		
		$this->createReadings(7, '*', array(
				"Isaiah 52:7-10",
				"Psalm 98",
				"Hebrews 1:1-4",
				"Hebrews 1:5-12",
				"John 1:1-14"
		));
		
		
		
		$this->createReadings(8, 'A', array(
				"Isaiah 63:7-9",
				"Psalm 148",
				"Hebrews 2:10-18",
				"Matthew 2:13-23"
		));
		
		$this->createReadings(8, 'B', array(
				"Isaiah 61:10-62:3",
				"Psalm 148",
				"Galatians 4:4-7",
				"Luke 2:22-40"
		));
		
		$this->createReadings(8, 'C', array(
				"1 Samuel 2:18-20",
				"1 Samuel 2:26",
				"Psalm 148",
				"Colossians 3:12-17",
				"Luke 2:41-52"
		));
		
		
		
		
		
		
		$this->createReadings(9, '*', array(
				"Numbers 6:22-27",
				"Psalm 8",
				"Galatians 4:4-7",
				"Philippians 2:5-11",
				"Luke 2:15-21"
		));
		
		$this->createReadings(10, '*', array(
				"Ecclesiastes 3:1-13",
				"Psalm 8",
				"Revelation 21:1-6a",
				"Matthew 25:31-46"
		));
		
		$this->createReadings(11, '*', array(
				"Jeremiah 31:7-14",
                "Sirach 24:1-12",
				"Psalm 147:12-20",
                "Wisdom of Solomon 10:15-21",
				"Ephesians 1:3-14",
				"John 1:1-9",
				"John 1:10-18"
		));
		
		$this->createReadings(12, '*', array(
				"Isaiah 60:1-6",
				"Psalm 72:1-7",
				"Psalm 72:10-14",
				"Ephesians 3:1-12",
				"Matthew 2:1-12"
		));
		
		
		$this->createReadings(13, 'A', array(
				"Isaiah 42:1-9",
				"Psalm 29",
				"Acts 10:34-43",
				"Matthew 3:13-17"
		));
		
		$this->createReadings(13, 'B', array(
				"Genesis 1:1-5",
				"Psalm 29",
				"Acts 19:1-7",
				"Mark 1:4-11"
		));
		
		$this->createReadings(13, 'C', array(
				"Isaiah 43:1-7",
				"Psalm 29",
				"Acts 8:14-17",
				"Luke 3:15-17",
				"Luke 3:21-22"
		));
		
		
		$this->createReadings(14, 'A', array(
				"Isaiah 49:1-7",
				"Psalm 40:1-11",
				"1 Corinthians 1:1-9",
				"John 1:29-42"
		));
		
		$this->createReadings(14, 'B', array(
				"1 Samuel 3:1-11-20",
				"Psalm 139:1-6",
				"Psalm 139:13-18",
				"1 Corinthians 6:12-20",
				"John 1:43-51"
		));
		
		$this->createReadings(14, 'C', array(
				"Isaiah 62:1-5",
				"Psalm 36:5-10",
				"1 Corinthians 12:1-11",
				"John 2:1-11"
		));
		
		
		
		
		$this->createReadings(15, 'A', array(
				"Isaiah 9:1-4",
				"Psalm 27:1",
				"Psalm 27:4-9",
				"1 Corinthians 1:10-18",
				"Matthew 4:12-23"
		));
		
		$this->createReadings(15, 'B', array(
				"Jonah 3:1-5",
				"Jonah 3:10",
				"Psalm 62:5-12",
				"1 Corinthians 7:29-31",
				"Mark 1:14-20"
		));
		
		$this->createReadings(15, 'C', array(
				"Nehemiah 8:1-3",
				"Nehemiah 8:5-6",
				"Nehemiah 8:8-10",
				"Psalm 19",
				"1 Corinthians 12:12-31a",
				"Luke 4:14-21"
		));
		
		
		
		
		
		$this->createReadings(16, 'A', array(
				"Micah 6:1-8",
				"Psalm 15",
				"1 Corinthians 1:18-31",
				"Matthew 5:1-12"
		));
		

		$this->createReadings(16, 'B', array(
				"Deuteronomy 18:15-20",
				"Psalm 111",
				"1 Corinthians 8:1-13",
				"Mark 1:21-28"
		));
		
		$this->createReadings(16, 'C', array(
				"Jeremiah 1:4-10",
				"Psalm 71:1-6",
				"1 Corinthians 13:1-13",
				"Luke 4:21-30"
		));
		
		
		
		$this->createReadings(17, 'A', array(
				"Isaiah 58:1-9a",
				"Isaiah 58:9b-12",
				"Psalm 112:1-10",
				"1 Corinthians 2:1-12",
				"1 Corinthians 2:13-16",
				"Matthew 5:13-20"
		));

		$this->createReadings(17, 'B', array(
				"Isaiah 40:21-31",
				"Psalm 147:1-11",
				"Psalm 147:20c",
				"1 Corinthians 9:16-23",
				"Mark 1:29-39"
		));
		
		$this->createReadings(17, 'C', array(
				"Isaiah 6:1-13",
				"Psalm 138",
				"1 Corinthians 15:1-11",
				"Luke 5:1-11"
		));
		
		
		
		
		$this->createReadings(18, 'A', array(
				"Deuteronomy 30:15-20",
                "Sirach 15:15-20",
				"Psalm 119:1-8",
				"1 Corinthians 3:1-9",
				"Matthew 5:21-37"
		));

		$this->createReadings(18, 'B', array(
				"2 Kings 5:1-14",
				"Psalm 30",
				"1 Corinthians 9:24-27",
				"Mark 1:40-45"
		));

		$this->createReadings(18, 'C', array(
				"Jeremiah 17:5-10",
				"Psalm 1",
				"1 Corinthians 15:12-20",
				"Luke 6:17-26"
		));
		
		
		
		$this->createReadings(19, 'A', array(
				"Leviticus 19:1-2",
				"Leviticus 19:9-18",
				"Psalm 119:33-40",
				"1 Corinthians 3:10-11",
				"1 Corinthians 3:16-23",
				"Matthew 5:38-48"
		));
		
		$this->createReadings(19, 'B', array(
				"Isaiah 43:18-25",
				"Psalm 41",
				"2 Corinthians 1:18-22",
				"Mark 2:1-12"
		));
		
		$this->createReadings(19, 'C', array(
				"Genesis 45:3-11",
				"Genesis 45:15",
				"Psalm 37:1-11",
				"Psalm 37:39-40",
				"1 Corinthians 15:35-38",
				"1 Corinthians 15:42-50",
				"Luke 6:27-38"
		));
		
		
		
		
		
		$this->createReadings(20, 'A', array(
				"Isaiah 49:8-16a",
				"Psalm 131",
				"1 Corinthians 4:1-5",
				"Matthew 6:24-34"
		));
		
		$this->createReadings(20, 'B', array(
				"Hosea 2:14-20",
				"Psalm 103:1-13",
				"Psalm 103:22",
				"2 Corinthians 3:1-6",
				"Mark 2:13-22"
		));
		
		$this->createReadings(20, 'C', array(
                "Sirach 27:4-7",
				"Isaiah 55:10-13",
				"Psalm 92:1-4",
				"Psalm 92:12-15",
				"1 Corinthians 15:51-58",
				"Luke 6:39-49"
		));
		
		
		
		
		
		
		
		$this->createReadings(21, 'A', array(
				"Deuteronomy 11:18-21",
				"Deuteronomy 11:26-28",
				"Psalm 31:1-5",
				"Psalm 31:19-24",
				"Romans 1:16-17",
				"Romans 3:22b-28",
				"Romans 3:29-31",
				"Matthew 7:21-29"
		));
		
		$this->createReadings(21, 'B', array(
				"Deuteronomy 5:12-15",
				"Psalm 81:1-10",
				"2 Corinthians 4:5-12",
				"Mark 2:23-3:6"
		));
		
		$this->createReadings(21, 'C', array(
				"1 Kings 8:22-23",
				"1 Kings 8:41-43",
				"Psalm 96:1-9",
				"Galatians 1:1-12".
				"Luke 7:1-10"
		));
		
		
		
		
		$this->createReadings(22, 'A', array(
				"Exodus 24:12-18",
				"Psalm 2",
				"Psalm 99",
				"2 Peter 1:16-21",
				"Matthew 17:1-9"
		));
		$this->createReadings(22, 'B', array(
				"2 Kings 2:1-12",
				"Psalm 50:1-6",
				"2 Corinthians 4:3-6",
				"Mark 9:2-9"
		));
		
		$this->createReadings(22, 'C', array(
				"Exodus 34:29-35",
				"Psalm 99",
				"2 Corinthians 3:12-4:2",
				"Luke 9:28-43"
		));
		
		
		
		
		
		// Ash Weds
		$this->createReadings(23, '*', array(
				"Joel 2:1-2",
				"Joel 2:12-17",
				"Isaiah 58:1-12",
				"Psalm 51:1-17",
				"2 Corinthians 5:20b—6:10",
				"Matthew 6:1-6",
				"Matthew 6:16-21"
		));
		
		
		
		$this->createReadings(24, 'A', array(
				"Genesis 2:15-17",
				"Genesis 3:1-7",
				"Psalm 32",
				"Romans 5:12-19",
				"Matthew 4:1-11"
		));
		
		$this->createReadings(24, 'B', array(
				"Genesis 9:8-17",
				"Psalm 25:1-10",
				"1 Peter 3:18-22",
				"Mark 1:9-15"
		));

		$this->createReadings(24, 'C', array(
				"Deuteronomy 26:1-11",
				"Psalm 91:1-2",
				"Psalm 91:9-16",
				"Romans 10:8b-13",
				"Luke 4:1-13"
		));
		
		
		
		$this->createReadings(25, 'A', array(
				"Genesis 12:1-4a",
				"Psalm 121",
				"Romans 4:1-5",
				"Romans 4:13-17",
				"John 3:1-17",
				"Matthew 17:1-9"
		));
		
		$this->createReadings(25, 'B', array(
				"Genesis 17:1-7",
				"Genesis 17:15-16",
				"Psalm 22:23-31",
				"Romans 4:13-25",
				"Mark 8:31-38",
				"Mark 9:2-9"
		));
		
		$this->createReadings(25, 'C', array(
				"Genesis 15:1-12",
				"Genesis 15:17-18",
				"Psalm 27",
				"Philippians 3:17-4:1",
				"Luke 13:31-35",
				"Luke 9:28-26"
		));
		
		
		
		$this->createReadings(26, 'A', array(
				"Exodus 17:1-7",
				"Psalm 95",
				"Romans 5:1-11",
				"John 4:5-42"
		));
		
		$this->createReadings(26, 'B', array(
				"Exodus 20:1-17",
				"Psalm 19",
				"1 Corinthians 1:18-25",
				"John 2:13-22"
		));
		
		$this->createReadings(26, 'C', array(
				"Isaiah 55:1-9",
				"Psalm 63:1-8",
				"1 Corinthians 10:1-13",
				"Luke 13:1-9"
		));
		
		
		

		$this->createReadings(27, 'A', array(
				"1 Samuel 16:1-13",
				"Psalm 23",
				"Ephesians 5:8-14",
				"John 9:1-41"
		));
		
		$this->createReadings(27, 'B', array(
				"Numbers 21:4-9",
				"Psalm 107:1-3",
				"Psalm 107:17-22",
				"Ephesians 2:1-10",
				"John 3:14-21"
		));
		
		$this->createReadings(27, 'C', array(
				"Joshua 5:9-12",
				"Psalm 32",
				"2 Corinthians 5:16-21",
				"Luke 15:1-3",
				"Luke 15:11b-32"
		));
		
		
		
		
		$this->createReadings(28, 'A', array(
				"Ezekiel 37:1-14",
				"Psalm 130",
				"Romans 8:6-11",
				"John 11:1-45"
		));
		
		$this->createReadings(28, 'B', array(
				"Jeremiah 31:31-34",
				"Psalm 51:1-12",
				"Psalm 119:9-16",
				"Hebrews 5:5-10",
				"John 12:20-33"
		));
		
		$this->createReadings(28, 'C', array(
				"Isaiah 43:16-21",
				"Psalm 126",
				"Philippians 3:4b-14",
				"John 12:1-8"
		));
		
		
		
		
		
		
		
		$this->createReadings(30, 'A', array(
				"Matthew 21:1-11",
				"Psalm 118:1-2",
				"Psalm 118:19-29"
		));
		
		$this->createReadings(30, 'B', array(
				"Mark 11:1-11",
				"John 12:12-16",
				"Psalm 118:1-2",
				"Psalm 118:19-29"
		));
		
		$this->createReadings(30, 'C', array(
				"Luke 19:28-40",
				"Psalm 118:1-2",
				"Psalm 118:19-29"
		));
		
		
		

		$this->createReadings(31, 'A', array(
				"Isaiah 50:4-9a",
				"Psalm 31:9-16",
				"Philippians 2:5-11",
				"Matthew 26:14-27:66",
				"Matthew 27:11-54"
		));
		
		$this->createReadings(31, 'B', array(
				"Isaiah 50:4-9a",
				"Psalm 31:9-16",
				"Philippians 2:5-11",
				"Mark 14:1-15:47",
				"Mark 15:1-47"
		));
		
		$this->createReadings(31, 'C', array(
				"Isaiah 50:4-9a",
				"Psalm 31:9-16",
				"Philippians 2:5-11",
				"Luke 22:14-23:56",
				"Luke 23:1-49"
		));
		
		
		
		
		$this->createReadings(32, '*', array(
				"Isaiah 42:1-9",
				"Psalm 36:5-11",
				"Hebrews 9:11-15",
				"John 12:1-11"
		));
		
		$this->createReadings(33, '*', array(
				"Isaiah 49:1-7",
				"Psalm 71:1-14",
				"1 Corinthians 1:18-31",
				"John 12:20-36"
		));
		
		$this->createReadings(34, '*', array(
				"Isaiah 50:4-9a",
				"Psalm 70",
				"Hebrews 12:1-3",
				"John 13:21-32"
		));
		
		$this->createReadings(35, '*', array(
				"Exodus 12:1-14",
				"Psalm 116:1-2",
				"Psalm 116:12-19",
				"1 Corinthians 11:23-26",
				"John 13:1-17",
				"John 13:31b-35"
		));
		
		$this->createReadings(36, '*', array(
				"Isaiah 52:13-53:12",
				"Psalm 22",
				"Hebrews 10:16-25",
				"Hebrews 4:14-16",
				"Hebrews 5:7-9",
				"John 18:1-19:42"
		));

		$this->createReadings(37, '*', array(
				"Job 14:1-14",
				"Lamentations 3:1-9",
				"Lamentations 3:19-24",
				"Psalm 31:1-4",
				"Psalm 31:15-16",
				"1 Peter 4:1-8",
				"Matthew 27:57-66",
				"John 19:38-42"
		));
		

		$this->createReadings(37, '*', array(
				"Job 14:1-14",
				"Lamentations 3:1-9",
				"Lamentations 3:19-24",
				"Psalm 31:1-4",
				"Psalm 31:15-16",
				"1 Peter 4:1-8",
				"Matthew 27:57-66",
				"John 19:38-42"
		));
		
		$this->createReadings(38, '*', array(
				"Genesis 1:1-2:4a",
				"Genesis 7:1-5",
				"Genesis 7:11-18",
				"Genesis 8:6-18",
				"Genesis 9:8-13",
				"Genesis 22:1-18",
				"Exodus 14:10-31",
				"Exodus 15:1b-13",
				"Exodus 15:17-18",
				"Exodus 15:20-21",
				"Psalm 16",
				"Psalm 19",
				"Psalm 31:1-4",
				"Psalm 31:15-16",
				"Psalm 42",
				"Psalm 43",
				"Psalm 46",
				"Psalm 98",
				"Psalm 114",
				"Psalm 136:1-9",
				"Psalm 136:23-26",
				"Psalm 143",
				"Proverbs 8:1-8",
				"Proverbs 8:19-21",
				"Proverbs 9:4b-6",
				"Isaiah 12:2-6",
				"Isaiah 55:1-11",
                "Baruch 3:9-15",
                "Baruch 3:32-4:4",
				"Ezekiel 36:24-28",
				"Ezekiel 37:1-14",
				"Zephaniah 3:14-20",
				"Romans 6:3-11",
		));
		
		
		
		$this->createReadings(38, 'A', array(
				"Matthew 28:1-10"
		));
		
		$this->createReadings(38, 'B', array(
				"Mark 16:1-8"
		));
		
		$this->createReadings(38, 'C', array(
				"Luke 24:1-12"
		));
		
		
		$this->createReadings(39, 'A', array(
				"Acts 10:34-43",
				"Jeremiah 31:1-6",
				"Psalm 118:1-2",
				"Psalm 118:14-24",
				"Colossians 3:1-4",
				"John 20:1-18",
				"Matthew 28:1-10"
		));
		
		$this->createReadings(39, 'B', array(
				"Acts 10:34-43",
				"Isaiah 25:6-9",
				"Psalm 118:1-2",
				"Psalm 118:14-24",
				"1 Corinthians 15:1-11",
				"John 20:1-18",
				"Mark 16:1-8"
		));
		
		$this->createReadings(39, 'C', array(
				"Acts 10:34-43",
				"Isaiah 65:17-25",
				"Psalm 118:1-2",
				"Psalm 118:14-24",
				"1 Corinthians 15:19-26",
				"John 20:1-18",
				"Luke 24:1-12"
		));
		
		
		
		
		$this->createReadings(40, '*', array(
				"Isaiah 25:6-9",
				"Psalm 114",
				"1 Corinthians 5:6b-8",
				"Luke 24:13-49"
		));
		
		
		
		$this->createReadings(41, 'A', array(
				"Acts 2:14a",
				"Acts 2:22-32",
				"Psalm 16",
				"1 Peter 1:3-9",
				"John 20:19-31"
		));
		
		$this->createReadings(41, 'B', array(
				"Acts 4:32-35",
				"Psalm 133",
				"1 John 1:1-2:2",
				"John 20:19-31"
		));
		
		$this->createReadings(41, 'C', array(
				"Acts 5:27-32",
				"Psalm 118:14-29",
				"Psalm 150",
				"Revelation 1:4-8",
				"John 20:19-31"
		));
		
		
		
		

		$this->createReadings(42, 'A', array(
				"Acts 2:14a",
				"Acts 2:36-41",
				"Psalm 116:1-4",
				"Psalm 116:12-19",
				"1 Peter 1:17-23",
				"Luke 24:13-35"
		));
		
		$this->createReadings(42, 'B', array(
				"Acts 3:12-19",
				"Psalm 4",
				"1 John 3:1-7",
				"Luke 24:36b-48"
		));
		
		$this->createReadings(42, 'C', array(
				"Acts 9:1-20",
				"Psalm 30",
				"Revelation 5:11-14",
				"John 21:1-19"
		));
		
		
		
		

		$this->createReadings(43, 'A', array(
				"Acts 2:42-47",
				"Psalm 23",
				"1 Peter 2:19-25",
				"John 10:1-10"
		));
		
		$this->createReadings(43, 'B', array(
				"Acts 4:5-12",
				"Psalm 23",
				"1 John 3:16-24",
				"John 10:11-18"
		));
		
		$this->createReadings(43, 'C', array(
				"Acts 9:36-43",
				"Psalm 23",
				"Revelation 7:9-17",
				"John 10:22-30"
		));
		
		
		

		$this->createReadings(44, 'A', array(
				"Acts 7:55-60",
				"Psalm 31:1-5",
				"Psalm 31:15-16",
				"1 Peter 2:2-10",
				"John 14:1-14"
		));
		
		$this->createReadings(44, 'B', array(
				"Acts 8:26-40",
				"Psalm 22:25-31",
				"1 John 4:7-21",
				"John 15:1-8"
		));
		
		$this->createReadings(44, 'C', array(
				"Acts 11:1-18",
				"Psalm 148",
				"Revelation 21:1-6",
				"John 13:31-35"
		));
		
		
		
		
		$this->createReadings(45, 'A', array(
				"Acts 17:22-31",
				"Psalm 66:8-20",
				"1 Peter 3:13-22",
				"John 14:15-21"
		));
		
		$this->createReadings(45, 'B', array(
				"Acts 10:44-48",
				"Psalm 98",
				"1 John 5:1-6",
				"John 15:9-17"
		));
		
		$this->createReadings(45, 'C', array(
				"Acts 16:9-15",
				"Psalm 67",
				"Revelation 21:10",
				"Revelation 21:22-22:5",
				"John 14:23-29",
				"John 5:1-9"
		));
		
		
		
		

		$this->createReadings(46, '*', array(
				"Acts 1:1-11",
				"Psalm 47",
				"Psalm 93",
				"Ephesians 1:15-23",
				"Luke 24:44-53"
		));
		
		
		

		$this->createReadings(47, 'A', array(
				"Acts 1:6-14",
				"Psalm 68:1-10",
				"Psalm 68:32-35",
				"1 Peter 4:12-14",
				"1 Peter 5:6-11",
				"John 17:1-11"
		));
		
		$this->createReadings(47, 'B', array(
				"Acts 1:15-17",
				"Acts 1:21-26",
				"Psalm 1",
				"1 John 5:9-13",
				"John 17:6-19"
		));
		
		$this->createReadings(47, 'C', array(
				"Acts 16:16-34",
				"Psalm 97",
				"Revelation 22:12-14",
				"Revelation 22:16-17",
				"Revelation 22:20-21",
				"John 17:20-26"
		));
		
		
		
		

		$this->createReadings(48, 'A', array(
				"Acts 2:1-21",
				"Numbers 11:24-30",
				"Psalm 68:32-35",
				"Psalm 104:24-34",
                "Psalm 104:35b",
				"1 Corinthians 12:3b-13",
				"John 20:19-23",
				"John 7:37-39"
		));

		$this->createReadings(48, 'B', array(
				"Acts 2:1-21",
				"Ezekiel 37:1-14",
				"Psalm 104:24-34",
                "Psalm 104:35b",
				"Romans 8:22-27",
				"John 15:26-27",
				"John 16:4b-15"
		));
		

		$this->createReadings(48, 'C', array(
				"Acts 2:1-21",
				"Genesis 11:1-9",
				"Psalm 104:24-35b",
				"Romans 8:14-17",
				"John 14:8-17",
				"John 14:25-27"
		));
		
		
		
		

		$this->createReadings(49, 'A', array(
				"Genesis 1:1-2:4a",
				"Psalm 8",
				"2 Corinthians 13:11-13",
				"Matthew 28:16-20"
		));
		
		$this->createReadings(49, 'B', array(
				"Isaiah 6:1-8",
				"Psalm 29",
				"Romans 8:12-17",
				"John 3:1-17"
		));
		
		$this->createReadings(49, 'C', array(
				"Proverbs 8:1-4",
				"Proverbs 8:22-31",
				"Psalm 8",
				"Romans 5:1-5",
				"John 16:12-15"
		));
		
		
		
		
		
		
		

		$this->createReadings(50, 'A', array(
				"Isaiah 49:8-16a",
				"Psalm 131",
				"1 Corinthians 4:1-5",
				"Matthew 6:24-34"
		));
		
		$this->createReadings(50, 'B', array(
				"Hosea 2:14-20",
				"Psalm 103:1-13",
				"Psalm 103:22",
				"2 Corinthians 3:1-6",
				"Mark 2:13-22"
		));
		
		$this->createReadings(50, 'C', array(
                "Sirach 27:4-7",
				"Isaiah 55:10-13",
				"Psalm 92:1-4",
				"Psalm 92:12-15",
				"1 Corinthians 15:51-58",
				"Luke 6:39-49"
		));
		
		
		
		

		$this->createReadings(51, 'A', array(
				"Genesis 6:9-22",
				"Genesis 7:24",
				"Genesis 8:14-19",
				"Psalm 46",
				"Romans 1:16-17",
				"Romans 3:22b-31",
				"Matthew 7:21-29",
				"Deuteronomy 11:18-21",
				"Deuteronomy 11:26-28",
				"Psalm 31:1-5",
				"Psalm 31:19-24"
		));
		
		$this->createReadings(51, 'B', array(
				"1 Samuel 3:1-20",
				"Psalm 139:1-6",
				"Psalm 139:13-18",
				"2 Corinthians 4:5-12",
				"Mark 2:23-3:6",
				"Deuteronomy 5:12-15",
				"Psalm 81:1-10"
		));
		
		$this->createReadings(51, 'C', array(
				"1 Kings 18:20-21",
				"1 Kings 18:22-39",
				"Psalm 96",
				"Galatians 1:1-12",
				"Luke 7:1-10",
				"1 Kings 8:22-23",
				"1 Kings 8:41-43",
				"Psalm 96:1-9"
		));
		
		
		

		$this->createReadings(52, 'A', array(
				"Genesis 12:1-9",
				"Psalm 33:1-12",
				"Romans 4:13-25",
				"Matthew 9:9-13",
				"Matthew 9:18-26",
				"Hosea 5:15-6:6",
				"Psalm 50:7-15"
		));
		
		$this->createReadings(52, 'B', array(
				"1 Samuel 8:4-20",
				"1 Samuel 11:14-15",
				"Psalm 138",
				"2 Corinthians 4:13-5:1",
				"Mark 3:20-35",
				"Genesis 3:8-15",
				"Psalm 130"
		));
		
		$this->createReadings(52, 'C', array(
				"1 Kings 17:8-24",
				"Psalm 146",
				"Galatians 1:11-24",
				"Luke 7:11-17",
				"Psalm 30"
		));
		
		
		

		$this->createReadings(53, 'A', array(
				"Genesis 18:1-15",
				"Genesis 21:1-7",
				"Psalm 116:1-2",
				"Psalm 116:12-19",
				"Matthew 9:18-26",
				"Romans 5:1-8",
				"Matthew 9:23",
				"Matthew 9:35—10:8",
				"Exodus 19:2-8",
				"Psalm 100"
		));
		
		$this->createReadings(53, 'B', array(
				"1 Samuel 15:34-16:13",
				"Psalm 20",
				"2 Corinthians 5:6-17",
				"Mark 4:26-34",
				"Ezekiel 17:22-24",
				"Psalm 92:1-4",
				"Psalm 92:12-15"
		));
		
		$this->createReadings(53, 'C', array(
				"1 Kings 21:1-21a",
				"Psalm 5:1-8",
				"Galatians 2:15-21",
				"Luke 7:36-8:3",
				"2 Samuel 11:26-12:10",
				"2 Samuel 12:13-15",
				"Psalm 32"
		));
		
		
		
		
		
		$this->createReadings(54, 'A', array(
				"Genesis 21:8-21",
				"Psalm 86:1-10",
				"Psalm 86:16-17",
				"Romans 6:1-11",
				"Matthew 10:24-39",
				"Jeremiah 20:7-13",
				"Psalm 69:7-18"
		));
		
		$this->createReadings(54, 'B', array(
				"1 Samuel 17:1a",
				"1 Samuel 17:4-11",
				"1 Samuel 17:19-23",
				"1 Samuel 17:32-49",
				"1 Samuel 17:57-18:5",
				"1 Samuel 18:10-16",
				"Psalm 9:9-20",
				"Psalm 133",
				"2 Corinthians 6:1-13",
				"Mark 4:35-41",
				"Job 38:1-11",
				"Psalm 107:1-3",
				"Psalm 107:23-32"
		));
		
		$this->createReadings(54, 'C', array(
				"1 Kings 19:1-15a",
				"Psalm 42",
				"Psalm 43",
				"Galatians 3:23-29",
				"Luke 8:26-39",
				"Isaiah 65:1-9",
				"Psalm 22:19-28"
		));
		
		
		
		

		$this->createReadings(55, 'A', array(
				"Genesis 22:1-14",
				"Psalm 13",
				"Romans 6:12-23",
				"Matthew 10:40-42",
				"Jeremiah 28:5-9",
				"Psalm 89:1-4",
				"Psalm 89:15-18"
		));
		
		$this->createReadings(55, 'B', array(
				"2 Samuel 1:1",
				"2 Samuel 1:17-27",
				"Psalm 130",
				"2 Corinthians 8:7-15",
				"Mark 5:21-43",
				"Lamentations 3:22-33",
				"Psalm 30"
		));
		
		$this->createReadings(55, 'C', array(
				"2 Kings 2:1-2",
				"2 Kings 2:6-14",
				"Psalm 77:1-2",
				"Psalm 77:11-20",
				"Galatians 5:1",
				"Galatians 5:13-25",
				"Luke 9:51-62",
				"1 Kings 19:15-16",
				"1 Kings 19:19-21",
				"Psalm 16"
		));
		
		

		$this->createReadings(56, 'A', array(
				"Genesis 24:34-38",
				"Genesis 24:42-49",
				"Genesis 24:58-67",
				"Psalm 45:10-17",
				"Song of Solomon 2:8-13",
				"Romans 7:15-25a",
				"Matthew 11:16-19",
				"Matthew 11:25-30",
				"Zechariah 9:9-12",
				"Psalm 145:8-14"
		));
		
		$this->createReadings(56, 'B', array(
				"2 Samuel 5:1-5",
				"2 Samuel 5:9-10",
				"Psalm 48",
				"2 Corinthians 12:2-10",
				"Mark 6:1-13",
				"Ezekiel 2:1-5",
				"Psalm 123"
		));
		
		$this->createReadings(56, 'C', array(
				"2 Kings 5:1-14",
				"Psalm 30",
				"Galatians 6:1-16",
				"Luke 10:1-11",
				"Luke 10:16-20",
				"Isaiah 66:10-14",
				"Psalm 66:1-9"
		));
		
		
		
		
		

		$this->createReadings(57, 'A', array(
				"Genesis 25:19-34",
				"Psalm 119:105-112",
				"Romans 8:1-11",
				"Matthew 13:1-9",
				"Matthew 13:18-23",
				"Isaiah 55:10-13",
				"Psalm 65:1-13"
		));
		
		$this->createReadings(57, 'B', array(
				"2 Samuel 6:1-5",
				"2 Samuel 6:12b-19",
				"Psalm 24",
				"Ephesians 1:3-14",
				"Mark 6:14-29",
				"Amos 7:7-15",
				"Psalm 85:8-13"
		));
		
		$this->createReadings(57, 'C', array(
				"Amos 7:7-17",
				"Psalm 82",
				"Colossians 1:1-14",
				"Luke 10:25-37",
				"Deuteronomy 30:9-14",
				"Psalm 25:1-10"
		));
		
		
		
		
		

		$this->createReadings(58, 'A', array(
				"Genesis 28:10-19a",
				"Psalm 139:1-12",
				"Psalm 139:23-24",
				"Romans 8:12-25",
				"Matthew 13:24-30",
				"Matthew 13:36-43",
                "Wisdom of Solomon 12:13",
                "Wisdom of Solomon 16-19",
				"Isaiah 44:6-8",
				"Psalm 86:11-17"
		));
		
		$this->createReadings(58, 'B', array(
				"2 Samuel 7:1-14a",
				"Psalm 89:20-37",
				"Ephesians 2:11-22",
				"Mark 6:30-34",
				"Mark 6:53-56",
				"Jeremiah 23:1-6",
				"Psalm 23"
		));
		
		$this->createReadings(58, 'C', array(
				"Amos 8:1-12",
				"Psalm 52",
				"Colossians 1:15-28",
				"Luke 10:38-42",
				"Genesis 18:1-10a",
				"Psalm 15"
		));
		
		
		
		

		$this->createReadings(59, 'A', array(
				"Genesis 29:15-28",
				"Psalm 105:1-11",
				"Psalm 105:45bb",
				"Psalm 128",
				"Romans 8:26-39",
				"Matthew 13:31-33",
				"Matthew 13:44-52",
				"1 Kings 3:5-12",
				"Psalm 119:129-136"
		));
		
		$this->createReadings(59, 'B', array(
				"2 Samuel 11:1-15",
				"Psalm 14",
				"Ephesians 3:14-21",
				"John 6:1-21",
				"2 Kings 4:42-44",
				"Psalm 145:10-18"
		));
		
		$this->createReadings(59, 'C', array(
				"Hosea 1:2-10",
				"Psalm 85",
				"Colossians 2:6-19",
				"Luke 11:1-13",
				"Genesis 18:20-32",
				"Psalm 138"
		));
		
		
		
		

		$this->createReadings(60, 'A', array(
				"Genesis 32:22-31",
				"Psalm 17:1-7",
				"Psalm 17:15",
				"Romans 9:1-5",
				"Matthew 14:13-21",
				"Isaiah 55:1-5",
				"Psalm 145:8-9",
				"Psalm 145:14-21"
		));
		
		$this->createReadings(60, 'B', array(
				"2 Samuel 11:26-12:13a",
				"Psalm 51:1-12",
				"Ephesians 4:1-16",
				"John 6:24-35",
				"Exodus 16:2-4",
				"Exodus 16:9-15",
				"Psalm 78:23-29"
		));
		
		$this->createReadings(60, 'C', array(
				"Hosea 11:1-11",
				"Psalm 107:1-9",
				"Psalm 107:43",
				"Colossians 3:1-11",
				"Luke 12:13-21",
				"Ecclesiastes 1:2",
				"Ecclesiastes 1:12-14",
				"Ecclesiastes 2:18-23",
				"Psalm 49:1-12"
		));
		
		
		
		
		

		$this->createReadings(61, 'A', array(
				"Genesis 37:1-4",
				"Genesis 37:12-28",
				"Psalm 105:1-6",
				"Psalm 105:16-22",
				"Psalm 105:45",
				"Romans 10:5-15",
				"Matthew 14:22-33",
				"1 Kings 19:9-18",
				"Psalm 85:8-13"
		));
		
		$this->createReadings(61, 'B', array(
				"2 Samuel 18:5-9",
				"2 Samuel 18:15",
				"2 Samuel 18:31-33",
				"Psalm 130",
				"Ephesians 4:25-5:2",
				"John 6:35",
				"John 6:41-51",
				"1 Kings 19:4-8",
				"Psalm 34:1-8"
		));
		
		$this->createReadings(61, 'C', array(
				"Isaiah 1:1",
				"Isaiah 1:10-20",
				"Psalm 50:1-8",
				"Psalm 50:22-23",
				"Hebrews 11:1-3",
				"Hebrews 11:8-16",
				"Luke 12:32-40",
				"Genesis 15:1-6",
				"Psalm 33:12-22"
		));
		
		
		
		
		
		$this->createReadings(62, 'A', array(
				"Genesis 45:1-15",
				"Psalm 133",
				"Romans 11:1-2a",
				"Romans 11:29-32",
				"Matthew 15:10-28",
				"Isaiah 56:1",
				"Isaiah 56:6-8",
				"Psalm 67"
		));

		$this->createReadings(62, 'B', array(
				"1 Kings 2:10-12",
				"1 Kings 3:3-14",
				"Psalm 111",
				"Ephesians 5:15-20",
				"John 6:51-58",
				"Proverbs 9:1-6",
				"Psalm 34:9-14"
		));
		

		$this->createReadings(62, 'C', array(
				"Isaiah 5:1-7",
				"Psalm 80:1-2",
				"Psalm 80:8-19",
				"Hebrews 11:29-12:2",
				"Luke 12:49-56",
				"Jeremiah 23:23-29",
				"Psalm 82"
		));
		
		
		
		
		

		$this->createReadings(63, 'A', array(
				"Exodus 1:8-2:10",
				"Psalm 124",
				"Romans 12:1-8",
				"Matthew 16:13-20",
				"Isaiah 51:1-6",
				"Psalm 138"
		));

		$this->createReadings(63, 'B', array(
				"1 Kings 8:1",
				"1 Kings 8:6",
				"1 Kings 8:10-11",
				"1 Kings 8:22-30",
				"1 Kings 8:41-43",
				"Psalm 84",
				"Ephesians 6:10-20",
				"John 6:56-69",
				"Joshua 24:1-2a",
				"Joshua 24:14-18",
				"Psalm 34:15-22"
		));
		
		$this->createReadings(63, 'C', array(
				"Jeremiah 1:4-10",
				"Psalm 71:1-6",
				"Hebrews 12:18-29",
				"Luke 13:10-17",
				"Isaiah 58:9b-14",
				"Psalm 103:1-8"
		));
		
		
		
		
		
		

		$this->createReadings(64, 'A', array(
				"Exodus 3:1-15",
				"Psalm 105:1-6",
				"Psalm 105:23-26",
				"Psalm 105:45b",
				"Romans 12:9-21",
				"Matthew 16:21-28",
				"Jeremiah 15:15-21",
				"Psalm 26:1-8"
		));
		
		$this->createReadings(64, 'B', array(
				"Song of Solomon 2:8-13",
				"Psalm 45:1-2",
				"Psalm 45:6-9",
				"James 1:17-27",
				"Mark 7:1-8",
				"Mark 7:14-15",
				"Mark 7:21-23",
				"Deuteronomy 4:1-2",
				"Deuteronomy 6-9"
				
		));
		
		$this->createReadings(64, 'C', array(
				"Jeremiah 2:4-13",
				"Psalm 81:1",
				"Psalm 81:10-16",
				"Hebrews 13:1-8",
				"Hebrews 13:15-16",
				"Luke 14:1",
				"Luke 14:7-14",
                "Sirach 10:12-18",
				"Proverbs 25:6-7",
				"Psalm 112"
		
		));
		
		
		
		

		$this->createReadings(65, 'A', array(
				"Exodus 12:1-14",
				"Psalm 149",
				"Romans 13:8-14",
				"Matthew 18:15-20",
				"Ezekiel 33:7-11",
				"Psalm 119:33-40"
		));
		
		$this->createReadings(65, 'B', array(
				"Proverbs 22:1-2",
				"Proverbs 22:8-9",
				"Proverbs 22:22-23",
				"Psalm 125",
				"James 2:1-17",
				"Mark 7:24-37",
				"Isaiah 35:4-7a",
				"Psalm 146"
		));
		
		$this->createReadings(65, 'C', array(
				"Jeremiah 18:1-11",
				"Psalm 139:1-6",
				"Psalm 139:13-18",
				"Philemon 1:1-21",
				"Luke 14:25-33",
				"Deuteronomy 30:15-20",
				"Psalm 1"
		));
		
		
		

		$this->createReadings(66, 'A', array(
				"Exodus 14:19-31",
				"Psalm 114",
				"Exodus 15:1b-11",
				"Exodus 15:20-21",
				"Romans 14:1-12",
				"Matthew 18:21-35",
				"Genesis 50:15-21",
				"Psalm 103:1-13"
		));
		
		$this->createReadings(66, 'B', array(
				"Proverbs 1:20-33",
				"Psalm 19",
                "Wisdom of Solomon 7:26-8:1",
                "James 3:1-12",
                "Mark 8:27-38",
                "Isaiah 50:4-9a",
                "Psalm 116:1-9",
				// "1 Timothy 1:12-17",
				// "Luke 15:1-10",
				// "Exodus 32:7-14",
				// "Psalm 51:1-10"
		));
		
		$this->createReadings(66, 'C', array(
				"Jeremiah 4:11-12",
				"Jeremiah 4:22-28",
				"Psalm 14",
				"Mark 8:27-38",
				"Isaiah 50:4-9",
				"Psalm 116:1-9"
		));
		
		
		
		

		$this->createReadings(68, 'A', array(
				"Exodus 16:2-15",
				"Psalm 105:1-6",
				"Psalm 105:37-45",
				"Philippians 1:21-30",
				"Matthew 20:1-16",
				"Jonah 3:10-4:11",
				"Psalm 145:1-8"
		));
		
		$this->createReadings(68, 'B', array(
				"Proverbs 31:10-31",
				"Psalm 1",
				"James 3:13-4:3",
				"James 4:7-8a",
				"Mark 9:30-37",
                "Wisdom of Solomon 1:16-2:1",
                "Wisdom of Solomon 2:12-22",
				"Jeremiah 11:18-20",
				"Psalm 54"
		));
		
		$this->createReadings(68, 'C', array(
				"Jeremiah 8:18-9:1",
				"Psalm 79:1-9",
				"1 Timothy 2:1-7",
				"Luke 16:1-13",
				"Amos 8:4-7",
				"Psalm 113"
		));
		
		
		
		
		

		$this->createReadings(69, 'A', array(
				"Exodus 17:1-7",
				"Psalm 78:1-4",
				"Psalm 78:12-16",
				"Philippians 2:1-13",
				"Matthew 21:23-32",
				"Ezekiel 18:1-4",
				"Ezekiel 18:25-32",
				"Psalm 25:1-9"
		));
		
		$this->createReadings(69, 'B', array(
				"Esther 7:1-6",
				"Esther 7:9-10",
				"Esther 9:20-22",
				"Psalm 124",
				"James 5:13-20",
				"Mark 9:38-50",
				"Numbers 11:4-6",
				"Numbers 11:10-16",
				"Numbers 11:24-29",
				"Psalm 19:7-14"
		));
		
		$this->createReadings(69, 'C', array(
				"Jeremiah 32:1-3a",
				"Jeremiah 32:6-15",
				"Psalm 91:1-6",
				"Psalm 91:14-16",
				"1 Timothy 6:6-19",
				"Luke 16:19-31",
				"Amos 6:1",
				"Amos 6:4-7",
				"Psalm 146"
		));
		
		
		
		
		

		$this->createReadings(70, 'A', array(
				"Exodus 20:1-4",
				"Exodus 20:7-9",
				"Exodus 20:12-20",
				"Psalm 19",
				"Philippians 3:4b-14",
				"Matthew 21:33-46",
				"Isaiah 5:1-7",
				"Psalm 80:7-15"
		));
		
		$this->createReadings(70, 'B', array(
				"Job 1:1",
				"Job 2:1-10",
				"Psalm 26",
				"Hebrews 1:1-4",
				"Hebrews 2:5-12",
				"Mark 10:2-16",
				"Genesis 2:18-24",
				"Psalm 8"
		));
		
		$this->createReadings(70, 'C', array(
				"Lamentations 1:1-6",
				"Lamentations 3:19-26",
				"Psalm 137",
				"2 Timothy 1:1-14",
				"Luke 17:5-10",
				"Habakkuk 1:1-4",
				"Habakkuk 2:1-4",
				"Psalm 37:1-9"
		));
		
		
		
		
		$this->createReadings(71, 'A', array(
				"Exodus 32:1-14",
				"Psalm 106:1-6",
				"Psalm 106:19-23",
				"Philippians 4:1-9",
				"Matthew 22:1-14",
				"Isaiah 25:1-9",
				"Psalm 23"
		));
		
		$this->createReadings(71, 'B', array(
				"Job 23:1-9",
				"Job 23:16-17",
				"Psalm 22:1-15",
				"Hebrews 4:12-16",
				"Mark 10:17-31",
				"Amos 5:6-7",
				"Amos 5:10-15",
				"Psalm 90:12-17"
		));
		
		$this->createReadings(71, 'C', array(
				"Jeremiah 29:1",
				"Jeremiah 29:4-7",
				"Psalm 66:1-12",
				"2 Timothy 2:8-15",
				"Luke 17:11-19",
				"2 Kings 5:1-3",
				"2 Kings 5:7-15c",
				"Psalm 111"
		));
		
		
		
		

		$this->createReadings(72, 'A', array(
				"Exodus 33:12-23",
				"Psalm 99",
				"1 Thessalonians 1:1-10",
				"Matthew 22:15-22",
				"Isaiah 45:1-7",
				"Psalm 96:1-13"
		));
		
		$this->createReadings(72, 'B', array(
				"Job 38:1-7",
				"Job 38:34-41",
				"Psalm 104:1-9",
				"Psalm 104:24",
				"Psalm 104:35b",
				"Hebrews 5:1-10",
				"Mark 10:35-45",
				"Isaiah 53:4-12",
				"Psalm 91:9-16"
		));
		
		$this->createReadings(72, 'C', array(
				"Jeremiah 31:27-34",
				"Psalm 119:97-104",
				"2 Timothy 3:14-4:5",
				"Luke 18:1-8",
				"Genesis 32:22-31",
				"Psalm 121"
		));
		
		
		
		
		

		$this->createReadings(73, 'A', array(
				"Deuteronomy 34:1-12",
				"Psalm 90:1-6",
				"Psalm 90:13-17",
				"1 Thessalonians 2:1-8",
				"Matthew 22:34-46",
				"Leviticus 19:1-2",
				"Leviticus 19:15-18",
				"Psalm 1"
		));
		
		$this->createReadings(73, 'B', array(
				"Job 42:1-6",
				"Job 42:10-17",
				"Psalm 34:1-8",
				"Psalm 34:19-22",
				"Hebrews 7:23-28",
				"Mark 10:46-52",
				"Jeremiah 31:7-9",
				"Psalm 126"
		));
		
		$this->createReadings(73, 'C', array(
				"Joel 2:23-32",
				"Psalm 65",
				"2 Timothy 4:6-8",
				"2 Timothy 4:16-18",
				"Luke 18:9-14",
                "Sirach 35:12-17",
				"Jeremiah 14:7-10",
				"Jeremiah 14:19-22",
				"Psalm 84:1-7"
		));
		
		
		
		

		$this->createReadings(74, 'A', array(
				"Joshua 3:7-17",
				"Psalm 107:1-7",
				"Psalm 107:33-37",
				"1 Thessalonians 2:9-13",
				"Matthew 23:1-12",
				"Micah 3:5-12",
				"Psalm 43"
		));
		
		$this->createReadings(74, 'B', array(
				"Ruth 1:1-18",
				"Psalm 146",
				"Hebrews 9:11-14",
				"Mark 12:28-34",
				"Deuteronomy 6:1-9",
				"Psalm 119:1-8"
		));
		
		$this->createReadings(74, 'C', array(
				"Habakkuk 1:1-4",
				"Habakkuk 2:1-4",
				"Psalm 119:137-144",
				"2 Thessalonians 1:1-4",
				"2 Thessalonians 1:11-12",
				"Luke 19:1-10",
				"Isaiah 1:10-18",
				"Psalm 32:1-7"
		));
		
		
		
		
		

		$this->createReadings(76, 'A', array(
				"Joshua 24:1-3a",
				"Joshua 24:14-25",
				"Psalm 78:1-7",
				"1 Thessalonians 4:13-18",
				"Matthew 25:1-13",
                "Wisdom of Solomon 6:12-16",
                "Wisdom of Solomon 6:17-20",
				"Amos 5:18-24",
				"Psalm 70"
		));
		
		$this->createReadings(76, 'B', array(
				"Ruth 3:1-5",
				"Ruth 4:13-17",
				"Psalm 127",
				"Hebrews 9:24-28",
				"Mark 12:38-44",
				"1 Kings 17:8-16",
				"Psalm 146"
		));
		
		$this->createReadings(76, 'C', array(
				"Haggai 1:15-2:9",
				"Psalm 145:1-5",
				"Psalm 145:17-21",
				"Psalm 98",
				"2 Thessalonians 2:1-5",
				"2 Thessalonians 2:13-17",
				"Luke 20:27-38",
				"Job 19:23-27a",
				"Psalm 17:1-9"
		));
		
		
		
		
		

		$this->createReadings(77, 'A', array(
				"Judges 4:1-7",
				"Psalm 123",
				"1 Thessalonians 5:1-11",
				"Matthew 25:14-30",
				"Zephaniah 1:7",
				"Zephaniah 1:12-18",
				"Psalm 90:1-12"
		));
		
		$this->createReadings(77, 'B', array(
				"1 Samuel 1:4-20",
				"1 Samuel 2:1-10",
				"Hebrews 10:11-25",
				"Mark 13:1-8",
				"Daniel 12:1-3",
				"Psalm 16"
		));
		
		$this->createReadings(77, 'C', array(
				"Isaiah 65:17-25",
				"Isaiah 12",
				"2 Thessalonians 3:6-13",
				"Luke 21:5-19",
				"Malachi 4:1-2a",
				"Psalm 98"
		));
		
		
		
		
		

		$this->createReadings(78, 'A', array(
				"Ezekiel 34:11-16",
				"Ezekiel 34:20-24",
				"Psalm 100",
				"Ephesians 1:15-23",
				"Matthew 25:31-46",
				"Ezekiel 34:11-16",
				"Ezekiel 34:20-24",
				"Psalm 95:1-7a"
		));
		
		$this->createReadings(78, 'B', array(
				"2 Samuel 23:1-7",
				"Psalm 132:1-18",
				"Revelation 1:4b-8",
				"John 18:33-37",
				"Daniel 7:9-10",
				"Daniel 7:9-13-14",
				"Psalm 93"
		));
		
		$this->createReadings(78, 'C', array(
				"Jeremiah 23:1-6",
				"Luke 1:68-79",
				"Colossians 1:11-20",
				"Luke 23:33-43",
				"Psalm 46"
		));
		
		
		
		
		
		$this->createReadings(80, '*', array(
				"Malachi 3:1-4",
				"Psalm 84",
				"Psalm 24:7-10",
				"Hebrews 2:14-18",
				"Luke 2:22-40"
		));
		

		$this->createReadings(29, '*', array(
				"Isaiah 7:10-14",
				"Psalm 45",
				"Psalm 40:5-10",
				"Hebrews 10:4-10",
				"Luke 1:26-38"
		));
		
		$this->createReadings(79, '*', array(
				"1 Samuel 2:1-10",
				"Psalm 113",
				"Romans 12:9-16b",
				"Luke 1:39-57"
		));
		
		$this->createReadings(67, '*', array(
				"Numbers 21:4b-9",
				"Psalm 98:1-5",
				"Psalm 78:1-2",
				"Psalm 78:34-38",
				"1 Corinthians 1:18-24",
				"John 3:13-17"
		));
		

		$this->createReadings(75, 'A', array(
				"Revelation 7:9-17",
				"Psalm 34:1-10",
				"Psalm 34:22",
				"1 John 3:1-3",
				"Matthew 5:1-12"
		));

        $this->createReadings(75, 'B', array(
            "Wisdom of Solomon 3:1-9",
            "Isaiah 25:6-9",
            "Psalm 24",
            "Revelation 21:1-6a",
            "John 11:32-44",
        ));

        $this->createReadings(75, 'C', array(
            "Daniel 7:1-3",
            "Daniel 7:15-18",
            "Psalm 149",
            "Ephesians 1:11-23",
            "Luke 6:20-31",
        ));

        $this->createReadings(81, 'A', array(
            "Deuteronomy 8:7-18",
            "Psalm 65",
            "2 Corinthians 9:6-15",
            "Luke 17:11-19",
        ));

        $this->createReadings(81, 'B', array(
            "Joel 2:21-27",
            "Psalm 126",
            "1 Timothy 2:1-7",
            "Matthew 6:25-33",
        ));

        $this->createReadings(81, 'C', array(
            "Deuteronomy 26:1-11",
            "Psalm 100",
            "Philippians 4:4-9",
            "John 6:25-35",
        ));
		
	}


    private function createReadings($week_id, $year, $refs) {

        $brp = new \AscentCreative\BibleRef\Parser();

        foreach($refs as $ref) {

            $parsed = $brp->parseBibleRef($ref);

            $parsed['biblerefable_type'] = "AscentCreative\Lectionary\Models\Week";
            $parsed['biblerefable_id'] = $week_id;
            $parsed['biblerefable_key'] = $year;

            $ref = BibleRef::create($parsed);
            

        }

    }



}
