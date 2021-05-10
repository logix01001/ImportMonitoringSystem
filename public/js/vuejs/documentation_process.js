new Vue({
    el: '.vue_app_element',
    data: {
        list_of_BOL: list_of_BOL,
        view_mode: true,
        filter_search: '',
        search_cn: '',
        opened: [],
        search: false,
        dateToday: '',
        loading_data: false,
        numberofTake: 20,
        showprogress: false,
        status: ['No Registry yet','Lodgement','Check/Send','Approved IP','Approved E2M','For Port Processing'],
        first_three_dates : [],
        statusFilter : 'ALL'
        
        
    },
    computed:{
        listForNoRegistry: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.connecting_vessel == null || i.registry_no == null
            })
        },
        listForLodgement: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
                return i.registry_no != null
            })
        },
        listForCheckSend: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
                return i.registry_no_confirm == 1
            })
        },
        listForApprovedIP: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
                return i.date_approve_ip != null
            })
        },
        listForApprovedE2M: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
                return i.e2m != null
            })
        },
        listForPort: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
                return i.actual_process != null
            })
        },
    },
    methods: {
        changeStatus(){
            var self = this
            self.loading_data = true
            $("#detail_obj").dataTable().fnDestroy()

            setTimeout(function() {
                self.loading_data = false
                self.dataTableInit();
            }, 500);
        },
        getDocsCount(docsDate = null,type = null){
            var self = this
            if(type == null){
               return _.filter(self.list_of_BOL, function(o) { if (o.estimated_time_arrival == docsDate) return o }).length
            }else{
                return _.filter(self.list_of_BOL, function(o) { if (o.estimated_time_arrival == docsDate && o[type] != null) return o }).length
            }
        },
        getUnique(arr, limit) {
            var indices = [];
            var out = [];
            for (var i = 0, l = arr.length; i < l; i++) {
               var id = arr[i].estimated_time_arrival;
               if (indices.indexOf(id) === -1) {
                 out.push(arr.filter(function (el) {
                     return el.estimated_time_arrival === id;
                 }).slice(0, limit));
                 indices.push(id);
               }
            }
            return out;
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
                        if (object.remarks_of_docs.includes("AS RED ")) {
                            object.remarks_of_docs = object.remarks_of_docs.replace(
                                "AS RED ",
                                ""
                            );
                        }
                        if (object.remarks_of_docs.includes("AS RED")) {
                            object.remarks_of_docs = object.remarks_of_docs.replace(
                                "AS RED",
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
        registry_confirm(id, value) {
            //value = this.trueOrFalse(value)

            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/save_registry_no_confirm`,
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
        refresh_e2m() {
            var self = this;
           
            self.list_of_BOL = [];
            self.loading_data = true;
            self.opened = []
            self.view_mode = true
            self.showprogress = true;
             //BLOCK THE PAGE SHOW PROCESSING MESSAGE
             $.blockUI({ message: '<h3> Processing ...</h3>' }); 
             $("#detail_obj").dataTable().fnDestroy()
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/refresh_master_arrival_update_e2m`
                )
                .then(response => {
                   
                    self.list_of_BOL = response.data;
                    setTimeout(function() {

                        self.refreshSelect2();
                        self.refreshPlugins();
                        self.loading_data = false
                        self.showprogress = false;
                        self.dataTableInit();

                    }, 500);

                    $.unblockUI();
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
        getRecord_e2m(take) {
            var self = this;
            let skip = this.list_of_BOL.length;
            self.loading_data = true 
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
                       
                    }
                )
                .then(response => {
                    response.data.forEach(value => {
                        this.list_of_BOL.push(value);
                    });
                    self.showprogress = false;
                    $("#detail_obj").dataTable().fnDestroy()
                    setTimeout(function() {
                        self.refreshSelect2();
                        self.refreshPlugins();
                        self.loading_data = false
                        self.dataTableInit();
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
        refreshPlugins() {
            var self = this;

            // $(".date_endorse")
            //     .datepicker({
            //         format: "yyyy-mm-dd",
            //         clearBtn: true,
            //     })
            //     .on("changeDate", e => {
            //         var selected_element = $(e.currentTarget);
            //         var select_val = selected_element.val();
            //         var id = parseInt(selected_element.prev().html());
            //         var object = _.find(this.list_of_BOL, { id: id });
            //         object.date_endorse = select_val;

            //         self.saveBlur(id, "date_endorse", select_val);
            //         /* Select the text field */
            //         if (selected_element.val() != "") {
            //             localStorage.copy_date = selected_element.val();
            //         }
            //     })
            //     .on("clearDate", e => {
            //         var selected_element = $(e.currentTarget);
            //         var select_val = selected_element.val();
            //         var id = parseInt(selected_element.prev().html());
            //         var object = _.find(this.list_of_BOL, { id: id });
            //         object.date_endorse = null;

            //         self.saveBlur(id, "date_endorse", null);
            //     });

            // $(".date_approve_ip")
            //     .datepicker({
            //         format: "yyyy-mm-dd",
            //         clearBtn: true,
            //     })
            //     .on("changeDate", e => {
            //         var selected_element = $(e.currentTarget);
            //         var select_val = selected_element.val();
            //         var id = parseInt(selected_element.prev().html());
            //         var object = _.find(this.list_of_BOL, { id: id });
            //         object.date_approve_ip = select_val;

            //         self.saveBlur(id, "date_approve_ip", select_val);
            //         /* Select the text field */
            //         if (selected_element.val() != "") {
            //             localStorage.copy_date = selected_element.val();
            //         }
            //     })
            //     .on("clearDate", e => {
            //         var selected_element = $(e.currentTarget);
            //         var select_val = selected_element.val();
            //         var id = parseInt(selected_element.prev().html());
            //         var object = _.find(this.list_of_BOL, { id: id });
            //         object.date_approve_ip = null;

            //         self.saveBlur(id, "date_approve_ip", null);
            //     });

            // $(".e2m")
            //     .datepicker({
            //         format: "yyyy-mm-dd",
            //         clearBtn: true,
            //     })
            //     .on("changeDate", e => {
            //         var selected_element = $(e.currentTarget);
            //         var select_val = selected_element.val();
            //         var id = parseInt(selected_element.prev().html());
            //         var object = _.find(this.list_of_BOL, { id: id });
            //         object.e2m = select_val;

            //         self.saveBlur(id, "e2m", select_val);
            //         /* Select the text field */
            //         if (selected_element.val() != "") {
            //             localStorage.copy_date = selected_element.val();
            //         }
            //     })
            //     .on("clearDate", e => {
            //         var selected_element = $(e.currentTarget);
            //         var select_val = selected_element.val();
            //         var id = parseInt(selected_element.prev().html());
            //         var object = _.find(this.list_of_BOL, { id: id });
            //         object.e2m = null;

            //         self.saveBlur(id, "e2m", null);
            //     });

            // $(".actual_process")
            //     .datepicker({
            //         format: "yyyy-mm-dd",
            //         clearBtn: true,
            //     })
            //     .on("changeDate", e => {
            //         var selected_element = $(e.currentTarget);
            //         var select_val = selected_element.val();
            //         var id = parseInt(selected_element.prev().html());
            //         var object = _.find(this.list_of_BOL, { id: id });
            //         object.actual_process = select_val;

            //         self.saveBlur(id, "actual_process", select_val);
            //         /* Select the text field */
            //         if (selected_element.val() != "") {
            //             localStorage.copy_date = selected_element.val();
            //         }
            //     })
            //     .on("clearDate", e => {
            //         var selected_element = $(e.currentTarget);
            //         var select_val = selected_element.val();
            //         var id = parseInt(selected_element.prev().html());
            //         var object = _.find(this.list_of_BOL, { id: id });
            //         object.actual_process = null;

            //         self.saveBlur(id, "actual_process", null);
            //     });


           
        },
        changeMode() {
            var self = this
            if (this.view_mode) {
                this.view_mode = false
            } else {
                this.view_mode = true
            }

            setTimeout(function () {
                self.refreshPlugins();
            }, 300)
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
        setDataTableETA(dateDocs){
            console.log(dateDocs)
            $('#datatable_ETA').val(dateDocs)
            $('#datatable_ETA').trigger('change')
        },
        dataTableInit(){
            // Setup - add a text input to each footer cell
            $('#detail_obj thead tr').clone(true).appendTo( '#example thead' );
            $('#detail_obj thead tr th').each( function (i) {
                var title = $(this).text();
                
                if(title.trim() == 'ETA'){
                    $(this).html( title + '<br><input type="text" id="datatable_ETA" placeholder="Search '+title+'" />' );
                }else{
                    $(this).html( title + '<br><input type="text" placeholder="Search '+title+'" />' );
                }
               

                $( 'input', this ).on( 'keyup change', function () {

                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }

                    if(this.value == '`'){
                        table
                        .column(i)
                        .search('^\s*$', true, false)
                        .draw();
                    }
                } );
            } );
        
            var table = $('#detail_obj').DataTable( {
            
                scrollY: "550px",
                scrollX: true,
                paging: false,
                bInfo : true, 
                ordering: false,   
                "dom": '<"top"i>rt<"bottom"flp><"clear">'
            
            

            
            } );


            $('.dataTables_scrollBody').on('scroll',function(e){
                        
                var leftscroll = e.currentTarget.scrollLeft;
                
                if(leftscroll == 0){
                    $('.stickycolumn').css({
                        'background-color': 'transparent ',
                        'color':'#676a6c',
                        'left':0,
                        'z-index':0,
                        '-webkit-box-shadow': '',
                        'box-shadow': '',
                    })
                    $('.stickycolumn1').css({
                        'background-color': 'transparent ',
                        'color':'#676a6c',
                        'left':0,
                        'z-index':0,
                        '-webkit-box-shadow': '',
                        'box-shadow': '',
                    })
                }else{
                    $('.stickycolumn').css({
                        // 'background': 'rgb(47,64,80,0.8)',
                        'background': '#f5f5f6',
                        '-webkit-box-shadow': '10px 0 5px -2px #888',
                        'box-shadow': '10px 0 5px -2px #888',
                        'color':'#676a6c',
                        'left':0,
                        'z-index':3
                    })
                    $('.stickycolumn1').css({
                        // 'background': 'rgb(47,64,80,0.8)',
                        'background': '#f5f5f6',
                        '-webkit-box-shadow': '10px 0 5px -2px #888',
                        'box-shadow': '10px 0 5px -2px #888',
                        'color':'#676a6c',
                        'left':179,
                        'z-index':3
                    })    
                    
                }
            })
        },
        CountingDays(discharge, pullout) {


            var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
            var firstDate = new Date(discharge);
            var secondDate = new Date(pullout);

            var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay)));

            return diffDays;

        },
        


    },
    mounted() {
        var self = this
        setTimeout(() => {

          self.dataTableInit()
        }, 500);

        self.refreshPlugins()
       
        self.first_three_dates =  _.take(_.uniq(_.map(self.list_of_BOL, 'estimated_time_arrival')),3)
       
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
       

    

    },
})