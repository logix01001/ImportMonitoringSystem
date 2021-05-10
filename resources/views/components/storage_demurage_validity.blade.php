

<div class="row">
    <div class="col-lg-2">
        <br>
        <button type="button" :disabled="loading_data" id="loading-example-btn" class="btn btn-white btn-sm" @click="refresh"><i class="fa fa-sync-alt "></i> Refresh</button>
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
        <label for="filter_search">Search by</label>
        <select name="" class="form-control" id="filter_search" v-model="filter_search">
            <option value="BL">BL #</option>
            <option value="CN">Container Number</option>
        </select>
    </div>
    <div class="col-lg-3">
        <br>
        <div class="input-group">
            <input  type="text" placeholder=" " v-model="search_cn" class="input-sm form-control"> <span class="input-group-btn">
            <button type="button" @click="filterSearch" class="btn btn-sm btn-primary" :disabled="search_cn.trim().length == 0 || filter_search == '' " > Search </button> </span>
        </div>
    </div>



</div>
<div class="row">
    <div class="col-lg-3">
        <span class="h4 font-bold m-t block">@{{list_of_BOL.length}} / @{{  list_of_BOL_Total }}</span>
        <h3 >Showing Records</h3>
    </div>
    <div class="col-lg-6  pull-right">
            <div class="col-lg-6 col-sm-6 col-md-offset-6 col-lg-offset-6">
                    <a href="./extract_container_pull_out/" class="btn btn-primary btn-outline btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>
            </div>
            <h4 style="color:red">* Please fill up both Pull out & Pull out time. if only 1 of those column was filled up, The record will not be saved. </h4>
    </div>
</div>

<div v-show="!loading_data">
        <table id="detail_obj"  class="table table-hover table-bordered toggle-arrow-tiny">
            <thead>
                <tr>

                    <th>Consignee</th>
                    <th>BL #</th>
                    <th>Container #</th>
                    <th>Commodities</th>
                    <th>Shipping Line</th>
                    <th>Container Size</th>

                    <th> Validty Storage </th>
                    <th> Validty Demurrage </th>
                    <th> Revalidaty Storage </th>
                    <th> Revalidaty Demurrage </th>
                    <th>
                        Revalidation Status
                    </th>
                    <th> Trucker </th>
                    <th> Dispatch Date </th>
                    <th> Delivery Date </th>
                    <th> Delivery Time </th>
                    <th> Detention Validation </th>
                    <th> Counting Days (Discharge to Delivery) </th>
                    <th> Reason of Delay Delivery </th>
                    <th> Remarks </th>
                    <th> Final Remarks </th>

                </tr>

            </thead>
            <tbody v-if="!loading_data">
            <template  v-for="(BOL,index) in list_of_BOL">
                <tr>

                    <td>
                        @{{BOL.factory}}
                    </td>
                    <td class="stickycolumn">
                        @{{BOL.bl_no}}
                    </td>
                    <td class="stickycolumn1">
                        @{{BOL.container_number}}
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
                        @{{BOL.container_type}}
                    </td>
                    <td v-if="BOL.pull_out != null">
                        <template v-if="view_mode">
                            @{{ BOL.validity_storage}}
                        </template>
                        <template v-if="!view_mode">
                                <input type="text" :data-index="index"  class="form-control validity_storage" placeholder="YYYY-MM-DD"  readonly='true' v-model="BOL.validity_storage">
                        </template>
                    </td>
                    <td v-if="BOL.pull_out == null" :style="compareDate(dateToday,BOL.validity_storage) && BOL.revalidity_storage == null ? {'background-color':'#f97272' } : {}">
                        <template v-if="view_mode" :style="compareDate(dateToday,BOL.validity_storage) && BOL.revalidity_storage == null ? { 'color': '#efefef' } : {}">
                            @{{ BOL.validity_storage }}
                        </template>
                        <template v-if="!view_mode">
                                <input type="text" :data-index="index"  class="form-control validity_storage" placeholder="YYYY-MM-DD"  readonly='true' v-model="BOL.validity_storage">
                        </template>
                    </td>

                    <td v-if="BOL.pull_out != null">
                        <template v-if="view_mode">
                            @{{ BOL.validity_demurrage }}
                        </template>
                        <template v-if="!view_mode">
                            <input type="text"
                            :data-index="index"
                            class="form-control validity_demurrage" placeholder="YYYY-MM-DD"  readonly='true' v-model="BOL.validity_demurrage">
                        </template>
                    </td>
                    <td v-if="BOL.pull_out == null" :style="compareDate(dateToday,BOL.validity_demurrage) && BOL.revalidity_demurrage == null   ? {'background-color':'#f97272' } : {}">

                        <template v-if="view_mode" :style="compareDate(dateToday,BOL.validity_demurrage) && BOL.revalidity_demurrage == null   ? { 'color': '#efefef' } : {}">
                            @{{ BOL.validity_demurrage}}
                        </template>
                        <template v-if="!view_mode">
                            <input type="text"
                            :data-index="index"
                            class="form-control validity_demurrage" placeholder="YYYY-MM-DD"  readonly='true' v-model="BOL.validity_demurrage">
                        </template>
                    </td>

                    <td v-if="BOL.pull_out != null">
                        <template v-if="view_mode">
                            @{{ BOL.revalidity_storage}}
                        </template>
                        <template v-if="!view_mode">
                            <input
                            :data-index="index"
                            :disabled="!compareDate(dateToday,BOL.validity_storage)" type="text" class="form-control revalidity_storage" placeholder="YYYY-MM-DD"  readonly='true' v-model="BOL.revalidity_storage">
                        </template>
                    </td>
                    <td  v-if="BOL.pull_out == null" :style="((compareDate(dateToday,BOL.validity_storage)  &&   (BOL.validity_storage != null && BOL.revalidity_storage  == null)) || (compareDate(dateToday,BOL.revalidity_storage)  &&   BOL.revalidity_storage != null)  )  ? {'background-color':'#f97272' } : {}">

                        <template :style="((compareDate(dateToday,BOL.validity_storage) &&  (BOL.validity_storage != null && BOL.revalidity_storage  == null)) || (compareDate(dateToday,BOL.revalidity_storage)  &&   BOL.revalidity_storage != null)  ) ? { 'color': '#efefef' } : {}" v-show="view_mode">
                            @{{ BOL.revalidity_storage}}
                        </template>
                        <template v-if="!view_mode">

                            <input
                            :data-index="index"
                            :disabled="!compareDate(dateToday,BOL.validity_storage)" type="text" class="form-control revalidity_storage" placeholder="YYYY-MM-DD"  readonly='true' v-model="BOL.revalidity_storage">
                        </template>

                    </td>


                    <td v-if="BOL.pull_out != null">
                        <template v-if="view_mode">
                            @{{ BOL.revalidity_demurrage}}
                        </template>
                        <template v-if="!view_mode">
                            <input
                            :data-index="index"
                            :disabled="!compareDate(dateToday,BOL.validity_demurrage)"
                            type="text" class="form-control revalidity_demurrage" placeholder="YYYY-MM-DD"  readonly='true' v-model="BOL.revalidity_demurrage">
                        </template>
                    </td>
                    <td v-if="BOL.pull_out == null" :style="((compareDate(dateToday,BOL.validity_demurrage) && (BOL.validity_demurrage != null && BOL.revalidity_demurrage == null)) || (compareDate(dateToday,BOL.revalidity_demurrage)  &&   BOL.revalidity_demurrage != null)  ) ? {'background-color':'#f97272' } : {}">
                        <template :style="((compareDate(dateToday,BOL.validity_demurrage) && (BOL.validity_demurrage != null && BOL.revalidity_demurrage == null)) || (compareDate(dateToday,BOL.revalidity_demurrage)  &&   BOL.revalidity_demurrage != null)  ) ? { 'color': '#efefef' } : {}" v-if="view_mode">
                            @{{ BOL.revalidity_demurrage}}
                        </template>
                        <template v-if="!view_mode">
                            <input
                            :data-index="index"
                            :disabled="!compareDate(dateToday,BOL.validity_demurrage)"
                            type="text" class="form-control revalidity_demurrage" placeholder="YYYY-MM-DD"  readonly='true' v-model="BOL.revalidity_demurrage">
                        </template>
                    </td>
                    <td>
                        <template v-if="view_mode">
                            @{{ BOL.revalidity_remarks}}
                        </template>
                        <template v-if="!view_mode">
                            <textarea
                                @blur="saveValidation(BOL.container_id,'revalidity_remarks',BOL.revalidity_remarks)"

                                type="text"
                                class="form-control revalidity_remarks"
                                v-model="BOL.revalidity_remarks"

                                @input="BOL.revalidity_remarks = $event.target.value.toUpperCase()">
                            </textarea>
                        </template>
                    </td>
                    <td>
                        <span v-show="view_mode">
                            @{{ BOL.trucker}}
                        </span>
                        <span v-show="!view_mode">

                            <span hidden>@{{ BOL.id}}</span>
                            <select name="" :data-index="index" class="trucker" v-model="BOL.trucker">
                                    <option value=""></option>
                                    <option v-for="trucker in truckers" v-if="trucker.trucker != null" :value="trucker.trucker">@{{ trucker.trucker }}</option>
                            </select>
                        </span>
                    </td>
                    <td>
                        <template v-if="view_mode">
                            @{{ BOL.dispatched_date}}
                        </template>
                        <template v-if="!view_mode">
                            <input
							:disabled="BOL.actual_gatepass == null"
                            :data-index="index" placeholder="YYYY-MM-DD"  readonly='true'
                            type="text"
                            class="form-control dispatched_date"
                            v-model="BOL.dispatched_date">
							<small style="color:red" v-if="BOL.actual_gatepass == null">No Gatepass yet</small>
                        </template>
                    </td>
                    <td :style="(BOL.pull_out_time != null  && BOL.pull_out_time != '')  && (BOL.pull_out == null || BOL.pull_out === '') ? 'background:red;color:white' : ''">
                        <template v-if="view_mode">

                            @{{ BOL.pull_out}}
                        </template>
                        <template v-if="!view_mode">
                            <input
							:disabled="BOL.actual_gatepass == null || BOL.actual_process == null"
                            :data-index="index" placeholder="YYYY-MM-DD"  readonly='true'
                            type="text"
                            class="form-control pull_out"
                            v-model="BOL.pull_out">
							<small style="color:red" v-if="BOL.actual_gatepass == null">No Gatepass yet</small>
							<small style="color:red" v-if="BOL.actual_process == null"><br> No Actual Process yet</small>
                        </template>
                    </td>
                    <td :style="(BOL.pull_out != null  && BOL.pull_out != '')  && (BOL.pull_out_time == null || BOL.pull_out_time === '') ? 'background:red;color:white' : ''">
                        <template v-if="view_mode">
                            @{{ BOL.pull_out_time}}
                        </template>
                        {{--  --}}
                        <template v-if="!view_mode">
                            <input
							:disabled="BOL.actual_gatepass == null || BOL.actual_process == null"
                            @blur="saveDelivery(index)"
                            type="text"
                            class="form-control"
                            v-model="BOL.pull_out_time"

                            @input="BOL.pull_out_time = $event.target.value.toUpperCase()">
							<small style="color:red" v-if="BOL.actual_gatepass == null">No Gatepass yet</small>
                            <small style="color:red" v-if="BOL.actual_process == null"><br> No Actual Process yet</small>
                        </template>
                    </td>
                    <td>
                        <template v-if="view_mode">
                            @{{ BOL.detention_validity}}
                        </template>
                        <template v-if="!view_mode">
                            <input
                            :data-index="index" placeholder="YYYY-MM-DD"  readonly='true'
                            type="text"
                            class="form-control detention_validity"
                            v-model="BOL.detention_validity"

                           >

                        </template>
                    </td>
                    <td>
                        <span style="color:#f89a13" v-if="BOL.actual_discharge == null">
                            -0 Days
                        </span>
                        <span v-else>
                                <span v-if="BOL.pull_out != null || BOL.pull_out == '' ">
                                    @{{CountingDays(BOL.actual_discharge,BOL.pull_out)}} Days
                                </span>
                                <span v-else style="color:#f97272">
                                      @{{CountingDays(BOL.actual_discharge,dateToday)}} Days
                                </span>
                        </span>

                    </td>
                    <td>
                        <template v-if="view_mode">
                            @{{ BOL.reason_of_delay_delivery}}
                        </template>
                        <template v-if="!view_mode">
                            <textarea
							:disabled="BOL.actual_gatepass == null"
                            @blur="saveValidation(BOL.container_id,'reason_of_delay_delivery',BOL.reason_of_delay_delivery)"
                            type="text"
                            class="form-control"
                            v-model="BOL.reason_of_delay_delivery"

                            @input="BOL.reason_of_delay_delivery = $event.target.value.toUpperCase()">
                            </textarea>
							<small style="color:red" v-if="BOL.actual_gatepass == null">No Gatepass yet</small>
                        </template>
                    </td>
                    <td>
                        <template v-if="view_mode">
                            @{{ BOL.pull_out_remarks}}
                        </template>
                        <template v-if="!view_mode">
                            <input
                            @blur="saveValidation(BOL.container_id,'pull_out_remarks',BOL.pull_out_remarks)"
                            type="text"
                            class="form-control"
                            v-model="BOL.pull_out_remarks"

                            @input="BOL.pull_out_remarks = $event.target.value.toUpperCase()">

                        </template>
                    </td>
                    <td>
                        <span v-if="BOL.reason_of_delay_gatepass">
                                @{{ BOL.reason_of_delay_gatepass }},
                        </span>
                        <span v-if="BOL.sop_remarks">
                                @{{ BOL.sop_remarks }}
                        </span>


                    </td>



                </tr>
            </template>
        </tbody>
    </table>
</div>
<center v-if="loading_data"><img src="{{ asset('img/loading.gif') }}"></center>

