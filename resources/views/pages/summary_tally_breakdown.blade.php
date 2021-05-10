@extends('layout.index2')

@section('body')

@slot('title')

@section('body')
<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Breakdown </h5>
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
                            </select>
                        </div>
                        <div class="col-lg-3 col-sm-3">
                                Select date..
                                <div class="hr-line-dashed"></div>
                                <span v-show="reference === 'D'">
                                        <input type="text" id="date_filter"  placeholder="Please Select date..." class="form-control">
                                </span>
                                <span v-show="reference === 'M'">
                                        <input type="text" id="date_month"  placeholder="Please Select month..." class="form-control">
                                </span>
                                <span v-show="reference === 'Y'">
                                        <input type="text" id="date_year"  placeholder="Please Select year..." class="form-control">
                                </span> 
                               
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-sm-12 col-lg-12">
                            <h2>Containers</h2>
                            <div class="hr-line-dashed"></div>
                            <table style="text-align:center" class="table table-bordered summary">
                                <thead>
                                    <tr>
                                        <th>FACTORY</th>
                                        <th>DISCHARGED</th>
                                        <th>GATEPASS</th>
                                        <th>PULLOUT</th>
                                        <th>IRS BACAO</th>
                                        <th>WITH CHASSIS </th>
                                        <th>UNLOADED</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="d in containers_tally">
                                            <td>@{{d.name}}</td>
                                            <td>@{{d.discharged}}</td>
                                            <td>@{{d.gatepass}}</td>
                                            <td>@{{d.pullout}}</td>
                                            <td>@{{d.irs}}</td>
                                            <td>@{{d.cy}}</td>
                                            <td>@{{d.unloaded}}</td>
                                            <td  style="font-weight: bold; background-color:yellow;">@{{_.sum([d.discharged,d.gatepass,d.pullout,d.irs,d.cy,d.unloaded])}}</td>
                                    </tr>
                                    <tr style="font-weight: bold; background-color:yellow;">
                                            <td>Total</td>
                                            <td>@{{_.sumBy(containers_tally, 'discharged')}}</td>
                                            <td>@{{_.sumBy(containers_tally, 'gatepass')}}</td>
                                            <td>@{{_.sumBy(containers_tally, 'pullout')}}</td>
                                            <td>@{{_.sumBy(containers_tally, 'irs')}}</td>
                                            <td>@{{_.sumBy(containers_tally, 'cy')}}</td>
                                            <td>@{{_.sumBy(containers_tally, 'unloaded')}}</td>  
                                            <td>@{{
                                                _.sum([
                                                    _.sumBy(containers_tally, 'discharged'),
                                                    _.sumBy(containers_tally, 'gatepass'),
                                                    _.sumBy(containers_tally, 'pullout'),
                                                    _.sumBy(containers_tally, 'irs'),
                                                    _.sumBy(containers_tally, 'cy'),
                                                    _.sumBy(containers_tally, 'unloaded'),
                                                ])
                                            }}</td>  
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
</div>
    
@endsection

@section('vuejsscript')
    <script src="{{asset('/js/vuejs/summary_tally_breakdown.js')}}"></script>

    
@endsection

