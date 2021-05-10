@extends('layout.index2')

@section('body')

@slot('title')

@section('body')
<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Unload Analysis</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-3 col-md-3">
                            <h3>Select Month</h3>
                            <div class="hr-line-dashed"></div>
                            <input type="text" id="date_month" readonly="true" v-model="dateMonth" placeholder="Please Select month..." class="form-control">
                        </div>
                    </div>
                    <hr class="style-one">
                    <div class="row" v-if="unload_series.length > 0">
                        <div class="row_scroll" style="height:auto;overflow-x:scroll">
                            <h2 style="margin:auto;text-align:center">Factories unload containers
                                <br>
                                <small>(# of containers unload per day)  </small>
                                <br>
                            </h2>
                            <br>
                            <table class="table container_new_table table-bordered">
                                <thead>
                                    <tr>
                                        <th> Date </th>
                                        <th v-for="cat in categories" :style="cat.indexOf('Sun') != -1 ? {'color':'red'} : {}"> @{{ cat }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(us,index) in unload_series">
                                        {{-- ,{'color':'white'} --}}
                                        <th class="stickycolumn" style="color:white" :style="{'background-color': container_type_colors[index] }"> @{{ us['name'] }} </th>
                                        <td  v-for="(s,index2) in us['data']" :style="categories[index2].indexOf('Sun') != -1 ? {'background-color':'#f5f5f6'} : {}"> <span v-if="s == 0">-</span><span v-else>@{{ s }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="stickycolumn" style="color:white" :style="{'background-color': container_type_colors[10] }">TOTAL</th>
                                        <td v-for="(tus,index) in total_unload_series" style="font-weight: bold; background-color:yellow;" > <h4>@{{ tus }} </h4></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr class="style-one">
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection

@section('vuejsscript')

<script src="{{asset('/js/vuejs/unload_analysis.js')}}"></script>
@endsection

@section('headscript')

@endsection
