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

class ContainerAtPortExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    private $factory;
    private $container_type;
    private $factory_all;
    private $container_all;

    public function __construct($factory,$container_type,$factory_all,$container_all){
        $this->factory = $factory;
        $this->container_type = $container_type;
        $this->factory_all = $factory_all;
        $this->container_all = $container_all;
       

    }


    public function collection()
    {
        //

        $factory = $this->factory;
        $date_request = date('Y-m-d');
        $container_type = $this->container_type;
        $factory_all = $this->factory_all;
        $container_all = $this->container_all;

        $select = explode(",", getenv('SELECT_EXPORT'));
        $list_of_BOL = Container::select($select)
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1);
       
        if ($factory_all == 'false') {
            $list_of_BOL = $list_of_BOL->where('factory', $factory);
        }  
        if ($container_all == 'false') {
            $list_of_BOL = $list_of_BOL->where('container_type', $container_type);
        } 
        
        $date_request = date('Y-m-d');
        
      
       
        $list_of_BOL = $list_of_BOL->where('actual_discharge', '<=', $date_request)
                        ->whereNull('pull_out')
                        //->whereNull('dismounted_cy')
                        // ->where(function ($query) use ($date_request) {
                        //     $query->whereNull('pull_out');
                        //     $query->orWhere('pull_out', '>', $date_request);
                        // })
                        ->orderBy('actual_discharge','ASC')
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
