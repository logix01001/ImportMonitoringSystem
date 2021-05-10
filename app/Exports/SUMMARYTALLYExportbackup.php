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

class SUMMARYTALLYExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $factory;
    private $category;
    private $all;
    private $date_request;

    public function __construct($factory,$category,$date_request,$all){
        $this->factory = $factory;
        $this->category = $category;
        $this->all = $all;
        $this->date_request = $date_request;

    }

    public function collection()
    {
        //
        $factory = $this->factory;
        $date_request = $this->date_request;
        $all = $this->all;
        $category = $this->category;

        $six_days_before = date('Y-m-d', strtotime($date_request . ' -6 days'));
        $eleven_days_before = date('Y-m-d', strtotime($date_request . ' -10 days'));


        $list_of_BOL =  bill_of_lading::select('factory',
        'bl_no',
        'supplier',
        'connecting_vessel',
        'shipping_line',
        'forwarder',
        'pol',
        'country',
        'pod',
        'volume',
        'estimated_time_arrival',
        'actual_time_arrival',
        'actual_berthing_date',
        'actual_discharge',
        'container_type',
        'container_number',
        'quantity',
		'actual_process',
		'actual_gatepass',
		'validity_storage',
		'validity_demurrage',
		'revalidity_storage',
		'revalidity_demurrage',
        'dismounted_cy',
        'dismounted_date',
        'unload',
        'pull_out')
        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
        ->where('quantity','=',1);

		$list_of_BOL2 =  bill_of_lading::select('factory',
        'bl_no',
        'supplier',
        'connecting_vessel',
        'shipping_line',
        'forwarder',
        'pol',
        'country',
        'pod',
        'volume',
        'estimated_time_arrival',
        'actual_time_arrival',
        'actual_berthing_date',
        'actual_discharge',
        'container_type',
        'container_number',
        'quantity',
		'actual_process',
		'actual_gatepass',
		'validity_storage',
		'validity_demurrage',
		'revalidity_storage',
		'revalidity_demurrage',
        'dismounted_cy',
        'dismounted_date',
        'unload',
        'pull_out')
        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
        ->where('quantity','=',1);
		
        if($all == 'false'){
            $list_of_BOL = $list_of_BOL->where('factory',$factory);
            $list_of_BOL2 = $list_of_BOL2->where('factory',$factory);
        }

        switch($category){
            case 'NORTH': 
                       
                        $list_of_BOL =  $list_of_BOL
                            ->where('actual_discharge','<=',$date_request)
                            ->whereNull('dismounted_cy')
                            ->where(function($query) use ($date_request){        
                                $query->whereNull('pull_out');
                                $query->orWhere('pull_out', '>', $date_request);
                            })
                        
                            ->where('pod','NORTH')
                            ->get();
                        break;
            case 'SOUTH': 
                            $list_of_BOL =  $list_of_BOL
                                ->where('actual_discharge','<=',$date_request)
								->whereNull('dismounted_cy')
                                ->where(function($query) use ($date_request){        
                                    $query->whereNull('pull_out');
                                    $query->orWhere('pull_out', '>', $date_request);
                                })
                            
                                ->where('pod','SOUTH')
                                ->get();
                        
                        break;
                    
            case 'PORT': 
                        $list_of_BOL =  $list_of_BOL
                                ->where('actual_discharge','<=',$date_request)
                                ->whereNull('dismounted_cy')
                                ->where(function($query) use ($date_request){        
                                    $query->whereNull('pull_out');
                                    $query->orWhere('pull_out', '>', $date_request);
                                })
                                ->get();
                        
                        break;
            case 'IRS':
                        $list_of_BOL =  $list_of_BOL
                            ->whereNull('unload')
                           
                            //->where('dismounted_cy','IRS BACAO')
							->whereIn('dismounted_cy', ['IRS BACAO','WITH CHASSI'])
                            ->get();    
                            break;
            case 'DF': //DELIVERY FACTORY
                    $list_of_BOL =  $list_of_BOL
                            ->whereNull('dismounted_cy')
                            ->where('pull_out',$date_request) //change by mam shaira from unload
                            ->get();  
                        break;
            case 'DI': //DELIVERY IRS
                    $list_of_BOL =  $list_of_BOL
                            ->whereNotNull('pull_out')
                            ->where('dismounted_date',$date_request)
                           
                            ->where('dismounted_cy','IRS BACAO')
                            ->get();  
                        break;
            case 'DWC': //DELIVERY with CHASSI
                    $list_of_BOL =  $list_of_BOL
                            ->where('dismounted_date','=',$date_request)
                            ->whereNotIn('dismounted_cy',['IRS BACAO'])
                            
                            ->get();  
                        break;
            case 'BERTHED': //DELIVERY with CHASSI
                    $list_of_BOL =  $list_of_BOL
                                ->whereActualBerthingDate($date_request)
                                ->get();  
                        break;
            case 'DISCHARGE': //DELIVERY with CHASSI
                    $list_of_BOL =  $list_of_BOL
                                ->whereActualDischarge($date_request)
                                ->get();  
                        break;
            case 'GATEPASS': //DELIVERY with CHASSI
                    $list_of_BOL =  $list_of_BOL
                                ->whereActualGatepass($date_request)
                                ->get();  
                        break;
            case 'DU': //Direct Unloading
                    $list_of_BOL =  $list_of_BOL
                                ->whereNull('dismounted_date')
                                ->where('unload',$date_request)
                                ->get();  
                        break;
            case 'UWC': //Unloading with Chassi
                    $list_of_BOL =  $list_of_BOL
                                ->whereNotNull('dismounted_date')
                                ->whereNotIn('dismounted_cy',['IRS BACAO'])
                                ->where('unload',$date_request)
                                ->get();  
                        break;
            case 'UI': //Unloading IRS
                    $list_of_BOL =  $list_of_BOL
                                ->whereNotNull('dismounted_date')
                                ->where('dismounted_cy','IRS BACAO')
                                ->where('unload',$date_request)
                                ->get();  
                        break;
            case 'TOTALALL': //TOTAL AT PORT AND IRS
                        $list_of_BOL =  $list_of_BOL  
                              
                                ->where(function($query) use ($date_request){ 
                                    $query->where('actual_discharge','<=',$date_request); 
									$query->whereNull('dismounted_cy');											
                                    $query->whereNull('pull_out');
                                    $query->orWhere('pull_out', '>', $date_request);
                                }) 
                               /* ->orWhere(function($query) use ($date_request){
                                    
                                    $query->WhereNull('unload');
                                    //$query->where('dismounted_cy','IRS BACAO');
									$query->whereIn('dismounted_cy', ['IRS BACAO','WITH CHASSI']);
                                })
								*/
                                ->get();
								$list_of_BOL2 = $list_of_BOL2->where(function($query) use ($date_request){
                                
											$query->WhereNull('unload');
											//$query->where('dismounted_cy','IRS BACAO');
											$query->whereIn('dismounted_cy', ['IRS BACAO','WITH CHASSI']);
										})
										->get();
								foreach($list_of_BOL2 as $irschassi) {
										$list_of_BOL->add($irschassi);
								}
                                break;
            //BEYOND FREE TIME STORAGE PER DAY
            case 'BFT_container':
                    $list_of_BOL = bill_of_lading::select('factory',
                        'bl_no',
                        'supplier',
                        'connecting_vessel',
                        'shipping_line',
                        'forwarder',
                        'pol',
                        'country',
                        'pod',
                        'volume',
                        'estimated_time_arrival',
                        'actual_time_arrival',
                        'actual_berthing_date',
                        'actual_discharge',
                        'container_type',
                        'container_number',
                        'quantity',
                        'actual_process',
                        'actual_gatepass',
                        'validity_storage',
                        'validity_demurrage',
                        'revalidity_storage',
                        'revalidity_demurrage',
                        'dismounted_cy',
                        'dismounted_date',
                        'unload',
                        'pull_out')
                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
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
                        $list_of_BOL = bill_of_lading::select('factory',
                            'bl_no',
                            'supplier',
                            'connecting_vessel',
                            'shipping_line',
                            'forwarder',
                            'pol',
                            'country',
                            'pod',
                            'volume',
                            'estimated_time_arrival',
                            'actual_time_arrival',
                            'actual_berthing_date',
                            'actual_discharge',
                            'container_type',
                            'container_number',
                            'quantity',
                            'actual_process',
                            'actual_gatepass',
                            'validity_storage',
                            'validity_demurrage',
                            'revalidity_storage',
                            'revalidity_demurrage',
                            'dismounted_cy',
                            'dismounted_date',
                            'unload',
                            'pull_out')
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
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
                        $list_of_BOL = bill_of_lading::select('factory',
                                'bl_no',
                                'supplier',
                                'connecting_vessel',
                                'shipping_line',
                                'forwarder',
                                'pol',
                                'country',
                                'pod',
                                'volume',
                                'estimated_time_arrival',
                                'actual_time_arrival',
                                'actual_berthing_date',
                                'actual_discharge',
                                'container_type',
                                'container_number',
                                'quantity',
                                'actual_process',
                                'actual_gatepass',
                                'validity_storage',
                                'validity_demurrage',
                                'revalidity_storage',
                                'revalidity_demurrage',
                                'dismounted_cy',
                                'dismounted_date',
                                'unload',
                                'pull_out')
                                    ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
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
                            $list_of_BOL = bill_of_lading::select('factory',
                            'bl_no',
                            'supplier',
                            'connecting_vessel',
                            'shipping_line',
                            'forwarder',
                            'pol',
                            'country',
                            'pod',
                            'volume',
                            'estimated_time_arrival',
                            'actual_time_arrival',
                            'actual_berthing_date',
                            'actual_discharge',
                            'container_type',
                            'container_number',
                            'quantity',
                            'actual_process',
                            'actual_gatepass',
                            'validity_storage',
                            'validity_demurrage',
                            'revalidity_storage',
                            'revalidity_demurrage',
                            'dismounted_cy',
                            'dismounted_date',
                            'unload',
                            'pull_out')
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
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
                    $five_days_before = date('Y-m-d',strtotime($date_request . ' -5 days'));            
                    $list_of_BOL =  $list_of_BOL
                                ->where('actual_discharge','<=',$five_days_before)
                                ->where(function($query) use ($date_request){        
                                    $query->whereNull('pull_out');
                                    $query->orWhere('pull_out', '>', $date_request);
                                })
                                ->get();  
                       
    
        }

        $data = [];
        $index = 0;
        if(count($list_of_BOL) > 0){
            foreach($list_of_BOL as $row){
                $invoices_data = [];
                $commodity = [];
                foreach(Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk','=',$row['bl_no'])->get() as $inv ){
                    $invoices_data[] = $inv['invoice_number'];
                }
    
                foreach(Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk','=',$row['bl_no'])->get() as $cm){
                    $commodity[] = $cm['commodity'];
                }
    
                $row['invoice_number'] = implode(',',$invoices_data);
                $row['commodities'] = implode(',',$commodity);
    
    
    
                $data[ $index ][] =  $row['factory'];
                $data[ $index ][] =  $row['bl_no'];
                $data[ $index ][] =  $row['invoice_number'];
                $data[ $index ][] =  $row['supplier'];
                $data[ $index ][] =  $row['commodities'];
                $data[ $index ][] =  $row['connecting_vessel'];
                $data[ $index ][] =  $row['shipping_line'];
                $data[ $index ][] =  $row['forwarder'];
                $data[ $index ][] =  $row['pol'];
                $data[ $index ][] =  $row['country'];
                $data[ $index ][] =  $row['pod'];
                $data[ $index ][] =  $row['volume'];
                $data[ $index ][] =  $row['container_type'];
                $data[ $index ][] =  $row['container_number'];
                $data[ $index ][] =  $row['quantity'];
                $data[ $index ][] =  $row['estimated_time_arrival'];
                $data[ $index ][] =  $row['actual_time_arrival'];
                $data[ $index ][] =  $row['actual_berthing_date'];
                $data[ $index ][] =  $row['actual_discharge'];
                
                $data[ $index ][] =  $row['actual_process'];
                $data[ $index ][] =  $row['actual_gatepass'];
                $data[ $index ][] =  $row['validity_storage'];
                $data[ $index ][] =  $row['validity_demurrage'];
                $data[ $index ][] =  $row['revalidity_storage'];
                $data[ $index ][] =  $row['revalidity_demurrage'];
                
                $data[ $index ][] =  $row['dismounted_cy'];
                $data[ $index ][] =  $row['dismounted_date'];
                $data[ $index ][] =  $row['pull_out'];
                $data[ $index ][] =  $row['unload'];
    
    
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
            'INVOICE NO.',
            'SUPPLIER',
            'COMMODITY',
            'CONNECTING VESSEL',
            'SHIPPING LINE',
            'FORWARDER',
            'POL',
            'COUNTRY',
            'POD',
			'VOLUME',
            'CNTR TYPE',
            'CONTAINER NUMBER',
            'QUANTITY',
            'ESTIMATED TIME OF ARRIVAL',
            'ACTUAL TIME OF ARRIVAL',
            'ACTUAL BERTHING DATE',
            'ACTUAL TIME DISCHARGE',
			'ACTUAL PROCESS',
			'ACTUAL GATEPASS',
			'VALIDITY STORAGE',
			'VALIDITY DUMURRAGE',
			'REVALIDITY STORAGE',
			'REVALIDITY DUMURRAGE',
            'DISMOUNTED PLACE',
            'DISMOUNTED DATE',
            'ACTUAL DELIVERY',
            'ACTUAL UNLOADING DATE',
        ];
    }

}
