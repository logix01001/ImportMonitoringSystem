<button class="btn btn-block btn-warning" @click="changeMode">
    <span v-if="view_mode">
        <i class="fa fa-edit"></i>
        Edit
    </span>
    <span v-if="!view_mode">
        <i class="fa fa-eye"></i>
        View
    </span>
</button>

<div class="hr-line-dashed"></div>
<div style="width:100%;overflow-y:scroll">
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
                <th class="tablewithrowspan column200" rowspan="2"> Trucker </th>
                <th class="tablewithrowspan column200" rowspan="2"> Detention Validity </th>
                <th class="tablewithrowspan column300" colspan="2"> Delivery</th>
                <th class="tablewithrowspan column100" rowspan="2"> Counting Days (Discharge to delivery) </th>
                <th class="tablewithrowspan column300" rowspan="2"> Reason of delay delivery </th>
                <th class="tablewithrowspan column300" rowspan="2"> Remarks </th>
            </tr>
            <tr>
                <th class="tablewithrowspan">Storage</th>
                <th class="tablewithrowspan">Demurrage</th>
                <th class="tablewithrowspan">Storage</th>
                <th class="tablewithrowspan">Demurrage</th>
                <th class="tablewithrowspan">Date</th>
                <th class="tablewithrowspan">Time</th>

            </tr>

        </thead>
        <tbody style="font-size:15px">
            <tr v-if="container.pull_out != null" v-for="(container,index2) in list_of_BOL[selectedIndex].container_numbers">
                <td :class="container.quantity == 0 ? 'quadrat' : ''">@{{container.container_type}}</td>
                <td :class="container.quantity == 0 ? 'quadrat' : ''"><span :style="container.xray == 1? {'color':'red'}:{}">@{{container.container_number}}</span></td>
                <td class="column50" style="text-align:center">
                    <span v-show="view_mode">
                        <span v-if="container.xray == 1">
                            <i class="fa fa-check"></i>
                        </span>
                    </span>
                    <span v-show="!view_mode">
                        <input type="checkbox" @change="xray(container.id,container.xray)" v-model="container.xray">
                    </span>

                </td>
                <td :style="compareDate(dateToday,container.validity_storage) && container.revalidity_storage == null ? {'background-color':'#f97272' } : {}">
                    <span v-show="view_mode" :style="compareDate(dateToday,container.validity_storage) && container.revalidity_storage == null ? { 'color': '#efefef' } : {}">
                        @{{container.validity_storage}}
                    </span>
                    <span v-show="!view_mode">
                        <input type="text" :data-index_container="index2" class="form-control validity_storage" placeholder="YYYY-MM-DD"
                            readonly='true' v-model="container.validity_storage">
                    </span>
                </td>
                <td :style="compareDate(dateToday,container.validity_demurrage) ? {'background-color':'#f97272' } : {}">

                    <span v-show="view_mode" :style="compareDate(dateToday,container.validity_demurrage) ? { 'color': '#efefef' } : {}">
                        @{{container.validity_demurrage}}
                    </span>
                    <span v-show="!view_mode">
                        <input type="text" :data-index_container="index2" class="form-control validity_demurrage"
                            placeholder="YYYY-MM-DD" readonly='true' v-model="container.validity_demurrage">
                    </span>
                </td>
                <td :style="((compareDate(dateToday,container.validity_storage)  &&   (container.validity_storage != null && container.revalidity_storage  == null)) || (compareDate(dateToday,container.revalidity_storage)  &&   container.revalidity_storage != null)  )  ? {'background-color':'#f97272' } : {}">

                    <span :style="((compareDate(dateToday,container.validity_storage) &&  (container.validity_storage != null && container.revalidity_storage  == null)) || (compareDate(dateToday,container.revalidity_storage)  &&   container.revalidity_storage != null)  ) ? { 'color': '#efefef' } : {}"
                        v-show="view_mode">
                        @{{container.revalidity_storage}}
                    </span>
                    <span v-show="!view_mode">
                        <input :data-index_container="index2" :disabled="!compareDate(dateToday,container.validity_storage)"
                            type="text" class="form-control revalidity_storage" placeholder="YYYY-MM-DD" readonly='true'
                            v-model="container.revalidity_storage">
                    </span>

                </td>
                <td :style="((compareDate(dateToday,container.validity_demurrage) && (container.validity_demurrage != null && container.revalidity_demurrage == null)) || (compareDate(dateToday,container.revalidity_demurrage)  &&   container.revalidity_demurrage != null)  ) ? {'background-color':'#f97272' } : {}">
                    <span :style="((compareDate(dateToday,container.validity_demurrage) && (container.validity_demurrage != null && container.revalidity_demurrage == null)) || (compareDate(dateToday,container.revalidity_demurrage)  &&   container.revalidity_demurrage != null)  ) ? { 'color': '#efefef' } : {}"
                        v-show="view_mode">
                        @{{container.revalidity_demurrage}}
                    </span>
                    <span v-show="!view_mode">
                        <input :data-index_container="index2" :disabled="!compareDate(dateToday,container.validity_demurrage)"
                            type="text" class="form-control revalidity_demurrage" placeholder="YYYY-MM-DD" readonly='true'
                            v-model="container.revalidity_demurrage">
                    </span>
                </td>
                <td>
                    <span v-show="view_mode">
                        @{{ container.revalidity_remarks}}
                    </span>
                    <span v-show="!view_mode">
                        <textarea @blur="saveValidation(container.id,'revalidity_remarks',container.revalidity_remarks)"
                            type="text" class="form-control revalidity_remarks" v-model="container.revalidity_remarks"
                            @input="container.revalidity_remarks = $event.target.value.toUpperCase()">
                                </textarea>
                    </span>
                </td>
                <td>

                    <span v-show="view_mode" >
                        @{{ container.trucker}}
                    </span>
                    <span v-show="!view_mode">

                        <span hidden>@{{ container.id}}</span>
                        <select name=""
                        :data-index_container="index2"
                        class="trucker" v-model="container.trucker">
                            <option value=""></option>
                            <option v-for="trucker in truckers" v-if="trucker.trucker != null" :value="trucker.trucker">@{{trucker.trucker }}</option>
                        </select>

                    </span>
                </td>
                <td>
                    <span v-show="view_mode">
                        @{{ container.detention_validity}}
                    </span>
                    <span v-show="!view_mode">
                        <input
                        :data-index_container="index2"  placeholder="YYYY-MM-DD"  readonly='true'
                        type="text"
                        class="form-control detention_validity"
                        v-model="container.detention_validity">

                    </span>
                </td>
                <td>
                    <span v-show="view_mode">
                        @{{ container.pull_out}}
                    </span>
                    <span v-show="!view_mode">
                        <input
                        :data-index_container="index2"  placeholder="YYYY-MM-DD"  readonly='true'
                        type="text"
                        class="form-control pull_out"
                        v-model="container.pull_out">

                    </span>
                </td>
                <td>
                    <span v-show="view_mode">
                        @{{ container.pull_out_time}}
                    </span>
                    <span v-show="!view_mode">
                        <input
                        @blur="saveValidation(container.id,'pull_out_time',container.pull_out_time)"
                        type="text"
                        class="form-control"
                        v-model="container.pull_out_time"

                        @input="container.pull_out_time = $event.target.value.toUpperCase()">

                    </span>
                </td>
                <td>
                    <span style="color:#f89a13" v-if="container.actual_discharge == null">
                        -0 Days
                    </span>
                    <span v-else>
                            <span v-if="container.pull_out != null || container.pull_out == '' ">
                                @{{ CountingDays(container.actual_discharge,container.pull_out)}} Days
                            </span>
                            <span v-else style="color:#f97272">
                                    @{{ CountingDays(container.actual_discharge,dateToday)}} Days
                            </span>
                    </span>

                </td>
                <td>
                    <span v-show="view_mode">
                        @{{ container.reason_of_delay_delivery}}
                    </span>
                    <span v-show="!view_mode">
                        <textarea
                        @blur="saveValidation(container.id,'reason_of_delay_delivery',container.reason_of_delay_delivery)"
                        type="text"
                        class="form-control"
                        v-model="container.reason_of_delay_delivery"

                        @input="container.reason_of_delay_delivery = $event.target.value.toUpperCase()">
                        </textarea>
                    </span>
                </td>
                <td>
                    <span v-show="view_mode">
                        @{{ container.pull_out_remarks}}
                    </span>
                    <span v-show="!view_mode">
                        <textarea
                        @blur="saveValidation(container.id,'pull_out_remarks',container.pull_out_remarks)"
                        type="text"
                        class="form-control"
                        v-model="container.pull_out_remarks"

                        @input="container.pull_out_remarks = $event.target.value.toUpperCase()">
                        </textarea>

                    </span>
                </td>


            </tr>
        </tbody>
    </table>
</div>
