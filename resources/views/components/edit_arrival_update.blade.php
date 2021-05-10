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

<table class="table  table-hover table-bordered-dark   toggle-arrow-tiny">
    <thead>
        <tr>

            <th> Consignee </th>
            <th>BL #</th>
            <th>Commodities</th>
            <th>Shipping Line</th>
            <th>Connecting Vessel</th>
            <th>POD</th>
            <th style="color:red">Estimated Time of Arrival</th>
            <th style="color:#9e34eb"> Latest ETA </th>
            <th style="color:blue">Actual Time of Arrival</th>
            <th style="color:brown">Actual Berthing Date</th>

        </tr>
    </thead>
    <tbody>

        <tr>

            <td>
                @{{list_of_BOL[selectedIndex].factory}}
            </td>
            <td>
                @{{list_of_BOL[selectedIndex].bl_no}}
            </td>
            <td>

                <span v-for="(c,index) in list_of_BOL[selectedIndex].commodities">
                    @{{c.commodity}} <span v-if="index+1 != list_of_BOL[selectedIndex].commodities.length ">,</span>
                </span>
            </td>
            <td>
                @{{list_of_BOL[selectedIndex].shipping_line}}
            </td>
            <td>
                <span v-show="view_mode" :style="list_of_BOL[selectedIndex].connecting_vessel_confirm == 1 ? {'color':'#9933ff'} : {'color' : 'red'}">
                    <b> @{{list_of_BOL[selectedIndex].connecting_vessel}}</b>
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <select name="" class="connecting_vessels" v-model="list_of_BOL[selectedIndex].connecting_vessel">
                        <option value=""></option>
                        <option v-for="cv in connecting_vessels" v-if="cv.connecting_vessel != null" :value="cv.connecting_vessel">@{{cv.connecting_vessel
                            }}</option>
                    </select>
                    <input type="checkbox" @change="connect_confirm(list_of_BOL[selectedIndex].id,list_of_BOL[selectedIndex].connecting_vessel_confirm)"
                        v-model="list_of_BOL[selectedIndex].connecting_vessel_confirm">
                </span>

            </td>
            <td>
                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].pod}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <select class="form-control pod" v-model="list_of_BOL[selectedIndex].pod">
                        <option value=""></option>
                        <option value="SOUTH">SOUTH</option>
                        <option value="NORTH">NORTH</option>
                    </select>
                </span>
            </td>

            <td style="color:red">
                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].estimated_time_arrival}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <input type="text" class="form-control estimated_time_arrival" placeholder="YYYY-MM-DD" readonly='true'
                        v-model="list_of_BOL[selectedIndex].estimated_time_arrival">
                </span>

            </td>
            <td style="color:#9e34eb">
                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].latest_estimated_time_arrival}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <input type="text" class="form-control latest_estimated_time_arrival" placeholder="YYYY-MM-DD" readonly='true'
                        v-model="list_of_BOL[selectedIndex].latest_estimated_time_arrival">
                </span>
            </td>
            <td style="color:blue">

                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].actual_time_arrival}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <input type="text" class="form-control actual_time_arrival" placeholder="YYYY-MM-DD" readonly='true'
                        v-model="list_of_BOL[selectedIndex].actual_time_arrival">
                </span>

            </td>
            <td style="color:#eaba6b">

                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].actual_berthing_date}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <input type="text" class="form-control berthing_date" placeholder="YYYY-MM-DD" readonly='true'
                        v-model="list_of_BOL[selectedIndex].actual_berthing_date">
                </span>

            </td>


        </tr>

        <tr class="bodyitem">
            <td colspan="10">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="color:#eaba6b">Qty</th>
                            <th style="color:green">Container #</th>
                            <th style="color:violet">
                                Discharge
                                <span v-show="!view_mode" class="pull-right">
                                    <input type="checkbox" v-model="list_of_BOL[selectedIndex].sameDischarge">
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="font-size:15px">
                        <tr v-if="container.actual_discharge != null" v-for="(container,index) in list_of_BOL[selectedIndex].container_numbers"
                            :style="container.quantity == 0 ? {'background-color': '#FAB341'} : {}">
                            <td>@{{container.quantity}}</td>
                            <td @dblclick="splitBL_NO(container.id,container.split_bl_no_fk,container.quantity)">@{{container.container_number}}</td>
                            <td v-show="!view_mode"><input :data-index_container="index" placeholder="YYYY-MM-DD"
                                    readonly='true' type="text" class="form-control container_discharge" v-model="container.actual_discharge"></td>
                            <td v-show="view_mode">@{{container.actual_discharge}}</td>
                        </tr>
                    </tbody>
                </table>
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
                    <td v-show="!view_mode"><input :data-index="index" :data-index_container="index2" type="text" class="form-control container_discharge"
                            v-model="container.actual_discharge"></td>
                    <td v-show="view_mode">@{{container.actual_discharge}}</td>
                </tr>
            </tbody>
        </table> --}}

    </tbody>
</table>
