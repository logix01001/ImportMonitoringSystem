<?php

use App\User;
use App\Factory;
use App\Holiday;
use App\Container;
use App\Bill_of_Lading;
use App\Container_Type;
use App\Classfile\Split;
use Illuminate\Http\Request;
use App\Bill_of_Lading_Invoice;
use App\Bill_of_Lading_Commodity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });




Route::post('/bill_of_lading/get_Record_index', function (Request $request) {

    $skip = $request->input('skip');
    $take = $request->input('take');
    $selected_factory = $request->input('selected_factory');

    if ($take == 'ALL') {
        $take = Bill_of_Lading::count();
        if ($selected_factory == null) {
            $list_of_BOL = Bill_of_Lading::skip($skip)->take($take)->orderBy('id', 'ASC')->get();
        } else {
            $list_of_BOL = Bill_of_Lading::skip($skip)->take($take)->whereFactory($selected_factory)->orderBy('id', 'ASC')->get();
        }
    } else {
        if ($selected_factory == null) {
            $list_of_BOL = Bill_of_Lading::skip($skip)->take($take)->orderBy('id', 'ASC')->get();
        } else {
            $list_of_BOL = Bill_of_Lading::skip($skip)->take($take)->whereFactory($selected_factory)->orderBy('id', 'ASC')->get();
        }
    }

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

    return Response::json($list_of_BOL);

});

Route::post('/user/checkUser', function (Request $request) {

    if ($request->input('Type') == 'NEW') {
        return User::where('employee_number', $request->input('Users')['employee_number'])
            ->count();
    } else {
        return User::where('employee_number', $request->input('Users')['employee_number'])
            ->where('id', '!=', $request->input('Users')['id'])
            ->count();

    }
});

Route::post('/holiday/save', function (Request $request) {

    $request->input('Holidays')['id'];
    if ($request->input('Type') == 'EDIT') {

        Holiday::where('id', $request->input('Holidays')['id'])
            ->update([
                'holiday_name' => strtoupper($request->input('Holidays')['holiday_name']),
                'holiday_date' => ucfirst($request->input('Holidays')['holiday_date']),
                'holiday_day' => strtoupper($request->input('Holidays')['holiday_day']),
				'updated_at' => new \DateTime(),
            ]);

    } else {

        Holiday::insert([
            'holiday_name' => strtoupper($request->input('Holidays')['holiday_name']),
            'holiday_date' => ucfirst($request->input('Holidays')['holiday_date']),
            'holiday_day' => strtoupper($request->input('Holidays')['holiday_day']),
            'created_at' => new \DateTime(),
        ]);
    }

});

Route::post('/user/save', function (Request $request) {

    $request->input('Users')['id'];
    if ($request->input('Type') == 'EDIT') {

        User::where('id', $request->input('Users')['id'])
            ->update([
                'master' => $request->input('Users')['master'],
                'maintenance' => $request->input('Users')['maintenance'],
                'encoding' => $request->input('Users')['encoding'],
                'arrival' => $request->input('Users')['arrival'],
                'e2m' => $request->input('Users')['e2m'],
                'current_status' => $request->input('Users')['current_status'],
                'gatepass' => $request->input('Users')['gatepass'],
                'storage_validity' => $request->input('Users')['storage_validity'],
                'container_movement' => $request->input('Users')['container_movement'],
                'safe_keep' => $request->input('Users')['safe_keep'],
            ]);

    } else {

        $password = md5($request->input('Users')['password']);

        User::insert([
            'employee_number' => $request->input('Users')['employee_number'],
            'employee_name' => ucfirst($request->input('Users')['employee_name']),
            'password' => $password,
            'master' => $request->input('Users')['master'],
            'maintenance' => $request->input('Users')['maintenance'],
            'encoding' => $request->input('Users')['encoding'],
            'arrival' => $request->input('Users')['arrival'],
            'e2m' => $request->input('Users')['e2m'],
            'current_status' => $request->input('Users')['current_status'],
            'gatepass' => $request->input('Users')['gatepass'],
            'storage_validity' => $request->input('Users')['storage_validity'],
            'container_movement' => $request->input('Users')['container_movement'],
            'safe_keep' => $request->input('Users')['safe_keep'],
        ]);
    }

});

Route::post('/user/delete', function (Request $request) {
    $id = $request->input('id');
    User::find($id)->delete();
});
Route::post('/holiday/delete', function (Request $request) {
    $id = $request->input('id');
    Holiday::find($id)->delete();
});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * import_index.js
 * shipment_on_process.js
 *
 */
Route::post('/bill_of_lading/get_info', function (Request $request) {

    switch ($request->input('category')) {
        case 'I':

            $count = Bill_of_Lading_Invoice::select('bl_no_fk')->where('invoice_number', $request->input('bl_no'))->distinct()->count();

            if ($count > 0) {
                $bl_no = Bill_of_Lading_Invoice::select('bl_no_fk')->where('invoice_number', $request->input('bl_no'))->distinct()->get()->toArray();

                foreach ($bl_no as $key => $values) {
                    $bl_list[] = $values['bl_no_fk'];
                }

                $list_of_BOL = Bill_of_Lading::whereIn('bl_no', $bl_list)->get();

                // foreach ($list_of_BOL as $row) {

                //     $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
                //     $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['total_round_use'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('return_date')->count();

                // }


            } else {
                return [];
            }

            break;
        case 'C':
            $count = Container::select('bl_no_fk')->where('container_number', $request->input('bl_no'))->distinct()->count();

            if ($count > 0) {
                $bl_no = Container::select('bl_no_fk')->where('container_number', $request->input('bl_no'))->distinct()->get()->toArray();

                foreach ($bl_no as $key => $values) {
                    $bl_list[] = $values['bl_no_fk'];
                }

                $list_of_BOL = Bill_of_Lading::whereIn('bl_no', $bl_list)->get();




                // foreach ($list_of_BOL as $row) {

                //     $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
                //     $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['total_round_use'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('return_date')->count();

                // }

                // foreach ($list_of_BOL as $row) {

                //     $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
                //     $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['total_round_use'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('return_date')->count();
                //     $row['total_with_gatepass'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('actual_gatepass')->count();
                //     $row['total_container_without_gatepass'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')->whereNotNull('actual_process')->whereNull('containers.actual_gatepass')->count();
                //     $row['total_container_onhand'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                //         ->whereNotNull('actual_process')
                //         ->whereNotNull('containers.actual_gatepass')
                //         ->whereNull('pull_out')->count();
                //     $row['total_container_discharged'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                //         ->whereNotNull('containers.actual_discharge')
                //         ->count();
                //     $row['total_container_pullout'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                //         ->whereNotNull('containers.pull_out')
                //         ->count();
                //     $row['total_container_unload'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                //         ->whereNotNull('containers.unload')
                //         ->count();
                //     $row['total_container_delivered'] = Container::where('bl_no_fk', '=', $row['bl_no'])
                //         ->whereNotNull('pull_out')
                //         ->whereNull('unload')->count();

                // }

                // return $list_of_BOL;
            } else {
                return [];
            }

            break;
        case 'SOP':


            $bl_no_search = $request->input('bl_no');
            $search_factory = $request->input('search_factory');
            $search_shipping_line = $request->input('search_shipping_line');
            $search_current_status = $request->input('search_current_status');
            $search_target_gatepass = $request->input('search_target_gatepass');
            $search_commodity = $request->input('search_commodity');


            //$list_of_BOL = Bill_of_Lading::select('bl_no')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')->whereNotNull('actual_process')->whereBlNo($request->input('bl_no'))->get();
            $list_of_BOL = Bill_of_Lading::distinct()->select('bl_no')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
            ->join('bill_of_lading_commodities', 'bill_of_lading_commodities.bl_no_fk', 'bill_of_ladings.bl_no')
            ->whereNotNull('actual_process')
            ->whereNull('containers.actual_gatepass');


            if( $bl_no_search != ''){
                $list_of_BOL = $list_of_BOL->where('bill_of_ladings.bl_no', $bl_no_search);
            }

            if( $search_factory != ''){
                $list_of_BOL = $list_of_BOL->whereFactory($search_factory);
            }

            if( $search_shipping_line != ''){
                $list_of_BOL = $list_of_BOL->whereShippingLine($search_shipping_line);
            }

            if( $search_commodity != ''){

               $list_of_BOL = $list_of_BOL->where('commodity', 'like',  $search_commodity . '%');
            }

            if( $search_current_status != ''){

                $list_of_BOL = $list_of_BOL->where('sop_current_status', 'like',  $search_current_status . '%');
            }

            if( $search_target_gatepass != ''){

                $list_of_BOL = $list_of_BOL->where('target_gatepass',  $search_target_gatepass);

            }

            //->whereBlNo($request->input('bl_no'))
            //->get();

            $list_of_BOL = $list_of_BOL->get();

            foreach ($list_of_BOL as $key) {
                $bl_no[] = $key["bl_no"];
            }



            if (count($list_of_BOL) > 0) {
                $list_of_BOL = Bill_of_Lading::whereIn('bl_no', $bl_no)
                    ->whereNotNull('actual_process')
                //->whereNotNull('actual_berthing_date')
                    ->get();
            }


            if(count($list_of_BOL) > 0){
                foreach ($list_of_BOL as $row) {

                    $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no']) ->whereNull('containers.actual_gatepass')->get();


                }

                return $list_of_BOL;
            }
            // foreach ($list_of_BOL as $row) {

                //$row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
                //$row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNull('actual_gatepass')->get();

            // }

            // foreach ($list_of_BOL as $row) {

            //     $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
            //     $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
            //     $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            //     $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->get();
            //     $row['total_round_use'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('return_date')->count();
            //     $row['total_with_gatepass'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('actual_gatepass')->count();
            //     $row['total_container_without_gatepass'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')->whereNotNull('actual_process')->whereNull('containers.actual_gatepass')->count();
            //     $row['total_container_onhand'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            //         ->whereNotNull('actual_process')
            //         ->whereNotNull('containers.actual_gatepass')
            //         ->whereNull('pull_out')->count();
            //     $row['total_container_discharged'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            //         ->whereNotNull('containers.actual_discharge')
            //         ->count();
            //     $row['total_container_pullout'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            //         ->whereNotNull('containers.pull_out')
            //         ->count();
            //     $row['total_container_unload'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            //         ->whereNotNull('containers.unload')
            //         ->count();
            //     $row['total_container_delivered'] = Container::where('bl_no_fk', '=', $row['bl_no'])
            //         ->whereNotNull('pull_out')
            //         ->whereNull('unload')->count();

            // }
            // return $list_of_BOL;
            break;
        default:

            $count = Bill_of_Lading::select('bl_no_fk')->where('bl_no', 'like', '%' . $request->input('bl_no') )->distinct()->count();

            if ($count > 0) {

                $bl_no = Bill_of_Lading::select('bl_no')->where('bl_no', 'like', '%' . $request->input('bl_no') )->distinct()->get();

                foreach ($bl_no as $key => $values) {
                    $bl_list[] = $values['bl_no'];
                }

                $list_of_BOL = Bill_of_Lading::whereIn('bl_no', $bl_list)->get();

                // foreach ($list_of_BOL as $row) {

                //     $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
                //     $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->get();
                //     $row['total_round_use'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('return_date')->count();
                //     $row['total_with_gatepass'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('actual_gatepass')->count();
                //     $row['total_container_without_gatepass'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')->whereNotNull('actual_process')->whereNull('containers.actual_gatepass')->count();
                //     $row['total_container_onhand'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                //         ->whereNotNull('actual_process')
                //         ->whereNotNull('containers.actual_gatepass')
                //         ->whereNull('pull_out')->count();
                //     $row['total_container_discharged'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                //         ->whereNotNull('containers.actual_discharge')
                //         ->count();
                //     $row['total_container_pullout'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                //         ->whereNotNull('containers.pull_out')
                //         ->count();
                //     $row['total_container_unload'] = Container::where('bl_no_fk', '=', $row['bl_no'])->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                //         ->whereNotNull('containers.unload')
                //         ->count();
                //     $row['total_container_delivered'] = Container::where('bl_no_fk', '=', $row['bl_no'])
                //         ->whereNotNull('pull_out')
                //         ->whereNull('unload')->count();

                // }

                // return $list_of_BOL;

            }else{
                return [];
            }
    }

    if(count($list_of_BOL) > 0){
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

        return $list_of_BOL;
    }

});

Route::post('/bill_of_lading/get_info_boc', function (Request $request) {

    $data['info'] = Bill_of_Lading::where('bl_no', '=', $request->input('bl_no'))->get();
    $data['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $request->input('bl_no'))->get();

    return $data;

});

// Route::post('/bill_of_lading/boc/save',function(Request $request){

//     $BL_NO              =   $request->input('BOC')  ['bl_no'];
//     $date_endorse       =   $request->input('BOC')  ['date_endorse'];
//     $place_endorsement  =   $request->input('BOC')  ['place_endorsement'];
//     $actual_process     =   $request->input('BOC')  ['actual_process'];
//     $remarks_of_docs    =   $request->input('BOC')  ['remarks_of_docs'];
//     $tsad_no            =   $request->input('BOC')  ['tsad_no'];
//     $assessment_tag  =   $request->input('BOC')  ['assessment_tag'];

//     if($assessment_tag == 'RED'){
//         $remarks_of_docs = 'AS RED ' . $remarks_of_docs;
//     }else{
//         $remarks_of_docs = str_replace('AS RED ','',$remarks_of_docs);
//     }

//     $Update_BOC = [
//         'date_endorse' =>$date_endorse,
//         'place_endorsement' =>  strtoupper($place_endorsement),
//         'actual_process' =>  $actual_process,
//         'remarks_of_docs' => strtoupper($remarks_of_docs),
//         'tsad_no' =>  strtoupper($tsad_no),
//         'assessment_tag' =>  strtoupper($assessment_tag),

//     ];

//     Bill_of_Lading::where('bl_no',$BL_NO)
//                 ->update($Update_BOC);

// });

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * arrival_update.js
 *
 *
 */

Route::post('/bill_of_lading/assessment_tag_remarks', function (Request $request) {

    $BL_NO = $request->input('bl_no');
    $remarks_of_docs = $request->input('remarks_of_docs');
    $assessment_tag = $request->input('assessment_tag');

    $Update_BOC = [
        'remarks_of_docs' => strtoupper($remarks_of_docs),
        'assessment_tag' => strtoupper($assessment_tag),

    ];

     return   Bill_of_Lading::where('bl_no', $BL_NO)
            ->update($Update_BOC);
   // return Bill_of_Lading::touch();

});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * shipment_on_process.js
 *
 *
 */

Route::post('/bill_of_lading/reason_delay_gatepass', function (Request $request) {

    $BL_NO = $request->input('id');
    $gatepass = $request->input('gatepass');
    $reason = $request->input('reason');
    $current_status = $request->input('current_status');

    $Update_BOC = [
        'sop_current_status' => $current_status,
        'actual_gatepass' => $gatepass,
        'reason_of_delay_gatepass' => strtoupper($reason),

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
            ]);
    }

});

Route::post('/bill_of_lading/reason_delay_gatepass_container', function (Request $request) {

    $id = $request->input('id');
    $gatepass = $request->input('gatepass');
    $reason = $request->input('reason');
    $current_status = $request->input('current_status');

    $Update_BOC = [
        'sop_current_status' => $current_status,
        'actual_gatepass' => $gatepass,
        'reason_of_delay_gatepass' => strtoupper($reason),

    ];

    Container::where('id', $id)
        ->update($Update_BOC);

    $split = new Split($id,'actual_gatepass', $gatepass);
    $split->achieved_gatepass($current_status,strtoupper($reason));

});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * import_index.js
 *
 *
 */
Route::post('/bill_of_lading/refresh_master', function (Request $request) {

    $selected_factory = $request->input('selected_factory');

    if ($selected_factory == null) {
        $list_of_BOL = Bill_of_Lading::skip(0)->take(50)->orderBy('id', 'DESC')->get();
        $totalRecord = Bill_of_Lading::count();
    } else {
        $list_of_BOL = Bill_of_Lading::skip(0)->take(50)->whereFactory($selected_factory)->orderBy('id', 'DESC')->get();
        $totalRecord = Bill_of_Lading::whereFactory($selected_factory)->count();
    }

    foreach ($list_of_BOL as $row) {

        $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
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

    return $list_of_BOL;

});
/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * shipment_on_process.js
 *
 *
 */
Route::post('/bill_of_lading/refresh_master_sop', function (Request $request) {

    $list_of_BOL = Bill_of_Lading::select('bl_no')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
        ->whereNull('containers.actual_gatepass')->distinct()->get();

    foreach ($list_of_BOL as $key) {
        $bl_no[] = $key["bl_no"];
    }

    if (count($list_of_BOL) > 0) {
        $list_of_BOL = Bill_of_Lading::skip(0)->take(50)->whereIn('bl_no', $bl_no)
            ->whereNotNull('actual_process')
        // ->whereNotNull('actual_berthing_date')
            ->get();
    }

    // $list_of_BOL = Bill_of_Lading::whereNotNull('e2m')->whereNotNull('actual_berthing_date')
    // ->whereNull('actual_gatepass')
    // ->get();

    foreach ($list_of_BOL as $row) {
        $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNull('actual_gatepass')->get();
    }

    return $list_of_BOL;

});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * arrival_update.js
 *
 *
 */
Route::post('/bill_of_lading/refresh_master_arrival_update_e2m', function (Request $request) {

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

    // foreach ($list_of_BOL as $row) {

    //     $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
    //     $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
    //     $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
    //     $row['connecting_vessel_string'] = ($row['connecting_vessel']) ? $row['connecting_vessel'] : ""; $row['pod_string'] = ($row['pod']) ? $row['pod'] : "";
    //     $row['vessel_string'] = ($row['vessel']) ? $row['vessel'] : "";
    //     $row['pod_string'] = ($row['pod']) ? $row['pod'] : "";
    //     $row['estimated_time_arrival_string'] = ($row['estimated_time_arrival']) ? $row['estimated_time_arrival'] : "";
    //     $row['actual_time_arrival_string'] = ($row['actual_time_arrival']) ? $row['actual_time_arrival'] : "";
    //     $row['actual_berthing_date_string'] = ($row['actual_berthing_date']) ? $row['actual_berthing_date'] : "";
    //     $row['date_endorse_string'] = ($row['date_endorse']) ? $row['date_endorse'] : "";
    //     $row['date_approve_ip_string'] = ($row['date_approve_ip']) ? $row['date_approve_ip'] : "";
    //     $row['e2m_string'] = ($row['e2m']) ? $row['e2m'] : "";
    //     $row['actual_process_string'] = ($row['actual_process']) ? $row['actual_process'] : "";
    //     $row['assessment_tag_string'] = ($row['assessment_tag']) ? $row['assessment_tag'] : "";
    //     $row['remarks_of_docs_string'] = ($row['remarks_of_docs']) ? $row['remarks_of_docs'] : "";
    //     $row['tsad_no_string'] = ($row['tsad_no']) ? $row['tsad_no'] : "";
    //     $com = [];
    //     foreach ($row['commodities'] as $cm) {
    //         $com[] = $cm['commodity'];
    //     }
    //     $row['commodities_string'] = implode(',', $com);
    // }

    return $list_of_BOL;

});

Route::post('/bill_of_lading/get_Record_arrival_update_e2m', function (Request $request) {

    $skip = $request->input('skip');
    $take = $request->input('take');
    if ($take == 'ALL') {
        $take = Bill_of_Lading::count();
    }
    $list_of_BOL = Bill_of_Lading::skip($skip)->take($take)->whereNull('e2m')
        ->orWhereNull('date_endorse')
        ->orWhereNull('date_approve_ip')
        ->orWhereNull('assessment_tag')
        ->orWhereNull('remarks_of_docs')
        ->orWhereNull('tsad_no')
        ->orderBy('registry_no', 'DESC')
        ->orderBy('estimated_time_arrival', 'ASC')
        ->get();
    /*
    foreach ($list_of_BOL as $row) {

        $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
        $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['connecting_vessel_string'] = ($row['connecting_vessel']) ? $row['connecting_vessel'] : ""; $row['pod_string'] = ($row['pod']) ? $row['pod'] : "";
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

    }
*/
    return Response::json($list_of_BOL);

});

Route::post('/bill_of_lading/refresh_master_arrival_update_discharge', function (Request $request) {

    $list_of_BOL = Bill_of_Lading::select('bl_no')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
        ->whereNull('containers.actual_discharge')->distinct()->get();

    foreach ($list_of_BOL as $key) {
        $bl_no[] = $key["bl_no"];
    }

    $list_of_BOL = Bill_of_Lading::skip(0)->take(50)->whereIn('bl_no', $bl_no)->orderBy('id', 'DESC')->get();

    foreach ($list_of_BOL as $row) {
        $row['sameDischarge'] = false;
        $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNull('actual_discharge')->get();
        $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
        $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['connecting_vessel_string'] = ($row['connecting_vessel']) ? $row['connecting_vessel'] : "";
        $row['registry_no_string'] = ($row['registry_no']) ? $row['registry_no'] : "";
        $row['pod_string'] = ($row['pod']) ? $row['pod'] : "";
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
    }

    return $list_of_BOL;

});

Route::post('/bill_of_lading/get_Record_arrival_update_discharge', function (Request $request) {

    $skip = $request->input('skip');
    $take = $request->input('take');

    $list_of_BOL = Bill_of_Lading::select('bl_no')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
        ->whereNull('containers.actual_discharge')->distinct()->get();

    foreach ($list_of_BOL as $key) {
        $bl_no[] = $key["bl_no"];
    }

    if (count($list_of_BOL) > 0) {
        if ($take == 'ALL') {
            $take = Bill_of_Lading::count();
        }
        $list_of_BOL = Bill_of_Lading::skip($skip)->take($take)->whereIn('bl_no', $bl_no)->orderBy('id', 'DESC')->get();

    }

    foreach ($list_of_BOL as $row) {
        $row['sameDischarge'] = false;
        $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNull('actual_discharge')->get();
        $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
        $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['connecting_vessel_string'] = ($row['connecting_vessel']) ? $row['connecting_vessel'] : "";
        $row['registry_no_string'] = ($row['registry_no']) ? $row['registry_no'] : "";
        $row['pod_string'] = ($row['pod']) ? $row['pod'] : "";
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
    }

    return Response::json($list_of_BOL);

});

Route::post('/bill_of_lading/refresh_master_arrival_update_discharge_e2m', function (Request $request) {

    $list_of_BOL = Bill_of_Lading::select('bl_no')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
        ->whereNull('containers.actual_discharge')->distinct()->get();

    foreach ($list_of_BOL as $key) {
        $bl_no[] = $key["bl_no"];
    }

    $list_of_BOL = Bill_of_Lading::skip(0)->take(50)->whereIn('bl_no', $bl_no)
        ->orWhereNull('e2m')
        ->orWhereNull('date_endorse')
        ->orWhereNull('date_approve_ip')
        ->orWhereNull('assessment_tag')
        ->orWhereNull('remarks_of_docs')
        ->orWhereNull('tsad_no')
        ->orderBy('id', 'DESC')->get();

    foreach ($list_of_BOL as $row) {
        $row['sameDischarge'] = false;
        $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNull('actual_discharge')->get();
        $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
        $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['connecting_vessel_string'] = ($row['connecting_vessel']) ? $row['connecting_vessel'] : "";
        $row['registry_no_string'] = ($row['registry_no']) ? $row['registry_no'] : "";
        $row['pod_string'] = ($row['pod']) ? $row['pod'] : "";
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
    }

    return $list_of_BOL;

});

Route::post('/bill_of_lading/get_Record_arrival_update_discharge_e2m', function (Request $request) {

    $skip = $request->input('skip');
    $take = $request->input('take');
    $connecting_vessel = $request->input('connecting_vessel');

    $query = Bill_of_Lading::select('bl_no')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
        ->whereNull('containers.actual_discharge');

    if ($connecting_vessel == 'EMPTY_FIELD') {

        $query = $query->whereNull('connecting_vessel');

    } else {
        if ($connecting_vessel != '') {
            $query = $query->where('connecting_vessel', '=', $connecting_vessel);
        }
    }

    $list_of_BOL = $query->get();

    foreach ($list_of_BOL as $key) {
        $bl_no[] = $key["bl_no"];
    }

    if ($take == 'ALL') {
        $take = Bill_of_Lading::count();
    }

    if (count($list_of_BOL) > 0) {

        $list_of_BOL = Bill_of_Lading::skip($skip)->take($take)->whereIn('bl_no', $bl_no)
            ->orWhereNull('e2m')
            ->orWhereNull('date_endorse')
            ->orWhereNull('date_approve_ip')
            ->orWhereNull('assessment_tag')
            ->orWhereNull('remarks_of_docs')
            ->orWhereNull('tsad_no')
            ->orderBy('id', 'DESC')->get();

    }

    foreach ($list_of_BOL as $row) {
        $row['sameDischarge'] = false;
        $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNull('actual_discharge')->get();
        $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
        $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['connecting_vessel_string'] = ($row['connecting_vessel']) ? $row['connecting_vessel'] : "";
        $row['registry_no_string'] = ($row['registry_no']) ? $row['registry_no'] : "";
        $row['pod_string'] = ($row['pod']) ? $row['pod'] : "";
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
    }

    return Response::json($list_of_BOL);

});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * storage_demurrage.js
 *
 *
 */

Route::post('/bill_of_lading/refresh_storage_validation', function (Request $request) {




    $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
        ->skip(0)
        ->take(50)
        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
        //->whereQuantity(1)
        ->whereNull('containers.pull_out')

        //2020-08-27
        ->whereNotNull('containers.actual_discharge')
        //->whereNotNull('containers.actual_gatepass')
        ->get();
        // $list_of_BOL = Bill_of_Lading::whereNotNull('actual_gatepass')
        //  ->get();
    if (count($list_of_BOL) > 0) {
        foreach ($list_of_BOL as $row) {
            $row['sameDischarge'] = false;
            $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
            $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
        }

    }

    return $list_of_BOL;

});

Route::post('/bill_of_lading/refresh_unload_returned', function (Request $request) {




    $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
        ->skip(0)
        ->take(50)
        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
        //->whereQuantity(1)
        ->whereNotNull('containers.pull_out')
        ->whereNull('containers.unload')
        ->orWhereNull('containers.return_date')
        ->get();
        // $list_of_BOL = Bill_of_Lading::whereNotNull('actual_gatepass')
        //  ->get();
    if (count($list_of_BOL) > 0) {
        foreach ($list_of_BOL as $row) {
            $row['sameDischarge'] = false;
            $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
            $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
        }

    }

    return $list_of_BOL;

});

Route::post('/bill_of_lading/refresh_cleared_shipment', function (Request $request) {




    $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
        ->skip(0)
        ->take(100)
        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
        ->whereQuantity(1)
        ->whereNotNull('return_date')
        ->whereNotNull('unload')
        ->get();
        // $list_of_BOL = Bill_of_Lading::whereNotNull('actual_gatepass')
        //  ->get();
    if (count($list_of_BOL) > 0) {
        foreach ($list_of_BOL as $row) {
            $row['sameDischarge'] = false;
            $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $inv = [];
            foreach ($row['invoice_numbers'] as $cm) {
                $inv[] = $cm['invoice_number'];
            }
            $row['invoice_string'] = implode(',', $inv);
            $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
            $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $com = [];
            foreach ($row['commodities'] as $cm) {
                $com[] = $cm['commodity'];
            }
            $row['commodities_string'] = implode(',', $com);
        }

    }

    return $list_of_BOL;

});
/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * storage_demurrage.js
 *
 *
 */

Route::post('/bill_of_lading/save_validation_revalidation_date', function (Request $request) {

    $id = $request->input('id');
    $columnName = $request->input('columnName');
    $value = $request->input('value');

    if (strlen(trim($value)) == 0) {

        Container::where('id', $id)

            ->update([
                (string) $columnName => null,
            ]);

    } else {

        Container::where('id', $id)

            ->update([
                (string) $columnName => strtoupper($value),
            ]);

        $split = new Split($id,$columnName, strtoupper($value));

    }

});


Route::post('/bill_of_lading/save_column_value_bl', function (Request $request) {

    $id = $request->input('id');
    $columnName = $request->input('columnName');
    $value = $request->input('value');

    if (strlen(trim($value)) == 0) {

        Bill_of_Lading::where('id', $id)

            ->update([
                (string) $columnName => null,
            ]);

    } else {

        Bill_of_Lading::where('id', $id)

            ->update([
                (string) $columnName => strtoupper($value),
            ]);

    }

});
/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * arrival_update.js
 *
 *
 */
Route::post('/bill_of_lading/arrival_update_filter', function (Request $request) {

    $connecting_vessel = $request->input('connecting_vessel');
    $shipping_line = $request->input('shipping_line');
    $search = $request->input('search');

    $query = DB::table('bill_of_ladings')
        ->select('bill_of_ladings.id','latest_estimated_time_arrival', 'pod', 'connecting_vessel_confirm', 'factory', 'bl_no', 'shipping_line', 'vessel', 'connecting_vessel','registry_no', 'estimated_time_arrival', 'actual_time_arrival', 'actual_berthing_date', 'date_endorse', 'date_approve_ip', 'e2m', 'actual_process', 'assessment_tag', 'remarks_of_docs', 'tsad_no','target_gatepass')
        ->join('containers', 'containers.bl_no_fk', '=', 'bill_of_ladings.bl_no')
        ->where(function ($query) {
            $query->whereNull('containers.actual_discharge');
        });

    if ($connecting_vessel == 'EMPTY_FIELD') {

        $query = $query->whereNull('connecting_vessel');

    } else {
        if ($connecting_vessel != '') {
            $query = $query->where('connecting_vessel', '=', $connecting_vessel);
        }
    }

    if ($shipping_line != '') {
        $query = $query->where('shipping_line', '=', $shipping_line);
    }

    if ($search != '') {
        $query = $query->where('containers.container_number', 'like', '%' . strtoupper($search));
    }

    $list_of_BOL = $query->distinct()->get();
    $list_of_BOL = json_decode($list_of_BOL, true);
    $c = 0;
    foreach ($list_of_BOL as $row) {
        $list_of_BOL[$c]['sameDischarge'] = false;
        $list_of_BOL[$c]['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $list_of_BOL[$c]['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNull('actual_discharge')->get();
        $list_of_BOL[$c]['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $list_of_BOL[$c]['connecting_vessel_string'] = ($list_of_BOL[$c]['connecting_vessel']) ? $list_of_BOL[$c]['connecting_vessel'] : "";
        $list_of_BOL[$c]['registry_no_string'] = ($list_of_BOL[$c]['registry_no']) ? $list_of_BOL[$c]['registry_no'] : "";
        $list_of_BOL[$c]['vessel_string'] = ($list_of_BOL[$c]['vessel']) ? $list_of_BOL[$c]['vessel'] : "";
        $list_of_BOL[$c]['pod_string'] = ($list_of_BOL[$c]['pod']) ? $list_of_BOL[$c]['pod'] : "";
        $list_of_BOL[$c]['estimated_time_arrival_string'] = ($list_of_BOL[$c]['estimated_time_arrival']) ? $list_of_BOL[$c]['estimated_time_arrival'] : "";
        $list_of_BOL[$c]['latest_estimated_time_arrival_string'] = ($list_of_BOL[$c]['latest_estimated_time_arrival']) ? $list_of_BOL[$c]['latest_estimated_time_arrival'] : "";
        $list_of_BOL[$c]['actual_time_arrival_string'] = ($list_of_BOL[$c]['actual_time_arrival']) ? $list_of_BOL[$c]['actual_time_arrival'] : "";
        $list_of_BOL[$c]['actual_berthing_date_string'] = ($list_of_BOL[$c]['actual_berthing_date']) ? $list_of_BOL[$c]['actual_berthing_date'] : "";
        $list_of_BOL[$c]['date_endorse_string'] = ($list_of_BOL[$c]['date_endorse']) ? $list_of_BOL[$c]['date_endorse'] : "";
        $list_of_BOL[$c]['date_approve_ip_string'] = ($list_of_BOL[$c]['date_approve_ip']) ? $list_of_BOL[$c]['date_approve_ip'] : "";
        $list_of_BOL[$c]['e2m_string'] = ($list_of_BOL[$c]['e2m']) ? $list_of_BOL[$c]['e2m'] : "";
        $list_of_BOL[$c]['actual_process_string'] = ($list_of_BOL[$c]['actual_process']) ? $list_of_BOL[$c]['actual_process'] : "";
        $list_of_BOL[$c]['assessment_tag_string'] = ($list_of_BOL[$c]['assessment_tag']) ? $list_of_BOL[$c]['assessment_tag'] : "";
        $list_of_BOL[$c]['remarks_of_docs_string'] = ($list_of_BOL[$c]['remarks_of_docs']) ? $list_of_BOL[$c]['remarks_of_docs'] : "";
        $list_of_BOL[$c]['tsad_no_string'] = ($list_of_BOL[$c]['tsad_no']) ? $list_of_BOL[$c]['tsad_no'] : "";
        $com = [];
        foreach ($list_of_BOL[$c]['commodities'] as $cm) {
            $com[] = $cm['commodity'];
        }
        $list_of_BOL[$c]['commodities_string'] = implode(',', $com);
        $c++;

    }

    return Response::json($list_of_BOL);

});

Route::post('/bill_of_lading/arrival_update_filter_e2m', function (Request $request) {

    $search = $request->input('search');

    $query = DB::table('bill_of_ladings')
        ->select('bill_of_ladings.id','latest_estimated_time_arrival', 'pod', 'connecting_vessel_confirm', 'factory', 'bl_no', 'shipping_line', 'vessel', 'connecting_vessel','registry_no', 'estimated_time_arrival', 'actual_time_arrival', 'actual_berthing_date', 'date_endorse', 'date_approve_ip', 'e2m', 'actual_process', 'assessment_tag', 'remarks_of_docs', 'tsad_no')

        ->where(function ($query) {

            $query->whereNull('e2m');
			$query->orWhereNull('date_endorse');
			$query->orWhereNull('date_approve_ip');
			$query->orWhereNull('assessment_tag');
			$query->orWhereNull('remarks_of_docs');
			$query->orWhereNull('tsad_no');
        });

    if ($search != '') {
        $query = $query->where('bill_of_ladings.bl_no', 'like', '%' . strtoupper($search) );
    }

    $list_of_BOL = $query->distinct()->get();
    $list_of_BOL = json_decode($list_of_BOL, true);
    $c = 0;
    foreach ($list_of_BOL as $row) {
        $list_of_BOL[$c]['sameDischarge'] = false;
        $list_of_BOL[$c]['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $list_of_BOL[$c]['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->get();
        $list_of_BOL[$c]['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $list_of_BOL[$c]['connecting_vessel_string'] = ($list_of_BOL[$c]['connecting_vessel']) ? $list_of_BOL[$c]['connecting_vessel'] : "";
        $list_of_BOL[$c]['registry_no_string'] = ($list_of_BOL[$c]['registry_no']) ? $list_of_BOL[$c]['registry_no'] : "";
        $list_of_BOL[$c]['vessel_string'] = ($list_of_BOL[$c]['vessel']) ? $list_of_BOL[$c]['vessel'] : "";
        $list_of_BOL[$c]['pod_string'] = ($list_of_BOL[$c]['pod']) ? $list_of_BOL[$c]['pod'] : "";
        $list_of_BOL[$c]['estimated_time_arrival_string'] = ($list_of_BOL[$c]['estimated_time_arrival']) ? $list_of_BOL[$c]['estimated_time_arrival'] : "";
        $list_of_BOL[$c]['latest_estimated_time_arrival_string'] = ($list_of_BOL[$c]['latest_estimated_time_arrival']) ? $list_of_BOL[$c]['latest_estimated_time_arrival'] : "";
        $list_of_BOL[$c]['actual_time_arrival_string'] = ($list_of_BOL[$c]['actual_time_arrival']) ? $list_of_BOL[$c]['actual_time_arrival'] : "";
        $list_of_BOL[$c]['actual_berthing_date_string'] = ($list_of_BOL[$c]['actual_berthing_date']) ? $list_of_BOL[$c]['actual_berthing_date'] : "";
        $list_of_BOL[$c]['date_endorse_string'] = ($list_of_BOL[$c]['date_endorse']) ? $list_of_BOL[$c]['date_endorse'] : "";
        $list_of_BOL[$c]['date_approve_ip_string'] = ($list_of_BOL[$c]['date_approve_ip']) ? $list_of_BOL[$c]['date_approve_ip'] : "";
        $list_of_BOL[$c]['e2m_string'] = ($list_of_BOL[$c]['e2m']) ? $list_of_BOL[$c]['e2m'] : "";
        $list_of_BOL[$c]['actual_process_string'] = ($list_of_BOL[$c]['actual_process']) ? $list_of_BOL[$c]['actual_process'] : "";
        $list_of_BOL[$c]['assessment_tag_string'] = ($list_of_BOL[$c]['assessment_tag']) ? $list_of_BOL[$c]['assessment_tag'] : "";
        $list_of_BOL[$c]['remarks_of_docs_string'] = ($list_of_BOL[$c]['remarks_of_docs']) ? $list_of_BOL[$c]['remarks_of_docs'] : "";
        $list_of_BOL[$c]['tsad_no_string'] = ($list_of_BOL[$c]['tsad_no']) ? $list_of_BOL[$c]['tsad_no'] : "";
        $com = [];
        foreach ($list_of_BOL[$c]['commodities'] as $cm) {
            $com[] = $cm['commodity'];
        }
        $list_of_BOL[$c]['commodities_string'] = implode(',', $com);
        $c++;

    }

    return Response::json($list_of_BOL);

});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * storage_demurrage.js
 *
 *
 */

Route::post('/bill_of_lading/storage_demurrage_search', function (Request $request) {

    $search = $request->input('search');

    if ($search == null || empty($search)) {

        $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
            ->skip(0)
            ->take(50)
            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
            //->whereQuantity(1)


            ->whereNull('containers.pull_out')
            //2020-08-27
            ->whereNotNull('containers.actual_discharge')
            //->whereNotNull('containers.actual_gatepass')
            ->get();

        if (count($list_of_BOL) > 0) {
            foreach ($list_of_BOL as $row) {

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            }
        }

    } else if ($request->input('category') == 'BL') {



        $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
            ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
            //->whereQuantity(1)
            ->where('bl_no', 'like',  '%' . $search )


            ->whereNull('containers.pull_out')
            //2020-08-27
            ->whereNotNull('containers.actual_discharge')
            //->whereNotNull('containers.actual_gatepass')
            ->get();

        if (count($list_of_BOL) > 0) {
            foreach ($list_of_BOL as $row) {

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();

            }
        }

    } else {



        $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
        ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
        //->whereQuantity(1)
        ->whereNull('containers.pull_out')
        //2020-08-27
        ->whereNotNull('containers.actual_discharge')
        //->whereNotNull('containers.actual_gatepass')
        ->where('containers.container_number', 'like', '%' . $search )
        ->get();

        $list_of_BOL = json_decode($list_of_BOL, true);
        $c = 0;
        foreach ($list_of_BOL as $row) {
            $list_of_BOL[$c]['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $list_of_BOL[$c]['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $c++;
        }

    }

    return Response::json($list_of_BOL);

});

Route::post('/bill_of_lading/unload_returned_search', function (Request $request) {

    $search = $request->input('search');

    if ($search == null || empty($search)) {


        $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
            ->skip(0)
            ->take(50)
            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
            //->whereQuantity(1)
            ->where(function ($query) {
                $query->whereNull('unload');
                $query->orWhereNull('return_date');
            })
            ->whereNotNull('containers.pull_out')
            ->get();


        if (count($list_of_BOL) > 0) {
            foreach ($list_of_BOL as $row) {

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            }
        }

    } else if ($request->input('category') == 'BL') {

            $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
                //->whereQuantity(1)
                ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
                ->where(function ($query) {
                    $query->whereNull('unload');
                    $query->orWhereNull('return_date');
                })
                ->whereNotNull('containers.pull_out')
                ->where('bl_no', 'like', '%' . $search )
                ->get();

            if (count($list_of_BOL) > 0) {
                foreach ($list_of_BOL as $row) {

                    $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                    $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();

                }
            }

    } else {



        $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
        ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
        //->whereQuantity(1)
        ->whereNotNull('containers.pull_out')
        ->where(function ($query) {
            $query->whereNull('unload');
            $query->orWhereNull('return_date');
        })
        ->where('containers.container_number', 'like', '%' . $search )
        ->get();

        $list_of_BOL = json_decode($list_of_BOL, true);
        $c = 0;
        foreach ($list_of_BOL as $row) {
            $list_of_BOL[$c]['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $list_of_BOL[$c]['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $c++;
        }

    }

    return Response::json($list_of_BOL);

});

Route::post('/bill_of_lading/cleared_shipment_search', function (Request $request) {

    $search = $request->input('search');

    if ($search == null || empty($search)) {


        $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
            ->skip(0)
            ->take(50)
            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
            ->whereQuantity(1)
            ->whereNotNull('return_date')
            ->whereNotNull('unload')
            ->get();

        if (count($list_of_BOL) > 0) {
            foreach ($list_of_BOL as $row) {

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            }
        }

    } else if ($request->input('category') == 'BL') {



            $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
                ->whereQuantity(1)
                ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
                ->where('bl_no', 'like',  '%' . $search )
                ->whereNotNull('return_date')
                ->whereNotNull('unload')
                ->get();

        if (count($list_of_BOL) > 0) {
            foreach ($list_of_BOL as $row) {

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();

            }
        }

    } else {



        $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
        ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
        ->whereQuantity(1)
        ->whereNotNull('return_date')
        ->whereNotNull('unload')
        ->where('containers.container_number', 'like', '%' . $search )
        ->get();

        $list_of_BOL = json_decode($list_of_BOL, true);
        $c = 0;
        foreach ($list_of_BOL as $row) {
            $list_of_BOL[$c]['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $list_of_BOL[$c]['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            $c++;
        }

    }

    return Response::json($list_of_BOL);

});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * storage_demurrage.js
 *
 *
 */

Route::post('/bill_of_lading/storage_demurrage_filter_validity_storage', function (Request $request) {

    $validity_storage = $request->input('validity_storage');

    // $query = DB::table('bill_of_ladings')->select('bl_no','factory')
    //     ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no');

    // $query =  $query->where('containers.validity_storage','=',  $validity_storage );

    if ($validity_storage == null || empty($validity_storage)) {
        // $list_of_BOL = Bill_of_Lading::whereNotNull('actual_gatepass')
        // ->get();
        // foreach($list_of_BOL as $row){

        //     $row['container_numbers'] = Container::where('bl_no_fk','=',$row['bl_no'])->get();
        //     $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk','=',$row['bl_no'])->get();
        // }
    } else {
        // $BOL_NO =  Container::select('bl_no_fk')->distinct()->where('containers.validity_storage','=',  $validity_storage )->get();

        // $list_of_BOL = Bill_of_Lading::whereIn('bl_no',$BOL_NO)->get();

        // $list_of_BOL = json_decode($list_of_BOL,true);
        // $c = 0;
        // foreach($list_of_BOL as $row){
        //     $list_of_BOL[$c]['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk','=',$row['bl_no'])->get();
        //     $list_of_BOL[$c]['container_numbers'] = Container::where('bl_no_fk','=',$row['bl_no'])->where('containers.validity_storage','=',  $validity_storage )->get();
        //     $c++;
        // }
    }

    return Response::json($list_of_BOL);

});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * arrival_update.js
 * import_index
 *
 */

Route::post('/bill_of_lading/save_connecting_confirm', function (Request $request) {
    $id = $request->input('id');
    $value = $request->input('value');
    Bill_of_Lading::where('id', $id)

        ->update([
            'connecting_vessel_confirm' => $value,
        ]);
});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * Documentation process.js
 *
 *
 */

Route::post('/bill_of_lading/save_registry_no_confirm', function (Request $request) {
    $id = $request->input('id');
    $value = $request->input('value');
    Bill_of_Lading::where('id', $id)

        ->update([
            'registry_no_confirm' => $value,
        ]);
});
/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * storage_demurrage.js
 *
 *
 */

Route::post('/bill_of_lading/save_xray_confirm', function (Request $request) {

    $id = $request->input('id');
    $value = $request->input('value');

    Container::where('id', $id)

        ->update([
            'xray' => $value,
        ]);
});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 * shipment_on_process.js
 * arrival_update.js
 *
 */

Route::post('/bill_of_lading/save_container_column', function (Request $request) {

    $id = $request->input('id');
    $columnName = $request->input('columnName');
    $value = $request->input('value');

    if (strlen(trim($value)) == 0) {

        Container::where('id', $id)

            ->update([
                (string) $columnName => null,
            ]);

    } else {

        Container::where('id', $id)

            ->update([
                (string) $columnName => strtoupper($value),
            ]);

        $split = new Split($id,$columnName, $value);
       return $split->achieved_gatepass('ACHIEVED GATEPASS');

    }



});

Route::post('/bill_of_lading/save_bill_of_lading_field', function (Request $request) {
    $id = $request->input('id');
    $columnName = $request->input('columnName');
    $value = $request->input('value');

    $flight = App\Flight::find(1);

    $flight->name = 'New Flight Name';

    $flight->save();


});
/*----------------------------
 * API for Blur records of bill of lading
 *
 *
 *
 */
Route::post('/bill_of_lading/save_bill_of_lading_blur', function (Request $request) {
    $id = $request->input('id');
    $columnName = $request->input('columnName');
    $value = $request->input('value');



        if (strlen(trim($value)) == 0) {

            Bill_of_Lading::where('id', $id)

                ->update([
                    (string) $columnName => null,
                ]);

        } else {

            Bill_of_Lading::where('id', $id)

                ->update([
                    (string) $columnName => strtoupper($value),
                ]);

        }

});

Route::post('/bill_of_lading/save_arrival_blur', function (Request $request) {

    $id = $request->input('id');
    $columnName = $request->input('columnName');
    $value = $request->input('value');

    if ((string) $columnName != 'sop_current_status' && (string) $columnName != 'actual_gatepass') {

        if (strlen(trim($value)) == 0) {

            Bill_of_Lading::where('id', $id)

                ->update([
                    (string) $columnName => null,
                ]);

        } else {

            Bill_of_Lading::where('id', $id)

                ->update([
                    (string) $columnName => strtoupper($value),
                ]);

        }

    }


    if ((string) $columnName == 'sop_current_status' || (string) $columnName == 'actual_gatepass') {


        $bl_no = Bill_of_Lading::select('bl_no')->find($id)['bl_no'];

        $list_id = Container::select('id')->whereNull('actual_gatepass')->where('bl_no_fk', $bl_no)->get();
        if (count($list_id) > 0) {
            foreach ($list_id as $id) {

                $ids[] = $id['id'];
            }

            if (strlen(trim($value)) == 0) {

                Container::whereIn('id', $ids)

                    ->update([
                        (string) $columnName => null,
                    ]);

            } else {

                Container::whereIn('id', $ids)
                    ->update([
                        (string) $columnName => $value,
                    ]);

            }

        }

    }





    if ((string) $columnName == 'actual_berthing_date') {
        // Create a new DateTime object


        $bl_no = Bill_of_lading::select('bl_no')->where('id', $id)->get();

        $bl_no = $bl_no[0]->bl_no;

        $discharge_latest =  Container::select('actual_discharge')->skip(0)->take(1)->where('bl_no_fk', $bl_no)->orderBy('actual_discharge','DESC')->get();
        $discharge_latest =  $discharge_latest[0]->actual_discharge;

        if($discharge_latest != null){

            $discharge=strtotime($discharge_latest);
            $berthed=strtotime($value);

            if($berthed < $discharge)
            {
                $value = $discharge_latest;
            }

        }


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



        Bill_of_Lading::where('id', $id)

        ->update([
            'target_gatepass' => $newdate,
        ]);

    }

    // if ((string) $columnName != 'sop_remarks') {

    //     if (strlen(trim($value)) == 0) {

    //         Bill_of_Lading::where('id', $id)

    //             ->update([
    //                 (string) $columnName => null,
    //             ]);

    //     } else {

    //         Bill_of_Lading::where('id', $id)

    //             ->update([
    //                 (string) $columnName => strtoupper($value),
    //             ]);

    //     }

    // }



});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 *
 * arrival_update.js
 *
 */
Route::post('/bill_of_lading/save_container_discharge', function (Request $request) {

    $id = $request->input('id');
    $value = $request->input('value');
    $deteinputed = $request->input('value');



    $bl_no = $request->input('bl_no');

    $discharge_latest =  Container::select('actual_discharge')->skip(0)->take(1)->where('bl_no_fk', $bl_no)->orderBy('actual_discharge','DESC')->get();
    $discharge_latest =  $discharge_latest[0]->actual_discharge;


    if($discharge_latest == null){
        $discharge_latest = $deteinputed;
    }

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


    DB::beginTransaction();

    try {

        $split = new Split($id,'actual_discharge',$deteinputed);

        Container::where('id', $id)
        ->update([
            'actual_discharge' => $deteinputed,
        ]);


        Bill_of_Lading::where('bl_no', $bl_no)
        ->update([
            'target_gatepass' => $newdate,
        ]);

        DB::commit();
        // all good
    } catch (\Exception $e) {
        DB::rollback();
        // something went wrong
    }






    //$bl_no =

    // Bill_of_Lading::where('id', $id)

    // ->update([
    //     'target_gatepass' => $newdate,
    // ]);



});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 *
 * arrival_update.js
 *
 */

Route::post('/bill_of_lading/save_container_discharge_all', function (Request $request) {

    $ids = $request->input('ids');
    $value = $request->input('value');
    $deteinputed = $request->input('value');






	//NEWLY ADDED
	$bl_no = $request->input('bl_no');

	$discharge_latest =  Container::select('actual_discharge')->skip(0)->take(1)->where('bl_no_fk', $bl_no)->orderBy('actual_discharge','DESC')->get();
    $discharge_latest =  $discharge_latest[0]->actual_discharge;


    if($discharge_latest == null){
        $discharge_latest = $deteinputed;
    }

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



    DB::beginTransaction();

    try {

        foreach($ids as $id){
            $split = new Split($id,'actual_discharge',$deteinputed);
        }


        Container::whereIn('id', $ids)
        ->update([
            'actual_discharge' => $deteinputed,
        ]);

        Bill_of_Lading::where('bl_no', $bl_no)
        ->update([
            'target_gatepass' => $newdate,
        ]);
        DB::commit();
        // all good
    } catch (\Exception $e) {
        DB::rollback();
        // something went wrong
    }

});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 *
 * import_new.js
 *
 */

Route::post('/bill_of_lading/transfer_bl_no_edit', function (Request $request) {

    $bl_no_old = $request->get('bl_no');
    $bl_no_new = $request->get('new_bl_no');
    $edited_by = $request->get('edited_by');
    $cpu = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $ip = request()->ip();



    try {
        DB::beginTransaction();
        DB::table('bill_of_ladings')->where('bl_no', $bl_no_old
        )->update(
            [
                'bl_no' => $bl_no_new,
            ]
        );
        DB::table('bill_of_lading_invoices')->where('bl_no_fk', $bl_no_old
        )->update(
            [
                'bl_no_fk' => $bl_no_new,
            ]
        );
        DB::table('bill_of_lading_commodities')->where('bl_no_fk', $bl_no_old
        )->update(
            [
                'bl_no_fk' => $bl_no_new,
            ]
        );
        DB::table('containers')->where('bl_no_fk', $bl_no_old
        )->update(
            [
                'bl_no_fk' => $bl_no_new,
            ]
        );

        DB::table('tranfer_bl_logs')->insert([
            'old_bl_number' => $bl_no_old,
            'new_bl_number' => $bl_no_new,
            'edited_by' => $edited_by,
            'cpu_used' => $cpu . ' ' . $ip,
            'date_edited' => date('Y-m-d H:i:s'),

        ]);
        DB::commit();
        // all good
    } catch (\Exception $e) {
        DB::rollback();
        return $e->getMessage();
        // something went wrong
    }

});

Route::post('/bill_of_lading/save', function (Request $request) {

    try {
        $BOL = $request->input('BOL');
        $shipping_docs = $BOL['shipping_docs'] . ' ' . $BOL['shipping_docs_time'];

        $invoiceNumber = explode(',', $BOL['invoice_no']);
        $commodities = $BOL['commodity'];

        foreach ($invoiceNumber as $key => $value) {
            $IN_DATA[] = array('bl_no_fk' => $BOL['bl_no'], 'invoice_number' => strtoupper($value));
        };

        foreach ($commodities as $key => $value) {
            $COMMODITY_DATA[] = array('bl_no_fk' => $BOL['bl_no'], 'commodity' => strtoupper($value));
        };

        $Containers = $request->input('Containers');
        $index = 0;

        foreach ($Containers as $container) {

            $Containers[$index]['bl_no_fk'] = $BOL['bl_no'];
            if (count($Containers[$index]['split_bl_no_fk']) > 0) {
                $Containers[$index]['split_bl_no_fk'] = implode(",", $Containers[$index]['split_bl_no_fk']);
            } else {
                $Containers[$index]['split_bl_no_fk'] = null;
            }

            $index++;
        }

        //  'connecting_vessel' =>   $BOL['connecting_vessel'],
        //  'commodity' =>   $BOL['commodity'],



        try {
            DB::beginTransaction();
            DB::table('bill_of_ladings')->insert([
                'factory' => $BOL['factory'],
                'bl_no' => $BOL['bl_no'],
                'supplier' => $BOL['supplier'],
                'vessel' => $BOL['vessel'],
                'shipping_line' => $BOL['shipping_line'],
                'forwarder' => $BOL['forwarder'],
                'broker' => $BOL['broker'],
                'pol' => $BOL['pol'],
                'country' => $BOL['country'],
                'pod' => $BOL['pod'], //TRIAL
                'volume' => $BOL['volume'],
                'incoterm' => $BOL['incoterm'],
                'shipping_docs' => $shipping_docs,
                'processing_date' => $BOL['processing_date'],
                'estimated_time_arrival' => $BOL['estimated_time_arrival'],
                'estimated_time_departure' => $BOL['estimated_time_departure'],
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime()
            ]);
            DB::connection('endorsmentdarwin')->table('endorsements')->insert(
                [
                    'BlNo' => $BOL['bl_no'],
                    'status' => 0,
                ]
            );
            DB::table('bill_of_lading_invoices')->insert($IN_DATA);
            DB::table('bill_of_lading_commodities')->insert($COMMODITY_DATA);
            DB::table('containers')->insert($Containers);
            DB::commit();
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            $error_id = uniqid('error_code_' . date('Y_m_d') . '_');

            Log::error($error_id . ' - INSERTING RECORD'. json_encode( $request->input() ));

            Log::error($error_id . ' - ' . json_encode($e->getMessage()));


            throw new Exception($error_id);

            // something went wrong
        }
    }catch(Exception $e){

        Log::error($error_id . ' - INSERTING RECORD'. json_encode( $request->input() ));

        Log::error($error_id . ' - ' . json_encode($e->getMessage()));
        throw new Exception($e->getMessage());

    }


});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 *
 * import_new.js
 *
 */

Route::post('/bill_of_lading/search_bl_exist', function (Request $request) {

    return Bill_of_Lading::where('bl_no', '=', $request->input('bl_no'))->count();

});

Route::post('/bill_of_lading/edit_search_bl', function (Request $request) {

    $list_of_BOL = Bill_of_Lading::select(
            'factory',
            'bl_no',
            'supplier',
            'vessel',
            'shipping_line',
            'forwarder',
            'broker',
            'pol',
            'country',
            'pod',
            'volume',
            'incoterm',
            'shipping_docs',
            'processing_date',
            'estimated_time_departure',
            'estimated_time_arrival'
        )
        ->where('bl_no', '=', $request->input('bl_no'))
        ->get();

    foreach ($list_of_BOL as $row) {
        $docstime = explode(' ', $row['shipping_docs']);
        $row['shipping_docs'] = $docstime[0];
        $row['shipping_docs_time'] = @$docstime[1];

        $invoicedata = Bill_of_Lading_Invoice::select('invoice_number')
            ->where('bl_no_fk', '=', $row['bl_no'])->get();
        foreach ($invoicedata as $key => $value) {
            $invoices[] = $value['invoice_number'];
        }

        $commiditiesdata = Bill_of_Lading_Commodity::select('commodity')
            ->where('bl_no_fk', '=', $row['bl_no'])->get();
        foreach ($commiditiesdata as $key => $value) {
            $commodities[] = $value['commodity'];
        }

        $row['invoice_no'] = implode(", ", $invoices);
        $row['commodity'] = $commodities;

        $list_container = Container::select(
            'id',
            'container_number',
            'container_type',
            'quantity',
            'split_bl_no_fk'
        )
            ->where('bl_no_fk', '=', $row['bl_no'])
            ->get();
        if(count( $list_container) > 0){
            foreach ($list_container as $key => $value) {
                $containers[$key]['id'] = $value['id'];
                $containers[$key]['container_number'] = $value['container_number'];
                $containers[$key]['container_type'] = $value['container_type'];
                $containers[$key]['quantity'] = $value['quantity'];
                //$containers[$key]['split_bl_no_fk'] = $value['split_bl_no_fk'];
                if ($value['split_bl_no_fk'] != null) {
                    $containers[$key]['split_bl_no_fk'] = explode(",", $value['split_bl_no_fk']);
                } else {
                    $containers[$key]['split_bl_no_fk'] = [];
                }
                $containers[$key]['remove'] = false;
            }
        }else{
            $containers = [];
        }

        $row['edit_list_container'] = $containers;
    }
    return $list_of_BOL;
});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 *
 * import_new.js
 *
 */

Route::post('/bill_of_lading/split_save_qty', function (Request $request) {
    $id = $request->input('id');
    $qty = $request->input('qty');

    Container::where('id', $id)
        ->update([
            'quantity' => $qty,
        ]);
});

Route::post('/bill_of_lading/filter_split_checking', function (Request $request) {

    $date_request = $request->input('date_filter');
    $year = explode('-', $date_request)[0];
    $month = explode('-', $date_request)[1];

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

    return $bl_with_split;
});

Route::post('/bill_of_lading/edit_update', function (Request $request) {

    $BOL = $request->input('BOL');
    $BOL_update_id = $BOL['bl_no'];
    $shipping_docs = $BOL['shipping_docs'] . ' ' . $BOL['shipping_docs_time'];

    $invoiceNumber = explode(',', $BOL['invoice_no']);
    $commodities = $BOL['commodity'];
    foreach ($commodities as $key => $value) {
        $COMMODITY_DATA[] = array('bl_no_fk' => $BOL['bl_no'], 'commodity' => strtoupper($value));
    };

    $Containers = $BOL['edit_list_container'];
    $index = 0;

    foreach ($invoiceNumber as $key => $value) {
        $IN_DATA[] = array('bl_no_fk' => $BOL['bl_no'], 'invoice_number' => strtoupper($value));
    };
    // foreach( $Containers as $container){

    //     $Containers[$index]['bl_no_fk'] = $BOL['bl_no'];
    //     $index++;
    // }

    $exist_id = [];
    $i = 0;
    foreach ($Containers as $container) {
        if (array_key_exists('id', $container) && $container['id'] != null) {

            $exist_id[] = $container['id'];
        }

        if (array_key_exists('split_bl_no_fk', $container)) {

            if (count($container['split_bl_no_fk']) > 0) {

                foreach ($container['split_bl_no_fk'] as $row => $value) {
                    $list = [];
                    $bl_no = Container::select('split_bl_no_fk')
                        ->where('bl_no_fk', $value)
                        ->where('container_number', $container['container_number'])
                        ->whereNotNull('split_bl_no_fk')
                        ->get();

                    if (count($bl_no) > 0) {

                        foreach ($bl_no as $bl => $value1) {

                            $list = explode(',', $value1['split_bl_no_fk']);

                        }

                        if (!in_array($BOL_update_id, $list)) {
                            $list[] = strtoupper($BOL_update_id);
                        }

                        $list_split = trim(implode(",", $list));

                    } else {

                        $list[] = strtoupper($BOL_update_id);

                        $list_split = trim(implode(",", $list));

                    }

                    Container::where('bl_no_fk', '=', $value)
                        ->where('container_number', '=', $container['container_number'])
                        ->update([
                            'split_bl_no_fk' => $list_split,
                        ]);

                }

                $Containers[$i]['split_bl_no_fk'] = implode(",", $container['split_bl_no_fk']);
            } else {
                $Containers[$i]['split_bl_no_fk'] = null;
            }

        } else {
            $Containers[$i]['split_bl_no_fk'] = null;

        }

        $i++;

    }

    //GETTING Not Included in selected
    $notincluded = Container::whereNotIn('id', $exist_id)
        ->where('bl_no_fk', $BOL_update_id)
        ->get();
    $deleted_id = [];
    //Delete This Material
    if (count($notincluded) > 0) {
        foreach ($notincluded as $ids) {

            $deleted_id[] = $ids['id'];
        }
    }



    try {
        DB::beginTransaction();
        DB::table('bill_of_ladings')->
            where('bl_no', '=', $BOL_update_id)
            ->update([
                'factory' => $BOL['factory'],
                'bl_no' => $BOL['bl_no'],
                'supplier' => $BOL['supplier'],
                'vessel' => $BOL['vessel'],
                'shipping_line' => $BOL['shipping_line'],
                'forwarder' => $BOL['forwarder'],
                'broker' => $BOL['broker'],
                'pol' => $BOL['pol'],
                'country' => $BOL['country'],
                'pod' => $BOL['pod'], //TRIAL
                'volume' => $BOL['volume'],
                'incoterm' => $BOL['incoterm'],
                'shipping_docs' => $shipping_docs,
                'processing_date' => $BOL['processing_date'],
                'estimated_time_arrival' => $BOL['estimated_time_arrival'],
                'estimated_time_departure' => $BOL['estimated_time_departure'],
            ]);

        DB::table('bill_of_lading_invoices')->where('bl_no_fk', '=', $BOL_update_id)->delete();
        DB::table('bill_of_lading_commodities')->where('bl_no_fk', '=', $BOL_update_id)->delete();

        DB::table('bill_of_lading_invoices')->insert($IN_DATA);
        DB::table('bill_of_lading_commodities')->insert($COMMODITY_DATA);

        DB::table('containers')->whereIn('id', $deleted_id)
            ->where('bl_no_fk', $BOL_update_id)
            ->delete();

        foreach ($Containers as $container) {

            if ($container['id'] == null) {
                DB::table('containers')->insert(
                    [
                        'bl_no_fk' => $BOL_update_id,
                        'container_number' => $container['container_number'],
                        'container_type' => $container['container_type'],
                        'quantity' => $container['quantity'],
                        'split_bl_no_fk' => $container['split_bl_no_fk'],
                    ]
                );
            } else {

                DB::table('containers')->where('id', '=', $container['id'])
                    ->update([
                        'container_number' => $container['container_number'],
                        'container_type' => $container['container_type'],
                        'quantity' => $container['quantity'],
                        'split_bl_no_fk' => $container['split_bl_no_fk'],

                    ]);
            }

        }

        //
        DB::commit();
        // all good
    } catch (\Exception $e) {
        DB::rollback();
        return $e->getMessage();
        // something went wrong
    }

});

/*
 * ------------------------------
 * Called FROM
 * ------------------------------
 *
 * summary_tally.js
 *
 */

Route::post('/bill_of_lading/search_date_tally_breakdown', function (Request $request) {
    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
    $containers_tally = [];
    $date_request = $request->input('date_filter');
    $year = explode('-', $date_request)[0];
    $reference = $request->input('reference');
    $i = 0;

    switch ($reference) {
        case 'D':
            foreach ($factories as $factory) {

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->where('pull_out', '=', $date_request)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['name'] = $factory['factory_id'];
                $containers_tally[$i]['pullout'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->where('actual_discharge', '=', $date_request)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['discharged'] = $count;

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

                $count_irs = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->where('dismounted_date', '=', $date_request)
                    ->where('dismounted_cy', '=', 'IRS BACAO')
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $count_cez = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->where('dismounted_date', '=', $date_request)
                    ->whereIn('dismounted_cy', ['CEZ 1 PUTOL', 'CEZ 2 PUTOL'])
                    ->where('factory', $factory['factory_id'])
                    ->count();

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
                // $dismounted[$i]['name'] = $factory['factory_id'];
                // $dismounted[$i]['irs'] = $count_irs;
                // $dismounted[$i]['cez'] = $count_cez;
                // $dismounted[$i]['cy'] = $count_cy;
                $i++;

            }

            $summary = [];
            // $summary['dismounted'] = $dismounted;
            $summary['containers_tally'] = $containers_tally;

            break;
        case 'M':

            $month = explode('-', $date_request)[1];
            foreach ($factories as $factory) {

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereMonth('pull_out', '=', $month)
                    ->whereYear('pull_out', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['name'] = $factory['factory_id'];
                $containers_tally[$i]['pullout'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereMonth('actual_discharge', '=', $month)
                    ->whereYear('actual_discharge', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['discharged'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereMonth('unload', '=', $month)
                    ->whereYear('unload', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['unloaded'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereMonth('containers.actual_gatepass', '=', $month)
                    ->whereYear('containers.actual_gatepass', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['gatepass'] = $count;

                $count_irs = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereMonth('dismounted_date', '=', $month)
                    ->whereYear('dismounted_date', '=', $year)
                    ->where('dismounted_cy', '=', 'IRS BACAO')
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $count_cez = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereMonth('dismounted_date', '=', $month)
                    ->whereYear('dismounted_date', '=', $year)
                    ->whereIn('dismounted_cy', ['CEZ 1 PUTOL', 'CEZ 2 PUTOL'])
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $count_cy = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereMonth('dismounted_date', '=', $month)
                    ->whereYear('dismounted_date', '=', $year)
                //->whereNotIn('dismounted_cy',['CEZ 1 PUTOL', 'CEZ 2 PUTOL','IRS BACAO'])
                    ->whereNotIn('dismounted_cy', ['IRS BACAO'])
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['irs'] = $count_irs;
                $containers_tally[$i]['cy'] = $count_cy;
                // $dismounted[$i]['name'] = $factory['factory_id'];
                // $dismounted[$i]['irs'] = $count_irs;
                // $dismounted[$i]['cez'] = $count_cez;
                // $dismounted[$i]['cy'] = $count_cy;
                $i++;

            }

            $summary = [];
            // $summary['dismounted'] = $dismounted;
            $summary['containers_tally'] = $containers_tally;

            break;
        default:

            foreach ($factories as $factory) {

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereYear('pull_out', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['name'] = $factory['factory_id'];
                $containers_tally[$i]['pullout'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereYear('actual_discharge', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['discharged'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereYear('unload', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['unloaded'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereYear('containers.actual_gatepass', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['gatepass'] = $count;

                $count_irs = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereYear('dismounted_date', '=', $year)
                    ->where('dismounted_cy', '=', 'IRS BACAO')
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $count_cez = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereYear('dismounted_date', '=', $year)
                    ->whereIn('dismounted_cy', ['CEZ 1 PUTOL', 'CEZ 2 PUTOL'])
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $count_cy = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereYear('dismounted_date', '=', $year)
                //->whereNotIn('dismounted_cy',['CEZ 1 PUTOL', 'CEZ 2 PUTOL','IRS BACAO'])
                    ->whereNotIn('dismounted_cy', ['IRS BACAO'])
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $containers_tally[$i]['irs'] = $count_irs;
                $containers_tally[$i]['cy'] = $count_cy;
                // $dismounted[$i]['name'] = $factory['factory_id'];
                // $dismounted[$i]['irs'] = $count_irs;
                // $dismounted[$i]['cez'] = $count_cez;
                // $dismounted[$i]['cy'] = $count_cy;
                $i++;

            }

            $summary = [];
            // $summary['dismounted'] = $dismounted;
            $summary['containers_tally'] = $containers_tally;

    }

    return $summary;

});

//API FOR GETTING DETAILS FROM BEYOND FREE TIME PER DAY

Route::post('/bill_of_lading/get_beyond_free_time_oer_day_details', function (Request $request) {

    $factory = $request->input('factory');
    $category = $request->input('category');
    $all = $request->input('all');
    $date_request = $request->input('date_filter');
    $select = ['bl_no_fk','factory','reason_of_delay_delivery','return_date','actual_gatepass','pull_out','dismounted_date','dismounted_cy','unload', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];

    $list_of_BOL = Container::select($select)
        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk');

    if (!$all) {
        $list_of_BOL = $list_of_BOL->where('factory', $factory);

    }

    $six_days_before = date('Y-m-d', strtotime($date_request . ' -6 days'));
    $eleven_days_before = date('Y-m-d', strtotime($date_request . ' -10 days'));

    switch ($category) {
        case "BFT_container":
            $list_of_BOL = $list_of_BOL
                ->whereQuantity(1)
                ->where('actual_discharge', '<=', $six_days_before)
                ->where(function ($query) use ($date_request) {
                    $query->whereNull('pull_out');
                    $query->orWhere('pull_out', '>', $date_request);
                })
                ->get();
            break;
        case "BFT_SIX":
            $list_of_BOL = $list_of_BOL
                ->whereQuantity(1)
                ->where('actual_discharge', '<=', $six_days_before)
                ->where('actual_discharge', '>', $eleven_days_before)
                ->where(function ($query) use ($date_request) {
                    $query->whereNull('pull_out');
                    $query->orWhere('pull_out', '>', $date_request);
                })
                ->get();
            break;
        case "BFT_ELEVEN":
            $list_of_BOL = $list_of_BOL
                ->whereQuantity(1)
                ->where('actual_discharge', '<=', $eleven_days_before)

                ->where(function ($query) use ($date_request) {
                    $query->whereNull('pull_out');
                    $query->orWhere('pull_out', '>', $date_request);
                })
                ->get();
            break;
        default:
            $year = $request->input('date_year');
            $n = date('m', strtotime($request->input('date_month')));
            $i = $request->input('date_day');

            $n = (strlen($n) > 1) ? $n : '0' . $n;
            $i = (strlen($i) > 1) ? $i : '0' . $i;

            $date_request = $year . '-' . $n . '-' . $i;
            $six_days_before = date('Y-m-d', strtotime($date_request . ' -6 days'));

            $list_of_BOL = $list_of_BOL
                ->whereQuantity(1)
                ->where('actual_discharge', '<=', $six_days_before)
                ->where(function ($query) use ($date_request) {
                    $query->whereNull('pull_out');
                    $query->orWhere('pull_out', '>', $date_request);
                })

                ->get();

    }

    if (count($list_of_BOL) > 0) {
        foreach ($list_of_BOL as $row) {
            $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();
            $row['delays'] = Container::select('reason_of_delay_delivery')->where('bl_no_fk', '=', $row['bl_no_fk'])->where('container_number', $row['container_number'])->whereNotNull('reason_of_delay_delivery')->get();

            $com = [];
            foreach ($row['commodities'] as $cm) {
                $com[] = $cm['commodity'];
            }

            if (count($row['delays']) > 0) {
                foreach ($row['delays'] as $dl) {
                    $row['reason_of_delay_delivery'] = $dl['reason_of_delay_delivery'];
                }
            }

            $row['commodity'] = implode(',', $com);

            $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

            $inv = [];
            foreach ($row['invoice_numbers'] as $in) {
                $inv[] = $in['invoice_number'];
            }

            $row['invoices'] = implode(',', $inv);

        }
    }

    return $list_of_BOL;

});

Route::post('/bill_of_lading/get_chart_details',function(Request $request){

    $factory = $request->input('factory');
    $container_type = $request->input('container_type');
    $factory_all = $request->input('factory_all');
    $container_all = $request->input('container_all');

    $select = ['bl_no_fk', 'container_number','actual_gatepass', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];
    $list_of_BOL = Container::select($select)
        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
        ->where('quantity', '=', 1);

    if (!$factory_all) {
        $list_of_BOL = $list_of_BOL->where('factory', $factory);
    }
    if (!$container_all) {
        $list_of_BOL = $list_of_BOL->where('container_type', $container_type);
    }

    $date_request = date('Y-m-d');



    $list_of_BOL = $list_of_BOL->where('actual_discharge', '<=', $date_request)
                    ->whereNull('pull_out')
                    // ->whereNull('dismounted_cy')
                    // ->where(function ($query) use ($date_request) {
                    //     $query->whereNull('pull_out');
                    //     $query->orWhere('pull_out', '>', $date_request);
                    // })
                    ->get();
    if (count($list_of_BOL) > 0) {

        foreach ($list_of_BOL as $row) {
            $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

            $com = [];
            foreach ($row['commodities'] as $cm) {
                $com[] = $cm['commodity'];
            }
            $row['commodity'] = implode(',', $com);

            $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

            $inv = [];
            foreach ($row['invoice_numbers'] as $in) {
                $inv[] = $in['invoice_number'];
            }

            $row['invoices'] = implode(',', $inv);
        }

    }

    return  $list_of_BOL;



});

// API FOR GETTING DETAILS FROM SUMMARY
Route::post('/bill_of_lading/get_summary_tall_details', function (Request $request) {

    $factory = $request->input('factory');
    $category = $request->input('category');
    $all = $request->input('all');
    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');

    $select = ['factory','actual_process','actual_gatepass','validity_storage','validity_demurrage','revalidity_storage','revalidity_demurrage','pull_out','dismounted_cy','dismounted_date','return_date','unload','forwarder','bl_no_fk', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type','quantity'];
    $list_of_BOL = Container::select($select)
        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
        ->where('quantity', '=', 1);

    $list_of_BOL2 = Container::select($select)
        ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
        ->where('quantity', '=', 1);
    if (!$all) {
        $list_of_BOL = $list_of_BOL->where('factory', $factory);
        $list_of_BOL2 = $list_of_BOL2->where('factory', $factory);
    }





    switch ($category) {
        case 'NORTH':
            switch ($reference) {
                case 'D':
                    $list_of_BOL = $list_of_BOL
                        ->where('actual_discharge', '<=', $date_request);
                        // ->where(function ($query) {
                        //     $query->whereNull('pull_out');
                        //     $query->orWhereNull('unload');
                        //    // $query->orWhere('pull_out', '>', $end);
                        // });
                    break;
                case 'M':
                    $month = explode('-', $dateMonth)[1];
                    $year = explode('-', $dateMonth)[0];
                    $lastDayThisMonth = date("Y-m-t",strtotime($year ."-".$month));

                    $list_of_BOL = $list_of_BOL
                       ->where('actual_discharge', '<=', $lastDayThisMonth);
                    //    ->where(function ($query) {
                    //         $query->whereNull('pull_out');
                    //         $query->orWhereNull('unload');
                    //         // $query->orWhere('pull_out', '>', $end);
                    //     });
                    break;
                case 'Y':
                    $year = explode('-', $dateYear)[0];
                    $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));

                    $list_of_BOL = $list_of_BOL
                                ->where('actual_discharge', '<=', $lastDayThisMonth);
                                // ->where(function ($query) {
                                //     $query->whereNull('pull_out');
                                //     $query->orWhereNull('unload');
                                //    // $query->orWhere('pull_out', '>', $end);
                                // });
                    break;
                default:
                 $list_of_BOL = $list_of_BOL
                                ->whereBetween('actual_discharge', [$start, $end]);
                                // ->where(function ($query) {
                                //     $query->whereNull('pull_out');
                                //     $query->orWhereNull('unload');
                                //    // $query->orWhere('pull_out', '>', $end);
                                // });
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
                            // ->where(function ($query) {
                            //     $query->whereNull('pull_out');
                            //     $query->orWhereNull('unload');
                            //    // $query->orWhere('pull_out', '>', $end);
                            // });
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-".$month));

                        $list_of_BOL = $list_of_BOL
                        ->where('actual_discharge', '<=', $lastDayThisMonth);
                        // ->where(function ($query) {
                        //     $query->whereNull('pull_out');
                        //     $query->orWhereNull('unload');
                        //    // $query->orWhere('pull_out', '>', $end);
                        // });
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));

                        $list_of_BOL = $list_of_BOL
                                    ->where('actual_discharge', '<=', $lastDayThisMonth);
                                    // ->where(function ($query) {
                                    //     $query->whereNull('pull_out');
                                    //     $query->orWhereNull('unload');
                                    //    // $query->orWhere('pull_out', '>', $end);
                                    // });
                        break;
                    default:
                    $list_of_BOL = $list_of_BOL
                                    ->whereBetween('actual_discharge', [$start, $end]);
                                    // ->where(function ($query) {
                                    //     $query->whereNull('pull_out');
                                    //     $query->orWhereNull('unload');
                                    //    // $query->orWhere('pull_out', '>', $end);
                                    // });
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
            /*
 $countIRS = Container::select('container_number')
                            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotIn('pod',['TBA'])
                            ->whereYear('dismounted_date', '=', $year)
                            ->whereFactory($factory['factory_id'])
                            ->where('dismounted_cy','IRS BACAO')
                            ->whereNull('unload')
                            ->count();

                $summary[$i]['irs'] = $countIRS;
            */
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



             $list_of_BOL = $list_of_BOL
				->whereNotIn('connecting_vessel',['T.B.A.'])
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



            $list_of_BOL = $list_of_BOL->whereNull('unload')
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



            $list_of_BOL = $list_of_BOL->whereNull('unload')
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

/*
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



            $list_of_BOL = $list_of_BOL->whereNull('unload')
                //->where('dismounted_cy','IRS BACAO')
                ->where('dismounted_cy', 'IRS BACAO')
                ->whereNotNull('pull_out')
                ->get();
            break;
*/

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
                        $query->whereNotNull('pull_out');
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

                    ->where(function ($query) use ($month,$year) {
                        $query->whereMonth('actual_discharge', '=', $month);
                        $query->whereYear('actual_discharge', '=', $year);
                        $query->whereNull('dismounted_cy');
                       // $query->orWhere('pull_out', '>', $lastDayThisMonth);
                    })
                    ->whereNull('pull_out')
                    ->get();

                    $list_of_BOL2 = $list_of_BOL2->where(function ($query) use ( $month,$year) {
                        $query->whereNotNull('pull_out');
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
                        //$query->orWhere('pull_out', '>', $lastDayThisMonth);
                    })

                    ->get();

                    $list_of_BOL2 = $list_of_BOL2->where(function ($query) use ($date_request,$lastDayThisMonth) {
                        $query->whereNotNull('pull_out');
                        $query->WhereNull('unload');
                        //$query->where('dismounted_cy','IRS BACAO');
                        $query->whereIn('dismounted_cy', ['IRS BACAO','CEZ1','CEZ2'])
                            ->where('dismounted_date', '<=', $lastDayThisMonth);
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
                        //$query->orWhere('pull_out', '>', $end);
                    })

                    ->get();

                    $list_of_BOL2 = $list_of_BOL2->where(function ($query) use ($start,$end) {
                        $query->whereNotNull('pull_out');
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
                        ->whereBetween('actual_discharge', [$start, $end])
                        ->where(function ($query) use ($five_days_after) {
                            $query->whereNull('pull_out');
                            $query->orWhere('pull_out', '>', $five_days_after);
                    })
                    ->get();
            }



    }
    if (count($list_of_BOL) > 0) {
        foreach ($list_of_BOL as $row) {
            $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

            $com = [];
            foreach ($row['commodities'] as $cm) {
                $com[] = $cm['commodity'];
            }
            $row['commodity'] = implode(',', $com);

            $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

            $inv = [];
            foreach ($row['invoice_numbers'] as $in) {
                $inv[] = $in['invoice_number'];
            }

            $row['invoices'] = implode(',', $inv);
        }
    }

    return $list_of_BOL;

});
Route::post('/bill_of_lading/search_date_tally_summary_range', function (Request $request) {

    $start = $request->input('range_start');
    $end = $request->input('range_end');

    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
    //$date_request = date('Y-m-d');
    //$date_request = $request->input('date_filter');
    $i = 0;
    $summary = [];
    foreach ($factories as $factory) {
        $summary[$i]['name'] = $factory['factory_id'];

        $countNorth = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereBetween('actual_discharge', [$start, $end])
            ->where('factory', $factory['factory_id'])
            ->whereNull('pull_out')

            ->where('pod', 'NORTH')
            ->count();

        $summary[$i]['north'] = $countNorth;

        $countSouth = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereBetween('actual_discharge', [$start, $end])
            ->where('factory', $factory['factory_id'])
            ->whereNull('pull_out')
            ->where('pod', 'SOUTH')
            ->count();

        $summary[$i]['south'] = $countSouth;
        $summary[$i]['at_port'] = $countSouth + $countNorth;

        $countIRS = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->whereQuantity(1)
                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                    ->whereNotIn('pod',['TBA'])
                    ->whereBetween('dismounted_date', [$start, $end])
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
                ->whereBetween('dismounted_date', [$start, $end])
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
                ->whereBetween('dismounted_date', [$start, $end])
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

        $countFactory = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereNull('dismounted_cy')
            ->whereBetween('pull_out', [$start, $end])
            ->where('factory', $factory['factory_id'])
            ->count();

        $summary[$i]['delivery_factory'] = $countFactory;

        $count_irs = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereNotNull('pull_out')
            ->whereBetween('dismounted_date', [$start, $end])
            ->where('factory', $factory['factory_id'])
            ->where('dismounted_cy', 'IRS BACAO')
            ->count();

        $summary[$i]['delivery_irs'] = $count_irs;

        $count_cez1 = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereNotNull('pull_out')
            ->whereBetween('dismounted_date', [$start, $end])
            ->where('factory', $factory['factory_id'])
            ->where('dismounted_cy', 'CEZ1')
            ->count();

        $summary[$i]['delivery_cez1'] = $count_cez1;

        $count_cez2 = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereNotNull('pull_out')
            ->whereBetween('dismounted_date', [$start, $end])
            ->where('factory', $factory['factory_id'])
            ->where('dismounted_cy', 'CEZ2')
            ->count();

        $summary[$i]['delivery_cez2'] = $count_cez2;

        $count = bill_of_lading::whereBetween('actual_berthing_date', [$start, $end])
            ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
            ->where('quantity', '=', 1)
            ->where('factory', $factory['factory_id'])
            ->count();
        $summary[$i]['berthed'] = $count;

        $count = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereBetween('containers.actual_discharge', [$start, $end])
            ->where('factory', $factory['factory_id'])
            ->count();

        $summary[$i]['discharge'] = $count;

        $count = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereBetween('containers.actual_gatepass', [$start, $end])
            ->where('factory', $factory['factory_id'])
            ->count();

        $summary[$i]['gatepass'] = $count;

        $count = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereNull('dismounted_date')
            ->whereBetween('unload', [$start, $end])
            ->where('factory', $factory['factory_id'])
            ->count();

        $summary[$i]['direct_unloading'] = $count;

        $count = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereNotNull('dismounted_date')
            ->whereNotIn('dismounted_cy', ['IRS BACAO'])
            ->whereBetween('unload', [$start, $end])
            ->where('factory', $factory['factory_id'])
            ->count();

        $summary[$i]['unloading_with_chassis'] = $count;

        $count = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereNotNull('dismounted_date')
            ->where('dismounted_cy', 'IRS BACAO')
            ->whereBetween('unload', [$start, $end])
            ->where('factory', $factory['factory_id'])
            ->count();

        $summary[$i]['unloading_irs'] = $count;

        $five_days_after = date('Y-m-d', strtotime($end . ' +5 days'));

        $countBeyond = Container::select('container_number')
            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
            ->where('quantity', '=', 1)
            ->whereBetween('actual_discharge', [$start, $end])
            ->where(function ($query) use ($five_days_after) {
                $query->whereNull('pull_out');
                $query->orWhere('pull_out', '>', $five_days_after);
            })
            ->where('factory', $factory['factory_id'])
            ->count();

        $summary[$i]['beyond_5_days'] = $countBeyond;

        $i++;
    }
    return $summary;

});
Route::post('/bill_of_lading/search_date_tally_summary', function (Request $request) {

    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();
    //$date_request = date('Y-m-d');
    $date_request = $request->input('date_filter');
    $reference = $request->input('reference');
    $year = explode('-', $date_request)[0];

    $i = 0;
    $summary = [];

    switch ($reference) {
        case 'D':
            foreach ($factories as $factory) {
                $summary[$i]['name'] = $factory['factory_id'];

                $countNorth = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->where('actual_discharge', '<=', $date_request)
                    ->where('factory', $factory['factory_id'])
                    ->whereNull('pull_out')

                    ->where('pod', 'NORTH')
                    ->count();

                $summary[$i]['north'] = $countNorth;

                $countSouth = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->where('actual_discharge', '<=', $date_request)
                    ->where('factory', $factory['factory_id'])
                    ->whereNull('pull_out')
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

                $countFactory = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNull('dismounted_cy')
                    ->where('pull_out', $date_request) //change by mam shaira from unload
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
            break;
        case 'M':
            $month = explode('-', $date_request)[1];
            $lastDayThisMonth = date("Y-m-t",strtotime($year ."-".$month));

            foreach ($factories as $factory) {
                $summary[$i]['name'] = $factory['factory_id'];

                $countNorth = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->where('actual_discharge', '<=', $lastDayThisMonth)
                    ->where('factory', $factory['factory_id'])
                    ->whereNull('pull_out')
                    ->where('pod', 'NORTH')
                    ->count();

                $summary[$i]['north'] = $countNorth;

                $countSouth = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->where('actual_discharge', '<=', $lastDayThisMonth)
                    ->where('factory', $factory['factory_id'])
                    ->whereNull('pull_out')
                    ->where('pod', 'SOUTH')
                    ->count();

                $summary[$i]['south'] = $countSouth;
                $summary[$i]['at_port'] = $countSouth + $countNorth;

                $countIRS = Container::select('container_number')
                            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotIn('pod',['TBA'])
                            ->whereMonth('dismounted_date', '=', $month)
                            ->whereYear('dismounted_date', '=', $year)
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
                        ->whereMonth('dismounted_date', '=', $month)
                        ->whereYear('dismounted_date', '=', $year)
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
                        ->whereMonth('dismounted_date', '=', $month)
                        ->whereYear('dismounted_date', '=', $year)
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
                //         ->whereMonth('dismounted_date', '=', $month)
                //         ->whereYear('dismounted_date', '=', $year)
                //         ->count();

                // $summary[$i]['at_chassi'] = $countChassi;


                $countFactory = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNull('dismounted_cy')
                    //->where('pull_out', $date_request) //change by mam shaira from unload
                    ->whereMonth('pull_out', '=', $month)
                    ->whereYear('pull_out', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['delivery_factory'] = $countFactory;

                $count_irs = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNotNull('pull_out')
                    ->whereMonth('dismounted_date', '=', $month)
                    ->whereYear('dismounted_date', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->where('dismounted_cy', 'IRS BACAO')
                    ->count();

                $summary[$i]['delivery_irs'] = $count_irs;

                $count_cez1 = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNotNull('pull_out')
                    ->whereMonth('dismounted_date', '=', $month)
                    ->whereYear('dismounted_date', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->where('dismounted_cy', 'CEZ1')
                    ->count();

                $summary[$i]['delivery_cez1'] = $count_cez1;

                $count_cez2 = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNotNull('pull_out')
                    ->whereMonth('dismounted_date', '=', $month)
                    ->whereYear('dismounted_date', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->where('dismounted_cy', 'CEZ2')
                    ->count();

                $summary[$i]['delivery_cez2'] = $count_cez2;

                $count = bill_of_lading::
                     whereMonth('actual_berthing_date', '=', $month)
                    ->whereYear('actual_berthing_date', '=', $year)
                    ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
                    ->where('quantity', '=', 1)
                    ->where('factory', $factory['factory_id'])
                    ->count();
                $summary[$i]['berthed'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    //->where('containers.actual_discharge', '=', $date_request)
                    ->whereMonth('containers.actual_discharge', '=', $month)
                    ->whereYear('containers.actual_discharge', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['discharge'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    //->where('containers.actual_gatepass', '=', $date_request)
                    ->whereMonth('containers.actual_gatepass', '=', $month)
                    ->whereYear('containers.actual_gatepass', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['gatepass'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNull('dismounted_date')
                    ->whereMonth('unload', '=', $month)
                    ->whereYear('unload', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['direct_unloading'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNotNull('dismounted_date')
                    ->whereNotIn('dismounted_cy', ['IRS BACAO'])
                    ->whereMonth('unload', '=', $month)
                    ->whereYear('unload', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['unloading_with_chassis'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNotNull('dismounted_date')
                    ->where('dismounted_cy', 'IRS BACAO')
                    ->whereMonth('unload', '=', $month)
                    ->whereYear('unload', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['unloading_irs'] = $count;

                $five_days_before = date('Y-m-d', strtotime($lastDayThisMonth . ' -6 days'));

                $countBeyond = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->where('actual_discharge', '<=', $five_days_before)
                    ->where(function ($query) use ($lastDayThisMonth) {
                        $query->whereNull('pull_out');
                        $query->orWhere('pull_out', '>', $lastDayThisMonth);
                    })
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['beyond_5_days'] = $countBeyond;

                $i++;
            }
            break;
        default:
            $lastDayThisMonth = date("Y-m-t",strtotime($year ."-12"));

            foreach ($factories as $factory) {
                $summary[$i]['name'] = $factory['factory_id'];

                $countNorth = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->where('actual_discharge', '<=', $lastDayThisMonth)
                    ->where('factory', $factory['factory_id'])
                    ->whereNull('pull_out')
                    ->where('pod', 'NORTH')
                    ->count();

                $summary[$i]['north'] = $countNorth;

                $countSouth = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->where('actual_discharge', '<=', $lastDayThisMonth)
                    ->where('factory', $factory['factory_id'])
                    ->whereNull('pull_out')
                    ->where('pod', 'SOUTH')
                    ->count();

                $summary[$i]['south'] = $countSouth;
                $summary[$i]['at_port'] = $countSouth + $countNorth;

                $countIRS = Container::select('container_number')
                            ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotIn('pod',['TBA'])
                            ->whereYear('dismounted_date', '=', $year)
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
                        ->whereYear('dismounted_date', '=', $year)
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
                        ->whereYear('dismounted_date', '=', $year)
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
                //         ->whereYear('dismounted_date', '=', $year)
                //         ->count();

                // $summary[$i]['at_chassi'] = $countChassi;

                $countFactory = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNull('dismounted_cy')
                    //->where('pull_out', $date_request) //change by mam shaira from unload
                    ->whereYear('pull_out', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['delivery_factory'] = $countFactory;

                $count_irs = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNotNull('pull_out')
                    ->whereYear('dismounted_date', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->where('dismounted_cy', 'IRS BACAO')
                    ->count();

                $summary[$i]['delivery_irs'] = $count_irs;

                $count_cez1 = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNotNull('pull_out')
                    ->whereYear('dismounted_date', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->where('dismounted_cy', 'CEZ1')
                    ->count();

                $summary[$i]['delivery_cez1'] = $count_cez1;

                $count_cez2 = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNotNull('pull_out')
                    ->whereYear('dismounted_date', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->where('dismounted_cy', 'CEZ2')
                    ->count();

                $summary[$i]['delivery_cez2'] = $count_cez2;

                // $count_chassi = Container::select('container_number')
                //     ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                //     ->where('quantity', '=', 1)
                //     ->whereYear('dismounted_date', '=', $year)
                // //->whereNotIn('dismounted_cy',['CEZ 1 PUTOL', 'CEZ 2 PUTOL','IRS BACAO'])
                //     ->whereNotIn('dismounted_cy', ['IRS BACAO'])
                //     ->where('factory', $factory['factory_id'])
                //     ->count();
                // $summary[$i]['with_chassi'] = $count_chassi;

                $count = bill_of_lading::
                    whereYear('actual_berthing_date', '=', $year)
                    ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
                    ->where('quantity', '=', 1)
                    ->where('factory', $factory['factory_id'])
                    ->count();
                $summary[$i]['berthed'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    //->where('containers.actual_discharge', '=', $date_request)
                    ->whereYear('containers.actual_discharge', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['discharge'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    //->where('containers.actual_gatepass', '=', $date_request)
                    ->whereYear('containers.actual_gatepass', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['gatepass'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNull('dismounted_date')
                    ->whereYear('unload', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['direct_unloading'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNotNull('dismounted_date')
                    ->whereNotIn('dismounted_cy', ['IRS BACAO'])
                    ->whereYear('unload', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['unloading_with_chassis'] = $count;

                $count = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereNotNull('dismounted_date')
                    ->where('dismounted_cy', 'IRS BACAO')
                    ->whereYear('unload', '=', $year)
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['unloading_irs'] = $count;

                $five_days_before = date('Y-m-d', strtotime($lastDayThisMonth . ' -6 days'));

                $countBeyond = Container::select('container_number')
                    ->join('bill_of_ladings', 'bill_of_ladings.bl_no', 'containers.bl_no_fk')
                    ->where('quantity', '=', 1)
                    ->whereYear('actual_discharge',$year)
                    ->where(function ($query) use ($lastDayThisMonth) {
                        $query->whereNull('pull_out');
                        $query->orWhere('pull_out', '>', $lastDayThisMonth);
                    })
                    ->where('factory', $factory['factory_id'])
                    ->count();

                $summary[$i]['beyond_5_days'] = $countBeyond;

                $i++;
            }
    }

    return $summary;

});


Route::post('/bill_of_lading/search_date_beyond_freetime_per_day', function (Request $request) {

    $date_request = $request->input('date_filter');
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

    return $reports;
});
Route::post('/port_charges', function (Request $request) {

    $days = $request->input('days');

    if ($days >= 6 && $days < 10) {
        $price = 1443.90;
    }
    if ($days > 10) {
        $price = 100000;
    }

});
    // ==============================
    // ADDITIONAL REQUEST FEBRUARY
    // ==============================

Route::post('/bill_of_lading/incoming_vessels',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $as_of_now = $request->input('as_of_now');

    $i = 0;
    $list_distinct = [];
    $list_bl_volume = [];




    foreach ($factories as $factory) {
        $list_distinct[$i]['name'] = $factory['factory_id'];
        $list_bl_volume[$i]['name'] = $factory['factory_id'];


        $QueryNorth =  bill_of_lading::distinct()
                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotNull('connecting_vessel')
                            ->whereNull('actual_time_arrival')
                            ->whereFactory($factory['factory_id']);
        $QuerySouth =  bill_of_lading::distinct()
                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotNull('connecting_vessel')
                            ->whereNull('actual_time_arrival')
                            ->whereFactory($factory['factory_id']);
            if(!$as_of_now){
                switch ($reference) {
                    case 'D':
                        $QueryNorth->where('estimated_time_arrival',$date_request);
                        $QuerySouth->where('estimated_time_arrival',$date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];

                        $QueryNorth->whereMonth('estimated_time_arrival',$month);
                        $QueryNorth->whereYear('estimated_time_arrival',$year);
                        $QuerySouth->whereMonth('estimated_time_arrival',$month);
                        $QuerySouth->whereYear('estimated_time_arrival',$year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $QueryNorth->whereYear('estimated_time_arrival',$year);
                        $QuerySouth->whereYear('estimated_time_arrival',$year);
                        break;
                    default:
                        $QueryNorth->whereBetween('estimated_time_arrival', [$start, $end]);
                        $QuerySouth->whereBetween('estimated_time_arrival', [$start, $end]);
                }
            }else{
                $QueryNorth->whereNotNull('estimated_time_arrival');
            $QuerySouth->whereNotNull('estimated_time_arrival');
            }

        $list_distinct[$i]['north'] =  $QueryNorth->wherePod('NORTH')->count('connecting_vessel');



        // --------------------------------------------------------------



        $list_distinct[$i]['south'] =  $QuerySouth->wherePod('SOUTH')->count('connecting_vessel');


        // ============================================================


        $list_distinct[$i]['total'] =  $list_distinct[$i]['north'] + $list_distinct[$i]['south'];



        $north_bl_volume_query = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotNull('connecting_vessel')
                                ->whereNull('actual_time_arrival')
                                ->whereFactory($factory['factory_id']);
        $south_bl_volume_query = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotNull('connecting_vessel')
                                ->whereNull('actual_time_arrival')
                                ->whereFactory($factory['factory_id']);
        if(!$as_of_now){
            switch ($reference) {
                case 'D':
                    $north_bl_volume_query->where('estimated_time_arrival',$date_request);
                    $south_bl_volume_query->where('estimated_time_arrival',$date_request);
                    break;

                case 'M':
                    $month = explode('-', $dateMonth)[1];
                    $year = explode('-', $dateMonth)[0];


                    $north_bl_volume_query->whereMonth('estimated_time_arrival',$month);
                    $north_bl_volume_query->whereYear('estimated_time_arrival',$year);
                    $south_bl_volume_query->whereMonth('estimated_time_arrival',$month);
                    $south_bl_volume_query->whereYear('estimated_time_arrival',$year);
                    break;
                case 'Y':
                    $year = explode('-', $dateYear)[0];
                    $north_bl_volume_query->whereYear('estimated_time_arrival',$year);
                    $south_bl_volume_query->whereYear('estimated_time_arrival',$year);
                    break;
                default:
                    $north_bl_volume_query->whereBetween('estimated_time_arrival', [$start, $end]);
                    $south_bl_volume_query->whereBetween('estimated_time_arrival', [$start, $end]);
            }
        }else{
            $north_bl_volume_query->whereNotNull('estimated_time_arrival');
            $south_bl_volume_query->whereNotNull('estimated_time_arrival');
        }


        $list_bl_volume[$i]['north_bl'] = $north_bl_volume_query->wherePod('NORTH')->count('bl_no');

        $bl_no = [];
        foreach( $north_bl_volume_query->wherePod('NORTH')->get() as $row ){
            $bl_no[]  = $row['bl_no'];
        }

        $list_bl_volume[$i]['north_volume'] = Container::whereQuantity(1)
                        ->whereIn('bl_no_fk',$bl_no)
                        ->count();


        $list_bl_volume[$i]['south_bl'] =  $south_bl_volume_query->wherePod('SOUTH')->count('bl_no');

        $bl_no = [];
        foreach( $south_bl_volume_query->wherePod('SOUTH')->get() as $row ){
            $bl_no[]  = $row['bl_no'];
        }

        $list_bl_volume[$i]['south_volume'] = Container::whereQuantity(1)
                                        ->whereIn('bl_no_fk',$bl_no)
                                        ->count();



        $list_bl_volume[$i]['bl_total'] = $list_bl_volume[$i]['south_bl'] +  $list_bl_volume[$i]['north_bl'];
        $list_bl_volume[$i]['volume_total'] = $list_bl_volume[$i]['south_volume'] +  $list_bl_volume[$i]['north_volume'];


        $i++;
    }

    return $list = [$list_distinct,$list_bl_volume];

});



Route::post('/bill_of_lading/incoming_vessels_details',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $category = $request->input('category');
    $factory =  $request->input('factory');
    $factory_all = $request->input('factory_all');
    $pod = $request->input('pod');
    $pod_all = $request->input('pod_all');
    $as_of_now = $request->input('as_of_now');

   if($category == 'DISTINCT' ){
        $Query =  bill_of_lading::distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->whereNotIn('connecting_vessel',['T.B.A.','TBA'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereNotNull('connecting_vessel')
                                ->whereNull('actual_time_arrival');
   }else{
        $select = ['bl_no_fk', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];
        $Query = Container::select($select)
                            ->join('bill_of_ladings','containers.bl_no_fk','bill_of_ladings.bl_no')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotIn('pod',['TBA'])
                            ->whereNotNull('connecting_vessel')
                            ->whereNull('actual_time_arrival');
   }
   if(!$as_of_now){
    switch ($reference) {
            case 'D':
                $Query->where('estimated_time_arrival',$date_request);
                break;
            case 'M':
                $month = explode('-', $dateMonth)[1];
                $year = explode('-', $dateMonth)[0];
                $Query->whereMonth('estimated_time_arrival',$month);
                $Query->whereYear('estimated_time_arrival',$year);
                break;
            case 'Y':
                $year = explode('-', $dateYear)[0];
                $Query->whereYear('estimated_time_arrival',$year);
                break;
            default:
                $Query->whereBetween('estimated_time_arrival', [$start, $end]);
        }
    }else{
        $Query->whereNotNull('estimated_time_arrival');
    }


    if (!$factory_all) {
        $Query->where('factory', $factory);
    }

    if (!$pod_all) {
        $Query->wherePod($pod);
    }



    if( $category == 'DISTINCT' ){
        return $Query->select('connecting_vessel')->get();

    }else{
        $list_of_BOL = $Query->get();
        if (count( $list_of_BOL) > 0) {

            foreach ( $list_of_BOL as $row) {


                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodity'] = implode(',', $com);

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $inv = [];
                foreach ($row['invoice_numbers'] as $in) {
                    $inv[] = $in['invoice_number'];
                }

                $row['invoices'] = implode(',', $inv);
            }

        }
        return  $list_of_BOL;
    }



});


Route::post('/bill_of_lading/onboard_shipment',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $as_of_now = $request->input('as_of_now');

    $i = 0;


    $list_bl_volume = [];




    foreach ($factories as $factory) {

        $list_bl_volume[$i]['name'] = $factory['factory_id'];

            $Querybl_north = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->wherePod('NORTH')
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereNull('actual_discharge')
                                ->whereFactory($factory['factory_id']);

            $Querybl_north_volume = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                    ->whereQuantity(1)
                                    ->wherePod('NORTH')
                                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                                    ->whereNotIn('pod',['TBA'])
                                    ->whereNull('actual_discharge')
                                    ->whereFactory($factory['factory_id']);

            $Querybl_south = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->wherePod('SOUTH')
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereNull('actual_discharge')
                                ->whereFactory($factory['factory_id']);

            $Querybl_south_volume = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNull('actual_discharge')
                                        ->whereFactory($factory['factory_id']);
            if(!$as_of_now){
                switch ($reference) {
                    case 'D':
                        $Querybl_north->where('estimated_time_departure',$date_request);
                        $Querybl_north_volume->where('estimated_time_departure',$date_request);
                        $Querybl_south->where('estimated_time_departure',$date_request);
                        $Querybl_south_volume->where('estimated_time_departure',$date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];

                        $Querybl_north->whereMonth('estimated_time_departure',$month);
                        $Querybl_north->whereYear('estimated_time_departure',$year);

                        $Querybl_north_volume->whereMonth('estimated_time_departure',$month);
                        $Querybl_north_volume->whereYear('estimated_time_departure',$year);

                        $Querybl_south->whereMonth('estimated_time_departure',$month);
                        $Querybl_south->whereYear('estimated_time_departure',$year);

                        $Querybl_south_volume->whereMonth('estimated_time_departure',$month);
                        $Querybl_south_volume->whereYear('estimated_time_departure',$year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $Querybl_north->whereYear('estimated_time_departure',$year);
                        $Querybl_north_volume->whereYear('estimated_time_departure',$year);
                        $Querybl_south->whereYear('estimated_time_departure',$year);
                        $Querybl_south_volume->whereYear('estimated_time_departure',$year);
                        break;
                    default:
                        $Querybl_north->whereBetween('estimated_time_departure', [$start, $end]);
                        $Querybl_north_volume->whereBetween('estimated_time_departure', [$start, $end]);
                        $Querybl_south->whereBetween('estimated_time_departure', [$start, $end]);
                        $Querybl_south_volume->whereBetween('estimated_time_departure', [$start, $end]);
                }
            }else{
                $Querybl_north->whereNotNull('estimated_time_departure');
                $Querybl_north_volume->whereNotNull('estimated_time_departure');
                $Querybl_south->whereNotNull('estimated_time_departure');
                $Querybl_south_volume->whereNotNull('estimated_time_departure');
            }


            $list_bl_volume[$i]['bl_north'] = $Querybl_north->count('bl_no');
            $list_bl_volume[$i]['volume_north'] = $Querybl_north_volume
                                        ->count('bl_no');
            $list_bl_volume[$i]['bl_south'] = $Querybl_south
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_south'] = $Querybl_south_volume
                                        ->count('bl_no');

            $list_bl_volume[$i]['total_bl'] =  $list_bl_volume[$i]['bl_south']  + $list_bl_volume[$i]['bl_north'];
            $list_bl_volume[$i]['total_volume'] =  $list_bl_volume[$i]['volume_south']  + $list_bl_volume[$i]['volume_north'];

        $i++;
    }

    return $list_bl_volume;

});

Route::post('/bill_of_lading/onboard_shipment_details',function(Request $request){

    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $category = $request->input('category');
    $factory =  $request->input('factory');
    $factory_all = $request->input('factory_all');
    $pod = $request->input('pod');
    $pod_all = $request->input('pod_all');
    $as_of_now = $request->input('as_of_now');

    $select = ['bl_no_fk', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];
    $Query = Container::select($select)
                        ->join('bill_of_ladings','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNotIn('pod',['TBA'])
                       // ->where('estimated_time_departure', $date_request)
                        ->whereNull('actual_discharge');
    if(!$as_of_now){
        switch ($reference) {
            case 'D':
                $Query->where('estimated_time_departure',$date_request);
                break;
            case 'M':
                $month = explode('-', $dateMonth)[1];
                $year = explode('-', $dateMonth)[0];
                $Query->whereMonth('estimated_time_departure',$month);
                $Query->whereYear('estimated_time_departure',$year);
                break;
            case 'Y':
                $year = explode('-', $dateYear)[0];
                $Query->whereYear('estimated_time_departure',$year);
                break;
            default:
                $Query->whereBetween('estimated_time_departure', [$start, $end]);
        }
    }else{

        $Query->whereNotNull('estimated_time_departure');

    }

    if (!$factory_all) {
        $Query->where('factory', $factory);
    }

    if (!$pod_all) {
        $Query->wherePod($pod);
    }

    $list_of_BOL = $Query->get();
        if (count( $list_of_BOL) > 0) {

            foreach ( $list_of_BOL as $row) {

                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodity'] = implode(',', $com);

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $inv = [];
                foreach ($row['invoice_numbers'] as $in) {
                    $inv[] = $in['invoice_number'];
                }

                $row['invoices'] = implode(',', $inv);
            }

        }
    return  $list_of_BOL;
});


Route::post('/bill_of_lading/vessel_waiting',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $as_of_now = $request->input('as_of_now');

    $i = 0;
    $list_distinct = [];
    $list_bl_volume = [];




    foreach ($factories as $factory) {
        $list_distinct[$i]['name'] = $factory['factory_id'];
        $list_bl_volume[$i]['name'] = $factory['factory_id'];


        $QueryNorth =  bill_of_lading::select('connecting_vessel')->distinct()
                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotNull('connecting_vessel')
                            ->whereNull('actual_berthing_date')
                            ->whereFactory($factory['factory_id']);
        $QuerySouth =  bill_of_lading::select('connecting_vessel')->distinct()
                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotNull('connecting_vessel')
                            ->whereNull('actual_berthing_date')
                            ->whereFactory($factory['factory_id']);

                            switch ($reference) {
                                case 'D':
                                    $QueryNorth->where('actual_time_arrival',$date_request);
                                    $QuerySouth->where('actual_time_arrival',$date_request);
                                    break;
                                case 'M':
                                    $month = explode('-', $dateMonth)[1];
                                    $year = explode('-', $dateMonth)[0];

                                    $QueryNorth->whereMonth('actual_time_arrival',$month);
                                    $QueryNorth->whereYear('actual_time_arrival',$year);
                                    $QuerySouth->whereMonth('actual_time_arrival',$month);
                                    $QuerySouth->whereYear('actual_time_arrival',$year);
                                    break;
                                case 'Y':
                                    $year = explode('-', $dateYear)[0];
                                    $QueryNorth->whereYear('actual_time_arrival',$year);
                                    $QuerySouth->whereYear('actual_time_arrival',$year);
                                    break;
                                default:
                                    $QueryNorth->whereBetween('actual_time_arrival', [$start, $end]);
                                    $QuerySouth->whereBetween('actual_time_arrival', [$start, $end]);
                            }

        $list_distinct[$i]['north'] =  $QueryNorth->wherePod('NORTH')->count('connecting_vessel');



        // --------------------------------------------------------------



        $list_distinct[$i]['south'] =  $QuerySouth->wherePod('SOUTH')->count('connecting_vessel');


        // ============================================================


        $list_distinct[$i]['total'] =  $list_distinct[$i]['north'] + $list_distinct[$i]['south'];



        $north_bl_volume_query = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotNull('connecting_vessel')
                                ->whereNull('actual_berthing_date')
                                ->whereFactory($factory['factory_id']);
        $south_bl_volume_query = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotNull('connecting_vessel')
                                ->whereNull('actual_berthing_date')
                                ->whereFactory($factory['factory_id']);
        if(!$as_of_now){
            switch ($reference) {
                case 'D':
                    $north_bl_volume_query->where('actual_time_arrival',$date_request);
                    $south_bl_volume_query->where('actual_time_arrival',$date_request);
                    break;

                case 'M':
                    $month = explode('-', $dateMonth)[1];
                    $year = explode('-', $dateMonth)[0];


                    $north_bl_volume_query->whereMonth('actual_time_arrival',$month);
                    $north_bl_volume_query->whereYear('actual_time_arrival',$year);
                    $south_bl_volume_query->whereMonth('actual_time_arrival',$month);
                    $south_bl_volume_query->whereYear('actual_time_arrival',$year);
                    break;
                case 'Y':
                    $year = explode('-', $dateYear)[0];
                    $north_bl_volume_query->whereYear('actual_time_arrival',$year);
                    $south_bl_volume_query->whereYear('actual_time_arrival',$year);
                    break;
                default:
                    $north_bl_volume_query->whereBetween('actual_time_arrival', [$start, $end]);
                    $south_bl_volume_query->whereBetween('actual_time_arrival', [$start, $end]);
            }
        }else{
            $north_bl_volume_query->whereNotNull('actual_time_arrival');
            $south_bl_volume_query->whereNotNull('actual_time_arrival');
        }


        $list_bl_volume[$i]['bl_north'] = $north_bl_volume_query->wherePod('NORTH')->count('bl_no');

        $bl_no = [];
        foreach( $north_bl_volume_query->wherePod('NORTH')->get() as $row ){
            $bl_no[]  = $row['bl_no'];
        }

        $list_bl_volume[$i]['volume_north'] = Container::whereQuantity(1)
                        ->whereIn('bl_no_fk',$bl_no)
                        ->count();


        $list_bl_volume[$i]['bl_south'] =  $south_bl_volume_query->wherePod('SOUTH')->count('bl_no');

        $bl_no = [];
        foreach( $south_bl_volume_query->wherePod('SOUTH')->get() as $row ){
            $bl_no[]  = $row['bl_no'];
        }

        $list_bl_volume[$i]['volume_south'] = Container::whereQuantity(1)
                                        ->whereIn('bl_no_fk',$bl_no)
                                        ->count();



        $list_bl_volume[$i]['total_bl'] = $list_bl_volume[$i]['bl_north'] +  $list_bl_volume[$i]['bl_south'];
        $list_bl_volume[$i]['total_volume'] = $list_bl_volume[$i]['volume_south'] +  $list_bl_volume[$i]['volume_north'];


        $i++;
    }

    return $list = [$list_distinct,$list_bl_volume];

});


Route::post('/bill_of_lading/vessel_waiting_details',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $category = $request->input('category');
    $factory =  $request->input('factory');
    $factory_all = $request->input('factory_all');
    $pod = $request->input('pod');
    $pod_all = $request->input('pod_all');
    $as_of_now = $request->input('as_of_now');

   if($category == 'DISTINCT' ){
        $Query =  bill_of_lading::distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->whereNotIn('connecting_vessel',['T.B.A.','TBA'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereNotNull('connecting_vessel')
                                ->whereNull('actual_berthing_date');
   }else{
        $select = ['bl_no_fk', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];
        $Query = Container::select($select)
                            ->join('bill_of_ladings','containers.bl_no_fk','bill_of_ladings.bl_no')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotIn('pod',['TBA'])
                            ->whereNotNull('connecting_vessel')
                            ->whereNull('actual_berthing_date');
   }

   if(!$as_of_now){
    switch ($reference) {
            case 'D':
                $Query->where('actual_time_arrival',$date_request);
                break;
            case 'M':
                $month = explode('-', $dateMonth)[1];
                $year = explode('-', $dateMonth)[0];
                $Query->whereMonth('actual_time_arrival',$month);
                $Query->whereYear('actual_time_arrival',$year);
                break;
            case 'Y':
                $year = explode('-', $dateYear)[0];
                $Query->whereYear('actual_time_arrival',$year);
                break;
            default:
                $Query->whereBetween('actual_time_arrival', [$start, $end]);
        }
    }else{
        $Query->whereNotNull('actual_time_arrival');
    }


    if (!$factory_all) {
        $Query->where('factory', $factory);
    }

    if (!$pod_all) {
        $Query->wherePod($pod);
    }



    if( $category == 'DISTINCT' ){
        return $Query->select('connecting_vessel')->get();
    }else{
        $list_of_BOL = $Query->get();
        if (count( $list_of_BOL) > 0) {

            foreach ( $list_of_BOL as $row) {

                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodity'] = implode(',', $com);

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $inv = [];
                foreach ($row['invoice_numbers'] as $in) {
                    $inv[] = $in['invoice_number'];
                }

                $row['invoices'] = implode(',', $inv);
            }

        }
        return  $list_of_BOL;
    }



});


Route::post('/bill_of_lading/notyet_discharge',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $as_of_now = $request->input('as_of_now');

    $i = 0;


    $list_bl_volume = [];




    foreach ($factories as $factory) {

        $list_bl_volume[$i]['name'] = $factory['factory_id'];

            $Querybl_north = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->wherePod('NORTH')
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereNull('actual_discharge')
                                ->whereFactory($factory['factory_id']);

            $Querybl_north_volume = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                    ->whereQuantity(1)
                                    ->wherePod('NORTH')
                                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                                    ->whereNotIn('pod',['TBA'])
                                    ->whereNull('actual_discharge')
                                    ->whereFactory($factory['factory_id']);

            $Querybl_south = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->wherePod('SOUTH')
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereNull('actual_discharge')
                                ->whereFactory($factory['factory_id']);

            $Querybl_south_volume = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNull('actual_discharge')
                                        ->whereFactory($factory['factory_id']);
            if(!$as_of_now){
                switch ($reference) {
                    case 'D':
                        $Querybl_north->where('actual_berthing_date',$date_request);
                        $Querybl_north_volume->where('actual_berthing_date',$date_request);
                        $Querybl_south->where('actual_berthing_date',$date_request);
                        $Querybl_south_volume->where('actual_berthing_date',$date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];

                        $Querybl_north->whereMonth('actual_berthing_date',$month);
                        $Querybl_north->whereYear('actual_berthing_date',$year);

                        $Querybl_north_volume->whereMonth('actual_berthing_date',$month);
                        $Querybl_north_volume->whereYear('actual_berthing_date',$year);

                        $Querybl_south->whereMonth('actual_berthing_date',$month);
                        $Querybl_south->whereYear('actual_berthing_date',$year);

                        $Querybl_south_volume->whereMonth('actual_berthing_date',$month);
                        $Querybl_south_volume->whereYear('actual_berthing_date',$year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $Querybl_north->whereYear('actual_berthing_date',$year);
                        $Querybl_north_volume->whereYear('actual_berthing_date',$year);
                        $Querybl_south->whereYear('actual_berthing_date',$year);
                        $Querybl_south_volume->whereYear('actual_berthing_date',$year);
                        break;
                    default:
                        $Querybl_north->whereBetween('actual_berthing_date', [$start, $end]);
                        $Querybl_north_volume->whereBetween('actual_berthing_date', [$start, $end]);
                        $Querybl_south->whereBetween('actual_berthing_date', [$start, $end]);
                        $Querybl_south_volume->whereBetween('actual_berthing_date', [$start, $end]);
                }
            }else{

                $Querybl_north->whereNotNull('actual_berthing_date');
                $Querybl_north_volume->whereNotNull('actual_berthing_date');
                $Querybl_south->whereNotNull('actual_berthing_date');
                $Querybl_south_volume->whereNotNull('actual_berthing_date');

            }


            $list_bl_volume[$i]['bl_north'] = $Querybl_north->count('bl_no');
            $list_bl_volume[$i]['volume_north'] = $Querybl_north_volume
                                        ->count('bl_no');
            $list_bl_volume[$i]['bl_south'] = $Querybl_south
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_south'] = $Querybl_south_volume
                                        ->count('bl_no');

            $list_bl_volume[$i]['total_bl'] =  $list_bl_volume[$i]['bl_south']  + $list_bl_volume[$i]['bl_north'];
            $list_bl_volume[$i]['total_volume'] =  $list_bl_volume[$i]['volume_south']  + $list_bl_volume[$i]['volume_north'];

        $i++;
    }

    return $list_bl_volume;

});

Route::post('/bill_of_lading/notyet_discharge_details',function(Request $request){

    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $category = $request->input('category');
    $factory =  $request->input('factory');
    $factory_all = $request->input('factory_all');
    $pod = $request->input('pod');
    $pod_all = $request->input('pod_all');
    $as_of_now = $request->input('as_of_now');

    $select = ['bl_no_fk', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];
    $Query = Container::select($select)
                        ->join('bill_of_ladings','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNotIn('pod',['TBA'])
                       // ->where('estimated_time_departure', $date_request)
                        ->whereNull('actual_discharge');
    if(!$as_of_now){

        switch ($reference) {

            case 'D':

                $Query->where('actual_berthing_date',$date_request);

                break;
            case 'M':

                $month = explode('-', $dateMonth)[1];
                $year = explode('-', $dateMonth)[0];
                $Query->whereMonth('actual_berthing_date',$month);
                $Query->whereYear('actual_berthing_date',$year);

                break;
            case 'Y':

                $year = explode('-', $dateYear)[0];
                $Query->whereYear('actual_berthing_date',$year);

                break;
            default:
                $Query->whereBetween('actual_berthing_date', [$start, $end]);



        }

    }else{

        $Query->whereNotNull('actual_berthing_date');

    }

    if (!$factory_all) {
        $Query->where('factory', $factory);
    }

    if (!$pod_all) {
        $Query->wherePod($pod);
    }

    $list_of_BOL = $Query->get();
        if (count( $list_of_BOL) > 0) {

            foreach ( $list_of_BOL as $row) {

                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();
                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodity'] = implode(',', $com);

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $inv = [];
                foreach ($row['invoice_numbers'] as $in) {
                    $inv[] = $in['invoice_number'];
                }

                $row['invoices'] = implode(',', $inv);
            }

        }
    return  $list_of_BOL;
});

Route::post('/bill_of_lading/shipment_onprocess_report',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();


    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');


    $i = 0;


    $list_bl_volume = [];




    foreach ($factories as $factory) {

        $list_bl_volume[$i]['name'] = $factory['factory_id'];

            $Querybl_north = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->wherePod('NORTH')
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereFactory($factory['factory_id']);

            $Querybl_north_volume = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                    ->whereQuantity(1)
                                    ->wherePod('NORTH')
                                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                                    ->whereNotIn('pod',['TBA'])
                                    ->whereFactory($factory['factory_id']);

            $Querybl_south = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->wherePod('SOUTH')
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereFactory($factory['factory_id']);

            $Querybl_south_volume = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereFactory($factory['factory_id']);

                        switch ($reference) {
                            case 'D':
                                $Querybl_north->where('actual_process',$date_request);
                                $Querybl_north_volume->where('actual_process',$date_request);
                                $Querybl_south->where('actual_process',$date_request);
                                $Querybl_south_volume->where('actual_process',$date_request);
                                break;
                            case 'M':
                                $month = explode('-', $dateMonth)[1];
                                $year = explode('-', $dateMonth)[0];

                                $Querybl_north->whereMonth('actual_process',$month);
                                $Querybl_north->whereYear('actual_process',$year);

                                $Querybl_north_volume->whereMonth('actual_process',$month);
                                $Querybl_north_volume->whereYear('actual_process',$year);

                                $Querybl_south->whereMonth('actual_process',$month);
                                $Querybl_south->whereYear('actual_process',$year);

                                $Querybl_south_volume->whereMonth('actual_process',$month);
                                $Querybl_south_volume->whereYear('actual_process',$year);
                                break;
                            case 'Y':
                                $year = explode('-', $dateYear)[0];
                                $Querybl_north->whereYear('actual_process',$year);
                                $Querybl_north_volume->whereYear('actual_process',$year);
                                $Querybl_south->whereYear('actual_process',$year);
                                $Querybl_south_volume->whereYear('actual_process',$year);
                                break;
                            default:
                                $Querybl_north->whereBetween('actual_process', [$start, $end]);
                                $Querybl_north_volume->whereBetween('actual_process', [$start, $end]);
                                $Querybl_south->whereBetween('actual_process', [$start, $end]);
                                $Querybl_south_volume->whereBetween('actual_process', [$start, $end]);
                        }


            $list_bl_volume[$i]['bl_north'] = $Querybl_north->count('bl_no');
            $list_bl_volume[$i]['volume_north'] = $Querybl_north_volume
                                        ->count('bl_no');
            $list_bl_volume[$i]['bl_south'] = $Querybl_south
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_south'] = $Querybl_south_volume
                                        ->count('bl_no');

            $list_bl_volume[$i]['total_bl'] =  $list_bl_volume[$i]['bl_south']  + $list_bl_volume[$i]['bl_north'];
            $list_bl_volume[$i]['total_volume'] =  $list_bl_volume[$i]['volume_south']  + $list_bl_volume[$i]['volume_north'];

        $i++;
    }

    return $list_bl_volume;

});

Route::post('/bill_of_lading/shipment_onprocess_report_details',function(Request $request){

    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $category = $request->input('category');
    $factory =  $request->input('factory');
    $factory_all = $request->input('factory_all');
    $pod = $request->input('pod');
    $pod_all = $request->input('pod_all');
    $as_of_now = $request->input('as_of_now');

    $select = ['bl_no_fk', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];
    $Query = Container::select($select)
                        ->join('bill_of_ladings','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNotIn('pod',['TBA']);
                       // ->where('estimated_time_departure', $date_request)

    switch ($reference) {
        case 'D':
            $Query->where('actual_process',$date_request);
            break;
        case 'M':
            $month = explode('-', $dateMonth)[1];
            $year = explode('-', $dateMonth)[0];
            $Query->whereMonth('actual_process',$month);
            $Query->whereYear('actual_process',$year);
            break;
        case 'Y':
            $year = explode('-', $dateYear)[0];
            $Query->whereYear('actual_process',$year);
            break;
        default:
            $Query->whereBetween('actual_process', [$start, $end]);
    }


    if (!$factory_all) {
        $Query->where('factory', $factory);
    }

    if (!$pod_all) {
        $Query->wherePod($pod);
    }

    $list_of_BOL = $Query->get();
        if (count( $list_of_BOL) > 0) {

            foreach ( $list_of_BOL as $row) {
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();
                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodity'] = implode(',', $com);

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $inv = [];
                foreach ($row['invoice_numbers'] as $in) {
                    $inv[] = $in['invoice_number'];
                }

                $row['invoices'] = implode(',', $inv);
            }

        }
    return  $list_of_BOL;
});


Route::post('/bill_of_lading/shipment_without_gatepass',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $as_of_now = $request->input('as_of_now');

    $i = 0;


    $list_bl_volume = [];




    foreach ($factories as $factory) {

        $list_bl_volume[$i]['name'] = $factory['factory_id'];

            $Querybl_north = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->wherePod('NORTH')
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereNull('actual_gatepass')
                                ->whereFactory($factory['factory_id']);

            $Querybl_north_volume = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                    ->whereQuantity(1)
                                    ->wherePod('NORTH')
                                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                                    ->whereNotIn('pod',['TBA'])
                                    ->whereNull('actual_gatepass')
                                    ->whereFactory($factory['factory_id']);

            $Querybl_south = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->wherePod('SOUTH')
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereNull('actual_gatepass')
                                ->whereFactory($factory['factory_id']);

            $Querybl_south_volume = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNull('actual_gatepass')
                                        ->whereFactory($factory['factory_id']);
        if(!$as_of_now){
            switch ($reference) {
                case 'D':
                    $Querybl_north->where('actual_discharge',$date_request);
                    $Querybl_north_volume->where('actual_discharge',$date_request);
                    $Querybl_south->where('actual_discharge',$date_request);
                    $Querybl_south_volume->where('actual_discharge',$date_request);
                    break;
                case 'M':
                    $month = explode('-', $dateMonth)[1];
                    $year = explode('-', $dateMonth)[0];

                    $Querybl_north->whereMonth('actual_discharge',$month);
                    $Querybl_north->whereYear('actual_discharge',$year);

                    $Querybl_north_volume->whereMonth('actual_discharge',$month);
                    $Querybl_north_volume->whereYear('actual_discharge',$year);

                    $Querybl_south->whereMonth('actual_discharge',$month);
                    $Querybl_south->whereYear('actual_discharge',$year);

                    $Querybl_south_volume->whereMonth('actual_discharge',$month);
                    $Querybl_south_volume->whereYear('actual_discharge',$year);
                    break;
                case 'Y':
                    $year = explode('-', $dateYear)[0];
                    $Querybl_north->whereYear('actual_discharge',$year);
                    $Querybl_north_volume->whereYear('actual_discharge',$year);
                    $Querybl_south->whereYear('actual_discharge',$year);
                    $Querybl_south_volume->whereYear('actual_discharge',$year);
                    break;
                default:
                    $Querybl_north->whereBetween('actual_discharge', [$start, $end]);
                    $Querybl_north_volume->whereBetween('actual_discharge', [$start, $end]);
                    $Querybl_south->whereBetween('actual_discharge', [$start, $end]);
                    $Querybl_south_volume->whereBetween('actual_discharge', [$start, $end]);
            }
        }else{

            $Querybl_north->whereNotNull('actual_discharge');
            $Querybl_north_volume->whereNotNull('actual_discharge');
            $Querybl_south->whereNotNull('actual_discharge');
            $Querybl_south_volume->whereNotNull('actual_discharge');

        }

            $list_bl_volume[$i]['bl_north'] = $Querybl_north->count('bl_no');
            $list_bl_volume[$i]['volume_north'] = $Querybl_north_volume
                                        ->count('bl_no');
            $list_bl_volume[$i]['bl_south'] = $Querybl_south
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_south'] = $Querybl_south_volume
                                        ->count('bl_no');

            $list_bl_volume[$i]['total_bl'] =  $list_bl_volume[$i]['bl_south']  + $list_bl_volume[$i]['bl_north'];
            $list_bl_volume[$i]['total_volume'] =  $list_bl_volume[$i]['volume_south']  + $list_bl_volume[$i]['volume_north'];

        $i++;
    }

    return $list_bl_volume;

});

Route::post('/bill_of_lading/shipment_without_gatepass_details',function(Request $request){

    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $category = $request->input('category');
    $factory =  $request->input('factory');
    $factory_all = $request->input('factory_all');
    $pod = $request->input('pod');
    $pod_all = $request->input('pod_all');
    $as_of_now = $request->input('as_of_now');

    $select = ['bl_no_fk','actual_gatepass', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];
    $Query = Container::select($select)
                        ->join('bill_of_ladings','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNull('actual_gatepass')
                        ->whereNotIn('pod',['TBA']);
                       // ->where('estimated_time_departure', $date_request)
    if(!$as_of_now){

        switch ($reference) {
            case 'D':
                $Query->where('actual_discharge',$date_request);
                break;
            case 'M':
                $month = explode('-', $dateMonth)[1];
                $year = explode('-', $dateMonth)[0];
                $Query->whereMonth('actual_discharge',$month);
                $Query->whereYear('actual_discharge',$year);
                break;
            case 'Y':
                $year = explode('-', $dateYear)[0];
                $Query->whereYear('actual_discharge',$year);
                break;
            default:
                $Query->whereBetween('actual_discharge', [$start, $end]);
        }

    }else{

        $Query->whereNotNull('actual_discharge');

    }

    if (!$factory_all) {
        $Query->where('factory', $factory);
    }

    if (!$pod_all) {
        $Query->wherePod($pod);
    }

    $list_of_BOL = $Query->get();
        if (count( $list_of_BOL) > 0) {

            foreach ( $list_of_BOL as $row) {
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();
                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodity'] = implode(',', $com);

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $inv = [];
                foreach ($row['invoice_numbers'] as $in) {
                    $inv[] = $in['invoice_number'];
                }

                $row['invoices'] = implode(',', $inv);
            }

        }
    return  $list_of_BOL;
});


Route::post('/bill_of_lading/onhand_gatepass',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $as_of_now = $request->input('as_of_now');

    $i = 0;


    $list_bl_volume = [];




    foreach ($factories as $factory) {

        $list_bl_volume[$i]['name'] = $factory['factory_id'];

            $Querybl_north = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->wherePod('NORTH')
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereNull('pull_out')
                                ->whereFactory($factory['factory_id']);

            $Querybl_north_volume = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                    ->whereQuantity(1)
                                    ->wherePod('NORTH')
                                    ->whereNotIn('connecting_vessel',['T.B.A.'])
                                    ->whereNotIn('pod',['TBA'])
                                    ->whereNull('pull_out')
                                    ->whereFactory($factory['factory_id']);

            $Querybl_south = bill_of_lading::select('bl_no')
                                ->distinct()
                                ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                ->whereQuantity(1)
                                ->wherePod('SOUTH')
                                ->whereNotIn('connecting_vessel',['T.B.A.'])
                                ->whereNotIn('pod',['TBA'])
                                ->whereNull('pull_out')
                                ->whereFactory($factory['factory_id']);

            $Querybl_south_volume = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->wherePod('SOUTH')
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereNull('pull_out')
                                        ->whereFactory($factory['factory_id']);
            if(!$as_of_now){
                switch ($reference) {
                    case 'D':
                        $Querybl_north->where('actual_gatepass',$date_request);
                        $Querybl_north_volume->where('actual_gatepass',$date_request);
                        $Querybl_south->where('actual_gatepass',$date_request);
                        $Querybl_south_volume->where('actual_gatepass',$date_request);
                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];

                        $Querybl_north->whereMonth('actual_gatepass',$month);
                        $Querybl_north->whereYear('actual_gatepass',$year);

                        $Querybl_north_volume->whereMonth('actual_gatepass',$month);
                        $Querybl_north_volume->whereYear('actual_gatepass',$year);

                        $Querybl_south->whereMonth('actual_gatepass',$month);
                        $Querybl_south->whereYear('actual_gatepass',$year);

                        $Querybl_south_volume->whereMonth('actual_gatepass',$month);
                        $Querybl_south_volume->whereYear('actual_gatepass',$year);
                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $Querybl_north->whereYear('actual_gatepass',$year);
                        $Querybl_north_volume->whereYear('actual_gatepass',$year);
                        $Querybl_south->whereYear('actual_gatepass',$year);
                        $Querybl_south_volume->whereYear('actual_gatepass',$year);
                        break;
                    default:
                        $Querybl_north->whereBetween('actual_gatepass', [$start, $end]);
                        $Querybl_north_volume->whereBetween('actual_gatepass', [$start, $end]);
                        $Querybl_south->whereBetween('actual_gatepass', [$start, $end]);
                        $Querybl_south_volume->whereBetween('actual_gatepass', [$start, $end]);
                }
            }else{
                $Querybl_north->whereNotNull('actual_gatepass');
                $Querybl_north_volume->whereNotNull('actual_gatepass');
                $Querybl_south->whereNotNull('actual_gatepass');
                $Querybl_south_volume->whereNotNull('actual_gatepass');
            }


            $list_bl_volume[$i]['bl_north'] = $Querybl_north->count('bl_no');
            $list_bl_volume[$i]['volume_north'] = $Querybl_north_volume
                                        ->count('bl_no');
            $list_bl_volume[$i]['bl_south'] = $Querybl_south
                                        ->count('bl_no');
            $list_bl_volume[$i]['volume_south'] = $Querybl_south_volume
                                        ->count('bl_no');

            $list_bl_volume[$i]['total_bl'] =  $list_bl_volume[$i]['bl_south']  + $list_bl_volume[$i]['bl_north'];
            $list_bl_volume[$i]['total_volume'] =  $list_bl_volume[$i]['volume_south']  + $list_bl_volume[$i]['volume_north'];

        $i++;
    }

    return $list_bl_volume;

});

Route::post('/bill_of_lading/onhand_gatepass_details',function(Request $request){

    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $category = $request->input('category');
    $factory =  $request->input('factory');
    $factory_all = $request->input('factory_all');
    $pod = $request->input('pod');
    $pod_all = $request->input('pod_all');
    $as_of_now = $request->input('as_of_now');

    $select = ['bl_no_fk','actual_gatepass', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];
    $Query = Container::select($select)
                        ->join('bill_of_ladings','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNull('pull_out')
                        ->whereNotIn('pod',['TBA']);
                       // ->where('estimated_time_departure', $date_request)
    if(!$as_of_now){
        switch ($reference) {
            case 'D':
                $Query->where('actual_gatepass',$date_request);
                break;
            case 'M':
                $month = explode('-', $dateMonth)[1];
                $year = explode('-', $dateMonth)[0];
                $Query->whereMonth('actual_gatepass',$month);
                $Query->whereYear('actual_gatepass',$year);
                break;
            case 'Y':
                $year = explode('-', $dateYear)[0];
                $Query->whereYear('actual_gatepass',$year);
                break;
            default:
                $Query->whereBetween('actual_gatepass', [$start, $end]);
        }
    }else{
        $Query->whereNotNull('actual_gatepass');
    }

    if (!$factory_all) {
        $Query->where('factory', $factory);
    }

    if (!$pod_all) {
        $Query->wherePod($pod);
    }

    $list_of_BOL = $Query->get();
        if (count( $list_of_BOL) > 0) {

            foreach ( $list_of_BOL as $row) {
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();
                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodity'] = implode(',', $com);

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $inv = [];
                foreach ($row['invoice_numbers'] as $in) {
                    $inv[] = $in['invoice_number'];
                }

                $row['invoices'] = implode(',', $inv);
            }

        }
    return  $list_of_BOL;
});

Route::post('/bill_of_lading/dismounted_with_chassi',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');

    $i = 0;


    $list_bl_volume = [];




    foreach ($factories as $factory) {

        $list_bl_volume[$i]['name'] = $factory['factory_id'];

            $QueryIRS = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereFactory($factory['factory_id'])
                                        ->where('dismounted_cy','IRS BACAO');

            $QueryChassi = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                                        ->whereQuantity(1)
                                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                                        ->whereNotIn('pod',['TBA'])
                                        ->whereFactory($factory['factory_id'])
                                        ->where('dismounted_cy','WITH CHASSI');
            switch ($reference) {
                case 'D':
                    $QueryIRS->where('dismounted_date',$date_request);

                    $QueryChassi->where('dismounted_date',$date_request);
                    break;
                case 'M':
                    $month = explode('-', $dateMonth)[1];
                    $year = explode('-', $dateMonth)[0];

                    $QueryIRS->whereMonth('dismounted_date',$month);
                    $QueryIRS->whereYear('dismounted_date',$year);


                    $QueryChassi->whereMonth('dismounted_date',$month);
                    $QueryChassi->whereYear('dismounted_date',$year);
                    break;
                case 'Y':
                    $year = explode('-', $dateYear)[0];
                    $QueryIRS->whereYear('dismounted_date',$year);

                    $QueryChassi->whereYear('dismounted_date',$year);
                    break;
                default:
                    $QueryIRS->whereBetween('dismounted_date', [$start, $end]);

                    $QueryChassi->whereBetween('dismounted_date', [$start, $end]);
            }


            $list_bl_volume[$i]['irs'] = $QueryIRS->count();
            $list_bl_volume[$i]['chassi'] = $QueryChassi
                                        ->count();



        $i++;
    }

    return $list_bl_volume;

});

Route::post('/bill_of_lading/dismounted_with_chassi_details',function(Request $request){

    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();

    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $category = $request->input('category');
    $factory =  $request->input('factory');
    $factory_all = $request->input('factory_all');
    $cy = $request->input('cy');
    $cy_all = $request->input('cy_all');
    $as_of_now = $request->input('as_of_now');

    $select = ['bl_no_fk','factory','return_date','actual_gatepass','pull_out','dismounted_date','dismounted_cy','unload', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];
    $Query = Container::select($select)
                        ->join('bill_of_ladings','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNotIn('pod',['TBA']);
                        //->whereFactory($factory['factory_id'])
                       // ->where('dismounted_cy','IRS BACAO');

                       // ->where('estimated_time_departure', $date_request)

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


    if (!$factory_all) {
        $Query->where('factory', $factory);
    }

    if (!$cy_all) {
        $Query->where('dismounted_cy',$cy);
    }

    $list_of_BOL = $Query->get();
        if (count( $list_of_BOL) > 0) {

            foreach ( $list_of_BOL as $row) {
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();
                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodity'] = implode(',', $com);

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $inv = [];
                foreach ($row['invoice_numbers'] as $in) {
                    $inv[] = $in['invoice_number'];
                }

                $row['invoices'] = implode(',', $inv);
            }

        }
    return  $list_of_BOL;
});

// Route::post('/bill_of_lading/container_irs_report',function(Request $request){


//     $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



//     $date_request = $request->input('date_filter');
//     $dateMonth = $request->input('dateMonth');
//     $dateYear = $request->input('dateYear');
//     $reference = $request->input('reference');
//     //$year = explode('-', $date_request)[0];
//     $start = $request->input('range_start');
//     $end = $request->input('range_end');

//     $i = 0;


//     $list_bl_volume = [];




//     foreach ($factories as $factory) {

//         $list_bl_volume[$i]['name'] = $factory['factory_id'];

//             $QueryIRS = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
//                                         ->whereQuantity(1)
//                                         ->whereNotIn('connecting_vessel',['T.B.A.'])
//                                         ->whereNotIn('pod',['TBA'])
//                                         ->whereFactory($factory['factory_id'])
//                                         ->where('dismounted_cy','IRS BACAO');
//             $QueryCEZ1 = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
//                                         ->whereQuantity(1)
//                                         ->whereNotIn('connecting_vessel',['T.B.A.'])
//                                         ->whereNotIn('pod',['TBA'])
//                                         ->whereFactory($factory['factory_id'])
//                                         ->where('dismounted_cy','CEZ 1 PUTOL');
//             $QueryCEZ2 = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
//                                         ->whereQuantity(1)
//                                         ->whereNotIn('connecting_vessel',['T.B.A.'])
//                                         ->whereNotIn('pod',['TBA'])
//                                         ->whereFactory($factory['factory_id'])
//                                         ->where('dismounted_cy','CEZ 2 PUTOL');
//             $QueryChassi = bill_of_lading::join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
//                                         ->whereQuantity(1)
//                                         ->whereNotIn('connecting_vessel',['T.B.A.'])
//                                         ->whereNotIn('pod',['TBA'])
//                                         ->whereFactory($factory['factory_id'])
//                                         ->where('dismounted_cy','WITH CHASSI');
//             switch ($reference) {
//                 case 'D':
//                     $QueryIRS->where('dismounted_date',$date_request);
//                     $QueryCEZ1->where('dismounted_date',$date_request);
//                     $QueryCEZ2->where('dismounted_date',$date_request);
//                     $QueryChassi->where('dismounted_date',$date_request);
//                     break;
//                 case 'M':
//                     $month = explode('-', $dateMonth)[1];
//                     $year = explode('-', $dateMonth)[0];

//                     $QueryIRS->whereMonth('dismounted_date',$month);
//                     $QueryIRS->whereYear('dismounted_date',$year);

//                     $QueryCEZ1->whereMonth('dismounted_date',$month);
//                     $QueryCEZ1->whereYear('dismounted_date',$year);

//                     $QueryCEZ2->whereMonth('dismounted_date',$month);
//                     $QueryCEZ2->whereYear('dismounted_date',$year);

//                     $QueryChassi->whereMonth('dismounted_date',$month);
//                     $QueryChassi->whereYear('dismounted_date',$year);
//                     break;
//                 case 'Y':
//                     $year = explode('-', $dateYear)[0];
//                     $QueryIRS->whereYear('dismounted_date',$year);
//                     $QueryCEZ1->whereYear('dismounted_date',$year);
//                     $QueryCEZ2->whereYear('dismounted_date',$year);
//                     $QueryChassi->whereYear('dismounted_date',$year);
//                     break;
//                 default:
//                     $QueryIRS->whereBetween('dismounted_date', [$start, $end]);
//                     $QueryCEZ1->whereBetween('dismounted_date', [$start, $end]);
//                     $QueryCEZ2->whereBetween('dismounted_date', [$start, $end]);
//                     $QueryChassi->whereBetween('dismounted_date', [$start, $end]);
//             }


//             $list_bl_volume[$i]['irs'] = $QueryIRS->count();
//             $list_bl_volume[$i]['cez1'] = $QueryCEZ1
//                                         ->count();
//             $list_bl_volume[$i]['cez2'] = $QueryCEZ2
//                                         ->count();
//             $list_bl_volume[$i]['chassi'] = $QueryChassi
//                                         ->count();



//         $i++;
//     }

//     return $list_bl_volume;

// });

Route::post('/bill_of_lading/container_irs_report_details',function(Request $request){

    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();


    $factory =  $request->input('factory');
    $factory_all = $request->input('factory_all');


    $select = ['bl_no_fk','actual_gatepass','pull_out','dismounted_date','dismounted_cy', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];
    $Query = Container::select($select)
                        ->join('bill_of_ladings','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNotIn('pod',['TBA'])
                        ->whereNotNull('dismounted_date')
                        ->where('dismounted_cy','IRS BACAO')
                        ->whereNull('unload');
                        //->whereFactory($factory['factory_id'])
                       // ->where('dismounted_cy','IRS BACAO');

                       // ->where('estimated_time_departure', $date_request)


    if (!$factory_all) {
        $Query->where('factory', $factory);
    }



    $list_of_BOL = $Query->get();
        if (count( $list_of_BOL) > 0) {

            foreach ( $list_of_BOL as $row) {
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();
                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodity'] = implode(',', $com);

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $inv = [];
                foreach ($row['invoice_numbers'] as $in) {
                    $inv[] = $in['invoice_number'];
                }

                $row['invoices'] = implode(',', $inv);
            }

        }
    return  $list_of_BOL;
});


Route::post('/bill_of_lading/containers_not_return',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();



    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $as_of_now = $request->input('as_of_now');

    $i = 0;


    $list_bl_volume = [];




    foreach ($factories as $factory) {

        $list_bl_volume[$i]['name'] = $factory['factory_id'];

            $QueryNotReturn = bill_of_lading::
                            select('container_type')
                            ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                            ->whereQuantity(1)
                            ->whereNotIn('connecting_vessel',['T.B.A.'])
                            ->whereNotIn('pod',['TBA'])
                            ->whereNull('return_date')
                            ->whereFactory($factory['factory_id']);
            if(!$as_of_now){
                switch ($reference) {
                    case 'D':
                        $QueryNotReturn->where('unload',$date_request);

                        break;
                    case 'M':
                        $month = explode('-', $dateMonth)[1];
                        $year = explode('-', $dateMonth)[0];

                        $QueryNotReturn->whereMonth('unload',$month);
                        $QueryNotReturn->whereYear('unload',$year);


                        break;
                    case 'Y':
                        $year = explode('-', $dateYear)[0];
                        $QueryNotReturn->whereYear('unload',$year);

                        break;
                    default:
                        $QueryNotReturn->whereBetween('unload', [$start, $end]);
                }
            }else{

                $QueryNotReturn->whereNotNull('unload');

            }

            $list_bl_volume[$i]['size'] =   $QueryNotReturn->get();
            $list_bl_volume[$i]['volume'] =  count($list_bl_volume[$i]['size']);



        $i++;
    }

    return $list_bl_volume;

});


Route::post('/bill_of_lading/containers_not_return_details',function(Request $request){


    $factories = Factory::select('factory_id')->whereNull('deleted_at')->get();

    $date_request = $request->input('date_filter');
    $dateMonth = $request->input('dateMonth');
    $dateYear = $request->input('dateYear');
    $reference = $request->input('reference');
    //$year = explode('-', $date_request)[0];
    $start = $request->input('range_start');
    $end = $request->input('range_end');
    $factory =  $request->input('factory');
    $factory_all = $request->input('factory_all');
    $as_of_now = $request->input('as_of_now');


    $select = ['bl_no_fk','actual_gatepass','pull_out','dismounted_date','dismounted_cy','unload', 'container_number', 'actual_time_arrival', 'actual_discharge','supplier','shipping_line','connecting_vessel','pol','country','pod','estimated_time_departure','estimated_time_arrival','actual_time_arrival','actual_berthing_date','container_type'];
    $Query = Container::select($select)
                        ->join('bill_of_ladings','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereQuantity(1)
                        ->whereNotIn('connecting_vessel',['T.B.A.'])
                        ->whereNotIn('pod',['TBA'])
                        ->orderBy('pull_out', 'asc')
                        ->whereNull('return_date');

                        //->whereFactory($factory['factory_id'])
                       // ->where('dismounted_cy','IRS BACAO');

                       // ->where('estimated_time_departure', $date_request)
    if(!$as_of_now){
        switch ($reference) {
            case 'D':
                $Query->where('unload',$date_request);
                break;
            case 'M':
                $month = explode('-', $dateMonth)[1];
                $year = explode('-', $dateMonth)[0];
                $Query->whereMonth('unload',$month);
                $Query->whereYear('unload',$year);
                break;
            case 'Y':
                $year = explode('-', $dateYear)[0];
                $Query->whereYear('unload',$year);
                break;
            default:
                $Query->whereBetween('unload', [$start, $end]);
        }
    }else{
        $Query->whereNotNull('unload');
    }

    if (!$factory_all) {
        $Query->where('factory', $factory);
    }


    $list_of_BOL = $Query->get();
        if (count( $list_of_BOL) > 0) {

            foreach ( $list_of_BOL as $row) {
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();
                $com = [];
                foreach ($row['commodities'] as $cm) {
                    $com[] = $cm['commodity'];
                }
                $row['commodity'] = implode(',', $com);

                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no_fk'])->get();

                $inv = [];
                foreach ($row['invoice_numbers'] as $in) {
                    $inv[] = $in['invoice_number'];
                }

                $row['invoices'] = implode(',', $inv);
            }

        }
    return  $list_of_BOL;

});

//Demurrage get show more
Route::post('/bill_of_lading/get_storage_demmurage_all',function(Request $request){
    $skip = $request->input('skip');
    $take = $request->input('take');


    if ($take == 'ALL') {
        ini_set('max_execution_time', 300);
        $total = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                        //->whereQuantity(1)
                        ->whereNull('containers.pull_out')
                        //2020-08-27
                        ->whereNotNull('containers.actual_discharge')
                        //->whereNotNull('containers.actual_gatepass')
                        ->orderBy('containers.actual_discharge','ASC')
                        ->count();
        $take = $total;

    }

    $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
    ->skip($skip)
    ->take($take)
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
        if (count($list_of_BOL) > 0) {
            foreach ($list_of_BOL as $row) {
                $row['sameDischarge'] = false;
                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            }

        }
    return  $list_of_BOL;
});

//Unload get show more
Route::post('/bill_of_lading/get_unload_all',function(Request $request){
    $skip = $request->input('skip');
    $take = $request->input('take');



    if ($take == 'ALL') {
        $total = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                        //->whereQuantity(1)
                        ->whereNull('containers.return_date')
                        ->whereNotNull('containers.pull_out')
                        ->count();
        $take = $total;
        ini_set('max_execution_time', 300);
    }

   $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
                        ->skip($skip )
                        ->take($take)
                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                        //->whereQuantity(1)
                        ->whereNull('containers.return_date')
                        ->whereNotNull('containers.pull_out')
                        ->get();
            // $list_of_BOL = Bill_of_Lading::whereNotNull('actual_gatepass')
            //   ->get();

        if (count($list_of_BOL) > 0) {
            foreach ($list_of_BOL as $row) {
                $row['sameDischarge'] = false;
                $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
                $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNotNull('containers.actual_gatepass')->get();
                $row['split_factory'] = Bill_of_Lading::select('factory')->where('bl_no', '=', $row['bl_no'])->get();
                $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
            }

        }
    return  $list_of_BOL;
});



//Cleared Shipment get show more
Route::post('/bill_of_lading/get_cleared_shipment_all',function(Request $request){
    $skip = $request->input('skip');
    $take = $request->input('take');



    if ($take == 'ALL') {
        $total = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereQuantity(1)
                        ->whereNotNull('return_date')
                        ->whereNotNull('unload')
                        ->count();
        $take = $total;
        ini_set('max_execution_time', 300);
    }

   $list_of_BOL = Bill_of_Lading::select('*',DB::raw("bill_of_ladings.id as bol_id, containers.id as container_id"))
                        ->skip($skip )
                        ->take($take)
                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereQuantity(1)
                        ->whereNotNull('return_date')
                        ->get();
            // $list_of_BOL = Bill_of_Lading::whereNotNull('actual_gatepass')
            //   ->get();

        if (count($list_of_BOL) > 0) {
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
            }

        }
    return  $list_of_BOL;
});


//get_shipment_on_process get show more
Route::post('/bill_of_lading/get_shipment_on_process',function(Request $request){
    $skip = $request->input('skip');
    $take = $request->input('take');


    $list_of_BOL = Bill_of_Lading::select('bl_no','target_gatepass')->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')
    ->whereNull('containers.actual_gatepass')
    ->orderBy('target_gatepass','ASC')->distinct()->get();

    if ($take == 'ALL') {
        $total = Bill_of_Lading::select('bl_no')
                        ->join('containers','containers.bl_no_fk','bill_of_ladings.bl_no')
                        ->whereNull('containers.actual_gatepass')
                        ->distinct()
                        ->count();
        $take = $total;
        ini_set('max_execution_time', 300);
    }

    foreach ($list_of_BOL as $key) {
        $bl_no[] = $key["bl_no"];
    }


    if (count($list_of_BOL) > 0) {
        $list_of_BOL = Bill_of_Lading::skip( $skip )->take( $take)->whereIn('bl_no', $bl_no)
            ->whereNotNull('actual_process')
        //->whereNotNull('actual_berthing_date')
            ->orderBy('target_gatepass','ASC')
            ->get();
        $list_of_BOL_Total = Bill_of_Lading::whereIn('bl_no', $bl_no)
            ->whereNotNull('actual_process')
        //->whereNotNull('actual_berthing_date')
            ->get();
    }



    // $list_of_BOL = Bill_of_Lading::whereNotNull('e2m')->whereNotNull('actual_berthing_date')
    //                                 ->whereNull('actual_gatepass')
    //                                 ->get();

    foreach ($list_of_BOL as $row) {

        $row['commodities'] = Bill_of_Lading_Commodity::select('commodity')->where('bl_no_fk', '=', $row['bl_no'])->get();
        $row['container_numbers'] = Container::where('bl_no_fk', '=', $row['bl_no'])->whereNull('actual_gatepass')->get();

    }

    return $list_of_BOL;
});


//****************/
// LOGISTICS REPORTS
//****************/

Route::post('/bill_of_lading/get_logistics_reports',function(Request $request){

    $vessel = $request->input('vessel');
    $ETA = $request->input('ETA');
    $ATA = $request->input('ATA');
    $ATB = $request->input('ATB');

    $port = $request->input('port');
    $gatepass = $request->input('gatepass');
    $delivery = $request->input('delivery');
    $unloaded = $request->input('unloaded');
    $returned = $request->input('returned');

    $start = $request->input('range_start');
    $end = $request->input('range_end');

    $list_of_bl = [];
    $list_of_bol = [];

    if( $vessel == true ){

        $list_of_bol = Bill_of_lading::select('bl_no');

        if($ETA == true){
            $list_of_bol =  $list_of_bol->orWhereBetween('estimated_time_arrival',[$start,$end]);
        }

        if($ATA == true){
            $list_of_bol =  $list_of_bol->orWhereBetween('actual_time_arrival',[$start,$end]);
        }

        if($ATB == true){
            $list_of_bol =  $list_of_bol->orWhereBetween('actual_berthing_date',[$start,$end]);
        }
                        // ->WhereBetween('estimated_time_arrival',[$start,$end])

                        // ->orWhereBetween('actual_time_arrival',[$start,$end])
        $list_of_bol =  $list_of_bol->get();

        foreach(  $list_of_bol as $row ){
            $list_of_bl[] = $row['bl_no'];
        }

    }

    if( $port == true ){

        $list_of_bol = Container::select('bl_no_fk')->distinct()
                        ->WhereBetween('actual_discharge',[$start,$end])
                        ->get();

        foreach(  $list_of_bol as $row ){
            $list_of_bl[] = $row['bl_no_fk'];
        }

    }

    if( $gatepass == true ){

        $list_of_bol = Container::select('bl_no_fk')->distinct()
                        ->WhereBetween('actual_gatepass',[$start,$end])
                        ->get();

        foreach(  $list_of_bol as $row ){
            $list_of_bl[] = $row['bl_no_fk'];
        }

    }

    if( $delivery == true ){

        $list_of_bol = Container::select('bl_no_fk')->distinct()
                        ->WhereBetween('pull_out',[$start,$end])
                        ->get();

        foreach(  $list_of_bol as $row ){
            $list_of_bl[] = $row['bl_no_fk'];
        }

    }

    if( $unloaded == true ){

        $list_of_bol = Container::select('bl_no_fk')->distinct()
                        ->WhereBetween('unload',[$start,$end])
                        ->get();

        foreach(  $list_of_bol as $row ){
            $list_of_bl[] = $row['bl_no_fk'];
        }

    }

    if( $returned == true ){

        $list_of_bol = Container::select('bl_no_fk')->distinct()
                        ->WhereBetween('return_date',[$start,$end])
                        ->get();

        foreach(  $list_of_bol as $row ){
            $list_of_bl[] = $row['bl_no_fk'];
        }

    }
    $list_of_bl = array_unique($list_of_bl);


    $list_of_BOL = Bill_of_lading::orderBy('id', 'DESC')
                    ->whereIn('bl_no',$list_of_bl)
                    ->get();

    foreach ($list_of_BOL as $row) {

        $row['invoice_numbers'] = Bill_of_Lading_Invoice::select('invoice_number')->where('bl_no_fk', '=', $row['bl_no'])->get();
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
    return $list_of_BOL;







});

