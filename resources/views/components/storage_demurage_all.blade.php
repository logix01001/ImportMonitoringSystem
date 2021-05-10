<div class="row">
    <div class="col-lg-2">
        <br>
        <button type="button" :disabled="loading_data"  id="loading-example-btn" class="btn btn-white btn-sm" @click="refresh"><i class="fa fa-sync-alt"></i>
            Refresh</button>
        <button @click="changeMode" class="btn  btn-white btn-sm">
            <span v-if="view_mode">
                <i class="fa fa-edit"></i>
                Edit
            </span>
            <span v-if="!view_mode">
                <i class="fa fa-eye"></i>
                View
            </span>
        </button>
        {{-- <button @click="changeMode" class="btn btn-md dim btn-outline btn-success pull-right">
            <span v-if="view_mode">
                <i class="fa fa-edit fa-2x"></i> <br>
                Edit
            </span>
            <span v-if="!view_mode">
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
            <input @keyup="filterSearch" type="text" placeholder=" " v-model="search_cn" class="input-sm form-control">
            <span class="input-group-btn">
                <button type="button" @click="filterSearch" class="btn btn-sm btn-primary" :disabled="search_cn.trim().length == 0 || filter_search == '' ">
                    Search </button> </span>
        </div>
    </div>
    <div class="col-lg-3">

    </div>

</div>
<div class="row">
    <div class="col-lg-3">
        <span class="h4 font-bold m-t block">@{{list_of_BOL.length}} / @{{  list_of_BOL_Total }}</span>
        <h3>Showing Records</h3>
    </div>
</div>
<div class="row" id="tablerow">

    <table class="table table-hover toggle-arrow-tiny" data-page-size="15">
        <thead>
            <tr>
                <th data-sort-ignore="true"></th>
                <th>Consignee</th>
                <th>BL #</th>
                <th>Commodities</th>
                <th>Shipping Line</th>
                <th>TSAD #</th>
                <th>Container(s) #</th>
            </tr>
        </thead>
        <tbody v-if="!loading_data">
            <template v-for="(BOL,index) in list_of_BOL">
                <tr style=" position: sticky;">
                    <td @dblclick="toggle(BOL.id)" style="cursor:pointer">
                        <i v-show="!opened.includes(BOL.id)" class="fa fa-angle-right"></i>
                        <i v-show="opened.includes(BOL.id)" class="fa fa-angle-down"></i>
                    </td>
                    <td>
                        @{{BOL.factory}}
                    </td>
                    <td>
                        @{{BOL.bl_no}}
                    </td>
                    <td>
                        <span v-for="(c,index) in BOL.commodities">
                            @{{c.commodity}} <span v-if="index+1 != BOL.commodities.length ">,</span>
                        </span>
                    </td>
                    <td>
                        @{{BOL.shipping_line}}
                    </td>
                    <td>
                        @{{BOL.tsad_no}}
                    </td>
                    <td>
                        @{{BOL.volume}}
                    </td>

                </tr>
                <tr v-if="opened.includes(BOL.id)">
                    <td colspan="20">
                        <div class="table-container">



                            <table class="table table-bordered table-bordered-dark fixTable2">
                                <thead>
                                    <tr>
                                        <th class="tablewithrowspan column100" rowspan="2"> Type </th>
                                        <th class="tablewithrowspan column200" rowspan="2"> Container # </th>
                                        <th class="tablewithrowspan column50" rowspan="2"> Xray </th>
                                        <th class="tablewithrowspan column300" colspan="2"> Validty </th>
                                        <th class="tablewithrowspan column300" colspan="2"> Revalidation </th>
                                        <th class="tablewithrowspan column300" rowspan="2">
                                            Revalidation Status
                                        </th>
                                        <th class="tablewithrowspan column300" rowspan="2"> Trucker </th>
                                        <th class="tablewithrowspan column100" rowspan="2"> Booking Time </th>
                                        <th class="tablewithrowspan column500" colspan="3"> Pull out </th>
                                        <th class="tablewithrowspan column100" rowspan="2"> Counting Days (Discharge to
                                            Delivery) </th>
                                        <th class="tablewithrowspan column300" colspan="2"> Dismounted/Bobtail </th>
                                        <th class="tablewithrowspan column200" rowspan="2"> Unload </th>
                                        <th class="tablewithrowspan column300" rowspan="2"> Reason of Delay Delivery
                                        </th>
                                        <th class="tablewithrowspan column200" rowspan="2"> Safekeep Date </th>
                                        <th class="tablewithrowspan column500" colspan="4"> Return / Round use </th>
                                    </tr>
                                    <tr>
                                        <th class="tablewithrowspan">Storage</th>
                                        <th class="tablewithrowspan">Demurrage</th>
                                        <th class="tablewithrowspan">Storage</th>
                                        <th class="tablewithrowspan">Demurrage</th>
                                        <th class="tablewithrowspan">Date</th>
                                        <th class="tablewithrowspan">Box # </th>
                                        <th class="tablewithrowspan">Summary # </th>
                                        <th class="tablewithrowspan">CY</th>
                                        <th class="tablewithrowspan">Date</th>
                                        <th class="tablewithrowspan">CY</th>
                                        <th class="tablewithrowspan">Date</th>
                                        <th class="tablewithrowspan"> Box # </th>
                                        <th class="tablewithrowspan"> Summary # </th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:15px">
                                    <tr v-for="(container,index2) in BOL.container_numbers">
                                        <td :class="container.quantity == 0 ? 'quadrat' : ''">@{{container.container_type}}</td>
                                        <td :class="container.quantity == 0 ? 'quadrat stickycolumn' : 'stickycolumn'"><span
                                                :style="container.xray == 1? {'color':'red'}:{}">@{{container.container_number}}</span>
                                        </td>
                                        <td class="column50 " style="text-align:center">
                                            <span v-show="view_mode">
                                                <span v-if="container.xray == 1">
                                                    <i class="fa fa-check"></i>
                                                </span>
                                            </span>
                                            <span v-show="!view_mode">
                                                <input type="checkbox" @change="xray(container.id,container.xray)"
                                                    v-model="container.xray">
                                            </span>

                                        </td>
                                        {{-- <td :style="compareDate(dateToday,container.validity_storage) && container.revalidity_storage == null ? {'background-color':'#f97272' } : {}">
                                            <span v-show="view_mode" :style="compareDate(dateToday,container.validity_storage) && container.revalidity_storage == null ? { 'color': '#efefef' } : {}">
                                                @{{container.validity_storage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input type="text" :data-index="index" :data-index_container="index2"
                                                    class="form-control validity_storage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.validity_storage">
                                            </span>
                                        </td>
                                        <td :style="compareDate(dateToday,container.validity_demurrage) && container.revalidity_demurrage == null ? {'background-color':'#f97272' } : {}">

                                            <span v-show="view_mode" :style="compareDate(dateToday,container.validity_demurrage) && container.revalidity_demurrage == null ? { 'color': '#efefef' } : {}">
                                                @{{container.validity_demurrage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input type="text" :data-index="index" :data-index_container="index2"
                                                    class="form-control validity_demurrage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.validity_demurrage">
                                            </span>
                                        </td>
                                        <td :style="((compareDate(dateToday,container.validity_storage)  &&   (container.validity_storage != null && container.revalidity_storage  == null)) || (compareDate(dateToday,container.revalidity_storage)  &&   container.revalidity_storage != null)  )  ? {'background-color':'#f97272' } : {}">

                                            <span :style="((compareDate(dateToday,container.validity_storage) &&  (container.validity_storage != null && container.revalidity_storage  == null)) || (compareDate(dateToday,container.revalidity_storage)  &&   container.revalidity_storage != null)  ) ? { 'color': '#efefef' } : {}"
                                                v-show="view_mode">
                                                @{{container.revalidity_storage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input :data-index="index" :data-index_container="index2" :disabled="!compareDate(dateToday,container.validity_storage)"
                                                    type="text" class="form-control revalidity_storage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.revalidity_storage">
                                            </span>

                                        </td>
                                        <td :style="((compareDate(dateToday,container.validity_demurrage) && (container.validity_demurrage != null && container.revalidity_demurrage == null)) || (compareDate(dateToday,container.revalidity_demurrage)  &&   container.revalidity_demurrage != null)  ) ? {'background-color':'#f97272' } : {}">
                                            <span :style="((compareDate(dateToday,container.validity_demurrage) && (container.validity_demurrage != null && container.revalidity_demurrage == null)) || (compareDate(dateToday,container.revalidity_demurrage)  &&   container.revalidity_demurrage != null)  ) ? { 'color': '#efefef' } : {}"
                                                v-show="view_mode">
                                                @{{container.revalidity_demurrage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input :data-index="index" :data-index_container="index2" :disabled="!compareDate(dateToday,container.validity_demurrage)"
                                                    type="text" class="form-control revalidity_demurrage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.revalidity_demurrage">
                                            </span>
                                        </td> --}}
                                        <td v-if="container.pull_out != null">
                                            <span v-show="view_mode">
                                                @{{container.validity_storage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input type="text" :data-index="index" :data-index_container="index2"
                                                    class="form-control validity_storage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.validity_storage">
                                            </span>
                                        </td>
                                        <td v-if="container.pull_out == null" :style="compareDate(dateToday,container.validity_storage) && container.revalidity_storage == null ? {'background-color':'#f97272' } : {}">
                                            <span v-show="view_mode" :style="compareDate(dateToday,container.validity_storage) && container.revalidity_storage == null ? { 'color': '#efefef' } : {}">
                                                @{{container.validity_storage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input type="text" :data-index="index" :data-index_container="index2"
                                                    class="form-control validity_storage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.validity_storage">
                                            </span>
                                        </td>

                                        <td v-if="container.pull_out != null">
                                            <span v-show="view_mode">
                                                @{{container.validity_demurrage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input type="text" :data-index="index" :data-index_container="index2"
                                                    class="form-control validity_demurrage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.validity_demurrage">
                                            </span>
                                        </td>
                                        <td v-if="container.pull_out == null" :style="compareDate(dateToday,container.validity_demurrage) ? {'background-color':'#f97272' } : {}">

                                            <span v-show="view_mode" :style="compareDate(dateToday,container.validity_demurrage) ? { 'color': '#efefef' } : {}">
                                                @{{container.validity_demurrage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input type="text" :data-index="index" :data-index_container="index2"
                                                    class="form-control validity_demurrage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.validity_demurrage">
                                            </span>
                                        </td>

                                        <td v-if="container.pull_out != null">
                                            <span v-show="view_mode">
                                                @{{container.revalidity_storage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input :data-index="index" :data-index_container="index2" :disabled="!compareDate(dateToday,container.validity_storage)"
                                                    type="text" class="form-control revalidity_storage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.revalidity_storage">
                                            </span>
                                        </td>
                                        <td v-if="container.pull_out == null" :style="((compareDate(dateToday,container.validity_storage)  &&   (container.validity_storage != null && container.revalidity_storage  == null)) || (compareDate(dateToday,container.revalidity_storage)  &&   container.revalidity_storage != null)  )  ? {'background-color':'#f97272' } : {}">

                                            <span :style="((compareDate(dateToday,container.validity_storage) &&  (container.validity_storage != null && container.revalidity_storage  == null)) || (compareDate(dateToday,container.revalidity_storage)  &&   container.revalidity_storage != null)  ) ? { 'color': '#efefef' } : {}"
                                                v-show="view_mode">
                                                @{{container.revalidity_storage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input :data-index="index" :data-index_container="index2" :disabled="!compareDate(dateToday,container.validity_storage)"
                                                    type="text" class="form-control revalidity_storage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.revalidity_storage">
                                            </span>

                                        </td>


                                        <td v-if="container.pull_out != null">
                                            <span v-show="view_mode">
                                                @{{container.revalidity_demurrage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input :data-index="index" :data-index_container="index2" :disabled="!compareDate(dateToday,container.validity_demurrage)"
                                                    type="text" class="form-control revalidity_demurrage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.revalidity_demurrage">
                                            </span>
                                        </td>
                                        <td v-if="container.pull_out == null" :style="((compareDate(dateToday,container.validity_demurrage) && (container.validity_demurrage != null && container.revalidity_demurrage == null)) || (compareDate(dateToday,container.revalidity_demurrage)  &&   container.revalidity_demurrage != null)  ) ? {'background-color':'#f97272' } : {}">
                                            <span :style="((compareDate(dateToday,container.validity_demurrage) && (container.validity_demurrage != null && container.revalidity_demurrage == null)) || (compareDate(dateToday,container.revalidity_demurrage)  &&   container.revalidity_demurrage != null)  ) ? { 'color': '#efefef' } : {}"
                                                v-show="view_mode">
                                                @{{container.revalidity_demurrage}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input :data-index="index" :data-index_container="index2" :disabled="!compareDate(dateToday,container.validity_demurrage)"
                                                    type="text" class="form-control revalidity_demurrage" placeholder="YYYY-MM-DD"
                                                    readonly='true' v-model="container.revalidity_demurrage">
                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.revalidity_remarks}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <textarea @blur="saveValidation(container.id,'revalidity_remarks',container.revalidity_remarks)"
                                                    type="text" class="form-control revalidity_remarks" v-model="container.revalidity_remarks"
                                                    @input="container.revalidity_remarks = $event.target.value.toUpperCase()">
                                                    </textarea>
                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.trucker}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <span hidden>@{{container.id}}</span>
                                                <select name="" :data-index="index" :data-index_container="index2"
                                                    class="trucker" v-model="container.trucker">
                                                    <option value=""></option>
                                                    <option v-for="trucker in truckers" v-if="trucker.trucker != null"
                                                        :value="trucker.trucker">@{{trucker.trucker }}</option>
                                                </select>
                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.booking_time}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input @blur="saveValidation(container.id,'booking_time',container.booking_time)"
                                                    type="text" class="form-control" v-model="container.booking_time"
                                                    @input="container.booking_time = $event.target.value.toUpperCase()">

                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.pull_out}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input :data-index="index" placeholder="YYYY-MM-DD" readonly='true'
                                                    :data-index_container="index2" type="text" class="form-control pull_out"
                                                    v-model="container.pull_out">

                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.pull_out_box_number}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input @blur="saveValidation(container.id,'pull_out_box_number',container.pull_out_box_number)"
                                                    type="text" class="form-control" v-model="container.pull_out_box_number"
                                                    @keypress="isNumber" @input="container.pull_out_box_number = $event.target.value.toUpperCase()">

                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.pull_out_summary_number}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input @blur="saveValidation(container.id,'pull_out_summary_number',container.pull_out_summary_number)"
                                                    type="text" class="form-control" v-model="container.pull_out_summary_number"
                                                    @input="container.pull_out_summary_number = $event.target.value.toUpperCase()">

                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="container.pull_out != null || container.pull_out == '' ">
                                                @{{CountingDays(container.actual_discharge,container.pull_out)}} Days
                                            </span>


                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.dismounted_cy}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <span hidden>@{{container.id}}</span>

                                                <select name="" :data-index="index" :data-index_container="index2"
                                                    class="dismounted_cy" v-model="container.dismounted_cy">
                                                    <option value=""></option>
                                                    <option value="IRS BACAO" v-if="_.findIndex(dismounted_cys, {'dismounted_cy': 'IRS BACAO'}) == -1">IRS
                                                        BACAO</option>
                                                    <option value="WITH CHASSI" v-if="_.findIndex(dismounted_cys, {'dismounted_cy': 'WITH CHASSI'}) == -1">WITH CHASSI</option>
                                                   
                                                    <option v-for="cy in dismounted_cys" v-if="cy.dismounted_cy != null && cy.dismounted_cy != ''"
                                                        :value="cy.dismounted_cy">@{{cy.dismounted_cy}}</option>
                                                </select>

                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.dismounted_date}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input :data-index="index" placeholder="YYYY-MM-DD" readonly='true'
                                                    :data-index_container="index2" type="text" class="form-control dismounted_date"
                                                    v-model="container.dismounted_date">

                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.unload}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input :data-index="index" placeholder="YYYY-MM-DD" readonly='true'
                                                    :data-index_container="index2" type="text" class="form-control unload"
                                                    v-model="container.unload">

                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.reason_of_delay_delivery}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <textarea @blur="saveValidation(container.id,'reason_of_delay_delivery',container.reason_of_delay_delivery)"
                                                    type="text" class="form-control" v-model="container.reason_of_delay_delivery"
                                                    @input="container.reason_of_delay_delivery = $event.target.value.toUpperCase()">
                                                    </textarea>
                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.safe_keep}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input :data-index="index" placeholder="YYYY-MM-DD" readonly='true'
                                                    :data-index_container="index2" type="text" class="form-control safe_keep"
                                                    v-model="container.safe_keep">

                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.return_cy}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <span hidden>@{{container.id}}</span>
                                                <select name="" :data-index="index" :data-index_container="index2"
                                                    class="return_cy" v-model="container.return_cy">
                                                    <option value=""></option>
                                                    <option v-for="cy in return_cys" v-if="cy.return_cy != null" :value="cy.return_cy">@{{cy.return_cy}}</option>
                                                </select>

                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.return_date}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input :data-index="index" placeholder="YYYY-MM-DD" readonly='true'
                                                    :data-index_container="index2" type="text" class="form-control return_date"
                                                    v-model="container.return_date">

                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.return_box_number}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input @blur="saveValidation(container.id,'return_box_number',container.return_box_number)"
                                                    type="text" class="form-control" v-model="container.return_box_number"
                                                    @keypress="isNumber" @input="container.return_box_number = $event.target.value.toUpperCase()">

                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="view_mode">
                                                @{{container.return_summary_number}}
                                            </span>
                                            <span v-show="!view_mode">
                                                <input @blur="saveValidation(container.id,'return_summary_number',container.return_summary_number)"
                                                    type="text" class="form-control" v-model="container.return_summary_number"
                                                    @input="container.return_summary_number = $event.target.value.toUpperCase()">

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
                            <th style="color:violet">
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
                            <td v-show="!view_mode"><input :data-index="index" :data-index_container="index2" type="text"
                                    class="form-control container_discharge" v-model="container.actual_discharge"></td>
                            <td v-show="view_mode">@{{container.actual_discharge}}</td>
                        </tr>
                    </tbody>
                </table> --}}
            </template>
        </tbody>
    </table>
    <center v-if="loading_data"><img src="{{ asset('img/loading.gif') }}"></center>
</div>

