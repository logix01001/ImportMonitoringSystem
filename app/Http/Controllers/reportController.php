<?php

namespace App\Http\Controllers;

use DB;
use Agent;
use Session;
use Storage;
use DateTime;
use Response;
use JavaScript;
use App\Factory;
use App\Holiday;
use App\Container;
use App\Bill_of_Lading;
use App\Container_Type;
use App\Libraries\Adddate;
use App\Uploading_CSV_Log;
use App\libraries\DateRange;
use Illuminate\Http\Request;
use App\Bill_of_Lading_Commodity;
use App\Exports\UploadReturnExport;
use Maatwebsite\Excel\Facades\Excel;
use App\libraries\ContainerDischarge;
use App\Exports\TransportScheduleExport;
use Illuminate\Support\Facades\Redirect;
use App\Exports\ImportExcelReturnRowsExport;
use App\Classfile\Split;

class reportController extends Controller
{
    //

    public function __construct()
    {

        $this->middleware('updateUserPrivilege');
        ini_set('max_execution_time', 300);

    }
    private function check_date_error($date){
        if($date != null && $date == '1970-01-01' ){
           return true;
        }else{
            return false;
        }
    }
    private function date_error_message($field){
        return $field . " 1970-01-01";
    }
    private function checkIfValid($date){

        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {

            return true;

        }else if(preg_match("/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)){

            return true;

        }else if(preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/",$date)){

            return true;

        }else {

            return false;
        }


    }



    public function charts()
    {
        if (Agent::browser() == 'IE') {

            return Redirect::to(route('login.index'));

            //$IE_Detected = 'Please use a newer Browser like firefox or Chrome for better usage of the IMS.';
        }
        $date_request = date('Y-m-d');
        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $cType = Container_Type::select('name')->whereNull('deleted_at')->get();
        $factory_array = [];
        $i = 0;
        $factory_drilldown_array = [];
        $factory_drilldown_array_pod = [];
        $factory_tally = [];
        $factory_count = [];
        $factory_total = [];
        $POD = ['NORTH','SOUTH'];
        foreach ($factories as $factory) {

            $count = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('factory', $factory['factory_id'])
                ->where('actual_discharge', '<=', $date_request)
                ->whereNull('pull_out')
                //->whereNull('dismounted_cy')
               // ->where(function ($query) use ($date_request) {
                   // $query->whereNull('pull_out');
                   // $query->orWhere('pull_out', '>', $date_request);
                //})
            //->whereNull('pull_out')
                ->count();

            if ($count > 0) {
                $factory_array[$i]['name'] = $factory['factory_id'];
                $factory_array[$i]['drilldown'] = $factory['factory_id'];
                $factory_array[$i]['y'] = $count;

                $factory_tally[] = $factory['factory_id'];

                $x = 0;
                $index_total = 0;
                $factory_drilldown_array[$i]['name'] = $factory['factory_id'];
                $factory_drilldown_array[$i]['id'] = $factory['factory_id'];
                $factory_drilldown_array[$i]['data'] = [];



                $factory_drilldown_array_pod[$i]['name'] = $factory['factory_id'];
                $factory_drilldown_array_pod[$i]['id'] = $factory['factory_id'];
                $factory_drilldown_array_pod[$i]['data'] = [];

                foreach ($cType as $type) {

                    $count = Container::select('container_number')
                        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                        ->where('quantity', '=', 1)
                        ->where('factory', $factory['factory_id'])
                        ->where('container_type', $type['name'])
                        ->where('actual_discharge', '<=', $date_request)
                        ->whereNull('pull_out')
                        //->whereNull('dismounted_cy')
                        //->where(function ($query) use ($date_request) {
                         //   $query->whereNull('pull_out');
                         //   $query->orWhere('pull_out', '>', $date_request);
                        //})
                        ->count('container_number');
                    if ($count > 0) {
                        $factory_count[$factory['factory_id']][] = $count;
                        $factory_drilldown_array[$i]['data'][$x] = [$type['name'], $count];
                        $p = 0;
                        foreach ($POD as $key=>$val) {
                            $countPOD = Container::select('container_number')
                                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                                    ->where('quantity', '=', 1)
                                    ->where('factory', $factory['factory_id'])
                                    ->where('pod', $val)
                                    ->where('actual_discharge', '<=', $date_request)
                                    ->whereNull('pull_out')
                                    //->whereNull('dismounted_cy')
                                    //->where(function ($query) use ($date_request) {
                                     //   $query->whereNull('pull_out');
                                     //   $query->orWhere('pull_out', '>', $date_request);
                                    //})
                                    ->count();

                                    $factory_drilldown_array_pod[$i]['data'][$p] = [$val, $countPOD];

                                    $p++;

                        }

                        $x++;

                    } else {
                        $factory_count[$factory['factory_id']][] = 0;
                    }

                    if (!array_key_exists($index_total, $factory_total)) {
                        $factory_total[$index_total] = 0;
                    }
                    $factory_total[$index_total] += $count;
                    $index_total++;

                }

                $i++;

            }

        }


        JavaScript::put([

            'factory_array' => $factory_array,
            'factory_drilldown_array' => $factory_drilldown_array,
            'factory_count' => $factory_count,
            'cType' => $cType,
            'factory_total' => $factory_total,
            'factory_drilldown_array_pod'=>$factory_drilldown_array_pod,
            'page' => 'home'

        ]);

        return view('pages.charts');
    }

    public function summary_tally()
    {

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2018-11-30';
        $i = 0;
        $summary = [];
        foreach ($factories as $factory) {
            $summary[$i]['name'] = $factory['factory_id'];

            $countNorth = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('actual_discharge', '<=', $date_request)
                ->where('factory', $factory['factory_id'])
                ->whereNull('pull_out')
                // ->where(function ($query) {
                //     $query->whereNull('pull_out');
                //     $query->orWhereNull('unload');
                // })
                        //->whereNull('dismounted_cy')
                        // ->where(function ($query) use ($date_request) {
                        //     $query->whereNull('pull_out');
                        //     $query->orWhere('pull_out', '>', $date_request);
                        // })

                ->where('pod', 'NORTH')
                ->count();

            $summary[$i]['north'] = $countNorth;

            $countSouth = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('actual_discharge', '<=', $date_request)
                ->where('factory', $factory['factory_id'])
                ->whereNull('pull_out')
                //->whereNull('dismounted_cy')
                // ->where(function ($query) use ($date_request) {
                //     $query->whereNull('pull_out');
                //     $query->orWhere('pull_out', '>', $date_request);
                // })
                ->where('pod', 'SOUTH')
                ->count();

            $summary[$i]['south'] = $countSouth;
            $summary[$i]['at_port'] = $countSouth + $countNorth;

            $countIRS = Container::select('container_number')
                        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNotIn('pod',['TBA'])
                        ->where('dismounted_date','<=',$date_request)
                        ->whereFactory($factory['factory_id'])
                        ->where('dismounted_cy','IRS BACAO')
                        ->whereNotNull('pull_out')
                        ->whereNull('unload')
                        ->count();

            $summary[$i]['irs'] = $countIRS;


            $countCez1 = Container::select('container_number')
                        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNotIn('pod',['TBA'])
                        ->where('dismounted_date','<=',$date_request)
                        ->whereFactory($factory['factory_id'])
                        ->where('dismounted_cy','CEZ1')
                        ->whereNotNull('pull_out')
                        ->whereNull('unload')
                        ->count();

            $summary[$i]['cez1'] = $countCez1;


            $countCez2 = Container::select('container_number')
                        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNotIn('pod',['TBA'])
                        ->where('dismounted_date','<=',$date_request)
                        ->whereFactory($factory['factory_id'])
                        ->where('dismounted_cy','CEZ2')
                        ->whereNotNull('pull_out')
                        ->whereNull('unload')
                        ->count();

            $summary[$i]['cez2'] = $countCez2;


            // $countChassi = Container::select('container_number')
            //         ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            //         ->where('quantity', '=', 1)
            //         ->whereNull('unload')
            //         ->where('factory', $factory['factory_id'])
            //     //->where('dismounted_cy','IRS BACAO')
            //         ->whereNotIn('dismounted_cy', ['IRS BACAO'])
            //         ->where('dismounted_date',$date_request)
            //         ->count();

            // $summary[$i]['at_chassi'] = $countChassi;





            //mawawala lang pag na unload na
            $countFactory = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->whereNull('dismounted_cy')
                ->where('pull_out', $date_request) //change by mam shaira  from unload
                ->where('factory', $factory['factory_id'])
                ->count();

            $summary[$i]['delivery_factory'] = $countFactory;

            $count_irs = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->whereNotNull('pull_out')
                ->where('dismounted_date', $date_request)
                ->where('factory', $factory['factory_id'])
                ->where('dismounted_cy', 'IRS BACAO')
                ->count();

            $summary[$i]['delivery_irs'] = $count_irs;

            $count_cez1 = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->whereNotNull('pull_out')
                ->where('dismounted_date', $date_request)
                ->where('factory', $factory['factory_id'])
                ->where('dismounted_cy', 'CEZ1')
                ->count();

            $summary[$i]['delivery_cez1'] = $count_cez1;

            $count_cez2 = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->whereNotNull('pull_out')
                ->where('dismounted_date', $date_request)
                ->where('factory', $factory['factory_id'])
                ->where('dismounted_cy', 'CEZ2')
                ->count();

            $summary[$i]['delivery_cez2'] = $count_cez2;

            // $count_chassi = Container::select('container_number')
            //     ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            //     ->where('quantity', '=', 1)
            //     ->where('dismounted_date', '=', $date_request)
            // //->whereNotIn('dismounted_cy',['CEZ 1 PUTOL', 'CEZ 2 PUTOL','IRS BACAO'])
            //     ->whereNotIn('dismounted_cy', ['IRS BACAO'])
            //     ->where('factory', $factory['factory_id'])
            //     ->count();
            // $summary[$i]['with_chassi'] = $count_chassi;

            $count = bill_of_lading::whereActualBerthingDate($date_request)
                ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
                ->where('quantity', '=', 1)
                ->where('factory', $factory['factory_id'])
                ->count();
            $summary[$i]['berthed'] = $count;

            $count = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('containers.actual_discharge', '=', $date_request)
                ->where('factory', $factory['factory_id'])
                ->count();

            $summary[$i]['discharge'] = $count;

            $count = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('containers.actual_gatepass', '=', $date_request)
                ->where('factory', $factory['factory_id'])
                ->count();

            $summary[$i]['gatepass'] = $count;

            $count = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->whereNull('dismounted_date')
                ->where('unload', $date_request)
                ->where('factory', $factory['factory_id'])
                ->count();

            $summary[$i]['direct_unloading'] = $count;

            $count = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->whereNotNull('dismounted_date')
                ->whereNotIn('dismounted_cy', ['IRS BACAO'])
                ->where('unload', $date_request)
                ->where('factory', $factory['factory_id'])
                ->count();

            $summary[$i]['unloading_with_chassis'] = $count;

            $count = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->whereNotNull('dismounted_date')
                ->where('dismounted_cy', 'IRS BACAO')
                ->where('unload', $date_request)
                ->where('factory', $factory['factory_id'])
                ->count();

            $summary[$i]['unloading_irs'] = $count;

            $five_days_before = date('Y-m-d', strtotime($date_request . ' -6 days'));

            $countBeyond = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('actual_discharge', '<=', $five_days_before)
                ->where(function ($query) use ($date_request) {
                    $query->whereNull('pull_out');
                    $query->orWhere('pull_out', '>', $date_request);
                })
                ->where('factory', $factory['factory_id'])
                ->count();

            $summary[$i]['beyond_5_days'] = $countBeyond;

            $i++;
        }

        JavaScript::put([
            'summary' => $summary,
            'date_request' => $date_request,
        ]);

        return view('pages.summary_tally');
    }

    public function beyond_storage_free_time_summary($year = null)
    {
        if ($year == null) {
            $year = date('Y');
        }

        $distinct_year = Container::SELECT(DB::raw('YEAR(`actual_discharge`) as Year'))->distinct()->get();

        //$year = 2018;
        //$month = 1;

        $summary_tally = [];

        /*
        {
        "name": "Chrome",
        "y": 62.74,
        "drilldown": "Chrome"
        },
        {
        "name": "Firefox",
        "y": 10.57,
        "drilldown": "Firefox"
        },
        {
        "name": "Internet Explorer",
        "y": 7.23,
        "drilldown": "Internet Explorer"
        },
        {
        "name": "Safari",
        "y": 5.58,
        "drilldown": "Safari"
        },
        {
        "name": "Edge",
        "y": 4.02,
        "drilldown": "Edge"
        },
        {
        "name": "Opera",
        "y": 1.92,
        "drilldown": "Opera"
        },
        {
        "name": "Other",
        "y": 7.62,
        "drilldown": null
        }

        [
        {
        "name": "Chrome",
        "id": "Chrome",
        "data": []
        }

        {
        "name": "Chrome",
        "id": "Chrome",
        "data":  [
        [
        "v65.0",
        0.1
        ],
        [
        "v64.0",
        1.3
        ],
        [
        "v63.0",
        53.02
        ],
        [
        "v62.0",
        1.4
        ],
        [
        "v61.0",
        0.88
        ],
        [
        "v60.0",
        0.56
        ],
        [
        "v59.0",
        0.45
        ],
        [
        "v58.0",
        0.49
        ],
        [
        "v57.0",
        0.32
        ],
        [
        "v56.0",
        0.29
        ],
        [
        "v55.0",
        0.79
        ],
        [
        "v54.0",
        0.18
        ],
        [
        "v51.0",
        0.13
        ],
        [
        "v49.0",
        2.16
        ],
        [
        "v48.0",
        0.13
        ],
        [
        "v47.0",
        0.11
        ],
        [
        "v43.0",
        0.17
        ],
        [
        "v29.0",
        0.26
        ]
        ]
        }
         */

        // $discharge_distinct = Container::distinct()->select('actual_discharge')
        //     ->whereYear('actual_discharge', $year)
        //     ->whereNotNull('actual_discharge')
        //     ->orderBy('actual_discharge', 'ASC')
        //     ->get();
        $discharge_distinct = Container::distinct()->select('actual_discharge')
            ->whereYear('actual_discharge', $year);

            if($year == date('Y')){
                $discharge_distinct =  $discharge_distinct->whereMonth('actual_discharge','<=', date('m'));
            }

        $discharge_distinct = $discharge_distinct->whereNotNull('actual_discharge')
            ->orderBy('actual_discharge', 'ASC')
            ->get();
        $selectedMonth = [];



        foreach ($discharge_distinct as $row) {

            if (!in_array(date('F', strtotime($row['actual_discharge'])), $selectedMonth)) {
                $selectedMonth[] = date('F', strtotime($row['actual_discharge']));
            }

        }

        if (!in_array(date('F', strtotime(date('Y-m-d'))), $selectedMonth)) {
            $selectedMonth[] = date('F', strtotime(date('Y-m-d')));
        }



        $charts = [];
        $series = [];

        for ($i = 0; $i < count($selectedMonth); $i++) {
            $charts[date('n', strtotime($selectedMonth[$i]))]['name'] = $selectedMonth[$i];
            $charts[date('n', strtotime($selectedMonth[$i]))]['y'] = 0;
            $charts[date('n', strtotime($selectedMonth[$i]))]['drilldown'] = $selectedMonth[$i];
            $series[date('n', strtotime($selectedMonth[$i]))]["id"] = $selectedMonth[$i];

        }



        $series_index = 0;

        for ($i = 1; $i <= 31; $i++) {
            for ($m = 1; $m <= 12; $m++) {
                if (date('Y') == $year) {
                    if ($m > date('n', strtotime(date('Y-m-d')))) {

                        continue;

                    }
                }

                if (date('Y') == $year) {
                    if ($m == date('n', strtotime(date('Y-m-d'))) && $i > date('j', strtotime(date('Y-m-d') . '-1 day'))) {
                        continue;
                    }
                }
                $n = (strlen($m) > 1) ? $m : '0' . $m;
                $i = (strlen($i) > 1) ? $i : '0' . $i;

                $number = cal_days_in_month(CAL_GREGORIAN, $m, $year); // 31
                $current_month = date('M', strtotime($year . '-' . $n . '-' . $i));
                $date_request = $year . '-' . $n . '-' . $i;
                $six_days_before = date('Y-m-d', strtotime($date_request . ' -6 days'));

                if ($i <= $number) {
                    $count = Container::select('container_number')
                        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                        ->where('actual_discharge', '<=', $six_days_before)
                        ->whereQuantity(1)
                        ->where(function ($query) use ($date_request) {
                            $query->whereNull('pull_out');
                            $query->orWhere('pull_out', '>', $date_request);
                        })
                        ->count();

                    $summary_tally[$current_month][] = $count;

                    if (array_key_exists((string) $m, $charts)) {

                        $charts[(string) $m]['y'] += $count;

                        // $series[] = array([
                        //     (string)$i
                        //     $count
                        //     ]

                        // );

                    }
                    if (array_key_exists((string) $m, $series)) {
                        $series[(string) $m]["name"] = $series[(string) $m]["id"];
                        $series[(string) $m]["data"][] = array(
                            (string) $i,
                            $count,
                        );
                    }

                    $series_index++;
                } else {
                    continue;
                }
            }

        }

        $data_charts = [];
        $series_charts = [];

        foreach ($charts as $key => $value) {
            if ($value['y'] == 0) {
                continue;
            }
            $data_charts[] = $value;
        }
        //return $series;
        foreach ($series as $key => $value) {
            if ($value["data"][1] == 0) {
                continue;
            }
            $series_charts[] = $value;
        }

        //return distinct year recorded

        JavaScript::put([
            'summary' => $summary_tally,
            'data_charts' => $data_charts,
            'series_charts' => $series_charts,
            'year' => $year,
            'distinct_year' => $distinct_year,
        ]);

        return view('pages.beyond_free_time_summary');

    }

    public function beyond_storage_free_time_per_day()
    {

        $date_request = date('Y-m-d', strtotime('-1 day'));
        $six_days_before = date('Y-m-d', strtotime($date_request . ' -6 days'));
        $eleven_days_before = date('Y-m-d', strtotime($date_request . ' -10 days'));
        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $reports = [];

        $i = 0;
        foreach ($factories as $factory) {
            $reports[$i]['name'] = $factory['factory_id'];
            $reports[$i]['container_count'] = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->whereQuantity(1)
                ->where('actual_discharge', '<=', $six_days_before)
                ->where(function ($query) use ($date_request) {
                    $query->whereNull('pull_out');
                    $query->orWhere('pull_out', '>', $date_request);
                })
                ->where('factory', $factory['factory_id'])
                ->count();
            $reports[$i]['bl_count'] = Bill_of_Lading::select('bl_no')
                ->join('containers', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->whereQuantity(1)
                ->where('containers.actual_discharge', '<=', $six_days_before)
                ->where(function ($query) use ($date_request) {
                    $query->whereNull('pull_out');
                    $query->orWhere('pull_out', '>', $date_request);
                })
                ->where('factory', $factory['factory_id'])
                ->distinct()
                ->count('bl_no');

            $reports[$i]['reason_of_delay'] = Container::select('reason_of_delay_delivery')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->whereQuantity(1)
                ->where('actual_discharge', '<=', $six_days_before)
                ->where(function ($query) use ($date_request) {
                    $query->whereNull('pull_out');
                    $query->orWhere('pull_out', '>', $date_request);
                })
                ->distinct()
                ->where('factory', $factory['factory_id'])
                ->get();

            $reports[$i]['six_days'] = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->whereQuantity(1)
                ->where('actual_discharge', '<=', $six_days_before)
                ->where('actual_discharge', '>', $eleven_days_before)
                ->where(function ($query) use ($date_request) {
                    $query->whereNull('pull_out');
                    $query->orWhere('pull_out', '>', $date_request);
                })
                ->where('factory', $factory['factory_id'])
                ->count();

            $reports[$i]['eleven_days'] = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->whereQuantity(1)
                ->where('actual_discharge', '<=', $eleven_days_before)

                ->where(function ($query) use ($date_request) {
                    $query->whereNull('pull_out');
                    $query->orWhere('pull_out', '>', $date_request);
                })
                ->where('factory', $factory['factory_id'])
                ->count();

            $i++;

        }

        JavaScript::put([
            'reports' => $reports,
            'date_request' => $date_request,
        ]);

        return view('pages.beyond_free_time_per_day');

    }

    private function port_charges_beyond_freetime($days)
    {
        if ($days >= 6 && $days <= 10) {
            $price = 1443.90;
        }
        if ($days > 10) {
            $price = 100000;
        }

        return $days * $price;

    }

    public function breakdown()
    {

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $containers_tally = [];
        $date_request = date('Y-m-d', strtotime('-1 days'));
        $i = 0;


        foreach ($factories as $factory) {

            $count = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('pull_out', '=', $date_request)
                ->where('factory', $factory['factory_id'])
                ->count();

            $containers_tally[$i]['name'] = $factory['factory_id'];
            //$containers_tally[$i]['count']['pullout'] = $count;
            $containers_tally[$i]['pullout'] = $count;

            $count = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('actual_discharge', '=', $date_request)
                ->where('factory', $factory['factory_id'])
                ->count();

            $containers_tally[$i]['discharged'] = $count;
            // $containers_tally[$i]['count']['discharged'] = $count;

            $count = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('unload', '=', $date_request)
                ->where('factory', $factory['factory_id'])
                ->count();

            $containers_tally[$i]['unloaded'] = $count;

            $count = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('containers.actual_gatepass', '=', $date_request)
                ->where('factory', $factory['factory_id'])
                ->count();

            $containers_tally[$i]['gatepass'] = $count;
            //$containers_tally[$i]['count']['unloaded'] = $count;

            $count_irs = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('dismounted_date', '=', $date_request)
                ->where('dismounted_cy', '=', 'IRS BACAO')
                ->where('factory', $factory['factory_id'])
                ->count();

            // $count_cez = Container::select('container_number')
            // ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
            // ->where('quantity','=',1)
            // ->where('dismounted_date','=',$date_request)
            // ->whereIn('dismounted_cy',['CEZ 1 PUTOL', 'CEZ 2 PUTOL'])
            // ->where('factory',$factory['factory_id'])
            // ->count();

            $count_cy = Container::select('container_number')
                ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->where('quantity', '=', 1)
                ->where('dismounted_date', '=', $date_request)
            //->whereNotIn('dismounted_cy',['CEZ 1 PUTOL', 'CEZ 2 PUTOL','IRS BACAO'])
                ->whereNotIn('dismounted_cy', ['IRS BACAO'])
                ->where('factory', $factory['factory_id'])
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
            'date_request' => $date_request,
        ]);
        //return $unloaded;
        return view('pages.summary_tally_breakdown');
    }

    public function excel_import()
    {

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

    private function storeUploadedCVS($filename = '',$content='',$type =''){

        $dateToday = date('his');
        $fileDate = date('Y-m-d');
        $uploaded_by = Session::get('employee_number');
        Storage::disk('local')->put($fileDate .'/'.$dateToday.'-'.$uploaded_by.'-'.$type.'-'.$filename,$content);

    }

    public function doexcel_import(Request $request)
    {

        if ($request->hasFile('csvMaterial')) {

            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $this->storeUploadedCVS($filename,$csvData,'ImportRecord');
            $rows = array_map('str_getcsv', explode("\n", $csvData));

            $list_exist_bl = Bill_of_lading::select('bl_no')->get()->toArray();
            if (count($list_exist_bl) > 0) {
                foreach ($list_exist_bl as $list) {
                    $exist_bl[] = $list['bl_no'];
                }
            } else {
                $exist_bl = [];
            }

            $exist_bl;

            $returnRows = [];
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
            // 11/16/18    Actual time arrival
            // 11/16/18    Actual Bething time
            // 11/17/18    Actual Discharge
            // 11/19/18 Target Gate pass
            // IRS BACAO DISMOUNTED CY
            // 09/20/18    DISMOUNTED DATE
            // 09/20/18    PULL OUT
            // 09/21/18    UNLOAD
            // ATI    RETURN CY
            // 09/22/18    RETURN DATE
            // J.ZAPATA    RETURN TRUCKER
            // 88     RETURN BOX NUMBER
            // 1742 RETURN SUMMARY NUMBER
            // MSDS PROBLEM NEED CERT FROM DOE      REASON OF DELAY DELIVERY

            $success = 0;
            $csv_code = new \DateTime();
            $csv_code = date('Y') . '-' . strtotime($csv_code->format('Y-m-d H:i:s'));

            for ($line = 0; $line < (count($rows) - 1); $line++) {

                if (count($rows[$line]) < 37) {

                    for ($i = count($rows[$line]); $i < 37; $i++) {
                        $rows[$line][] = "";
                    }

                }

                if (strlen(trim($rows[$line][1])) == 0) {
                    continue;
                }

                if (in_array($rows[$line][1], $exist_bl)) {

                    $returnRows[] = [$rows[$line][1],'Exist BL Number'];
                    continue;

                }

				if(strlen(trim($rows[$line][16])) > 11){
					$returnRows[] = [ $rows[$line][16], 'Container Number is exceed of 11 characters'];
                    continue;
				}

                if (strlen(trim($rows[$line][26])) == 0) {

                    $target_gatepass = null;

                } else {
                    if (trim($rows[$line][26]) == 'Err:504') {
                        $target_gatepass = $this->getTargetGatepass(date('Y-m-d', strtotime($rows[$line][24])));
                    } else {
                        $target_gatepass = date('Y-m-d', strtotime($rows[$line][26]));
                    }
                }

                $IN_DATA = [];
                foreach (explode(',', $rows[$line][2]) as $key => $value) {
                    $IN_DATA[] = array('bl_no_fk' => $rows[$line][1],
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
                $COMMODITY_DATA = [];
                foreach (explode(',', $rows[$line][4]) as $key => $value) {
                    $COMMODITY_DATA[] = array('bl_no_fk' => $rows[$line][1],
                        'commodity' => $value);

                    // Container::insert([
                    //     'bl_no_fk' => $rows[$line][1],
                    //     'invoice_number' => $value
                    // ]);
                }
                // print_r([
                //     'factory' => (strlen(trim($rows[$line][0])) > 0) ? $rows[$line][0] : null,
                //     'bl_no' => (strlen(trim($rows[$line][1])) > 0) ? $rows[$line][1] : null,
                //     'supplier' => (strlen(trim($rows[$line][3])) > 0) ? $rows[$line][3] : null,
                //     'vessel' => (strlen(trim($rows[$line][5])) > 0) ? $rows[$line][5] : null,
                //     'connecting_vessel' => (strlen(trim($rows[$line][6])) > 0) ? $rows[$line][6] : null,
                //     'shipping_line' => (strlen(trim($rows[$line][7])) > 0) ? $rows[$line][7] : null,
                //     'forwarder' => (strlen(trim($rows[$line][8])) > 0) ? $rows[$line][8] : null,
                //     'broker' => (strlen(trim($rows[$line][9])) > 0) ? $rows[$line][9] : null,
                //     'pol' => (strlen(trim($rows[$line][10])) > 0) ? $rows[$line][10] : null,
                //     'country' => (strlen(trim($rows[$line][11])) > 0) ? $rows[$line][11] : null,
                //     'pod' => (strlen(trim($rows[$line][12])) > 0) ? $rows[$line][12] : null,
                //     'volume' => (strlen(trim($rows[$line][13])) > 0) ? $rows[$line][13] : null,
                //     'shipping_docs' => ((strlen(trim($rows[$line][17])) > 0) ? date('Y-m-d', strtotime($rows[$line][17])) : null) . ' ' . $rows[$line][18],
                //     'processing_date' => (strlen(trim($rows[$line][19])) > 0) ? date('Y-m-d', strtotime($rows[$line][19])) : null,
                //     'estimated_time_departure' => (strlen(trim($rows[$line][20])) > 0) ? date('Y-m-d', strtotime($rows[$line][20])) : null,
                //     'estimated_time_arrival' => (strlen(trim($rows[$line][21])) > 0) ? date('Y-m-d', strtotime($rows[$line][21])) : null,
                //     'incoterm' => (strlen(trim($rows[$line][22])) > 0) ? $rows[$line][22] : 'test',
                //     'actual_time_arrival' => (strlen(trim($rows[$line][23])) > 0) ? date('Y-m-d', strtotime($rows[$line][23])) : null,
                //     'actual_berthing_date' => (strlen(trim($rows[$line][24])) > 0) ? date('Y-m-d', strtotime($rows[$line][24])) : null,
                //     'target_gatepass' => $target_gatepass,
                //     'created_at' => new \DateTime(),
                //     'updated_at' => new \DateTime(),
                //     'csv_batch_code' => strtotime($csv_code)
                // ]);
                // echo '<hr>';
                // continue;

                //return $rows[$line];
                if (!isset($prev_bl)) {

                    $prev_bl = $rows[$line][1];

                    try {
                        DB::beginTransaction();
                        DB::table('bill_of_ladings')
                            ->insert([
                                'factory' => (strlen(trim($rows[$line][0])) > 0) ? $rows[$line][0] : null,
                                'bl_no' => (strlen(trim($rows[$line][1])) > 0) ? trim($rows[$line][1]) : null,
                                'supplier' => (strlen(trim($rows[$line][3])) > 0) ? $rows[$line][3] : null,
                                'vessel' => (strlen(trim($rows[$line][5])) > 0) ? $rows[$line][5] : null,
                                'connecting_vessel' => (strlen(trim($rows[$line][6])) > 0) ? $rows[$line][6] : null,
                                'shipping_line' => (strlen(trim($rows[$line][7])) > 0) ? $rows[$line][7] : null,
                                'forwarder' => (strlen(trim($rows[$line][8])) > 0) ? $rows[$line][8] : null,
                                'broker' => (strlen(trim($rows[$line][9])) > 0) ? $rows[$line][9] : null,
                                'pol' => (strlen(trim($rows[$line][10])) > 0) ? $rows[$line][10] : null,
                                'country' => (strlen(trim($rows[$line][11])) > 0) ? $rows[$line][11] : null,
                                'pod' => (strlen(trim($rows[$line][12])) > 0) ? $rows[$line][12] : null,
                                'volume' => (strlen(trim($rows[$line][13])) > 0) ? $rows[$line][13] : null,
                                'shipping_docs' => ((strlen(trim($rows[$line][17])) > 0) ? date('Y-m-d', strtotime($rows[$line][17])) : null) . ' ' . $rows[$line][18],
                                'processing_date' => (strlen(trim($rows[$line][19])) > 0) ? date('Y-m-d', strtotime($rows[$line][19])) : null,
                                'estimated_time_departure' => (strlen(trim($rows[$line][20])) > 0) ? date('Y-m-d', strtotime($rows[$line][20])) : null,
                                'estimated_time_arrival' => (strlen(trim($rows[$line][21])) > 0) ? date('Y-m-d', strtotime($rows[$line][21])) : null,
                                'incoterm' => (strlen(trim($rows[$line][22])) > 0) ? $rows[$line][22] : null,
                                'actual_time_arrival' => (strlen(trim($rows[$line][23])) > 0) ? date('Y-m-d', strtotime($rows[$line][23])) : null,
                                'actual_berthing_date' => (strlen(trim($rows[$line][24])) > 0) ? date('Y-m-d', strtotime($rows[$line][24])) : null,
                                'target_gatepass' => $target_gatepass,
                                'created_at' => new \DateTime(),
                                'updated_at' => new \DateTime(),
                                'csv_batch_code' => $csv_code
                            ]);
                        DB::connection('endorsmentdarwin')->table('endorsements')->insert([
                                'BlNo' => (strlen(trim($rows[$line][1])) > 0) ? $rows[$line][1] : null,
                                'status' => 0,
                            ]
                        );
                        DB::table('bill_of_lading_invoices')->insert($IN_DATA);
                        DB::table('bill_of_lading_commodities')->insert($COMMODITY_DATA);
                        DB::table('containers')->insert([
                            'bl_no_fk' => (strlen(trim($rows[$line][1])) > 0) ? trim($rows[$line][1]) : null,
                            'quantity' => (strlen(trim($rows[$line][14])) > 0) ? $rows[$line][14] : 1,
                            'container_type' => (strlen(trim($rows[$line][15])) > 0) ? $rows[$line][15] : null,
                            'container_number' => (strlen(trim($rows[$line][16])) > 0) ? trim($rows[$line][16]) : null,
                            'actual_discharge' => (strlen(trim($rows[$line][25])) > 0) ? date('Y-m-d', strtotime($rows[$line][25])) : null,
                            'dismounted_cy' => (strlen(trim($rows[$line][27])) > 0) ? $rows[$line][27] : null,
                            'dismounted_date' => (strlen(trim($rows[$line][28])) > 0) ? date('Y-m-d', strtotime($rows[$line][28])) : null,
                            'pull_out' => (strlen(trim($rows[$line][29])) > 0) ? date('Y-m-d', strtotime($rows[$line][29])) : null,
                            'unload' => (strlen(trim($rows[$line][30])) > 0) ? date('Y-m-d', strtotime($rows[$line][30])) : null,
                            'return_cy' => (strlen(trim($rows[$line][31])) > 0) ? $rows[$line][31] : null,
                            'return_date' => (strlen(trim($rows[$line][32])) > 0) ? date('Y-m-d', strtotime($rows[$line][32])) : null,
                            'trucker' => (strlen(trim($rows[$line][33])) > 0) ? $rows[$line][33] : null,
                            'return_box_number' => (strlen(trim($rows[$line][34])) > 0) ? $rows[$line][34] : 0,
                            'return_summary_number' => (strlen(trim($rows[$line][35])) > 0) ? $rows[$line][35] : 0,
                            'reason_of_delay_gatepass' => (strlen(trim($rows[$line][36])) > 0) ? $rows[$line][36] : null,
                        ]);
                        DB::commit();
                        // all good
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                        // something went wrong
                    }

                } else {


                    try {
                        DB::beginTransaction();
                        if ($prev_bl != $rows[$line][1]) {
                            $prev_bl = $rows[$line][1];

                            DB::table('bill_of_ladings')->insert([
                                'factory' => (strlen(trim($rows[$line][0])) > 0) ? $rows[$line][0] : null,
                                'bl_no' => (strlen(trim($rows[$line][1])) > 0) ? $rows[$line][1] : null,
                                'supplier' => (strlen(trim($rows[$line][3])) > 0) ? $rows[$line][3] : null,
                                'vessel' => (strlen(trim($rows[$line][5])) > 0) ? $rows[$line][5] : null,
                                'connecting_vessel' => (strlen(trim($rows[$line][6])) > 0) ? $rows[$line][6] : null,
                                'shipping_line' => (strlen(trim($rows[$line][7])) > 0) ? $rows[$line][7] : null,
                                'forwarder' => (strlen(trim($rows[$line][8])) > 0) ? $rows[$line][8] : null,
                                'broker' => (strlen(trim($rows[$line][9])) > 0) ? $rows[$line][9] : null,
                                'pol' => (strlen(trim($rows[$line][10])) > 0) ? $rows[$line][10] : null,
                                'country' => (strlen(trim($rows[$line][11])) > 0) ? $rows[$line][11] : null,
                                'pod' => (strlen(trim($rows[$line][12])) > 0) ? $rows[$line][12] : null,
                                'volume' => (strlen(trim($rows[$line][13])) > 0) ? $rows[$line][13] : null,
                                'shipping_docs' => ((strlen(trim($rows[$line][17])) > 0) ? date('Y-m-d', strtotime($rows[$line][17])) : null) . ' ' . $rows[$line][18],
                                'processing_date' => (strlen(trim($rows[$line][19])) > 0) ? date('Y-m-d', strtotime($rows[$line][19])) : null,
                                'estimated_time_departure' => (strlen(trim($rows[$line][20])) > 0) ? date('Y-m-d', strtotime($rows[$line][20])) : null,
                                'estimated_time_arrival' => (strlen(trim($rows[$line][21])) > 0) ? date('Y-m-d', strtotime($rows[$line][21])) : null,
                                'incoterm' => (strlen(trim($rows[$line][22])) > 0) ? $rows[$line][22] : null,
                                'actual_time_arrival' => (strlen(trim($rows[$line][23])) > 0) ? date('Y-m-d', strtotime($rows[$line][23])) : null,
                                'actual_berthing_date' => (strlen(trim($rows[$line][24])) > 0) ? date('Y-m-d', strtotime($rows[$line][24])) : null,
                                'target_gatepass' => $target_gatepass,
                                'created_at' => new \DateTime(),
                                'updated_at' => new \DateTime(),
                                'csv_batch_code' => $csv_code
                            ]);

                            DB::connection('endorsmentdarwin')->table('endorsements')->insert([
                                    'BlNo' => (strlen(trim($rows[$line][1])) > 0) ? $rows[$line][1] : null,
                                    'status' => 0,
                                ]
                            );
                            DB::table('bill_of_lading_invoices')->insert($IN_DATA);
                            DB::table('bill_of_lading_commodities')->insert($COMMODITY_DATA);
                        }

                        DB::table('containers')->insert([
                            'bl_no_fk' => (strlen(trim($rows[$line][1])) > 0) ? $rows[$line][1] : null,
                            'quantity' => (strlen(trim($rows[$line][14])) > 0) ? $rows[$line][14] : 1,
                            'container_type' => (strlen(trim($rows[$line][15])) > 0) ? $rows[$line][15] : null,
                            'container_number' => (strlen(trim($rows[$line][16])) > 0) ? $rows[$line][16] : null,
                            'actual_discharge' => (strlen(trim($rows[$line][25])) > 0) ? date('Y-m-d', strtotime($rows[$line][25])) : null,
                            'dismounted_cy' => (strlen(trim($rows[$line][27])) > 0) ? $rows[$line][27] : null,
                            'dismounted_date' => (strlen(trim($rows[$line][28])) > 0) ? date('Y-m-d', strtotime($rows[$line][28])) : null,
                            'pull_out' => (strlen(trim($rows[$line][29])) > 0) ? date('Y-m-d', strtotime($rows[$line][29])) : null,
                            'unload' => (strlen(trim($rows[$line][30])) > 0) ? date('Y-m-d', strtotime($rows[$line][30])) : null,
                            'return_cy' => (strlen(trim($rows[$line][31])) > 0) ? $rows[$line][31] : null,
                            'return_date' => (strlen(trim($rows[$line][32])) > 0) ? date('Y-m-d', strtotime($rows[$line][32])) : null,
                            'trucker' => (strlen(trim($rows[$line][33])) > 0) ? $rows[$line][33] : null,
                            'return_box_number' => (strlen(trim($rows[$line][34])) > 0) ? $rows[$line][34] : 0,
                            'return_summary_number' => (strlen(trim($rows[$line][35])) > 0) ? $rows[$line][35] : 0,
                            'reason_of_delay_gatepass' => (strlen(trim($rows[$line][36])) > 0) ? $rows[$line][36] : null,
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
                'uploaded_by' => Session::get('employee_number'),
            ]);


            if(count($returnRows) > 0){
                $returnRows[] = ['Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds'];

                return $this->_DownloadReturn($returnRows);
                // return $returnRows;
             }


            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds');

        } else {
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }

    }

    public function doexcel_import_docs_team(Request $request)
    {
        if ($request->hasFile('csvMaterial')) {

            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $this->storeUploadedCVS($filename,$csvData,'docsTeam');
            $rows = array_map('str_getcsv', explode("\n", $csvData));



            /*
            OOLU4034505420
            11/01/18    dateendorse
            11/01/18     IP
            11/01/18    E2m
            11/01/18    actual process
            AS RED, FOR MANIFEST
            12456 TSAD NO
             */

            $returnRows = [];
            $success = 0;
            for ($line = 0; $line < (count($rows) - 1); $line++) {

                $ReturnString = [];
                if (strlen(trim($rows[$line][0])) == 0) {
                    continue;
                }

                if (count($rows[$line]) < 7) {

                    for ($i = count($rows[$line]); $i < 7; $i++) {
                        $rows[$line][] = "";
                    }

                }

                if (Bill_of_lading::whereBlNo(trim($rows[$line][0]))->count() == 0) {
                    $rows[$line][] = "NO RECORD FOUND";
                    $returnRows[] = $rows[$line];
                    continue;
                }


                if (strlen(trim($rows[$line][5])) > 0) {
                    $remarks_of_docs = strtoupper($rows[$line][5]);

                    if (strpos($remarks_of_docs, 'AS RED') !== false) {
                        $assessment_tag = 'RED';
                    } else {
                        $assessment_tag = 'YELLOW';
                    }

                } else {
                    $remarks_of_docs = null;
                    $assessment_tag = null;
                }





                /*
                echo $rows[$line][0];
                echo '<br>';
                echo (strlen(trim( $rows[$line][1] )) > 0) ? date('Y-m-d', strtotime($rows[$line][1])) : null;
                echo '<br>';
                echo (strlen(trim( $rows[$line][2] )) > 0) ? date('Y-m-d', strtotime($rows[$line][2])) : null;
                echo '<br>';
                echo (strlen(trim( $rows[$line][3] )) > 0) ? date('Y-m-d', strtotime($rows[$line][3])) : null;
                echo '<br>';
                echo (strlen(trim( $rows[$line][4] )) > 0) ? date('Y-m-d', strtotime($rows[$line][4])) : null;
                echo '<br>';
                echo  $remarks_of_docs;
                echo '<br>';
                echo $assessment_tag;
                echo '<br>';
                echo (strlen(trim( $rows[$line][6] )) > 0) ? $rows[$line][6] : null;
                echo '<hr>';
                 */
                $date_endorse = (strlen(trim($rows[$line][1])) > 0) ? date('Y-m-d', strtotime($rows[$line][1])) : null;
                $date_approve_ip = (strlen(trim($rows[$line][2])) > 0) ? date('Y-m-d', strtotime($rows[$line][2])) : null;
                $e2m = (strlen(trim($rows[$line][3])) > 0) ? date('Y-m-d', strtotime($rows[$line][3])) : null;
                $actual_process = (strlen(trim($rows[$line][4])) > 0) ? date('Y-m-d', strtotime($rows[$line][4])) : null;



                if($date_endorse != null && $this->check_date_error($date_endorse) ){
                    $ReturnString[] = $this->date_error_message("Date Endorse");
                }
                if($date_approve_ip != null && $this->check_date_error($date_approve_ip) ){

                    $ReturnString[] = $this->date_error_message("IP");
                }
                if($e2m != null && $this->check_date_error($e2m) ){

                    $ReturnString[] = $this->date_error_message("E2M");
                }
                if($actual_process != null && $this->check_date_error($actual_process) ){

                    $ReturnString[] = $this->date_error_message("Actual Process");
                }


                if(count($ReturnString) > 0){
                    $rows[$line][] = implode(',',$ReturnString);
                    $returnRows[] =  $rows[$line];
                    continue;
                }else{
                    $success++;
                }

                try {
                    DB::beginTransaction();
                    DB::table('bill_of_ladings')
                        ->whereBlNo($rows[$line][0])
                        ->update([

                            'date_endorse' =>   $date_endorse,
                            'date_approve_ip' => $date_approve_ip,
                            'e2m' =>  $e2m,
                            'actual_process' => $actual_process,
                            'remarks_of_docs' => $remarks_of_docs,
                            'assessment_tag' => $assessment_tag,
                            'tsad_no' => (strlen(trim($rows[$line][6])) > 0) ? $rows[$line][6] : null,

                        ]);

                    DB::commit();

                    // all good
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());

                    // something went wrong
                }

            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;
            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE RECORDS DOCS TEAM',
                'uploaded_by' => Session::get('employee_number'),
            ]);

            if(count($returnRows) > 0){
                $returnRows[] = ['Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds'];
                return $this->_DownloadReturn($returnRows);
                // return $returnRows;
             }
            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds');

        } else {
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }

    }

    private function update_target_gatepass($bl_no,$value){


        $discharge_latest =  Container::select('actual_discharge')->skip(0)->take(1)->where('bl_no_fk', $bl_no)->orderBy('actual_discharge','DESC')->get();
        $discharge_latest =  $discharge_latest[0]->actual_discharge;

        $berthing_latest = Bill_of_lading::select('actual_berthing_date')->where('bl_no', $bl_no)->get();
        $berthing_latest = $berthing_latest[0]->actual_berthing_date;


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
    }

    public function doexcel_import_update_discharge_date(Request $request){

        if ($request->hasFile('csvMaterial')) {

            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $this->storeUploadedCVS($filename,$csvData,'tsad');
            $rows = array_map('str_getcsv', explode("\n", $csvData));

            /*

            OOLU4034505420 BL_NO
            1234 TSAD NO

             */
            $returnRows = [];
            $success = 0;
            for ($line = 0; $line < (count($rows) - 1); $line++) {
                $ReturnString = [];
                if (strlen(trim($rows[$line][0])) == 0) {
                    continue;
                }



                if(Bill_of_lading::whereBlNo($rows[$line][0])->count() == 0){
                    $rows[$line][] = "NO RECORD FOUND";
                    $returnRows[] = $rows[$line];

                    continue;
                }

                if(Container::whereBlNoFk($rows[$line][0])->whereContainerNumber($rows[$line][1])->count() == 0){
                    $rows[$line][] = "NO CONTAINER RECORD FOUND";
                    $returnRows[] = $rows[$line];

                    continue;
                }


                $actual_arrival = (strlen(trim($rows[$line][2])) > 0) ? date('Y-m-d', strtotime($rows[$line][2])) : null;
                $berthing = (strlen(trim($rows[$line][3])) > 0) ? date('Y-m-d', strtotime($rows[$line][3])) : null;
                $discharge_date = (strlen(trim($rows[$line][4])) > 0) ? date('Y-m-d', strtotime($rows[$line][4])) : null;

                if($actual_arrival != null && $this->check_date_error($actual_arrival) ){
                    $ReturnString[] = $this->date_error_message("Actual Date");
                }

                if($berthing != null && $this->check_date_error($berthing) ){
                    $ReturnString[] = $this->date_error_message("Berthing Date");
                }

                if($discharge_date != null && $this->check_date_error($discharge_date) ){
                    $ReturnString[] = $this->date_error_message("Discharge Date");
                }


                if(count($ReturnString) > 0){
                    $rows[$line][] = implode(',',$ReturnString);
                    $returnRows[] =  $rows[$line];
                    continue;
                }else{
                    $success++;
                }

                $id = container::whereBlNoFk($rows[$line][0])
                                ->whereContainerNumber($rows[$line][1])
                                ->first()->id;

                try {
                    DB::beginTransaction();



                    DB::table('bill_of_ladings')
                        ->whereBlNo($rows[$line][0])
                        ->update([
                            'actual_time_arrival' => $actual_arrival,
                            'actual_berthing_date' => $berthing,
                        ]);

                    DB::table('containers')
                        ->whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->update([
                            'actual_discharge' => $discharge_date,
                        ]);

                    $split = new Split($id,'actual_discharge',$discharge_date);

                    DB::commit();
                    // all good
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());

                    // something went wrong
                }

                $this->update_target_gatepass($rows[$line][0],$discharge_date);
            }



            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;
            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE DISCHARGE DATE',
                'uploaded_by' => Session::get('employee_number'),
            ]);

            if(count($returnRows) > 0){
                $returnRows[] = ['Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds'];
                return $this->_DownloadReturn($returnRows);
                // return $returnRows;
             }
            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds');

        } else {
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }


    }

    public function doexcel_import_update_tsad(Request $request)
    {
        if ($request->hasFile('csvMaterial')) {

            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $this->storeUploadedCVS($filename,$csvData,'tsad');
            $rows = array_map('str_getcsv', explode("\n", $csvData));

            /*

            OOLU4034505420 BL_NO
            1234 TSAD NO

             */
            $returnRows = [];
            $success = 0;
            for ($line = 0; $line < (count($rows) - 1); $line++) {

                if (strlen(trim($rows[$line][0])) == 0) {
                    continue;
                }


                if(Bill_of_lading::whereBlNo($rows[$line][0])->count() == 0){

                    $returnRows[] = $rows[$line];

                    continue;
                }


                try {
                    DB::beginTransaction();
                    DB::table('bill_of_ladings')
                        ->whereBlNo($rows[$line][0])
                        ->update([
                            'tsad_no' => (strlen(trim($rows[$line][1])) > 0) ? $rows[$line][1] : null,
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
                'uploaded_by' => Session::get('employee_number'),
            ]);

            if(count($returnRows) > 0){
                $returnRows[] = ['Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds'];
                return $this->_DownloadReturn($returnRows);
                // return $returnRows;
             }
            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds');

        } else {
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }

    }


    public function doexcel_import_update_gatepass(Request $request)
    {
        if ($request->hasFile('csvMaterial')) {

            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $this->storeUploadedCVS($filename,$csvData,'gatepass');
            $rows = array_map('str_getcsv', explode("\n", $csvData));


            /*
            OOLU4034505420    BL_NO_FK
            OOLU9587787        CONTAINER #
            11/12/18            ACTUAL GATEPASS
            TEST 1            REASON OF DELAY GATEPASS
            TEST 1           SOP REMARKS
             */
            $returnRows = [];
            $success = 0;
            for ($line = 0; $line < (count($rows) - 1); $line++) {

                $ReturnString = [];

                if (strlen(trim($rows[$line][0])) == 0) {
                    continue;
                }

                if (count($rows[$line]) < 5) {

                    for ($i = count($rows[$line]); $i < 5; $i++) {
                        $rows[$line][] = "";
                    }

                }

                $BOLDetails = Bill_of_lading::whereBlNo(trim($rows[$line][0]))->get();
                $ContainerDetails = Container::whereBlNoFk(trim($rows[$line][0]))
                                            ->whereContainerNumber($rows[$line][1])->get();

                //CHECK IF EXIST RECORD
                if (Bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereBlNo(trim($rows[$line][0]))
                        ->whereContainerNumber($rows[$line][1])
                        ->count() == 0) {

                    $rows[$line][] = "NO RECORD FOUND";
                    $returnRows[] = $rows[$line];
                    continue;
                }


                //CHECK IF TARGET GATEPASS IS NOT NULL
                if($BOLDetails[0]->target_gatepass == null){
                    $ReturnString[] = "NO TARGET GATEPASS YET.";
                }

                //CHECK IF ACTUAL DISCHARGE IS GREATER THAN TARGETGATEPASS
                //Check length of row
                if(strlen(trim($rows[$line][2])) > 0){
                    //CHECK IF ACTUAL DISCHARGE IS GREATER THAN TARGETGATEPASS
                    if(date('Y-m-d', strtotime($rows[$line][2])) > $BOLDetails[0]->target_gatepass ){
                        //CHECK REMARKS INDICATED
                        if(strlen(trim($rows[$line][3])) == 0){
                            $ReturnString[] = "REASON OF DELAY IS REQUIRED. TARGET GATEPASS IS " . $BOLDetails[0]->target_gatepass;
                        }

                    }
                }



				//SMD REQUEST
				//SMD-SD-210303-151
				//NORTH WITH DISCHARGE



                // CHECKING IF SOUTH POD
                if (Bill_of_lading::whereBlNo(trim($rows[$line][0]))->wherePod('SOUTH')->count() > 0) {

                    if(Bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                    ->whereBlNo(trim($rows[$line][0]))
                                    ->whereContainerNumber($rows[$line][1])
                                    ->whereNotNull('actual_process')
                                    ->whereNotNull('actual_discharge')
                                    ->count() == 0){

                        $ReturnString[] =  "NO ACTUAL PROCESS or DISCHARGE YET.";
                       // $returnRows[] = $rows[$line];
                       // continue;
                    }

                } else {
                    if($BOLDetails[0]->actual_process == null){

                        $ReturnString[] = "NO ACTUAL PROCESS";
                        //$returnRows[] = $rows[$line];
                       // continue;
                    }

                }

                $actual_gatepass = (strlen(trim($rows[$line][2])) > 0) ? date('Y-m-d', strtotime($rows[$line][2])) : null;

                if($actual_gatepass != null && $this->check_date_error($actual_gatepass)){
                    $ReturnString[] = $this->date_error_message("Actual gatepass");
                }

                if(count($ReturnString) > 0){
                    $rows[$line][] = implode(',',$ReturnString);
                    $returnRows[] =  $rows[$line];
                    continue;
                }else{
                    $success++;
                }

                $id = container::whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->first()->id;
                try {
                    DB::beginTransaction();
                    DB::table('containers')
                        ->whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->update([
                            'actual_gatepass' => $actual_gatepass,
                            'gatepass_datetime_update' => \date('Y-m-d H:i:s'),
                            'gatepass_update_by' => ($actual_gatepass == "") ?  Session::get('employee_number') . " - Clear" : Session::get('employee_number'),
                            'reason_of_delay_gatepass' => (strlen(trim($rows[$line][3])) > 0) ? strtoupper($rows[$line][3]) : null,
                            'pull_out_remarks' => (strlen(trim($rows[$line][4])) > 0) ? strtoupper($rows[$line][4]) : null,
                            'sop_current_status' => 'ACHIEVED GATEPASS',

                        ]);
                    // DB::table('bill_of_ladings')
                    //     ->whereBlNo($rows[$line][0])
                    //     ->update([
                    //         'sop_remarks' => (strlen(trim($rows[$line][4])) > 0) ? $rows[$line][4] : null,
                    //     ]);

                    $split = new Split($id,'actual_gatepass',$actual_gatepass);
                    $split->achieved_gatepass('ACHIEVED GATEPASS',(strlen(trim($rows[$line][3])) > 0) ? strtoupper($rows[$line][3]) : null , (strlen(trim($rows[$line][4])) > 0) ? strtoupper($rows[$line][4]) : null);

                    DB::commit();
                    // all good
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                    // something went wrong
                }

            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;

            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE GATEPASS',
                'uploaded_by' => Session::get('employee_number')
            ]);

            if(count($returnRows) > 0){
                $returnRows[] = ['Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds'];
                return $this->_DownloadReturn($returnRows);
                // return $returnRows;
             }
            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds');

        } else {
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }

    }

    protected $fieldsupdate = [];
    private function _demurragefield($field,$value){

        if($value != null){
            $this->fieldsupdate[$field] = $value;
        }
    }

    public function doexcel_import_update_validity(Request $request)
    {
        if ($request->hasFile('csvMaterial')) {

            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $this->storeUploadedCVS($filename,$csvData,'validity');
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            $this->fieldsupdate = [];
            /*

                OOLU4034505420    BL NO
                OOLU9587787        CONTAINER #
                11/02/18        VAL STORAGE
                11/03/18        VAL DEMURRAGE
                11/03/18        REVAL STORAGE
                11/04/18        REVAL DEMURRAGE
                                Reason of REVALIDITY
                                Remarks

             */
            $returnRows = [];

            $success = 0;

            for ($line = 0; $line < (count($rows) - 1); $line++) {



				$this->fieldsupdate = [];
                if (strlen(trim($rows[$line][0])) == 0) {
                    continue;
                }

				if (count($rows[$line]) < 9) {

                    for ($i = count($rows[$line]); $i < 9; $i++) {
                        $rows[$line][] = "";
                    }

                }


                $rows[$line][0] = trim($rows[$line][0]);
                $rows[$line][1] = trim($rows[$line][1]);

                $ReturnString = [];

                if (strlen(trim($rows[$line][0])) == 0) {
                    continue;
                }

                if (Container::whereBlNoFk($rows[$line][0])->whereContainerNumber(trim($rows[$line][1]))->count() == 0) {

                    $ReturnString[] = "NO RECORD FOUND.";
                    $rows[$line][] = implode(',',$ReturnString);
                    $returnRows[] =  $rows[$line];
                    //$returnRows[] = $rows[$line];
                    continue;
                }

                $BOLDetails = Bill_of_lading::whereBlNo(trim($rows[$line][0]))->get();
                $ContainerDetails = Container::whereBlNoFk(trim($rows[$line][0]))
                                            ->whereContainerNumber($rows[$line][1])->get();




                // if($BOLDetails[0]->actual_process == null){
                //     $ReturnString[] = "NO ACTUAL PROCESS.";
                //    // $returnRows[] = $rows[$line];
                //    // continue;
                // }

                if($ContainerDetails[0]->actual_discharge == null){

                    $ReturnString[] = "NO DISCHARGE YET.";
                    //$returnRows[] = $rows[$line];
                    // continue;

                }


                //2020-08-27
                // if($ContainerDetails[0]->actual_gatepass == null){

                //     $ReturnString[] = "NO ACTUAL GATEPASS YET.";
                //     // $returnRows[] = $rows[$line];
                //     // continue;

                // }

                $validity_storage = (strlen(trim($rows[$line][2])) > 0) ? date('Y-m-d', strtotime($rows[$line][2])) : null;
                $validity_demurrage = (strlen(trim($rows[$line][3])) > 0) ? date('Y-m-d', strtotime($rows[$line][3])) : null;
                $detention_validity = (strlen(trim($rows[$line][4])) > 0) ? date('Y-m-d', strtotime($rows[$line][4])) : null;

                $revalidity_storage = (strlen(trim($rows[$line][5])) > 0) ? date('Y-m-d', strtotime($rows[$line][5])) : null;
                $revalidity_demurrage = (strlen(trim($rows[$line][6])) > 0) ? date('Y-m-d', strtotime($rows[$line][6])) : null;

                $revalidity_remarks = (strlen(trim($rows[$line][7])) > 0)  ? $rows[$line][7] : null;
                $pull_out_remarks = (strlen(trim($rows[$line][8])) > 0)  ? $rows[$line][8] : null;


                $this->_demurragefield('validity_storage',$validity_storage);
                $this->_demurragefield('validity_demurrage',$validity_demurrage);
                $this->_demurragefield('detention_validity',$detention_validity);
                $this->_demurragefield('revalidity_storage',$revalidity_storage);
                $this->_demurragefield('revalidity_demurrage',$revalidity_demurrage);
                $this->_demurragefield('revalidity_remarks',$revalidity_remarks);
                $this->_demurragefield('pull_out_remarks',$pull_out_remarks);



                // if($validity_storage != null && $this->check_date_error($validity_storage)){
                //     $ReturnString[] = $this->date_error_message("Validity Storage");
                // }

                // if($validity_demurrage != null && $this->check_date_error($validity_demurrage)){
                //     $ReturnString[] = $this->date_error_message("Validity Demurrage");
                // }

                // if($revalidity_storage != null && $this->check_date_error($revalidity_storage)){
                //     $ReturnString[] = $this->date_error_message("Revalidity Storage");
                // }

                // if($revalidity_demurrage != null && $this->check_date_error($revalidity_demurrage)){
                //     $ReturnString[] = $this->date_error_message("Revalidity Demurrage");
                // }


                if(count($ReturnString) > 0){
                    $rows[$line][] = implode(',',$ReturnString);
                    $returnRows[] =  $rows[$line];
                    continue;
                }else{
                    $success++;
                }

                $id = container::whereBlNoFk($rows[$line][0])
                                ->whereContainerNumber($rows[$line][1])
                                ->first()->id;

                // DB::beginTransaction();
				if(!empty($this->fieldsupdate)){

					try {
                    DB::beginTransaction();
                    DB::table('containers')
                        ->whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->update($this->fieldsupdate);

                        foreach($this->fieldsupdate as $field=>$value){
                            new Split( $id,$field,$value );
                        }

                        // new Split( $id,'validity_demurrage',$validity_demurrage );
                        // new Split( $id,'revalidity_storage',$revalidity_storage);
                        // new Split( $id,'revalidity_demurrage',$revalidity_demurrage);
                        // new Split( $id,'revalidity_remarks', (strlen(trim($rows[$line][6])) > 0) ? $rows[$line][6] : null );
                        // new Split( $id,'pull_out_remarks', (strlen(trim($rows[$line][7])) > 0) ? $rows[$line][7] : null );

						DB::commit();
						// all good
					} catch (\Exception $e) {
						DB::rollback();
						return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
						// something went wrong
					}
				}


                //$success++;
            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;


            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE VALIDITY',
                'uploaded_by' => Session::get('employee_number'),
            ]);
            //$request->session()->flash('message', 'Total imported Data : ' .  $success);

            if(count($returnRows) > 0){
                $returnRows[] = ['Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds'];
                return $this->_DownloadReturn($returnRows);
                // return $returnRows;
            }

            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds' );



        } else {

            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');

        }

    }


    public function doexcel_import_update_booking_time(Request $request)
    {
        if ($request->hasFile('csvMaterial')) {

            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $rows = array_map('str_getcsv', explode("\n", $csvData));

            /*

            OOLU4034505420    BL NO
            OOLU9587787        CONTAINER #
            ACAI            TRUCKER
            1:00 午後       BOOKING TIME

             */

            $returnRows = [];

            $success = 0;
            for ($line = 0; $line < (count($rows) - 1); $line++) {

                if (strlen(trim($rows[$line][0])) == 0) {
                    continue;
                }

                if (Container::whereBlNoFk($rows[$line][0])->whereContainerNumber(trim($rows[$line][1]))->count() > 0) {

                    $success++;
                } else {
                    $returnRows[] = $rows[$line];
                    continue;
                }




                try {
                    DB::beginTransaction();
                    DB::table('containers')
                        ->whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->update([
                            'trucker' => (strlen(trim($rows[$line][2])) > 0) ? $rows[$line][2] : null,
                            'booking_time' => (strlen(trim($rows[$line][3])) > 0) ? $rows[$line][3] : null,
                        ]);

                    DB::commit();
                    // all good
                } catch (\Exception $e) {
                    DB::rollback();
                    // return $e->getMessage();
                    return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                    // something went wrong
                }


            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;

            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE BOOKING TIME',
                'uploaded_by' => Session::get('employee_number'),
            ]);

            if(count($returnRows) > 0){
                $returnRows[] = ['Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds'];
                return $this->_DownloadReturn($returnRows);
                // return $returnRows;
             }

            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds');

        } else {
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }

    }

    public function doexcel_import_update_delivery_pullout(Request $request)
    {
        if ($request->hasFile('csvMaterial')) {

            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $this->storeUploadedCVS($filename,$csvData,'delivery');
            $rows = array_map('str_getcsv', explode("\n", $csvData));

            /*

            OOLU4034505420    BL NO
            OOLU9587787       CONTAINER #
            ACAI            PULLOUT
            10:45            PULLOUTTIME
                            REASON OF DELAY

             */


            $returnRows = [];

            $success = 0;
            for ($line = 0; $line < (count($rows) - 1); $line++) {

                if (count($rows[$line]) < 5) {

                    for ($i = count($rows[$line]); $i < 5; $i++) {
                        $rows[$line][] = "";
                    }

                }

                $rows[$line][0] = trim($rows[$line][0]);
                $rows[$line][1] = trim($rows[$line][1]);

                $ReturnString = [];

                if (strlen(trim($rows[$line][0])) == 0) {
                    continue;
                }

                if (Container::whereBlNoFk($rows[$line][0])->whereContainerNumber(trim($rows[$line][1]))->count() == 0) {

                    $ReturnString[] = "NO RECORD FOUND.";
                    $rows[$line][] = implode(',',$ReturnString);
                    $returnRows[] =  $rows[$line];
                    //$returnRows[] = $rows[$line];
                    continue;
                }

                $BOLDetails = Bill_of_lading::whereBlNo(trim($rows[$line][0]))->get();
                $ContainerDetails = Container::whereBlNoFk(trim($rows[$line][0]))
                                            ->whereContainerNumber($rows[$line][1])->get();



                if($BOLDetails[0]->actual_process == null){

                    $ReturnString[] = "NO ACTUAL PROCESS.";
                   // $returnRows[] = $rows[$line];
                   // continue;
                }

                if($ContainerDetails[0]->actual_discharge == null){

                    $ReturnString[] = "NO DISCHARGE YET.";
                    //$returnRows[] = $rows[$line];
                    // continue;

                }

                if($ContainerDetails[0]->actual_gatepass == null){

                    $ReturnString[] = "NO ACTUAL GATEPASS YET.";
                    // $returnRows[] = $rows[$line];
                    // continue;

                }


                //CHECK IF PULL OUT DATE IS GREATER THAN TARGETGATEPASS
                //Check length of row
                if(strlen(trim($rows[$line][2])) > 0){
                    //CHECK IF PULL OUT DATE  IS GREATER THAN TARGETGATEPASS
                    if(date('Y-m-d', strtotime($rows[$line][2])) >  $ContainerDetails[0]->actual_discharge ){
                        //CHECK REMARKS INDICATED
                        if(strlen(trim($rows[$line][4])) == 0){
                            $ReturnString[] = "REASON OF DELAY IS REQUIRED. DISCHARGE DATE IS " . $ContainerDetails[0]->actual_discharge;
                            //$returnRows[] = $rows[$line];
                           // continue;
                        }

                    }
                }


                $pull_out = (strlen(trim($rows[$line][2])) > 0) ? date('Y-m-d', strtotime($rows[$line][2])) : null;




                if($pull_out != null && $this->check_date_error($pull_out)){
                    $ReturnString[] = $this->date_error_message("Delivery");
                }
                //NEWLY ADDED SCRIPT TO CHECK PULLOUT IS GREATER THAN UNLOAd
                //2021-03-27

                if( $pull_out != null && $ContainerDetails[0]->unload != null){
                    $d1 = strtotime($pull_out);
                    $d2 = strtotime($ContainerDetails[0]->unload);

                    if($d1 > $d2)
                    {
                        $ReturnString[] = "Delivery date is greater than Unload date {$ContainerDetails[0]->unload}";
                    }

                }

                if(count($ReturnString) > 0){
                    $rows[$line][] = implode(',',$ReturnString);
                    $returnRows[] =  $rows[$line];
                    continue;
                }else{
                    $success++;
                }



                $id = container::whereBlNoFk($rows[$line][0])
                                ->whereContainerNumber($rows[$line][1])
                                ->first()->id;


                try {
                    DB::beginTransaction();
                    DB::table('containers')
                        ->whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->update([
                            'pull_out' => $pull_out,
                            'pull_out_time' => (strlen(trim($rows[$line][3])) > 0) ? $rows[$line][3] : null,
                            'reason_of_delay_delivery' => (strlen(trim($rows[$line][4])) > 0) ? strtoupper($rows[$line][4]) : null

                        ]);
                        $split = new Split( $id,'pull_out',$pull_out );
                        $split = new Split( $id,'pull_out_time',(strlen(trim($rows[$line][3])) > 0) ? $rows[$line][3] : null );
                        $split = new Split( $id,'reason_of_delay_delivery',(strlen(trim($rows[$line][4])) > 0) ? strtoupper($rows[$line][4]) : null );

                    DB::commit();
                    // all good
                } catch (\Exception $e) {
                    DB::rollback();
                    // return $e->getMessage();
                    return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                    // something went wrong
                }


            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;

            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE DELIVERY PULL OUT',
                'uploaded_by' => Session::get('employee_number'),
            ]);

            if(count($returnRows) > 0){
                $returnRows[] = ['Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds'];
                return $this->_DownloadReturn($returnRows);
                // return $returnRows;
            }
            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds');

        } else {
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }

    }



    public function doexcel_import_update_actual_unload(Request $request)
    {
        if ($request->hasFile('csvMaterial')) {

            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $this->storeUploadedCVS($filename,$csvData,'unload');
            $rows = array_map('str_getcsv', explode("\n", $csvData));

            /*

            OOLU4034505420    BL NO
            OOLU9587787       CONTAINER #
            ACAI            UNLOAD

             */

            $returnRows = [];

            $success = 0;
            for ($line = 0; $line < (count($rows) - 1); $line++) {
                if (count($rows[$line]) < 3) {

                    for ($i = count($rows[$line]); $i < 3; $i++) {
                        $rows[$line][] = "";
                    }

                }

                $rows[$line][0] = trim($rows[$line][0]);
                $rows[$line][1] = trim($rows[$line][1]);

                $ReturnString = [];

                if (strlen(trim($rows[$line][0])) == 0) {
                    continue;
                }

                if (Container::whereBlNoFk($rows[$line][0])->whereContainerNumber(trim($rows[$line][1]))->count() == 0) {

                    $ReturnString[] = "NO RECORD FOUND.";
                    $rows[$line][] = implode(',',$ReturnString);
                    $returnRows[] =  $rows[$line];
                    //$returnRows[] = $rows[$line];
                    continue;
                }

                $BOLDetails = Bill_of_lading::whereBlNo(trim($rows[$line][0]))->get();
                $ContainerDetails = Container::whereBlNoFk(trim($rows[$line][0]))
                                            ->whereContainerNumber($rows[$line][1])->get();



                if($BOLDetails[0]->actual_process == null){

                    $ReturnString[] = "NO ACTUAL PROCESS.";
                    // $returnRows[] = $rows[$line];
                    // continue;
                }

                if($ContainerDetails[0]->actual_discharge == null){

                    $ReturnString[] = "NO DISCHARGE YET.";
                    //$returnRows[] = $rows[$line];
                    // continue;

                }

                if($ContainerDetails[0]->actual_gatepass == null){

                    $ReturnString[] = "NO ACTUAL GATEPASS YET.";
                    // $returnRows[] = $rows[$line];
                    // continue;

                }

                if($ContainerDetails[0]->pull_out == null){

                    $ReturnString[] = "NO DELIVERY DATE YET.";
                    // $returnRows[] = $rows[$line];
                    // continue;

                }

                $unload = (strlen(trim($rows[$line][2])) > 0) ? date('Y-m-d', strtotime($rows[$line][2])) : null;


                // if( $unload != null && $ContainerDetails[0]->pull_out != null){
                //     $d1 = strtotime($unload);
                //     $d2 = strtotime($ContainerDetails[0]->pull_out);

                //     if($d1 < $d2)
                //     {
                //         $ReturnString[] = "Unload date is earlier than delivery date {$ContainerDetails[0]->pull_out}";
                //     }

                // }

                if( $unload != null && $ContainerDetails[0]->return_date != null){
                    $d1 = strtotime($unload);
                    $d2 = strtotime($ContainerDetails[0]->return_date);

                    if($d1 > $d2)
                    {
                        $ReturnString[] = "Unload date is greater than return date {$ContainerDetails[0]->return_date}";
                    }

                }

                if($unload != null && $this->check_date_error($unload)){
                    $ReturnString[] = $this->date_error_message("Unloaded");
                }

                if(count($ReturnString) > 0){
                    $rows[$line][] = implode(',',$ReturnString);
                    $returnRows[] =  $rows[$line];
                    continue;
                }else{
                    $success++;
                }


                $id = container::whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->first()->id;

                try {
                    DB::beginTransaction();
                    DB::table('containers')
                        ->whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->update([
                            'unload' =>  $unload

                        ]);


                    $split = new Split($id,'unload',$unload);

                    DB::commit();
                    // all good
                } catch (\Exception $e) {
                    DB::rollback();
                    // return $e->getMessage();
                    return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                    // something went wrong
                }


            }

			//return dd($returnRows);



            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;

            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE ACTUAL UNLOAD DATE',
                'uploaded_by' => Session::get('employee_number'),
            ]);

            if(count($returnRows) > 0){
                $returnRows[] = ['Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds'];
                return $this->_DownloadReturn($returnRows);
                // return $returnRows;
            }

            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds');

        } else {
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }

    }

    public function doexcel_import_update_current_status(Request $request)
    {
        if ($request->hasFile('csvMaterial')) {

            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $this->storeUploadedCVS($filename,$csvData,'currentstatus');
            $rows = array_map('str_getcsv', explode("\n", $csvData));


            $returnRows = [];
            /*

            OOLU4034505420    BL NO
             TEST           CURRENT STATUS

             */


            $success = 0;
            for ($line = 0; $line < (count($rows) - 1); $line++) {

                if (strlen(trim($rows[$line][0])) == 0) {
                    continue;
                }

                if(Container::whereBlNoFk($rows[$line][0])->count() == 0){

                    $returnRows[] = $rows[$line];

                    continue;
                }

                if (count($rows[$line]) < 2) {

                    for ($i = count($rows[$line]); $i < 2; $i++) {
                        $rows[$line][] = "";
                    }

                }



                try {
                    DB::beginTransaction();
                    DB::table('containers')
                        ->whereBlNoFk($rows[$line][0])
                        ->update([
                            'sop_current_status' => (strlen(trim($rows[$line][1])) > 0) ? $rows[$line][1] : null

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
                'upload_category' => 'UPDATE CURRENT STATUS',
                'uploaded_by' => Session::get('employee_number'),
            ]);

            if(count($returnRows) > 0){
               $returnRows[] = ['Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds'];
               return $this->_DownloadReturn($returnRows);
               // return $returnRows;
            }
            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds');

        } else {
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }

    }
    private function _DownloadReturn($returnRows){
        return  Excel::download(new ImportExcelReturnRowsExport($returnRows),'ReturnRows.xlsx');
    }

    public function doexcel_import_update_return(Request $request)
    {
        if ($request->hasFile('csvMaterial')) {

            $executionStartTime = microtime(true);

            $file = $request->file('csvMaterial');
            $filename = $file->getClientOriginalName();
            $csvData = file_get_contents($file);
            $this->storeUploadedCVS($filename,$csvData,'return');
            $rows = array_map('str_getcsv', explode("\n", $csvData));

            /*

            OOLU4034505420    BL NO
            OOLU9587787       CONTAINER #
            ACAI            RETURN CY
            ACAI            RETURN DATE

             */

            $returnRows = [];

            $success = 0;
//-- Begin looping for each rows --//
            for ($line = 0; $line < (count($rows) - 1); $line++) {

//-- Check if there is a lacking of columns --//
//-- if Lack of columns add a string --//

                if (count($rows[$line]) < 4) {

                    for ($i = count($rows[$line]); $i < 4; $i++) {
                        $rows[$line][] = "";
                    }

                }

                $rows[$line][0] = trim($rows[$line][0]);
                $rows[$line][1] = trim($rows[$line][1]);

                $ReturnString = [];

                if (strlen(trim($rows[$line][0])) == 0) {
                    continue;
                }


                if (Container::whereBlNoFk($rows[$line][0])->whereContainerNumber(trim($rows[$line][1]))->count() == 0) {

                    $ReturnString[] = "NO RECORD FOUND.";
                    $rows[$line][] = implode(',',$ReturnString);
                    $returnRows[] =  $rows[$line];
                    //$returnRows[] = $rows[$line];
                    continue;
                }

                $BOLDetails = Bill_of_lading::whereBlNo(trim($rows[$line][0]))->get();
                $ContainerDetails = Container::whereBlNoFk(trim($rows[$line][0]))
                                            ->whereContainerNumber($rows[$line][1])->get();


                if($BOLDetails[0]->actual_process == null){

                    $ReturnString[] = "NO ACTUAL PROCESS.";

                }

                if($ContainerDetails[0]->actual_discharge == null){

                    $ReturnString[] = "NO DISCHARGE YET.";

                }

                if($ContainerDetails[0]->actual_gatepass == null){

                    $ReturnString[] = "NO ACTUAL GATEPASS YET.";

                }

                if($ContainerDetails[0]->pull_out == null){

                    $ReturnString[] = "NO DELIVERY DATE YET.";

                }

                if($ContainerDetails[0]->unload == null){

                    $ReturnString[] = "NO UNLOADED DATE YET.";

                }

                $return_cy = (strlen(trim($rows[$line][2])) > 0) ? $rows[$line][2] : null;

				 $return_date = (strlen(trim($rows[$line][3])) > 0) ? date('Y-m-d', strtotime($rows[$line][3])) : null;



                if( $return_date != null && $ContainerDetails[0]->unload != null){
                    $d1 = strtotime($return_date);
                    $d2 = strtotime($ContainerDetails[0]->unload);

                    if($d1 < $d2)
                    {
                        $ReturnString[] = "return date is earlier than unload date {$ContainerDetails[0]->unload}";
                    }

                }

                if($return_date != null && $this->check_date_error($return_date)){
                    $ReturnString[] = $this->date_error_message("Return Date");
                }

                if(count($ReturnString) > 0){

                    $rows[$line][] = implode(',',$ReturnString);
                    $returnRows[] =  $rows[$line];
                    continue;

                }else{
                    $success++;
                }

				$id = container::whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->first()->id;

//-- Begin transaction --\\
                try {
                    DB::beginTransaction();
                    DB::table('containers')
                        ->whereBlNoFk($rows[$line][0])
                        ->whereContainerNumber($rows[$line][1])
                        ->update([
                            'return_cy' => $return_cy,
                            'return_date' =>  $return_date

                        ]);

					$split = new Split($id,'return_cy',$return_cy);
					$split = new Split($id,'return_date',$return_date);

                    DB::commit();
                    // all good
                } catch (\Exception $e) {
                    DB::rollback();
                    // return $e->getMessage();
                    return redirect()->route('importation.excel_import')->with('Error_Importation', $e->getMessage());
                    // something went wrong
                }
//-- END transaction --\\

            }

            $executionEndTime = microtime(true);
            //The result will be in seconds and milliseconds.
            $seconds = $executionEndTime - $executionStartTime;

            Uploading_CSV_Log::insert([
                'uploaded_filed' => $filename,
                'upload_category' => 'UPDATE RETURNED',
                'uploaded_by' => Session::get('employee_number'),
            ]);

            if(count($returnRows) > 0){
                $returnRows[] = ['Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds'];
                return $this->_DownloadReturn($returnRows);
                // return $returnRows;
            }

            //$request->session()->flash('message', 'Total imported Data : ' .  $success);
            return redirect()->route('importation.excel_import')->with('message', 'Total imported Data : ' . $success . ' total, Query took ' . $seconds . ' seconds');

        } else {
            return redirect()->route('importation.excel_import')->with('nofile', 'No attachment.');
        }

    }

    private function getTargetGatepass($value)
    {
        // Create a new DateTime object
        $date = new DateTime($value);
        $date->modify('+1 day');
        // Output
        $value = $date->format('Y-m-d');
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
                if ((date('N', strtotime((string) $newdate)) >= 6) == 1) {
                    // Modify the date it contains
                    $date->modify('next monday');
                    // Output
                    $newdate = $date->format('Y-m-d');
                }

            }

            //echo Holiday::where('holiday_date','=', (string) $newdate )->count();

        }

        return $newdate;
    }



    // ==============================
    // ADDITIONAL REQUEST FEBRUARY
    // ==============================

    public function incoming_vessels(){


        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2018-11-30';
        $i = 0;
        $list_distinct = [];
        $list_bl_volume = [];




        foreach ($factories as $factory) {
            $list_distinct[$i]['name'] = $factory['factory_id'];
            $list_bl_volume[$i]['name'] = $factory['factory_id'];


            $list_distinct[$i]['north'] =  bill_of_lading::select('connecting_vessel','bl_no','pod')
                                    ->distinct()
                                    ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                    ->whereQuantity(1)
                                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                                    ->whereNotNull('connecting_vessel')
                                    ->whereNotNull('estimated_time_arrival')
                                   // ->where('estimated_time_arrival', $date_request)
                                    ->whereNull('actual_time_arrival')
                                    ->whereFactory($factory['factory_id'])
                                    ->wherePod('NORTH')
                                    ->count('connecting_vessel');
            $north_bl = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotNull('connecting_vessel')
                                ->whereNotNull('estimated_time_arrival')
                                //->where('estimated_time_arrival', $date_request)
                                ->whereNull('actual_time_arrival')
                                ->whereFactory($factory['factory_id'])
                                ->wherePod('NORTH');
            $list_bl_volume[$i]['north_bl'] = $north_bl->count('bl_no');
            $bl_no = [];
            foreach( $north_bl->get() as $row ){
                $bl_no[]  = $row['bl_no'];
            }

            $list_bl_volume[$i]['north_volume'] = Container::whereQuantity(1)
                                                            ->whereIn('bl_no_fk',$bl_no)
                                                            ->count();



            $list_distinct[$i]['south'] =  bill_of_lading::select('connecting_vessel','bl_no','pod')
                                    ->distinct()
                                    ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                    ->whereQuantity(1)
                                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                                    ->whereNotNull('connecting_vessel')
                                    ->whereNotNull('estimated_time_arrival')
                                    //->where('estimated_time_arrival', $date_request)
                                    ->whereNull('actual_time_arrival')
                                    ->whereFactory($factory['factory_id'])
                                    ->wherePod('SOUTH')
                                    ->count('connecting_vessel');
            $south_bl = bill_of_lading::select('bl_no')
                                    ->distinct()
                                    ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                    ->whereQuantity(1)
                                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                                    ->whereNotNull('connecting_vessel')
                                    ->whereNotNull('estimated_time_arrival')
                                   // ->where('estimated_time_arrival', $date_request)
                                    ->whereNull('actual_time_arrival')
                                    ->whereFactory($factory['factory_id'])
                                    ->wherePod('SOUTH');
            $list_bl_volume[$i]['south_bl'] = $south_bl->count('bl_no');

            $bl_no = [];
            foreach( $south_bl->get() as $row ){
                $bl_no[]  = $row['bl_no'];
            }

            $list_bl_volume[$i]['south_volume'] = Container::whereQuantity(1)
                                                            ->whereIn('bl_no_fk',$bl_no)
                                                            ->count();


            $list_distinct[$i]['total'] =  $list_distinct[$i]['north'] + $list_distinct[$i]['south'];
            $list_bl_volume[$i]['bl_total'] = $list_bl_volume[$i]['south_bl'] +  $list_bl_volume[$i]['north_bl'];
            $list_bl_volume[$i]['volume_total'] = $list_bl_volume[$i]['south_volume'] +  $list_bl_volume[$i]['north_volume'];


            $i++;
        }

        //return $list_bl_volume;
        JavaScript::put([
            'date_request' => $date_request,
            'list_distinct' => $list_distinct,
            'list_bl_volume' => $list_bl_volume,
            'as_of_now' => true,
            'date_today' => Date('Y-m-d')

        ]);

        return view('pages.reports.incoming_vessels');

    }

    public function onboard_shipment(){



        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2018-11-30';
        $i = 0;
        $list_bl_volume = [];

        foreach ($factories as $factory) {
            $list_bl_volume[$i]['name'] = $factory['factory_id'];
            $list_bl_volume[$i]['bl_north'] = bill_of_lading::select('bl_no')
                                        ->distinct()
                                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('NORTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('estimated_time_departure')
                                        //->where('estimated_time_departure', $date_request)
                                        ->whereNull('actual_discharge')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_north'] = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('NORTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('estimated_time_departure')
                                       // ->where('estimated_time_departure', $date_request)
                                        ->whereNull('actual_discharge')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['bl_south'] = bill_of_lading::select('bl_no')
                                        ->distinct()
                                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('estimated_time_departure')
                                        //->where('estimated_time_departure', $date_request)
                                        ->whereNull('actual_discharge')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_south'] = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('estimated_time_departure')
                                       // ->where('estimated_time_departure', $date_request)
                                        ->whereNull('actual_discharge')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');

            $list_bl_volume[$i]['total_bl'] =  $list_bl_volume[$i]['bl_south']  + $list_bl_volume[$i]['bl_north'];
            $list_bl_volume[$i]['total_volume'] =  $list_bl_volume[$i]['volume_south']  + $list_bl_volume[$i]['volume_north'];
            $i++;
        }

        //return $list_bl_volume;
        JavaScript::put([
            'date_request' => $date_request,
            'list_bl_volume' => $list_bl_volume,
            'as_of_now' => true,
            'date_today' => Date('Y-m-d')

        ]);

        return view('pages.reports.onboard_shipment');

    }

    public function vessel_waiting(){



        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2018-11-30';
        $i = 0;
        $list_distinct = [];
        $list_bl_volume = [];




        foreach ($factories as $factory) {
            $list_distinct[$i]['name'] = $factory['factory_id'];
            $list_bl_volume[$i]['name'] = $factory['factory_id'];


            $list_distinct[$i]['north'] =  bill_of_lading::select('connecting_vessel','bl_no','pod')
                                        ->distinct()
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotNull('connecting_vessel')
                                        ->whereNotNull('actual_time_arrival')
                                        //->where('actual_time_arrival', $date_request)
                                        ->whereNull('actual_berthing_date')
                                        ->whereFactory($factory['factory_id'])
                                        ->wherePod('NORTH')
                                        ->count('connecting_vessel');

            $north_bl = bill_of_lading::select('bl_no')
                                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                                    ->whereNotNull('connecting_vessel')
                                    ->whereNotNull('actual_time_arrival')
                                    //->where('actual_time_arrival', $date_request)
                                    ->whereNull('actual_berthing_date')
                                    ->whereFactory($factory['factory_id'])
                                    ->wherePod('NORTH');

            $list_bl_volume[$i]['bl_north'] = $north_bl->count('bl_no');
            $bl_no = [];
            foreach( $north_bl->get() as $row ){
                $bl_no[]  = $row['bl_no'];
            }

            $list_bl_volume[$i]['volume_north'] = Container::whereQuantity(1)
                                                            ->whereIn('bl_no_fk',$bl_no)
                                                            ->count();



            $list_distinct[$i]['south'] =  bill_of_lading::select('connecting_vessel','bl_no','pod')
                                    ->distinct()
                                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                                    ->whereNotNull('connecting_vessel')
                                    ->whereNotNull('actual_time_arrival')
                                    //->where('actual_time_arrival', $date_request)
                                    ->whereNull('actual_berthing_date')
                                    ->whereFactory($factory['factory_id'])
                                    ->wherePod('SOUTH')
                                    ->count('connecting_vessel');
            $south_bl = bill_of_lading::select('bl_no')
                                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                                    ->whereNotNull('connecting_vessel')
                                    ->whereNotNull('actual_time_arrival')
                                    //->where('actual_time_arrival', $date_request)
                                    ->whereNull('actual_berthing_date')
                                    ->whereFactory($factory['factory_id'])
                                    ->wherePod('SOUTH');
            $list_bl_volume[$i]['bl_south'] = $south_bl->count('bl_no');

            $bl_no = [];
            foreach( $south_bl->get() as $row ){
                $bl_no[]  = $row['bl_no'];
            }

            $list_bl_volume[$i]['volume_south'] = Container::whereQuantity(1)
                                                            ->whereIn('bl_no_fk',$bl_no)
                                                            ->count();


            $list_distinct[$i]['total'] =  $list_distinct[$i]['north'] + $list_distinct[$i]['south'];
            $list_bl_volume[$i]['total_bl'] = $list_bl_volume[$i]['bl_south'] +  $list_bl_volume[$i]['bl_north'];
            $list_bl_volume[$i]['total_volume'] = $list_bl_volume[$i]['volume_north'] +  $list_bl_volume[$i]['volume_south'];


            $i++;
        }

        //return $list_bl_volume;
        JavaScript::put([
            'date_request' => $date_request,
            'list_distinct' => $list_distinct,
            'list_bl_volume' => $list_bl_volume,
            'as_of_now' => true,
            'date_today' => Date('Y-m-d')

        ]);

        return view('pages.reports.vessel_waiting');

    }


    public function notyet_discharge(){



        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2018-11-30';
        $i = 0;
        $list_bl_volume = [];

        foreach ($factories as $factory) {

            $list_bl_volume[$i]['name'] = $factory['factory_id'];
            $list_bl_volume[$i]['bl_north'] = bill_of_lading::select('bl_no')
                                        ->distinct()
                                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('NORTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('actual_berthing_date')
                                        //->where('actual_berthing_date', $date_request)
                                        ->whereNull('actual_discharge')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_north'] = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('NORTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('actual_berthing_date')
                                        //->where('actual_berthing_date', $date_request)
                                        ->whereNull('actual_discharge')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['bl_south'] = bill_of_lading::select('bl_no')
                                        ->distinct()
                                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('actual_berthing_date')
                                        //->where('actual_berthing_date', $date_request)
                                        ->whereNull('actual_discharge')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_south'] = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('actual_berthing_date')
                                        //->where('actual_berthing_date', $date_request)
                                        ->whereNull('actual_discharge')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');

            $list_bl_volume[$i]['total_bl'] =  $list_bl_volume[$i]['bl_south']  + $list_bl_volume[$i]['bl_north'];
            $list_bl_volume[$i]['total_volume'] =  $list_bl_volume[$i]['volume_south']  + $list_bl_volume[$i]['volume_north'];
            $i++;


        }

        //return $list_bl_volume;
        JavaScript::put([
            'date_request' => $date_request,
            'list_bl_volume' => $list_bl_volume,
            'as_of_now' => true,
            'date_today' => Date('Y-m-d')
        ]);

        return view('pages.reports.notyet_discharge');

    }

    public function shipment_on_process_report(){



        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2018-11-30';
        $i = 0;
        $list_bl_volume = [];

        foreach ($factories as $factory) {

            $list_bl_volume[$i]['name'] = $factory['factory_id'];
            $list_bl_volume[$i]['bl_north'] = bill_of_lading::select('bl_no')
                                        ->distinct()
                                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('NORTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->where('actual_process', $date_request)
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_north'] = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('NORTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->where('actual_process', $date_request)
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['bl_south'] = bill_of_lading::select('bl_no')
                                        ->distinct()
                                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->where('actual_process', $date_request)
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_south'] = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->where('actual_process', $date_request)
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');

            $list_bl_volume[$i]['total_bl'] =  $list_bl_volume[$i]['bl_south']  + $list_bl_volume[$i]['bl_north'];
            $list_bl_volume[$i]['total_volume'] =  $list_bl_volume[$i]['volume_south']  + $list_bl_volume[$i]['volume_north'];
            $i++;


        }

        //return $list_bl_volume;
        JavaScript::put([
            'date_request' => $date_request,
            'list_bl_volume' => $list_bl_volume,


        ]);

        return view('pages.reports.shipment_onprocess');

    }


    public function shipment_without_gatepass(){

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2018-11-30';
        $i = 0;
        $list_bl_volume = [];

        foreach ($factories as $factory) {

            $list_bl_volume[$i]['name'] = $factory['factory_id'];
            $list_bl_volume[$i]['bl_north'] = bill_of_lading::select('bl_no')
                                        ->distinct()
                                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('NORTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                       // ->where('actual_discharge', $date_request)
                                        ->whereNotNull('actual_discharge')
                                        ->whereNull('actual_gatepass')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_north'] = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('NORTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        //->where('actual_discharge', $date_request)
                                        ->whereNotNull('actual_discharge')
                                        ->whereNull('actual_gatepass')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['bl_south'] = bill_of_lading::select('bl_no')
                                        ->distinct()
                                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        //->where('actual_discharge', $date_request)
                                        ->whereNotNull('actual_discharge')
                                        ->whereNull('actual_gatepass')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_south'] = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                       // ->where('actual_discharge', $date_request)
                                        ->whereNotNull('actual_discharge')
                                        ->whereNull('actual_gatepass')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');

            $list_bl_volume[$i]['total_bl'] =  $list_bl_volume[$i]['bl_south']  + $list_bl_volume[$i]['bl_north'];
            $list_bl_volume[$i]['total_volume'] =  $list_bl_volume[$i]['volume_south']  + $list_bl_volume[$i]['volume_north'];
            $i++;


        }

        //return $list_bl_volume;
        JavaScript::put([
            'date_request' => $date_request,
            'list_bl_volume' => $list_bl_volume,
            'as_of_now' => true,
            'date_today' => Date('Y-m-d')

        ]);

        return view('pages.reports.shipment_without_gatepass');

    }


    public function onhand_gatepass(){

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2018-11-30';
        $i = 0;
        $list_bl_volume = [];

        foreach ($factories as $factory) {

            $list_bl_volume[$i]['name'] = $factory['factory_id'];
            $list_bl_volume[$i]['bl_north'] = bill_of_lading::select('bl_no')
                                        ->distinct()
                                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('NORTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('actual_gatepass')
                                        //->where('actual_gatepass', $date_request)
                                        ->whereNull('pull_out')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_north'] = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('NORTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('actual_gatepass')
                                        //->where('actual_gatepass', $date_request)
                                        ->whereNull('pull_out')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['bl_south'] = bill_of_lading::select('bl_no')
                                        ->distinct()
                                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('actual_gatepass')
                                        //->where('actual_gatepass', $date_request)
                                        ->whereNull('pull_out')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_south'] = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNotNull('actual_gatepass')
                                        //->where('actual_gatepass', $date_request)
                                        ->whereNull('pull_out')
                                        ->whereFactory($factory['factory_id'])
                                        ->count('bl_no');

            $list_bl_volume[$i]['total_bl'] =  $list_bl_volume[$i]['bl_south']  + $list_bl_volume[$i]['bl_north'];
            $list_bl_volume[$i]['total_volume'] =  $list_bl_volume[$i]['volume_south']  + $list_bl_volume[$i]['volume_north'];
            $i++;


        }

        //return $list_bl_volume;
        JavaScript::put([
            'date_request' => $date_request,
            'list_bl_volume' => $list_bl_volume,
            'as_of_now' => true,
            'date_today' => Date('Y-m-d')

        ]);

        return view('pages.reports.onhand_gatepass');

    }

    public function containers_irs(){

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2018-11-30';
        $i = 0;
        $list_bl_volume = [];
       // dismounted_cy
      // IRS BACAO
      //WITH CHASSI
      //CEZ 1 PUTOL
      //CEZ 2 PUTOL
        foreach ($factories as $factory) {

            $list_bl_volume[$i]['name'] = $factory['factory_id'];

            $list_bl_volume[$i]['irs'] =   bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                            ->whereQuantity(1)
                                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                                            ->whereNotIn('pod',['TBA'])
                                           // ->whereNotNull('dismounted_date')
                                            ->where('dismounted_date','<=',$date_request)
                                            ->whereFactory($factory['factory_id'])
                                            ->where('dismounted_cy','IRS BACAO')
                                            ->whereNull('unload')
                                            ->count();
            $i++;

        }


        $date_request = date('Y-m-d');
        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();

        $cType = Container_Type::select('name')->whereNull('deleted_at')->get();
        $factory_array = [];
        $i = 0;
        $factory_drilldown_array = [];

        $factory_tally = [];
        $factory_count = [];
        $factory_total = [];

        foreach ($factories as $factory) {

            $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->whereQuantity(1)
                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                    ->whereNotIn('pod',['TBA'])
                    ->whereNotNull('dismounted_date')
                    ->whereFactory($factory['factory_id'])
                    ->where('dismounted_cy','IRS BACAO')
                    ->whereNull('unload')

            //->whereNull('pull_out')
                ->count();

            if ($count > 0) {
                $factory_array[$i]['name'] = $factory['factory_id'];
                $factory_array[$i]['drilldown'] = $factory['factory_id'];
                $factory_array[$i]['y'] = $count;

                $factory_tally[] = $factory['factory_id'];

                $x = 0;
                $index_total = 0;
                $factory_drilldown_array[$i]['name'] = $factory['factory_id'];
                $factory_drilldown_array[$i]['id'] = $factory['factory_id'];
                $factory_drilldown_array[$i]['data'] = [];

                foreach ($cType as $type) {

                    $count = Container::select('container_number')
                        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                        ->where('container_type', $type['name'])
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNotIn('pod',['TBA'])
                        ->whereNotNull('dismounted_date')
                        ->whereFactory($factory['factory_id'])
                        ->where('dismounted_cy','IRS BACAO')
                        ->whereNull('unload')
                        ->count();
                    if ($count > 0) {
                        $factory_count[$factory['factory_id']][] = $count;
                        $factory_drilldown_array[$i]['data'][$x] = [$type['name'], $count];
                        $x++;

                    } else {
                        $factory_count[$factory['factory_id']][] = 0;
                    }

                    if (!array_key_exists($index_total, $factory_total)) {
                        $factory_total[$index_total] = 0;
                    }
                    $factory_total[$index_total] += $count;
                    $index_total++;

                }

                $i++;

            }

        }


        //return $list_bl_volume;
        JavaScript::put([

            'date_request' => $date_request,
            'list_bl_volume' => $list_bl_volume,
            'date_today' => Date('Y-m-d'),
            'factory_array' => $factory_array,
            'factory_drilldown_array' => $factory_drilldown_array,
            'factory_count' => $factory_count,
            'cType' => $cType,
            'factory_total' => $factory_total,

        ]);

        return view('pages.reports.containers_in_irs');

    }


    public function dismounted_with_chassi(){

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2018-11-30';
        $i = 0;
        $list_bl_volume = [];
       // dismounted_cy
      // IRS BACAO
      //WITH CHASSI
      //CEZ 1 PUTOL
      //CEZ 2 PUTOL
        foreach ($factories as $factory) {
            $list_bl_volume[$i]['name'] = $factory['factory_id'];

            $list_bl_volume[$i]['irs'] =   bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                            ->whereQuantity(1)
                                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                                            ->whereNotIn('pod',['TBA'])
                                            ->where('dismounted_date', $date_request)
                                            ->whereFactory($factory['factory_id'])
                                            ->where('dismounted_cy','IRS BACAO')
                                            ->count();

            $list_bl_volume[$i]['chassi'] =   bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                            ->whereQuantity(1)
                                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                                            ->whereNotIn('pod',['TBA'])
                                            ->where('dismounted_date', $date_request)
                                            ->whereFactory($factory['factory_id'])
                                            ->where('dismounted_cy','WITH CHASSI')
                                            ->count();


            $i++;
        }

        //return $list_bl_volume;
        JavaScript::put([
            'date_request' => $date_request,
            'list_bl_volume' => $list_bl_volume,

        ]);

        return view('pages.reports.dismounted_with_chassi');

    }

    public function containers_not_return(){

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $date_request = date('Y-m-d');
        //$date_request = '2019-01-30';
        $i = 0;
        $list_bl_volume = [];

        // dismounted_cy
        // IRS BACAO
        //WITH CHASSI
        //CEZ 1 PUTOL
        //CEZ 2 PUTOL

        foreach ($factories as $factory) {
            $list_bl_volume[$i]['name'] = $factory['factory_id'];

            $list_bl_volume[$i]['size'] =   bill_of_lading::
                                            select('container_type')
                                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                            ->whereQuantity(1)
                                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                                            ->whereNotIn('pod',['TBA'])
                                            ->whereNotNull('unload')
                                            //->where('unload', $date_request)
                                            ->whereNull('return_date')
                                            ->whereFactory($factory['factory_id'])
                                            ->orderBy('pull_out', 'asc')
                                            ->get();
            $list_bl_volume[$i]['volume'] =  count($list_bl_volume[$i]['size']);

            $i++;
        }

        $list_bl_volume[$i]['name'] = 'TOTAL';

        $list_bl_volume[$i]['size'] =   bill_of_lading::
                                            select('container_type')
                                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                            ->whereQuantity(1)
                                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                                            ->whereNotIn('pod',['TBA'])
                                            ->whereNotNull('unload')
                                            //->where('unload', $date_request)
                                            ->whereNull('return_date')
                                            ->orderBy('pull_out', 'asc')
                                            ->get();
        $list_bl_volume[$i]['volume'] =  count($list_bl_volume[$i]['size']);


        //return $list_bl_volume;
        JavaScript::put([
            'date_request' => $date_request,
            'list_bl_volume' => $list_bl_volume,
            'as_of_now' => true,
            'date_today' => Date('Y-m-d')


        ]);

        return view('pages.reports.containers_not_return');

    }



    public function unload_analysis($year = null, $m = null){

        if($m == null){
            $m = date('m');
        }
        if($year == null){
            $year = date('Y');
        }

        $n = (strlen($m) > 1) ? $m : '0' . $m;
        $number_of_days = cal_days_in_month(CAL_GREGORIAN, $m, $year); // 31
        $month_rep = date('M',strtotime($year . '-' . $m ));

        $unload = new \stdClass();
        $unload->name = "Unload";

        $unload_series = [];

        $categories = [];

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();

        $first_factory = 0;

        $total_unload_series = [];


        foreach($factories as $factory){
            $unload_obj = new \stdClass();
            $unload_obj->name =  $factory['factory_id'];
            $unload_obj->data = [];

            for($i = 1; $i <= $number_of_days;$i++){

                $i = (strlen($i) > 1) ? $i : '0' . $i;

                if($first_factory < 1){
                    $categories[] = $month_rep . ' ' . $i . ' (' . date('D',strtotime($month_rep . ' ' . $i . ' ' . $year)) . ')';
                }

                $count = Container::join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                                ->where('unload',$year. '-'.$n.'-'.$i)
                                ->where('factory', $factory['factory_id'])
                                ->whereQuantity(1)
                                ->count();

                $unload_obj->data[] = $count;
                if(array_key_exists(($i - 1), $total_unload_series)){
                    $total_unload_series[($i - 1)] += $count;
                }else{
                    $total_unload_series[($i - 1)] = $count;
                }

            }


                $unload_series[] =   $unload_obj;
                $first_factory++;
        }


        JavaScript::put([
            'categories' => $categories,
            'unload_series' => $unload_series,
            'total_unload_series'=>$total_unload_series,
            'm' => $m,
            'year' => $year

        ]);

        return view('pages.import_analysis.unload_analysis');


    }

    public function import_analysis($year = null, $m = null){

        if($m == null){
            $m = date('m');
        }
        if($year == null){
            $year = date('Y');
        }




        $n = (strlen($m) > 1) ? $m : '0' . $m;

        $number_of_days = cal_days_in_month(CAL_GREGORIAN, $m, $year); // 31
         $month_rep = date('M',strtotime($year . '-' . $m ));
        $categories = [];

        $discharge = new \stdClass();
        $gatepass = new \stdClass();
        $delivery = new \stdClass();
        $ontime = new \stdClass();
        $beyond_5 = new \stdClass();

        $discharge->name = "Discharge";
        $gatepass->name = "Gatepass";
        $delivery->name = "Delivery";
        $ontime->name = "On time delivery";
        $beyond_5->name = "Beyond 5 days";

        $gatepass_series = [];
        $container_20gp = [];
        $container_40hc = [];



        $discharge->data = [];
        $discharge->daily_date = [];
        $gatepass->data = [];
        $delivery->data = [];
        $ontime->data = [];
        $beyond_5->data = [];


        $cType = Container_Type::select('name')->whereNull('deleted_at')->get();
        $first_type = 0;
        $shipping_lines = Bill_of_lading::select('shipping_line')->distinct()->whereMonth('actual_time_arrival', $m )->orderBy('shipping_line','ASC')->get();

        foreach($shipping_lines as $c){
            $gatepass_obj = new \stdClass();
            $gatepass_obj->name =  $c['shipping_line'];
            $gatepass_obj->data = [];
            $gatepass_obj->containers = [];

            $obj20GP = new \stdClass();
            $obj20GP->name =  $c['shipping_line'];
            $obj20GP->data = [];

            $obj40HC = new \stdClass();
            $obj40HC->name =  $c['shipping_line'];
            $obj40HC->data = [];


            for($i = 1; $i <= $number_of_days;$i++){

                $i = (strlen($i) > 1) ? $i : '0' . $i;

                if(  $first_type < 1){

                        $categories[] = $month_rep . ' ' . $i . ' (' . date('D',strtotime($month_rep . ' ' . $i . ' ' . $year)) . ')';

                        $discharge->data[] = Container::
                                                where('actual_discharge',$year. '-'.$n.'-'.$i)
                                                ->whereQuantity(1)
                                                ->count();
                        $discharge->daily_date[] = $year. '-'.$n.'-'.$i;
                        $gatepass->data[] = Container::
                                                where('actual_gatepass',$year. '-'.$n.'-'.$i)
                                                ->whereQuantity(1)
                                                ->count();
                        $delivery->data[] = Container::
                                                where('pull_out',$year. '-'.$n.'-'.$i)
                                                ->whereQuantity(1)
                                                ->count();

                        $date_request = $year. '-'.$n.'-'. $i;
                        $six_days_before = date('Y-m-d', strtotime($date_request . ' -6 days'));
                        $ontime->data[] = Container::select('container_number')
                                        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                                        ->whereQuantity(1)
                                        ->where('pull_out', $date_request)
                                        ->where(function ($query) use ($six_days_before) {
                                            $query->Where('actual_discharge', '>=', $six_days_before);
                                        })
                                        ->count();

                        $beyond_5->data[] =  Container::select('container_number')
                                            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                                            ->whereQuantity(1)
                                            ->where('actual_discharge', '<=', $six_days_before)
                                            ->where(function ($query) use ($date_request) {
                                                $query->whereNull('pull_out');
                                                $query->orWhere('pull_out', '>', $date_request);
                                            })
                                            ->count();
                    }

                    $gatepass_obj->data[] =    Container::join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                                                ->where('actual_time_arrival',$year. '-'.$n.'-'.$i)
                                                ->where('shipping_line', $c['shipping_line'])
                                                ->whereQuantity(1)
                                                ->count();
                    $GP_20 = Container::join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                                ->where('actual_time_arrival',$year. '-'.$n.'-'.$i)
                                ->where('shipping_line', $c['shipping_line'])
                                ->where('container_type','20 GP')
                                ->whereQuantity(1)
                                ->count();
                    $obj20GP->data[] =  $GP_20;

                    $HC40 = Container::join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                                ->where('actual_time_arrival',$year. '-'.$n.'-'.$i)
                                ->where('shipping_line', $c['shipping_line'])
                                ->where('container_type','40 HC')
                                ->whereQuantity(1)
                                ->count();
                    $obj40HC->data[] =  $HC40 ;
                                                //actual_time_arrival
                    $gatepass_obj->containers[] = "20 GP : " .  $GP_20 . " || 40 HC : " . $HC40;

            }


                $gatepass_series[] =   $gatepass_obj;



                $container_20gp[] =   $obj20GP;



                $container_40hc[] =   $obj40HC;


            $first_type++;
        }









        $series = array($discharge,$gatepass,$delivery);

        $series_2nd = array($ontime,$beyond_5);


/*
            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
              ],
            series: [{
              name: 'Tokyo',
              data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]

            }, {
              name: 'New York',
              data: [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0, 104.3, 91.2, 83.5, 106.6, 92.3]

            }, {
              name: 'London',
              data: [48.9, 38.8, 39.3, 41.4, 47.0, 48.3, 59.0, 59.6, 52.4, 65.2, 59.3, 51.2]

            }, {
              name: 'Berlin',
              data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8, 51.1]

            }]

*/

            JavaScript::put([
                'categories' => $categories,
                'series' => $series,
                'series_2nd'=> $series_2nd,
                'gatepass_series' => $gatepass_series,
                'container_20gp' => $container_20gp,
                'container_40hc' => $container_40hc,
                'm' => $m,
                'year' => $year

            ]);
        return view('pages.import_analysis.analysis');

    }

    public function import_analysis_discharge_gatepass(Request $request){
        $date = $request->input('date');

        $discharge = new \stdClass();
        $gatepass = new \stdClass();


        $discharge->name = "Discharge";
        $gatepass->name = "Gatepass";
        $discharge->data = [];
        $gatepass->data = [];



        $date_today = date('Y-m-d');
        $get_discharge = Container::join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')->where('actual_discharge',$date)->whereQuantity(1)->get();
        $get_discharge_count = count($get_discharge);
        $discharge->data[] = $get_discharge_count;
        $date_list = [];

        $category = [];

        $total_discharge_from_discharge_tab = 0;

        while( $date <= $date_today && $get_discharge_count  > 0){
            $month_rep = date('M-d',strtotime($date));

            $count_gatepass = 0;


            foreach($get_discharge as $struct) {
                if ($date == (string)$struct->actual_gatepass) {
                    $count_gatepass++;
                }
            }
            $total_discharge_from_discharge_tab += $count_gatepass;
            $gatepass->data[] = $count_gatepass;
            $category[] = $month_rep . ' ' . date('D',strtotime( $month_rep));
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
            $get_discharge_count -= $count_gatepass;
            if($date <= $date_today && $get_discharge_count  > 0){

                $discharge->data[] = $get_discharge_count;

            }

            $date_list[] = $date;
        }




        $delivery = new \stdClass();
        $delivery_gatepass = new \stdClass();

        $delivery->name = "Delivery";
        $delivery_gatepass->name = "Gatepass";
        $delivery->data = [];
        $delivery_gatepass->data = [];

        $total_delivery = 0;
        $total_gatepass = 0;

        $category_delivery = [];



        $date = $request->input('date');
        $get_discharge = Container::join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')->where('actual_discharge',$date)->whereQuantity(1)->get();
        $get_discharge_count = count($get_discharge);

        $check_first_loop = 0;
        while( $date <= $date_today && $total_discharge_from_discharge_tab > 0){




            $month_rep = date('M-d',strtotime($date));
            $category_delivery[] = $month_rep . ' ' . date('D',strtotime( $month_rep));


            $count_gatepass = 0;
            $count_delivery = 0;


            foreach($get_discharge as $struct) {

                if ($date == (string)$struct->actual_gatepass) {
                    $count_gatepass++;

                }

                if ($date == (string)$struct->pull_out) {
                    $count_delivery++;
                }
            }









            $date = date('Y-m-d', strtotime($date . ' +1 day'));
            $total_gatepass += $count_gatepass;
            $delivery->data[] = $count_delivery;

            if($date <= $date_today){

                $delivery_gatepass->data[] = $total_gatepass;

            }
            $total_gatepass -= $count_delivery;


            $total_discharge_from_discharge_tab -= $count_delivery;
            $date_list[] = $date;

            $check_first_loop++;
        }

        $series = array($discharge,$gatepass);
        $series2 = array($delivery_gatepass,$delivery);


        foreach ($get_discharge as $row) {
            $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();
            $com = [];
            foreach ($row['commodities'] as $cm) {
                $com[] = $cm['commodity'];
            }
            $row['commodities'] = implode(',', $com);
            $row['pod'] = Bill_of_lading::select('pod')->whereBlNo($row['bl_no_fk'])->get('');
            $row['pod'] = $row['pod'][0]->pod;
        }

        $returnable = array($series,$category,$get_discharge,$series2,$category_delivery);
        return $returnable;

    }

    public function logistics_report(){


        return view('pages.reports.logistics_reports');
    }


    public function transport_schedule(){




        $factories = Factory::select('factory_id')->pluck('factory_id');
        $factories[] = '';
        //'2020-01-15' date('Y-m-d')
        $containerdischarge = new ContainerDischarge(date('Y-m-d'), Adddate::newdate(date('Y-m-d'),'+1 week'));

		$containerdischarge->get_discharge();
        //return $containerdischarge->get_data();

        $containerdischarge->get_eta();

		//return $containerdischarge->get_data();
        JavaScript::put([
            'data' => $containerdischarge->get_data(),
            'date_request' => date('Y-m-d'),
            'factories' => $factories
        ]);

        //return Container::whereNotNull('actual_discharge')
        //                 ->whereNotNull('actual_discharge')
        //                 ->get();

        return view('pages.reports.transport_schedule');
    }


    public function api_transport_schedule(Request $request){

        $rdate = $request->input('date_filter');

        $containerdischarge = new ContainerDischarge($rdate, Adddate::newdate($rdate,'+1 week'));
        $containerdischarge->get_discharge();
        $containerdischarge->get_eta();


        return $containerdischarge->get_data();

    }

    public function transport_schedule_export($date_filter){


        return Excel::download(new TransportScheduleExport($date_filter), 'transport_schedule_'.date('Y_m_d').'.xlsx');

    }

    public function transport_schedule_tally(){

        $daterange = Container::distinct()->select('actual_discharge')
                    ->where('quantity',1)
                    ->whereNotNull('actual_discharge')
                    ->whereNull('pull_out')
                    ->orderBy('actual_discharge','asc')
                    ->pluck('actual_discharge');

        $dates = DateRange::get_range( $daterange[0], $daterange[ count($daterange) - 1]);



        $factories = Factory::pluck('factory_id');
        $obj = [];

        foreach($dates as $date){

            $discharge_count =  Container::get_all_discharge( $date);
            if(  $discharge_count == 0){
                continue;
            }
            $obj[$date] = new \stdClass();
            $obj[$date]->date = $date;
            $obj[$date]->discharge =   $discharge_count;
            $obj[$date]->gatepass = Container::get_all_discharge( $date,true);
            $obj[$date]->total = [
                0, //south 20
                0, //south 40
                0,  //north 20
                0   //north 40
            ];
            foreach($factories as $factory){
                $ft20s =  Container::get_all_discharge( $date,false,$factory,20,'SOUTH');
                $ft40s =  Container::get_all_discharge( $date,false,$factory,40,'SOUTH');
                $ft20n =  Container::get_all_discharge( $date,false,$factory,20,'NORTH');
                $ft40n =  Container::get_all_discharge( $date,false,$factory,40,'NORTH');

                $obj[$date]->{$factory} = [
                    $ft20s,
                    $ft40s,
                    $ft20n,
                    $ft40n
                ];

                $obj[$date]->total[0] += $ft20s;
                $obj[$date]->total[1] += $ft40s;
                $obj[$date]->total[2] += $ft20n;
                $obj[$date]->total[3] += $ft40n;

            }



        }

        //return $obj;
        JavaScript::put([
            'data' => $obj,
            'factories' => $factories,
            'today' => date('Y-m-d')
        ]);
        //return Container::get_all_discharge('2020-01-14',true,'HTI',20);

        return view('pages.reports.transport_schedule_tally');
    }

    public function transport_schedule_tally_export(){

        $file = public_path()."/files/TALLYTEMPLATE4.xls";
        $headers = array('Content-Type: application/vnd.ms-excel');

       return Response::download($file, "Transport Schedule Tally.xls",$headers);

    }

    public function api_transport_schedule_tally(Request $request){

        $start = $request->input('range_start');
        $end = $request->input('range_end');





        $dates = DateRange::get_range($start, $end);
        $factories = Factory::pluck('factory_id');
        $obj = [];

        foreach($dates as $date){
            $obj[$date] = new \stdClass();

            $obj[$date]->date = $date;
            $obj[$date]->discharge = Container::get_all_discharge( $date);
            $obj[$date]->gatepass = Container::get_all_discharge( $date,true);
            $obj[$date]->total = [
                0,
                0
            ];
            foreach($factories as $factory){
                $ft20 =  Container::get_all_discharge( $date,false,$factory,20);
                $ft40 =  Container::get_all_discharge( $date,false,$factory,40);

                $obj[$date]->{$factory} = [
                    $ft20,
                    $ft40
                ];

                $obj[$date]->total[0] += $ft20;
                $obj[$date]->total[1] += $ft40;
            }



        }


        return $obj;


    }




    private function _deleteApostrophy($string){

        $trimmed = str_replace('`', '', $string) ;

        return $trimmed;
    }


}
