<?php

namespace App\Exports;

use App\bill_of_lading;
use App\bill_of_lading_commodity;
use App\Bill_of_Lading_Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class AllRecordExport implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //

        $select = explode(",", getenv('SELECT_EXPORT'));
        $list_of_BOL = bill_of_lading::select( $select)
            ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
			//->whereMonth('processing_date',03)
			//->limit(100)
			->get();
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
