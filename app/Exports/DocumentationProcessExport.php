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

class DocumentationProcessExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {

         //$select = explode(",", getenv('SELECT_EXPORT'));
         $select = ['factory','bl_no','pod','volume','container_type','remarks_of_docs','estimated_time_arrival','date_endorse','tsad_no'];
         $Query =  bill_of_lading::select( $select )
                 ->distinct()
                 ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                 ->whereQuantity(1)
                 ->whereNull('e2m')
                 ->orWhereNull('date_endorse')
                 ->orWhereNull('date_approve_ip')
                 ->orWhereNull('assessment_tag')
                 ->orWhereNull('remarks_of_docs')
                 ->orWhereNull('tsad_no')
                 //->orderBy('registry_no', 'DESC')
                 ->orderBy('estimated_time_arrival', 'ASC')
                 ->get();
     
         $list_of_BOL = $Query ;
 
         $data = [];
         $index = 0;
         
         if(count($list_of_BOL) > 0){
 
         //  $Obj = new \App\Libraries\BillOfLading($list_of_BOL);
 
         //  $data = $Obj->getData();
 
            foreach($list_of_BOL as $row){
            
                $data[ $index ][] =  $row['factory'];
                $data[ $index ][] =  $row['bl_no'];
                $data[ $index ][] =  $row['pod'];
                $data[ $index ][] =  Container::where('bl_no_fk',$row['bl_no'])->where('container_type', $row['container_type'])->count();//$row['volume'];
                $data[ $index ][] =  $row['container_type'];
                $data[ $index ][] =  $row['remarks_of_docs'];
                $data[ $index ][] =  $row['estimated_time_arrival'];
                $data[ $index ][] =  $row['date_endorse'];
                $data[ $index ][] =  $row['tsad_no'];
            
                $index++;
            } 
 
         }
         
         return collect($data);
    }

    public function headings(): array
    {
        return [
            'FACTORY',
            'BL NO.',
            'POD',
            'VOLUME',
            'SIZE',
            'REMARKS',
            'ESTIMATED TIME ARRIVAL',
            'DATE ENDORSEMENT',
            'TSAD NO.',


        ];
    }

}
