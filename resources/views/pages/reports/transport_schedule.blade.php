@extends('layout.index2')

@section('body')

<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Container Discharge / Gatepass</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-3">
                            <span>Please select date</span>
                            <input type="text" id="date_filter" v-model="date_filter" placeholder="Please Select date..." class="form-control">
                            <br>
                            <span>Please select factory</span>
                            <select class="form-control" v-model="selected_factory" >
                                <option v-for="factory in factories" :value="factory">@{{factory}}</option>

                            </select>
                            <span>Please select Port</span>
                            <select class="form-control" v-model="selected_port">
                                <option v-for="val in port" :value="val">@{{val}}</option>

                            </select>
                        </div>
                        <div class="col-lg-3">
                            <br>
                            <h2>@{{ cdata.length}} Total record</h2>
                        </div>
                        <div class="col-lg-3">
                            <br>
                            <h3>@{{ date_filter + ' - ' +nextweek}} (ETA) <br>
                               <hr>
                                Estimated container(s) : @{{ eta_count }}
                            </h3>
                            <hr>
                            <a :href="'./transport_schedule_export/'+date_filter" class="btn btn-primary btn-outline btn-sm btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>

                        </div>
                        <div class="col-lg-3">
                            <h3>Status</h3>
                            <ul>
                                <li class="cursor" @click="filterstatus(1)">1 - Discharge w/ Gatepass</li>
                                <li class="cursor" @click="filterstatus(2)">2 - Discharge w/o Gatepass</li>
                                <li class="cursor" @click="filterstatus(3)">3 - w/ Berthing</li>
                                <li class="cursor" @click="filterstatus(4)">4 - w/ Actual Arrival</li>
                                <li class="cursor" @click="filterstatus(5)">5 - Estimated Arrival only</li>
                            </ul>
                            <button v-show="status_filtered != null" @click="filterstatus(null)"> Remove filtered </button>
                        </div>
                    </div>
                    <br>
                    @if (Session::get('container_movement') == 1 )

                        <button @click="changeMode"  class="btn  btn-white btn-sm">
                            <span v-if="view_mode">
                                <i class="fa fa-edit"></i>
                                Edit
                            </span>
                            <span  v-if="!view_mode">
                                <i class="fa fa-eye"></i>
                                View
                            </span>
                        </button>

                    @endif

                    <div id="tablediv">
                            <table id="tabledata"  style="white-space: nowrap;" class="table  table-hover table-bordered-dark  toggle-arrow-tiny">
                                <thead>
                                    <tr>
                                        <th class="column100">STATUS</th>
                                        <th class="column100">STATUS 2</th>
                                        <th class="column100">FACTORY</th>
                                        <th class="column100">SHIPPING LINE</th>
                                        <th class="column200">CONTAINER #</th>
                                        <th class="column300">COMMODITY</th>

                                        {{-- <th>ACTUAL GATEPASS</th>
                                        <th>ACTUAL BERTHING</th>
                                        <th>ACTUAL ARRIVAL</th>
                                        <th>ESTIMATED</th> --}}
                                        {{-- <th>ETA</th>
                                        <th>SHIPPING LINE.</th>
                                        <th>FACTORY</th>
                                        <th>CONTAINER NUMBER</th> --}}
                                        <th class="column100">SIZE</th>
                                        <th class="column100">PORT</th>
                                        <th class="column100">XRAY</th>
                                        <th class="column100">TARGET DISPATCH</th>
                                        <th class="column100">DISPATCHED DATE</th>
                                        {{-- <th>CONTAINER NUMBER</th> --}}
                                        {{-- <th>CONTAINER SIZE</th>
                                        <th>PORT</th> --}}

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="container in cdata">
                                        <td style="color:red">
                                            @{{container.status}}
                                        </td>
                                        <td>
                                            <span v-if="container.actual_discharge != null">
                                                @{{container.actual_discharge}}
                                            </span>
                                            <span v-else-if="container.actual_berthing_date != null">
                                                @{{container.actual_berthing_date}}
                                            </span>
                                            <span v-else-if="container.actual_time_arrival != null">
                                                @{{container.actual_time_arrival}}
                                            </span>
                                            <span v-else-if="container.estimated_time_arrival != null">
                                                @{{container.estimated_time_arrival}}
                                            </span>
                                        </td>
                                        <td>@{{container.factory}}</td>
                                        <td>
                                            @{{container.shipping_line}}
                                        </td>
                                        {{-- <td >@{{container.actual_gatepass}}</td>
                                        <td >@{{container.actual_berthing_date}}</td>
                                        <td >@{{container.actual_time_arrival}}</td>
                                        <td >@{{container.estimated_time_arrival}}</td> --}}
                                        <td >
                                            @{{container.container_number}}
                                        </td>
                                        <td >
                                            @{{container.commodity.join()}}
                                        </td>

                                        <td>@{{container.container_type}}</td>
                                        <td>@{{container.pod}}</td>
                                        <td>
                                            <span v-show="container.assessment_tag == 'RED'"><i class="fa fa-check"></i></span>
                                        </td>
                                        <td>
                                            <template v-if="view_mode">
                                                @{{ container.target_dispatch_date}}
                                            </template>
                                            <template v-if="!view_mode">
                                                {{-- #ffc107 Yellow --}}
                                                {{-- #10b759 green --}}
                                                {{-- #dc3545 red --}}

                                                <input
                                                v-if="container.status == 1"
                                                style="border-color:#10b759"
                                                :data-container_id="container.container_id" placeholder="YYYY-MM-DD"  readonly='true'
                                                type="text"
                                                class="form-control target_dispatch_date"
                                                v-model="container.target_dispatch_date">
                                                <input
                                                v-if="container.status == 2"
                                                style="border-color:#ffc107"
                                                :data-container_id="container.container_id" placeholder="YYYY-MM-DD"  readonly='true'
                                                type="text"
                                                class="form-control target_dispatch_date"
                                                v-model="container.target_dispatch_date">
                                                <input
                                                v-if="container.status == 3 || container.status == 4 || container.status == 5"
                                                :style="(container.status != 3) ? '' : 'border-color:#ffc107' "
                                                :data-container_id="container.container_id" placeholder="YYYY-MM-DD"  readonly='true'
                                                type="text"
                                                class="form-control target_dispatch_date"
                                                v-model="container.target_dispatch_date">

                                            </template>
                                        </td>
                                        <td>
                                            <template v-if="view_mode">
                                                @{{ container.dispatched_date}}
                                            </template>
                                            <template v-if="!view_mode">
                                                {{-- #ffc107 Yellow --}}
                                                {{-- #10b759 green --}}
                                                {{-- #dc3545 red --}}

                                                <input
                                                v-if="container.status == 1"
                                                style="border-color:#10b759"
                                                :data-container_id="container.container_id" placeholder="YYYY-MM-DD"  readonly='true'
                                                type="text"
                                                class="form-control dispatched_date"
                                                v-model="container.dispatched_date">
                                                <input
                                                v-if="container.status == 2"
                                                style="border-color:#ffc107"
                                                :data-container_id="container.container_id" placeholder="YYYY-MM-DD"  readonly='true'
                                                type="text"
                                                class="form-control dispatched_date"
                                                v-model="container.dispatched_date">
                                                <input
                                                v-if="container.status == 3 || container.status == 4 || container.status == 5"
                                                :style="(container.status != 3) ? '' : 'border-color:#ffc107' "
                                                :data-container_id="container.container_id" placeholder="YYYY-MM-DD"  readonly='true'
                                                type="text"
                                                class="form-control dispatched_date"
                                                v-model="container.dispatched_date">

                                            </template>
                                        </td>

                                    </tr>
                                    {{-- <tr v-for="container in containers_loaded">
                                        <td class="stickycolumn"> <span v-show="container.actual_gatepass != null"> @{{container.actual_discharge}} </span></td>
                                        <td >@{{container.actual_gatepass}}</td>
                                        <td >@{{container.estimated_time_arrival}}</td>
                                        <td >@{{container.shipping_line}}</td>
                                        <td >@{{container.factory}}</td>
                                        <td >@{{container.container_number}}</td>
                                        <td >
                                            @{{container.status}}
                                        </td>
                                        <td >@{{container.container_type}}</td>
                                        <td >@{{container.pod}}</td>
                                    </tr> --}}
                                </tbody>
                            </table>
                                        {{-- <td class="stickycolumn">@{{details.bl_no_fk}}</td>
                                        <td>@{{details.invoices}}</td>
                                        <td>@{{details.supplier}}</td>
                                        <td >@{{details.container_number}}</td>
                                        <td>@{{details.commodity}}</td>
                                        <td>@{{details.shipping_line}}</td>
                                        <td>@{{details.connecting_vessel}}</td>
                                        <td>@{{details.pol}}</td>
                                        <td>@{{details.country}}</td>
                                        <td>@{{details.pod}}</td>
                                        <td>@{{details.estimated_time_departure}}</td>
                                        <td>@{{details.estimated_time_arrival}}</td>
                                        <td>@{{details.actual_time_arrival}}</td>
                                        <td>@{{details.actual_berthing_date}}</td>
                                        <td>@{{details.actual_discharge}}</td>
                                        <td>@{{details.container_type}}</td>
                                        <td>@{{details.actual_gatepass}}</td>
                                        <td>@{{details.pull_out}}</td>
                                        <td>@{{details.dismounted_cy}}</td>
                                        <td>@{{details.dismounted_date}}</td> --}}
                        </div>
                </div>
            </div>
        </div>
</div>
 {{-- <small class="font-bold">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</small> --}}
{{-- <div class="modal inmodal fade myModal5" id="myModal5" tabindex="-1" role="dialog"  aria-hidden="true">

    <div class="modal-dialog modal-lg modal-request-lg ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Details Summary</h4>

            </div>
            <div class="modal-body">
                <div class="hr-line-dashed"></div>
                <div class="row">

                    <div class="col-lg-6 col-sm-6 col-md-offset-6 col-lg-offset-6">
                            <a v-if="details_obj.length > 0" :href="'extract_container_irs/' + factory " class="btn btn-primary btn-outline btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>
                    </div>
                </div>
                <table id="detail_obj" class="table table-striped">
                    <thead>
                        <tr>
                            <th>BL NO.</th>
                            <th>Invoice Number</th>
                            <th>Supplier</th>
                            <th>Container Number</th>
                            <th>Commodity</th>
                            <th>Shipping Line</th>
                            <th>Connecting Vessel</th>
                            <th>POL</th>
                            <th>Country</th>
                            <th>POD</th>
                            <th>ETD</th>
                            <th>ETA</th>
                            <th>ATA</th>
                            <th>ATB</th>
                            <th>ATD</th>
                            <th>Size</th>
                            <th>Actual Gatepass</th>
                            <th>Date pullout</th>
                            <th>CY</th>
                            <th>Dismounted Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="details in filteredList">
                            <td class="stickycolumn">@{{details.bl_no_fk}}</td>
                            <td>@{{details.invoices}}</td>
                            <td>@{{details.supplier}}</td>
                            <td >@{{details.container_number}}</td>
                            <td>@{{details.commodity}}</td>
                            <td>@{{details.shipping_line}}</td>
                            <td>@{{details.connecting_vessel}}</td>
                            <td>@{{details.pol}}</td>
                            <td>@{{details.country}}</td>
                            <td>@{{details.pod}}</td>
                            <td>@{{details.estimated_time_departure}}</td>
                            <td>@{{details.estimated_time_arrival}}</td>
                            <td>@{{details.actual_time_arrival}}</td>
                            <td>@{{details.actual_berthing_date}}</td>
                            <td>@{{details.actual_discharge}}</td>
                            <td>@{{details.container_type}}</td>
                            <td>@{{details.actual_gatepass}}</td>
                            <td>@{{details.pull_out}}</td>
                            <td>@{{details.dismounted_cy}}</td>
                            <td>@{{details.dismounted_date}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> --}}

@endsection

@section('vuejsscript')
    <script src="{{asset('/js/vuejs/transport_schedule.js')}}"></script>
@endsection



@section('headscript')
<style>


    #tablediv {
        max-width: 100%;
        max-height: 500px;
        overflow: scroll;
        position: relative;
    }
    #tabledata thead {

        position: -webkit-sticky;
        position: sticky;
        top: 0;
        color: red;
    }

    .table th {
        text-align: center;
    }
</style>
@endsection
