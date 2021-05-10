@extends('layout.index2')

@section('body')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5> <i class="fa fa-ticket"></i> Shipment on process</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-2">
                        <button type="button" :disabled="loading_data" id="loading-example-btn" class="btn btn-white btn-sm" @click="refresh"><i
                                class="fa fa-sync-alt"></i> Refresh </button>
                        <button @click="changeMode" class="btn  btn-white btn-sm">
                            <span v-if="view_mode">
                                <i class="fa fa-edit "></i>
                                Edit
                            </span>
                            <span v-if="!view_mode">
                                <i class="fa fa-eye"></i>
                                View
                            </span>
                        </button>
                    </div>
                    <div class="col-md-4">
                        {{-- <div class="input-group">
                            <input type="text" placeholder="Search" v-model="search_bl_no" class="input-sm form-control">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-sm btn-primary" :disabled="search_bl_no.trim().length == 0" @click="searchBL"> Search </button> </span>
                        </div> --}}
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="row">
                    <div class="col-md-2">
                        <label>Factory</label>
                        <select name="" class="form-control" v-model="search_factory" id="search_factory">
                            <option value=""></option>
                            <option :value="factory.factory_id" v-for="factory in factories">@{{ factory.factory_id }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>BL #</label>
                        <input type="text" v-model="search_bl_no"  id="search_bl_no" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Commodity</label>
                        <input type="text" id="search_commodity" v-model="search_commodity" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Shipping line</label>
                        <select name="" class="form-control" v-model="search_shipping_line" id="search_shipping_line">
                            <option value=""></option>
                            <option :value="sl.shipping_line" v-for="sl in shipping_line">@{{ sl.shipping_line }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Current Status</label>
                        <input type="text" id="search_current_status" v-model="search_current_status" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Target Gatepass</label>
                        <input type="text" id="search_target_gatepass" v-model="search_target_gatepass" class="form-control">
                    </div>

                </div>
                <div class="row">
                    <br>
                    <div class="col-md-2">
                        <button class="btn btn-primary"
                        class="btn btn-sm btn-primary"
                        :disabled="search_factory.trim() == 0
                        && search_bl_no.trim() == 0
                        && search_commodity.trim() == 0
                        && search_shipping_line.trim() == 0
                        && search_current_status.trim() == 0
                        && search_target_gatepass.trim() == 0"
                        @click="searchBL"> <i class="fa fa-search"></i> Search </button>
                    </div>
                    <div class="col-lg-3  pull-right">
                        <div class="col-lg-6 col-sm-6 col-md-offset-6 col-lg-offset-6">
                                <a href="extract_ShipmentOnProcessOngoingExport/" class="btn btn-primary btn-outline btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>
                        </div>
                    </div>
                </div>

                <div class="hr-line-dashed"></div>
                <div class="row">
                    <h3> Total Record : @{{ list_of_BOL.length }} /  @{{  list_of_BOL_Total }} </h3>
                </div>


                <div class="row" id="tablerow">

                    <table class="footable table table-stripped  table-bordered-dark indexSearchShipment toggle-arrow-tiny"
                        data-page-size="15">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Factory</th>
                                <th>BL #</th>
                                <th>Commodity</th>
                                <th>POL</th>
                                <th>Shippine Line</th>
                                <th>Container</th>
                                <th>Current Status</th>
                                <th>Target Gatepass</th>
                                <th>Actual Gatepass</th>
                                <th>Reason of Delay Gatepass</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="myRefreshTable" v-if="!loading_data">

                            <div v-if="first_load == false && list_of_BOL.length == 0" class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                No result found. Please click refresh
                            </div>
                            <template v-for="(BOL,index) in list_of_BOL">
                                <tr>
                                    <td @dblclick="toggle(BOL.id)" style="cursor:pointer">
                                        <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                        <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                    </td>
                                    <td> @{{BOL.factory}} </td>
                                    <td> @{{BOL.bl_no}} </td>
                                    <td>
                                        <span v-for="(cm,index) in BOL.commodities">@{{cm.commodity}} <span v-if="(index+1) != BOL.commodities.length ">,</span></span>
                                    </td>
                                    <td>@{{BOL.pol}}</td>
                                    <td>@{{BOL.shipping_line}}</td>
                                    <td>@{{BOL.volume}}</td>
                                    <td>
                                        @if (Session::get('current_status') == 1)
                                        <span v-show="view_mode">
                                            @{{ BOL.sop_current_status = BOL.container_numbers[0].sop_current_status }}
                                        </span>
                                        <span v-show="!view_mode">
                                            <input type="text" class="form-control" @keyup="sameContainers('sop_current_status',BOL.sop_current_status,index)"
                                                @blur="saveBlur(BOL.id,'sop_current_status',BOL.sop_current_status)"
                                                v-model="BOL.sop_current_status">
                                        </span>
                                        @else

                                            @{{ BOL.sop_current_status }}


                                        @endif

                                    </td>
                                    <td style="color:red"> <b> @{{ BOL.target_gatepass }} </b></td>
                                    <td>

                                        @if (Session::get('gatepass') == 1)
                                        <span v-show="view_mode">
                                            @{{BOL.actual_gatepass}}
                                        </span>
                                        <span v-show="!view_mode">
                                            <span hidden>@{{BOL.id}}</span>
                                            <input type="text" :disabled="BOL.target_gatepass == null || BOL.target_gatepass == ''"
                                                readonly='true' :data-index="index" class="form-control  actual_gatepass "
                                                v-model="BOL.actual_gatepass">
                                        </span>
                                        @else
                                            @{{BOL.actual_gatepass}}
                                        @endif

                                    </td>
                                    <td>
                                        @if (Session::get('gatepass') == 1)
                                        <span v-show="view_mode">
                                            @{{BOL.reason_of_delay_gatepass}}
                                        </span>
                                        <span v-show="!view_mode">
                                            <textarea @keyup="sameContainers('reason_of_delay_gatepass',BOL.reason_of_delay_gatepass,index)"
                                                :disabled="!BOL.reason_of_delay_gatepass_boolean" class="form-control"
                                                v-model="BOL.reason_of_delay_gatepass" placeholder="Reason of Delay"></textarea>
                                            <span v-if="BOL.reason_of_delay_gatepass_boolean">
                                                <button class="btn btn-default" :disabled="BOL.reason_of_delay_gatepass == null || BOL.reason_of_delay_gatepass.length == 0"
                                                    @click="saveDelayReason(BOL.bl_no,BOL.actual_gatepass,BOL.reason_of_delay_gatepass)">Save
                                                    with delay reason</button>
                                            </span>
                                        </span>
                                        @else
                                            @{{ BOL.reason_of_delay_gatepass}}
                                        @endif

                                    </td>
                                    <td>
                                        @if (Session::get('gatepass') == 1)
                                        <span v-show="view_mode">
                                            @{{BOL.sop_remarks}}
                                        </span>
                                        <span v-show="!view_mode">
                                            <textarea @blur="saveBlur(BOL.id,'sop_remarks',BOL.sop_remarks)"
                                                class="form-control"
                                                v-model="BOL.sop_remarks" placeholder="Ramarks"></textarea>
                                        </span>
                                        @else
                                            @{{ BOL.sop_remarks}}
                                        @endif

                                    </td>
                                </tr>
                                <tr v-show="opened.includes(BOL.id)">
                                    <td colspan="20">
                                        <table class="table table-bordered table-responsive">
                                            <thead>
                                                <tr>
                                                    <th> Type </th>
                                                    <th> Container # </th>
                                                    <th>Current Status</th>
                                                    <th>Actual Gatepass</th>
                                                    <th>Reason of Delay Gatepass</th>
                                                </tr>
                                            </thead>
                                            <tbody style="font-size:15px">
                                                <tr v-for="(container,index2) in BOL.container_numbers">
                                                    <td>
                                                        @{{ container.container_type}}
                                                    </td>
                                                    <td>
                                                        @{{ container.container_number}}
                                                    </td>
                                                    <td>
                                                        <span v-show="view_mode">
                                                            @{{ container.sop_current_status }}
                                                        </span>
                                                        <span v-show="!view_mode">
                                                            <input type="text" class="form-control" @blur="saveContainerBlur(container.id,'sop_current_status',container.sop_current_status)"
                                                                v-model="container.sop_current_status">
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span v-show="view_mode">
                                                            @{{ container.actual_gatepass }}
                                                        </span>
                                                        <span v-show="!view_mode">
                                                            <input type="text" :disabled="BOL.target_gatepass == null || BOL.target_gatepass == ''"
                                                                readonly='true' :data-index="index"
                                                                :data-index_container="index2" class="form-control actual_gatepass_container"
                                                                v-model="container.actual_gatepass">
                                                        </span>
                                                    </td>
                                                    <td>

                                                        <span v-show="view_mode">
                                                            @{{ container.reason_of_delay_gatepass}}
                                                        </span>
                                                        <span v-show="!view_mode">
                                                            <textarea :disabled="!container.reason_of_delay_gatepass_boolean"
                                                                class="form-control" v-model="container.reason_of_delay_gatepass"
                                                                placeholder="Reason of Delay"></textarea>
                                                            <span v-if="container.reason_of_delay_gatepass_boolean">
                                                                <button class="btn btn-default" :disabled="container.reason_of_delay_gatepass == null || container.reason_of_delay_gatepass.length == 0"
                                                                    @click="saveDelayReasonContainer(container.id,container.actual_gatepass,container.reason_of_delay_gatepass)">Save
                                                                    with delay reason</button>
                                                            </span>
                                                        </span>
                                                        @{{ container.reason_delay_gatepass}}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <center v-if="loading_data"><img src="{{ asset('img/loading.gif') }}"></center>
                </div>
                <div class="hr-line-dashed"></div>
                    <div class="row" v-if="!searchTrue && list_of_BOL.length < list_of_BOL_Total" >
                        <div class="col-lg-2">
                            <label for=""> Number of record </label>
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
                                <button :disabled="showprogress" class="btn btn-primary btn-block m-t" @click="getRecord_SOP(numberofTake)"><i
                                    class="fa fa-arrow-down"></i> Show More</button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
<div id="myRefreshTableClone">

</div>
@endsection

@section('vuejsscript')
    <script src="{{asset('/js/vuejs/shipment_on_process.js')}}"></script>
@endsection
