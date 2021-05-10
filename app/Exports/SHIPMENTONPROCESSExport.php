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

class SHIPMENTONPROCESSExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $factory;

    private $date_request;
    private $reference;
    private $dateMonth;
    private $dateYear;
    private $start;
    private $end;
    private $pod;
//{factory?}/{category?}/{reference?}/{pod?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}
    public function __construct($factory,$reference,$pod,$date_request,$dateMonth,$dateYear,$start, $end ){
        $this->factory = $factory;

        $this->pod = $pod;
        $this->date_request = $date_request;
        $this->reference = $reference;
        $this->dateMonth = $dateMonth;
        $this->dateYear = $dateYear;
        $this->start = $start;
        $this->end = $end;

    }

    public function collection()
    {
        //
        $factory = $this->factory;
        $date_request = $this->date_request;
        $reference = $this->reference;
        $dateMonth = $this->dateMonth;
        $dateYear = $this->dateYear;
        $start = $this->start;
        $end = $this->end;
        $pod = $this->pod;
       
        
        // $select = explode(",", getenv('SELECT_EXPORT'));
        $select = ['factory','bl_no','pod','volume','container_type','remarks_of_docs','estimated_time_arrival','date_endorse','tsad_no'];
        $Query =   bill_of_lading::select( $select )
                            ->distinct()
                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotIn('pod',['TBA']);
    
        switch ($reference) {
            case 'D':
                $Query->where('actual_process',$date_request);
                break;
            case 'M':
                $month = explode('-', $dateMonth)[1];
                $year = explode('-', $dateMonth)[0];
                $Query->whereMonth('actual_process',$month);
                $Query->whereYear('actual_process',$year);
                break;
            case 'Y':
                $year = explode('-', $dateYear)[0];
                $Query->whereYear('actual_process',$year);
                break;
            default:
                $Query->whereBetween('actual_process', [$start, $end]);
        }

        if ($factory != 'false') {
            $Query->where('factory', $factory);
        }   
        
        if ($pod  != 'false') {
            $Query->wherePod($pod);
        }   
    
       
        $list_of_BOL = $Query->get();

        $data = [];
        $index = 0;
        if(count($list_of_BOL) > 0){

            // $Obj = new \App\Libraries\BillOfLading($list_of_BOL);

            // $data = $Obj->getData();

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
        // return explode(",", getenv('SELECT_EXPORT_HEADER'));
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
