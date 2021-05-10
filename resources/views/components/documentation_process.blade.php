

<div class="row">
        <div class="col-lg-3">
            <div class="row">
                {{-- <div class="col-lg-6">
                    <label>Status</label>
                    <br>
                    <select @change="changeStatus"  v-model="statusFilter" class="form-control" name="" id="">
                        <option value="ALL"> --- All --- </option>
                        <option v-for="stat in status"> @{{ stat }}</option>
                    </select>
                </div> --}}
                <div class="col-lg-12">
                    <br>
                    <button type="button" :disabled="loading_data" id="loading-example-btn" class="btn btn-block btn-white btn-sm" @click="refresh_e2m"><i class="fa fa-sync-alt "></i> Refresh</button>
                    <button @click="changeMode"  class="btn btn-block btn-white btn-sm">
                        <span v-if="view_mode">
                            <i class="fa fa-edit"></i>
                            Edit
                        </span>
                        <span  v-if="!view_mode">
                            <i class="fa fa-eye"></i>
                            View
                        </span>
                    </button>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                        <a href="/ims/extract_DocumentationProcessExport/" class="btn btn-block btn-primary btn-outline btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>
                </div>
            </div>

        </div>

        <div class="col-lg-9">
               <table class="table">
                   <thead>
                       <tr>
                            <th colspan="4">Lodgement and Endorsement ( <i>First 3 ETA</i> )</th>
                       </tr>
                       <tr>
                            <th>ETA</th>
                            <th>No. Docs</th>
                            <th>Lodged Docs</th>
                            <th>W/ Endorsement</th>
                       </tr>
                   </thead>
                   <tbody>
                       <tr v-for="date in first_three_dates">
                           <th class="cursor" @click="setDataTableETA(date)">
                               @{{ date }}
                           </th>
                           <td>
                                @{{ getDocsCount(date) }}
                            </td>
                            <td>
                                @{{ getDocsCount(date,'registry_no') }}
                            </td>
                            <td>
                                @{{ getDocsCount(date,'date_endorse') }}
                            </td>
                       </tr>
                   </tbody>
               </table>
        </div>
</div>

    <div class="row">
        <div class="col-lg-3">
            <span class="h4 font-bold m-t block">@{{list_of_BOL.length}} / @{{  list_of_BOL_Total }}</span>
            <h3 >Showing Records</h3>
        </div>
        <div class="col-lg-3  pull-right">
                <div class="col-lg-6 col-sm-6 col-md-offset-6 col-lg-offset-6">
                        {{-- <a href="/extract_UnloadReturnExport/" class="btn btn-primary btn-outline btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a> --}}
                </div>
        </div>
    </div>

    <div v-show="!loading_data">
            <table id="detail_obj"  class="table table-hover  toggle-arrow-tiny">
                <thead>
                    <tr>
                        <th> BL </th>
                        <th> Connecting Vessel</th>
                        <th> ETA </th>
                        <th> Latest ETA </th>
                        <th> Registry No.</th>
                        <th> POD </th>
                        <th> Shipping Line </th>
                        <th> Factory </th>
                        <th> Status </th>
                        <th> Approved IP </th>
                        <th> Approved E2M </th>
                        <th> TSAD # </th>
                        <th> Remarks </th>
                        <th> Port Processing Date </th>
                        <th> Tag </th>
                        <th> Endorsement Date</th>
                    </tr>
                </thead>
                <tbody >

                <template v-if="statusFilter == 'ALL'" v-for="(BOL,index) in list_of_BOL">
                    <tr>
                        <td class="stickycolumn">
                                <span :style="BOL.registry_no_confirm == 1 ? {'color':'red'} : {}">
                                    <b>@{{BOL.bl_no}}</b>
                                </span>
                            <br>
                            <span v-if="!view_mode">
                                <input type="checkbox" @change="registry_confirm(BOL.id,BOL.registry_no_confirm)"
                                    v-model="BOL.registry_no_confirm">
                            </span>
                        </td>
                        <td>
                            <span :style="BOL.connecting_vessel_confirm == 1 ? {'color':'#9933ff'} : {'color' : 'red'}">
                                <b> @{{BOL.connecting_vessel}}</b>
                            </span>
                        </td>

                        <td>
                            @{{BOL.estimated_time_arrival}}
                        </td>
                        <td>
                            @{{ BOL.latest_estimated_time_arrival }}
                        </td>
                        <td >
                            @{{ BOL.registry_no}}
                        </td>
                        <td>
                            @{{BOL.pod}}
                        </td>
                        <td>
                            @{{ BOL.shipping_line}}
                        </td>
                        <td>
                            @{{ BOL.factory }}
                        </td>
                        <td>

                                <template v-if="BOL.actual_process != null">
                                        @{{ list_of_BOL[index].docs_status =  'For Port Processing' }}
                                </template>
                                <template v-else-if="BOL.e2m != null">
                                        @{{ list_of_BOL[index].docs_status = 'Approved E2M' }}
                                </template>
                                <template v-else-if="BOL.date_approve_ip != null">
                                        @{{ list_of_BOL[index].docs_status = 'Approved IP' }}
                                </template>
                                <template v-else-if="BOL.registry_no_confirm == 1">
                                        @{{ list_of_BOL[index].docs_status = 'Check/Send' }}
                                </template>
                                <template v-else-if="BOL.registry_no != null">
                                        @{{ list_of_BOL[index].docs_status = 'Lodgement' }}
                                </template>
                                <template v-else-if="BOL.registry_no == null || BOL.connecting_vessel == null">
                                        @{{ list_of_BOL[index].docs_status = 'No Registry yet' }}
                                </template>

                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.date_approve_ip}}
                            </template>
                            <template  v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>

                                <input :disabled="BOL.registry_no == null" type="text" readonly='true' class="form-control date_approve_ip"
                                    placeholder="YYYY-MM-DD" v-model="BOL.date_approve_ip">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.e2m}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>


                                <input :disabled="BOL.date_approve_ip == null" type="text" readonly='true' class="form-control e2m" placeholder="YYYY-MM-DD"
                                    v-model="BOL.e2m">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.tsad_no}}
                            </template>
                            <template v-if="!view_mode">

                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" @blur="saveBlur(BOL.id,'tsad_no',BOL.tsad_no)" v-model="BOL.tsad_no"
                                    class="form-control">

                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.remarks_of_docs}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" @blur="saveBlur(BOL.id,'remarks_of_docs',BOL.remarks_of_docs)"
                                    v-model="BOL.remarks_of_docs" class="form-control">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.actual_process}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input  :disabled="BOL.date_approve_ip == null || BOL.date_endorse == null "  type="text" readonly='true' class="form-control actual_process"
                                    placeholder="YYYY-MM-DD" v-model="BOL.actual_process">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.assessment_tag}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <select class="form-control boc_select2" @change="assesmentTagBlur(BOL.id)" v-model="BOL.assessment_tag"
                                    id="assessment_tag">
                                    <option value="RED">RED</option>
                                    <option value="YELLOW">YELLOW</option>
                                </select>
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{ BOL.date_endorse}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" readonly='true' class="form-control date_endorse"
                                    placeholder="YYYY-MM-DD" v-model="BOL.date_endorse">
                            </template>

                        </td>






                    </tr>
                </template>
                <template v-if="statusFilter == 'No Registry yet'" v-for="(BOL,index) in listForNoRegistry">
                        <tr>
                            <td class="stickycolumn">
                                    <span :style="BOL.registry_no_confirm == 1 ? {'color':'red'} : {}">
                                        <b>@{{BOL.bl_no}}</b>
                                    </span>
                                <br>
                                <span v-if="!view_mode">
                                    <input type="checkbox" @change="registry_confirm(BOL.id,BOL.registry_no_confirm)"
                                        v-model="BOL.registry_no_confirm">
                                </span>
                            </td>
                            <td>
                                @{{BOL.connecting_vessel}}
                            </td>

                            <td>
                                @{{BOL.estimated_time_arrival}}
                            </td>
                            <td >
                                    <template v-if="view_mode">
                                        @{{  BOL.registry_no}}
                                    </template>
                                    <template  v-if="!view_mode">
                                        <span hidden>@{{BOL.id}}</span>

                                        <input type="text" @blur="saveBlur(BOL.id,'registry_no',BOL.registry_no)" v-model="BOL.registry_no"
                                        class="form-control">
                                    </template>
                            </td>
                            <td>
                                @{{BOL.pod}}
                            </td>
                            <td>
                                @{{ BOL.shipping_line}}
                            </td>
                            <td>
                                @{{ BOL.factory }}
                            </td>
                            <td>

                                    <template v-if="BOL.actual_process != null">
                                            @{{ BOL.docs_status =  'For Port Processing' }}
                                    </template>
                                    <template v-else-if="BOL.e2m != null">
                                            @{{ BOL.docs_status = 'Approved E2M' }}
                                    </template>
                                    <template v-else-if="BOL.date_approve_ip != null">
                                            @{{ BOL.docs_status = 'Approved IP' }}
                                    </template>
                                    <template v-else-if="BOL.registry_no_confirm == 1">
                                            @{{ BOL.docs_status = 'Check/Send' }}
                                    </template>
                                    <template v-else-if="BOL.registry_no != null">
                                            @{{ BOL.docs_status = 'Lodgement' }}
                                    </template>
                                    <template v-else-if="BOL.registry_no == null || BOL.connecting_vessel == null">
                                            @{{ BOL.docs_status = 'No Registry yet' }}
                                    </template>

                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.date_approve_ip}}
                                </template>
                                <template  v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>

                                    <input :disabled="BOL.registry_no == null" type="text" readonly='true' class="form-control date_approve_ip"
                                        placeholder="YYYY-MM-DD" v-model="BOL.date_approve_ip">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.e2m}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>


                                    <input :disabled="BOL.date_approve_ip == null" type="text" readonly='true' class="form-control e2m" placeholder="YYYY-MM-DD"
                                        v-model="BOL.e2m">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.tsad_no}}
                                </template>
                                <template v-if="!view_mode">

                                    <span hidden>@{{BOL.id}}</span>
                                    <input type="text" @blur="saveBlur(BOL.id,'tsad_no',BOL.tsad_no)" v-model="BOL.tsad_no"
                                        class="form-control">

                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.remarks_of_docs}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <input type="text" @blur="saveBlur(BOL.id,'remarks_of_docs',BOL.remarks_of_docs)"
                                        v-model="BOL.remarks_of_docs" class="form-control">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.actual_process}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <input  :disabled="BOL.date_approve_ip == null || BOL.date_endorse == null "  type="text" readonly='true' class="form-control actual_process"
                                        placeholder="YYYY-MM-DD" v-model="BOL.actual_process">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.assessment_tag}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <select class="form-control boc_select2" @change="assesmentTagBlur(BOL.id)" v-model="BOL.assessment_tag"
                                        id="assessment_tag">
                                        <option value="RED">RED</option>
                                        <option value="YELLOW">YELLOW</option>
                                    </select>
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{ BOL.date_endorse}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <input type="text" readonly='true' class="form-control date_endorse"
                                        placeholder="YYYY-MM-DD" v-model="BOL.date_endorse">
                                </template>

                            </td>






                        </tr>
                </template>
                <template v-if="statusFilter == 'Lodgement'" v-for="(BOL,index) in listForLodgement">
                        <tr>
                            <td class="stickycolumn">
                                    <span :style="BOL.registry_no_confirm == 1 ? {'color':'red'} : {}">
                                        <b>@{{BOL.bl_no}}</b>
                                    </span>
                                <br>
                                <span v-if="!view_mode">
                                    <input type="checkbox" @change="registry_confirm(BOL.id,BOL.registry_no_confirm)"
                                        v-model="BOL.registry_no_confirm">
                                </span>
                            </td>
                            <td>
                                @{{BOL.connecting_vessel}}
                            </td>

                            <td>
                                @{{BOL.estimated_time_arrival}}
                            </td>
                            <td >
                                    <template v-if="view_mode">
                                        @{{  BOL.registry_no}}
                                    </template>
                                    <template  v-if="!view_mode">
                                        <span hidden>@{{BOL.id}}</span>

                                        <input type="text" @blur="saveBlur(BOL.id,'registry_no',BOL.registry_no)" v-model="BOL.registry_no"
                                        class="form-control">
                                    </template>
                            </td>
                            <td>
                                @{{BOL.pod}}
                            </td>
                            <td>
                                @{{ BOL.shipping_line}}
                            </td>
                            <td>
                                @{{ BOL.factory }}
                            </td>
                            <td>

                                    <template v-if="BOL.actual_process != null">
                                            @{{ BOL.docs_status =  'For Port Processing' }}
                                    </template>
                                    <template v-else-if="BOL.e2m != null">
                                            @{{ BOL.docs_status = 'Approved E2M' }}
                                    </template>
                                    <template v-else-if="BOL.date_approve_ip != null">
                                            @{{ BOL.docs_status = 'Approved IP' }}
                                    </template>
                                    <template v-else-if="BOL.registry_no_confirm == 1">
                                            @{{ BOL.docs_status = 'Check/Send' }}
                                    </template>
                                    <template v-else-if="BOL.registry_no != null">
                                            @{{ BOL.docs_status = 'Lodgement' }}
                                    </template>
                                    <template v-else-if="BOL.registry_no == null || BOL.connecting_vessel == null">
                                            @{{ BOL.docs_status = 'No Registry yet' }}
                                    </template>

                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.date_approve_ip}}
                                </template>
                                <template  v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>

                                    <input :disabled="BOL.registry_no == null" type="text" readonly='true' class="form-control date_approve_ip"
                                        placeholder="YYYY-MM-DD" v-model="BOL.date_approve_ip">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.e2m}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>


                                    <input :disabled="BOL.date_approve_ip == null" type="text" readonly='true' class="form-control e2m" placeholder="YYYY-MM-DD"
                                        v-model="BOL.e2m">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.tsad_no}}
                                </template>
                                <template v-if="!view_mode">

                                    <span hidden>@{{BOL.id}}</span>
                                    <input type="text" @blur="saveBlur(BOL.id,'tsad_no',BOL.tsad_no)" v-model="BOL.tsad_no"
                                        class="form-control">

                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.remarks_of_docs}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <input type="text" @blur="saveBlur(BOL.id,'remarks_of_docs',BOL.remarks_of_docs)"
                                        v-model="BOL.remarks_of_docs" class="form-control">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.actual_process}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <input  :disabled="BOL.date_approve_ip == null || BOL.date_endorse == null "  type="text" readonly='true' class="form-control actual_process"
                                        placeholder="YYYY-MM-DD" v-model="BOL.actual_process">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.assessment_tag}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <select class="form-control boc_select2" @change="assesmentTagBlur(BOL.id)" v-model="BOL.assessment_tag"
                                        id="assessment_tag">
                                        <option value="RED">RED</option>
                                        <option value="YELLOW">YELLOW</option>
                                    </select>
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{ BOL.date_endorse}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <input type="text" readonly='true' class="form-control date_endorse"
                                        placeholder="YYYY-MM-DD" v-model="BOL.date_endorse">
                                </template>

                            </td>






                        </tr>
                </template>
                <template v-if="statusFilter == 'Check/Send'" v-for="(BOL,index) in listForCheckSend">
                        <tr>
                            <td class="stickycolumn">
                                    <span :style="BOL.registry_no_confirm == 1 ? {'color':'red'} : {}">
                                        <b>@{{BOL.bl_no}}</b>
                                    </span>
                                <br>
                                <span v-if="!view_mode">
                                    <input type="checkbox" @change="registry_confirm(BOL.id,BOL.registry_no_confirm)"
                                        v-model="BOL.registry_no_confirm">
                                </span>
                            </td>
                            <td>
                                @{{BOL.connecting_vessel}}
                            </td>

                            <td>
                                @{{BOL.estimated_time_arrival}}
                            </td>
                            <td >
                                    <template v-if="view_mode">
                                        @{{  BOL.registry_no}}
                                    </template>
                                    <template  v-if="!view_mode">
                                        <span hidden>@{{BOL.id}}</span>

                                        <input type="text" @blur="saveBlur(BOL.id,'registry_no',BOL.registry_no)" v-model="BOL.registry_no"
                                        class="form-control">
                                    </template>
                            </td>
                            <td>
                                @{{BOL.pod}}
                            </td>
                            <td>
                                @{{ BOL.shipping_line}}
                            </td>
                            <td>
                                @{{ BOL.factory }}
                            </td>
                            <td>

                                    <template v-if="BOL.actual_process != null">
                                            @{{ BOL.docs_status =  'For Port Processing' }}
                                    </template>
                                    <template v-else-if="BOL.e2m != null">
                                            @{{ BOL.docs_status = 'Approved E2M' }}
                                    </template>
                                    <template v-else-if="BOL.date_approve_ip != null">
                                            @{{ BOL.docs_status = 'Approved IP' }}
                                    </template>
                                    <template v-else-if="BOL.registry_no_confirm == 1">
                                            @{{ BOL.docs_status = 'Check/Send' }}
                                    </template>
                                    <template v-else-if="BOL.registry_no != null">
                                            @{{ BOL.docs_status = 'Lodgement' }}
                                    </template>
                                    <template v-else-if="BOL.registry_no == null || BOL.connecting_vessel == null">
                                            @{{ BOL.docs_status = 'No Registry yet' }}
                                    </template>

                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.date_approve_ip}}
                                </template>
                                <template  v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>

                                    <input :disabled="BOL.registry_no == null" type="text" readonly='true' class="form-control date_approve_ip"
                                        placeholder="YYYY-MM-DD" v-model="BOL.date_approve_ip">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.e2m}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>


                                    <input :disabled="BOL.date_approve_ip == null" type="text" readonly='true' class="form-control e2m" placeholder="YYYY-MM-DD"
                                        v-model="BOL.e2m">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.tsad_no}}
                                </template>
                                <template v-if="!view_mode">

                                    <span hidden>@{{BOL.id}}</span>
                                    <input type="text" @blur="saveBlur(BOL.id,'tsad_no',BOL.tsad_no)" v-model="BOL.tsad_no"
                                        class="form-control">

                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.remarks_of_docs}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <input type="text" @blur="saveBlur(BOL.id,'remarks_of_docs',BOL.remarks_of_docs)"
                                        v-model="BOL.remarks_of_docs" class="form-control">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.actual_process}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <input  :disabled="BOL.date_approve_ip == null || BOL.date_endorse == null "  type="text" readonly='true' class="form-control actual_process"
                                        placeholder="YYYY-MM-DD" v-model="BOL.actual_process">
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{BOL.assessment_tag}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <select class="form-control boc_select2" @change="assesmentTagBlur(BOL.id)" v-model="BOL.assessment_tag"
                                        id="assessment_tag">
                                        <option value="RED">RED</option>
                                        <option value="YELLOW">YELLOW</option>
                                    </select>
                                </template>
                            </td>
                            <td>
                                <template v-if="view_mode">
                                    @{{ BOL.date_endorse}}
                                </template>
                                <template v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>
                                    <input type="text" readonly='true' class="form-control date_endorse"
                                        placeholder="YYYY-MM-DD" v-model="BOL.date_endorse">
                                </template>

                            </td>






                        </tr>
                </template>
                <template v-if="statusFilter == 'Approved IP'" v-for="(BOL,index) in listForApprovedIP">
                    <tr>
                        <td class="stickycolumn">
                                <span :style="BOL.registry_no_confirm == 1 ? {'color':'red'} : {}">
                                    <b>@{{BOL.bl_no}}</b>
                                </span>
                            <br>
                            <span v-if="!view_mode">
                                <input type="checkbox" @change="registry_confirm(BOL.id,BOL.registry_no_confirm)"
                                    v-model="BOL.registry_no_confirm">
                            </span>
                        </td>
                        <td>
                            @{{BOL.connecting_vessel}}
                        </td>

                        <td>
                            @{{BOL.estimated_time_arrival}}
                        </td>
                        <td >
                                <template v-if="view_mode">
                                    @{{  BOL.registry_no}}
                                </template>
                                <template  v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>

                                    <input type="text" @blur="saveBlur(BOL.id,'registry_no',BOL.registry_no)" v-model="BOL.registry_no"
                                    class="form-control">
                                </template>
                        </td>
                        <td>
                            @{{BOL.pod}}
                        </td>
                        <td>
                            @{{ BOL.shipping_line}}
                        </td>
                        <td>
                            @{{ BOL.factory }}
                        </td>
                        <td>

                                <template v-if="BOL.actual_process != null">
                                        @{{ BOL.docs_status =  'For Port Processing' }}
                                </template>
                                <template v-else-if="BOL.e2m != null">
                                        @{{ BOL.docs_status = 'Approved E2M' }}
                                </template>
                                <template v-else-if="BOL.date_approve_ip != null">
                                        @{{ BOL.docs_status = 'Approved IP' }}
                                </template>
                                <template v-else-if="BOL.registry_no_confirm == 1">
                                        @{{ BOL.docs_status = 'Check/Send' }}
                                </template>
                                <template v-else-if="BOL.registry_no != null">
                                        @{{ BOL.docs_status = 'Lodgement' }}
                                </template>
                                <template v-else-if="BOL.registry_no == null || BOL.connecting_vessel == null">
                                        @{{ BOL.docs_status = 'No Registry yet' }}
                                </template>

                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.date_approve_ip}}
                            </template>
                            <template  v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>

                                <input :disabled="BOL.registry_no == null" type="text" readonly='true' class="form-control date_approve_ip"
                                    placeholder="YYYY-MM-DD" v-model="BOL.date_approve_ip">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.e2m}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>


                                <input :disabled="BOL.date_approve_ip == null" type="text" readonly='true' class="form-control e2m" placeholder="YYYY-MM-DD"
                                    v-model="BOL.e2m">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.tsad_no}}
                            </template>
                            <template v-if="!view_mode">

                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" @blur="saveBlur(BOL.id,'tsad_no',BOL.tsad_no)" v-model="BOL.tsad_no"
                                    class="form-control">

                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.remarks_of_docs}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" @blur="saveBlur(BOL.id,'remarks_of_docs',BOL.remarks_of_docs)"
                                    v-model="BOL.remarks_of_docs" class="form-control">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.actual_process}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input  :disabled="BOL.date_approve_ip == null || BOL.date_endorse == null "  type="text" readonly='true' class="form-control actual_process"
                                    placeholder="YYYY-MM-DD" v-model="BOL.actual_process">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.assessment_tag}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <select class="form-control boc_select2" @change="assesmentTagBlur(BOL.id)" v-model="BOL.assessment_tag"
                                    id="assessment_tag">
                                    <option value="RED">RED</option>
                                    <option value="YELLOW">YELLOW</option>
                                </select>
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{ BOL.date_endorse}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" readonly='true' class="form-control date_endorse"
                                    placeholder="YYYY-MM-DD" v-model="BOL.date_endorse">
                            </template>

                        </td>






                    </tr>
                </template>
                <template v-if="statusFilter == 'Approved E2M'" v-for="(BOL,index) in listForApprovedE2M">
                    <tr>
                        <td class="stickycolumn">
                                <span :style="BOL.registry_no_confirm == 1 ? {'color':'red'} : {}">
                                    <b>@{{BOL.bl_no}}</b>
                                </span>
                            <br>
                            <span v-if="!view_mode">
                                <input type="checkbox" @change="registry_confirm(BOL.id,BOL.registry_no_confirm)"
                                    v-model="BOL.registry_no_confirm">
                            </span>
                        </td>
                        <td>
                            @{{BOL.connecting_vessel}}
                        </td>

                        <td>
                            @{{BOL.estimated_time_arrival}}
                        </td>
                        <td >
                                <template v-if="view_mode">
                                    @{{  BOL.registry_no}}
                                </template>
                                <template  v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>

                                    <input type="text" @blur="saveBlur(BOL.id,'registry_no',BOL.registry_no)" v-model="BOL.registry_no"
                                    class="form-control">
                                </template>
                        </td>
                        <td>
                            @{{BOL.pod}}
                        </td>
                        <td>
                            @{{ BOL.shipping_line}}
                        </td>
                        <td>
                            @{{ BOL.factory }}
                        </td>
                        <td>

                                <template v-if="BOL.actual_process != null">
                                        @{{ BOL.docs_status =  'For Port Processing' }}
                                </template>
                                <template v-else-if="BOL.e2m != null">
                                        @{{ BOL.docs_status = 'Approved E2M' }}
                                </template>
                                <template v-else-if="BOL.date_approve_ip != null">
                                        @{{ BOL.docs_status = 'Approved IP' }}
                                </template>
                                <template v-else-if="BOL.registry_no_confirm == 1">
                                        @{{ BOL.docs_status = 'Check/Send' }}
                                </template>
                                <template v-else-if="BOL.registry_no != null">
                                        @{{ BOL.docs_status = 'Lodgement' }}
                                </template>
                                <template v-else-if="BOL.registry_no == null || BOL.connecting_vessel == null">
                                        @{{ BOL.docs_status = 'No Registry yet' }}
                                </template>

                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.date_approve_ip}}
                            </template>
                            <template  v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>

                                <input :disabled="BOL.registry_no == null" type="text" readonly='true' class="form-control date_approve_ip"
                                    placeholder="YYYY-MM-DD" v-model="BOL.date_approve_ip">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.e2m}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>


                                <input :disabled="BOL.date_approve_ip == null" type="text" readonly='true' class="form-control e2m" placeholder="YYYY-MM-DD"
                                    v-model="BOL.e2m">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.tsad_no}}
                            </template>
                            <template v-if="!view_mode">

                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" @blur="saveBlur(BOL.id,'tsad_no',BOL.tsad_no)" v-model="BOL.tsad_no"
                                    class="form-control">

                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.remarks_of_docs}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" @blur="saveBlur(BOL.id,'remarks_of_docs',BOL.remarks_of_docs)"
                                    v-model="BOL.remarks_of_docs" class="form-control">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.actual_process}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input  :disabled="BOL.date_approve_ip == null || BOL.date_endorse == null "  type="text" readonly='true' class="form-control actual_process"
                                    placeholder="YYYY-MM-DD" v-model="BOL.actual_process">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.assessment_tag}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <select class="form-control boc_select2" @change="assesmentTagBlur(BOL.id)" v-model="BOL.assessment_tag"
                                    id="assessment_tag">
                                    <option value="RED">RED</option>
                                    <option value="YELLOW">YELLOW</option>
                                </select>
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{ BOL.date_endorse}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" readonly='true' class="form-control date_endorse"
                                    placeholder="YYYY-MM-DD" v-model="BOL.date_endorse">
                            </template>

                        </td>






                    </tr>
                </template>
                <template v-if="statusFilter == 'For Port Processing'" v-for="(BOL,index) in listForPort">
                    <tr>
                        <td class="stickycolumn">
                                <span :style="BOL.registry_no_confirm == 1 ? {'color':'red'} : {}">
                                    <b>@{{BOL.bl_no}}</b>
                                </span>
                            <br>
                            <span v-if="!view_mode">
                                <input type="checkbox" @change="registry_confirm(BOL.id,BOL.registry_no_confirm)"
                                    v-model="BOL.registry_no_confirm">
                            </span>
                        </td>
                        <td>
                            @{{BOL.connecting_vessel}}
                        </td>

                        <td>
                            @{{BOL.estimated_time_arrival}}
                        </td>
                        <td >
                                <template v-if="view_mode">
                                    @{{  BOL.registry_no}}
                                </template>
                                <template  v-if="!view_mode">
                                    <span hidden>@{{BOL.id}}</span>

                                    <input type="text" @blur="saveBlur(BOL.id,'registry_no',BOL.registry_no)" v-model="BOL.registry_no"
                                    class="form-control">
                                </template>
                        </td>
                        <td>
                            @{{BOL.pod}}
                        </td>
                        <td>
                            @{{ BOL.shipping_line}}
                        </td>
                        <td>
                            @{{ BOL.factory }}
                        </td>
                        <td>

                                <template v-if="BOL.actual_process != null">
                                        @{{ BOL.docs_status =  'For Port Processing' }}
                                </template>
                                <template v-else-if="BOL.e2m != null">
                                        @{{ BOL.docs_status = 'Approved E2M' }}
                                </template>
                                <template v-else-if="BOL.date_approve_ip != null">
                                        @{{ BOL.docs_status = 'Approved IP' }}
                                </template>
                                <template v-else-if="BOL.registry_no_confirm == 1">
                                        @{{ BOL.docs_status = 'Check/Send' }}
                                </template>
                                <template v-else-if="BOL.registry_no != null">
                                        @{{ BOL.docs_status = 'Lodgement' }}
                                </template>
                                <template v-else-if="BOL.registry_no == null || BOL.connecting_vessel == null">
                                        @{{ BOL.docs_status = 'No Registry yet' }}
                                </template>

                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.date_approve_ip}}
                            </template>
                            <template  v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>

                                <input :disabled="BOL.registry_no == null" type="text" readonly='true' class="form-control date_approve_ip"
                                    placeholder="YYYY-MM-DD" v-model="BOL.date_approve_ip">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.e2m}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>


                                <input :disabled="BOL.date_approve_ip == null" type="text" readonly='true' class="form-control e2m" placeholder="YYYY-MM-DD"
                                    v-model="BOL.e2m">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.tsad_no}}
                            </template>
                            <template v-if="!view_mode">

                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" @blur="saveBlur(BOL.id,'tsad_no',BOL.tsad_no)" v-model="BOL.tsad_no"
                                    class="form-control">

                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.remarks_of_docs}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" @blur="saveBlur(BOL.id,'remarks_of_docs',BOL.remarks_of_docs)"
                                    v-model="BOL.remarks_of_docs" class="form-control">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.actual_process}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input  :disabled="BOL.date_approve_ip == null || BOL.date_endorse == null "  type="text" readonly='true' class="form-control actual_process"
                                    placeholder="YYYY-MM-DD" v-model="BOL.actual_process">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{BOL.assessment_tag}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <select class="form-control boc_select2" @change="assesmentTagBlur(BOL.id)" v-model="BOL.assessment_tag"
                                    id="assessment_tag">
                                    <option value="RED">RED</option>
                                    <option value="YELLOW">YELLOW</option>
                                </select>
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{ BOL.date_endorse}}
                            </template>
                            <template v-if="!view_mode">
                                <span hidden>@{{BOL.id}}</span>
                                <input type="text" readonly='true' class="form-control date_endorse"
                                    placeholder="YYYY-MM-DD" v-model="BOL.date_endorse">
                            </template>

                        </td>






                    </tr>
                </template>
            </tbody>
        </table>
    </div>
        <center v-if="loading_data"><img src="{{ asset('img/loading.gif') }}"></center>

