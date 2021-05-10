@extends('layout.index2')

@section('body')

@slot('title')

@section('body')
<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Beyond Storage Freetime Summary </h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-4 col-sm-4">
                            <select class="form-control" @change="selectYear" v-model="selected_year">
                                <option :selected="Year.Year == selected_year" v-show="Year.Year" :value="Year.Year" v-for="Year in distinct_year">@{{Year.Year}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                                <div id="container" style=" height: 500px; margin: 0 auto"></div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <h4 class="pull-right"> Click the values to show the details</h4>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>MONTH</th>
                                    @for ($i = 1; $i < 32; $i++)
                                        <th>{{$i}}</th>
                                    @endfor
                                    <th>TOTAL</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <tr v-for="(sum,index) in summary">
                                    <th>@{{ index }}</th>
                                    <td class="cursor" v-for="(value,index2) in sum" @click="showDetails(null,index2,index,'BFT_SUMMARY',true)">@{{ value }}</td>  
                                    <td v-for="n in (31 - sum.length) "></td>
                                    <th class="redboldfont">@{{_.sum(summary[index])}}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="hr-line-dashed"></div>
                    
                   

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
                                            <a v-if="details_obj.length > 0" :href="'extract_summary/' + factory  + '/' + category + '/' + date_filter + '/' + all" class="btn btn-primary btn-outline btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>
                                    </div>        
                                   
                                </div>
                        {{-- <a v-if="details_obj.length > 0" :href="'/extract_summary/' + factory  + '/' + category + '/' + date_filter + '/' + all" class="btn btn-primary btn-block"> <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a> --}}
                    </div>
                {{-- <h2>Total Record : <span style="color:red"> @{{details_obj.length}} </span> </h2> --}}
                    <table id="detail_obj" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Consignee</th>
                                <th>BL</th>
                                <th>Container #</th>
                                <th>Commodity</th>
                                <th>Shipping line</th>
                                <th>POD</th>
                                <th>Container size</th>
                                <th>Reason of Delay</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="details in filteredList">
                                <td>@{{details.factory}}</td>
                                <td class="stickycolumn">@{{details.bl_no_fk}}</td>
                                <td>@{{details.container_number}}</td>
                                <td>@{{details.commodity}}</td>
                                <td>@{{details.shipping_line}}</td>
                                <td>@{{details.pod}}</td>
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
    <script src="{{asset('/js/vuejs/beyond_free_time_summary.js')}}"></script>
@endsection

