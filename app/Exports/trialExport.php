<?php

namespace App\Exports;

use App\bill_of_lading;
use App\bill_of_lading_commodity;
use App\Bill_of_Lading_Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class trialExport implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    //Shipment on board
    // public function collection()
    // {
    //     //
    //     $list_of_BOL = bill_of_lading::select('factory',
    //         'bl_no',
    //         'container_number',
    //         'connecting_vessel',
    //         'estimated_time_departure',
    // 
    //         'quantity',
    //         'pod',
    //
    //         'actual_discharge'
    //         )
    //         ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')->get();
    //     $data = [];
    //     $index = 0;
    //     foreach ($list_of_BOL as $row) {
           

    //         $data[$index][] = $row['factory'];
    //         $data[$index][] = $row['bl_no'];
    //         $data[$index][] = $row['container_number'];
    //       
    //        $data[$index][] = $row['estimated_time_departure'];
    //         $data[$index][] = $row['actual_time_arrival'];
    //         $data[$index][] = $row['quantity'];
    //         $data[$index][] = $row['pod'];
    //         $data[$index][] = $row['actual_discharge'];
    //         if($row['actual_time_arrival'] != null){
    //             $data[$index][] = explode('-',$row['estimated_time_departure'])[0];
    //             $data[$index][] = explode('-',$row['estimated_time_departure'])[1];
    //             $data[$index][] = explode('-',$row['estimated_time_departure'])[2];
    
    //         }
           

    //         $index++;
    //     }

    //     return collect($data);
    // }

    //VESSEL WAITING
    //  public function collection()
    // {
    //     //
    //     $list_of_BOL = bill_of_lading::select('factory',
    //         'bl_no',
    //         'container_number',
    //         'connecting_vessel',
    //         //'estimated_time_departure',
    //         'actual_time_arrival',
    //         'quantity',
    //         'pod',
    //         'actual_berthing_date'
    //         //'actual_discharge'
    //         )
    //         ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')->get();
    //     $data = [];
    //     $index = 0;
    //     foreach ($list_of_BOL as $row) {
           

    //         $data[$index][] = $row['factory'];
    //         $data[$index][] = $row['bl_no'];
    //         $data[$index][] = $row['container_number'];
    //         $data[$index][] = $row['connecting_vessel'];
    //        // $data[$index][] = $row['estimated_time_departure'];
    //         $data[$index][] = $row['actual_time_arrival'];
    //         $data[$index][] = $row['quantity'];
    //         $data[$index][] = $row['pod'];
    //         $data[$index][] = $row['actual_berthing_date'];
    //         if($row['actual_time_arrival'] != null){
    //             $data[$index][] = explode('-',$row['actual_time_arrival'])[0];
    //             $data[$index][] = explode('-',$row['actual_time_arrival'])[1];
    //             $data[$index][] = explode('-',$row['actual_time_arrival'])[2];
    
    //         }
           

    //         $index++;
    //     }

    //     return collect($data);
    // }

    //Not yet Discharge
    // public function collection()
    // {
    //     //
    //     $list_of_BOL = bill_of_lading::select('factory',
    //         'bl_no',
    //         'container_number',
    //         'connecting_vessel',
    //         'actual_berthing_date',
    //         'quantity',
    //         'pod',
    //         'actual_discharge'
    //         )
    //         ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')->get();
    //     $data = [];
    //     $index = 0;
    //     foreach ($list_of_BOL as $row) {
           

    //         $data[$index][] = $row['factory'];
    //         $data[$index][] = $row['bl_no'];
    //         $data[$index][] = $row['container_number'];
    //         $data[$index][] = $row['connecting_vessel'];
    //        // $data[$index][] = $row['estimated_time_departure'];
    //         $data[$index][] = $row['actual_berthing_date'];
    //         $data[$index][] = $row['quantity'];
    //         $data[$index][] = $row['pod'];
    //         $data[$index][] = $row['actual_discharge'];
    //         if($row['actual_berthing_date'] != null){
    //             $data[$index][] = explode('-',$row['actual_berthing_date'])[0];
    //             $data[$index][] = explode('-',$row['actual_berthing_date'])[1];
    //             $data[$index][] = explode('-',$row['actual_berthing_date'])[2];
    
    //         }
           

    //         $index++;
    //     }

    //     return collect($data);
    // }
    //Shipment on process
    // public function collection()
    // {
    //     //
    //     $list_of_BOL = bill_of_lading::select('factory',
    //         'bl_no',
    //         'container_number',
    //         'connecting_vessel',
    //         'actual_process',
    //         'quantity',
    //         'pod'
    //         )
    //         ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')->get();
    //     $data = [];
    //     $index = 0;
    //     foreach ($list_of_BOL as $row) {
           

    //         $data[$index][] = $row['factory'];
    //         $data[$index][] = $row['bl_no'];
    //         $data[$index][] = $row['container_number'];
    //         $data[$index][] = $row['connecting_vessel'];
    //        // $data[$index][] = $row['estimated_time_departure'];
    //         $data[$index][] = $row['actual_process'];
    //         $data[$index][] = $row['quantity'];
    //         $data[$index][] = $row['pod'];
    //         if($row['actual_process'] != null){
    //             $data[$index][] = explode('-',$row['actual_process'])[0];
    //             $data[$index][] = explode('-',$row['actual_process'])[1];
    //             $data[$index][] = explode('-',$row['actual_process'])[2];
    
    //         }
           

    //         $index++;
    //     }

    //     return collect($data);
    // }


    //Shipment without gatepass
    // public function collection()
    // {
    //     //
    //     $list_of_BOL = bill_of_lading::select('factory',
    //         'bl_no',
    //         'container_number',
    //         'connecting_vessel',
    //         'actual_discharge',
    //         'quantity',
    //         'pod',
    //         'actual_gatepass'
    //         )
    //         ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')->get();
    //     $data = [];
    //     $index = 0;
    //     foreach ($list_of_BOL as $row) {
           

    //         $data[$index][] = $row['factory'];
    //         $data[$index][] = $row['bl_no'];
    //         $data[$index][] = $row['container_number'];
    //         $data[$index][] = $row['connecting_vessel'];
    //        // $data[$index][] = $row['estimated_time_departure'];
    //         $data[$index][] = $row['actual_discharge'];
    //         $data[$index][] = $row['quantity'];
    //         $data[$index][] = $row['pod'];
    //         $data[$index][] = $row['actual_gatepass'];
    //         if($row['actual_discharge'] != null){
    //             $data[$index][] = explode('-',$row['actual_discharge'])[0];
    //             $data[$index][] = explode('-',$row['actual_discharge'])[1];
    //             $data[$index][] = explode('-',$row['actual_discharge'])[2];
    //         }
           

    //         $index++;
    //     }

    //     return collect($data);
    // }

    //Onhand Gatepass
    // public function collection()
    // {
    //     //
    //     $list_of_BOL = bill_of_lading::select('factory',
    //         'bl_no',
    //         'container_number',
    //         'connecting_vessel',
    //         'actual_gatepass',
    //         'quantity',
    //         'pod',
    //         'pull_out'
    //         )
    //         ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')->get();
    //     $data = [];
    //     $index = 0;
    //     foreach ($list_of_BOL as $row) {
           

    //         $data[$index][] = $row['factory'];
    //         $data[$index][] = $row['bl_no'];
    //         $data[$index][] = $row['container_number'];
    //         $data[$index][] = $row['connecting_vessel'];
    //        // $data[$index][] = $row['estimated_time_departure'];
    //         $data[$index][] = $row['actual_gatepass'];
    //         $data[$index][] = $row['quantity'];
    //         $data[$index][] = $row['pod'];
    //         $data[$index][] = $row['pull_out'];
    //         if($row['actual_gatepass'] != null){
    //             $data[$index][] = explode('-',$row['actual_gatepass'])[0];
    //             $data[$index][] = explode('-',$row['actual_gatepass'])[1];
    //             $data[$index][] = explode('-',$row['actual_gatepass'])[2];
    //         }
           

    //         $index++;
    //     }

    //     return collect($data);
    // }

    //Container IRS    
    // public function collection()
    // {
    //     //
    //     $list_of_BOL = bill_of_lading::select('factory',
    //         'bl_no',
    //         'container_number',
    //         'connecting_vessel',
    //         'dismounted_date',
    //         'quantity',
    //         'pod',
    //         'dismounted_cy'
    //         )
    //         ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')->get();
    //     $data = [];
    //     $index = 0;
    //     foreach ($list_of_BOL as $row) {
           

    //         $data[$index][] = $row['factory'];
    //         $data[$index][] = $row['bl_no'];
    //         $data[$index][] = $row['container_number'];
    //         $data[$index][] = $row['connecting_vessel'];
    //        // $data[$index][] = $row['estimated_time_departure'];
    //         $data[$index][] = $row['dismounted_date'];
    //         $data[$index][] = $row['quantity'];
    //         $data[$index][] = $row['pod'];
    //         $data[$index][] = $row['dismounted_cy'];
    //         if($row['dismounted_date'] != null){
    //             $data[$index][] = explode('-',$row['dismounted_date'])[0];
    //             $data[$index][] = explode('-',$row['dismounted_date'])[1];
    //             $data[$index][] = explode('-',$row['dismounted_date'])[2];
    //         }
           

    //         $index++;
    //     }

    //     return collect($data);
    // }

    //Containers not return   
    public function collection()
    {
        //
        $list_of_BOL = bill_of_lading::select('factory',
            'bl_no',
            'container_number',
            'container_type',
            'connecting_vessel',
            'unload',
            'quantity',
            'pod',
            'return_date'
            )
            ->join('containers', 'containers.bl_no_fk', 'bill_of_ladings.bl_no')->get();
        $data = [];
        $index = 0;
        foreach ($list_of_BOL as $row) {
           

            $data[$index][] = $row['factory'];
            $data[$index][] = $row['bl_no'];
            $data[$index][] = $row['container_number'];
            $data[$index][] = $row['container_number'];
            $data[$index][] = $row['connecting_vessel'];
           // $data[$index][] = $row['estimated_time_departure'];
            $data[$index][] = $row['unload'];
            $data[$index][] = $row['quantity'];
            $data[$index][] = $row['pod'];
            $data[$index][] = $row['return_date'];
            if($row['unload'] != null){
                $data[$index][] = explode('-',$row['unload'])[0];
                $data[$index][] = explode('-',$row['unload'])[1];
                $data[$index][] = explode('-',$row['unload'])[2];
            }
           

            $index++;
        }

        return collect($data);
    }

    //NOT YET DISCHARGE
    public function headings(): array
    {
        return [
            'factory',
            'bl_no',
            'container_number',
            'container_type',
            'connecting_vessel',
            'unload',
            'quantity',
            'pod',
            'return_date',
            'year',
            'month',
            'day',
        ];
    }

}
