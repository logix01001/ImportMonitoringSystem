<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Factory;
use App\Container_Type;
use App\Container;
use App\Bill_of_Lading;
use App\Bill_of_Lading_Invoice;
use App\Bill_of_Lading_Commodity;
use App\Uploading_CSV_Log;
use JavaScript;
use Session;
use App\Exports\PODExport;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use DB;
use App\Holiday;
use Storage;
use DateTime;

class reportController extends Controller
{
    //
    public function __construct(){

        $this->middleware('updateUserPrivilege');
        ini_set('max_execution_time', 300);
       
    }
    public function charts(){
        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();

        $cType = Container_Type::select('name')->whereNull('deleted_at')->get();
        $factory_array = [];
        $i = 0;
        $factory_drilldown_array = [];

        $factory_tally = [];
        $factory_count = [];
        $factory_total = [];
        foreach( $factories as $factory){

          


            
          $count = Container::select('container_number')
          ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
          ->where('quantity','=',1)
          ->where('factory',$factory['factory_id'])
          ->whereNull('pull_out')
          ->count();

          if($count > 0){
            $factory_array[$i]['name'] = $factory['factory_id'];
            $factory_array[$i]['drilldown'] = $factory['factory_id'];
            $factory_array[$i]['y'] =  $count;

            $factory_tally[] = $factory['factory_id'];
           

            $x = 0;
            $index_total = 0;
            $factory_drilldown_array[$i]['name'] = $factory['factory_id'];
            $factory_drilldown_array[$i]['id'] = $factory['factory_id'];
            $factory_drilldown_array[$i]['data'] = [];

            foreach( $cType as $type){

                $count = Container::select('container_number')
                    ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                    ->where('quantity','=',1)
                    ->where('factory',$factory['factory_id'])
                    ->where('container_type',$type['name'])
                    ->whereNull('pull_out')
                    ->count();
                if($count > 0){
                    $factory_count[$factory['factory_id']][] = $count;
                    $factory_drilldown_array[$i]['data'][$x]   =  [$type['name'] , $count];
                    $x++;
                  
                }else{
                    $factory_count[$factory['factory_id']][] = 0;
                }

                if(!array_key_exists($index_total,$factory_total)){
                    $factory_total[$index_total] = 0;
                }
                 $factory_total[$index_total]  +=  $count;
                 $index_total++;
                

            }

            $i++;   

          }


       
      }

  
      JavaScript::put([
        'factory_array' => $factory_array,
        'factory_drilldown_array' => $factory_drilldown_array,
        'factory_count' =>  $factory_count,
        'cType' =>  $cType,
        'factory_total' => $factory_total,
      ]);   
      
        return view('pages.charts');
    }

    public function summary_tally(){

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2018-11-30';
        $i = 0;
        $summary = [];
        foreach($factories as $factory){
            $summary[$i]['name'] = $factory['factory_id'];

            $countNorth = Container::select('container_number')
                    ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                    ->where('actual_discharge','<=',$date_request)
                    ->where(function($query) use ($date_request){        
                        $query->whereNull('pull_out');
                        $query->orWhere('pull_out', '>', $date_request);
                    })
                    ->where('factory',$factory['factory_id'])
                    ->where('pod','NORTH')
                    ->count();


            $summary[$i]['north'] = $countNorth;

            $countSouth = Container::select('container_number')
                    ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                    ->where('actual_discharge','<=',$date_request)
                    ->where(function($query) use ($date_request){        
                        $query->whereNull('pull_out');
                        $query->orWhere('pull_out', '>', $date_request);
                    })
                    ->where('factory',$factory['factory_id'])
                    ->where('pod','SOUTH')
                    ->count();


            $summary[$i]['south'] = $countSouth;
            $summary[$i]['at_port'] = $countSouth + $countNorth;

           

            $countIRS = Container::select('container_number')
                    ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                    ->whereNull('unload')
                    ->where('factory',$factory['factory_id'])
                    ->where('dismounted_cy','IRS BACAO')
                    ->count();

            $summary[$i]['irs'] = $countIRS;

            $countFactory = Container::select('container_number')
                ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                ->whereNull('dismounted_cy')
                ->where('unload',$date_request)
                ->where('factory',$factory['factory_id'])
                ->count();

            $summary[$i]['delivery_factory'] = $countFactory;

            $count_irs = Container::select('container_number')
                            ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                            ->whereNotNull('pull_out')
                            ->where('dismounted_date',$date_request)
                            ->where('factory',$factory['factory_id'])
                            ->where('dismounted_cy','IRS BACAO')
                            ->count();

            $summary[$i]['delivery_irs'] = $count_irs;

            $count_chassi = Container::select('container_number')
                ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                ->where('quantity','=',1)
                ->where('dismounted_date','=',$date_request)
                //->whereNotIn('dismounted_cy',['CEZ 1 PUTOL', 'CEZ 2 PUTOL','IRS BACAO'])
                ->whereNotIn('dismounted_cy',['IRS BACAO'])
                ->where('factory',$factory['factory_id'])
                ->count();
            $summary[$i]['with_chassi'] = $count_chassi;



            $count =   bill_of_lading::whereActualBerthingDate($date_request)
                    ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                    ->where('quantity','=',1)
                    ->where('factory',$factory['factory_id'])
                    ->count();
            $summary[$i]['berthed'] = $count;

            $count = Container::select('container_number')
                ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                ->where('quantity','=',1)
                ->where('containers.actual_discharge','=',$date_request)
                ->where('factory',$factory['factory_id'])
                ->count();

        
            $summary[$i]['discharge'] = $count;



            $count = Container::select('container_number')
                ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                ->where('quantity','=',1)
                ->where('containers.actual_gatepass','=',$date_request)
                ->where('factory',$factory['factory_id'])
                ->count();

        
            $summary[$i]['gatepass'] = $count;

            $count = Container::select('container_number')
                        ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                        ->whereNull('dismounted_date')
                        ->where('unload',$date_request)
                        ->where('factory',$factory['factory_id'])
                        ->count();

            $summary[$i]['direct_unloading'] = $count;

            $count = Container::select('container_number')
                ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                ->whereNotNull('dismounted_date')
                ->whereNotIn('dismounted_cy',['IRS BACAO'])
                ->where('unload',$date_request)
                ->where('factory',$factory['factory_id'])
                ->count();

            $summary[$i]['unloading_with_chassis'] = $count;

            $count = Container::select('container_number')
            ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
            ->whereNotNull('dismounted_date')
            ->where('dismounted_cy','IRS BACAO')
            ->where('unload',$date_request)
            ->where('factory',$factory['factory_id'])
            ->count();

            $summary[$i]['unloading_irs'] = $count;



            $i++;
        }

        JavaScript::put([
            'summary' => $summary,
            'date_request' => $date_request
        ]);  
        
        return view('pages.summary_tally');
    }

    public function beyond_storage_free_time(){

        
    }

    public function breakdown(){

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $containers_tally  = [];
        $date_request = date('Y-m-d',strtotime('-1 days'));

        $i = 0;
        foreach($factories as $factory){

            $count = Container::select('container_number')
            ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
            ->where('quantity','=',1)
            ->where('pull_out','=',$date_request)
            ->where('factory',$factory['factory_id'])
            ->count();
            
            $containers_tally[$i]['name'] = $factory['factory_id'];
            //$containers_tally[$i]['count']['pullout'] = $count;
            $containers_tally[$i]['pullout'] = $count;

            $count = Container::select('container_number')
            ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
            ->where('quantity','=',1)
            ->where('actual_discharge','=',$date_request)
            ->where('factory',$factory['factory_id'])
            ->count();

           
            $containers_tally[$i]['discharged'] = $count;
           // $containers_tally[$i]['count']['discharged'] = $count;


            $count = Container::select('container_number')
            ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
            ->where('quantity','=',1)
            ->where('unload','=',$date_request)
            ->where('factory',$factory['factory_id'])
            ->count();

           
            $containers_tally[$i]['unloaded'] = $count;

            $count = Container::select('container_number')
                ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                ->where('quantity','=',1)
                ->where('containers.actual_gatepass','=',$date_request)
                ->where('factory',$factory['factory_id'])
                ->count();

           
            $containers_tally[$i]['gatepass'] = $count;
            //$containers_tally[$i]['count']['unloaded'] = $count;


            $count_irs = Container::select('container_number')
            ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
            ->where('quantity','=',1)
            ->where('dismounted_date','=',$date_request)
            ->where('dismounted_cy','=','IRS BACAO')
            ->where('factory',$factory['factory_id'])
            ->count();

            // $count_cez = Container::select('container_number')
            // ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
            // ->where('quantity','=',1)
            // ->where('dismounted_date','=',$date_request)
            // ->whereIn('dismounted_cy',['CEZ 1 PUTOL', 'CEZ 2 PUTOL'])
            // ->where('factory',$factory['factory_id'])
            // ->count();

            $count_cy = Container::select('container_number')
            ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
            ->where('quantity','=',1)
            ->where('dismounted_date','=',$date_request)
            //->whereNotIn('dismounted_cy',['CEZ 1 PUTOL', 'CEZ 2 PUTOL','IRS BACAO'])
            ->whereNotIn('dismounted_cy',['IRS BACAO'])
            ->where('factory',$factory['factory_id'])
            ->count();


            $containers_tally[$i]['irs'] = $count_irs;
            $containers_tally[$i]['cy'] = $count_cy;
            
            $i++;

        }

        $summary = [];
        //$summary['dismounted'] = $dismounted;
        $summary['containers_tally'] = $containers_tally;

        JavaScript::put([
            'summary' => $summary,
            'date_request' => $date_request
        ]);  
        //return $unloaded;
        return view('pages.summary_tally_breakdown');
    }

    public function excel_import(){

       return view('pages.excel_import');

    }

    /*
            CHECKING ERR 504
            for($line = 0; $line < (count($rows) - 1); $line++ ){
               // echo (strlen(trim( $rows[$line][25] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][25])) : null;
                //print_r( $rows[$line]);
                if(trim($rows[$line][25]) == 'Err:504'){
                   echo $this->getTargetGatepass(date('Y-m-d', strtotime($rows[$line][23])));
                }
               echo '<hr>';
            }


      

    */
    public function doexcel_import(Request $request){
       
        if($request->hasFile('csvMaterial')){


            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
             $rows = array_map('str_getcsv',explode("\n",$csvData));

            $list_exist_bl = Bill_of_lading::select('bl_no')->get()->toArray();
            if(count($list_exist_bl) > 0){
                    foreach($list_exist_bl as $list){
                        $exist_bl[] = $list['bl_no'];
                    }   
            }else{
                $exist_bl = [];
            }
          
            $exist_bl;

            // "WKG",
            // "APLU086791373",
            // "AX1810175/WFPW, AX1810176/WFPW, AX1810177/WFPW",
            // "WESTERN FOREST PRODUCTS INC.",
            // "LUMBER",
            // "XIN LOS ANGELES V.126",
            // "ARGOS 035?",
            // "APL",
            // "NO FORWARDER",
            // "HRD BROKERAGE",
            // "VANCOUVER",
            // "CANADA",
            // "SOUTH",
            // "19 CNTRS",
            // "1",
            // "40 HC",
            // "CMAU4892384",
            // "10/30/18",
            // "13:20",
            // "11/03/18",
            // "10/25/18",
            // "12/07/18"
            // 11/16/18	Actual time arrival
            // 11/16/18	Actual Bething time
            // 11/17/18	Actual Discharge
            // 11/19/18 Target Gate pass
            // IRS BACAO DISMOUNTED CY
            // 09/20/18	DISMOUNTED DATE
            // 09/20/18	PULL OUT
            // 09/21/18	UNLOAD
            // ATI	RETURN CY
            // 09/22/18	RETURN DATE
            // J.ZAPATA	RETURN TRUCKER
            // 88 	RETURN BOX NUMBER
            // 1742 RETURN SUMMARY NUMBER
            // MSDS PROBLEM NEED CERT FROM DOE      REASON OF DELAY DELIVERY


           


            $success = 0;
            for($line = 0; $line < (count($rows) - 1); $line++ ){




                if(strlen(trim($rows[$line][1])) == 0){
                    continue; 
                }
               

                if (in_array($rows[$line][1], $exist_bl))
                {
                    continue; 
                }
                if(strlen(trim( $rows[$line][25] )) == 0 ){
                    $target_gatepass = null;
                }else{
                    if(trim($rows[$line][25]) == 'Err:504'){
                        $target_gatepass = $this->getTargetGatepass(date('Y-m-d', strtotime($rows[$line][23])));
                    }else{
                        $target_gatepass = date('Y-m-d', strtotime($rows[$line][25]));
                    }
                }
               
                foreach( explode(',',$rows[$line][2])  as $key=>$value){
                    $IN_DATA[] = array( 'bl_no_fk' => $rows[$line][1],
                    'invoice_number' => $value);
                   
                }
               

                //Containers

                // Container::insert([
                //    'bl_no_fk'  => $rows[$line][1],
                //    'quantity' => $rows[$line][14],
                //    'container_type'  => $rows[$line][15],
                //    'container_number' => $rows[$line][16],
                // ]);

                //commodity
                foreach( explode(',',$rows[$line][4])  as $key=>$value){
                    $COMMODITY_DATA[] = array('bl_no_fk' => $rows[$line][1],
                    'commodity' => $value);
                  
                    // Container::insert([
                    //     'bl_no_fk' => $rows[$line][1],
                    //     'invoice_number' => $value
                    // ]);
                }

                //return $rows[$line];
                if(!isset($prev_bl)){

                    $prev_bl = $rows[$line][1];
                  

                    // Bill_of_lading::insert([
                    //     'factory' => $rows[$line][0],
                    //     'bl_no' => $rows[$line][1],
                    //     'supplier' => $rows[$line][3],
                    //     'vessel' => $rows[$line][5],
                    //     'connecting_vessel' => $rows[$line][6],
                    //     'shipping_line' => $rows[$line][7],
                    //     'forwarder' => $rows[$line][8],
                    //     'broker' => $rows[$line][9],
                    //     'pol' => $rows[$line][10],
                    //     'country' => $rows[$line][11],
                    //     'pod' => $rows[$line][12],
                    //     'volume' => $rows[$line][13],
                    //     'shipping_docs' => date('Y-m-d', strtotime($rows[$line][17])) . ' ' .  $rows[$line][18],
                    //     'processing_date' => date('Y-m-d', strtotime($rows[$line][19])),
                    //     'processing_date' => date('Y-m-d', strtotime($rows[$line][19])),
                    //     'estimated_time_departure' => date('Y-m-d', strtotime($rows[$line][20])),
                    //     'estimated_time_arrival' => date('Y-m-d', strtotime($rows[$line][21])),
                    // ]);

                    //invoice

                   
                  
                    DB::beginTransaction();

                    try {
                
                        DB::table('bill_of_ladings')->insert([
                            'factory' => (strlen(trim( $rows[$line][0] )) > 0) ? $rows[$line][0] : null ,
                            'bl_no' => (strlen(trim( $rows[$line][1] )) > 0) ? $rows[$line][1] : null,
                            'supplier' => (strlen(trim( $rows[$line][3] )) > 0) ? $rows[$line][3] : null ,
                            'vessel' => (strlen(trim( $rows[$line][5] )) > 0) ? $rows[$line][5] : null ,
                            'connecting_vessel' => (strlen(trim($rows[$line][6] )) > 0) ? $rows[$line][6] : null ,
                            'shipping_line' => (strlen(trim($rows[$line][7]  )) > 0) ? $rows[$line][7] : null,
                            'forwarder' => (strlen(trim($rows[$line][8]  )) > 0) ?  $rows[$line][8] : null,
                            'broker' => (strlen(trim($rows[$line][9]  )) > 0) ?  $rows[$line][9] : null,
                            'pol' => (strlen(trim($rows[$line][10]  )) > 0) ?  $rows[$line][10] : null,
                            'country' => (strlen(trim($rows[$line][11]  )) > 0) ?  $rows[$line][11] : null,
                            'pod' => (strlen(trim($rows[$line][12]  )) > 0) ?  $rows[$line][12] : null,
                            'volume' => (strlen(trim($rows[$line][13]  )) > 0) ?  $rows[$line][13] : null,
                            'shipping_docs' => (strlen(trim( $rows[$line][17] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][17])) : null  . ' ' .  $rows[$line][18],
                            'processing_date' => (strlen(trim( $rows[$line][19] )) > 0 ) ?  date('Y-m-d', strtotime($rows[$line][19])) : null,
                            'estimated_time_departure' =>  (strlen(trim( $rows[$line][20] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][20])) : null,
                            'estimated_time_arrival' =>   (strlen(trim( $rows[$line][21] )) > 0 ) ?  date('Y-m-d', strtotime($rows[$line][21])) : null,
                            'actual_time_arrival' => (strlen(trim( $rows[$line][22] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][22])) : null,
                            'actual_berthing_date' => (strlen(trim( $rows[$line][23] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][23])) : null,
                        ]);
                        DB::table('bill_of_lading_invoices')->insert( $IN_DATA );
                        DB::table('bill_of_lading_commodities')->insert( $COMMODITY_DATA );
                        DB::table('containers')->insert([
                            'bl_no_fk'  => (strlen(trim( $rows[$line][1] )) > 0) ? $rows[$line][1] : null, 
                            'quantity' => (strlen(trim( $rows[$line][14] )) > 0 ) ? $rows[$line][14] : 1,
                            'container_type'  => (strlen(trim( $rows[$line][15] )) > 0 ) ? $rows[$line][15] : null ,
                            'container_number' => (strlen(trim( $rows[$line][16] )) > 0 ) ? $rows[$line][16] : null,
                            'actual_discharge' => (strlen(trim( $rows[$line][24] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][24])) : null,
                            'actual_gatepass' =>  $target_gatepass,
                            'dismounted_cy' => (strlen(trim( $rows[$line][26] )) > 0 ) ? $rows[$line][26] : null,
                            'dismounted_date' => (strlen(trim( $rows[$line][27] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][27])) : null,
                            'pull_out' => (strlen(trim( $rows[$line][28] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][28])) : null,
                            'unload' => (strlen(trim( $rows[$line][29] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][29])) : null,
                            'return_cy' => (strlen(trim( $rows[$line][30] )) > 0 ) ? $rows[$line][30] : null,
                            'return_date' => (strlen(trim( $rows[$line][31] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][31])) : null,
                            'trucker' => (strlen(trim( $rows[$line][32] )) > 0 ) ? $rows[$line][32] : null,
                            'return_box_number' => (strlen(trim( $rows[$line][33] )) > 0 ) ? $rows[$line][33] : 0,
                            'return_summary_number' => (strlen(trim( $rows[$line][34] )) > 0 ) ? $rows[$line][34] : 0,
                            'reason_of_delay_gatepass' => (strlen(trim( $rows[$line][35] )) > 0 ) ? $rows[$line][35] : null,
                         ]);
                        DB::commit();
                        // all good
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                        // something went wrong
                    }

                   
                }else{
                    DB::beginTransaction();

                    try {
                        if($prev_bl != $rows[$line][1]){
                            $prev_bl =  $rows[$line][1];
                        

                            
                            // Bill_of_lading::insert([
                            // 'factory' => (strlen(trim( $rows[$line][0] )) > 0) ? $rows[$line][0] : null ,
                            //     'bl_no' => (strlen(trim( $rows[$line][1] )) > 0) ? $rows[$line][1] : null,
                            //     'supplier' => (strlen(trim( $rows[$line][3] )) > 0) ? $rows[$line][3] : null ,
                            //     'vessel' => (strlen(trim( $rows[$line][5] )) > 0) ? $rows[$line][5] : null ,
                            //     'connecting_vessel' => (strlen(trim($rows[$line][6] )) > 0) ? $rows[$line][6] : null ,
                            //     'shipping_line' => (strlen(trim($rows[$line][7]  )) > 0) ? $rows[$line][7] : null,
                            //     'forwarder' => (strlen(trim($rows[$line][8]  )) > 0) ?  $rows[$line][8] : null,
                            //     'broker' => (strlen(trim($rows[$line][9]  )) > 0) ?  $rows[$line][9] : null,
                            //     'pol' => (strlen(trim($rows[$line][10]  )) > 0) ?  $rows[$line][10] : null,
                            //     'country' => (strlen(trim($rows[$line][11]  )) > 0) ?  $rows[$line][11] : null,
                            //     'pod' => (strlen(trim($rows[$line][12]  )) > 0) ?  $rows[$line][12] : null,
                            //     'volume' => (strlen(trim($rows[$line][13]  )) > 0) ?  $rows[$line][13] : null,
                            //     'shipping_docs' => date('Y-m-d', strtotime($rows[$line][17])) . ' ' .  $rows[$line][18],
                            //     'processing_date' => date('Y-m-d', strtotime($rows[$line][19])),
                            //     'processing_date' => date('Y-m-d', strtotime($rows[$line][19])),
                            //     'estimated_time_departure' => date('Y-m-d', strtotime($rows[$line][20])),
                            //     'estimated_time_arrival' => date('Y-m-d', strtotime($rows[$line][21])),
                            //     'actual_time_arrival' => (strlen(trim( $rows[$line][22] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][22])) : null,
                            //     'actual_berthing_date' => (strlen(trim( $rows[$line][23] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][23])) : null,
                            //     ]);
                            // //invoice
                            //     foreach( explode(',',$rows[$line][2])  as $key=>$value){
                            //         Bill_of_Lading_Invoice::insert([
                            //             'bl_no_fk' => $rows[$line][1],
                            //             'invoice_number' => $value
                            //         ]);
                            //     }

                            //     //commidities
                            //     foreach( explode(',',$rows[$line][4])  as $key=>$value){
                            //         Bill_of_Lading_Commodity::insert([
                            //             'bl_no_fk' => $rows[$line][1],
                            //             'commodity' => $value
                            //         ]);
                            //     }


                            DB::table('bill_of_ladings')->insert([
                                'factory' => (strlen(trim( $rows[$line][0] )) > 0) ? $rows[$line][0] : null ,
                                'bl_no' => (strlen(trim( $rows[$line][1] )) > 0) ? $rows[$line][1] : null,
                                'supplier' => (strlen(trim( $rows[$line][3] )) > 0) ? $rows[$line][3] : null ,
                                'vessel' => (strlen(trim( $rows[$line][5] )) > 0) ? $rows[$line][5] : null ,
                                'connecting_vessel' => (strlen(trim($rows[$line][6] )) > 0) ? $rows[$line][6] : null ,
                                'shipping_line' => (strlen(trim($rows[$line][7]  )) > 0) ? $rows[$line][7] : null,
                                'forwarder' => (strlen(trim($rows[$line][8]  )) > 0) ?  $rows[$line][8] : null,
                                'broker' => (strlen(trim($rows[$line][9]  )) > 0) ?  $rows[$line][9] : null,
                                'pol' => (strlen(trim($rows[$line][10]  )) > 0) ?  $rows[$line][10] : null,
                                'country' => (strlen(trim($rows[$line][11]  )) > 0) ?  $rows[$line][11] : null,
                                'pod' => (strlen(trim($rows[$line][12]  )) > 0) ?  $rows[$line][12] : null,
                                'volume' => (strlen(trim($rows[$line][13]  )) > 0) ?  $rows[$line][13] : null,
                                'shipping_docs' => (strlen(trim( $rows[$line][17] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][17])) : null  . ' ' .  $rows[$line][18],
                                'processing_date' => (strlen(trim( $rows[$line][19] )) > 0 ) ?  date('Y-m-d', strtotime($rows[$line][19])) : null,
                                'estimated_time_departure' =>  (strlen(trim( $rows[$line][20] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][20])) : null,
                                'estimated_time_arrival' =>   (strlen(trim( $rows[$line][21] )) > 0 ) ?  date('Y-m-d', strtotime($rows[$line][21])) : null,
                                'actual_time_arrival' => (strlen(trim( $rows[$line][22] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][22])) : null,
                                'actual_berthing_date' => (strlen(trim( $rows[$line][23] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][23])) : null,
                            ]);
                            DB::table('bill_of_lading_invoices')->insert( $IN_DATA );
                            DB::table('bill_of_lading_commodities')->insert( $COMMODITY_DATA );
                        }

                        //Containers

                        // Container::insert([
                        //     'bl_no_fk'  => (strlen(trim( $rows[$line][1] )) > 0) ? $rows[$line][1] : null, 
                        //     'quantity' => (strlen(trim( $rows[$line][14] )) > 0 ) ? $rows[$line][14] : 1,
                        //     'container_type'  => (strlen(trim( $rows[$line][15] )) > 0 ) ? $rows[$line][15] : null ,
                        //     'container_number' => (strlen(trim( $rows[$line][16] )) > 0 ) ? $rows[$line][16] : null,
                        //     'actual_discharge' => (strlen(trim( $rows[$line][24] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][24])) : null,
                        //     'actual_gatepass' => $target_gatepass,
                        //     'dismounted_cy' => (strlen(trim( $rows[$line][26] )) > 0 ) ? $rows[$line][26] : null,
                        //     'dismounted_date' => (strlen(trim( $rows[$line][27] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][27])) : null,
                        //     'pull_out' => (strlen(trim( $rows[$line][28] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][28])) : null,
                        //     'unload' => (strlen(trim( $rows[$line][29] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][29])) : null,
                        //     'return_cy' => (strlen(trim( $rows[$line][30] )) > 0 ) ? $rows[$line][30] : null,
                        //     'return_date' => (strlen(trim( $rows[$line][31] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][31])) : null,
                        //     'trucker' => (strlen(trim( $rows[$line][32] )) > 0 ) ? $rows[$line][32] : null,
                        //     'return_box_number' => (strlen(trim( $rows[$line][33] )) > 0 ) ? $rows[$line][33] : 0,
                        //     'return_summary_number' => (strlen(trim( $rows[$line][34] )) > 0 ) ? $rows[$line][34] : 0,
                        //     'reason_of_delay_gatepass' => (strlen(trim( $rows[$line][35] )) > 0 ) ? $rows[$line][35] : null,
                        // ]);
                        DB::table('containers')->insert([
                            'bl_no_fk'  => (strlen(trim( $rows[$line][1] )) > 0) ? $rows[$line][1] : null, 
                            'quantity' => (strlen(trim( $rows[$line][14] )) > 0 ) ? $rows[$line][14] : 1,
                            'container_type'  => (strlen(trim( $rows[$line][15] )) > 0 ) ? $rows[$line][15] : null ,
                            'container_number' => (strlen(trim( $rows[$line][16] )) > 0 ) ? $rows[$line][16] : null,
                            'actual_discharge' => (strlen(trim( $rows[$line][24] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][24])) : null,
                            'actual_gatepass' =>  $target_gatepass,
                            'dismounted_cy' => (strlen(trim( $rows[$line][26] )) > 0 ) ? $rows[$line][26] : null,
                            'dismounted_date' => (strlen(trim( $rows[$line][27] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][27])) : null,
                            'pull_out' => (strlen(trim( $rows[$line][28] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][28])) : null,
                            'unload' => (strlen(trim( $rows[$line][29] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][29])) : null,
                            'return_cy' => (strlen(trim( $rows[$line][30] )) > 0 ) ? $rows[$line][30] : null,
                            'return_date' => (strlen(trim( $rows[$line][31] )) > 0 ) ? date('Y-m-d', strtotime($rows[$line][31])) : null,
                            'trucker' => (strlen(trim( $rows[$line][32] )) > 0 ) ? $rows[$line][32] : null,
                            'return_box_number' => (strlen(trim( $rows[$line][33] )) > 0 ) ? $rows[$line][33] : 0,
                            'return_summary_number' => (strlen(trim( $rows[$line][34] )) > 0 ) ? $rows[$line][34] : 0,
                            'reason_of_delay_gatepass' => (strlen(trim( $rows[$line][35] )) > 0 ) ? $rows[$line][35] : null,
                         ]);

                         DB::commit();
                         // all good
                     } catch (\Exception $e) {
                         DB::rollback();
                         return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                         // something went wrong
                     }

                }

               


                
                $success++;
            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;
            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'INSERT RECORDS',
                'uploaded_by' => Session::get('employee_number')
            ]);

            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : '. $success.' total, Query took '.  $seconds .' seconds');

        }else{
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }



    }


    public function doexcel_import_docs_team(Request $request){
        if($request->hasFile('csvMaterial')){


            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $rows = array_map('str_getcsv',explode("\n",$csvData));

            

            /*
            OOLU4034505420	
            11/01/18	
            11/01/18	
            AS RED, FOR MANIFEST

            */ 

           


            $success = 0;
            for($line = 0; $line < (count($rows) - 1); $line++ ){

                    if(strlen(trim($rows[$line][0])) == 0){
                        continue; 
                    }
                



                    if(strlen(trim( $rows[$line][3] )) > 0){
                        $remarks_of_docs = strtoupper($rows[$line][3]);
                       

                        if (strpos($remarks_of_docs, 'AS RED') !== false) {
                            $assessment_tag = 'RED';
                        }else{
                            $assessment_tag = 'YELLOW';
                        }


                    }else{
                        $remarks_of_docs = null;
                        $assessment_tag = null;
                    }
            

                    DB::beginTransaction();

                    try {
                
                        DB::table('bill_of_ladings')
                        ->whereBlNo($rows[$line][0])
                        ->update([
                           
                            'date_endorse' => (strlen(trim( $rows[$line][1] )) > 0) ? date('Y-m-d', strtotime($rows[$line][1])) : null,
                            'actual_process' => (strlen(trim( $rows[$line][3] )) > 0) ? date('Y-m-d', strtotime($rows[$line][2])) : null ,
                            'remarks_of_docs' => $remarks_of_docs ,
                            'assessment_tag' => $assessment_tag,
                          
                        ]);
                        
                       
                        DB::commit();
                        // all good
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                        
                        // something went wrong
                    }

                
                $success++;
            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;
            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE RECORDS DOCS TEAM',
                'uploaded_by' => Session::get('employee_number')
            ]);

            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : '. $success.' total, Query took '.  $seconds .' seconds');

        }else{
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }



    }

    public function doexcel_import_update_tsad(Request $request){
        if($request->hasFile('csvMaterial')){


            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $rows = array_map('str_getcsv',explode("\n",$csvData));

           

            /*

           OOLU4034505420 BL_NO
           1234 TSAD NO


            */ 

           


            $success = 0;
            for($line = 0; $line < (count($rows) - 1); $line++ ){

                    if(strlen(trim($rows[$line][0])) == 0){
                        continue; 
                    }
                
                    DB::beginTransaction();

                    try {
                
                        DB::table('bill_of_ladings')
                        ->whereBlNo($rows[$line][0])
                        ->update([
                            'tsad_no' => (strlen(trim( $rows[$line][1] )) > 0) ? $rows[$line][1] : null,
                        ]);
                        
                       
                        DB::commit();
                        // all good
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                       
                        // something went wrong
                    }

                
                $success++;
            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;
            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE TSAD',
                'uploaded_by' => Session::get('employee_number')
            ]);

            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : '. $success.' total, Query took '.  $seconds .' seconds');

        }else{
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }



    }

    public function doexcel_import_update_gatepass(Request $request){
        if($request->hasFile('csvMaterial')){


            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $rows = array_map('str_getcsv',explode("\n",$csvData));

        
            /*

                OOLU4034505420	BL_NO_FK
                OOLU9587787	    CONTAINER #
                11/12/18	        ACTUAL GATEPASS
                TEST 1            REASON OF DELAY GATEPASS

            */ 

           


            $success = 0;
            for($line = 0; $line < (count($rows) - 1); $line++ ){

                    if(strlen(trim($rows[$line][0])) == 0){
                        continue; 
                    }
                
                    DB::beginTransaction();

                    try {
                
                        DB::table('containers')
                        ->whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->update([
                            'actual_gatepass' => (strlen(trim( $rows[$line][2] )) > 0) ? date('Y-m-d', strtotime($rows[$line][2]))  : null,
                            'reason_of_delay_gatepass' => (strlen(trim( $rows[$line][3] )) > 0) ? $rows[$line][3] : null,
                        ]);
                        
                       
                        DB::commit();
                        // all good
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                        // something went wrong
                    }

                
                $success++;
            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;
            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE GATEPASS',
                'uploaded_by' => Session::get('employee_number')
            ]);

            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : '. $success.' total, Query took '.  $seconds .' seconds');

        }else{
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }



    }

    public function doexcel_import_update_validity(Request $request){
        if($request->hasFile('csvMaterial')){


            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $rows = array_map('str_getcsv',explode("\n",$csvData));

        
            /*

                OOLU4034505420	BL NO
                OOLU9587787	    CONTAINER #
                11/02/18	    VAL STORAGE
                11/03/18	    VAL DEMURRAGE
                11/03/18	    REVAL STORAGE
                11/04/18        REVAL DEMURRAGE


            */ 

           


            $success = 0;
            for($line = 0; $line < (count($rows) - 1); $line++ ){

                    if(strlen(trim($rows[$line][0])) == 0){
                        continue; 
                    }
                
                    DB::beginTransaction();

                    try {
                
                        DB::table('containers')
                        ->whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->update([
                            'validity_storage' => (strlen(trim( $rows[$line][2] )) > 0) ? date('Y-m-d', strtotime($rows[$line][2])) : null,
                            'validity_demurrage' => (strlen(trim( $rows[$line][3] )) > 0) ? date('Y-m-d', strtotime($rows[$line][3])) : null,
                            'revalidity_storage' => (strlen(trim( $rows[$line][4] )) > 0) ? date('Y-m-d', strtotime($rows[$line][4]))  : null,
                            'revalidity_demurrage' => (strlen(trim( $rows[$line][5] )) > 0) ? date('Y-m-d', strtotime($rows[$line][5])) : null,
                        ]);
                        
                       
                        DB::commit();
                        // all good
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                        // something went wrong
                    }

                
                $success++;
            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;
            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE VALIDITY',
                'uploaded_by' => Session::get('employee_number')
            ]);
            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : '. $success.' total, Query took '.  $seconds .' seconds');

        }else{

            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        
        }



    }

    public function doexcel_import_update_booking_time(Request $request){
        if($request->hasFile('csvMaterial')){


            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
             $rows = array_map('str_getcsv',explode("\n",$csvData));

        
            /*

            OOLU4034505420	BL NO
            OOLU9587787	    CONTAINER #
            ACAI	        TRUCKER
            1:00 午後       BOOKING TIME


            */ 

           


            $success = 0;
            for($line = 0; $line < (count($rows) - 1); $line++ ){

                    if(strlen(trim($rows[$line][0])) == 0){
                        continue; 
                    }
                
                    DB::beginTransaction();

                    try {
                
                        DB::table('containers')
                        ->whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->update([
                            'trucker' => (strlen(trim( $rows[$line][2] )) > 0) ? $rows[$line][2] : null,
                            'booking_time' => (strlen(trim( $rows[$line][3] )) > 0) ? $rows[$line][3] : null,
                        ]);
                        
                       
                        DB::commit();
                        // all good
                    } catch (\Exception $e) {
                        DB::rollback();
                       // return $e->getMessage();
                        return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                        // something went wrong
                    }

                
                $success++;
            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;
            
            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE BOOKING TIME',
                'uploaded_by' => Session::get('employee_number')
            ]);

            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : '. $success.' total, Query took '.  $seconds .' seconds');

        }else{
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }



    }


    private function getTargetGatepass($value){
          // Create a new DateTime object
          $date = new DateTime($value);
          $date->modify('+1 day');
             // Output
             $value =  $date->format('Y-m-d');
             if((date('N', strtotime($value)) >= 6) == 1) {
                 // Create a new DateTime object
                 $date = new DateTime($value);
         
                 // Modify the date it contains
                 $date->modify('next monday');
         
                 // Output
                 $newdate =  $date->format('Y-m-d');
             
         
                 while(Holiday::where('holiday_date','=',$newdate)->count() == 1){
                     $date = new DateTime( (string) $newdate )  ;
                     $date->modify('+1 day');
                     $newdate =  $date->format('Y-m-d');
                     if((date('N', strtotime((string) $newdate )) >= 6) == 1) {
                         // Modify the date it contains
                         $date->modify('next monday');
                         // Output
                         $newdate =  $date->format('Y-m-d');
                     }
             
                 
                 }
         
                 //echo date("Y-m-d", strtotime("next monday"));
             }else{
                 $date = new DateTime($value);
                 
                 // Output
                 $newdate =  $date->format('Y-m-d');
         
                 while(Holiday::where('holiday_date','=',$newdate)->count() == 1){
                     $date = new DateTime( (string) $newdate )  ;
                     $date->modify('+1 day');
                     $newdate =  $date->format('Y-m-d');
                     if((date('N', strtotime((string) $newdate )) >= 6) == 1) {
                         // Modify the date it contains
                         $date->modify('next monday');
                         // Output
                         $newdate =  $date->format('Y-m-d');
                     }
             
                 
                 }
         
                 //echo Holiday::where('holiday_date','=', (string) $newdate )->count();
             
         }
 
        return $newdate;
    }

}
