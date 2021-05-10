@extends('layout.index2')

@section('body')

@slot('title')

@section('body')
<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Import Analysis</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-3 col-md-3">
                            <h3>Select Month</h3>
                            <div class="hr-line-dashed"></div>
                            <input type="text" id="date_month" readonly="true" v-model="dateMonth" placeholder="Please Select month..." class="form-control">
                        </div>
                    </div>
                    <hr class="style-one">
                    <div class="row">
                        <div class="row_scroll" style="height:auto;overflow-x:scroll">
                            <table class="table container_new_table table-bordered">
                                    <thead>
                                        <tr>
                                            <th> Date </th>
                                            <th v-for="cat in categories" :style="cat.indexOf('Sun') != -1 ? {'color':'red'} : {}"> @{{ cat }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <tr>
                                                <th class="stickycolumn" style="background-color:#336699;color:white"> @{{ series[0]['name'] }} </th>
                                                <td   v-for="(s,index) in series[0]['data']" :style="categories[index].indexOf('Sun') != -1 ? {'background-color':'#f5f5f6'} : {}"  class="cursor" @click="showGatepass(series[0]['daily_date'][index])"> @{{ s }} </td>
                                            </tr>
                                            <tr>
                                                <th class="stickycolumn" style="background-color:#e88d67;color:white"> @{{ series[1]['name'] }} </th>
                                                <td v-for="(s,index) in series[1]['data']" :style="categories[index].indexOf('Sun') != -1 ? {'background-color':'#f5f5f6'} : {}"> @{{ s }}</td>
                                            </tr>
                                            <tr>
                                                <th class="stickycolumn" style="background-color:#41d3bd;color:white"> @{{ series[2]['name'] }} </th>
                                                <td v-for="(s,index) in series[2]['data']" :style="categories[index].indexOf('Sun') != -1 ? {'background-color':'#f5f5f6'} : {}"> @{{ s }}</td>
                                            </tr>
                                    </tbody>
                                </table>
                                {{-- ff3366 --}}
                        </div>
                        <div id="container" style="min-width: 310px; height: 600px; margin: 0 auto"></div>
                    </div>
                    <hr class="style-one">
                    <div class="row">
                        <div class="row_scroll" style="height:auto;overflow-x:scroll">
                            <table class="table container_new_table table-bordered">
                                    <thead>
                                        <tr>
                                            <th> Date </th>
                                            <th v-for="cat in categories" :style="cat.indexOf('Sun') != -1 ? {'color':'red'} : {}"> @{{ cat }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <tr>
                                                <th class="stickycolumn" style="background-color:#336699;color:white"> @{{ series_2nd[0]['name'] }} </th>
                                                <td v-for="(s,index) in series_2nd[0]['data']" :style="categories[index].indexOf('Sun') != -1 ? {'background-color':'#f5f5f6'} : {}"> @{{ s }}</td>
                                            </tr>
                                            <tr>
                                                <th class="stickycolumn" style="background-color:#ff3366;color:white"> @{{ series_2nd[1]['name'] }} </th>
                                                <td v-for="(s,index) in series_2nd[1]['data']" :style="categories[index].indexOf('Sun') != -1 ? {'background-color':'#f5f5f6'} : {}"> @{{ s }}</td>
                                            </tr>
                                    </tbody>
                                </table>
                        </div>
                        <div id="container1" style="min-width: 310px; height: 600px; margin: 0 auto"></div>
                    </div>
                    <hr class="style-one">
                    <div class="row" v-if="gatepass_series.length > 0">
                        <div class="row_scroll" style="height:auto;overflow-x:scroll">
                            <h2 style="margin:auto;text-align:center">Shipping lines arrived at port
                                <br>
                                <small>(# of containers arrived per day)  </small>
                                <br>
                                <small>place your mouse pointer in the value to check the number of 20 GP and 40 HC containers  </small>
                            </h2>
                            <br>
                            <table class="table container_new_table table-bordered">
                                <thead>
                                    <tr>
                                        <th> Date </th>
                                        <th v-for="cat in categories" :style="cat.indexOf('Sun') != -1 ? {'color':'red'} : {}"> @{{ cat }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(gs,index) in gatepass_series">
                                        {{-- ,{'color':'white'} --}}
                                        <th class="stickycolumn" style="color:white" :style="{'background-color': container_type_colors[index] }"> @{{ gs['name'] }} </th>
                                        <td  v-for="(s,index2) in gs['data']" :style="categories[index2].indexOf('Sun') != -1 ? {'background-color':'#f5f5f6'} : {}"> <span :class="(s > 0) ? 'tooltip' : ''"   :title="(s > 0) ? gs.containers[index2] : null" :style="(s > 0) ? {'color':'red','font-size':'15px'} : {}" ><span v-if="s == 0">-</span><span v-else>@{{ s }}</span></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr class="style-one">
                        <div class="tabs-container">
                            <h3>Select Container Type : </h3>
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#20GP"> 20 GP</a></li>
                                <li class=""><a data-toggle="tab" href="#40HC">40HC</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="20GP" class="tab-pane active">
                                        <div id="container2" style="min-width: 310px; height: 600px; margin: 0 auto"></div>
                                        
                                </div>
                                <div id="40HC" class="tab-pane">
                                    <div class="panel-body">
                                            <div id="container2_1" style="min-width: 310px; height: 600px; margin: 0 auto"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<div class="modal inmodal fade" id="myModal5" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg modal-request-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Details Summary</h4>
                {{-- <small class="font-bold">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</small> --}}
            </div>
            <div class="modal-body" style="height:auto;">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-3"> Discharge GatePass</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-4">Gatepass Delivery</a></li>
                        
                    </ul>
                    <div class="tab-content">
                        <div id="tab-3" class="tab-pane active">
                                <div id="container4" style="min-width: 310px; height: 600px; margin: 0 auto"></div>
                                <hr class="style-one">
                                
                        </div>
                        <div id="tab-4" class="tab-pane">
                            <div class="panel-body">
                                <div id="container5" style="min-width: 310px; height: 600px; margin: 0 auto"></div>
                                <hr class="style-one">
                            </div>
                        </div>
                    </div>
                </div>
            
                <table v-if="series_dischare_gatepass_monitoring_table.length > 0" id="detail_obj" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Factory</th>
                                <th>BL #</th>
                                <th>CONTAINER #</th>
                                <th>COMMODITIES</th>
                                <th>SHIPPING LINE</th>
                                <th>SIZE</th>
                                <th>POD</th>
                                <th>REASON_OF_DELAY</th>
                                <th>ACTUAL GATEPASS</th>
                                <th>ACTUAL DELIVERY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="monitoring in series_dischare_gatepass_monitoring_table">
                                <td>@{{ monitoring.factory }}</td>
                                <td class="">@{{ monitoring.bl_no_fk }}</td>
                                <td>@{{ monitoring.container_number }}</td>
                                <td>@{{ monitoring.commodities }}</td>
                                <td>@{{ monitoring.shipping_line }}</td>
                                <td>@{{ monitoring.container_type }}</td>
                                <td>@{{ monitoring.pod }}</td>
                                <td>@{{ monitoring.reason_of_delay_delivery }}</td>
                                <td>@{{ monitoring.actual_gatepass }}</td>
                                <td>@{{ monitoring.pull_out }}</td>
                            </tr>
                        </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vuejsscript')

<script type="text/javascript" src="{{asset('/js/tooltipster.bundle.min.js')}}"></script>
<script src="{{asset('/js/vuejs/analysis.js')}}"></script>
@endsection

@section('headscript')
<link rel="stylesheet" type="text/css" href="{{asset('/css/tooltipster.bundle.min.css')}}" />    
<style>
.tooltip {
    opacity: 1;
}
.tooltip-inner {
    white-space: pre-line;
}
</style>
@endsection
