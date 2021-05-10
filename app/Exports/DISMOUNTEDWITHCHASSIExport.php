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

class DISMOUNTEDWITHCHASSIExport implements FromCollection,WithHeadings,ShouldAutoSize
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
    private $cy;
//{factory?}/{category?}/{reference?}/{pod?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}
    public function __construct($factory,$reference,$cy,$date_request,$dateMonth,$dateYear,$start, $end ){
        $this->factory = $factory;

        $this->cy = $cy;
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
        $cy = $this->cy;
       
        $select = explode(",", getenv('SELECT_EXPORT'));
        $Query =   bill_of_lading::select($select)
                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotIn('pod',['TBA']);
    
        switch ($reference) {
            case 'D':
                $Query->where('dismounted_date',$date_request);
                break;
            case 'M':
                $month = explode('-', $dateMonth)[1];
                $year = explode('-', $dateMonth)[0];
                $Query->whereMonth('dismounted_date',$month);
                $Query->whereYear('dismounted_date',$year);
                break;
            case 'Y':
                $year = explode('-', $dateYear)[0];
                $Query->whereYear('dismounted_date',$year);
                break;
            default:
                $Query->whereBetween('dismounted_date', [$start, $end]);
        }

        if ($factory != 'false') {
            $Query->where('factory', $factory);
        }   
        
        if ($cy  != 'false') {
            $Query->where('dismounted_cy',$cy);
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
