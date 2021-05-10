 
                   
<div class="row">
    <div class="col-lg-2">
        <br>
        <button type="button"  :disabled="loading_data"  id="loading-example-btn" class="btn btn-white btn-sm" @click="refresh"><i class="fa fa-sync-alt"></i> Refresh</button>
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
        {{-- <button @click="changeMode"  class="btn btn-md dim btn-outline btn-success pull-right">
            <span v-if="view_mode">
                <i class="fa fa-edit fa-2x"></i> <br>
                Edit
            </span>
            <span  v-if="!view_mode">
                <i class="fa fa-eye fa-2x"></i> <br>
                View
            </span>
        </button> --}}
    </div>
    <div class="col-lg-2">
        <label for="">Filter Validity</label>
        <select name="" @change="filterValidityStorage" class="form-control" id="filter_search" v-model="filter_validity_date">
                <option value=""></option>
                <option v-for="s in validity_storage" :value="s.validity_storage">@{{s.validity_storage}}</option>
            
        </select>
    </div>
    <div class="col-lg-2">
        <label for="">Search by</label>
        <select name="" class="form-control" id="filter_search" v-model="filter_search">
            <option value="BL">BL #</option>
            <option value="CN">Container Number</option>
        </select>
    </div>
    <div class="col-lg-3">
        <br>
        <div class="input-group">
            <input @keyup="filterSearch" type="text" placeholder=" " v-model="search_cn" class="input-sm form-control"> <span class="input-group-btn">
            <button type="button" @click="filterSearch" class="btn btn-sm btn-primary" :disabled="search_cn.trim().length == 0 || filter_search == '' " > Search </button> </span>
        </div>
    </div>
    <div class="col-lg-3">

    </div>

</div>
<div class="row">
    <div class="col-lg-3">
        <span class="h4 font-bold m-t block">@{{list_of_BOL.length}} / @{{  list_of_BOL_Total }}</span>  
        <h3 >Showing Records</h3>
    </div>
</div>
<div class="row" id="tablerow">

        <table  class="table table-hover toggle-arrow-tiny"  data-page-size="15">
            <thead>
                <tr>
                    <th data-sort-ignore="true"></th>
                    <th>Consignee</th>
                    <th>BL #</th>
                    <th>Commodities</th>
                    <th>Shipping Line</th>
                    <th>Container(s) #</th>
                </tr>
            </thead>
        <tbody v-if="!loading_data">
            <template  v-for="(BOL,index) in list_of_BOL">
                <tr style=" position: sticky;">
                    <td  @dblclick="toggle(BOL.id)" style="cursor:pointer">
                            <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                            <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                    </td>
                    <td>
                        @{{BOL.factory}}
                    </td>
                    <td >
                        @{{BOL.bl_no}}
                    </td>
                    <td >
                        <span v-for="(c,index) in BOL.commodities">
                            @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                        </span>
                    </td>
                    <td>
                        @{{BOL.shipping_line}}
                    </td>   
                    
                    <td>
                        @{{BOL.volume}}
                    </td>
                    
                </tr>
                <tr v-if="opened.includes(BOL.id)">
                    <td colspan="20">
                        <div class="table-container">
                            
                            

                            <table  class="table table-bordered table-bordered-dark">
                                <thead>
                                    <tr>
                                        <th class="tablewithrowspan column100"> Type </th>
                                        <th class="tablewithrowspan column100"> Container # </th>
                                        <th class="tablewithrowspan column50"> Xray </th>
                                        <th class="tablewithrowspan column100"> Safekeep Date </th>
                                       
                                    </tr>
                                   
                                </thead>
                                <tbody style="font-size:15px">
                                    <tr v-for="(container,index2) in BOL.container_numbers"  >
                                            <td :class="container.quantity == 0 ? 'quadrat' : ''" >@{{container.container_type}}</td>
                                            <td :class="container.quantity == 0 ? 'quadrat' : ''" 
                                            
                                            ><span  :style="container.xray == 1? {'color':'red'}:{}">@{{container.container_number}}</span></td>
                                            <td class="column50" style="text-align:center" >
                                                    <span v-if="container.xray == 1"  >
                                                        <i class="fa fa-check"></i>
                                                    </span>
                                            </td>
                                            <td>
                                                    <span v-show="view_mode">
                                                        @{{container.safe_keep}}
                                                    </span>
                                                    <span v-show="!view_mode">
                                                        <input 
                                                        :data-index="index" placeholder="YYYY-MM-DD"  readonly='true' :data-index_container="index2"
                                                        type="text" 
                                                        class="form-control safe_keep" 
                                                        v-model="container.safe_keep">
                                                    
                                                    </span>
                                                </td>
                                    </tr>
                                </tbody>
                            </table>



                        </div>
                    </td>
                    </tr>
                {{-- <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="color:#eaba6b">Qty</th>
                                <th style="color:green">Container #</th>
                                <th  style="color:violet">
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
                                    <td v-show="!view_mode"><input :data-index="index" :data-index_container="index2" type="text" class="form-control container_discharge" v-model="container.actual_discharge"></td>
                                    <td v-show="view_mode">@{{container.actual_discharge}}</td>
                            </tr>
                        </tbody>
                    </table> --}}
            </template>
        </tbody>
    </table>
    <center v-if="loading_data"><img src="{{ asset('img/loading.gif') }}"></center>
</div>     