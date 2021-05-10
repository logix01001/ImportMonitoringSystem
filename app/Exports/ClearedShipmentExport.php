<?php

namespace App\Exports;

use App\Container;

use App\bill_of_lading;
use App\Bill_of_Lading_Invoice;
use App\bill_of_lading_commodity;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
;

class ClearedShipmentExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //

        $select = explode(",", getenv('SELECT_EXPORT'));
        $Query =   bill_of_lading::select( $select )
        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
        ->whereQuantity(1)
        ->whereNotNull('return_date')
        ->whereNotNull('unload')
        ->get();
    
    
        $list_of_BOL = $Query ;

        $data = [];
        $index = 0;
        if(count($list_of_BOL) > 0){

            $Obj = new \App\Libraries\BillOfLading($list_of_BOL);

            $data = $Obj->getData();

        }
        
        return collect($data);
    }

    public function headings(): array
    {
        return explode(",", getenv('SELECT_EXPORT_HEADER'));
    }
}
