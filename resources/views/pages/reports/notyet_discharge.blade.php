@extends('layout.index2')

@section('body')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Not yet discharge</h5>
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
                            <button   @click="searchDate(null,'CR')" :disabled="range_start == '' || range_end == ''" class="btn btn-primary"> Filter </button>  
                        </span>
                    </div>
                    <div class="col-lg-2 col-sm-2">
                        &nbsp;
                        <div class="hr-line-dashed"></div>
                        <button @click="searchDate(date_today,'CR',true)" class="btn btn-primary btn-block"> As of @{{ date_today }}</button>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="row">
                    <h4 class="pull-right"> Click the values to show the details</h4>
                    <div class="col-lg-12 col-md-12">
                        <table class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th rowspan="2">Factory</th>
                                    <th colspan="2">North</th>
                                    <th colspan="2">South</th>
                                    <th colspan="2">Total</th>
                                </tr>
                                <tr>
                                    <th>BL</th>
                                    <th>Volume</th>
                                    <th>BL</th>
                                    <th>Volume</th>
                                    <th>BL</th>
                                    <th>Volume</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="ld in list_bl_volume">
                                    <td>@{{ ld.name }}</td>
                                    <td class="cursor" @click="showDetails(ld.name,'DISTINCT','NORTH',false,false)">@{{ ld.bl_north }}</td>
                                    <td class="cursor" @click="showDetails(ld.name,'DISTINCT','NORTH',false,false)">@{{ ld.volume_north }}</td>
                                    <td class="cursor" @click="showDetails(ld.name,'DISTINCT','SOUTH',false,false)">@{{ ld.bl_south }}</td>
                                    <td class="cursor" @click="showDetails(ld.name,'DISTINCT','SOUTH',false,false)">@{{ ld.volume_south }}</td>
                                    <td class="cursor" @click="showDetails(ld.name,'DISTINCT','false',true,false)">@{{ ld.total_bl }}</td>
                                    <td class="cursor" @click="showDetails(ld.name,'DISTINCT','false',true,false)">@{{ ld.total_volume }}</td>
                                </tr>
                                <tr style="font-weight: bold; background-color:yellow;">
                                    <th> TOTAL </th>
                                    <td  @click="showDetails(false,'','NORTH',false,true)" class="cursor">@{{ _.sumBy(list_bl_volume,'bl_north') }}</td>
                                    <td  @click="showDetails(false,'','NORTH',false,true)" class="cursor">@{{ _.sumBy(list_bl_volume,'volume_north') }}</td>
                                    <td  @click="showDetails(false,'','SOUTH',false,true)" class="cursor">@{{ _.sumBy(list_bl_volume,'bl_south') }}</td>
                                    <td  @click="showDetails(false,'','SOUTH',false,true)" class="cursor">@{{ _.sumBy(list_bl_volume,'volume_south') }}</td>
                                    <td  @click="showDetails(false,'','false',true,true)" class="cursor">@{{ _.sumBy(list_bl_volume,'total_bl') }}</td>
                                    <td  @click="showDetails(false,'','false',true,true)" class="cursor">@{{ _.sumBy(list_bl_volume,'total_volume') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal fade myModal5" id="myModal5" tabindex="-1" role="dialog"  aria-hidden="true">
      
        <div class="modal-dialog modal-lg modal-request-lg ">
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
                                <a v-if="details_obj.length > 0" :href="'extract_notyet_discharge/' + factory  + '/' + last_reference  + '/' + pod + '/' + date_filter  + '/' + dateMonth + '/' + dateYear + '/'+ range_start + '/' + range_end + '/' + as_of_now" class="btn btn-primary btn-outline btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>
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
    
    <script src="{{asset('/js/vuejs/notyet_discharge.js')}}"></script>

@endsection

