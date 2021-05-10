@extends('layout.index2')

@section('body')

<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">

                <h5>Importation Monitoring</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>


            <div class="ibox-content">
                <div class="hr-line-dashed"></div>
                <div class="row">
                    <span class="pull-left col-lg-12">
                        <h2>
                            Tally:
                        </h2>
                        <div class="row">
                                <div class="col-lg-2">
                                        <span class="h4 font-bold m-t block">@{{totalForE2M}}</span>
                                        <small class="text-muted m-b block filterText" @click="filterOff('showingE2m')">E2M not yet Approve (/BL)</small>
                                </div>
                                <div class="col-lg-2">
                                    <span class="h4 font-bold m-t block">@{{totalForGatepass}}</span>
                                    <small class="text-muted m-b block filterText" @click="filterOff('showingGatepass')">Shipment on process (/BL)</small>
                                </div>
                                <div class="col-lg-2">
                                        <span class="h4 font-bold m-t block">@{{totalWithoutGatepass}}</span>
                                        <small class="text-muted m-b block filterText" @click="filterOff('showingGatepass')">Without Gatepass (/Container)</small>
                                </div>
                                <div class="col-lg-2">
                                        <span class="h4 font-bold m-t block">@{{totalonHand}}</span>
                                        <small class="text-muted m-b block filterText" @click="filterOff('showingOnHand')">On-hand (/Container)</small>
                                </div>
                                <div class="col-lg-2">
                                        <span class="h4 font-bold m-t block">@{{totalDelivered}}</span>
                                        <small class="text-muted m-b block filterText" @click="filterOff('showingDelivered')">Delivered (/Container)</small>
                                </div>
                                <div class="col-lg-2">
                                        <span class="h4 font-bold m-t block">@{{totalCompleted}}</span>
                                        <small class="text-muted m-b block filterText" @click="filterOff('showingCompleted')">Completed (/BL)</small>
                                </div>
                                {{--

                                 <div class="col-lg-2">
                                        <span class="h4 font-bold m-t block">@{{totalRoundUse}}</span>
                                        <small class="text-muted m-b block filterText" @click="filterOff('showingContainerMovement')">NOT YET RETURN</small>
                                </div>
                                <div class="col-lg-2">
                                        <span class="h4 font-bold m-t block">@{{totalCompleted}}</span>
                                        <small class="text-muted m-b block filterText" @click="filterOff('showingCompleted')">Completed</small>
                                </div>
                                <div class="col-lg-2">
                                        <span class="h4 font-bold m-t block">@{{totalSouth}}</span>
                                        <small class="text-muted m-b block filterText" @click="filterOff('showingSouth')">SOUTH</small>
                                        <span class="h4 font-bold m-t block">@{{totalNorth}}</span>
                                        <small class="text-muted m-b block filterText" @click="filterOff('showingNorth')">NORTH</small>
                                </div> --}}
                                {{-- <div class="col-lg-2">
                                        <span class="h4 font-bold m-t block">@{{list_of_BOL.length}} / @{{totalRecord}}</span>
                                        <small class="text-muted m-b block filterText" @click="filterOff('showingRecords')">SHOWING RECORD</small>
                                </div> --}}

                        </div>
                        <div class="row">
                                <div class="col-lg-2">

                                    <span class="h4 font-bold m-t block">@{{totalSouth}}</span>
                                    <small class="text-muted m-b block filterText" @click="filterOff('showingSouth')">SOUTH</small>
                                    <span class="h4 font-bold m-t block">@{{totalNorth}}</span>
                                    <small class="text-muted m-b block filterText" @click="filterOff('showingNorth')">NORTH</small>

                                </div>
                                <div class="col-lg-2">

                                    <span class="h4 font-bold m-t block">@{{list_of_BOL.length}} / @{{totalRecord}}</span>
                                    <small class="text-muted m-b block filterText" @click="filterOff('showingRecords')">SHOWING RECORD</small>

                                </div>
                        </div>
                    </span>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="row" style="text-align:center">
                    <div class="col-sm-1 filterText"  @click="filterOff('showingTransit')">
                        <h3 class="m-b-xs " >
                            @{{ totalForTransit }}
                        </h3>
                        <img class="numberCircle"  src="{{asset('/img/arrival.png') }}"> <br> <b> In transit </b>
                    </div>
                    <div class="col-sm-1 filterText"  @click="filterOff('showingArrived')">
                        <h3 class="m-b-xs" >
                            @{{ totalForArrived }}
                        </h3>
                        <img class="numberCircle"  src="{{asset('/img/arrived.png') }}"> <br> <b> Arrived </b>
                    </div>
                    <div class="col-sm-1 filterText"  @click="filterOff('showingBerthed')">

                        <h3 class="m-b-xs" >
                            @{{ totalForBerthed }}
                        </h3>
                        <img class="numberCircle"  src="{{asset('/img/berthed.png') }}"> <br> <b> Berthed </b>
                    </div>
                    <div class="col-sm-1 filterText"  @click="filterOff('showingDischarge')">
                        <h3 class="m-b-xs" >
                            @{{ totalForDischarge }}
                        </h3>
                        <img class="numberCircle"  src="{{asset('/img/discharge.png') }}"> <br> <b> Discharged </b>
                    </div>
                    <div class="col-sm-1 filterText"  @click="filterOff('showingOnProcess')">
                        <h3 class="m-b-xs" >
                            @{{ totalForOnProcess }}
                        </h3>
                        <img class="numberCircle"  src="{{asset('/img/process.png') }}"> <br> <b> On Process </b>
                    </div>
                    <div class="col-sm-1 filterText"  @click="filterOff('showingWithGatepass')">
                        <h3 class="m-b-xs" >
                            @{{ totalForWithGatepass }}
                        </h3>
                        <img class="numberCircle"  src="{{asset('/img/gatepass.png') }}"> <br> <b> W/ Gatepass </b>
                    </div>
                    <div class="col-sm-1 filterText"  @click="filterOff('showingPullOut')">
                        <h3 class="m-b-xs" >
                            @{{ totalForPullout }}
                        </h3>
                        <img class="numberCircle"  src="{{asset('/img/pullout.png') }}"> <br> <b> Pulled Out </b>
                    </div>
                    <div class="col-sm-1 filterText"  @click="filterOff('showingUnload')">
                        <h3 class="m-b-xs" >
                            @{{ totalForUnload }}
                        </h3>
                        <img class="numberCircle"  src="{{asset('/img/unloaded.png') }}"> <br> <b> Unloaded </b>
                    </div>
                    <div class="col-sm-1 filterText"  @click="filterOff('showingReturn')">
                        <h3 class="m-b-xs" >
                            @{{ totalForReturn }}
                        </h3>
                        <img class="numberCircle"  src="{{asset('/img/returned.png') }}"> <br> <b> Returned </b>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
            <div class="ibox-content m-b-sm border-bottom">
                    <div class="row">
                        <div class="col-sm-6 col-lg-12">
                            <div class="row">
                                        {{-- <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="control-label" for="product_name">Month</label>
                                                <input type="text" class="form-control" v-model="filter_month"  id="filter_month">
                                            </div>
                                        </div> --}}
                                        <div class="col-lg-2 col-sm-12">
                                                <div class="form-group">
                                                    <label class="control-label" for="product_name">Consignee</label>
                                                    <select @change="changeFactorySelected" name="" class="form-control" v-model="selected_factory" id="">
                                                        <option value=""> ---- All ---- </option>
                                                        <option v-for="factory in factories" :value="factory.factory_id">@{{factory.factory_id}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="control-label" for="product_name">Search By</label>
                                                <select name="status" id="status" v-model="filter_search" class="form-control">
                                                    <option value=""></option>
                                                    <option value="BL">BL #</option>
                                                    <option value="C">Container #</option>
                                                    <option value="I">Invoice #</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="input-group" style="margin-top:25px;">

                                                <input type="text" placeholder="Search" v-model="search_bl_no" class="input-sm form-control">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-sm btn-primary" :disabled="search_bl_no.trim().length == 0" @click="searchBL"><i class="fa fa-search"></i> Search </button>
                                                    <button type="button" id="loading-example-btn" class="btn btn-success btn-sm" @click="refresh"><i class="fa fa-sync-alt "></i> Refresh</button>
                                                </span>
                                            </div>
                                        </div>
                            </div>
                        </div>


                    </div>


                </div>
                <div class="row table-responsive" id="tablerow">


<!-- Trigger to open Modal -->


                    <table  class="table  table-hover table-bordered-dark indexSearchShipment   toggle-arrow-tiny">
                            <thead>
                                <tr>
                                    {{-- <th  class="column50"></th> --}}
                                    <th>Consignee</th>
                                    <th>BL #</th>
                                    <th>Commodities</th>
                                    <th>Volume</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr v-if="list_of_BOL.length == 0">
                                            <td colspan="7">
                                                <div class="alert alert-danger">
                                                  <center> No Record found </center>
                                                </div>
                                            </td>
                                    </tr>
                                    <template v-if="showingRecords"  v-for="(BOL,index) in list_of_BOL">

                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                    {{-- @{{BOL.total_container_discharged}} --}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                    <span v-if="BOL.container_numbers.length == 0">
                                                        No Containers Encoded.
                                                    </span>
                                                    <span v-else class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                                <span class="dots"></span>
                                                                <template v-if="BOL.actual_time_arrival != null">
                                                                    <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                                </template>
                                                                <template v-if="BOL.actual_time_arrival == null">
                                                                    <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                                </template>
                                                                <span class="dots"></span>
                                                                <template v-if="BOL.actual_berthing_date != null">
                                                                    <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                                </template>
                                                                <template v-if="BOL.actual_berthing_date == null">
                                                                    <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                                </template>
                                                                <span class="dots"></span>
                                                                <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                    <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                                </template>
                                                                <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                    <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                                </template>
                                                                <span class="dots"></span>
                                                                <template v-if="BOL.actual_process != null">
                                                                    <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                                </template>
                                                                <template v-if="BOL.actual_process == null">
                                                                    <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                                </template>
                                                                <span class="dots"></span>
                                                                <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                    <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                                </template>
                                                                <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                                </template>
                                                                <span class="dots"></span>
                                                                <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                    <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                                </template>
                                                                <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                    <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                                </template>
                                                                <span class="dots"></span>
                                                                <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                    <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                                </template>
                                                                <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                    <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                                </template>
                                                                <span class="dots"></span>
                                                                <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                    <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                                </template>
                                                                <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                    <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                                </template>


                                                                {{-- <template v-if="BOL.e2m == null">
                                                                        <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                        <span class="dots"></span>
                                                                        <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                        <span class="dots"></span>

                                                                        <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                        <span class="dots"></span>
                                                                        <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                </template>
                                                                <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                        <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                        <span class="dots"></span>
                                                                        <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                        <span class="dots"></span>

                                                                        <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                        <span class="dots"></span>
                                                                        <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                </template>

                                                                <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                        <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                        <span class="dots"></span>
                                                                        <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                        <span class="dots"></span>
                                                                        <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                        <span class="dots"></span>
                                                                        <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                </template>
                                                                <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                        <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                        <span class="dots"></span>
                                                                        <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                        <span class="dots"></span>

                                                                        <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                        <span class="dots"></span>
                                                                        <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                                </template> --}}

                                                    </span>
                                                </td>

                                            </tr>

                                            {{-- <tr v-if="opened.includes(BOL.id)">
                                                <td colspan="10">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th style="color:#eaba6b">Qty</th>
                                                                <th style="color:green">Container #</th>
                                                                <th style="color:violet">
                                                                    Discharge


                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size:15px">
                                                            <tr v-for="(container,index2) in BOL.container_numbers" :style="container.quantity == 0 ? {'background-color': '#FAB341'} : {}" >
                                                                    <td >@{{container.quantity}}</td>
                                                                    <td @dblclick="splitBL_NO(container.id,container.split_bl_no_fk,container.quantity)">@{{container.container_number}}</td>

                                                                    <td >@{{container.actual_discharge}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                </tr> --}}
                                    </template>
                                    <template v-if="showingE2m"  v-for="(BOL,index) in listForE2M">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>

                                            </tr>
                                    </template>
                                    <template v-if="showingGatepass"  v-for="(BOL,index) in listForGatepass">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingOnHand"  v-for="(BOL,index) in listForOnHand">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>

                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingDelivered"  v-for="(BOL,index) in listForDelivered">
                                        <tr >
                                            {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                            </td> --}}
                                            <td>
                                                @{{BOL.factory}}
                                            </td>
                                            <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                @{{BOL.bl_no}}
                                            </td>
                                            <td >
                                                <span v-for="(c,index) in BOL.commodities">
                                                    @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                </span>
                                            </td>
                                            <td>
                                                @{{BOL.container_numbers.length}}
                                            </td>
                                            <td>
                                                    <span class="status_request column500">
                                                            <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                        <span class="dots"></span>
                                                        <template v-if="BOL.actual_time_arrival != null">
                                                            <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                        </template>
                                                        <template v-if="BOL.actual_time_arrival == null">
                                                            <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                        </template>
                                                        <span class="dots"></span>
                                                        <template v-if="BOL.actual_berthing_date != null">
                                                            <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                        </template>
                                                        <template v-if="BOL.actual_berthing_date == null">
                                                            <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                        </template>
                                                        <span class="dots"></span>
                                                        <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                            <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                        </template>
                                                        <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                            <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                        </template>
                                                        <span class="dots"></span>
                                                        <template v-if="BOL.actual_process != null">
                                                            <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                        </template>
                                                        <template v-if="BOL.actual_process == null">
                                                            <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                        </template>
                                                        <span class="dots"></span>
                                                        <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                            <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                        </template>
                                                        <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                            <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                        </template>
                                                        <span class="dots"></span>
                                                        <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                            <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                        </template>
                                                        <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                            <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                        </template>
                                                        <span class="dots"></span>
                                                        <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                            <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                        </template>
                                                        <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                            <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                        </template>
                                                        <span class="dots"></span>
                                                        <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                            <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                        </template>
                                                        <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                            <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                        </template>


                                                        {{-- <template v-if="BOL.e2m == null">
                                                                <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                <span class="dots"></span>
                                                                <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                <span class="dots"></span>

                                                                <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                <span class="dots"></span>
                                                                <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                        </template>
                                                        <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                <span class="dots"></span>
                                                                <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                <span class="dots"></span>

                                                                <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                <span class="dots"></span>
                                                                <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                        </template>

                                                        <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                <span class="dots"></span>
                                                                <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                <span class="dots"></span>
                                                                <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                <span class="dots"></span>
                                                                <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                        </template>
                                                        <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                <span class="dots"></span>
                                                                <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                <span class="dots"></span>

                                                                <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                <span class="dots"></span>
                                                                <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                        </template> --}}

                                                </span>
                                            </td>
                                        </tr>
                                    </template>
                                    <template v-if="showingCompleted"  v-for="(BOL,index) in listForCompleted">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingSouth"  v-for="(BOL,index) in listForSouth">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingNorth"  v-for="(BOL,index) in listForNorth">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingTransit"  v-for="(BOL,index) in listForTransit">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingArrived"  v-for="(BOL,index) in listForArrived">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingBerthed"  v-for="(BOL,index) in listForBerthed">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingDischarge"  v-for="(BOL,index) in listForDischarge">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingOnProcess"  v-for="(BOL,index) in listForOnProcess">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingWithGatepass"  v-for="(BOL,index) in listForWithGatepass">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingPullOut"  v-for="(BOL,index) in listForPullOut">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingUnload"  v-for="(BOL,index) in listForUnload">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                                    <template v-if="showingReturn"  v-for="(BOL,index) in listForReturn">
                                            <tr >
                                                {{-- <td  @dblclick="toggle(BOL.id)" style="cursor:pointer" >
                                                    <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                                    <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                                </td> --}}
                                                <td>
                                                    @{{BOL.factory}}
                                                </td>
                                                <td  @dblclick="openModal(BOL.id)" style="cursor:pointer">
                                                    @{{BOL.bl_no}}
                                                </td>
                                                <td >
                                                    <span v-for="(c,index) in BOL.commodities">
                                                        @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    @{{BOL.container_numbers.length}}
                                                </td>
                                                <td>
                                                        <span class="status_request column500">
                                                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_time_arrival != null">
                                                                <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_time_arrival == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_berthing_date != null">
                                                                <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_berthing_date == null">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_discharged == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_discharged != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.actual_process != null">
                                                                <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <template v-if="BOL.actual_process == null">
                                                                <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_with_gatepass == BOL.container_numbers.length">
                                                                <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_pullout == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_pullout != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_container_unload == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_container_unload != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                                            </template>
                                                            <span class="dots"></span>
                                                            <template v-if="BOL.total_round_use == BOL.container_numbers.length">
                                                                <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                                            </template>
                                                            <template v-if="BOL.total_round_use != BOL.container_numbers.length">
                                                                <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                                            </template>


                                                            {{-- <template v-if="BOL.e2m == null">
                                                                    <i class="fa fa-ship fa-3x text-legend-light-blue numberCircle"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt  fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="BOL.e2m != null && BOL.total_with_gatepass != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-3x text-info numberCircle"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>

                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use != BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-shipping-fast fa-3x text-warning numberCircle" ></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                            </template>
                                                            <template v-if="(BOL.e2m != null && BOL.total_with_gatepass == BOL.container_numbers.length) && BOL.total_round_use == BOL.container_numbers.length">
                                                                    <i class="fa fa-ship fa-2x text-primary  numberCircle"  style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-ticket-alt fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>

                                                                    <i class="fa fa-shipping-fast fa-2x text-primary numberCircle" style="opacity: 0.5"></i>
                                                                    <span class="dots"></span>
                                                                    <i class="fa fa-check fa-3x text-legend-light-success numberCircle" ></i>
                                                            </template> --}}

                                                    </span>
                                                </td>
                                            </tr>
                                    </template>
                            </tbody>
                    </table>

                </div>
                <div class="hr-line-dashed"></div>
                <div class="row">
                    <div class="col-lg-2">

                        <label for="">  Number of record </label>

                        <select class="form-control" v-model="numberofTake" id="">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                                <option value="300">300</option>
                                <option value="ALL"> --- All --- </option>
                        </select>

                    </div>
                    <div class="col-lg-10">
                            <button :disabled="showprogress" class="btn btn-primary btn-block m-t" @click="getRecord(numberofTake)"><i class="fa fa-arrow-down"></i> Show More</button>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<div v-if="showModal" data-backdrop="static" data-keyboard="false" class="modal inmodal fade" id="BL_DETAILS" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-xll">
            <div class="modal-content">
                <div class="modal-header">
                    <button  type="button" @click="closeModal" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

                    <h4 class="modal-title">@{{ list_of_BOL[selectedIndex].bl_no}}</h4>
                    <small class="font-bold">@{{list_of_BOL[selectedIndex].factory}}</small>

                            {{-- <template v-if="list_of_BOL[selectedIndex].e2m == null">
                                <h4 class="text-legend-light-blue"><i class="fa fa-ship"></i> For E2M and arrival updating</h4>
                            </template>
                            <template v-if="list_of_BOL[selectedIndex].e2m != null && (list_of_BOL[selectedIndex].total_with_gatepass != list_of_BOL[selectedIndex].container_numbers.length)">
                                    <h4 class="text-info"><i class="fa fa-ticket-alt"></i> For Gatepass updating</h4>
                            </template>
                            <template v-if="(list_of_BOL[selectedIndex].total_with_gatepass == list_of_BOL[selectedIndex].container_numbers.length) && list_of_BOL[selectedIndex].total_round_use != list_of_BOL[selectedIndex].container_numbers.length">
                                    <h4 class="text-warning"><i class="fa fa-shipping-fast"></i> For Validation and Container Movement</h4>
                            </template>
                            <template v-if="(list_of_BOL[selectedIndex].e2m != null && list_of_BOL[selectedIndex].actual_gatepass != null) && list_of_BOL[selectedIndex].total_round_use == list_of_BOL[selectedIndex].container_numbers.length">
                                    <h4 class="text-legend-light-success"> <i class="fa fa-check ">Completed</i> </h4>
                            </template> --}}
                            <span class="status_request column500" style="margin: auto;">
                                <img class="numberCircle"  src="{{asset('/img/arrival.png') }}">
                                <span class="dots"></span>
                                <template v-if="list_of_BOL[selectedIndex].actual_time_arrival != null">
                                    <img class="numberCircle "   src="{{asset('/img/arrived.png') }}">
                                </template>
                                <template v-if="list_of_BOL[selectedIndex].actual_time_arrival == null">
                                    <img class="numberCircle  indexStatus_null"  src="{{asset('/img/arrived.png') }}">
                                </template>
                                <span class="dots"></span>
                                <template v-if="list_of_BOL[selectedIndex].actual_berthing_date != null">
                                    <img class="numberCircle " src="{{asset('/img/berthed.png') }}">
                                </template>
                                <template v-if="list_of_BOL[selectedIndex].actual_berthing_date == null">
                                    <img class="numberCircle  indexStatus_null"  src="{{asset('/img/berthed.png') }}">
                                </template>
                                <span class="dots"></span>
                                <template v-if="list_of_BOL[selectedIndex].total_container_discharged == list_of_BOL[selectedIndex].container_numbers.length">
                                    <img class="numberCircle "  src="{{asset('/img/discharge.png') }}">
                                </template>
                                <template v-if="list_of_BOL[selectedIndex].total_container_discharged != list_of_BOL[selectedIndex].container_numbers.length">
                                    <img class="numberCircle  indexStatus_null"   src="{{asset('/img/discharge.png') }}">
                                </template>
                                <span class="dots"></span>
                                <template v-if="list_of_BOL[selectedIndex].actual_process != null">
                                    <img class="numberCircle "  src="{{asset('/img/process.png') }}">
                                </template>
                                <template v-if="list_of_BOL[selectedIndex].actual_process == null">
                                    <img class="numberCircle  indexStatus_null" src="{{asset('/img/process.png') }}">
                                </template>
                                <span class="dots"></span>
                                <template v-if="list_of_BOL[selectedIndex].total_with_gatepass == list_of_BOL[selectedIndex].container_numbers.length">
                                    <img class="numberCircle "  src="{{asset('/img/gatepass.png') }}">
                                </template>
                                <template v-if="list_of_BOL[selectedIndex].total_with_gatepass != list_of_BOL[selectedIndex].container_numbers.length">
                                    <img class="numberCircle  indexStatus_null"  src="{{asset('/img/gatepass.png') }}">
                                </template>
                                <span class="dots"></span>
                                <template v-if="list_of_BOL[selectedIndex].total_container_pullout == list_of_BOL[selectedIndex].container_numbers.length">
                                    <img class="numberCircle " src="{{asset('/img/pullout.png') }}">
                                </template>
                                <template v-if="list_of_BOL[selectedIndex].total_container_pullout != list_of_BOL[selectedIndex].container_numbers.length">
                                    <img class="numberCircle  indexStatus_null"   src="{{asset('/img/pullout.png') }}">
                                </template>
                                <span class="dots"></span>
                                <template v-if="list_of_BOL[selectedIndex].total_container_unload == list_of_BOL[selectedIndex].container_numbers.length">
                                    <img class="numberCircle " src="{{asset('/img/unloaded.png') }}">
                                </template>
                                <template v-if="list_of_BOL[selectedIndex].total_container_unload != list_of_BOL[selectedIndex].container_numbers.length">
                                    <img class="numberCircle  indexStatus_null"  src="{{asset('/img/unloaded.png') }}">
                                </template>
                                <span class="dots"></span>
                                <template v-if="list_of_BOL[selectedIndex].total_round_use == list_of_BOL[selectedIndex].container_numbers.length">
                                    <img class="numberCircle " src="{{asset('/img/returned.png') }}">
                                </template>
                                <template v-if="list_of_BOL[selectedIndex].total_round_use != list_of_BOL[selectedIndex].container_numbers.length">
                                    <img class="numberCircle  indexStatus_null"  src="{{asset('/img/returned.png') }}">
                                </template>
                            </span>
                            <br>
                            <h4  v-if="list_of_BOL[selectedIndex].actual_process != null">
                                   @{{  list_of_BOL[selectedIndex].container_numbers[0].sop_current_status }}
                            </h4>
                            <h4  v-if="list_of_BOL[selectedIndex].bl_remarks != null">
                                [ BL remarks ] :
                                @{{  list_of_BOL[selectedIndex].bl_remarks }}
                            </h4>

                </div>
                <div class="modal-body">
                        <div class="tabs-container">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#tab-3"> <i class="fa fa-eye"></i></a></li>
                                    @if (Session::get('arrival') == 1)
                                        <li class=""><a data-toggle="tab" href="#arrival"> Arrival </a></li>
                                    @endif
                                    @if (Session::get('e2m') == 1)
                                        <li class="" v-if="list_of_BOL[selectedIndex].e2m != null"><a data-toggle="tab" href="#e2m"> E2M </a></li>
                                    @endif
                                    @if (Session::get('gatepass') == 1 )
                                        <li class="" >
                                            <a data-toggle="tab" href="#gatepass"> Gatepass </a>
                                        </li>
                                    @endif
                                    @if (Session::get('storage_validity') == 1 )
                                        <li class=""><a data-toggle="tab" href="#revalidation"> Revalidation </a></li>
                                    @endif
                                    @if (Session::get('container_movement') == 1 )
                                        <li class=""><a data-toggle="tab" href="#container_movement"> Container Movement </a></li>
                                    @endif
                                    @if (Session::get('safe_keep') == 1 )
                                        <li class=""><a data-toggle="tab" href="#safe_keep"> Safe Keep </a></li>
                                    @endif
                                    @if (Session::get('maintenance') == 1 )
                                        <li class=""><a data-toggle="tab" href="#bl_remarks"> BL Remarks </a></li>
                                    @endif

                                </ul>
                                <div class="tab-content">
                                    <div id="tab-3" class="tab-pane active">
                                        <div class="panel-body">
                                            <div class="hr-line-dashed"></div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <h3><strong>Invoice:</strong><span class="text-navy" v-for="(invoice,index) in list_of_BOL[selectedIndex].invoice_numbers"> @{{invoice.invoice_number}} <span v-if="(index + 1) != list_of_BOL[selectedIndex].invoice_numbers.length">,</span> </span><br/> </h3>

                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Supplier:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].supplier}}  </span><br/> </h3>

                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Vessel:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].vessel}}  </span><br/> </h3>
                                                </div>
                                            </div>
                                            <div class="hr-line-dashed"></div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <h3><strong>Commodity:</strong><span class="text-navy" v-for="(commodity,index) in list_of_BOL[selectedIndex].commodities"> @{{commodity.commodity}} <span v-if="(index + 1) != list_of_BOL[selectedIndex].commodities.length">,</span> </span><br/> </h3>

                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Connecting Vessel:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].connecting_vessel}}  </span><br/> </h3>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Shipping Line:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].shipping_line}}  </span><br/> </h3>
                                                </div>

                                            </div>
                                            <div class="hr-line-dashed"></div>
                                            <div class="row">
                                                <div class="col-lg-4">

                                                    <h3><strong>Forwarder:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].forwarder}}  </span><br/> </h3>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Broker:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].broker}}  </span><br/> </h3>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Port of Loading:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].pol}}  </span><br/> </h3>
                                                </div>
                                            </div>
                                            <div class="hr-line-dashed"></div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <h3><strong>Country:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].country}}  </span><br/> </h3>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Processing Date:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].processing_date}}  </span><br/> </h3>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Port of Discharge:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].pod}}  </span><br/> </h3>
                                                </div>
                                            </div>
                                            <div class="hr-line-dashed"></div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <h3><strong>Estimated Time of Departure:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].estimated_time_departure}}  </span><br/> </h3>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Estimated Time of Arrival:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].estimated_time_arrival}}  </span><br/> </h3>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Latest ETA:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].latest_estimated_time_arrival}}  </span><br/> </h3>
                                                </div>
                                            </div>
                                            <div class="hr-line-dashed"></div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <h3><strong>Actual Time of Arrival:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].actual_time_arrival}}  </span><br/> </h3>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>T-SAD #:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].tsad_no}}  </span><br/> </h3>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Shipping Docs:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].shipping_docs }}  </span><br/> </h3>
                                                </div>


                                            </div>
                                            <div class="hr-line-dashed"></div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <h3><strong>Actual Time of Berthing:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].actual_berthing_date }}  </span><br/> </h3>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h3><strong>Incoterm:</strong><span class="text-navy"> @{{list_of_BOL[selectedIndex].incoterm }}  </span><br/> </h3>
                                                </div>
                                                <div class="col-lg-6">
                                                    <h3><strong>Split BL:</strong>
                                                        <span v-if="list_of_BOL[selectedIndex].split_bl_no_list.length > 0">
                                                            <span class="text-navy" v-for="spl in list_of_BOL[selectedIndex].split_bl_no_list">
                                                                @{{ spl.container_number }}  - <span style="color:red"> @{{ spl.split_bl_no_fk }} </span> <br>
                                                            </span>
                                                        </span>
                                                        <span v-else class="text-navy">No Split Indication</span>
                                                        <br/>
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="hr-line-dashed"></div>
                                            <div class="row" id="containerRow" style="height:500px;overflow:scroll">
                                                <table id="indexSearchShipment" class="table table-bordered-dark container_new_table table-hover table-responsive fixTable2" data-page-size="15">
                                                        <thead>
                                                        <tr>
                                                            <th class="tablewithrowspan column50" rowspan="2">#</th>
                                                            <th class="tablewithrowspan column200" rowspan="2" >Container #</th>
                                                            <th class="tablewithrowspan column50" rowspan="2">Type</th>
                                                            <th class="tablewithrowspan column50" rowspan="2">Qty</th>
                                                            <th class="tablewithrowspan column50" rowspan="2">X-ray</th>
                                                            <th class="tablewithrowspan column200" rowspan="2">Discharge</th>
                                                            <th class="tablewithrowspan column200" rowspan="2">Gatepass</th>
                                                            <th class="tablewithrowspan column200" colspan="2">Validity</th>
                                                            <th class="tablewithrowspan column200" colspan="2">Revalidation</th>
                                                            <th class="tablewithrowspan column200" rowspan="2">Revalidation Status</th>
                                                            <th class="tablewithrowspan column100" rowspan="2">Trucker</th>
                                                            <th class="tablewithrowspan column100" rowspan="2">Booking Time</th>
                                                            <th class="tablewithrowspan column100" rowspan="2">Detention Validity</th>
                                                            <th class="tablewithrowspan column100" rowspan="2">Pull-Out</th>
                                                            <th class="tablewithrowspan column200" colspan="2">Dismounted</th>
                                                            <th class="tablewithrowspan column100" rowspan="2">Unloaded</th>
                                                            <th class="tablewithrowspan column200" rowspan="2">Reason of Delay</th>
                                                            <th class="tablewithrowspan column100" rowspan="2">Safekeep</th>
                                                            <th class="tablewithrowspan column200" colspan="2">Return round use</th>
                                                            <th class="tablewithrowspan column100" rowspan="2">Box #</th>
                                                            <th class="tablewithrowspan column100" rowspan="2">Summary #</th>
                                                        </tr>
                                                        <tr>
                                                                <th>Storage</th>
                                                                <th>Demurrage</th>
                                                                <th>Storage</th>
                                                                <th>Demurrage</th>
                                                                <th>CY</th>
                                                                <th>Date</th>
                                                                <th>CY</th>
                                                                <th>Date</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr  data-toggle="tooltip" data-placement="top" :title="container.container_number" v-for="(container,index) in list_of_BOL[selectedIndex].container_numbers">
                                                                    <td class="test">
                                                                        @{{index + 1}}
                                                                    </td>
                                                                    <td class="test stickycolumn">
                                                                        @{{container.container_number}}
                                                                    </td>
                                                                    <td>@{{container.container_type}}</td>
                                                                    <td>@{{container.quantity }}</td>
                                                                    <td>
                                                                        <span v-if="list_of_BOL[selectedIndex].assessment_tag == 'RED'"  >
                                                                            <i class="fa fa-check"></i>
                                                                        </span>
                                                                    </td>
                                                                    <td>@{{container.actual_discharge }}</td>
                                                                    <td>@{{container.actual_gatepass }}</td>
                                                                    <td>@{{container.validity_storage }}</td>
                                                                    <td>@{{container.validity_demurrage }}</td>
                                                                    <td>@{{container.revalidity_storage }}</td>
                                                                    <td>@{{container.revalidity_demurrage }}</td>
                                                                    <td>@{{container.revalidity_remarks }}</td>
                                                                    <td>@{{container.trucker }}</td>
                                                                    <td>@{{container.booking_time }}</td>
                                                                    <td>@{{container.detention_validity }}</td>
                                                                    <td>@{{container.pull_out }} @{{container.pull_out_time }}</td>
                                                                    <td>@{{container.dismounted_cy }}</td>
                                                                    <td>@{{container.dismounted_date }}</td>
                                                                    <td>@{{container.unload }}</td>
                                                                    <td>@{{container.reason_of_delay_delivery }}</td>
                                                                    <td>@{{container.safe_keep }}</td>
                                                                    <td>@{{container.return_cy }}</td>
                                                                    <td>@{{container.return_date }}</td>
                                                                    <td>@{{container.return_box_number }}</td>
                                                                    <td>@{{container.return_summary_number }}</td>
                                                            </tr>
                                                        </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    @if (Session::get('arrival') == 1)
                                    <div id="arrival" class="tab-pane">
                                        <div class="panel-body">


                                            @component('components.edit_arrival_update');

                                            @endcomponent

                                            {{-- @elseif()

                                            @else --}}


                                        </div>
                                    </div>
                                    @endif
                                    @if (Session::get('e2m') == 1)
                                    <div id="e2m" class="tab-pane">
                                        <div class="panel-body">

                                           @component('components.edit_e2m')

                                           @endcomponent

                                            {{-- @elseif()

                                            @else --}}


                                        </div>
                                    </div>
                                    @endif

                                    @if (Session::get('gatepass') == 1 )
                                        <div id="gatepass" class="tab-pane">
                                            <div class="panel-body">
                                               @component('components.edit_gatepass')

                                               @endcomponent
                                            </div>
                                        </div>
                                    @endif
                                    @if (Session::get('storage_validity') == 1 )

                                    <div id="revalidation" class="tab-pane">
                                            <div class="panel-body">
                                               @component('components.edit_revalidation')

                                               @endcomponent
                                            </div>
                                        </div>

                                    @endif

                                    @if (Session::get('container_movement') == 1 )

                                    <div id="container_movement" class="tab-pane">
                                            <div class="panel-body">
                                               @component('components.edit_container_movement')

                                               @endcomponent
                                            </div>
                                        </div>

                                    @endif

                                    @if (Session::get('safe_keep') == 1 )

                                    <div id="safe_keep" class="tab-pane">
                                            <div class="panel-body">
                                               @component('components.edit_safe_keep')

                                               @endcomponent
                                            </div>
                                        </div>

                                    @endif

                                    @if (Session::get('maintenance') == 1 )

                                    <div id="bl_remarks" class="tab-pane">
                                            <div class="panel-body">
                                                @component('components.edit_bl_remarks')

                                                @endcomponent
                                            </div>
                                        </div>

                                    @endif

                                    <div id="tab-5" class="tab-pane">
                                        <div class="panel-body">

                                        </div>
                                    </div>
                                </div>
                            </div>


                </div>
                <div class="modal-footer">
                    <button type="button"  @click="closeModal"  class="btn btn-white" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('vuejsscript')
<script src="{{asset('/js/vuejs/import_index.js')}}"></script>
@endsection

