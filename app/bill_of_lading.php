<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class bill_of_lading extends Model
{
    //
    protected $fillable = ['connecting_vessel','assessment_tag','incoterm'];


    public function containers()
    {
        return $this->hasMany('App\Container', 'bl_no_fk', 'bl_no');
    }


    public function containers_by_id($bl_no = null)
    {
        return $this->hasMany('App\Container', 'bl_no_fk', 'bl_no')->where('bl_no_fk',$bl_no);
    }

    public function csvbatch()
    {
        return $this->whereNull('csv_batch_code')->get();
    }


    public static function csvbatchstatic()
    {
        return self::whereNull('csv_batch_code');
    }

}
