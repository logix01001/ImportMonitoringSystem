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

                <th class="tablewithrowspan column300" rowspan="2"> Trucker </th>
                <th class="tablewithrowspan column100" rowspan="2"> Delivery Date </th>
                <th class="tablewithrowspan column200" rowspan="2"> Delivery Time </th>
                <th class="tablewithrowspan column300" colspan="2"> Dismounted/Bobtail </th>
                <th class="tablewithrowspan column200" rowspan="2"> Unload </th>
                <th class="tablewithrowspan column100" rowspan="2"> Safekeep Date </th>
                <th class="tablewithrowspan column300" rowspan="2"> Reason of Delay Delivery </th>
                <th class="tablewithrowspan column300" colspan="2"> Return / Round use </th>
                <th class="tablewithrowspan column100" rowspan="2"> Box # </th>
                <th class="tablewithrowspan column100" rowspan="2"> Summary # </th>
            </tr>
            <tr>
                <th class="tablewithrowspan">Storage</th>
                <th class="tablewithrowspan">Demurrage</th>
                <th class="tablewithrowspan">Storage</th>
                <th class="tablewithrowspan">Demurrage</th>
                <th class="tablewithrowspan">CY</th>
                <th class="tablewithrowspan">Date</th>
                <th class="tablewithrowspan">CY</th>
                <th class="tablewithrowspan">Date</th>
            </tr>
        </thead>
        <tbody style="font-size:15px">
            <tr v-if="container.return_date != null" v-for="(container,index2) in list_of_BOL[selectedIndex].container_numbers">
                <td :class="container.quantity == 0 ? 'quadrat' : ''">@{{container.container_type}}</td>
                <td :class="container.quantity == 0 ? 'quadrat' : ''"><span :style="container.xray == 1? {'color':'red'}:{}">@{{container.container_number}}</span></td>
                <td class="column50" style="text-align:center">

                    <span v-if="container.xray == 1">
                        <i class="fa fa-check"></i>
                    </span>


                </td>
                <td>

                    @{{container.validity_storage}}

                </td>
                <td>


                    @{{container.validity_demurrage}}

                </td>
                <td>
                    @{{container.revalidity_storage}}

                </td>
                <td>
                    @{{container.revalidity_demurrage}}
                </td>
                <td>
                    @{{container.trucker}}
                </td>
                <td>
                
                    @{{container.pull_out}}
                   
                </td>
                <td>
                  
                    @{{container.pull_out_time}}
                   
                </td>
                <td>
                    <span v-show="view_mode">
                        @{{container.dismounted_cy}}
                    </span>
                    <span v-show="!view_mode">

                        <span hidden>@{{container.id}}</span>
                        <select name="" :data-index_container="index2" class="dismounted_cy" v-model="container.dismounted_cy">
                            <option value=""></option>
                            <option v-for="cy in dismounted_cys" v-if="cy.dismounted_cy != null" :value="cy.dismounted_cy">@{{cy.dismounted_cy}}</option>
                        </select>

                    </span>


                </td>
                <td>
                    <span v-show="view_mode">
                        @{{container.dismounted_date}}
                    </span>
                    <span v-show="!view_mode">
                        <input :data-index_container="index2" placeholder="YYYY-MM-DD" readonly='true' type="text"
                            class="form-control dismounted_date" v-model="container.dismounted_date">

                    </span>
                </td>
                <td>
                    <span v-show="view_mode">
                        @{{container.unload}}
                    </span>
                    <span v-show="!view_mode">
                        <input :data-index_container="index2" placeholder="YYYY-MM-DD" readonly='true' type="text"
                            class="form-control unload" v-model="container.unload">

                    </span>
                </td>
                <td>
                        <span v-show="view_mode">
                            @{{container.safe_keep}}
                        </span>
                        <span v-show="!view_mode">
                            <input :data-index_container="index2" placeholder="YYYY-MM-DD" readonly='true' type="text" class="form-control safe_keep"
                                v-model="container.safe_keep">
        
                        </span>
                    </td>
                <td>
                    <span v-show="view_mode">
                        @{{container.reason_of_delay_delivery}}
                    </span>
                    <span v-show="!view_mode">
                        <textarea @blur="saveValidation(container.id,'reason_of_delay_delivery',container.reason_of_delay_delivery)"
                            type="text" class="form-control" v-model="container.reason_of_delay_delivery" @input="container.reason_of_delay_delivery = $event.target.value.toUpperCase()">
                            </textarea>
                    </span>
                </td>
                <td>
                    <span v-show="view_mode">
                        @{{container.return_cy}}
                    </span>
                    <span v-show="!view_mode">
                        <span hidden>@{{container.id}}</span>
                        <select name="" :data-index_container="index2" class="return_cy" v-model="container.return_cy">
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
                        <input :data-index_container="index2" placeholder="YYYY-MM-DD" readonly='true' type="text"
                            class="form-control return_date" v-model="container.return_date">

                    </span>
                </td>
                <td>
                    <span v-show="view_mode">
                        @{{container.return_box_number}}
                    </span>
                    <span v-show="!view_mode">
                        <input @blur="saveValidation(container.id,'return_box_number',container.return_box_number)"
                            type="text" class="form-control" v-model="container.return_box_number" @keypress="isNumber"
                            @input="container.return_box_number = $event.target.value.toUpperCase()">

                    </span>
                </td>
                <td>
                    <span v-show="view_mode">
                        @{{container.return_summary_number}}
                    </span>
                    <span v-show="!view_mode">
                        <input @blur="saveValidation(container.id,'return_summary_number',container.return_summary_number)"
                            type="text" class="form-control" v-model="container.return_summary_number" @input="container.return_summary_number = $event.target.value.toUpperCase()">

                    </span>
                </td>
            </tr>
        </tbody>
    </table>


</div>