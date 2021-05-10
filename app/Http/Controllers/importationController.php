<?php

namespace App\Http\Controllers;

use Agent;
use App\Bill_of_Lading;
use App\Bill_of_Lading_Commodity;
use App\Bill_of_Lading_Invoice;
use App\Container;
use App\Container_Type;
use App\Factory;
use Illuminate\Support\Facades\Redirect;
use JavaScript;
use Session;
use DB;


class importationController extends Controller
{
    //

    public function __construct()
    {

        $this->middleware('updateUserPrivilege');

    }

    public function index($factory = null)
    {

        if (Agent::browser() == 'IE') {

            return Redirect::to(route('login.index'));

            //$IE_Detected = 'Please use a newer Browser like firefox or Chrome for better usage of the IMS.';
        }

        if ($factory == null) {
            $list_of_BOL = Bill_of_Lading::skip(0)->take(50)->orderBy('id', 'ASC')->get();
            $totalRecord = Bill_of_Lading::count();
        } else {
            $list_of_BOL = Bill_of_Lading::skip(0)->take(50)->whereFactory($factory)->orderBy('id', 'ASC')->get();
            $totalRecord = Bill_of_Lading::whereFactory($factory)->count();
        }

        $connecting_vessels = Bill_of_Lading::select('connecting_vessel')
            ->distinct()->get();

        $truckers = Container::select('trucker')
            ->distinct()->get();
        $factories = Factory::select('factory_id')
            ->distinct()->get();
        $dismounted_cys = Container::select('dismounted_cy')
            ->distinct()->get();

        $return_cys = Container::select('return_cy')
            ->distinct()->get();

        foreach ($list_of_BOL as $row) {

            $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $row['split_bl_no_list'] =  Container::select('container_number','split_bl_no_fk')->where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('split_bl_no_fk')->get();
            $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
            $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->get();
            $row['total_round_use'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('return_date')->count();
            $row['total_with_gatepass'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('actual_gatepass')->count();
            $row['total_container_without_gatepass'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')->whereNotNull('actual_process')->whereNull('containers.actual_gatepass')->count();
            $row['total_container_onhand'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->whereNotNull('actual_process')
                ->whereNotNull('containers.actual_gatepass')
                ->whereNull('pull_out')->count();
            $row['total_container_discharged'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->whereNotNull('containers.actual_discharge')
                ->count();
            $row['total_container_pullout'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->whereNotNull('containers.pull_out')
                ->count();
            $row['total_container_unload'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                ->whereNotNull('containers.unload')
                ->count();
            $row['total_container_delivered'] = Container::where('bl_no_fk', '=', $row['bl_no'])
                ->whereNotNull('pull_out')
                ->whereNull('unload')->count();

        }

       // return $list_of_BOL;
        JavaScript::put([

            'list_of_BOL' => $list_of_BOL,
            'connecting_vessels' => $connecting_vessels,
            'totalRecord' => $totalRecord,
            'truckers' => $truckers,
            'dismounted_cys' => $dismounted_cys,
            'return_cys' => $return_cys,
            'master' => Session::get('master'),
            'maintenance' => Session::get('maintenance'),
            'encoding' => Session::get('encoding'),
            'arrival' => Session::get('arrival'),
            'e2m' => Session::get('e2m'),
            'gatepass' => Session::get('gatepass'),
            'storage_validity' => Session::get('storage_validity'),
            'container_movement' => Session::get('container_movement'),
            'safe_keep' => Session::get('safe_keep'),
            'selected_factory' => strtoupper($factory),
            'factories' => $factories,

        ]);

        return view('pages.importindex');

    }

    public function new_record()
    {




        $split_lists = Container::split_lists();

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $container_types = Container_Type::select('name')->whereNull('deleted_at')->get();
        $suppliers = Bill_of_Lading::select('supplier')
            ->distinct()->get();
        $commodities = Bill_of_Lading_Commodity::select('commodity')
            ->distinct()->get();
        $vessels = Bill_of_Lading::select('vessel')
            ->distinct()->get();
        $connecting_vessels = Bill_of_Lading::select('connecting_vessel')
            ->distinct()->get();
        $shipping_lines = Bill_of_Lading::select('shipping_line')
            ->distinct()->get();
        $forwarders = Bill_of_Lading::select('forwarder')
            ->distinct()->get();
        $brokers = Bill_of_Lading::select('broker')
            ->distinct()->get();
        $pol = Bill_of_Lading::select('pol')
            ->distinct()->get();
        $countries = Bill_of_Lading::select('country')
            ->distinct()->get();

        $date_now = date('n', strtotime('m'));

        $previous_month = $date_now - 1;

        if ($previous_month == 0) {
            $date_year_now = date('Y', strtotime(date('Y-m-d') . ' -1 year'));
            $previous_month = 12;
        } else {
            $date_year_now = date('Y');
        }

        $list_of_bl_no_for_split = Bill_of_Lading::select('bl_no')
            ->where(function ($query) use ($date_now, $previous_month) {

                $query->whereMonth('processing_date', $date_now);
                $query->orWhereMonth('processing_date', $previous_month);

            })
            ->whereYear('processing_date', $date_year_now)
            ->get();

        // SPLITS LIST
        $month = date('m');
        $year = date('Y');
        $bl_with_split = Container::select('bl_no_fk', 'container_number', 'split_bl_no_fk')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->whereNotNull('split_bl_no_fk')
            ->whereQuantity(1)
            ->whereMonth('processing_date', $month)
            ->whereYear('processing_date', $year)
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
                    'quantity' => @$quantity[0]['quantity'],
                    'id' => @$quantity[0]['id'],
                ];

            }
            $split['splits_bl_array'] = $obj;
            unset($split['split_bl_no_fk']);
        }

        $bl_with_split;
        // END SPLITS LIST

        JavaScript::put([
            'factories' => $factories,
            'container_types' => $container_types,
            'commodities' => $commodities,
            'suppliers' => $suppliers,
            'vessels' => $vessels,
            'connecting_vessels' => $connecting_vessels,
            'shipping_lines' => $shipping_lines,
            'forwarders' => $forwarders,
            'brokers' => $brokers,
            'pol' => $pol,
            'countries' => $countries,
            'bl_with_split' => $bl_with_split,
            'list_of_bl_no_for_split' => $list_of_bl_no_for_split,
            'logged_in' => Session::get('employee_name'),
            'split_lists' => $split_lists,
        ]);

        return view('pages.importnew');
    }


    public function daily_boc()
    {

        $list_of_place_endorsement = Bill_of_Lading::select('place_endorsement')
                    ->distinct()->whereNotNull('place_endorsement')->get();


        JavaScript::put([
            'list_of_place_endorsement' => $list_of_place_endorsement,
        ]);
        return view('pages.daily_process_boc');

    }

    public function storage_demurrage()
    {


            $list_of_BOL_Total  = Bill_of_Lading::select('bl_no')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
                //->whereQuantity(1)
                ->whereNull('containers.pull_out')
                //2020-08-27
                ->whereNotNull('containers.actual_discharge')
                //->whereNotNull('containers.actual_gatepass')
                ->count('bl_no');

            $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
                ->skip(0)
                ->take(50)
                //->whereQuantity(1)
                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                ->whereNull('containers.pull_out')
                //2020-08-27
                ->whereNotNull('containers.actual_discharge')
                //->whereNotNull('containers.actual_gatepass')
                ->orderBy('containers.actual_discharge','ASC')
                ->get();

            // $list_of_BOL = Bill_of_Lading::whereNotNull('actual_gatepass')
            //   ->get();

            if(count($list_of_BOL) > 0){
                foreach ($list_of_BOL as $row) {
                    $row['sameDischarge'] = false;
                    $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                    $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
                    $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
                }
            }





        // $list_of_BOL = Bill_of_Lading::whereNotNull('actual_gatepass')
        //                ->get();

        $connecting_vessels = Bill_of_Lading::select('connecting_vessel')
            ->distinct()->get();

        $truckers = Container::select('trucker')
            ->distinct()->get();
        $dismounted_cys = Container::select('dismounted_cy')
            ->distinct()->get();

        $return_cys = Container::select('return_cy')
            ->distinct()->get();

        $shipping_lines = Bill_of_Lading::select('shipping_line')
            ->distinct()->get();
        $validity_storage = Container::select('validity_storage')
            ->whereNotNull('validity_storage')
            ->whereNull('revalidity_storage')
            ->distinct()->get();
        /*
        foreach($list_of_BOL as $row){

        $row['container_numbers'] = Container::where('bl_no_fk','=',$row['bl_no'])->whereNotNull('containers.actual_gatepass')->get();
        $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk','=',$row['bl_no'])->get();
        }
         */

        JavaScript::put([
            'validity_storage' => $validity_storage,
            'list_of_BOL' => $list_of_BOL,
            'truckers' => $truckers,
            'dismounted_cys' => $dismounted_cys,
            'return_cys' => $return_cys,
            'list_of_BOL_backup' => $list_of_BOL,
            'connecting_vessels' => $connecting_vessels,
            'shipping_lines' => $shipping_lines,
            'list_of_BOL_Total' =>  $list_of_BOL_Total,
        ]);

        return view('pages.storage_demurrage');
    }

    public function unload_returned()
    {


        $list_of_BOL_Total  = Bill_of_Lading::select('bl_no')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
        //->whereQuantity(1)
        ->whereNotNull('containers.pull_out')
        ->where(function ($query) {
            $query->whereNull('unload');
            $query->orWhereNull('return_date');
        })

        // ->whereNotIn('connecting_vessel',['T.B.A.'])
        // ->whereNotIn('pod',['TBA'])
        // ->whereNotNull('containers.unload')


        ->count('bl_no');



        $bl_no = [];


        $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
        ->skip(0)
        ->take(50)
        //->whereQuantity(1)
        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
         ->whereNotNull('containers.pull_out')
        ->where(function ($query) {
            $query->whereNull('unload');
            $query->orWhereNull('return_date');
        })

        // ->whereNotIn('connecting_vessel',['T.B.A.'])
        // ->whereNotIn('pod',['TBA'])
        // ->whereNotNull('containers.unload')



        ->get();

            // $list_of_BOL = Bill_of_Lading::whereNotNull('actual_gatepass')
            //   ->get();
        if(count($list_of_BOL) > 0){
            foreach ($list_of_BOL as $row) {
                $row['sameDischarge'] = false;
                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            }

        }



        // $list_of_BOL = Bill_of_Lading::whereNotNull('actual_gatepass')
        //                ->get();

        $connecting_vessels = Bill_of_Lading::select('connecting_vessel')
            ->distinct()->get();

        $truckers = Container::select('trucker')
            ->distinct()->get();
        $dismounted_cys = Container::select('dismounted_cy')
            ->distinct()->get();

        $return_cys = Container::select('return_cy')
            ->distinct()->get();

        $shipping_lines = Bill_of_Lading::select('shipping_line')
            ->distinct()->get();
        $validity_storage = Container::select('validity_storage')
            ->whereNotNull('validity_storage')
            ->whereNull('revalidity_storage')
            ->distinct()->get();
        /*

        foreach($list_of_BOL as $row){

            $row['container_numbers'] = Container::where('bl_no_fk','=',$row['bl_no'])->whereNotNull('containers.actual_gatepass')->get();
            $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk','=',$row['bl_no'])->get();
        }

        */

        JavaScript::put([
            'validity_storage' => $validity_storage,
            'list_of_BOL' => $list_of_BOL,
            'dismounted_cys' => $dismounted_cys,
            'return_cys' => $return_cys,
            'list_of_BOL_backup' => $list_of_BOL,
            'connecting_vessels' => $connecting_vessels,
            'shipping_lines' => $shipping_lines,
            'list_of_BOL_Total' =>  $list_of_BOL_Total,
        ]);

        return view('pages.unload_returned');

    }


    public function cleared_shipment()
    {

        $list_of_BOL_Total = Bill_of_Lading::join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
                                            ->whereQuantity(1)
                                            ->whereNotNull('return_date')
                                            ->whereNotNull('unload')
                                            ->count();

        $list_of_BOL  = Bill_of_Lading::join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
                                            ->skip(0)
                                            ->take(100)
                                            ->whereQuantity(1)
                                            ->whereNotNull('return_date')
                                            ->whereNotNull('unload')
                                            ->get();
            foreach ($list_of_BOL as $row) {

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                    $inv = [];
                    foreach ($row['invoice_numbers'] as $cm) {
                        $inv[] = $cm['invoice_number'];
                    }
                    $row['invoice_string'] = implode(',', $inv);

                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();

                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodities_string'] = implode(',', $com);



                // FOR FILTERING PURPOSES -----------------------------------
            }


        JavaScript::put([
            'list_of_BOL' => $list_of_BOL,
            'list_of_BOL_Total' => $list_of_BOL_Total
        ]);
        return view('pages.cleared_shipment');

    }

    public function arrival_update()
    {
        $total_records = 0;

        if (Session::get('arrival') == 1) {
            $list_of_BOL = Bill_of_Lading::select('bl_no')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
                ->whereNull('containers.actual_discharge')->distinct()->get();

            foreach ($list_of_BOL as $key) {
                $bl_no[] = $key["bl_no"];
            }

            if (count($list_of_BOL) > 0) {

                if (Session::get('e2m') == 1) {
                    $list_of_BOL = Bill_of_Lading::skip(0)->take(50)->whereIn('bl_no', $bl_no)
                        ->orWhereNull('e2m')
                        ->orWhereNull('date_endorse')
                        ->orWhereNull('date_approve_ip')
                        ->orWhereNull('assessment_tag')
                        ->orWhereNull('remarks_of_docs')
                        ->orWhereNull('tsad_no')
                        ->orderBy('id', 'DESC')->get();
                    $total_records = Bill_of_Lading::whereIn('bl_no', $bl_no)
                        ->orWhereNull('e2m')
                        ->orWhereNull('date_endorse')
                        ->orWhereNull('date_approve_ip')
                        ->orWhereNull('assessment_tag')
                        ->orWhereNull('remarks_of_docs')
                        ->orWhereNull('tsad_no')
                        ->orderBy('id', 'DESC')->count();
                } else {
                    $list_of_BOL = Bill_of_Lading::skip(0)->take(50)->whereIn('bl_no', $bl_no)->orderBy('id', 'DESC')->get();
                    $total_records = Bill_of_Lading::whereIn('bl_no', $bl_no)->orderBy('id', 'DESC')->count();
                }

            }

            foreach ($list_of_BOL as $row) {
                $row['sameDischarge'] = false;
                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNull('actual_discharge')->get();
                $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();

                // FOR FILTERING PURPOSES ----------------------------------
                // REASON : CANNOT USE LOWER CASE IF NULL VALUE
                //----------------------------------------------------------

                $row['connecting_vessel_string'] = ($row['connecting_vessel']) ? $row['connecting_vessel'] : "";
                $row['registry_no_string'] = ($row['registry_no']) ? $row['registry_no'] : "";
                $row['vessel_string'] = ($row['vessel']) ? $row['vessel'] : "";
                $row['pod_string'] = ($row['pod']) ? $row['pod'] : "";
                $row['estimated_time_arrival_string'] = ($row['estimated_time_arrival']) ? $row['estimated_time_arrival'] : "";
                $row['latest_estimated_time_arrival_string'] = ($row['latest_estimated_time_arrival']) ? $row['latest_estimated_time_arrival'] : "";
                $row['actual_time_arrival_string'] = ($row['actual_time_arrival']) ? $row['actual_time_arrival'] : "";
                $row['actual_berthing_date_string'] = ($row['actual_berthing_date']) ? $row['actual_berthing_date'] : "";
                $row['date_endorse_string'] = ($row['date_endorse']) ? $row['date_endorse'] : "";
                $row['date_approve_ip_string'] = ($row['date_approve_ip']) ? $row['date_approve_ip'] : "";
                $row['e2m_string'] = ($row['e2m']) ? $row['e2m'] : "";
                $row['actual_process_string'] = ($row['actual_process']) ? $row['actual_process'] : "";
                $row['assessment_tag_string'] = ($row['assessment_tag']) ? $row['assessment_tag'] : "";
                $row['remarks_of_docs_string'] = ($row['remarks_of_docs']) ? $row['remarks_of_docs'] : "";
                $row['tsad_no_string'] = ($row['tsad_no']) ? $row['tsad_no'] : "";

                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodities_string'] = implode(',', $com);

                // FOR FILTERING PURPOSES -----------------------------------
            }

        } else {
            $list_of_BOL = Bill_of_Lading::skip(0)->take(50)
                ->whereNull('e2m')
                ->orWhereNull('date_endorse')
                ->orWhereNull('date_approve_ip')
                ->orWhereNull('assessment_tag')
                ->orWhereNull('remarks_of_docs')
                ->orWhereNull('tsad_no')
                ->orderBy('id', 'DESC')
                ->get();

            $total_records = Bill_of_Lading::whereNull('e2m')
                ->orWhereNull('date_endorse')
                ->orWhereNull('date_approve_ip')
                ->orWhereNull('assessment_tag')
                ->orWhereNull('remarks_of_docs')
                ->orWhereNull('tsad_no')
                ->orderBy('id', 'DESC')
                ->count();

            foreach ($list_of_BOL as $row) {

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
                // FOR FILTERING PURPOSES ----------------------------------
                // REASON : CANNOT USE LOWER CASE IF NULL VALUE
                //----------------------------------------------------------

                $row['connecting_vessel_string'] = ($row['connecting_vessel']) ? $row['connecting_vessel'] : "";
                $row['vessel_string'] = ($row['vessel']) ? $row['vessel'] : "";
                $row['pod_string'] = ($row['pod']) ? $row['pod'] : "";
                $row['estimated_time_arrival_string'] = ($row['estimated_time_arrival']) ? $row['estimated_time_arrival'] : "";
                $row['actual_time_arrival_string'] = ($row['actual_time_arrival']) ? $row['actual_time_arrival'] : "";
                $row['actual_berthing_date_string'] = ($row['actual_berthing_date']) ? $row['actual_berthing_date'] : "";
                $row['date_endorse_string'] = ($row['date_endorse']) ? $row['date_endorse'] : "";
                $row['date_approve_ip_string'] = ($row['date_approve_ip']) ? $row['date_approve_ip'] : "";
                $row['e2m_string'] = ($row['e2m']) ? $row['e2m'] : "";
                $row['actual_process_string'] = ($row['actual_process']) ? $row['actual_process'] : "";
                $row['assessment_tag_string'] = ($row['assessment_tag']) ? $row['assessment_tag'] : "";
                $row['remarks_of_docs_string'] = ($row['remarks_of_docs']) ? $row['remarks_of_docs'] : "";
                $row['tsad_no_string'] = ($row['tsad_no']) ? $row['tsad_no'] : "";

                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodities_string'] = implode(',', $com);

                // FOR FILTERING PURPOSES -----------------------------------
            }

        }

        $connecting_vessels = Bill_of_Lading::select('connecting_vessel')
            ->distinct()
            ->orderBy('connecting_vessel', 'ASC')
            ->get();
        $connecting_vessels_filter = Bill_of_Lading::select('connecting_vessel')
            ->join('containers', 'containers.bl_no_fk', '=', 'bill_of_ladings.bl_no')
            ->whereNull('containers.actual_discharge')
            ->distinct()
            ->orderBy('connecting_vessel', 'ASC')
            ->get();
        $shipping_lines = Bill_of_Lading::select('shipping_line')->whereNull('actual_berthing_date')
            ->orWhereNull('e2m')
            ->distinct()
            ->orderBy('shipping_line', 'ASC')
            ->get();

        $browser = strtolower(Agent::browser());

        JavaScript::put([
            'list_of_BOL' => $list_of_BOL,
            'list_of_BOL_backup' => $list_of_BOL,
            'totalRecord' => $total_records,
            'connecting_vessels' => $connecting_vessels,
            'shipping_lines' => $shipping_lines,
            'connecting_vessels_filter' => $connecting_vessels_filter,
        ]);

        return view('pages.arrival_update',compact('browser'));

    }

    public function documentation_process(){

         $list_of_BOL = Bill_of_Lading::skip(0)->take(100)
            ->whereNull('e2m')
            ->orWhereNull('date_endorse')
            ->orWhereNull('date_approve_ip')
            ->orWhereNull('assessment_tag')
            ->orWhereNull('remarks_of_docs')
            ->orWhereNull('tsad_no')
            ->orderBy('registry_no', 'DESC')
            ->orderBy('estimated_time_arrival', 'ASC')

            ->get();

        $total_records = Bill_of_Lading::whereNull('e2m')
            ->orWhereNull('date_endorse')
            ->orWhereNull('date_approve_ip')
            ->orWhereNull('assessment_tag')
            ->orWhereNull('remarks_of_docs')
            ->orWhereNull('tsad_no')
            ->orderBy('id', 'DESC')
            ->count();


        JavaScript::put([
            'list_of_BOL' => $list_of_BOL,
            'list_of_BOL_Total' =>  $total_records,
        ]);

        return view('pages.documentation_process');

    }

    public function shipments_on_process()
    {

        $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
        $shipping_line =Bill_of_Lading::select('shipping_line')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
        ->whereNotNull('actual_process')->whereNull('containers.actual_gatepass')->distinct()->get();
        $list_of_BOL = Bill_of_Lading::select('bl_no','target_gatepass')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
        ->whereNotNull('actual_process')->whereNull('containers.actual_gatepass')
        ->orderBy('target_gatepass','ASC')
        ->distinct()->get();

        $list_of_BOL_Total = 0;
        foreach ($list_of_BOL as $key) {
            $bl_no[] = $key["bl_no"];
        }


        if (count($list_of_BOL) > 0) {
            $list_of_BOL = Bill_of_Lading::skip(0)->take(10)->whereIn('bl_no', $bl_no)
                ->whereNotNull('actual_process')
            //->whereNotNull('actual_berthing_date')
                ->orderBy('target_gatepass','ASC')
                ->get();
            $list_of_BOL_Total = Bill_of_Lading::whereIn('bl_no', $bl_no)
                ->whereNotNull('actual_process')
            //->whereNotNull('actual_berthing_date')

                ->count();
        }



        // $list_of_BOL = Bill_of_Lading::whereNotNull('e2m')->whereNotNull('actual_berthing_date')
        //                                 ->whereNull('actual_gatepass')
        //                                 ->get();

        foreach ($list_of_BOL as $row) {

            $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNull('actual_gatepass')->get();

        }

        JavaScript::put([
            'list_of_BOL' => $list_of_BOL,
            'list_of_BOL_Total' =>  $list_of_BOL_Total,
            'factories' => $factories,
            'shipping_line' => $shipping_line
        ]);
        return view('pages.shipments_on_process');

    }

}
