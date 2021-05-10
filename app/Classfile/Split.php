<?php

namespace App\Classfile;

use App\container;
use App\Bill_of_Lading;
use App\Holiday;
use Session;

class Split {

    protected $fieldsname = [

        'actual_discharge',
        'validity_storage',
        'validity_demurrage',
        'revalidity_storage',
        'revalidity_demurrage',
        'revalidity_remarks',
        'trucker',
        'actual_gatepass',
        'dispatched_date',
        'pull_out',
        'pull_out_time',
        'target_dispatch_date',
        'reason_of_delay_delivery',
        'pull_out_remarks',
        'detention_validity',
        'unload',
        'return_cy',
        'return_date',
        'return_box_number',
        'safe_keep',
        'dismounted_cy',
        'dismounted_date',


    ];

    protected $id,$value,$column,$bl_number,$split_bls = [],$containers = [],$ids_update = [];

    public function __construct($id,$column,$value)
    {

        $this->id = $id;
        $this->column = $column;
        $this->value = $value;


        if(in_array($this->column, $this->fieldsname)){

            $containers = container::find($id);

            if($containers->split_bl_no_fk != null){
                $this->split_bls = explode(',',$containers->split_bl_no_fk);

                foreach($this->split_bls as $bl){
                    $this->bl_number =  $bl;
                    if($this->column == 'actual_discharge' ){

                        $this->target_gatepass();

                    }

                    $result = container::get_split_containers($bl, $containers->container_number);

                    if($result != null){
                        $this->containers[] = $result   ;
                    }
                }

                $this->ids_update = array_map(function($container) { return $container['id']; },$this->containers);

                $this->save_ids_update();

            }

        }

    }

    private function save_ids_update(){

        container::save_split($this->ids_update,$this->column,$this->value);
    }

    public function achieved_gatepass($status,$reason = null,$pullout_remarks = null){

        if($this->column == 'actual_gatepass'){

            container::whereIn('id',$this->ids_update)->update([
                'sop_current_status' => $status,
                'pull_out_remarks' => $pullout_remarks,
                'gatepass_datetime_update' => \date('Y-m-d H:i:s'),
                'gatepass_update_by' =>  Session::get('employee_number'),
                'reason_of_delay_gatepass' => $reason != null ? strtoupper($reason) : null
            ]);

        }
    }
    private function target_gatepass(){

        $value = $this->value;

        $discharge_latest =  Container::select('actual_discharge')->skip(0)->take(1)->where('bl_no_fk', $this->bl_number )->orderBy('actual_discharge','DESC')->get();
        $discharge_latest =  $discharge_latest[0]->actual_discharge;

        if($discharge_latest == null){
            $discharge_latest = $this->value;
        }

        $berthing_latest = Bill_of_lading::select('actual_berthing_date')->where('bl_no', $this->bl_number )->get();
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
        $date = new \DateTime($value);
        $date->modify('+1 day');
        // Output
        $value = $date->format('Y-m-d');
        //BIANCA SUNDAY ONLY
        //if ((date('N', strtotime($value)) >= 6) == 1) {

        if ((date('N', strtotime($value)) >= 6) == 1) {
            // Create a new DateTime object
            $date = new \DateTime($value);

            // Modify the date it contains
            $date->modify('next monday');

            // Output
            $newdate = $date->format('Y-m-d');

            while (Holiday::where('holiday_date', '=', $newdate)->count() == 1) {
                $date = new \DateTime((string) $newdate);
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
            $date = new \DateTime($value);

            // Output
            $newdate = $date->format('Y-m-d');

            while (Holiday::where('holiday_date', '=', $newdate)->count() == 1) {
                $date = new \DateTime((string) $newdate);
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


        Bill_of_Lading::where('bl_no', $this->bl_number )
        ->update([
            'target_gatepass' =>  $newdate,
        ]);
    }


}
