<?php

namespace App\Exports;

use App\bill_of_lading;
use App\Container;
use Maatwebsite\Excel\Concerns\FromCollection;

class BOLExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return bill_of_lading::all();
        $BOL = bill_of_lading::select('bl_no','factory','estimated_time_arrival','remarks_of_docs','container_type')->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')->get();
        foreach($BOL as $row){
            $row['container_number'] = Container::select('container_number')->where('bl_no_fk','=',$row['bl_no'])->count();
        }
       
        return $BOL;
    }
}
