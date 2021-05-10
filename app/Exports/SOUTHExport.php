<?php

namespace App\Exports;
use App\Container;

use App\bill_of_lading;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SOUTHExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $date_endorse;
    private $type_date;

    public function __construct($date,$type_date){
        $this->date_endorse = $date;
        $this->type_date = $type_date;
    }


    public function collection()
    {

        $BOL =  bill_of_lading::select('bl_no','pod','factory','estimated_time_arrival','remarks_of_docs','container_type')
        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
        ->wherePod('SOUTH')
        ->where((string) $this->type_date ,$this->date_endorse)
        ->orderBy('factory','DESC')->distinct()->get();
        $data = [];
        $i = 0;
        $x = 0;
        $len = count($BOL);
    
    
        foreach($BOL as $row){
            //SKIP IF CONTAINER QUANITITY IS ZERO
            if( Container::whereBlNoFk($row['bl_no'])->whereContainerType($row['container_type'])->sum('quantity') == 0){
                $i++;
                continue;
            }


            if(!isset($currentFactory)){
                $currentFactory = $row['factory'];
                $total_quantity = 0;
            }
            $data[$x]['bl_no'] = $row['bl_no'];
            $data[$x]['pod'] = $row['pod'];
            $data[$x]['factory'] = $row['factory'];
          
    
            $row['NoContainers'] = Container::whereBlNoFk($row['bl_no'])->whereContainerType($row['container_type'])->sum('quantity');

           


            $data[$x]['NoContainers'] = $row['NoContainers'];
            $data[$x]['container_type'] = substr($row['container_type'],0,2);
            $data[$x]['estimated_time_arrival'] = $row['estimated_time_arrival'];
            $data[$x]['remarks_of_docs'] = $row['remarks_of_docs'];
          
            $total_quantity += $row['NoContainers'];
           
           
            if ($i == $len - 1) {
                     $x++;
                    $data[$x]['bl_no'] = '';
                    $data[$x]['pod'] = '';
                    $data[$x]['factory'] = '';
                   
                    $data[$x]['NoContainers'] =  $total_quantity;
                    $data[$x]['container_type'] = '';
                    $data[$x]['estimated_time_arrival'] = '';
                    $data[$x]['remarks_of_docs'] = '';
                    $total_quantity = 0;
            }else{
                $i++;
                $x++;
                if($BOL[$i]['factory'] != $currentFactory){
                    

                 

                    $currentFactory = $BOL[$i]['factory'];
    
                    $data[$x]['bl_no'] = '';
                    $data[$x]['pod'] = '';
                    $data[$x]['factory'] = '';
                   
                    $data[$x]['NoContainers'] =  $total_quantity;
                    $data[$x]['container_type'] = '';
                    $data[$x]['estimated_time_arrival'] = '';
                    $data[$x]['remarks_of_docs'] = '';
                    $total_quantity = 0;
                    $x++;
                   
     
                }
            }
               
        }
   

     
        return  collect($data);
    }

    public function headings(): array
    {
        return [
            'BL NUMBER',
            'POD',
            'FACTORY',
            'NO.OF.CONTAINER',
            'CNTR TYPE',
            'ETA',
            'REMARKS',
        ];
    }

    public function title(): string
    {
        return 'SOUTH';
    }
}
