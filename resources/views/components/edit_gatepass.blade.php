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
<h2>Target Gatepass : <span style="color: red">@{{ list_of_BOL[selectedIndex].target_gatepass}}</span></h2>
<h2>Remarks : </h2>
<span v-show="view_mode" style="color: red">
    @{{ list_of_BOL[selectedIndex].sop_remarks}}
</span>
<span v-show="!view_mode" >
    <textarea @blur="saveBlur(list_of_BOL[selectedIndex].id,'sop_remarks',list_of_BOL[selectedIndex].sop_remarks)"
        class="form-control"
        v-model="list_of_BOL[selectedIndex].sop_remarks" placeholder="Ramarks"></textarea>
</span>
<div class="hr-line-dashed"></div>
<table class="table table-bordered table-responsive">
    <thead>
        <tr>
            <th> Type </th>
            <th> Container # </th>
            <th> Current Status </th>
            <th> Actual Gatepass </th>
            <th> Reason of Delay Gatepass </th>
            
        </tr>
    </thead>
    <tbody style="font-size:15px">
        <tr v-if="container.actual_gatepass != null" v-for="(container,index2) in list_of_BOL[selectedIndex].container_numbers">
            <td>
                @{{container.container_type}}
            </td>
            <td>
                @{{container.container_number}}
            </td>
            <td>
                <span v-show="view_mode">
                    @{{container.sop_current_status }}
                </span>
                <span v-show="!view_mode">
                    <input type="text" class="form-control" @blur="saveContainerBlur(container.id,'sop_current_status',container.sop_current_status)"
                        v-model="container.sop_current_status">
                </span>
            </td>
            <td>


                <span v-show="view_mode">
                    @{{container.actual_gatepass }}
                </span>
                <span v-show="!view_mode">
                    <input type="text" placeholder="YYYY-MM-DD" readonly='true' :data-index_container="index2" class="form-control actual_gatepass_container"
                        v-model="container.actual_gatepass">
                </span>
            </td>
            <td>

                <span v-show="view_mode">
                    @{{container.reason_of_delay_gatepass}}
                </span>
                <span v-show="!view_mode">
                    <textarea :disabled="!container.reason_of_delay_gatepass_boolean" class="form-control" v-model="container.reason_of_delay_gatepass"
                        placeholder="Reason of Delay"></textarea>
                    <span v-if="container.reason_of_delay_gatepass_boolean">
                        <button class="btn btn-default" :disabled="container.reason_of_delay_gatepass == null || container.reason_of_delay_gatepass.length == 0"
                            @click="saveDelayReasonContainer(container.id,container.actual_gatepass,container.reason_of_delay_gatepass)">Save
                            with delay reason</button>
                    </span>
                </span>
                @{{container.reason_delay_gatepass}}
            </td>
            
        </tr>
    </tbody>
</table>