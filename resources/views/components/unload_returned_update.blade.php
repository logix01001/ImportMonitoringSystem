

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
                <input  :disabled="filter_search == ''" type="text" placeholder=" " v-model="search_cn" class="input-sm form-control"> <span class="input-group-btn">
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
                        <a href="./extract_UnloadReturnExport/" class="btn btn-primary btn-outline btn-dim pull-right"> Download CSV <i class="fa fa-2x fa-file-excel"></i> <i class="fa fa-2x fa-download"></i> </a>
                </div>
                <h4 style="color:red">* Please fill up both Dismounted Cy & Date. if only 1 of those column was filled up, The record will not be saved. </h4>
        </div>
    </div>
    <div v-show="!loading_data">
            <table id="detail_obj"  class="table table-hover table-bordered toggle-arrow-tiny">
                <thead>
                    <tr>
                        <th> Consignee</th>
                        <th> BL #</th>
                        <th> Container #</th>
                        <th> Commodities</th>
                        <th> Shipping Line</th>
                        <th> Container Size</th>
                        <th> Delivery Date</th>
                        <th> Dismounted CY </th>
                        <th> Dismounted Date </th>
                        <th> Unloaded Date </th>
                        <th> Safekeep Date </th>
                        <th> Return/Round used CY</th>
                        <th> Date </th>
                        <th> Box #</th>
                        <th> Summary # </th>
                    </tr>
                </thead>
                <tbody >
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
                        <td>
                            @{{ BOL.pull_out }} <span v-if="BOL.pull_out_time != null">@{{ BOL.pull_out_time }}</span>
                        </td>
                        <td :style="(BOL.dismounted_date != null  && BOL.dismounted_date != '')  && (BOL.dismounted_cy == null || BOL.dismounted_cy === '') ? 'background:red;color:white' : ''">
                            <span v-show="view_mode">
                                @{{ BOL.dismounted_cy}}

                            </span>
                            <span v-show="!view_mode">
                                <span hidden>@{{ BOL.container_id}}</span>
                                <select name="" :data-index="index"
                                    class="dismounted_cy" v-model="BOL.dismounted_cy">
                                        <option value=""></option>
                                        <option value="IRS BACAO" v-if="_.findIndex(dismounted_cys, {'dismounted_cy': 'IRS BACAO'}) == -1">IRS BACAO</option>
                                        <option value="CEZ1" v-if="_.findIndex(dismounted_cys, {'dismounted_cy': 'CEZ1'}) == -1">CEZ 1 PUTOL</option>
                                        <option value="CEZ2" v-if="_.findIndex(dismounted_cys, {'dismounted_cy': 'CEZ2'}) == -1">CEZ 2 PUTOL</option>
                                        {{-- <option value="WITH CHASSI" v-if="_.findIndex(dismounted_cys, {'dismounted_cy': 'WITH CHASSI'}) == -1">WITH CHASSI</option> --}}
                                        <option v-for="cy in dismounted_cys" v-if="cy.dismounted_cy != null && cy.dismounted_cy != ''" :value="cy.dismounted_cy">@{{cy.dismounted_cy}}</option>
                                </select>
                            </span>
                        </td>
                        <td  :style="(BOL.dismounted_cy != null && BOL.dismounted_cy != '')  && (BOL.dismounted_date == null || BOL.dismounted_date === '' ) ? 'background:red;color:white' : ''">
                            <template v-if="view_mode">
                                @{{ BOL.dismounted_date}}

                            </template>
                            <template v-if="!view_mode">
                                <input :data-index="index" placeholder="YYYY-MM-DD" readonly='true'
                                    type="text" class="form-control dismounted_date"
                                    v-model="BOL.dismounted_date">
                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{ BOL.unload}}
                            </template>
                            <template v-if="!view_mode">
                                <input :data-index="index" placeholder="YYYY-MM-DD" readonly='true'
                                    type="text" class="form-control unload"
                                    v-model="BOL.unload">

                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{ BOL.safe_keep}}
                            </template>
                            <template v-if="!view_mode">
                                <input :data-index="index" placeholder="YYYY-MM-DD" readonly='true'
                                     type="text" class="form-control safe_keep"
                                    v-model="BOL.safe_keep">
                            </template>
                        </td>
                        <td>

                            <span v-show="view_mode">
                                @{{ BOL.return_cy}}
                            </span>
                            <span v-show="!view_mode">
                                <span hidden>@{{ BOL.container_id}}</span>
                                <select :disabled="BOL.unload == null" name="" :data-index="index"
                                    class="return_cy" v-model="BOL.return_cy">
                                    <option value=""></option>
                                    <option v-for="cy in return_cys" v-if="cy.return_cy != null" :value="cy.return_cy">@{{cy.return_cy}}</option>
                                </select>

                            </span>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                @{{ BOL.return_date}}
                            </template>
                            <template v-if="!view_mode">
                                <input :disabled="BOL.unload == null" :data-index="index" placeholder="YYYY-MM-DD" readonly='true'
                                     type="text" class="form-control return_date"
                                    v-model="BOL.return_date">

                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                               <template v-if="BOL.return_box_number > 0">@{{ BOL.return_box_number}}</template>
                            </template>
                            <template v-if="!view_mode">
                                <input @blur="saveValidation(BOL.container_id,'return_box_number',BOL.return_box_number)"
                                    type="text" class="form-control" v-model="BOL.return_box_number"
                                    @keypress="isNumber" @input="BOL.return_box_number = $event.target.value.toUpperCase()">

                            </template>
                        </td>
                        <td>
                            <template v-if="view_mode">
                                <template v-if="BOL.return_summary_number > 0">@{{ BOL.return_summary_number}}</template>
                            </template>
                            <template v-if="!view_mode">
                                <input @blur="saveValidation(BOL.container_id,'return_summary_number',BOL.return_summary_number)"
                                    type="text" class="form-control" v-model="BOL.return_summary_number"
                                    @input="BOL.return_summary_number = $event.target.value.toUpperCase()">

                            </template>
                        </td>

                    </tr>
                </template>
            </tbody>
        </table>
    </div>
        <center v-if="loading_data"><img src="{{ asset('img/loading.gif') }}"></center>

