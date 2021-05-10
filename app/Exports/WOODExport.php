<?php

namespace App\Exports;
use App\Container;

use App\bill_of_lading;
use App\bill_of_lading_commodity;
use App\Wood;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WOODExport implements FromCollection,WithHeadings,WithTitle,WithEvents,ShouldAutoSize
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

    public function registerEvents(): array
    {
        $styleArray = [
                'font' => [
                    'bold' => true,
                ]
        ];

        return [
            AfterSheet::class => function(AfterSheet $event) use ( $styleArray ) {
                
                $event->sheet->getStyle('A1:G1')->applyFromArray( $styleArray );

            },
        ];
    }

    public function collection()
    {
        //

        foreach(Wood::select('name')->get() as $wood){
            $Woods[] = $wood['name'];
        }
        
        $BOL_IDS = bill_of_lading_commodity::select('bl_no_fk')
        ->join('bill_of_ladings','bill_of_lading_commodities.bl_no_fk','bill_of_ladings.bl_no')
        ->where((string)  $this->type_date, $this->date_endorse)
        ->distinct() 
        ->whereIn('commodity',
            $Woods
        )->get();
    
        if(count($BOL_IDS) > 0){
          
            foreach($BOL_IDS as $ids){
                $list_of_bl[] = $ids['bl_no_fk'];
            }
            
            $BOL =  bill_of_lading::select('tsad_no','bl_no','pod','factory','estimated_time_arrival','remarks_of_docs','container_type','container_number')
            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
            ->wherePod('SOUTH')
            ->orderBy('factory','DESC')
            ->orderBy('container_type','ASC')
            ->orderBy('bl_no','ASC')
            ->where((string)  $this->type_date, $this->date_endorse)
            ->distinct()->get();
            
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
                //CHECK IF IS IN WOOD
                if(!in_array($row['bl_no'],$list_of_bl)){
                    $i++;
                    continue;
                }
        
        
                if(!isset($current_bl)){
                    $current_bl = $row['bl_no'];
                    $container_type = $row['container_type'];
                    $total_quantity = 0;
                }
        
                if($x > 0){
                    if(
                        $BOL[$i]['bl_no'] == $current_bl &&
                        $BOL[$i]['container_type'] == $container_type
                        
                        ){
                        
        
                        $current_bl = $row['bl_no'];
                        $container_type = $row['container_type'];
        
                        $data[$x]['tsad_no'] = '';
                        $data[$x]['bl_no'] = '';
                        $data[$x]['factory'] = '';
                        $data[$x]['NoContainers'] = '';
                        $data[$x]['container_type'] = '';
                        $data[$x]['estimated_time_arrival'] = '';
                        $data[$x]['container_number'] =  $row['container_number'];
                     
                     
                    
        
                    }else{
        
                        $current_bl = $row['bl_no'];
                        $container_type = $row['container_type'];
                        
                        $data[$x]['tsad_no'] = $row['tsad_no'];
                        $data[$x]['bl_no'] = $row['bl_no'];
                        $data[$x]['factory'] = $row['factory'];
                        $row['NoContainers'] = Container::whereBlNoFk($row['bl_no'])->whereContainerType($row['container_type'])->sum('quantity');
                        $data[$x]['NoContainers'] = $row['NoContainers'];
                        $data[$x]['container_type'] = $row['container_type'];
                        $data[$x]['estimated_time_arrival'] = $row['estimated_time_arrival'];
                        $data[$x]['container_number'] = $row['container_number'];
                      
                      
        
                    }
                }else{
                    $data[$x]['tsad_no'] = $row['tsad_no'];
                    $data[$x]['bl_no'] = $row['bl_no'];
                    $data[$x]['factory'] = $row['factory'];
                    $row['NoContainers'] = Container::whereBlNoFk($row['bl_no'])->whereContainerType($row['container_type'])->sum('quantity');
                    $data[$x]['NoContainers'] = $row['NoContainers'];
                    $data[$x]['container_type'] = $row['container_type'];
                    $data[$x]['estimated_time_arrival'] = $row['estimated_time_arrival'];
                    $data[$x]['container_number'] = $row['container_number'];
                  
                   
                }
                
                    $i++;
                    $x++;
                   
            }
            
        }else{
            $data = [];
        }
      
    
        return  collect($data);

    }

    public function headings(): array
    {
        return [
            'T-SAD',
            'BL',
            'FACTORY',
            'NO.OF.CONTAINERS',
            'CNTR TYPE',
            'ETA',
            'CONTAINERS',
        ];
    }

    public function title(): string
    {
        return 'WOOD ONLY';
    }
}
