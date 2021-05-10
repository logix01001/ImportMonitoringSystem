<?php
namespace App\libraries;
use App\bill_of_lading;
use App\Bill_of_Lading_Invoice;
use App\bill_of_lading_commodity;


class BillOfLading {

    private $ListOfBL;
    private $returnedData;

    public function __construct(object $Lists){
        $this->ListOfBL = $Lists;
        $this->setArray();
    }

    private function setArray(){
        $data = [];
        $index = 0;

        foreach($this->ListOfBL as $row){
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
            $data[ $index ][] =  $row['vessel'];
            $data[ $index ][] =  $row['connecting_vessel'];
            $data[ $index ][] =  $row['shipping_line'];
            $data[ $index ][] =  $row['forwarder'];
            $data[ $index ][] =  $row['broker'];
            $data[ $index ][] =  $row['pol'];
            $data[ $index ][] =  $row['country'];
            $data[ $index ][] =  $row['pod'];
            $data[ $index ][] =  $row['volume'];
            $data[ $index ][] =  $row['quantity'];
            $data[ $index ][] =  $row['container_type'];
            $data[ $index ][] =  $row['container_number'];
            $data[ $index ][] =  ($row['assessment_tag'] == 'RED') ? 'YES' : '';
            $data[ $index ][] =  $row['processing_date'];
            $data[ $index ][] =  $row['tsad_no'];
            $data[ $index ][] =  $row['estimated_time_departure'];
            $data[ $index ][] =  $row['estimated_time_arrival'];
			$data[ $index ][] =  $row['latest_estimated_time_arrival'];
            $data[ $index ][] =  $row['actual_time_arrival'];
            $data[ $index ][] =  $row['actual_berthing_date'];
            $data[ $index ][] =  $row['actual_discharge'];
            $data[ $index ][] =  $row['target_gatepass'];
            $data[ $index ][] =  $row['actual_gatepass'];
            $data[ $index ][] =  ( $row['actual_gatepass'] == "" || $row['actual_gatepass'] == null ) ? "" :  $row['gatepass_datetime_update'];
            $data[ $index ][] =  $row['validity_storage'];
            $data[ $index ][] =  $row['validity_demurrage'];
            $data[ $index ][] =  $row['revalidity_storage'];
            $data[ $index ][] =  $row['revalidity_demurrage'];
           // $data[ $index ][] =  $row['detention_validity'];
            $data[ $index ][] =  $row['pull_out'];
            $data[ $index ][] =  $row['detention_validity'];
            $data[ $index ][] =  $row['reason_of_delay_delivery'];

            if( $row['pull_out'] == null || $row['actual_discharge'] == null){
                $data[ $index ][] =  '-';
            }else{
                $data[ $index ][] =  $this->countingDays($row['actual_discharge'],$row['pull_out']) . ' Day(s)';
            }

            $data[ $index ][] =  $row['dismounted_cy'];
            $data[ $index ][] =  $row['dismounted_date'];
            $data[ $index ][] =  $row['unload'];
            $data[ $index ][] =  $row['return_date'];
            $data[ $index ][] =  $row['return_box_number'];
            $data[ $index ][] =  $row['return_summary_number'];
            $data[ $index ][] =  $row['incoterm'];
			$data[ $index ][] =  $row['registry_no'];


            $index++;
        }

        $this->returnedData = $data;
    }

    public function getData(){
        return $this->returnedData;
    }

    private function countingDays($start,$end){
        $startTimeStamp = strtotime($start);
        $endTimeStamp = strtotime($end);



        $timeDiff = abs($endTimeStamp - $startTimeStamp);

        $numberDays = $timeDiff/86400;  // 86400 seconds in one day

        // and you might want to convert to integer
       return $numberDays = intval($numberDays);
    }


}
