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


<table class="table table-bordered table-bordered-dark">
    <thead>
        <tr>
            <th class="tablewithrowspan column100"> Type </th>
            <th class="tablewithrowspan column100"> Container # </th>
            <th class="tablewithrowspan column50"> Xray </th>
            <th class="tablewithrowspan column100"> Safekeep Date </th>

        </tr>

    </thead>
    <tbody style="font-size:15px">
        <tr v-if="container.safe_keep != null" v-for="(container,index2) in list_of_BOL[selectedIndex].container_numbers">
            <td :class="container.quantity == 0 ? 'quadrat' : ''">@{{container.container_type}}</td>
            <td :class="container.quantity == 0 ? 'quadrat' : ''"><span :style="container.xray == 1? {'color':'red'}:{}">@{{container.container_number}}</span></td>
            <td class="column50" style="text-align:center">
                <span v-if="container.xray == 1">
                    <i class="fa fa-check"></i>
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
        </tr>
    </tbody>
</table>