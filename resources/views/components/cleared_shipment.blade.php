 
                   
<div class="row">
    <div class="col-lg-2">
        <br>
        <button type="button" :disabled="loading_data" id="loading-example-btn" class="btn btn-white btn-sm" @click="refresh"><i class="fa fa-sync-alt "></i> Refresh</button>
       
    </div>
    <div class="col-lg-2">
        <label for="filter_search">Search by</label>
        <select name="" class="form-control" id="filter_search" v-model="filter_search">
            <option value="BL">BL #</option>
            <option value="CN">Container Number</option>
        </select>
    </div>
    <div class="col-lg-3">
        <br>
        <div class="input-group">
            <input  :disabled="filter_search == ''" type="text" placeholder=" " v-model="search_cn" class="input-sm form-control"> <span class="input-group-btn">
            <button type="button" @click="filterSearch" class="btn btn-sm btn-primary" :disabled="search_cn.trim().length == 0 || filter_search == '' " > Search </button> </span>
        </div>
    </div>
   
    

</div>
<div class="row">
    <div class="col-lg-3">
        <span class="h4 font-bold m-t block">@{{list_of_BOL.length}} / @{{  list_of_BOL_Total }}</span>  
    </div>
    <div class="col-lg-3  pull-right">
        <div class="col-lg-6 col-sm-6 col-md-offset-6 col-lg-offset-6">
                <a href="/extract_ClearedShipmentExport/" class="btn btn-primary btn-outline btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>
        </div>     
    </div>
</div>
<div v-show="!loading_data">
        <table id="detail_obj"  class="table table-hover table-bordered toggle-arrow-tiny">
            <thead>
                <tr>
                    <th> Factory</th>
                    <th> BL #</th>
                    <th> Container #</th>
                    <th> Invoice #</th>
                    <th> Supplier </th>
                    <th> Commodity </th>
                    <th> Connecting Vessel </th>
                    <th> Shipping Line </th>
                    <th> Forwarder </th>
                    <th> POL </th>
                    <th> Country </th>
                    <th> POD </th>
                    <th> Volume </th>
                    <th> Qty </th>
                    <th> Container Size</th>
                    <th> ATA</th>
                    <th> ATB </th>
                    <th> ATD </th>
                    <th> Gatepass </th>
                    <th> Delivery </th>
                    <th> Counting Days (Discharge Delivery) </th>
                    <th> Dismounted CY </th>
                    <th> Dismounted Date </th>
                    <th> Unload </th>
                    <th> Counting Days (Delivery Return) </th>
                    <th> Date of Return </th>
                    <th> Place of Empty Return </th>

                </tr>
               
            </thead>
            <tbody >
            <template  v-for="(BOL,index) in list_of_BOL">
                <tr>
                   
                    <td>
                        @{{ BOL.factory}}
                    </td>
                    <td class="stickycolumn">
                        @{{ BOL.bl_no}}
                    </td> 
                    <td class="stickycolumn1">
                        @{{ BOL.container_number}}
                    </td>
                    <td >
                        @{{ BOL.invoice_string}}
                    </td>
                    <td>
                        @{{ BOL.supplier}}
                    </td>
                    <td >
                       @{{ BOL.commodities_string }}
                    </td>
                    <td>
                        @{{ BOL.connecting_vessel}}
                    </td>   
                    <td>
                        @{{ BOL.shipping_line}}
                    </td>
                    <td>
                        @{{ BOL.forwarder}}
                    </td>
                    <td>
                        @{{ BOL.pol}}
                    </td>
                    <td>
                        @{{ BOL.country}}
                    </td>
                    <td>
                        @{{ BOL.pod}}
                    </td>
                    <td>
                        @{{ BOL.volume}}
                    </td>
                    <td>
                        @{{ BOL.quantity}}
                    </td>
                    <td>
                        @{{ BOL.container_type}}
                    </td>
                    <td>
                        @{{ BOL.actual_time_arrival}}
                    </td>
                    <td>
                        @{{ BOL.actual_berthing_date}}
                    </td>
                    <td>
                        @{{ BOL.actual_discharge}}
                    </td>
                    <td>
                        @{{ BOL.actual_gatepass}}
                    </td>
                    <td>
                        @{{ BOL.pull_out}}
                    </td>
                    <td>
                        <span v-if="BOL.actual_discharge != null && BOL.pull_out != null">
                            <span v-if="CountingDays(BOL.actual_discharge,BOL.pull_out)  > 1">
                                @{{ CountingDays(BOL.actual_discharge,BOL.pull_out) }} Days
                            </span>
                            <span v-else>
                                @{{ CountingDays(BOL.actual_discharge,BOL.pull_out) }} Day
                            </span>
                        </span>
                    </td>
                    <td>
                        @{{ BOL.dismounted_cy}}
                    </td>
                    <td>
                        @{{ BOL.dismounted_date}}
                    </td>
                    <td>
                        @{{ BOL.unload}}
                    </td>
                    <td>
                        <span v-if="BOL.pull_out != null && BOL.return_date != null">
                            <span v-if="CountingDays(BOL.pull_out,BOL.return_date)  > 1">
                                   
                                    @{{ CountingDays(BOL.pull_out,BOL.return_date) }} Days
                            </span>
                            <span v-else>
                                    @{{ CountingDays(BOL.pull_out,BOL.return_date) }} Day
                            </span>
                        </span>
                       
                    </td>
                    <td>
                        
                        @{{ BOL.return_date}}
                    </td>
                    <td>
                        @{{ BOL.return_cy}}
                    </td>
                   
                  
                </tr>
            </template>
        </tbody>
    </table>
</div>
    <center v-if="loading_data"><img src="{{ asset('img/loading.gif') }}"></center>

  