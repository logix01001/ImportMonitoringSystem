<?php

namespace App\Classfile;

use App\container;
use App\Bill_of_Lading;
use App\Holiday;


class SplitMain {

    protected $main;
    public function __construct(String $bl_no,String $container_no){
        $bl_nos = explode(',',$bl_no);
        $container = new container;

        $main = Container::select(
            $container->split_details
            )
            ->join('bill_of_ladings','bl_no','bl_no_fk')
            ->whereIn('bl_no_fk',$bl_nos)
            ->whereContainerNumber($container_no)
            ->whereQuantity(1)
            ->first();

        $this->main = $main;
    }

    public function getdata(){
        return $this->main;
    }


}
