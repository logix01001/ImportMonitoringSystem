@extends('layout.index2')

@section('body')

@slot('title')

@section('body')
<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Beyond Storage Freetime per day </h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-3 col-sm-3">
                            Select date..
                            <div class="hr-line-dashed"></div>
                            <input type="text" id="date_filter"  placeholder="Please Select date..." class="form-control">   
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                            <h1 style="text-align:center">@{{date_request}}</h1>
                            <h4 style="text-align:center"> --- Click the Beyond and QTY to show the details --- </h4>
                            <div class="hr-line-dashed"></div>
                            <div class="col-sm-12 col-lg-6">
                                <h2>CONTAINERS AND BL # TALLY</h2>
                                
                                <div class="hr-line-dashed"></div>
                                <table style="text-align:left" class="table table-bordered summary">
                                    <thead>
                                        <tr>
                                            <th>FACTORY</th>
                                            <th>Beyond</th>
                                            <th># BL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="report in reports">
                                            <td>
                                                @{{ report.name }}
                                            </td>
                                            
                                            <td class="redboldfont cursor" @click="showDetails(report.name,'BFT_container')">
                                                @{{report.container_count}}
                                            </td>

                                            <td class="redboldfont">
                                                @{{report.bl_count}}
                                            </td>
                                        </tr>
                                        <tr style="font-weight: bold;">
                                            <td>TOTAL</td>
                                            <td class="redboldfont cursor" @click="showDetails(null,'BFT_container',true)" >@{{_.sumBy(reports, 'container_count')}} CNTRS</td>
                                            <td class="redboldfont">@{{_.sumBy(reports, 'bl_count')}} BL</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12 col-lg-6">
                                <h2>PORT CHARGES (STORAGE ESTIMATION)</h2>
                                <div class="hr-line-dashed"></div>
                                <table style="text-align:left" class="table table-bordered summary">
                                    <thead>
                                        <tr>
                                            <th>RATE</th>
                                            <th>QTY</th>
                                            <th>AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>6 - 10 Days ( PHP 1,443.90 / Day )</td>
                                            <td class="redboldfont cursor" @click="showDetails(null,'BFT_SIX',true)">@{{_.sumBy(reports, 'six_days')}}</td>    
                                            <td class="redboldfont">@{{numberWithCommas(port_charges(1,_.sumBy(reports, 'six_days')))}}</td>    
                                        </tr>
                                        <tr>
                                            <td>11 Days & Beyond ( PHP 10,000 / Day )</td>
                                            <td class="redboldfont cursor"  @click="showDetails(null,'BFT_ELEVEN',true)">@{{_.sumBy(reports, 'eleven_days')}}</td>  
                                            <td class="redboldfont">@{{numberWithCommas(port_charges(2,_.sumBy(reports, 'eleven_days')))}}</td>      
                                        </tr>
                                        <tr style="font-weight: bold;">
                                             <td>TOTAL</td>
                                            <td class="redboldfont cursor" @click="showDetails(null,'BFT_container',true)">
                                                @{{
                                                _.sumBy(reports, 'six_days') + _.sumBy(reports, 'eleven_days')
                                                }}
                                                CNTRS
                                            </td>
                                            <td class="redboldfont">
                                                PHP
                                                @{{
                                                   numberWithCommas( 
                                                       parseFloat(port_charges(1,_.sumBy(reports, 'six_days'))) 
                                                       + 
                                                       parseFloat(port_charges(2,_.sumBy(reports, 'eleven_days')) ))
                                                }}

                                                    
                                            </td>
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
                <div class="row">
                    <input v-model="search" class="form-control" placeholder="Filter BL # or Container #">
                </div>
                <div class="hr-line-dashed"></div>   
                <div class="row">
                        <div class="row">
                           
                            <div class="col-lg-6 col-sm-6 col-md-offset-6 col-lg-offset-6">
                                    <a v-if="details_obj.length > 0" :href="'extract_summary2/' + factory  + '/' + category + '/' + date_request + '/' + all" class="btn btn-primary btn-outline btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>
                            </div>        
                            
                        </div>
                    {{-- <a v-if="details_obj.length > 0" :href="'/extract_summary/' + factory  + '/' + category + '/' + date_request    + '/' + all" class="btn btn-primary btn-block"> <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a> --}}
                </div>
                {{-- <h2>Total Record : <span style="color:red"> @{{details_obj.length}} </span> </h2> --}}
             
                         
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
                                        <th>Reason of Delay</th>
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="details in filteredList">
                                        <td class="stickycolumn">@{{details.bl_no_fk}}</td>
                                        <td>@{{details.invoices}}</td>
                                        <td>@{{details.supplier}}</td>
                                        <td>@{{details.container_number}}</td>
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
                                        <td>@{{details.reason_of_delay_delivery}}</td>
                                        
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
   
    <script src="{{asset('/js/vuejs/beyond_free_time_per_day.js')}}"></script>
@endsection

