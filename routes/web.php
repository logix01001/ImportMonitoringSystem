<?php
use App\Port;
use App\factory;
use App\Holiday;
use App\Supplier;
use App\Container;
use App\Impexsv4_port;
use GuzzleHttp\Client;
use App\bill_of_lading;
use App\Exports\PODExport;
use App\Impexsv4_supplier;
use App\Exports\trialExport;
use Illuminate\Http\Request;
use App\Bill_of_Lading_Invoice;
use App\Exports\WoodONLYExport;
use App\Exports\AllRecordExport;
use App\Exports\IncomingVExport;
use App\bill_of_lading_commodity;
use App\Classfile\Split;
use App\Exports\ContainerIRSExport;
use App\Exports\SummaryTallyExport;
use App\Exports\UnloadReturnExport;
use App\Exports\UploadReturnExport;
use App\Exports\ActualProcessExport;
use App\Exports\VesselWaitingExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ONBOARSHIPMENTExport;
use App\Exports\OnhandGatepassExport;
use App\Exports\ClearedShipmentExport;
use App\Exports\ContainerAtPortExport;
use App\Exports\NotYetDischargeExport;
use App\Exports\ContainerPullOutExport;
use App\Exports\LogisticsReportsExport;
use App\Exports\ShipmentOnProcessExport;
use App\Exports\ContainersNotReturnExport;
use App\Exports\DismountedWithChassiExport;
use App\Exports\DocumentationProcessExport;

use App\Exports\ShipmentWithOutGatepassExport;
use App\Exports\ShipmentOnProcessOngoingExport;
use App\ImsJob;
use App\libraries\BillOfLading;

use App\Exports\AllRecordExportNew;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Route::get('/',function(){

// });

 Route::get('/testonly',function(){

        $select = ['factory','bl_no','pod','volume','container_type','remarks_of_docs','estimated_time_arrival','date_endorse','tsad_no'];
        $Query =  bill_of_lading::select( $select, DB::raw('COUNT(*) as container_volume from containers where `quantity` = 1 and `bl_no_fk` = bl_no' ) )
                ->distinct()
                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                ->whereQuantity(1)
                ->whereNull('e2m')
                ->orWhereNull('date_endorse')
                ->orWhereNull('date_approve_ip')
                ->orWhereNull('assessment_tag')
                ->orWhereNull('remarks_of_docs')
                ->orWhereNull('tsad_no')
                //->orderBy('registry_no', 'DESC')
                ->orderBy('estimated_time_arrival', 'ASC')
                ->get();

        $list_of_BOL = $Query ;

        $data = [];
        $index = 0;
        if(count($list_of_BOL) > 0){

        //  $Obj = new \App\Libraries\BillOfLading($list_of_BOL);

        //  $data = $Obj->getData();

        return $list_of_BOL;

        }

    exit;
        return getenv();

    exit;
    //return date('m  ', strtotime( $string ));
    //Last day of current month.
    $date_request = date('Y-m-d');

    return bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
    ->whereQuantity(1)
    ->whereNotIn('connecting_vessel',['T.B.A.'])
    ->whereNotIn('pod',['TBA'])
    ->whereMonth('dismounted_date',1)
    ->whereYear('dismounted_date',2019)
    ->where('dismounted_cy','WITH CHASSI')
    ->get();


exit;
    $lastDayThisMonth = date("Y-m-t",strtotime("2018-12"));

//Print it out for example purposes.
echo  $five_days_before = date('Y-m-d', strtotime('2019-02-01' . ' -5 days'));
    echo $lastDayThisMonth;
 });

Route::get('/extract_actual_process', function () {

    return Excel::download(new ActualProcessExport(), 'actual_process.xlsx');

});
Route::get('/extract_summary/{factory?}/{category?}/{date_request?}/{all?}/', function ($factory = null, $category = null, $date_request = null, $all = false) {
    if ($factory == null || $category == null || $date_request == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new SummaryTallyExport($factory, $category, $date_request, $all), 'allrecord.xlsx');

});

Route::get('/extract_container_at_port/{factory?}/{container_type?}/{factory_all?}/{container_all?}/', function
($factory = null, $container_type = null, $factory_all = false, $container_all = false) {

    if ($factory == null || $container_type == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new ContainerAtPortExport($factory, $container_type, $factory_all, $container_all), 'allrecord.xlsx');

});


Route::get('/extract_summary2/{factory?}/{category?}/{date_request?}/{all?}/{reference?}/{dateMonth?}/{dateYear?}/{start?}/{end?}', function
($factory = null, $category = null, $date_request = null, $all = false,$reference = null,$dateMonth = null,$dateYear = null,$start = null, $end = null) {

	if($factory == '-'){
		$factory = null;
	}
	if($dateMonth == '-'){
		$dateMonth = null;
	}
	if($dateYear == '-'){
		$dateYear = null;
	}
	if($start == '-'){
		$start = null;
	}
	if($end == '-'){
		$end = null;
	}

    if ($factory == null || $category == null || $date_request == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new SummaryTallyExport($factory, $category, $date_request, $all,$reference,$dateMonth,$dateYear,$start, $end ), 'allrecord.xlsx');

});

Route::get('/extract_incoming_vessel/{factory?}/{reference?}/{pod?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}/{as_of_now?}', function
($factory = all, $reference = null, $pod = all, $date_request = null,$dateMonth = null,$dateYear = null,$start = null, $end = null,$as_of_now= null) {
    if ($factory == null  || $date_request == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new IncomingVExport($factory,$reference,$pod,$date_request,$dateMonth,$dateYear,$start, $end,$as_of_now ), 'allrecord.xlsx');

});



Route::get('/extract_vessel_waiting/{factory?}/{reference?}/{pod?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}/{as_of_now?}', function
($factory = all, $reference = null, $pod = all, $date_request = null,$dateMonth = null,$dateYear = null,$start = null, $end = null,$as_of_now= null) {
    if ($factory == null  || $date_request == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new VesselWaitingExport($factory,$reference,$pod,$date_request,$dateMonth,$dateYear,$start, $end,$as_of_now ), 'allrecord.xlsx');

});


Route::get('/extract_onboard_shipment/{factory?}/{reference?}/{pod?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}/{as_of_now?}', function
($factory = all, $reference = null, $pod = all, $date_request = null,$dateMonth = null,$dateYear = null,$start = null, $end = null,$as_of_now = null) {
    if ($factory == null  || $date_request == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new ONBOARSHIPMENTExport($factory,$reference,$pod,$date_request,$dateMonth,$dateYear,$start, $end,$as_of_now ), 'allrecord.xlsx');

});

Route::get('/extract_notyet_discharge/{factory?}/{reference?}/{pod?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}/{as_of_now?}', function
($factory = all, $reference = null, $pod = all, $date_request = null,$dateMonth = null,$dateYear = null,$start = null, $end = null,$as_of_now = null) {
    if ($factory == null  || $date_request == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new NotYetDischargeExport($factory,$reference,$pod,$date_request,$dateMonth,$dateYear,$start, $end,$as_of_now ), 'allrecord.xlsx');

});


Route::get('/extract_shipment_onprocess/{factory?}/{reference?}/{pod?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}', function
($factory = all, $reference = null, $pod = all, $date_request = null,$dateMonth = null,$dateYear = null,$start = null, $end = null) {
    if ($factory == null  || $date_request == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new ShipmentOnProcessExport($factory,$reference,$pod,$date_request,$dateMonth,$dateYear,$start, $end ), 'allrecord.xlsx');

});
Route::get('/extract_shipment_without_gatepass/{factory?}/{reference?}/{pod?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}/{as_of_now?}', function
($factory = all, $reference = null, $pod = all, $date_request = null,$dateMonth = null,$dateYear = null,$start = null, $end = null,$as_of_now = null) {
    if ($factory == null  || $date_request == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new ShipmentWithOutGatepassExport($factory,$reference,$pod,$date_request,$dateMonth,$dateYear,$start, $end,$as_of_now ), 'allrecord.xlsx');

});


Route::get('/extract_onhand_gatepass/{factory?}/{reference?}/{pod?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}/{as_of_now?}', function
($factory = all, $reference = null, $pod = all, $date_request = null,$dateMonth = null,$dateYear = null,$start = null, $end = null,$as_of_now = null) {


    if ($factory == null  || $date_request == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new OnhandGatepassExport($factory,$reference,$pod,$date_request,$dateMonth,$dateYear,$start, $end,$as_of_now ), 'allrecord.xlsx');

});



Route::get('/extract_container_irs/{factory?}', function
($factory = all) {
    if ($factory == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new ContainerIRSExport($factory), 'allrecord.xlsx');

});

Route::get('/extract_dismounted_with_chassi/{factory?}/{reference?}/{cy?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}', function
($factory = all, $reference = null, $cy = all, $date_request = null,$dateMonth = null,$dateYear = null,$start = null, $end = null) {
    if ($factory == null  || $date_request == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new DismountedWithChassiExport($factory,$reference,$cy,$date_request,$dateMonth,$dateYear,$start, $end ), 'allrecord.xlsx');

});

Route::get('/extract_container_not_return/{factory?}/{reference?}/{date_request?}/{dateMonth?}/{dateYear?}/{start?}/{end?}/{as_of_now?}', function
($factory = all, $reference = null, $date_request = null,$dateMonth = null,$dateYear = null,$start = null, $end = null,$as_of_now = null) {
    if ($factory == null  || $date_request == null) {
        return Redirect::to('/index');
    }



	if($dateMonth == '-'){
		$dateMonth = null;
	}
	if($dateYear == '-'){
		$dateYear = null;
	}
	if($start == '-'){
		$start = null;
	}
	if($end == '-'){
		$end = null;
	}


    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new ContainersNotReturnExport($factory,$reference,$date_request,$dateMonth,$dateYear,$start, $end,$as_of_now ), 'allrecord.xlsx');

});



Route::get('/extract_logistics_reports/{vessel?}/{ETA?}/{ATA?}/{ATB?}/{port?}/{gatepass?}/{delivery?}/{unloaded?}/{returned?}/{start?}/{end?}', function
($vessel = all, $ETA = null, $ATA = null,$ATB = null,$port = null,$gatepass = null, $delivery = null,$unloaded = null,$returned = null,$start = null,$end = null) {
    if ($vessel == null  || $ETA == null) {
        return Redirect::to('/index');
    }
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new LogisticsReportsExport($vessel,$ETA,$ATA,$ATB,$port,$gatepass, $delivery,$unloaded,$returned ,$start ,$end  ), 'allrecord.xlsx');

});

Route::get('/extract_container_pull_out', function () {
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new ContainerPullOutExport(), 'container_for_pull_out.xlsx');

});

Route::get('/extract_UnloadReturnExport', function () {
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new UnloadReturnExport(), 'container_unload_return.xlsx');

});



Route::get('/extract_ClearedShipmentExport', function () {
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new ClearedShipmentExport(), 'cleared_shipment.xlsx');

});

Route::get('/extract_ShipmentOnProcessOngoingExport', function () {
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new ShipmentOnProcessOngoingExport(), 'shipment_on_process.xlsx');

});
Route::get('/extract_DocumentationProcessExport', function () {
    ini_set('max_execution_time', 500);
    ini_set('memory_limit', '500M');
    return Excel::download(new DocumentationProcessExport(), 'Documentation_Process.xlsx');

});



Route::get('/trial_export', function () {
    ini_set('max_execution_time', 500);

    return Excel::download(new trialExport(), 'allrecord.xlsx');

});


Route::get('/extract_all', function () {

    //ini_set('max_execution_time', 1000);
    //ini_set('memory_limit', '1000M');
	/*if($_SERVER['REMOTE_ADDR'] != '10.169.141.111'){
		return 'Maintenance';
	}
	*/
	// $select = explode(",", getenv('SELECT_EXPORT'));
	// return $list_of_BOL = bill_of_lading::select( $select)
            // ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')

	// ->get();

    return Excel::download(new AllRecordExport(), 'allrecord.xlsx');

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
        'dismounted_cy',
        'dismounted_date',
        'unload',
        'pull_out')
        ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')->get();
    $data = [];
    $index = 0;
    foreach ($list_of_BOL as $row) {
        $invoices_data = [];
        $commodity = [];
        foreach (Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get() as $inv) {
            $invoices_data[] = $inv['invoice_number'];
        }

        foreach (Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get() as $cm) {
            $commodity[] = $cm['commodity'];
        }

        $row['invoice_number'] = implode(',', $invoices_data);
        $row['commodities'] = implode(',', $commodity);

        $data[$index][] = $row['factory'];
        $data[$index][] = $row['bl_no'];
        $data[$index][] = $row['invoice_number'];
        $data[$index][] = $row['supplier'];
        $data[$index][] = $row['commodities'];
        $data[$index][] = $row['connecting_vessel'];
        $data[$index][] = $row['shipping_line'];
        $data[$index][] = $row['pol'];
        $data[$index][] = $row['country'];
        $data[$index][] = $row['pod'];
        $data[$index][] = $row['volume'];
        $data[$index][] = $row['container_type'];
        $data[$index][] = $row['container_number'];
        $data[$index][] = $row['quantity'];
        $data[$index][] = $row['estimated_time_arrival'];
        $data[$index][] = $row['actual_time_arrival'];
        $data[$index][] = $row['actual_berthing_date'];
        $data[$index][] = $row['actual_discharge'];
        $data[$index][] = $row['dismounted_cy'];
        $data[$index][] = $row['dismounted_date'];
        $data[$index][] = $row['pull_out'];
        $data[$index][] = $row['unload'];

        $index++;
    }

    return $data;

});
Route::get('/actual_target', function () {

    $list = Container::select('bl_no_fk', 'actual_gatepass')
        ->whereNotNull('actual_gatepass')
        ->distinct()
        ->get();

    foreach ($list as $row) {
        Bill_of_lading::where('bl_no', $row['bl_no_fk'])
            ->update([
                'target_gatepass' => $row['actual_gatepass'],
            ]);
        echo $row['bl_no_fk'] . ' ' . $row['actual_gatepass'] . ' : SUCCESS';
        echo '<hr>';
    }
});

Route::get('/sample', function () {

    //return Bill_of_lading::find(1)->containers;
    return Bill_of_Lading::containers;
    exit;
    $client = new GuzzleHttp\Client();
    $res = $client->request('GET', 'http://ebiz.heung-a.com/cargotrace_cntr_dtl_ajax.cfm?bkno=HL40A8000800&cntrno=BMOU6427820&copno=CMOJ8B05263925', [
        'auth' => ['user', 'pass'],
    ]);
    //echo $res->getStatusCode();
    // "200"
    //echo $res->getHeader('content-type');
    // 'application/json; charset=utf8'
    return $res->getBody();

    exit;
    $bl_with_split = Container::select('container_number', 'split_bl_no_fk')
        ->whereNotNull('split_bl_no_fk')
        ->whereQuantity(1)
        ->get();
    foreach ($bl_with_split as $split) {
        $split_bl = explode(',', $split['split_bl_no_fk']);
        $obj = [];

        foreach ($split_bl as $key => $value) {
            $quantity = Container::select('quantity', 'id')
                ->where('bl_no_fk', $value)
                ->where('container_number', $split['container_number'])
                ->get();

            $obj[] = [
                'bl_no_fk' => $value,
                'quantity' => $quantity[0]['quantity'],
                'id' => $quantity[0]['id'],
            ];

        }
        $split['splits_bl_array'] = $obj;
        unset($split['split_bl_no_fk']);
    }

    return $bl_with_split;

});

Route::get('/update_port_from_impex', function () {

    $ports = Impexsv4_port::get()->toArray();

    $i = 0;
    foreach ($ports as $port) {

        $ports[$i]['port_id'] = $ports[$i]['PortId'];
        $ports[$i]['port_name'] = $ports[$i]['PortName'];

        unset($ports[$i]['PortId']);
        unset($ports[$i]['PortName']);
        unset($ports[$i]['CountryId']);

        $i++;

    }

    Port::insert($ports);

});

Route::get('/update_supplier_from_impex', function () {

    $suppliers = Impexsv4_supplier::get()->toArray();

    $i = 0;
    foreach ($suppliers as $supplier) {

        $suppliers[$i]['supplier_id'] = $suppliers[$i]['SupplierID'];
        $suppliers[$i]['supplier_name'] = $suppliers[$i]['SupplierName'];

        unset($suppliers[$i]['SupplierID']);
        unset($suppliers[$i]['SupplierName']);

        $i++;

    }

    // foreach($suppliers as $supplier){
    //     Supplier::updateOrCreate(

    //         ['supplier_id' =>  $supplier['supplier_id']],
    //         [
    //         'supplier_id' => $supplier['supplier_id'],
    //         'supplier_name' => $supplier['supplier_name']
    //         ]

    //     );

    // }

    Supplier::insert($suppliers);

});
Route::get('/test2', function () {


    //return Impexsv4_supplier::get();

    //return 'hello world';
    //return Container::get();

    //$data =  DB::select('select * from containers');
    // return  $data[0]->bl_no_fk;
    //$query = DB::connection()->getPdo()->query("select * from containers");

    //$data = $query->fetchAll(PDO::FETCH_ASSOC);

    //print_r($data);
    // dd($data);
    //$data = Container::get();

    return Container::get();

    // try{
    //     $pdo = new PDO('mysql:host=localhost;dbname=laravel_logistic_project','root','lynadmin');
    //     $pdo->setAttribute( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
    //     $stmt = $pdo->query(
    //     "
    //     SELECT
    //     *
    //     FROM
    //     containers
    //     "
    //     );
    //     return $stmt->fetchAll();
    // }catch(PDOException $e){
    //     echo $e->getMessage();
    // }
});
Route::get('/exceltest/{date_endorse?}/{type_date?}', function ($date_endorse = null, $type_date = null) {
    return Excel::download(new PODExport($date_endorse, $type_date), 'importation_extract.xlsx');
    // $BOL =  bill_of_lading::select('bl_no','pod','factory','estimated_time_arrival','remarks_of_docs','container_type','container_number')
    //     ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
    //     ->join('bill_of_lading_commodities','bill_of_lading_commodities.bl_no_fk','bill_of_ladings.bl_no')
    //     ->wherePod('SOUTH')
    //     ->orderBy('factory','DESC')->distinct()->get();

    //     return $BOL;

    // return Excel::download(new PODExport, 'users.xlsx');

    // if(Session::has('employee_number')){
    //     return Session::get('employee_number');

    // }
});

Route::get('/excelwood/{date_endorse?}/{type_date?}', function ($date_endorse = null, $type_date = null) {
//0018C88964

    return Excel::download(new WoodONLYExport($date_endorse, $type_date), 'importation_extract_wood_only.xlsx');
    // $BOL =  bill_of_lading::select('bl_no','pod','factory','estimated_time_arrival','remarks_of_docs','container_type','container_number')
    //     ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
    //     ->join('bill_of_lading_commodities','bill_of_lading_commodities.bl_no_fk','bill_of_ladings.bl_no')
    //     ->wherePod('SOUTH')
    //     ->orderBy('factory','DESC')->distinct()->get();

    //     return $BOL;

    // return Excel::download(new PODExport, 'users.xlsx');

    // if(Session::has('employee_number')){
    //     return Session::get('employee_number');

    // }
});

route::get('/test1', function () {
    $five_days_before = date('Y-m-d', strtotime('2018-12-02' . ' -5 days'));

    return $countBeyond = Container::select('container_number')
        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
        ->where('actual_discharge', $five_days_before)
        ->where(function ($query) {
            $query->whereNull('pull_out');
            $query->orWhere('pull_out', '>', '2018-12-02');
        })
        ->where('factory', $factory['factory_id'])
        ->count();
});

route::get('/test', function () {
    if ((date('N', strtotime('2017-01-01')) >= 6) == 1) {
        // Create a new DateTime object
        $date = new DateTime('2017-01-01');

        // Modify the date it contains
        $date->modify('next monday');

        // Output
        $newdate = $date->format('Y-m-d');

        while (Holiday::where('holiday_date', '=', $newdate)->count() == 1) {
            $date = new DateTime((string) $newdate);
            $date->modify('+1 day');
            $newdate = $date->format('Y-m-d');
            if ((date('N', strtotime((string) $newdate)) >= 6) == 1) {
                // Modify the date it contains
                $date->modify('next monday');
                // Output
                $newdate = $date->format('Y-m-d');
            }

        }
        echo $date->format('Y-m-d');
        //echo date("Y-m-d", strtotime("next monday"));
    } else {
        $date = new DateTime('2017-01-02');

        // Output
        $newdate = $date->format('Y-m-d');

        while (Holiday::where('holiday_date', '=', $newdate)->count() == 1) {
            $date = new DateTime((string) $newdate);
            $date->modify('+1 day');
            $newdate = $date->format('Y-m-d');
            if ((date('N', strtotime((string) $newdate)) >= 6) == 1) {
                // Modify the date it contains
                $date->modify('next monday');
                // Output
                $newdate = $date->format('Y-m-d');
            }

        }

        //echo Holiday::where('holiday_date','=', (string) $newdate )->count();
        echo $newdate;
    }
    //echo date("Y-m-d", strtotime("next monday"));
});
Route::get('/', function () {
    // return view('welcome');
    return Redirect::to('/index');

});

//--> DOWNLOAD CSV
Route::get('/download/{filename?}', 'homeController@download');

//Route::get('/index','homeController@index');
Route::get('/index', 'reportController@charts')->name('importation.charts');

//--> Login Index
Route::get('/login', 'login@login')->name('login.index');
//--> Do Login
Route::get('/loginprocess', 'login@dologin')->name('login.do');
//--> Do Logout
Route::get('/loginlogout', 'login@logout')->name('login.logout');
//--> Change Password Page
Route::post('/change_password', 'login@change_password')->name('login.change_password');

//--> User Maintenance
Route::get('/maintenance_user', 'homecontroller@maintenance_user')->name('maintenance.user');
//--> Holiday Maintenance
Route::get('/maintenance_holiday', 'homecontroller@maintenance_holiday')->name('maintenance.holiday');
//--> UploadedCSV Maintenance
Route::get('/maintenance_uploadedcsv/{dir?}', 'homecontroller@maintenance_uploadedcsv')->name('maintenance.uploadedCSV');
//--> DOWNLOAD CSV
Route::get('/maintenance_uploadedcsv_download/{directory?}/{filename?}', 'homeController@downloadUploadedCSV')->name('maintenance.maintenance_uploadedcsv_download')->middleware('checkUserSession');
//--> DELETE Directories
Route::get('/maintenance_deleteUploadedCSVDirectory/{dir?}/{all?}', 'homecontroller@deleteUploadedCSVDirectory')->name('maintenance.deleteUploadedCSVDirectory')->middleware('checkUserSession');
/*
 * IMPORTATION CONTROLLER
 */
Route::get('/importation/{factory?}', 'importationController@index')->name('importation.index');

Route::get('/importation_new', 'importationController@new_record')->name('importation.new')->middleware('checkUserSession', 'RoleEncoding');

Route::get('/daily_boc', 'importationController@daily_boc')->name('importation.daily_boc');

Route::get('/arrival_update', 'importationController@arrival_update')->name('importation.arrival_update')->middleware('RoleArrivalUpdate');

Route::get('/documentation_process', 'importationController@documentation_process')->name('importation.documentation_process')->middleware('RoleArrivalUpdate');

Route::get('/shipments_on_process', 'importationController@shipments_on_process')->name('importation.sop')->middleware('RoleShipmentProcess');

Route::get('/storage_demurrage', 'importationController@storage_demurrage')->name('importation.storage_validity')->middleware('RoleStorageMovementSafe');
Route::get('/unload_returned', 'importationController@unload_returned')->name('importation.unload_returned')->middleware('RoleStorageMovementSafe');
Route::get('/cleared_shipment', 'importationController@cleared_shipment')->name('importation.cleared_shipment')->middleware('RoleStorageMovementSafe');

Route::get('/charts', 'reportController@charts')->name('importation.charts');
Route::get('/breakdown', 'reportController@breakdown')->name('importation.breakdown');
Route::get('/beyond_storage_free_time_per_day', 'reportController@beyond_storage_free_time_per_day')->name('importation.beyond_storage_free_time_per_day');
Route::get('/beyond_storage_free_time_summary/{year?}', 'reportController@beyond_storage_free_time_summary')->name('importation.beyond_storage_free_time_summary');
Route::get('/summary_tally', 'reportController@summary_tally')->name('importation.summary_tally');
Route::get('/excel_import', 'reportController@excel_import')->name('importation.excel_import')->middleware('checkUserSession');
Route::post('/doexcel_import', 'reportController@doexcel_import')->name('importation.do_excel_import');
Route::post('/doexcel_import_docs_team', 'reportController@doexcel_import_docs_team')->name('importation.do_excel_import_docs_team');
Route::post('/doexcel_import_update_tsad', 'reportController@doexcel_import_update_tsad')->name('importation.do_excel_import_update_tsad');
Route::post('/doexcel_import_update_gatepass', 'reportController@doexcel_import_update_gatepass')->name('importation.do_excel_import_update_gatepass');
Route::post('/doexcel_import_update_current_status', 'reportController@doexcel_import_update_current_status')->name('importation.do_excel_import_update_current_status');
Route::post('/doexcel_import_update_return', 'reportController@doexcel_import_update_return')->name('importation.do_excel_import_update_return');
Route::post('/doexcel_import_update_validity', 'reportController@doexcel_import_update_validity')->name('importation.do_excel_import_update_validity');
Route::post('/doexcel_import_update_booking_time', 'reportController@doexcel_import_update_booking_time')->name('importation.do_excel_import_update_booking_time');
Route::post('/doexcel_import_update_delivery_pullout', 'reportController@doexcel_import_update_delivery_pullout')->name('importation.do_excel_import_update_delivery_pullout');
Route::post('/doexcel_import_update_actual_unload', 'reportController@doexcel_import_update_actual_unload')->name('importation.do_excel_import_update_actual_unload');

Route::post('/doexcel_import_update_discharge_date', 'reportController@doexcel_import_update_discharge_date')->name('importation.doexcel_import_update_discharge_date');

Route::get('/incoming_vessels','reportController@incoming_vessels')->name('importation.incoming_vessels');
Route::get('/onboard_shipment','reportController@onboard_shipment')->name('importation.onboard_shipment');
Route::get('/vessel_waiting','reportController@vessel_waiting')->name('importation.vessel_waiting');
Route::get('/notyet_discharge','reportController@notyet_discharge')->name('importation.notyet_discharge');
Route::get('/shipment_on_process_report','reportController@shipment_on_process_report')->name('importation.shipment_on_process_report');
Route::get('/shipment_without_gatepass','reportController@shipment_without_gatepass')->name('importation.shipment_without_gatepass');
Route::get('/onhand_gatepass','reportController@onhand_gatepass')->name('importation.onhand_gatepass');
Route::get('/containers_irs','reportController@containers_irs')->name('importation.containers_irs');
Route::get('/dismounted_with_chassi','reportController@dismounted_with_chassi')->name('importation.dismounted_with_chassi');
Route::get('/containers_not_return','reportController@containers_not_return')->name('importation.containers_not_return');
Route::get('/import_analysis/{year?}/{month?}','reportController@import_analysis')->name('importation.import_analysis');
Route::post('/import_analysis_reports/discharge_gatepass','reportController@import_analysis_discharge_gatepass');
Route::get('/import_analysis_reports/logistics_report','reportController@logistics_report')->name('importation.logistics_report');

Route::get('/unload_analysis/{year?}/{month?}','reportController@unload_analysis')->name('importation.unload_analysis');

Route::get('/transport_schedule','reportController@transport_schedule')->name('importation.transport_schedule');

Route::post('/bill_of_lading/api_transport_schedule','reportController@api_transport_schedule');

Route::get('/transport_schedule_export/{date_filter?}','reportController@transport_schedule_export')->name('importation.transport_schedule_export');

Route::get('/transport_schedule_tally_export',function(){


    \Excel::load(asset('files/TALLYTEMPLATE.xls'), function($reader) {

        $reader->sheet('ReportsMaster',function($sheet) {
            $sheet->appendRow([
                'test1', 'test2',
            ]);
        });
        })->export('xls');

//    return \Excel::load(asset('files/TALLYTEMPLATE.xls'), function($reader)
//     {
//         $reader->sheet(function($sheet)
//         {
//             $sheet->appendRow([
//                  'test1', 'test2',
//              ]);
//         });
//     })->export('xls');


});

Route::get('/transport_schedule_tally','reportController@transport_schedule_tally')->name('importation.discharge_gatepass_tally');
Route::post('/bill_of_lading/api_transport_schedule_tally','reportController@api_transport_schedule_tally')->name('importation.api_discharge_gatepass_tally');


Route::get('/transport_schedule_tally_export_download','reportController@transport_schedule_tally_export')->name('importation.transport_schedule_tally_export_download');





Route::get('/wrong_discharge',function(){
	ini_set('max_execution_time', 1000);
    ini_set('memory_limit', '500M');


    // $bl_nos = bill_of_lading::select('bl_no')->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
    //  ->whereColumn('bill_of_ladings.target_gatepass','containers.actual_discharge')
    //  ->get();

	// foreach($bl_nos as $row){
	// 	//NEWLY ADDED
	// 		$bl_no = $row['bl_no'];

	// 		$discharge_latest =  Container::select('actual_discharge')->skip(0)->take(1)->where('bl_no_fk', $bl_no)->orderBy('actual_discharge','DESC')->get();
	// 		$discharge_latest =  $discharge_latest[0]->actual_discharge;

	// 		$berthing_latest = Bill_of_lading::select('actual_berthing_date')->where('bl_no', $bl_no)->get();
	// 		$berthing_latest = $berthing_latest[0]->actual_berthing_date;


	// 		if($berthing_latest != null){

	// 			$berthed=strtotime($berthing_latest);
	// 			$discharge=strtotime($discharge_latest);

	// 			if($berthed < $discharge)
	// 			{
	// 				$value = $discharge_latest;
	// 			}else{
	// 				$value = $berthing_latest;
	// 			}

    //         }




	// 		// Create a new DateTime object
	// 		$date = new DateTime($value);
	// 		$date->modify('+1 day');
	// 		// Output
	// 		$value = $date->format('Y-m-d');
	// 		//BIANCA SUNDAY ONLY
	// 		//if ((date('N', strtotime($value)) >= 6) == 1) {

	// 		if ((date('N', strtotime($value)) >= 7) == 1) {
	// 			// Create a new DateTime object
	// 			$date = new DateTime($value);

	// 			// Modify the date it contains
	// 			$date->modify('next monday');

	// 			// Output
	// 			$newdate = $date->format('Y-m-d');

	// 			while (Holiday::where('holiday_date', '=', $newdate)->count() == 1) {
	// 				$date = new DateTime((string) $newdate);
	// 				$date->modify('+1 day');
	// 				$newdate = $date->format('Y-m-d');
	// 				//BIANCA SUNDAY ONLY
	// 				//if ((date('N', strtotime((string) $newdate)) >= 6) == 1) {

	// 				if ((date('N', strtotime((string) $newdate)) >= 7) == 1) {
	// 					// Modify the date it contains
	// 					$date->modify('next monday');
	// 					// Output
	// 					$newdate = $date->format('Y-m-d');
	// 				}

	// 			}

	// 			//echo date("Y-m-d", strtotime("next monday"));
	// 		} else {
	// 			$date = new DateTime($value);

	// 			// Output
	// 			$newdate = $date->format('Y-m-d');

	// 			while (Holiday::where('holiday_date', '=', $newdate)->count() == 1) {
	// 				$date = new DateTime((string) $newdate);
	// 				$date->modify('+1 day');
	// 				$newdate = $date->format('Y-m-d');
	// 				//BIANCA SUNDAY ONLY
	// 				//if ((date('N', strtotime((string) $newdate)) >= 6) == 1) {

	// 				if ((date('N', strtotime((string) $newdate)) >= 7) == 1) {
	// 					// Modify the date it contains
	// 					$date->modify('next monday');
	// 					// Output
	// 					$newdate = $date->format('Y-m-d');
	// 				}

	// 			}

	// 			//echo Holiday::where('holiday_date','=', (string) $newdate )->count();

	// 		}

	// 		Bill_of_Lading::where('bl_no', $bl_no)

	// 		->update([
	// 			'target_gatepass' => $newdate,
    //         ]);

    //         echo $bl_no . ' - ' . $discharge_latest . ' - ' . $berthing_latest . ' = ' . $newdate;
    //         echo '<br>';
	// }


    	//NEWLY ADDED
			$bl_no = 'test';


			$discharge_latest = null;

			$berthing_latest = '2020-01-04';


			if($berthing_latest != null){

				$berthed=strtotime($berthing_latest);
				$discharge=strtotime($discharge_latest);

				if($berthed < $discharge)
				{
					$value = $discharge_latest;
				}else{
					$value = $berthing_latest;
				}

            }




			// Create a new DateTime object
			$date = new DateTime($value);
			$date->modify('+1 day');
			// Output
			$value = $date->format('Y-m-d');
			//BIANCA SUNDAY ONLY
			//if ((date('N', strtotime($value)) >= 6) == 1) {

			if ((date('N', strtotime($value)) >= 6) == 1) {
				// Create a new DateTime object
				$date = new DateTime($value);

				// Modify the date it contains
				$date->modify('next monday');

				// Output
				$newdate = $date->format('Y-m-d');

				while (Holiday::where('holiday_date', '=', $newdate)->count() == 1) {
					$date = new DateTime((string) $newdate);
					$date->modify('+1 day');
					$newdate = $date->format('Y-m-d');
					//BIANCA SUNDAY ONLY
					//if ((date('N', strtotime((string) $newdate)) >= 6) == 1) {

					if ((date('N', strtotime((string) $newdate)) >= 6) == 1) {
						// Modify the date it contains
						$date->modify('next monday');
						// Output
						$newdate = $date->format('Y-m-d');
					}

				}

				//echo date("Y-m-d", strtotime("next monday"));
			} else {
				$date = new DateTime($value);

				// Output
				$newdate = $date->format('Y-m-d');

				while (Holiday::where('holiday_date', '=', $newdate)->count() == 1) {
					$date = new DateTime((string) $newdate);
					$date->modify('+1 day');
					$newdate = $date->format('Y-m-d');
					//BIANCA SUNDAY ONLY
					//if ((date('N', strtotime((string) $newdate)) >= 6) == 1) {

					if ((date('N', strtotime((string) $newdate)) >= 6) == 1) {
						// Modify the date it contains
						$date->modify('next monday');
						// Output
						$newdate = $date->format('Y-m-d');
					}

				}

				//echo Holiday::where('holiday_date','=', (string) $newdate )->count();

			}

			Bill_of_Lading::where('bl_no', $bl_no)

			->update([
				'target_gatepass' => $newdate,
            ]);

            echo $bl_no . ' - ' . $discharge_latest . ' - ' . $berthing_latest . ' = ' . $newdate;
            echo '<br>';




});



Route::get('/xray_containers_check',function(){
	ini_set('max_execution_time', 1000);
    ini_set('memory_limit', '500M');


    $bl_nos = bill_of_lading::select('bl_no')->where('assessment_tag','RED')->pluck('bl_no');
    return $bl_nos;
    // foreach( $bl_nos as $bl_no){

    //     Container::whereBlNoFk($bl_no)->update( [ 'xray' => 1 ] );

    // }
});

Route::get('/imsjobqueries',function(){
    return ImsJob::all();
});



Route::get('debug',function(){

    $id = 39869;
    $column = 'actual_discharge';
    $value = '2020-09-01';

    $split = new Split($id,$column, $value);
    return dd($split);

    //SITU9031988
});


Route::post('/bill_of_lading/save_container_columnweb', function (Request $request) {

    $id = $request->input('id');
    $columnName = $request->input('columnName');
    $value = $request->input('value');

    if (strlen(trim($value)) == 0) {

        if($columnName == 'actual_gatepass' ){
            $updateData = [
                (string) $columnName => null,
                'gatepass_datetime_update' => \date('Y-m-d H:i:s'),
                'gatepass_update_by' =>  Session::get('employee_number') . " - Clear",
            ];
            Container::where('id', $id)
                        ->update( $updateData);
        }else{

            Container::where('id', $id)

            ->update([
                (string) $columnName => null,
            ]);

        }

    } else {

        if($columnName == 'actual_gatepass' ){
            $updateData = [
                (string) $columnName => strtoupper($value),
                'gatepass_datetime_update' => \date('Y-m-d H:i:s'),
                'gatepass_update_by' =>  Session::get('employee_number'),
            ];
            Container::where('id', $id)
                        ->update( $updateData);
        }else{

            Container::where('id', $id)

            ->update([
                (string) $columnName => strtoupper($value),
            ]);
        }



        $split = new Split($id,$columnName, $value);
        $split->achieved_gatepass('ACHIEVED GATEPASS');

    }



});


Route::post('/bill_of_lading/save_arrival_blurweb', function (Request $request) {

    $id = $request->input('id');
    $columnName = $request->input('columnName');
    $value = $request->input('value');

    if ((string) $columnName == 'sop_current_status' || (string) $columnName == 'actual_gatepass') {


        $bl_no = Bill_of_Lading::select('bl_no')->find($id)['bl_no'];

        $list_id = Container::select('id')->whereNull('actual_gatepass')->where('bl_no_fk', $bl_no)->get();


        if (count($list_id) > 0) {
            foreach ($list_id as $id) {

                $ids[] = $id['id'];

                $split = new Split( $id['id'] ,$columnName, $value);
                $split->achieved_gatepass('ACHIEVED GATEPASS');

            }

            if (strlen(trim($value)) == 0) {


                    if($columnName == 'actual_gatepass' ){
                        $updateData = [
                            (string) $columnName => null,
                            'gatepass_datetime_update' => \date('Y-m-d H:i:s'),
                            'gatepass_update_by' =>  Session::get('employee_number') . " - Clear",
                        ];
                        Container::whereIn('id', $ids)
                                    ->update( $updateData);
                    }else{

                        Container::whereIn('id', $ids)

                        ->update([
                            (string) $columnName => null,
                        ]);

                    }


            } else {


                if($columnName == 'actual_gatepass' ){
                    $updateData = [
                        (string) $columnName => strtoupper($value),
                        'gatepass_datetime_update' => \date('Y-m-d H:i:s'),
                        'gatepass_update_by' =>  Session::get('employee_number'),
                    ];


                    Container::whereIn('id', $ids)
                                ->update( $updateData);
                }else{

                    Container::whereIn('id', $ids)

                    ->update([
                        (string) $columnName => strtoupper($value),
                    ]);
                }

            }

        }

    }







});


Route::post('/bill_of_lading/reason_delay_gatepass_containerweb', function (Request $request) {

    $id = $request->input('id');
    $gatepass = $request->input('gatepass');
    $reason = $request->input('reason');
    $current_status = $request->input('current_status');

    $Update_BOC = [
        'sop_current_status' => $current_status,
        'actual_gatepass' => $gatepass,
        'reason_of_delay_gatepass' => strtoupper($reason),
        'gatepass_datetime_update' => \date('Y-m-d H:i:s'),
        'gatepass_update_by' =>  Session::get('employee_number'),

    ];

    Container::where('id', $id)
        ->update($Update_BOC);

    $split = new Split($id,'actual_gatepass', $gatepass);
    $split->achieved_gatepass($current_status,strtoupper($reason));

});


Route::post('/bill_of_lading/reason_delay_gatepass_web', function (Request $request) {

    $BL_NO = $request->input('id');
    $gatepass = $request->input('gatepass');
    $reason = $request->input('reason');
    $current_status = $request->input('current_status');

    $Update_BOC = [
        'sop_current_status' => $current_status,
        'actual_gatepass' => $gatepass,
        'reason_of_delay_gatepass' => strtoupper($reason),
		'gatepass_datetime_update' => \date('Y-m-d H:i:s'),
        'gatepass_update_by' =>  Session::get('employee_number')
		
		];

    // Bill_of_Lading::where('bl_no',$BL_NO)
    //         ->update($Update_BOC);

    $list_id = Container::select('id')->whereNull('actual_gatepass')->where('bl_no_fk', $BL_NO)->get();
    if (count($list_id) > 0) {
        foreach ($list_id as $id) {

            $ids[] = $id['id'];
        }

        Container::whereIn('id', $ids)
            ->update([
                'sop_current_status' => $current_status,
                'actual_gatepass' => $gatepass,
                'reason_of_delay_gatepass' => strtoupper($reason),
				'gatepass_datetime_update' => \date('Y-m-d H:i:s'),
				'gatepass_update_by' =>  Session::get('employee_number')
            ]);
		foreach ($list_id as $id) {

             
			$split = new Split($id['id'],'actual_gatepass', $gatepass);
			$split->achieved_gatepass($current_status,strtoupper($reason));
        }	
			
		
    }

});






Route::get('/extract_all_new/{factory?}/{start?}/{end?}', function ($factory = 'all', $start = null, $end = null) {


    ini_set('max_execution_time', 1000);
    ini_set('memory_limit', '1000M');

    $validfactory = factory::select('factory_id')->get()->pluck('factory_id')->toArray();
    $validfactory[] = 'ALL';

    $factory = strtoupper($factory);
    if($end != null){
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$end)) {
            return response("Not valid start date",400);
        }
    }else{
        $end =  bill_of_lading::select('estimated_time_arrival')->orderBy('estimated_time_arrival','DESC')->first()->estimated_time_arrival;
    }

    if($start != null){
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$start)) {
            return response("Not valid end date",400);
        }

    }else{
        $start = bill_of_lading::select('processing_date')->orderBy('id')->first()->processing_date;
    }


    if( in_array($factory,$validfactory) ){

		
        return Excel::download(new AllRecordExportNew($factory,$start,$end) , 'allrecord.xlsx');

    }else
    {
        return response("Not valid factory",400);
    }



});