@extends('layout.index2')

@section('body')

<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Daily Process to BOC</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group"><label class="col-sm-2 control-label">Search</label>
                                <div class="col-sm-8"><input type="text" class="form-control"  :value="search_bl_no.toUpperCase()" @input="search_bl_no = $event.target.value.toUpperCase()" v-model="search_bl_no" placeholder="BL Number"></div>
                                <div class="col-sm-2"><button class="btn btn-primary" :disabled="search_bl_no.trim().length == 0" @click="searchBL"> <i class="fa fa-search"></i></button></div>
                            </div>
                        </div>
                    </div>  
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                                <div class="col-lg-6">
                                        <div class="form-group"><label class="col-sm-4 control-label">BL Number</label>

                                            <div class="col-sm-8">
                                            <h4 class="text-navy">@{{newBOL.bl_no}}</h4>
                                                <div class="hr-line-dashed"></div>
                                            </div>
                                            
                                        </div>
                                       
                                        <div class="form-group"><label class="col-sm-4 control-label">Factory</label>
                                            <div class="col-sm-8">
                                                <h4 class="text-navy">@{{newBOL.factory}}</h4>
                                                <div class="hr-line-dashed"></div> 
                                            </div>
                                        </div>
                                        <div class="form-group"><label class="col-sm-4 control-label">Volume</label>
                                            <div class="col-sm-8">
                                                <h4 class="text-navy">@{{newBOL.volume}}</h4>
                                                <div class="hr-line-dashed"></div> 
                                            </div>
                                        </div>
                                        <div class="form-group"><label class="col-sm-4 control-label">Port of Discharge</label>
                                            <div class="col-sm-8">
                                                <h4 class="text-navy">@{{newBOL.pod}}</h4>
                                                <div class="hr-line-dashed"></div> 
                                            </div>
                                        </div>
                                        <div class="form-group"><label class="col-sm-4 control-label">Estimated Time of Arrival</label>
                                            <div class="col-sm-8">
                                                <h4 class="text-navy">@{{newBOL.estimated_time_arrival}}</h4>
                                                <div class="hr-line-dashed"></div> 
                                            </div>
                                        </div>
                                        {{-- <div class="form-group"><label class="col-sm-4 control-label">Reason of Delay Gatepass</label>
                                            <div class="col-sm-8"><input type="text" v-model="newBOL.estimated_time_arrival" class="form-control" disabled>
                                                <div class="hr-line-dashed"></div> 
                                            </div>
                                        </div> --}}
                                        <div class="form-group"><label class="col-sm-4 control-label">Commodities</label>
                                            <div class="col-sm-8">
                                                <ul class="list-group">
                                                    <li class="list-group-item" v-for="commodity in commodities" >
                                                       @{{commodity.commodity}}
                                                    </li>
                                                </ul>
                                                <div class="hr-line-dashed"></div> 
                                            </div>
                                        </div>
                                        
                                </div>
                                <div class="col-lg-6">
                                        <div class="form-group"><label class="col-sm-4 control-label">Date Endorse</label>

                                            <div class="col-sm-8"><input type="text" :disabled="newBOL.bl_no.trim().length == 0"  id="date_endorse" v-model="newBOL.date_endorse"  placeholder="YYYY-MM-DD" class="form-control">
                                                <div class="hr-line-dashed"></div>
                                            </div>
                                            
                                        </div>
                                       
                                        <div class="form-group"><label class="col-sm-4 control-label">Place of Endorsement</label>
                                            <div class="col-sm-8">
                                                <select :disabled="newBOL.bl_no.trim().length == 0" class="boc_select2" v-model="newBOL.place_endorsement"  id="place_endorsement">
                                                        <option value=""></option>
                                                        <option :value="pe.place_endorsement"  v-for="pe in list_of_place_endorsement">@{{pe.place_endorsement}}</option>
                                                </select>
                                                <div class="hr-line-dashed"></div> 
                                            </div>
                                        </div>
                                        <div class="form-group"><label class="col-sm-4 control-label">Actual Process</label>
                                            <div class="col-sm-8"><input type="text" :disabled="newBOL.bl_no.trim().length == 0" id="actual_process" v-model="newBOL.actual_process"  placeholder="YYYY-MM-DD" class="form-control">
                                                <div class="hr-line-dashed"></div> 
                                            </div>
                                        </div>
                                        <div class="form-group"><label class="col-sm-4 control-label">Assesment tag</label>
                                            <div class="col-sm-8">
                                                    <select :disabled="newBOL.bl_no.trim().length == 0" class="boc_select2" v-model="newBOL.assessment_tag"  id="assessment_tag">
                                                            <option value=""></option>
                                                            <option value="RED">RED</option>
                                                            <option value="YELLOW">YELLOW</option>
                                                    </select>
                                                <div class="hr-line-dashed"></div> 
                                            </div>
                                        </div>
                                        <div class="form-group"><label class="col-sm-4 control-label">Remarks of Docs</label>
                                            <div class="col-sm-8"><input type="text" :disabled="newBOL.bl_no.trim().length == 0"   v-model="newBOL.remarks_of_docs" class="form-control">
                                                <div class="hr-line-dashed"></div> 
                                            </div>
                                        </div>
                                        <div class="form-group"><label class="col-sm-4 control-label">TSAD #</label>
                                            <div class="col-sm-8"><input type="text" :disabled="newBOL.bl_no.trim().length == 0"   v-model="newBOL.tsad_no" class="form-control">
                                                <div class="hr-line-dashed"></div> 
                                            </div>
                                        </div>

                                </div>
                    </div>
                    <div class="row">
                        <button 
                        class="btn btn-primary dim btn-block" 
                        @click="save"
                        :disabled="newBOL.bl_no.trim().length == 0 ||  newBOL.actual_process == null"
                        > <i class="fa fa-save"></i> Save</button>
                    </div>  
                    
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Export to Excel</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group"><label class="col-sm-4 control-label">Actual Process</label>
                                        <div class="col-sm-6"><input type="text" class="form-control" v-model="search_actual_process" id="search_actual_process" placeholder="YYYY-MM-DD"></div>
                                        <div class="col-sm-2"><button class="btn btn-primary"> <i class="fa fa-search"></i></button></div>
                                        
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                        <label> Woods <input type="checkbox" class="i-checks"> </label>
                                        <button class="btn btn-outline dim btn-primary pull-right m-t-n-xs"><strong><i class="fa fa-file-excel-o"></i> Excel</strong></button>
                                </div>
                            </div> 
                    </div>
                </div>
            </div>
        </div>


@endsection

@section('vuejsscript')
    <script src="{{asset('/js/vuejs/daily_boc.js')}}"></script>
@endsection

