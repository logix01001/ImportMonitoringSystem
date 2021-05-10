<?php


namespace App\libraries;


class Adddate
{

    private static $_selecteddate,$_addeddays;

    public static function newdate($selecteddate , $addeddays){

        self::$_selecteddate = $selecteddate;
        self::$_addeddays = $addeddays;

        return self::add_date();

    }

    private static function add_date(){


        $start_date = "2015-03-02";
        $date = strtotime(self::$_selecteddate);
        $date = strtotime(self::$_addeddays, $date);
        return date('Y-m-d', $date);

    }
}
