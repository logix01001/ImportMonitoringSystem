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

class SUMMARYTALLYExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $factory;
    private $category;
    private $all;
    private $date_request;
    private $reference;
    private $dateMonth;
    private $dateYear;
    private $start;
    private $end;

    public function __construct($factory,$category,$date_request,$all,$reference = null,$dateMonth = null,$dateYear = null,$start = null, $end = null){
        $this->factory = $factory;
        $this->category = $category;
        $this->all = $all;
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
        $all = $this->all;
        $category = $this->category;
        $reference = $this->reference;
        $dateMonth = $this->dateMonth;
        $dateYear = $this->dateYear;
        $start = $this->start;
        $end = $this->end;






        $six_days_before = date('Y-m-d', strtotime($date_request . ' -6 days'));
        $eleven_days_before = date('Y-m-d', strtotime($date_request . ' -10 days'));

        $select = explode(",", getenv('SELECT_EXPORT'));
        $list_of_BOL =  bill_of_lading::select($select)
        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
        ->where('quantity','=',1);

		$list_of_BOL2 =  bill_of_lading::select($select)
        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
        ->where('quantity','=',1);
		
        if($all == 'false'){
            $list_of_BOL = $list_of_BOL->where('factory',$factory);
            $list_of_BOL2 = $list_of_BOL2->where('factory',$factory);
        }

        switch ($category) {
            case 'NORTH':
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL
                            ->where('actual_discharge', '<=', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-".$month));
    
                        $list_of_BOL = $list_of_BOL
                           ->where('actual_discharge', '<=', $lastDayThisMonth);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL
                                    ->where('actual_discharge', '<=', $lastDayThisMonth);
                        break;
                    default:
                     $list_of_BOL = $list_of_BOL
                                    ->whereBetween('actual_discharge', [$start, $end]);
                }
    
                $list_of_BOL =   $list_of_BOL->whereNull('pull_out')
                    ->where('pod', 'NORTH')
                    ->get();
                break;

            case 'SOUTH':
                    switch ($reference) {
                        case 'D':
                            $list_of_BOL = $list_of_BOL
                                ->where('actual_discharge', '<=', $date_request);
                            break;
                        case 'M':
                            $month = explode('-', $dateMonth)[1];
                            $year = explode('-', $dateMonth)[0];
                            $lastDayThisMonth = date("Y-m-t",strtotime($year ."-".$month));
    
                            $list_of_BOL = $list_of_BOL
                            ->where('actual_discharge', '<=', $lastDayThisMonth);
                            break;
                        case 'Y':
                            $year = explode('-', $dateYear)[0];
                            $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                            
                            $list_of_BOL = $list_of_BOL
                                        ->where('actual_discharge', '<=', $lastDayThisMonth);
                            break;
                        default:
                        $list_of_BOL = $list_of_BOL
                                        ->whereBetween('actual_discharge', [$start, $end]);
                    }
    
    
    
                    $list_of_BOL = $list_of_BOL->whereNull('pull_out')
                    ->where('pod', 'SOUTH')
                    ->get();
    
                break;
    
            case 'PORT':
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL
                            ->where('actual_discharge', '<=', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-".$month));
    
                        $list_of_BOL = $list_of_BOL
                        ->where('actual_discharge', '<=', $lastDayThisMonth);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL
                                    ->where('actual_discharge', '<=', $lastDayThisMonth);
                        break;
                    default:
                    $list_of_BOL = $list_of_BOL
                                    ->whereBetween('actual_discharge', [$start, $end]);
                }
                $list_of_BOL = $list_of_BOL->whereNull('pull_out')
                ->get();
    
                break;
            case 'IRS':
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->where('dismounted_date','<=', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $list_of_BOL = $list_of_BOL->whereMonth('dismounted_date', '=', $month)
                                        ->whereYear('dismounted_date', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('dismounted_date', '=', $year);
                        break;
                    default:
                    $list_of_BOL = $list_of_BOL->whereBetween('dismounted_date', [$start, $end]);
                }
    
              
    
                $list_of_BOL = $list_of_BOL->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNull('unload')
                                            //->where('dismounted_cy','IRS BACAO')
                                        ->where('dismounted_cy', 'IRS BACAO')
                                        ->whereNotNull('pull_out')
                                        ->get();
                break;
            case 'CEZ1_Accu':
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->where('dismounted_date','<=', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $list_of_BOL = $list_of_BOL->whereMonth('dismounted_date', '=', $month)
                                        ->whereYear('dismounted_date', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('dismounted_date', '=', $year);
                        break;
                    default:
                    $list_of_BOL = $list_of_BOL->whereBetween('dismounted_date', [$start, $end]);
                }
    
              
    
                $list_of_BOL = $list_of_BOL->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNull('unload')
                                            //->where('dismounted_cy','IRS BACAO')
                                        ->where('dismounted_cy', 'CEZ1')
                                        ->whereNotNull('pull_out')
                                        ->get();
                break;
            case 'CEZ2_Accu':
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->where('dismounted_date','<=', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $list_of_BOL = $list_of_BOL->whereMonth('dismounted_date', '=', $month)
                                        ->whereYear('dismounted_date', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('dismounted_date', '=', $year);
                        break;
                    default:
                    $list_of_BOL = $list_of_BOL->whereBetween('dismounted_date', [$start, $end]);
                }
    
                $list_of_BOL = $list_of_BOL->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNull('unload')
                                            //->where('dismounted_cy','IRS BACAO')
                                        ->where('dismounted_cy', 'CEZ2')
                                        ->whereNotNull('pull_out')
                                        ->get();
                break;
            case 'CHASSI':
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->where('dismounted_date', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $list_of_BOL = $list_of_BOL->whereMonth('dismounted_date', '=', $month)
                                        ->whereYear('dismounted_date', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('dismounted_date', '=', $year);
                        break;
                    default:
                    $list_of_BOL = $list_of_BOL->whereBetween('dismounted_date', [$start, $end]);
                }
    
              
    
                $list_of_BOL = $list_of_BOL->whereNull('unload')
                    //->where('dismounted_cy','IRS BACAO')
                    ->whereNotIn('dismounted_cy', ['IRS BACAO'])
                    ->get();
                break;
            case 'DF': //DELIVERY FACTORY
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->where('pull_out', $date_request); //change by mam shaira  from unload
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $list_of_BOL = $list_of_BOL
                                    ->whereMonth('pull_out', '=', $month)
                                    ->whereYear('pull_out', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('pull_out', '=', $year);
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL->whereBetween('pull_out', [$start, $end]);
                }
    
                  
                
                    $list_of_BOL = $list_of_BOL->whereNull('dismounted_cy') ->get();
                break;
            case 'DI': //DELIVERY IRS
    
               
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->where('dismounted_date', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $list_of_BOL = $list_of_BOL
                                        ->whereMonth('dismounted_date', '=', $month)
                                        ->whereYear('dismounted_date', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('dismounted_date', '=', $year);
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL->whereBetween('dismounted_date', [$start, $end]);
                }
                
                $list_of_BOL = $list_of_BOL->whereNotNull('pull_out')
                    ->where('dismounted_cy', 'IRS BACAO')
                    ->get();
                break;
            case 'DCEZ1': //DELIVERY CEZ1

                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->where('dismounted_date', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $list_of_BOL = $list_of_BOL
                                        ->whereMonth('dismounted_date', '=', $month)
                                        ->whereYear('dismounted_date', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('dismounted_date', '=', $year);
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL->whereBetween('dismounted_date', [$start, $end]);
                }
                
                $list_of_BOL = $list_of_BOL->whereNotNull('pull_out')
                    ->where('dismounted_cy', 'CEZ1')
                    ->get();
                break;
            case 'DCEZ2': //DELIVERY CEZ2

            
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->where('dismounted_date', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $list_of_BOL = $list_of_BOL
                                        ->whereMonth('dismounted_date', '=', $month)
                                        ->whereYear('dismounted_date', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('dismounted_date', '=', $year);
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL->whereBetween('dismounted_date', [$start, $end]);
                }
                
                $list_of_BOL = $list_of_BOL->whereNotNull('pull_out')
                    ->where('dismounted_cy', 'CEZ2')
                    ->get();
                break;
            case 'DWC': //DELIVERY with CHASSI
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->where('dismounted_date', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $list_of_BOL = $list_of_BOL
                                        ->whereMonth('dismounted_date', '=', $month)
                                        ->whereYear('dismounted_date', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('dismounted_date', '=', $year);
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL->whereBetween('dismounted_date', [$start, $end]);
                }
    
                $list_of_BOL = $list_of_BOL->whereNotIn('dismounted_cy', ['IRS BACAO'])->get();
                break;
            case 'BERTHED': //BERTHED
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->whereActualBerthingDate($date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
    
                        $list_of_BOL = $list_of_BOL
                                        ->whereMonth('actual_berthing_date', '=', $month)
                                        ->whereYear('actual_berthing_date', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('actual_berthing_date', '=', $year);
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL->whereBetween('actual_berthing_date', [$start, $end]);
                }
    
    
                $list_of_BOL = $list_of_BOL->get();
                break;
            case 'DISCHARGE': //DELIVERY with CHASSI
    
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->whereActualDischarge($date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
    
                        $list_of_BOL = $list_of_BOL
                                        ->whereMonth('containers.actual_discharge', '=', $month)
                                        ->whereYear('containers.actual_discharge', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('containers.actual_discharge', '=', $year);
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL->whereBetween('containers.actual_discharge', [$start, $end]);
                }
               
                    $list_of_BOL = $list_of_BOL->get();
                break;
            case 'GATEPASS': //DELIVERY with CHASSI
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->whereActualGatepass($date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
    
                        $list_of_BOL = $list_of_BOL
                            ->whereMonth('containers.actual_gatepass', '=', $month)
                            ->whereYear('containers.actual_gatepass', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('containers.actual_gatepass', '=', $year);
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL->whereBetween('containers.actual_gatepass', [$start, $end]);
                }
                    
                    $list_of_BOL = $list_of_BOL->get();
                break;
            case 'DU': //Direct Unloading
                switch ($reference) {
                    case 'D':
                         $list_of_BOL = $list_of_BOL->where('unload', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
    
                        $list_of_BOL = $list_of_BOL
                            ->whereMonth('unload', '=', $month)
                            ->whereYear('unload', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('unload', '=', $year);
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL->whereBetween('unload', [$start, $end]);
                }
               
    
                $list_of_BOL = $list_of_BOL
                    ->whereNull('dismounted_date')
                    ->get();
                break;
            case 'UWC': //Unloading with Chassi
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->where('unload', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
    
                        $list_of_BOL = $list_of_BOL
                            ->whereMonth('unload', '=', $month)
                            ->whereYear('unload', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('unload', '=', $year);
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL->whereBetween('unload', [$start, $end]);
                }
               
                $list_of_BOL = $list_of_BOL
                    ->whereNotNull('dismounted_date')
                    ->whereNotIn('dismounted_cy', ['IRS BACAO'])
                    ->get();
                break;
            case 'UI': //Unloading IRS
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL->where('unload', $date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
    
                        $list_of_BOL = $list_of_BOL
                            ->whereMonth('unload', '=', $month)
                            ->whereYear('unload', '=', $year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL->whereYear('unload', '=', $year);
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL->whereBetween('unload', [$start, $end]);
                }
           
                $list_of_BOL = $list_of_BOL
                    ->whereNotNull('dismounted_date')
                    ->where('dismounted_cy', 'IRS BACAO')
                    ->get();
                break;
            case 'TOTALALL': //TOTAL AT PORT AND IRS
    
                switch ($reference) {
                    case 'D':
                        $list_of_BOL = $list_of_BOL
    
                            ->where(function ($query) use ($date_request) {
                                $query->where('actual_discharge', '<=', $date_request);
                                $query->whereNull('dismounted_cy');
                                $query->whereNull('pull_out');
                                //$query->orWhere('pull_out', '>', $date_request);
                            })
    
                            ->get();
    
                        $list_of_BOL2 = $list_of_BOL2->where(function ($query) use ($date_request) {
    
                            $query->WhereNull('unload');
                            //$query->where('dismounted_cy','IRS BACAO');
                            $query->whereIn('dismounted_cy', ['IRS BACAO','CEZ1','CEZ2'])
                            ->where('dismounted_date','<=', $date_request);
                        })
                            ->get();
                        foreach ($list_of_BOL2 as $irschassi) {
                            $list_of_BOL->add($irschassi);
                        }
    
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-".$month));
                        $list_of_BOL = $list_of_BOL
    
                        ->where(function ($query) use ($lastDayThisMonth) {
                            $query->where('actual_discharge', '<=', $lastDayThisMonth);
                            $query->whereNull('dismounted_cy');
                            $query->whereNull('pull_out');
                           // $query->orWhere('pull_out', '>', $lastDayThisMonth);
                        })
    
                        ->get();
    
                        $list_of_BOL2 = $list_of_BOL2->where(function ($query) use ($month ,$year) {
    
                            $query->WhereNull('unload');
                            //$query->where('dismounted_cy','IRS BACAO');
                            $query->whereIn('dismounted_cy', ['IRS BACAO','CEZ1','CEZ2'])
                                ->whereMonth('dismounted_date', '=', $month)
                                ->whereYear('dismounted_date', '=', $year);
                        })
                            ->get();
                        foreach ($list_of_BOL2 as $irschassi) {
                            $list_of_BOL->add($irschassi);
                        }
                        
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $list_of_BOL = $list_of_BOL
    
                        ->where(function ($query) use ($lastDayThisMonth) {
                            $query->where('actual_discharge', '<=', $lastDayThisMonth);
                            $query->whereNull('dismounted_cy');
                            $query->whereNull('pull_out');
                           // $query->orWhere('pull_out', '>', $lastDayThisMonth);
                        })
    
                        ->get();
    
                        $list_of_BOL2 = $list_of_BOL2->where(function ($query) use ($year) {
    
                            $query->WhereNull('unload');
                            //$query->where('dismounted_cy','IRS BACAO');
                            $query->whereIn('dismounted_cy', ['IRS BACAO','CEZ1','CEZ2'])
                                ->whereYear('dismounted_date', '=', $year);
                        })
                        ->get();
                        foreach ($list_of_BOL2 as $irschassi) {
                            $list_of_BOL->add($irschassi);
                        }
                        break;
                    default:
                        $list_of_BOL = $list_of_BOL
                        //[$start, $end]);
                        ->whereBetween('actual_discharge', [$start,$end])
                        ->whereNull('dismounted_cy')
                        ->where(function ($query) use ($end) {
                           
                          
                            $query->whereNull('pull_out');
                           // $query->orWhere('pull_out', '>', $end);
                        })
    
                        ->get();
    
                        $list_of_BOL2 = $list_of_BOL2->where(function ($query) use ($start,$end) {
    
                            $query->WhereNull('unload');
                            //$query->where('dismounted_cy','IRS BACAO');
                            $query->whereIn('dismounted_cy', ['IRS BACAO','CEZ1','CEZ2'])
                            ->whereBetween('dismounted_date', [$start, $end]);
                          
                                
                        })
                        ->get();
                        foreach ($list_of_BOL2 as $irschassi) {
                            $list_of_BOL->add($irschassi);
                        }
                }
              
    
                break;
            //BEYOND FREE TIME STORAGE PER DAY
            case 'BFT_container':
                    $list_of_BOL = bill_of_lading::select($select)
                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                            ->whereQuantity(1)
                            ->where('actual_discharge', '<=', $six_days_before)
                            ->where(function ($query) use ($date_request) {
                                $query->whereNull('pull_out');
                                $query->orWhere('pull_out', '>', $date_request);
                            });
                            if($all == 'false'){
                                $list_of_BOL = $list_of_BOL->where('factory',$factory);
                               
                            }
                            $list_of_BOL =  $list_of_BOL->get();

            

                        break;
                case 'BFT_SIX':
                        $list_of_BOL = bill_of_lading::select($select)
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->where('actual_discharge', '<=', $six_days_before)
                                ->where('actual_discharge', '>', $eleven_days_before)
                                ->where(function ($query) use ($date_request) {
                                    $query->whereNull('pull_out');
                                    $query->orWhere('pull_out', '>', $date_request);
                                });
                                if($all == 'false'){
                                    $list_of_BOL = $list_of_BOL->where('factory',$factory);
                                   
                                }
                                $list_of_BOL =  $list_of_BOL->get();
    
                
    
                            break;
                case 'BFT_ELEVEN':
                        $list_of_BOL = bill_of_lading::select($select)
                                    ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                    ->whereQuantity(1)
                                    ->where('actual_discharge', '<=', $eleven_days_before)

                                    ->where(function ($query) use ($date_request) {
                                        $query->whereNull('pull_out');
                                        $query->orWhere('pull_out', '>', $date_request);
                                    });
                                    if($all == 'false'){
                                        $list_of_BOL = $list_of_BOL->where('factory',$factory);
                                       
                                    }
                                    $list_of_BOL =  $list_of_BOL->get();
        
                    
        
                                break;
            case 'BFT_SUMMARY' :
                            $list_of_BOL = bill_of_lading::select($select)
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->where('actual_discharge', '<=', $six_days_before)
                                ->where(function ($query) use ($date_request) {
                                    $query->whereNull('pull_out');
                                    $query->orWhere('pull_out', '>', $date_request);
                                });
                                if($all == 'false'){
                                    $list_of_BOL = $list_of_BOL->where('factory',$factory);
                                
                                }
                                $list_of_BOL =  $list_of_BOL->get();

                                 break;
            //END BEYOND FREE TIME STORAGE PER DAY
            default: //Unloading IRS

                switch ($reference) {
                    case 'D':
                        $five_days_before = date('Y-m-d', strtotime($date_request . ' -6 days'));
                        $list_of_BOL = $list_of_BOL
                            ->whereQuantity(1)
                            ->where('actual_discharge', '<=', $five_days_before)
                            ->where(function ($query) use ($date_request) {
                                $query->whereNull('pull_out');
                                $query->orWhere('pull_out', '>', $date_request);
                        })
                        ->get();

                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-".$month));


                        $five_days_before = date('Y-m-d', strtotime($lastDayThisMonth . ' -6 days'));
                            $list_of_BOL = $list_of_BOL
                                ->whereQuantity(1)
                                ->where('actual_discharge', '<=', $five_days_before)
                                ->where(function ($query) use ($lastDayThisMonth) {
                                    $query->whereNull('pull_out');
                                    $query->orWhere('pull_out', '>', $lastDayThisMonth);
                            })
                            ->get();
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));
                        
                        $five_days_before = date('Y-m-d', strtotime($lastDayThisMonth . ' -6 days'));
                        $list_of_BOL = $list_of_BOL
                            ->whereQuantity(1)
                            ->where('actual_discharge', '<=', $five_days_before)
                            ->where(function ($query) use ($lastDayThisMonth) {
                                $query->whereNull('pull_out');
                                $query->orWhere('pull_out', '>', $lastDayThisMonth);
                        })
                        ->get();
                        break;
                    default:
                
                        $five_days_after = date('Y-m-d', strtotime($end . ' +5 days'));
                        $list_of_BOL = $list_of_BOL
                            ->whereQuantity(1)
                            ->whereBetween('actual_discharge', [$start, $end])
                            ->where(function ($query) use ($five_days_after) {
                                $query->whereNull('pull_out');
                                $query->orWhere('pull_out', '>', $five_days_after);
                        })
                        ->get();
                }

        }

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
