@extends('layout.index2')

@section('body')

@slot('title')

@section('body')
<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Summary Tally  </h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-3 col-sm-3">
                            Reference
                            <div class="hr-line-dashed"></div>

                            <select class="form-control" name="" v-model="reference" id="">
                                    <option value="D">DAY</option>
                                    <option value="M">MONTH</option>
                                    <option value="Y">YEAR</option>
                                    <option value="CR">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-sm-3">
                                Select date..
                                <div class="hr-line-dashed"></div>
                                <span v-show="reference === 'D'">
                                        <input type="text" id="date_filter" v-model="date_filter" placeholder="Please Select date..." class="form-control">
                                </span>
                                <span v-show="reference === 'M'">
                                        <input type="text" id="date_month"  placeholder="Please Select month..." class="form-control">
                                </span>
                                <span v-show="reference === 'Y'">
                                        <input type="text" id="date_year"  placeholder="Please Select year..." class="form-control">
                                </span>
                                <span v-show="reference === 'CR'">
                                        <div class="input-daterange input-group" id="datepicker">

                                            <input readonly="true" type="text" class="input-sm form-control" id="range_start" name="start" />

                                            <span class="input-group-addon">to</span>

                                            <input readonly="true" type="text" class="input-sm form-control" id="range_end" name="end"  />

                                        </div>

                                        <button @click="searchRangeDate" :disabled="range_start == '' || range_end == ''" class="btn btn-primary"> Filter </button>

                                </span>

                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row" style="overflow-y:scroll">
                        <h4 class="pull-right"> Click the values to show the details</h4>
                        <div class="col-lg-12">
                            <table style="text-align:center" class="table table-bordered summary">
                                <thead style="text-align:center">
                                <tr>
                                    <th rowspan="2"> FACTORY </th>
                                    <th rowspan="2"> NORTH </th>
                                    <th rowspan="2"> SOUTH </th>
                                    <th rowspan="2"> AT PORT </th>
                                    <th rowspan="2"> IRS </th>
                                    <th colspan="2"> W/ CHASSI </th>
                                    <th rowspan="2"> BEYOND 5 DAYS </th>
                                    <th colspan="5"> DELIVERY </th>
                                    <th rowspan="2"> BERTHED </th>
                                    <th rowspan="2"> DISCHARGE </th>
                                    <th rowspan="2"> GATEPASS </th>
                                    <th colspan="4"> Unloading </th>
                                </tr>
                                <tr>
                                    <th> CEZ 1 </th>
                                    <th> CEZ 2 </th>
                                    <th> FACTORY </th>
                                    <th> CEZ 1 </th>
                                    <th> CEZ 2 </th>
                                    <th> IRS </th>
                                    <th> Total </th>
                                    <th> DIRECT UNLOADING </th>
                                    <th> UNLOADED FROM CY </th>
                                    <th> UNLOADED FROM IRS </th>
                                    <th> Total </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="summary in summary">
                                    <th> @{{summary.name}} </th>
                                    <td @click="showDetails(summary.name,'NORTH')" class="cursor"> @{{summary.north}} </td>
                                    <td @click="showDetails(summary.name,'SOUTH')" class="cursor" > @{{summary.south}} </td>
                                    <td @click="showDetails(summary.name,'PORT')" class="cursor"> @{{summary.at_port}} </td>
                                    <td @click="showDetails(summary.name,'IRS')" class="cursor"> @{{summary.irs}} </td>
                                    <td @click="showDetails(summary.name,'CEZ1_Accu')" class="cursor"> @{{summary.cez1}} </td>
                                    <td @click="showDetails(summary.name,'CEZ2_Accu')" class="cursor"> @{{summary.cez2}} </td>
                                    <td @click="showDetails(summary.name,'Beyond')" class="cursor"> @{{summary.beyond_5_days}} </td>
                                    <td @click="showDetails(summary.name,'DF')" class="cursor"> @{{summary.delivery_factory}} </td>
                                    <td @click="showDetails(summary.name,'DCEZ1')" class="cursor"> @{{ summary.delivery_cez1 }} </td>
                                    <td @click="showDetails(summary.name,'DCEZ2')" class="cursor"> @{{ summary.delivery_cez2 }} </td>
                                    <td @click="showDetails(summary.name,'DI')" class="cursor"> @{{  summary.delivery_irs}} </td>
                                    <td>
                                        @{{summary.delivery_factory + summary.delivery_cez1 + summary.delivery_cez2 + summary.delivery_irs}}
                                    </td>
                                    <td @click="showDetails(summary.name,'BERTHED')" class="cursor"> @{{summary.berthed}} </td>
                                    <td @click="showDetails(summary.name,'DISCHARGE')" class="cursor"> @{{summary.discharge}} </td>
                                    <td @click="showDetails(summary.name,'GATEPASS')" class="cursor"> @{{summary.gatepass}} </td>
                                    <td @click="showDetails(summary.name,'DU')" class="cursor"> @{{summary.direct_unloading}} </td>
                                    <td @click="showDetails(summary.name,'UWC')" class="cursor"> @{{summary.unloading_with_chassis}} </td>
                                    <td @click="showDetails(summary.name,'UI')" class="cursor"> @{{summary.unloading_irs}} </td>
                                    <td>
                                        @{{summary.direct_unloading + summary.unloading_with_chassis + summary.unloading_irs}}
                                    </td>
                                </tr>
                                <tr style="font-weight: bold; background-color:yellow;">
                                    <th> TOTAL </th>
                                    <td  @click="showDetails(summary.name,'NORTH',true)" class="cursor">@{{ _.sumBy(summary,'north') }}</td>
                                    <td  @click="showDetails(summary.name,'SOUTH',true)" class="cursor">@{{ _.sumBy(summary,'south') }}</td>
                                    <td  @click="showDetails(summary.name,'PORT',true)" class="cursor">@{{ _.sumBy(summary,'at_port') }}</td>
                                    <td  @click="showDetails(summary.name,'IRS',true)" class="cursor">@{{ _.sumBy(summary,'irs') }}</td>
                                    <td  @click="showDetails(summary.name,'CEZ1_Accu',true)" class="cursor">@{{ _.sumBy(summary,'cez1') }}</td>
                                    <td  @click="showDetails(summary.name,'CEZ2_Accu',true)" class="cursor">@{{ _.sumBy(summary,'cez2') }}</td>
                                    <td  @click="showDetails(summary.name,'Beyond',true)" class="cursor">@{{ _.sumBy(summary,'beyond_5_days') }}</td>
                                    <td  @click="showDetails(summary.name,'DF',true)" class="cursor">@{{ _.sumBy(summary,'delivery_factory') }}</td>
                                    <td  @click="showDetails(summary.name,'DCEZ1',true)" class="cursor">@{{ _.sumBy(summary,'delivery_cez1') }}</td>
                                    <td  @click="showDetails(summary.name,'DCEZ2',true)" class="cursor">@{{ _.sumBy(summary,'delivery_cez2') }}</td>
                                    <td  @click="showDetails(summary.name,'DI',true)" class="cursor">@{{ _.sumBy(summary,'delivery_irs') }}</td>
                                    <td>
                                        @{{  _.sumBy(summary,'delivery_irs') + _.sumBy(summary,'delivery_cez1') + _.sumBy(summary,'delivery_cez2')  + _.sumBy(summary,'delivery_factory')}}

                                    </td>
                                    <td  @click="showDetails(summary.name,'BERTHED',true)" class="cursor">@{{ _.sumBy(summary,'berthed') }}</td>
                                    <td  @click="showDetails(summary.name,'DISCHARGE',true)" class="cursor">@{{ _.sumBy(summary,'discharge') }}</td>
                                    <td  @click="showDetails(summary.name,'GATEPASS',true)" class="cursor">@{{ _.sumBy(summary,'gatepass') }}</td>
                                    <td  @click="showDetails(summary.name,'DU',true)" class="cursor">@{{ _.sumBy(summary,'direct_unloading') }}</td>
                                    <td  @click="showDetails(summary.name,'UWC',true)" class="cursor">@{{ _.sumBy(summary,'unloading_with_chassis') }}</td>
                                    <td  @click="showDetails(summary.name,'UI',true)" class="cursor">@{{ _.sumBy(summary,'unloading_irs') }}</td>
                                    <td>
                                        @{{  _.sumBy(summary,'direct_unloading') + _.sumBy(summary,'unloading_with_chassis')  + _.sumBy(summary,'unloading_irs')}}

                                    </td>
                                </tr>
                                <tr style="font-weight: bold; background-color:yellow;">
                                    <th colspan="3">TOTAL CNTRS NOT YET UNLOADED</th>
                                    <td @click="showDetails(summary.name,'TOTALALL',true)" class="cursor">@{{ _.sumBy(summary,'at_port') + _.sumBy(summary,'irs') + _.sumBy(summary,'cez1') + _.sumBy(summary,'cez2')}}</td>
                                </tr>
                                </tbody>
                            </table>
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
            <div class="modal-body">

                <div class="hr-line-dashed"></div>
                <div class="row">

                    <div class="col-lg-6 col-sm-6 col-md-offset-6 col-lg-offset-6">
                            <a v-if="details_obj.length > 0" :href="'extract_summary2/' + ((factory == ' ') ? '-' : factory)   + '/' + category + '/' + date_filter + '/' + all + '/'+ last_reference + '/' + ((dateMonth == ' ') ? '-' : dateMonth) + '/' + ((dateYear == ' ') ? '-' : dateYear)  + '/'+ ((range_start == ' ') ? '-' : range_start)  + '/' + ((range_end == ' ') ? '-' : range_end)  " class="btn btn-primary btn-outline btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>
                    </div>
                </div>
                <table id="detail_obj" class="table table-striped">
                    <thead>
                        <tr>
                            <th>BL NO.</th>
                            <th>Factory</th>
                            <th>Invoice Number</th>
                            <th>Supplier</th>
                            <th>Container Number</th>
                            <th>Commodity</th>
                            <th>Connecting Vessel</th>
                            <th>Shipping Line</th>
                            <th>Forwarder</th>
                            <th>POL</th>
                            <th>Country</th>
                            <th>POD</th>
                            <th>Size</th>
                            <th>Container #</th>
                            <th>ETA</th>
                            <th>ATA</th>
                            <th>ATB</th>
                            <th>ATD</th>
                            <th>Actual Process</th>
                            <th>Actual Gatepass</th>
                            <th>Validity Storage</th>
                            <th>Validity Demurrage</th>
                            <th>Revalidity Storage</th>
                            <th>Revalidity Demurrage</th>
                            <th>Dismounted Place</th>
                            <th>Dismounted Date</th>
                            <th>Actual Delivery</th>
                            <th>Unload</th>
                            <th>Date of return</th>





                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="details in filteredList">
                            <td class="stickycolumn">@{{details.bl_no_fk}}</td>
                            <td>@{{details.factory}}</td>
                            <td>@{{details.invoices}}</td>
                            <td>@{{details.supplier}}</td>
                            <td >@{{details.container_number}}</td>
                            <td>@{{details.commodity}}</td>
                            <td>@{{details.connecting_vessel}}</td>
                            <td>@{{details.shipping_line}}</td>
                            <td>@{{details.forwarder}}</td>
                            <td>@{{details.pol}}</td>
                            <td>@{{details.country}}</td>
                            <td>@{{details.pod}}</td>
                            <td>@{{details.container_type}}</td>
                            <td>@{{details.container_number}}</td>
                            <td>@{{details.estimated_time_arrival}}</td>
                            <td>@{{details.actual_time_arrival}}</td>
                            <td>@{{details.actual_berthing_date}}</td>
                            <td>@{{details.actual_discharge}}</td>
                            <td>@{{details.actual_process}}</td>
                            <td>@{{details.actual_gatepass}}</td>
                            <td>@{{details.validity_storage}}</td>
                            <td>@{{details.validity_demurrage}}</td>
                            <td>@{{details.revalidity_storage}}</td>
                            <td>@{{details.revalidity_demurrage}}</td>
                            <td>@{{details.dismounted_cy }}</td>
                            <td>@{{details.dismounted_date }}</td>
                            <td>@{{details.pull_out }}</td>
                            <td>@{{details.unload }}</td>
                            <td>@{{details.return_date }}</td>






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
    <script src="{{asset('/js/vuejs/summary_tally.js')}}"></script>
@endsection

