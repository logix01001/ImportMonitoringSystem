<?php

namespace App\Exports;

use App\bill_of_lading;
use App\bill_of_lading_commodity;
use App\Bill_of_Lading_Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AllRecordExportNew implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $_factory,$_start,$_end;
    public function __construct($factory,$start,$end)
    {

        $this->_factory = $factory;
        $this->_start = $start;
        $this->_end = $end;

    }
    public function collection()
    {
        //

        $select = explode(",", getenv('SELECT_EXPORT'));
        $list_of_BOL = bill_of_lading::select( $select)
            ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no');
			//->whereMonth('processing_date',03)
			//->limit(100)


        if(strtolower($this->_factory) != "all"){

            $list_of_BOL  =  $list_of_BOL->where('factory',$this->_factory);

        }

        $list_of_BOL = $list_of_BOL
        ->whereBetween('estimated_time_arrival',[$this->_start,$this->_end])
        ->orderBy('estimated_time_arrival','ASC')
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
