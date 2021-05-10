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

class CONTAINERIRSExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $factory;

//{factory?}/{category?}/{reference?}/{pod?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}
    public function __construct($factory){
        $this->factory = $factory;


    }

    public function collection()
    {
        //
        $factory = $this->factory;
       
       
        $select = explode(",", getenv('SELECT_EXPORT'));
        $Query =   bill_of_lading::select( $select )
                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotIn('pod',['TBA'])
                            ->whereNotNull('dismounted_date')
                            ->where('dismounted_cy','IRS BACAO')
                            ->whereNull('unload');
    
        

        if ($factory != 'false') {

            $Query->where('factory', $factory);

        }   
        
       
       
        $list_of_BOL = $Query->get();

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
