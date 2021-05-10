@extends('layout.index2')

@section('body')

<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Arrival & Docs Update</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">

                    <div class="col-lg-3">
                        <label for="">Connecting Vessel</label>
                        <select name="" id="filter_cv" v-model="filter_cv">
                            {{-- <option value=""></option> --}}
                            <option v-for="cv in connecting_vessels_filter" v-if="cv.connecting_vessel != null" :value="cv.connecting_vessel">@{{cv.connecting_vessel
                                }}</option>
                            <option value="empty_field">Blank | Empty</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label for="">Shipping Line</label>
                        <select name="" id="filter_sl" v-model="filter_sl">
                            {{-- <option value=""></option> --}}
                            <option v-for="sl in shipping_lines" v-if="sl.shipping_line != null" :value="sl.shipping_line">@{{sl.shipping_line
                                }}</option>
                        </select>
                    </div>

                        {{-- IMPORT EXCEL --}}

                        {{-- <div class="col-lg-3">

                            <input type="radio" value="date_endorse" v-model="generate_date" name="optionsRadios">Date
                            Endorse
                            <input type="radio" value="actual_process" v-model="generate_date" name="optionsRadios"> Actual
                            Process
                            <br>
                            <div class="input-group">
                                <input type="text" id="date_endorse" readonly='true' placeholder="Select a date..." v-model="date_endorse"
                                    class="input-sm form-control">
                                <span class="input-group-btn">
                                    <a v-if="!woods" :disabled="date_endorse.trim().length == 0" :href="'/exceltest/' + date_endorse + '/' + generate_date"
                                        class="btn btn-sm btn-primary"> Generate </a>
                                    <a v-if="woods" :disabled="date_endorse.trim().length == 0" :href="'/excelwood/' + date_endorse + '/' + generate_date"
                                        class="btn btn-sm btn-primary"> Generate Woods </a>
                                </span>
                            </div>
                            <input type="checkbox" v-model="woods" name="" id="">
                            Woods Only

                        </div> --}}
                        {{-- IMPORT EXCEL --}}
                    <div class="col-lg-3">
                        <br>
                        <div class="input-group">
                            <input  type="text" placeholder=" Container Number " v-model="search_cn"
                                class="input-sm form-control"> <span class="input-group-btn">
                                <button type="button" @click="filterSearch" class="btn btn-sm btn-primary" :disabled="search_cn.trim().length == 0">
                                    Search </button> </span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <br>
                        <button @click="changeMode" class="btn  btn-white btn-sm">
                            <span v-if="view_mode">
                                <i class="fa fa-edit"></i>
                                Edit
                            </span>
                            <span v-if="!view_mode">
                                <i class="fa fa-eye"></i>
                                View
                            </span>
                        </button>

                        <button type="button" :disabled="loading_data" id="loading-example-btn" class="btn btn-white btn-sm" @click="refresh_discharge"><i
                                class="fa fa-sync-alt "></i> Refresh</button>

                        <button class="btn btn-white btn-sm" @click="clearFilter"><i class="fa fa-eraser"></i> Clear
                            Filter</button>



                        {{-- <button @click="changeMode" class="btn btn-md dim btn-outline btn-success pull-right">
                            <span v-if="view_mode">
                                <i class="fa fa-edit fa-2x"></i> <br>
                                Edit
                            </span>
                            <span v-if="!view_mode">
                                <i class="fa fa-eye fa-2x"></i> <br>
                                View
                            </span>
                        </button> --}}

                    </div>

                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <span class="h4 font-bold m-t block">@{{list_of_BOL.length}} / @{{totalRecord}}</span>
                        <h3>Showing Records</h3>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="row table-responsive" id="tablerow">
                    {{-- --}}
                        @if ($browser == 'chrome')
                            <table id="fixTable_chrome" class="table  table-hover table-bordered-dark   toggle-arrow-tiny">
                        @else
                            <table id="fixTable" class="table  table-hover table-bordered-dark   toggle-arrow-tiny">
                        @endif

                        <thead>
                            <tr>
                                <th>BL #
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control header_filter" placeholder="BL #" v-model="header_bl">
                                </th>
                                <th style="width:8%"> Consignee
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control header_filter" placeholder="Consignee"
                                        v-model="header_consignee">
                                </th>
                                <th>Vessel
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control header_filter" placeholder="Vessel" v-model="header_vessel">
                                </th>
                                <th>Shipping Line
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control header_filter" placeholder="SL" v-model="header_shipping_line">
                                </th>
                                <th>Connecting Vessel
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control header_filter" placeholder="Connecting Vessel"
                                        v-model="header_connecting_vessel">
                                </th>
                                <th>Registry No.
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control header_filter" placeholder="Connecting Vessel"
                                        v-model="header_registry_no">
                                </th>
                                <th>POD
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control header_filter" placeholder="POD" v-model="header_pod">
                                </th>
                                <th style="color:red">Est Time Arrival
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control header_filter" placeholder="ETA" v-model="header_ETA">
                                </th>
                                <th style="color:#9e34eb">Latest ETA
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control header_filter" placeholder="Latest ETA" v-model="header_LETA">
                                </th>
                                <th style="color:blue">Act Time Arrival
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control header_filter" placeholder="ATA" v-model="header_ATA">
                                </th>
                                <th style="color:brown">Act Berthing Date
                                    <div class="hr-line-dashed"></div>
                                    <input type="text" class="form-control header_filter" placeholder="Berthing"
                                        v-model="header_ABD">
                                </th>
                            </tr>
                        </thead>
                        <tbody v-if="!loading_data">
                            <template v-for="(BOL,index) in filteredList">
                                <tr>
                                    {{-- <td @dblclick="toggle(BOL.id)" style="cursor:pointer">
                                        <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                                        <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                                    </td> --}}
                                    <td class="stickycolumn" @dblclick="toggle(BOL.id)">
                                        @{{BOL.bl_no}}
                                    </td>
                                    <td>
                                        @{{BOL.factory}}
                                    </td>

                                    <td>
                                        @{{BOL.vessel}}
                                    </td>
                                    <td>
                                        @{{BOL.shipping_line}}
                                    </td>
                                    <td>
                                        <span v-show="view_mode" :style="BOL.connecting_vessel_confirm == 1 ? {'color':'#9933ff'} : {'color' : 'red'}">
                                            <b> @{{BOL.connecting_vessel}}</b>
                                        </span>
                                        <span v-show="!view_mode">
                                            <span hidden>@{{BOL.id}}</span>
                                            <select name="" class="connecting_vessels" v-model="BOL.connecting_vessel">
                                                <option value=""></option>
                                                <option v-for="cv in connecting_vessels" v-if="cv.connecting_vessel != null"
                                                    :value="cv.connecting_vessel">@{{cv.connecting_vessel }}</option>
                                            </select>
                                            <input type="checkbox" @change="connect_confirm(BOL.id,BOL.connecting_vessel_confirm)"
                                                v-model="BOL.connecting_vessel_confirm">
                                        </span>

                                    </td>
                                    <td >
                                            <template v-if="view_mode">
                                                @{{  BOL.registry_no}}
                                            </template>
                                            <template  v-if="!view_mode">
                                                <span hidden>@{{BOL.id}}</span>

                                                <input type="text" @blur="saveBlur(BOL.id,'registry_no',BOL.registry_no)" v-model="BOL.registry_no"
                                                class="form-control">
                                            </template>
                                    </td>
                                    <td>
                                        <span v-show="view_mode">
                                            @{{BOL.pod}}
                                        </span>
                                        <span v-show="!view_mode">
                                            <span hidden>@{{BOL.id}}</span>
                                            <select class="form-control pod" v-model="BOL.pod">
                                                <option value=""></option>
                                                <option value="SOUTH">SOUTH</option>
                                                <option value="NORTH">NORTH</option>
                                            </select>
                                        </span>
                                    </td>

                                    <td style="color:red">
                                        <span v-show="view_mode">
                                            @{{BOL.estimated_time_arrival}}
                                        </span>
                                        <span v-show="!view_mode">
                                            <span hidden>@{{BOL.id}}</span>
                                            <input type="text" readonly='true' class="form-control estimated_time_arrival"
                                                placeholder="YYYY-MM-DD" v-model="BOL.estimated_time_arrival">
                                        </span>

                                    </td>
                                    <td style="color:#9e34eb">
                                        <span v-show="view_mode">
                                            @{{BOL.latest_estimated_time_arrival}}
                                        </span>
                                        <span v-show="!view_mode">
                                            <span hidden>@{{BOL.id}}</span>
                                            <input type="text" readonly='true' class="form-control latest_estimated_time_arrival"
                                                placeholder="YYYY-MM-DD" v-model="BOL.latest_estimated_time_arrival">
                                        </span>

                                    </td>
                                    <td style="color:blue">

                                        <span v-show="view_mode">
                                            @{{BOL.actual_time_arrival}}
                                        </span>
                                        <span v-show="!view_mode">
                                            <span hidden>@{{BOL.id}}</span>
                                            <input type="text" readonly='true' class="form-control actual_time_arrival"
                                                placeholder="YYYY-MM-DD" v-model="BOL.actual_time_arrival">
                                        </span>

                                    </td>
                                    <td style="color:#eaba6b">

                                        <span v-show="view_mode">
                                            @{{BOL.actual_berthing_date}}
                                        </span>
                                        <span v-show="!view_mode">
                                            <span hidden>@{{BOL.id}}</span>
                                            <input :disabled="(BOL.connecting_vessel == null || BOL.connecting_vessel == '') || BOL.connecting_vessel == 'T.B.A.'" type="text" readonly='true' class="form-control berthing_date"
                                                placeholder="YYYY-MM-DD" v-model="BOL.actual_berthing_date">
                                        </span>

                                    </td>


                                </tr>

                                <tr class="bodyitem" v-if="opened.includes(BOL.id)">
                                    <td colspan="8">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th style="color:#eaba6b">Qty</th>
                                                    <th style="color:green">Container #</th>
                                                    <th style="color:violet">
                                                        Discharge
                                                        <span v-show="!view_mode" class="pull-right">
                                                            <input type="checkbox" v-model="BOL.sameDischarge">
                                                        </span>

                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody style="font-size:15px">
                                                <tr v-for="(container,index2) in BOL.container_numbers" :style="container.quantity == 0 ? {'background-color': '#FAB341'} : {}">
                                                    <td>@{{container.quantity}}</td>
                                                    <td @dblclick="splitBL_NO(container.id,container.split_bl_no_fk,container.quantity)">@{{container.container_number}}</td>
                                                    <td v-show="!view_mode"><input :data-index="index"
                                                            :data-index_container="index2"
															:data-index_bl_no="BOL.bl_no" readonly='true' type="text"
                                                            class="form-control container_discharge" v-model="container.actual_discharge"></td>
                                                    <td v-show="view_mode">@{{container.actual_discharge}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                {{-- <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="color:#eaba6b">Qty</th>
                                            <th style="color:green">Container #</th>
                                            <th style="color:violet">
                                                Discharge
                                                <span v-show="!view_mode" class="pull-right">
                                                    <input type="checkbox" v-model="BOL.sameDischarge">
                                                </span>

                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:15px">
                                        <tr v-for="(container,index2) in BOL.container_numbers">
                                            <td style="color:#eaba6b">@{{container.quantity}}</td>
                                            <td>@{{container.container_number}}</td>
                                            <td v-show="!view_mode"><input :data-index="index" :data-index_container="index2"
                                                    type="text" class="form-control container_discharge" v-model="container.actual_discharge"></td>
                                            <td v-show="view_mode">@{{container.actual_discharge}}</td>
                                        </tr>
                                    </tbody>
                                </table> --}}
                            </template>
                        </tbody>
                    </table>




                    <center v-if="loading_data"><img src="{{ asset('img/loading.gif') }}"></center>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="row">
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
                    <div class="col-lg-10" v-if="filter_cv == '' && filter_sl == '' ">

                        <button :disabled="showprogress" class="btn btn-primary btn-block m-t" @click="getRecord(numberofTake)"><i
                                class="fa fa-arrow-down"></i> Show More</button>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('vuejsscript')
<script src="{{asset('/js/vuejs/arrival_update.js')}}"></script>
@endsection
