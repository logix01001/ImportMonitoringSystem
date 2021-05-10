<?php

namespace App;

use App\Classfile\SplitMain;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class container extends Model
{
    //
    protected $fillable = ['actual_discharge','gatepass_datetime_update','gatepass_update_by'];


    public $split_details = [
        'containers.id as cid',
        'bill_of_ladings.id as bid',
        'bl_no','container_number',
        'estimated_time_arrival',
        'actual_time_arrival',
        'actual_berthing_date',
        'actual_discharge',
        'dismounted_cy',
        'dismounted_date',
        'split_bl_no_fk',
        'validity_storage',
        'validity_demurrage',
        'revalidity_storage',
        'revalidity_demurrage',
        'trucker',
        'pull_out',
        'pull_out_time',
        'detention_validity',
        'unload',
        'reason_of_delay_delivery',
        'return_cy',
        'return_date',
        'return_box_number',
        'return_summary_number',
        'safe_keep',
        'dispatched_date',
        'target_dispatch_date',
        'quantity'
    ];
    public static function get_all_discharge($date, $gatepass = false,$factory = null, $size = null,$port = null){

        $container = new self;
        $container = $container->join('bill_of_ladings','bill_of_ladings.bl_no','containers.bl_no_fk')
        ->whereNull('pull_out')
        ->where('quantity',1);
        if($gatepass){
            $container = $container->where('actual_discharge',$date)->whereNotNull('actual_gatepass');
        }else{
            $container = $container->where('actual_discharge',$date);
        }

        if($factory != null){
            $container = $container->where('factory',$factory);
        }

        if($size != null){
            $container = $container->where('container_type','like', $size . '%');
        }

        if($port){
            $container = $container->where('pod',$port);
        }



        return $container->count();


    }

    public static function get_split_containers($bl,$container_number){
        return self::where('bl_no_fk',$bl)->where('container_number',$container_number)->first();
    }


    public static function save_split($ids,$column,$value){
        if($column == 'actual_gatepass'){
            self::whereIn('id',$ids)->update([
                $column => $value,
                'gatepass_datetime_update' => \date('Y-m-d H:i:s'),
                'gatepass_update_by' =>  Session::get('employee_number'),
                ]);

        }else{
            return self::whereIn('id',$ids)->update([$column => $value]);
        }
    }


    public static function split_lists(){
        $container = new self;

        $bls = self::select($container->split_details)
        ->join('bill_of_ladings','bl_no','bl_no_fk')
        ->whereQuantity(0)
        ->whereNotNull('split_bl_no_fk')
        ->orderBy('containers.id', 'DESC')
        ->get();

        $index = 0;
        foreach($bls as $bl){
            $main = new SplitMain($bl['split_bl_no_fk'],$bl['container_number']);

            if($main->getdata() == null){
                unset($bls[$index]);
            }else{

                $bl['difference'] = array_diff(json_decode($main->getdata(),true),json_decode($bl,true));
                if(self::check_split_field($bl['difference']) == false){
                    unset($bls[$index]);
                }else{
                    $bl['main'] = $main->getdata();
                }
                // if(count($bl['difference']) == 0 || count($bl['difference']) == 2){
                //     unset($bls[$index]);
                // }

            }

            $index++;
        }

        return $bls;

    }

    private static function check_split_field($difference){
        $fields = [
            //'estimated_time_arrival',
            //'actual_time_arrival',
            //'actual_berthing_date',
            'actual_discharge',
            'dismounted_cy',
            'dismounted_date',
            'validity_storage',
            'validity_demurrage',
            'revalidity_storage',
            'revalidity_demurrage',
            'trucker',
            'pull_out',
            'pull_out_time',
            'detention_validity',
            'unload',
            'reason_of_delay_delivery',
            'return_cy',
            'return_date',
            'return_box_number',
            'return_summary_number',
            'safe_keep',
            'dispatched_date',
            'target_dispatch_date'
        ];

        foreach(array_keys($difference) as $key){
            if(in_array($key,$fields)){
                return true;
                break;
            }
        }
        return false;
    }
}
