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

class LOGISTICSREPORTSExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $vessel;

    private $ETA;
    private $ATA;
    private $ATB;
    private $port;
    private $gatepass;
    private $delivery;
    private $unloaded;
    private $returned;
    private $start;
    private $end;
   
   

    public function __construct($vessel,$ETA,$ATA,$ATB,$port,$gatepass,$delivery, $unloaded,$returned,$start,$end ){

        $this->vessel = $vessel;
        $this->ETA = $ETA;
        $this->ATA = $ATA;
        $this->ATB = $ATB;
        $this->port = $port;
        $this->gatepass = $gatepass;
        $this->delivery = $delivery;
        $this->unloaded = $unloaded;
        $this->returned = $returned;
        $this->start = $start;
        $this->end = $end;

    }

    public function collection()
    {
        //

        $vessel = $this->vessel;
        $ETA = $this->ETA;
        $ATA = $this->ATA;
        $ATB = $this->ATB;

        $port = $this->port;
        $gatepass = $this->gatepass;
        $delivery = $this->delivery;
        $unloaded = $this->unloaded;
        $returned = $this->returned;

        $start = $this->start;
        $end = $this->end;


        $list_of_bl = [];
        $list_of_bol = [];
        
        if( $vessel == 'true' ){
    
            $list_of_bol = Bill_of_lading::select('bl_no');
    
            if($ETA == 'true'){
                $list_of_bol =  $list_of_bol->orWhereBetween('estimated_time_arrival',[$start,$end]);
            }
    
            if($ATA == 'true'){
                $list_of_bol =  $list_of_bol->orWhereBetween('actual_time_arrival',[$start,$end]);
            }
    
            if($ATB == 'true'){
                $list_of_bol =  $list_of_bol->orWhereBetween('actual_berthing_date',[$start,$end]);
            }
                 
                            // ->WhereBetween('estimated_time_arrival',[$start,$end])
                            
                            // ->orWhereBetween('actual_time_arrival',[$start,$end])
            $list_of_bol =  $list_of_bol->get();
     
            foreach(  $list_of_bol as $row ){
                $list_of_bl[] = $row['bl_no'];
            }
    
        }
    
        if( $port == 'true' ){
    
            $list_of_bol = Container::select('bl_no_fk')->distinct()
                            ->WhereBetween('actual_discharge',[$start,$end])
                            ->get();
     
            foreach(  $list_of_bol as $row ){
                $list_of_bl[] = $row['bl_no_fk'];
            }
          
        }
    
        if( $gatepass == 'true' ){
    
            $list_of_bol = Container::select('bl_no_fk')->distinct()
                            ->WhereBetween('actual_gatepass',[$start,$end])
                            ->get();
     
            foreach(  $list_of_bol as $row ){
                $list_of_bl[] = $row['bl_no_fk'];
            }
          
        }
    
        if( $delivery == 'true' ){
    
            $list_of_bol = Container::select('bl_no_fk')->distinct()
                            ->WhereBetween('pull_out',[$start,$end])
                            ->get();
     
            foreach(  $list_of_bol as $row ){
                $list_of_bl[] = $row['bl_no_fk'];
            }
          
        }
    
        if( $unloaded == 'true' ){
    
            $list_of_bol = Container::select('bl_no_fk')->distinct()
                            ->WhereBetween('unload',[$start,$end])
                            ->get();
     
            foreach(  $list_of_bol as $row ){
                $list_of_bl[] = $row['bl_no_fk'];
            }
          
        }
    
        if( $returned == 'true' ){
    
            $list_of_bol = Container::select('bl_no_fk')->distinct()
                            ->WhereBetween('return_date',[$start,$end])
                            ->get();
     
            foreach(  $list_of_bol as $row ){
                $list_of_bl[] = $row['bl_no_fk'];
            }
          
        }
        $list_of_bl = array_unique($list_of_bl);
    
        $select = explode(",", getenv('SELECT_EXPORT'));
        $list_of_BOL = Bill_of_lading::select($select)
                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->orderBy('bill_of_ladings.id', 'DESC')
                        ->whereIn('bl_no',$list_of_bl)
                        ->get();

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
