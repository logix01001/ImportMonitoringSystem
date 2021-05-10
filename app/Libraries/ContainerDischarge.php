<?php
namespace App\libraries;

use App\bill_of_lading_commodity;
use App\Container;

class ContainerDischarge {

    private $_start,
            $_end,
            $_data = [],
            $_selected_field = ['bill_of_ladings.assessment_tag','containers.id as container_id','estimated_time_arrival','dispatched_date','target_dispatch_date','target_gatepass','actual_time_arrival','actual_berthing_date','actual_gatepass','factory','bl_no','pod','shipping_line','container_number','actual_discharge','container_type','factory','pull_out'],
            $_container
            ;

    public function __construct($start,$end){

        $this->start = $start;
        $this->end = $end;
        $this->_container = new Container;

    }

    public function get_discharge(){

        $container = $this->_container;

        $container = $container->select($this->_selected_field)
                ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                //->whereBetween('estimated_time_arrival',[$this->start, $this->end])
                ->where(function($query){
                    $query->whereNotNull('actual_discharge');
                    $query->whereNull('pull_out');
                })
                ->where('quantity',1);

                //actual_discharge
                //estimated_time_arrival
        // if($gatepass){
        //     $container = $container->whereNotNull('actual_gatepass');
        // }else{
        //     $container = $container->whereNull('actual_gatepass');
        // }

        $data = $container->orderBy('actual_discharge','asc')->get()->toArray();

        for($i = 0; $i < count( $data); $i++){
            $data[$i]['commodity'] = bill_of_lading_commodity::select('commodity')->where('bl_no_fk', $data[$i]['bl_no'])->pluck('commodity');
        }


        // foreach( $data as $key=>$row){

        //     $data[$key]['status'] = ($gatepass) ? 1 : 2 ;
        // }


        if(count($data) > 0){
            $this->put_data($data);
        }


    }

    public function get_eta(){

        $container = $this->_container;

        $container = $container->select($this->_selected_field)
                ->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
                ->whereBetween('estimated_time_arrival',[$this->start, $this->end])
				->whereNull('pull_out')
				->where('quantity',1);
                //actual_discharge
                //estimated_time_arrival
        // if($gatepass){
        //     $container = $container->whereNotNull('actual_gatepass');
        // }else{
        //     $container = $container->whereNull('actual_gatepass');
        // }

        $data = $container->get()->sortBy('estimated_time_arrival')->toArray();

        for($i = 0; $i < count( $data); $i++){
            $data[$i]['commodity'] = bill_of_lading_commodity::select('commodity')->where('bl_no_fk', $data[$i]['bl_no'])->pluck('commodity');
            $data[$i]['eta'] = true;
        }


        // foreach( $data as $key=>$row){

        //     $data[$key]['status'] = ($gatepass) ? 1 : 2 ;
        // }


        if(count($data) > 0){
            $this->put_data($data);
        }


    }


    private function put_data($data){

        if(empty($this->_data))

            $this->_data =  $data;
        else
            $this->_data = array_merge($this->_data,$data);
            //array_push($this->_data,$data);
         //

    }

    public function get_data(){
        $this->_data = array_merge($this->_data,[]);

         //   $_selected_field = ['estimated_time_arrival','actual_time_arrival','actual_berthing_date','actual_gatepass','factory','bl_no','pod','shipping_line','container_number','actual_discharge','container_type'],

        if(count( $this->_data) > 0){

            foreach(  $this->_data as $key=>$row){
                if( $row['actual_discharge'] != null &&  $row['actual_gatepass'] != null){
                    $this->_data[$key]['status'] = 1;
                }elseif($row['actual_discharge'] != null &&  $row['actual_gatepass'] == null){
                    $this->_data[$key]['status'] = 2;
                }elseif($row['actual_berthing_date'] != null ){
                    $this->_data[$key]['status'] = 3;
                }elseif($row['actual_time_arrival'] != null ){
                    $this->_data[$key]['status'] = 4;
                }else{
                    $this->_data[$key]['status'] = 5;
                }

            }

        }




        return $this->_data;
    }


}
