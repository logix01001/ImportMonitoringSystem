<?php


namespace App\Libraries;


class DateRange {

    private static $_start,$_end,$_interval;

    public static function get_range($start, $end,$interval  = 1){

        self::$_start = $start;
        self::$_end = $end;
        self::$_interval = $interval ;
        return self::range();
    }

    private static function range(){


        $period = new \DatePeriod(
            new \DateTime( self::$_start),
            new \DateInterval('P'.self::$_interval.'D'),
            new \DateTime(self::$_end . " +1 day")
        );
        $dates = [];
        foreach ($period as $key => $value) {
            $dates[] = $value->format('Y-m-d');
        }
        return $dates;

    }


}
