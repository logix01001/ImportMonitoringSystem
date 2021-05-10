
    <textarea class="form-control" v-model="list_of_BOL[selectedIndex].bl_remarks">
    </textarea>
    <div>
        <hr>
        <button class="btn btn-primary float-right"
            @click="save_bl_remarks(list_of_BOL[selectedIndex].id,'bl_remarks',list_of_BOL[selectedIndex].bl_remarks)">
            Save
        </button>
    </div>


