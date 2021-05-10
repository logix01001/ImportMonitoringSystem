new Vue({
    el: '.vue_app_element',
    data: {
        containers : data,
        nextweek: '',
        containers_loaded: [],
        date_filter: date_request,
        port: ['SOUTH','NORTH',''],
        selected_port: '',
        asynLoadmore: 0,
        status_filtered: null,
        factories: factories,
        selected_factory: '',
        view_mode: true,
    },
    computed: {
        cdata(){
            var self = this
            var obj = []

            if(self.status_filtered == null){
                obj  = _.orderBy(this.containers,['status','actual_discharge'],['asc','asc'])
            }else{
                obj =  _.filter(this.containers, function(o) { return o.status == self.status_filtered; });
            }

            if(self.selected_factory != ''){
                obj = _.filter(obj,function(o){
                    return o.factory == self.selected_factory
                })
            }

            if(self.selected_port != ''){
                obj = _.filter(obj,function(o){
                    return o.pod == self.selected_port
                })
            }

            return obj;
        },
        eta_count(){
            var self = this

            let obj = _.filter(self.containers, {eta: true})

            return obj.length
        }
    },
    created(){

    },
    watch:{
        date_filter(val){
            var firstDay = new Date(val);
            this.nextweek = new Date(firstDay.getTime() + 7 * 24 * 60 * 60 * 1000).toISOString().split("T")[0];

        }
    },
    methods: {

        changeMode() {
            var self = this
            if (self.view_mode) {
                self.view_mode = false
            } else {
                self.view_mode = true
            }


        },
        filterstatus(status){
            var self = this
            self.status_filtered = status
        },
        nextweek(seldate){
            var firstDay = new Date("2009/06/25");
            var nextWeek = new Date(firstDay.getTime() + 7 * 24 * 60 * 60 * 1000);
        },
        searchdata(date){
            var self = this
            $.blockUI({ message: '<h3> Processing ...</h3>' });
            axios.post(`http://${window.location.host}/${myBaseName}bill_of_lading/api_transport_schedule`,
            {

                date_filter : date,

            }).then(function(res){
                self.containers = res.data
                //self.containers_loaded = self.cdata.length == 0 ? [] : self.cdata.slice(0,100)
                self.containers_loaded = self.cdata
                $('#tablerow').scrollTop(0)
                //console.log(res)
                $.unblockUI();

            }).catch(function(err){
                    console.log(err)
            })
        },
        getdata(){
            var self = this
            var clength =  self.containers.length
            var cllength =  self.containers_loaded.length

            if(cllength < clength){
                $.blockUI({ message: '<h3> Processing ...</h3>' });


                setTimeout(() => {

                    self.containers_loaded =  self.containers_loaded.concat(self.cdata.slice((cllength),(cllength ) + 100))

                    console.log(self.containers_loaded.length,self.cdata.length)


                    self.asynLoadmore = 0
                    $.unblockUI();
                }, 1000);




                //self.containers_loaded.push(self.containers.slice((cllength - 1),100))


            }
        },
        saveValidation(id, column, value) {
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_validation_revalidation_date`,
                {
                    id: id,
                    columnName: column,
                    value: value

                }).then(function (res) {
                    console.log(res.data)
                }).catch(function (err) {
                    console.log(err)
                })
        },

    },
    mounted() {
        var self = this
        $('#date_filter').datepicker({
            format: "yyyy-mm-dd"
        }).on(
            "changeDate", (e) => {

                this.date_filter = $('#date_filter').val()
                this.searchdata(this.date_filter)
        })
        var firstDay = new Date(this.date_filter);
        this.nextweek = new Date(firstDay.getTime() + 7 * 24 * 60 * 60 * 1000).toISOString().split("T")[0];

       // self.containers_loaded = self.cdata.length == 0 ? [] : self.cdata.slice(0,100),
       self.containers_loaded = self.cdata;
        $('#tablerow').on('scroll', function() {
            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {

                if(self.asynLoadmore == 0){
                    self.asynLoadmore++
                    self.getdata()
                }



            }


        })



        $(document).on(
            "focus",
            ".target_dispatch_date:not(.hasDatepicker)",
            function() {
                $(this).addClass("hasDatepicker");
                $(this)
                .datepicker({
                    format: "yyyy-mm-dd",
                    clearBtn: true,
                })
                .on("changeDate", e => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var container_id = selected_element.data('container_id');

                    var list_index = _.findIndex(self.containers, function (o) { return o.container_id == container_id; });

                    self.containers[list_index].target_dispatch_date = select_val

                    console.log(container_id,  self.containers[list_index])

                    //console.log( self.list_of_BOL[list_index].bl_no,list_index,container_id)
                    self.saveValidation(container_id, 'target_dispatch_date', select_val);
                })
                .on("clearDate", e => {


                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var container_id = selected_element.data('container_id');

                    var list_index = _.findIndex(self.containers, function (o) { return o.container_id == container_id; });

                    self.containers[list_index].target_dispatch_date = select_val

                    //self.saveValidation(container_id, 'target_dispatch_date', select_val);

                });
        });

        $(document).on(
            "focus",
            ".dispatched_date:not(.hasDatepicker)",
            function() {
                $(this).addClass("hasDatepicker");
                $(this)
                .datepicker({
                    format: "yyyy-mm-dd",
                    clearBtn: true,
                })
                .on("changeDate", e => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var container_id = selected_element.data('container_id');

                    var list_index = _.findIndex(self.containers, function (o) { return o.container_id == container_id; });

                    self.containers[list_index].dispatched_date = select_val

                    console.log(container_id,  self.containers[list_index])

                    //console.log( self.list_of_BOL[list_index].bl_no,list_index,container_id)
                    self.saveValidation(container_id, 'dispatched_date', select_val);
                })
                .on("clearDate", e => {


                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var container_id = selected_element.data('container_id');

                    var list_index = _.findIndex(self.containers, function (o) { return o.container_id == container_id; });

                    self.containers[list_index].dispatched_date = select_val

                    //self.saveValidation(container_id, 'dispatched_date', select_val);

                });
        });




    },
})
