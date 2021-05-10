<?php

namespace App\Exports;

use App\bill_of_lading;
use App\bill_of_lading_commodity;
use App\Bill_of_Lading_Invoice;
use App\Factory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use App\libraries\ContainerDischarge;
use App\Libraries\Adddate;


class TransportScheduleExport implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */

    private $date_filter;

    public function __construct($date_filter){
        $this->date_filter = $date_filter;
    }

    public function collection()
    {
        //

        $factories = Factory::select('factory_id')->pluck('factory_id');
        $factories[] = '';
        //'2020-01-15' date('Y-m-d')
        $containerdischarge = new ContainerDischarge($this->date_filter, Adddate::newdate($this->date_filter,'+1 week'));

        $containerdischarge->get_discharge();
        //return $containerdischarge->get_data();
        $containerdischarge->get_eta();

        $containerdischarge->get_data();


        $data = [];
        $index = 0;
        if(count($containerdischarge->get_data()) > 0){
            $datas = $containerdischarge->get_data();
            // $price = array_column($datas, 'status');

            // array_multisort($price, SORT_ASC, $datas);

            //MULTI SORT

            array_multisort(
                array_column($datas, 'status'), SORT_ASC,
                array_column($datas, 'actual_discharge'), SORT_ASC,
                $datas
            );


            foreach($datas as $key=>$val){

                $data[$key][] = $val['status'];
                $data[$key][] = $val['actual_discharge'];
                $data[$key][] = $val['factory'];
                $data[$key][] = $val['shipping_line'];
                $data[$key][] = $val['container_number'];
                $data[$key][] = $val['commodity'];
                $data[$key][] = $val['container_type'];
                $data[$key][] = $val['pod'];
                $data[$key][] = $val['dispatched_date'];

            }


        }

        return collect($data);
    }


    public function headings(): array
    {
        return ['STATUS','ACTUAL DISCHARGE','FACTORY','SHIPPING LINE','CONTAINER NUMBER','COMMODITY','CONTAINER SIZE','PORT','DISPATCH DATE'];
    }


}
