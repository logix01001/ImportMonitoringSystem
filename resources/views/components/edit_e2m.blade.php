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
            <th> BL # </th>
            <th style="color:red"> Estimated Time of Arrival </th>
            <th style="color:#9e34eb"> Latest ETA </th>
            <th> Registry # </th>
            <th> IP </th>
            <th> E2M </th>
            <th> TSAD # </th>
            <th> Remarks of Docs </th>
            <th> Actual Process </th>
            <th> Assessment Tag </th>
            <th> Date Endorse </th>
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

            <td style="color:red">
                @{{list_of_BOL[selectedIndex].estimated_time_arrival}}
            </td>
            <td style="color:#9e34eb">
                @{{list_of_BOL[selectedIndex].latest_estimated_time_arrival}}
            </td>
            <td>
                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].registry_no}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <input type="text" @blur="saveBlur(list_of_BOL[selectedIndex].id,'registry_no',list_of_BOL[selectedIndex].registry_no)"
                        v-model="list_of_BOL[selectedIndex].registry_no" class="form-control">
                </span>
            </td>
            <td>
                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].date_approve_ip}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <input type="text" placeholder="YYYY-MM-DD" readonly='true' class="form-control date_approve_ip"
                        placeholder="YYYY-MM-DD" v-model="list_of_BOL[selectedIndex].date_approve_ip">
                </span>
            </td>
            <td>
                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].e2m}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <input type="text" placeholder="YYYY-MM-DD" readonly='true' class="form-control e2m" placeholder="YYYY-MM-DD"
                        v-model="list_of_BOL[selectedIndex].e2m">
                </span>
            </td>
            <td>
                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].tsad_no}}
                </span>
                <span v-show="!view_mode">

                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <input type="text" @blur="saveBlur(list_of_BOL[selectedIndex].id,'tsad_no',list_of_BOL[selectedIndex].tsad_no)"
                        v-model="list_of_BOL[selectedIndex].tsad_no" class="form-control">
                </span>
            </td>
            <td>
                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].remarks_of_docs}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <input type="text" @blur="saveBlur(list_of_BOL[selectedIndex].id,'remarks_of_docs',list_of_BOL[selectedIndex].remarks_of_docs)"
                        v-model="list_of_BOL[selectedIndex].remarks_of_docs" class="form-control">
                </span>
            </td>
            <td>
                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].actual_process}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <input type="text" placeholder="YYYY-MM-DD" readonly='true' class="form-control actual_process"
                        placeholder="YYYY-MM-DD" v-model="list_of_BOL[selectedIndex].actual_process">
                </span>
            </td>
            <td>
                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].assessment_tag}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <select class="boc_select2" @change="assesmentTagBlur()" v-model="list_of_BOL[selectedIndex].assessment_tag"
                        id="assessment_tag">
                        <option value=""></option>
                        <option value="RED">RED</option>
                        <option value="YELLOW">YELLOW</option>
                    </select>
                </span>
            </td>
            <td>
                <span v-show="view_mode">
                    @{{list_of_BOL[selectedIndex].date_endorse}}
                </span>
                <span v-show="!view_mode">
                    <span hidden>@{{list_of_BOL[selectedIndex].id}}</span>
                    <input type="text" placeholder="YYYY-MM-DD" readonly='true' class="form-control date_endorse"
                        placeholder="YYYY-MM-DD" v-model="list_of_BOL[selectedIndex].date_endorse">
                </span>
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
