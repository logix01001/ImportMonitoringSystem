

new Vue({
    el: ".vue_app_element",
    data: {
        factories: factories,
        shipping_line: shipping_line,
        list_of_BOL: list_of_BOL,
        search_factory: "",
        search_bl_no: "",
        search_shipping_line: "",
        search_current_status: "",
        search_target_gatepass: "",
        search_commodity: "",
        view_mode: true,
        first_load: true,
        opened: [],
        loading_data: false,
        list_of_BOL_Total: list_of_BOL_Total,
        numberofTake: 20,
        showprogress: false,
        searchTrue : false,
    },
    methods: {

        getRecord_SOP(take,role = null) {
            var self = this;
            let skip = this.list_of_BOL.length;
            //let role = role

            self.showprogress = true;

            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
            $.blockUI({ message: '<h3> Processing ...</h3>' });
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/get_shipment_on_process`,
                    {
                        skip: skip,
                        take: take,

                    }
                )
                .then(response => {
                    response.data.forEach(value => {
                        this.list_of_BOL.push(value);
                    });
                    self.showprogress = false;

                    setTimeout(function() {
                        self.refreshPlugins();
                    }, 500);
                     //END BLOCK
                    $.unblockUI();
                })
                .catch(err => {
                    self.showprogress = false;
                     //END BLOCK
                     $.unblockUI();
                });
        },
        toggle(id) {
            var self = this;
            const index = this.opened.indexOf(id);

            if (index > -1) {
                this.opened.splice(index, 1);
            } else {
                this.opened.push(id);
            }
        },
        checkIfDelay(date1, date2) {
            a = new Date(date1);
            b = new Date(date2);

            if (b > a) {
                return true;
            } else {
                return false;
            }
        },
        changeMode() {
            var self = this;
            if (this.view_mode) {
                this.view_mode = false;
                // setTimeout(function(){
                //     self.refreshPlugin()
                // },100)
            } else {
                this.view_mode = true;
            }
        },
        refresh() {
            var self = this;
            this.search = false;
            this.first_load = false;
            self.loading_data = true;
            self.opened = []
            self.view_mode = true
            self.newBOL = {};
            axios
                .post(
                    `http://${
                    window.location.host
                    }/${myBaseName}api/bill_of_lading/refresh_master_sop`
                )
                .then(response => {
                    self.loading_data = false;
                    self.list_of_BOL = response.data;
                });
                self.searchTrue = false;
        },
        searchBL() {
            var self = this;
            this.search = false;
            this.first_load = false;
            axios
                .post(
                    `http://${
                    window.location.host
                    }/${myBaseName}api/bill_of_lading/get_info`,
                    {
                        category: "SOP",
                        bl_no: self.search_bl_no,
                        search_factory: self.search_factory,
                        search_shipping_line: self.search_shipping_line,
                        search_current_status: self.search_current_status,
                        search_target_gatepass: self.search_target_gatepass,
                        search_commodity: self.search_commodity
                    }
                )
                .then(response => {
                    self.list_of_BOL = {};
                    self.list_of_BOL = response.data;

                    // setTimeout(function(){
                    //     self.refreshPlugin()
                    // },100)
                });
            self.searchTrue = true;
        },
        saveBlur(id, columnName, value, index) {
            axios
                .post(
                    `http://${
                    window.location.host
                    }/${myBaseName}api/bill_of_lading/save_arrival_blur`,
                    {
                        id: id,
                        columnName: columnName,
                        value: value,
                    }
                )
                .then(function (res) {
                    console.log(res.data);
                })
                .catch(function (err) {
                    console.log(err);
                });

            // _.forEach won't throw errors if arr is not an array...
        },
        saveBlurGatepass(id, columnName, value, index) {
            axios
                .post(
                    `http://${
                    window.location.host
                    }/${myBaseName}bill_of_lading/save_arrival_blurweb`,
                    {
                        id: id,
                        columnName: columnName,
                        value: value,
                    }
                )
                .then(function (res) {
                    console.log(res.data);
                })
                .catch(function (err) {
                    console.log(err);
                });

            // _.forEach won't throw errors if arr is not an array...
        },



        sameContainers(columnName, value, index) {
            _.forEach(this.list_of_BOL[index].container_numbers, function (obj) {
                // _.set won't throw errors if obj is not an object. With more complex objects, if a portion of the path doesn't exist, _.set creates it
                _.set(obj, columnName, value);
            });
        },
        saveContainerBlur(id, columnName, value) {
            axios
                .post(
                    `http://${
                    window.location.host
                    }/${myBaseName}bill_of_lading/save_container_columnweb`,
                    {
                        id: id,
                        columnName: columnName,
                        value: value,
                    }
                )
                .then(function (res) {
                    console.log(res.data);
                })
                .catch(function (err) {
                    console.log(err);
                });
        },
        saveDelayReason(id, gatepass, reason, current_status) {
            $.confirm({
                title: "IMS Confirm Action",
                content:
                    "Are you sure you want to save this delay information?",
                buttons: {
                    confirm: function () {
                        axios
                            .post(
                                `http://${
                                window.location.host
                                }/${myBaseName}bill_of_lading/reason_delay_gatepass_web`,
                                {
                                    id: id,
                                    gatepass: gatepass,
                                    reason: reason,
                                    current_status: "Achieved Gatepass",
                                }
                            )
                            .then(function (res) {
                                $.alert({
                                    title: "System Information",
                                    content: "Save Successfully",
                                    animation: "left",
                                    closeAnimation: "right",
                                });
                            })
                            .catch(function (err) {
                                console.log(err);
                            });
                    },
                    cancel: {
                        text: "cancel",
                        btnClass: "btn-warning",
                    },
                },
            });
        },
        saveDelayReasonContainer(id, gatepass, reason, current_status) {
            $.confirm({
                title: "IMS Confirm Action",
                content:
                    "Are you sure you want to save this delay information?",
                buttons: {
                    confirm: function () {
                        axios
                            .post(
                                `http://${
                                window.location.host
                                }/${myBaseName}bill_of_lading/reason_delay_gatepass_containerweb`,
                                {
                                    id: id,
                                    gatepass: gatepass,
                                    reason: reason,
                                    current_status: "Achieved Gatepass",
                                }
                            )
                            .then(function (res) {
                                $.alert({
                                    title: "System Information",
                                    content: "Save Successfully",
                                    animation: "left",
                                    closeAnimation: "right",
                                });
                            })
                            .catch(function (err) {
                                console.log(err);
                            });
                    },
                    cancel: {
                        text: "cancel",
                        btnClass: "btn-warning",
                    },
                },
            });
        },
        refreshPlugin() {
            var self = this;


            $('#search_target_gatepass').datepicker({
                                format: "yyyy-mm-dd"
                            });


            $(document).on(
                "focus",
                ".actual_gatepass_container:not(.hasDatepicker)",
                function () {
                    $(this).addClass("hasDatepicker");

                    $(this)
                        .datepicker({
                            format: "yyyy-mm-dd",
                            clearBtn: true,
                            beforeShow: function (input, inst) {
                                $(this).addClass("hasDatepicker");
                            },
                        })
                        .on("changeDate", e => {
                            e.stopPropagation();
                            var selected_element = $(e.currentTarget);
                            var select_val = selected_element.val();

                            var list_index = selected_element.data("index");
                            var index_container = selected_element.data(
                                "index_container"
                            );

                            self.list_of_BOL[list_index].container_numbers[
                                index_container
                            ].actual_gatepass = select_val;

                            var container_id =
                                self.list_of_BOL[list_index].container_numbers[
                                    index_container
                                ].id;

                            if (
                                self.checkIfDelay(
                                    self.list_of_BOL[list_index]
                                        .target_gatepass,
                                    select_val
                                )
                            ) {
                                $.alert({
                                    title: "Delay Information",
                                    content:
                                        "The reason of delay is require to fill up.",
                                    animation: "left",
                                    closeAnimation: "right",
                                });

                                self.list_of_BOL[list_index].container_numbers[
                                    index_container
                                ].reason_of_delay_gatepass_boolean = true;
                                self.list_of_BOL[list_index].container_numbers[
                                    index_container
                                ].sop_current_status = "Achieved Gatepass";

                                self.saveContainerBlur(
                                    container_id,
                                    "actual_gatepass",
                                    null
                                );
                            } else {
                                self.list_of_BOL[list_index].container_numbers[
                                    index_container
                                ].reason_of_delay_gatepass_boolean = false;
                                self.list_of_BOL[list_index].container_numbers[
                                    index_container
                                ].sop_current_status = "Achieved Gatepass";
                                self.saveContainerBlur(
                                    container_id,
                                    "actual_gatepass",
                                    select_val
                                );
                                self.saveContainerBlur(
                                    container_id,
                                    "sop_current_status",
                                    "Achieved Gatepass"
                                );
                            }
                        })
                        .on("clearDate", e => {
                            e.stopPropagation();
                            var selected_element = $(e.currentTarget);
                            var select_val = selected_element.val();

                            var list_index = selected_element.data("index");
                            var index_container = selected_element.data(
                                "index_container"
                            );

                            self.list_of_BOL[list_index].container_numbers[
                                index_container
                            ].actual_gatepass = select_val;

                            var container_id =
                                self.list_of_BOL[list_index].container_numbers[
                                    index_container
                                ].id;

                            self.saveContainerBlur(
                                container_id,
                                "actual_gatepass",
                                null
                            );
                            self.saveContainerBlur(
                                container_id,
                                "sop_current_status",
                                "Achieved Gatepass"
                            );
                        });
                }
            );

            $(document).on(
                "focus",
                ".actual_gatepass:not(.hasDatepicker)",
                function () {
                    $(this).addClass("hasDatepicker");

                    $(this)
                        .datepicker({
                            format: "yyyy-mm-dd",
                            clearBtn: true,
                        })
                        .on("changeDate", e => {
                            e.stopPropagation();
                            var selected_element = $(e.currentTarget);
                            var select_val = selected_element.val();
                            var id = parseInt(selected_element.prev().html());
                            var object = _.find(self.list_of_BOL, { id: id });
                            object.actual_gatepass = select_val;
                            var I = selected_element.data("index");

                            if (
                                self.checkIfDelay(
                                    object.target_gatepass,
                                    object.actual_gatepass
                                )
                            ) {
                                $.alert({
                                    title: "Delay Information",
                                    content:"The reason of delay is require to fill up.",
                                    animation: "left",
                                    closeAnimation: "right",
                                });

                                object.reason_of_delay_gatepass_boolean = true;
                                object.sop_current_status =
                                    object.sop_current_status_temporary;
                                object.sop_current_status = "Achieved Gatepass";
                                self.saveBlurGatepass(id, "actual_gatepass", null);
                                // self.saveBlur( id,'sop_current_status',  object.sop_current_status)
                            } else {
                                object.reason_of_delay_gatepass_boolean = false;
                                object.sop_current_status_temporary =
                                    object.sop_current_status;
                                object.sop_current_status = "Achieved Gatepass";
                                object.actual_gatepass = select_val;

                                self.saveBlurGatepass(
                                    id,
                                    "sop_current_status",
                                    "Achieved Gatepass"
                                );
                                self.saveBlurGatepass(
                                    id,
                                    "actual_gatepass",
                                    select_val
                                );
                            }

                            self.sameContainers(
                                "actual_gatepass",
                                select_val,
                                I
                            );
                            self.sameContainers(
                                "sop_current_status",
                                "Achieved Gatepass",
                                I
                            );
                        })
                        .on("clearDate", e => {
                            e.stopPropagation();
                            var selected_element = $(e.currentTarget);
                            var select_val = selected_element.val();
                            var id = parseInt(selected_element.prev().html());
                            var object = _.find(self.list_of_BOL, { id: id });
                            object.sop_current_status = "";

                            self.saveBlurGatepass(id, "actual_gatepass", select_val);
                            self.saveBlurGatepass(id, "sop_current_status", "");
                            var I = selected_element.data("index");
                            self.sameContainers(
                                "actual_gatepass",
                                select_val,
                                I
                            );
                        });
                }
            );
            // $('.actual_gatepass_container').datepicker({
            //     format: "yyyy-mm-dd",clearBtn: true
            // }).on(
            //     "changeDate", (e) => {
            //     e.stopPropagation();
            //     var selected_element = $(e.currentTarget);
            //     var select_val = selected_element.val();

            //     var list_index = selected_element.data('index');
            //     var index_container = selected_element.data('index_container');

            //     self.list_of_BOL[list_index].container_numbers[index_container].actual_gatepass = select_val

            //     var container_id = self.list_of_BOL[list_index].container_numbers[index_container].id;

            //     if(self.checkIfDelay( self.list_of_BOL[list_index].target_gatepass, select_val)){
            //         $.alert({
            //             title: 'Delay Information',
            //             content: 'The reason of delay is require to fill up.',
            //             animation: 'left',
            //             closeAnimation: 'right',
            //         });

            //         self.list_of_BOL[list_index].container_numbers[index_container].reason_of_delay_gatepass_boolean = true
            //         self.list_of_BOL[list_index].container_numbers[index_container].sop_current_status = 'Achieved Gatepass';

            //         self.saveContainerBlur(container_id,'actual_gatepass',null)

            //     }
            //     else{
            //         self.list_of_BOL[list_index].container_numbers[index_container].reason_of_delay_gatepass_boolean = false
            //         self.list_of_BOL[list_index].container_numbers[index_container].sop_current_status = 'Achieved Gatepass';
            //         self.saveContainerBlur(container_id,'actual_gatepass',select_val)
            //         self.saveContainerBlur(container_id,'sop_current_status','Achieved Gatepass')

            //     }

            // }).on(
            //     "clearDate", (e) => {
            //     e.stopPropagation();
            //     var selected_element = $(e.currentTarget);
            //     var select_val = selected_element.val();

            //     var list_index = selected_element.data('index');
            //     var index_container = selected_element.data('index_container');

            //     self.list_of_BOL[list_index].container_numbers[index_container].actual_discharge = select_val

            //     var container_id = self.list_of_BOL[list_index].container_numbers[index_container].id;

            //     self.saveContainerBlur(container_id,'actual_gatepass',null)
            //     self.saveContainerBlur(container_id,'sop_current_status','Achieved Gatepass')

            // });

            // $('.actual_gatepass').datepicker({
            //     format: "yyyy-mm-dd",clearBtn: true
            // }).on(
            //     "changeDate", (e) => {

            //         e.stopPropagation();
            //     var selected_element = $(e.currentTarget);
            //     var select_val = selected_element.val();
            //     var id = parseInt(selected_element.prev().html());
            //     var object = _.find(this.list_of_BOL, { 'id':  id });
            //     object.actual_gatepass  = select_val;
            //     var I = selected_element.data('index');
            //     console.log('test');
            //     if(self.checkIfDelay( object.target_gatepass, object.actual_gatepass)){
            //         $.alert({
            //             title: 'Delay Information',
            //             content: 'The reason of delay is require to fill up.',
            //             animation: 'left',
            //             closeAnimation: 'right',
            //         });

            //             object.reason_of_delay_gatepass_boolean = true
            //             object.sop_current_status  = object.sop_current_status_temporary
            //             object.sop_current_status = 'Achieved Gatepass';
            //             self.saveBlur( id,'actual_gatepass',null)
            //            // self.saveBlur( id,'sop_current_status',  object.sop_current_status)
            //     }else{
            //         object.reason_of_delay_gatepass_boolean = false
            //         object.sop_current_status_temporary =  object.sop_current_status
            //         object.sop_current_status = 'Achieved Gatepass';
            //         object.actual_gatepass  = select_val;

            //         self.saveBlur( id,'sop_current_status','Achieved Gatepass')
            //         self.saveBlur( id,'actual_gatepass',select_val)

            //     }

            //     self.sameContainers('actual_gatepass',select_val,I)
            //     self.sameContainers('sop_current_status','Achieved Gatepass',I)

            // }).on(
            //     "clearDate", (e) => {
            //         e.stopPropagation();
            //         var selected_element = $(e.currentTarget);
            //         var select_val = selected_element.val();
            //         var id = parseInt(selected_element.prev().html());
            //         var object = _.find(this.list_of_BOL, { 'id':  id });
            //         object.sop_current_status = '';

            //         self.saveBlur( id,'actual_gatepass',select_val)
            //         self.saveBlur( id,'sop_current_status','')
            //         var I = selected_element.data('index');
            //         self.sameContainers('actual_gatepass',select_val,I  )

            // });
        },
        destroyPlugin() {
            $(".actual_gatepass")
                .datepicker()
                .destroy();
        },
    },
    mounted() {
        var self = this;

        self.refreshPlugin();
    },
});
