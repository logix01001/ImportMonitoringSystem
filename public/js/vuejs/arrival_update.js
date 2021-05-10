    new Vue({
    el: ".vue_app_element",
    data: {
        loading_data: false,
        list_of_BOL: list_of_BOL,
        connecting_vessels: connecting_vessels,
        connecting_vessels_filter: connecting_vessels_filter,
        shipping_lines: shipping_lines,
        totalRecord: totalRecord,
        view_mode: true,
        filter_cv: "",
        filter_sl: "",
        search_cn: "",
        date_endorse: "",
        opened: [],
        showprogress: false,
        numberofTake: 20,
        woods: false,
        generate_date: "date_endorse",
        header_bl: "",
        header_consignee: "",
        header_commodities: "",
        header_shipping_line: "",
        header_vessel: "",
        header_connecting_vessel: "",
        header_registry_no: "",
        header_pod: "",
        header_ETA: "",
        header_LETA: "",
        header_ATA: "",
        header_ABD: "",
        header_DE: "",
        header_IP: "",
        header_E2M: "",
        header_AP: "",
        header_AT: "",
        header_RD: "",
        header_TSAD: "",
        copy_date: "",
    },
    computed: {
        filteredList() {
            var self = this;
            if (this.list_of_BOL.length > 0) {
                return this.list_of_BOL.filter(post => {
                    return post.bl_no
                        .toLowerCase()
                        .includes(this.header_bl.toLowerCase()) &&
                        post.factory
                            .toLowerCase()
                            .includes(this.header_consignee.toLowerCase()) &&
                        post.shipping_line
                            .toLowerCase()
                            .includes(
                                this.header_shipping_line.toLowerCase()
                            ) &&
                        //post.vessel.toLowerCase().includes(this.header_vessel.toLowerCase())  &&
                        (this.header_vessel === "-"
                        ? post.vessel_string == ""
                        : post.vessel_string
                              .toLowerCase()
                              .includes(this.header_vessel.toLowerCase())) &&
                              (this.header_connecting_vessel === "-"
                                  ? post.connecting_vessel_string == ""
                                  : post.connecting_vessel_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_connecting_vessel.toLowerCase()
                                        ))
                                        &&
                                (this.header_registry_no === "-"
                                    ? post.registry_no_string == ""
                                    : post.registry_no_string
                                            .toLowerCase()
                                            .includes(
                                                this.header_registry_no.toLowerCase()
                                            ))&&
                              (this.header_pod === "-"
                                  ? post.pod_string == ""
                                  : post.pod_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_pod.toLowerCase()
                                        )) &&
                              (this.header_ETA === "-"
                                  ? post.estimated_time_arrival_string == ""
                                  : post.estimated_time_arrival_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_ETA.toLowerCase()
                                        )) &&
                                (this.header_LETA === "-"
                                ? post.latest_estimated_time_arrival_string == ""
                                : post.latest_estimated_time_arrival_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_LETA.toLowerCase()
                                        )) &&
                              (this.header_ATA === "-"
                                  ? post.actual_time_arrival_string == ""
                                  : post.actual_time_arrival_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_ATA.toLowerCase()
                                        )) &&
                              (this.header_ABD === "-"
                                  ? post.actual_berthing_date_string == ""
                                  : post.actual_berthing_date_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_ABD.toLowerCase()
                                        )) &&
                              (this.header_DE === "-"
                                  ? post.date_endorse_string == ""
                                  : post.date_endorse_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_DE.toLowerCase()
                                        )) &&
                              (this.header_IP === "-"
                                  ? post.date_approve_ip_string == ""
                                  : post.date_approve_ip_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_IP.toLowerCase()
                                        )) &&
                              (this.header_E2M === "-"
                                  ? post.e2m_string == ""
                                  : post.e2m_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_E2M.toLowerCase()
                                        )) &&
                              (this.header_AP === "-"
                                  ? post.actual_process_string == ""
                                  : post.actual_process_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_AP.toLowerCase()
                                        )) &&
                              (this.header_AT === "-"
                                  ? post.assessment_tag_string == ""
                                  : post.assessment_tag_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_AT.toLowerCase()
                                        )) &&
                              (this.header_RD === "-"
                                  ? post.remarks_of_docs_string == ""
                                  : post.remarks_of_docs_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_RD.toLowerCase()
                                        )) &&
                              (this.header_TSAD === "-"
                                  ? post.tsad_no_string == ""
                                  : post.tsad_no_string
                                        .toLowerCase()
                                        .includes(
                                            this.header_TSAD.toLowerCase()
                                        )) &&
                              post.commodities_string
                                  .toLowerCase()
                                  .includes(
                                      this.header_commodities.toLowerCase()
                                  );
                });
            }
        },
    },
    methods: {
        clearFilter() {
            this.header_bl = "";
            this.header_consignee = "";
            this.header_commodities = "";
            this.header_shipping_line = "";
            this.header_vessel = "";
            this.header_connecting_vessel = "";
            this.header_pod = "";
            this.header_ETA = "";
            this.header_ATA = "";
            this.header_ABD = "";
            this.header_DE = "";
            this.header_IP = "";
            this.header_E2M = "";
            this.header_AP = "";
            this.header_AT = "";
            this.header_RD = "";
            this.header_TSAD = "";
        },
        getRecord_e2m(take) {
            var self = this;
            let skip = this.list_of_BOL.length;
            let filter_cv = this.filter_cv;
            self.showprogress = true;

            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
            $.blockUI({ message: '<h3> Processing ...</h3>' });
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/get_Record_arrival_update_e2m`,
                    {
                        skip: skip,
                        take: take,
                        connecting_vessel: filter_cv,
                    }
                )
                .then(response => {
                    response.data.forEach(value => {
                        this.list_of_BOL.push(value);
                    });
                    self.showprogress = false;

                    setTimeout(function() {
                        self.refreshSelect2();
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
        getRecord(take) {
            var self = this;
            let skip = this.list_of_BOL.length;
            let filter_cv = this.filter_cv;
            self.showprogress = true;


            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
            $.blockUI({ message: '<h3> Processing ...</h3>' });
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/get_Record_arrival_update_discharge`,
                    {
                        skip: skip,
                        take: take,
                        connecting_vessel: filter_cv,
                    }
                )
                .then(response => {
                    response.data.forEach(value => {
                        this.list_of_BOL.push(value);
                    });
                    self.showprogress = false;

                    setTimeout(function() {
                        self.refreshSelect2();
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
        getRecord_all(take) {
            var self = this;
            let skip = this.list_of_BOL.length;
            let filter_cv = this.filter_cv;
            self.showprogress = true;
            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
            $.blockUI({ message: '<h3> Processing ...</h3>' });
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/get_Record_arrival_update_discharge_e2m`,
                    {
                        skip: skip,
                        take: take,
                        connecting_vessel: filter_cv,
                    }
                )
                .then(response => {
                    response.data.forEach(value => {
                        this.list_of_BOL.push(value);
                    });
                    self.showprogress = false;

                    setTimeout(function() {
                        self.refreshSelect2();
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
        isNumber: function(evt) {
            evt = evt ? evt : window.event;
            var charCode = evt.which ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                evt.preventDefault();
            } else {
                return true;
            }
        },
        refresh_discharge_e2m() {
            var self = this;
            self.clearFilter();
            self.list_of_BOL = {};
            self.loading_data = true;
            self.opened = []
            self.view_mode = true
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/refresh_master_arrival_update_discharge_e2m`
                )
                .then(response => {
                    self.loading_data = false;
                    self.list_of_BOL = response.data;
                    setTimeout(function() {
                        self.refreshSelect2();
                        self.refreshPlugins();
                    }, 500);
                    self.filter_cv = "";
                    self.filter_sl = "";

                    $("#filter_cv")
                        .val("")
                        .trigger("change");
                    $("#filter_sl")
                        .val("")
                        .trigger("change");
                });
        },
        refresh_discharge() {
            var self = this;
            self.clearFilter();
            self.list_of_BOL = {};
            self.loading_data = true;
            self.opened = []
            self.view_mode = true
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/refresh_master_arrival_update_discharge`
                )
                .then(response => {
                    self.loading_data = false;
                    self.list_of_BOL = response.data;
                    setTimeout(function() {
                        self.refreshSelect2();
                        self.refreshPlugins();
                    }, 500);
                    self.filter_cv = "";
                    self.filter_sl = "";

                    $("#filter_cv")
                        .val("")
                        .trigger("change");
                    $("#filter_sl")
                        .val("")
                        .trigger("change");
                });
        },

        refresh_e2m() {
            var self = this;
            self.clearFilter();
            self.list_of_BOL = {};
            self.loading_data = true;
            self.opened = []
            self.view_mode = true
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/refresh_master_arrival_update_e2m`
                )
                .then(response => {
                    self.loading_data = false;
                    self.list_of_BOL = response.data;
                    setTimeout(function() {
                        self.refreshSelect2();
                        self.refreshPlugins();
                    }, 500);
                });
        },
        splitBL_NO(id, split_bl_no_fk, quantity) {
            if (quantity == 0) {
                // axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_connecting_confirm`,
                // {
                //         id : id,
                //         value : value

                // }).then(function(res){
                //         console.log(res.data)
                // }).catch(function(err){
                //         console.log(err)
                // })

                $.alert({
                    title: "Split Information",
                    content: "Split under BL # " + split_bl_no_fk,
                    animation: "left",
                    closeAnimation: "right",
                });
            }
        },
        toggle(id) {
            var self = this;
            const index = this.opened.indexOf(id);

            if (index > -1) {
                this.opened.splice(index, 1);
            } else {
                this.opened.push(id);
            }

            // setTimeout(function(){
            //     self.refreshPlugins();
            // },300)
        },
        trueOrFalse(value) {
            if (value == true) {
                return 1;
            } else {
                return 0;
            }
        },
        changeMode() {
            var self = this
            if (this.view_mode) {
                this.view_mode = false;

                setTimeout(function(){
                    self.refreshSelect2();
                    self.refreshPlugins();
                },500)

            } else {
                this.view_mode = true;
            }
        },
        connect_confirm(id, value) {
            //value = this.trueOrFalse(value)

            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/save_connecting_confirm`,
                    {
                        id: id,
                        value: value,
                    }
                )
                .then(function(res) {
                    console.log(res.data);
                })
                .catch(function(err) {
                    console.log(err);
                });
        },
        saveBlur(id, columnName, value) {
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
                .then(function(res) {
                    console.log(res.data);
                })
                .catch(function(err) {
                    console.log(err);
                });
        },
        saveDischarge(id, discharge_date,bl_no,target_gatepass) {
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/save_container_discharge`,
                    {
                        id: id,
                        value: discharge_date,
                        bl_no: bl_no
                    }
                )
                .then(function(res) {
                    console.log(res.data);
                })
                .catch(function(err) {
                    console.log(err);
                });
        },
        saveDischarge_all(ids, discharge_date,bl_no) {
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/save_container_discharge_all`,
                    {
                        ids: ids,
                        value: discharge_date,
                        bl_no: bl_no
                    }
                )
                .then(function(res) {
                    console.log(res.data);
                })
                .catch(function(err) {
                    console.log(err);
                });
        },
        filterSearch() {
            var self = this;
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/arrival_update_filter`,
                    {
                        connecting_vessel: self.filter_cv,
                        shipping_line: self.filter_sl,
                        search: self.search_cn,
                    }
                )
                .then(function(res) {
                    self.list_of_BOL = {};
                    self.list_of_BOL = res.data;

                    setTimeout(function() {
                        self.refreshSelect2();
                        self.refreshPlugins();
                        $(".connecting_vessels").trigger("change");
                        $(".pod").trigger("change");
                    }, 500);
                })
                .catch(function(err) {
                    console.log(err);
                });
        },

        filterSearch_e2m() {
            var self = this;
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/arrival_update_filter_e2m`,
                    {
                        search: self.search_cn,
                    }
                )
                .then(function(res) {
                    self.list_of_BOL = {};
                    self.list_of_BOL = res.data;

                    setTimeout(function() {
                        self.refreshPlugins();
                    }, 100);
                })
                .catch(function(err) {
                    console.log(err);
                });
        },

        assesmentTagBlur(id) {
            var object = _.find(this.list_of_BOL, { id: id });

            if (object.remarks_of_docs == null) {
                if (object.assessment_tag == "RED") {
                    object.remarks_of_docs = "AS RED ";
                } else {
                    object.remarks_of_docs = "";
                }
            } else {
                switch (object.assessment_tag) {
                    case "RED":
                        if (!object.remarks_of_docs.includes("AS RED")) {
                            object.remarks_of_docs =
                                "AS RED " + object.remarks_of_docs;
                        }
                        break;
                    default:
                        if (object.remarks_of_docs.includes("AS RED")) {
                            object.remarks_of_docs = object.remarks_of_docs.replace(
                                "AS RED ",
                                ""
                            );
                        }
                }
            }

            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/assessment_tag_remarks`,
                    {
                        bl_no: object.bl_no,
                        remarks_of_docs: object.remarks_of_docs,
                        assessment_tag: object.assessment_tag,
                    }
                )
                .then(function(res) {})
                .catch(function(err) {});
        },
        refreshPlugins() {
            var self = this;





        },
        refreshSelect2() {
            var self = this;
            $(".connecting_vessels")
                .select2({
                    width: "90%",
                    tags: true,
                    insertTag: function(data, tag) {
                        tag.text = "New Item ? Click Here To Add";
                        data.push(tag);
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                })
                .on("select2:select", e => {

                    //    this.selectedModel_Number = $('#modelhouselist').val()
                    //    this.getSelectedModelHouse()
                    var selected_element = $(e.currentTarget);


                    var select_val = selected_element.val().toUpperCase();


                    //console.log(selected_element.prev().html())
                    var id = parseInt(selected_element.prev().html());


                    var object = _.find(this.list_of_BOL, { id: id });
                    object.connecting_vessel = select_val;


                    var I = _.findIndex(
                        this.connecting_vessels_filter,
                        function(o) {
                            return (
                                o.connecting_vessel == selected_element.val()
                            );
                        }
                    );

                    if (I == -1) {
                        this.connecting_vessels_filter.push({
                            connecting_vessel: select_val,
                        });
                    }

                    self.saveBlur(id, "connecting_vessel", select_val);
                });

            $(".pod")
                .select2({
                    width: "90%",
                    tags: true,
                })
                .on("select2:select", e => {
                    //    this.selectedModel_Number = $('#modelhouselist').val()
                    //    this.getSelectedModelHouse()
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val().toUpperCase();
                    //console.log(selected_element.prev().html())
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(this.list_of_BOL, { id: id });
                    object.pod = select_val;

                    self.saveBlur(id, "pod", select_val);
                });
        },
    },
    mounted() {
        var self = this;
        // requires jquery library
        var leftscroll = $("#tablerow").scrollLeft();

        $(document).on(
            "focus",
            ".date_endorse:not(.hasDatepicker)",
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
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.date_endorse = select_val;

                    self.saveBlur(id, "date_endorse", select_val);
                    /* Select the text field */
                    if (selected_element.val() != "") {
                        localStorage.copy_date = selected_element.val();
                    }
                })
                .on("clearDate", e => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.date_endorse = null;

                    self.saveBlur(id, "date_endorse", null);
                });
        });



        $(document).on(
            "focus",
            ".date_approve_ip:not(.hasDatepicker)",
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
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.date_approve_ip = select_val;

                    self.saveBlur(id, "date_approve_ip", select_val);
                    /* Select the text field */
                    if (selected_element.val() != "") {
                        localStorage.copy_date = selected_element.val();
                    }
                })
                .on("clearDate", e => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.date_approve_ip = null;

                    self.saveBlur(id, "date_approve_ip", null);
                });
        });





        $(document).on(
            "focus",
            ".e2m:not(.hasDatepicker)",
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
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.e2m = select_val;

                    self.saveBlur(id, "e2m", select_val);
                    /* Select the text field */
                    if (selected_element.val() != "") {
                        localStorage.copy_date = selected_element.val();
                    }
                })
                .on("clearDate", e => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.e2m = null;

                    self.saveBlur(id, "e2m", null);
                });
        });




        $(document).on(
            "focus",
            ".actual_process:not(.hasDatepicker)",
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
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.actual_process = select_val;

                    self.saveBlur(id, "actual_process", select_val);
                    /* Select the text field */
                    if (selected_element.val() != "") {
                        localStorage.copy_date = selected_element.val();
                    }
                })
                .on("clearDate", e => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.actual_process = null;

                    self.saveBlur(id, "actual_process", null);
                });
        });










        $(document).on(
            "focus",
            ".actual_time_arrival:not(.hasDatepicker)",
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
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.actual_time_arrival = select_val;

                    self.saveBlur(id, "actual_time_arrival", select_val);
                    /* Select the text field */
                    if (selected_element.val() != "") {
                        localStorage.copy_date = selected_element.val();
                    }
                }).on("clearDate", e => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.actual_time_arrival = null;

                    self.saveBlur(id, "actual_time_arrival", null);
                });
        });




        $(document).on(
            "focus",
            ".latest_estimated_time_arrival:not(.hasDatepicker)",
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
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.latest_estimated_time_arrival = select_val;

                    self.saveBlur(id, "latest_estimated_time_arrival", select_val);
                    /* Select the text field */
                    if (selected_element.val() != "") {
                        localStorage.copy_date = selected_element.val();
                    }
                })
                .on("clearDate", e => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.latest_estimated_time_arrival = null;

                    self.saveBlur(id, "latest_estimated_time_arrival", null);
                });
        });


        $(document).on(
            "focus",
            ".estimated_time_arrival:not(.hasDatepicker)",
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
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.estimated_time_arrival = select_val;

                    self.saveBlur(id, "estimated_time_arrival", select_val);
                    /* Select the text field */
                    if (selected_element.val() != "") {
                        localStorage.copy_date = selected_element.val();
                    }
                })
                .on("clearDate", e => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.estimated_time_arrival = null;

                    self.saveBlur(id, "estimated_time_arrival", null);
                });
        });

        $(document).on(
            "focus",
            ".berthing_date:not(.hasDatepicker)",
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
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(self.list_of_BOL, { id: id });
                    object.actual_berthing_date = select_val;

                    self.saveBlur(id, "actual_berthing_date", select_val);

                    /* Select the text field */
                    if (selected_element.val() != "") {
                        localStorage.copy_date = selected_element.val();
                    }
                });
        });

        $(document).on(
            "focus",
            ".container_discharge:not(.hasDatepicker)",
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

                        var list_index = selected_element.data("index");
                        var index_container = selected_element.data(
                            "index_container"
                        );
						var index_bl_no = selected_element.data(
                            "index_bl_no"
                        );


						//NEWLY ADDED 2020-01-04 due to unresseting of index number when filter
						//maybe a cause of target gatepass
						var list_index = _.findIndex( self.list_of_BOL, function(o) { return o.bl_no == index_bl_no; });

						/*
						console.log(list_index2,list_index, index_container);

						alert();
						return false;

						*/
                        //console.log(index_bl_no,list_index, index_container);

                        self.list_of_BOL[list_index].container_numbers[
                            index_container
                        ].actual_discharge = select_val;

                        var container_id =
                            self.list_of_BOL[list_index].container_numbers[
                                index_container
                            ].id;

                        if (self.list_of_BOL[list_index].sameDischarge) {
                            _.each(
                                self.list_of_BOL[list_index]
                                    .container_numbers,
                                function(obj, key) {
                                    obj["actual_discharge"] = select_val;
                                }
                            );
                            var list_of_container_id = _.map(
                                self.list_of_BOL[list_index]
                                    .container_numbers,
                                "id"
                            );

                            var list_of_container_bl_no = self.list_of_BOL[list_index].bl_no;


                            self.saveDischarge_all(
                                list_of_container_id,
                                select_val,
                                list_of_container_bl_no
                            );
                        } else {
                            var list_of_container_bl_no = self.list_of_BOL[list_index].bl_no;

                            self.saveDischarge(container_id,
                                select_val,
                                list_of_container_bl_no
                               );

                        }

                        /* Select the text field */
                        if (selected_element.val() != "") {
                            localStorage.copy_date = selected_element.val();
                        }
                    })
                    .on("clearDate", e => {
                        var selected_element = $(e.currentTarget);
                        var select_val = selected_element.val();

                        var list_index = selected_element.data("index");
                        var index_container = selected_element.data(
                            "index_container"
                        );

                        console.log(list_index, index_container);

                        self.list_of_BOL[list_index].container_numbers[
                            index_container
                        ].actual_discharge = select_val;

                        var container_id =
                            self.list_of_BOL[list_index].container_numbers[
                                index_container
                            ].id;

                        if (self.list_of_BOL[list_index].sameDischarge) {
                            _.each(
                                self.list_of_BOL[list_index]
                                    .container_numbers,
                                function(obj, key) {
                                    obj["actual_discharge"] = select_val;
                                }
                            );
                            var list_of_container_id = _.map(
                                self.list_of_BOL[list_index]
                                    .container_numbers,
                                "id"
                            );
                            var list_of_container_bl_no = self.list_of_BOL[list_index].bl_no;


                            self.saveDischarge_all(
                                list_of_container_id,
                                select_val,
                                list_of_container_bl_no

                            );
                        } else {
                            var list_of_container_bl_no = self.list_of_BOL[list_index].bl_no;


                            self.saveDischarge(
                                list_of_container_id,
                                select_val,
                                list_of_container_bl_no

                            );
                        }
                    });
            }
        );

        $(document).on(
            "keypress",
            ".berthing_date , .container_discharge, .date_endorse, .date_approve_ip, .actual_process, .actual_time_arrival, .estimated_time_arrival,.latest_estimated_time_arrival, .e2m",
            function(e) {
                if (e.which == 96) {
                    $(this).val(localStorage.getItem("copy_date"));
                }
            }
        );

        $("#tablerow").on("scroll", function(e) {
            var leftscroll = e.currentTarget.scrollLeft;

            if (leftscroll == 0) {
                $(".stickycolumn").css({
                    "background-color": "transparent ",
                    color: "#676a6c",
                    left: 0,
                });
            } else {
                $(".stickycolumn").css({
                    background: "rgb(47,64,80,0.8)",
                    color: "#fff",
                    left: 0,
                    'z-index':3
                });
            }
            //console.log(leftscroll)
        });
        self.refreshPlugins();
        self.refreshSelect2();

        $("#filter_sl")
            .select2({
                width: "100%",
            })
            .on("select2:select", e => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val().toUpperCase();
                self.filter_sl = select_val;

                self.filterSearch();
            });

        $("#date_endorse")
            .datepicker({
                format: "yyyy-mm-dd",
                clearBtn: true,
            })
            .on("changeDate", e => {
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                this.date_endorse = select_val;
            })
            .on("clearDate", e => {
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                this.date_endorse = select_val;
            });

        $("#filter_cv")
            .select2({
                width: "100%",
            })
            .on("select2:select", e => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val().toUpperCase();
                self.filter_cv = select_val;

                self.filterSearch();
            });
    },
});
