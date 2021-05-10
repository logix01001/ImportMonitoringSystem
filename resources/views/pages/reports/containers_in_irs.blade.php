@extends('layout.index2')

@section('body')

<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Containers in IRS as of <span style="color:red" >@{{ date_today }} </span></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div id="container" style="min-width: 310px; height: 600px; margin: 0 auto"></div>
                    <div class="hr-line-dashed"></div>
                    <h4 class="pull-right"> Click the values to show the details</h4>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>FACTORY</th>
                                        <th>IRS</th>
                                    </tr>
                                   
                                </thead>
                                <tbody>
                                    <tr v-for="ld in list_bl_volume">
                                        <td>@{{ ld.name }}</td>
                                        <td  class="cursor" @click="showDetails(ld.name,false)">@{{ ld.irs }}</td>
                                    </tr>
                                    <tr style="font-weight: bold; background-color:yellow;">
                                            <th> TOTAL </th>
                                            <td  @click="showDetails('false',true)" class="cursor">@{{ _.sumBy(list_bl_volume,'irs') }}</td>
                                          
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
</div>

@endsection

@section('vuejsscript')
    <script src="{{asset('/js/vuejs/containers_in_irs.js')}}"></script>
@endsection

