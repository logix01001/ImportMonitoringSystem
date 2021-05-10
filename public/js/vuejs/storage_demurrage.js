new Vue({
    el: '.vue_app_element',
    data: {
        list_of_BOL: list_of_BOL,
        list_of_BOL_Total: list_of_BOL_Total,
        connecting_vessels: connecting_vessels,
        shipping_lines: shipping_lines,
        truckers: truckers,
        view_mode: true,
        filter_search: '',
        search_cn: '',
        opened: [],
        search: false,
        dateToday: '',
        validity_storage: validity_storage,
        filter_validity_date: '',
        dismounted_cys: dismounted_cys,
        return_cys: return_cys,
        loading_data: false,
        numberofTake: 20,
        showprogress: false,
    },
    methods: {
        saveDelivery(index){
            var self = this
            var container_id = self.list_of_BOL[index].container_id;

            if(((self.list_of_BOL[index].pull_out != '' &&  self.list_of_BOL[index].pull_out != null) && (self.list_of_BOL[index].pull_out_time != '' &&  self.list_of_BOL[index].pull_out_time != null)) || (self.list_of_BOL[index].pull_out == '' && self.list_of_BOL[index].pull_out_time == '')){

                self.saveValidation(container_id, 'pull_out',  self.list_of_BOL[index].pull_out)
                self.saveValidation(container_id, 'pull_out_time', self.list_of_BOL[index].pull_out_time)
            }

        },
        getRecord_demurrage(take,role = null) {
            var self = this;
            let skip = this.list_of_BOL.length;
            //let role = role
            self.loading_data = true
            self.showprogress = true

            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
             $.blockUI({ message: '<h3> Processing ...</h3>' });
             $("#detail_obj").dataTable().fnDestroy()
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/get_storage_demmurage_all`,
                    {
                        skip: skip,
                        take: take,

                    }
                )
                .then(response => {

                    response.data.forEach(value => {
                        self.list_of_BOL.push(value);
                    });
                    self.loading_data = false
                    self.showprogress = false;

                    setTimeout(function() {

                        self.refreshPlugins();

                        self.dataTableInit();
                    }, 300);
                     //END BLOCK
                    $.unblockUI();
                })
                .catch(err => {
                    self.showprogress = false;
                     //END BLOCK
                     $.unblockUI();
                });
        },
        dataTableInit(){
            $('#detail_obj thead tr').clone(true).appendTo( '#example thead' );
            $('#detail_obj thead tr th').each( function (i) {
                var title = $(this).text();
                $(this).html( title + '<br><input type="text" placeholder="Search '+title+'" />' );

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
                        'z-index':0
                     })
                     $('.stickycolumn1').css({
                        // 'background': 'rgb(47,64,80,0.8)',
                         'background': '#f5f5f6',
                         '-webkit-box-shadow': '10px 0 5px -2px #888',
                        'box-shadow': '10px 0 5px -2px #888',
                        'color':'#676a6c',
                        'left':179,
                        'z-index':0
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
        refresh() {
            var self = this
            this.search = false
            self.loading_data = true;
            self.list_of_BOL = {}
            self.opened = []
            self.view_mode = true
            $.blockUI({ message: '<h3> Processing ...</h3>' });
            $("#detail_obj").dataTable().fnDestroy()
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/refresh_storage_validation`).then((response) => {

                self.list_of_BOL = response.data
                setTimeout(function () {


                    self.refreshPlugins();
                    self.dataTableInit();
                    $.unblockUI();

                }, 500)
            })
            self.loading_data = false;




        },
        isNumber: function (evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode > 31 && (charCode < 48 || charCode > 57))) {
                evt.preventDefault();;
            } else {
                return true;
            }
        },
        xray(id, value) {

            //value = this.trueOrFalse(value)

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_xray_confirm`,
                {
                    id: id,
                    value: value

                }).then(function (res) {
                    console.log(res.data)
                }).catch(function (err) {
                    console.log(err)
                })
        },
        filterValidityStorage() {
            var self = this
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/storage_demurrage_filter_validity_storage`,
                {

                    validity_storage: self.filter_validity_date

                }).then(function (res) {

                    self.list_of_BOL = {};
                    self.list_of_BOL = res.data;

                    $("#detail_obj").dataTable().fnDestroy()

                    setTimeout(function () {
                        self.refreshPlugins();
                        self.dataTableInit();

                    }, 100)

                }).catch(function (err) {
                    console.log(err)
                })
        },
        filterSearch() {

            var self = this
            if (self.filter_search != '') {
                $("#detail_obj").dataTable().fnDestroy()
                axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/storage_demurrage_search`,
                    {

                        category: self.filter_search,
                        search: self.search_cn

                    }).then(function (res) {

                        self.list_of_BOL = {};
                        self.list_of_BOL = res.data;

                        setTimeout(function () {
                            self.refreshPlugins();
                            self.dataTableInit();
                        }, 100)

                    }).catch(function (err) {
                        console.log(err)
                    })

            }


        },
        toggle(id) {
            var self = this;
            const index = this.opened.indexOf(id);

            if (index > -1) {
                this.opened.splice(index, 1)
            } else {
                this.opened.push(id)
            }

            setTimeout(function () {
                self.refreshPlugins();
            }, 300)



        },
        trueOrFalse(value) {

            if (value == true) {
                return 1
            } else {
                return 0
            }



        },
        saveValidation(id, column, discharge_date) {
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_validation_revalidation_date`,
                {
                    id: id,
                    columnName: column,
                    value: discharge_date

                }).then(function (res) {
                    console.log(res.data)
                }).catch(function (err) {
                    console.log(err)
                })
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
        refreshPlugins() {
            var self = this

            $('.trucker').select2({
                width: '90%',
                tags: true,
                insertTag: function (data, tag) {
                    tag.text = "New Trucker ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {

                    //    this.selectedModel_Number = $('#modelhouselist').val()
                    //    this.getSelectedModelHouse()
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val().toUpperCase();


                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].trucker = select_val
                    var container_id = self.list_of_BOL[list_index].container_id;


                    var I = _.findIndex(self.truckers, function (o) { return o.trucker == select_val; });

                    if (I == -1) {
                        self.truckers.push({ trucker: select_val });
                        console.log(self.truckers)
                    }
                    self.saveValidation(container_id, 'trucker', select_val)


            })

            // $('.dismounted_cy').select2({
            //     width: '90%',
            //     tags: true,
            //     insertTag: function (data, tag) {
            //         tag.text = "New CY ? Click Here To Add";
            //         data.push(tag);
            //     },
            //     escapeMarkup: function (markup) { return markup; }

            // }).on(
            //     "select2:select", (e) => {

            //         //    this.selectedModel_Number = $('#modelhouselist').val()
            //         //    this.getSelectedModelHouse()
            //         var selected_element = $(e.currentTarget);
            //         var select_val = selected_element.val().toUpperCase();


            //         var list_index = selected_element.data('index');
            //         var index_container = selected_element.data('index_container');

            //         self.list_of_BOL[list_index].container_numbers[index_container].dismounted_cy = select_val
            //         var container_id = self.list_of_BOL[list_index].container_numbers[index_container].id;


            //         var I = _.findIndex(self.dismounted_cys, function (o) { return o.dismounted_cy == select_val; });

            //         if (I == -1) {
            //             self.dismounted_cys.push({ dismounted_cy: select_val });

            //         }
            //         self.saveValidation(container_id, 'dismounted_cy', select_val)


            //     })

            // $('.return_cy').select2({
            //     width: '90%',
            //     tags: true,
            //     insertTag: function (data, tag) {
            //         tag.text = "New CY ? Click Here To Add";
            //         data.push(tag);
            //     },
            //     escapeMarkup: function (markup) { return markup; }

            // }).on(
            //     "select2:select", (e) => {

            //         //    this.selectedModel_Number = $('#modelhouselist').val()
            //         //    this.getSelectedModelHouse()
            //         var selected_element = $(e.currentTarget);
            //         var select_val = selected_element.val().toUpperCase();


            //         var list_index = selected_element.data('index');
            //         var index_container = selected_element.data('index_container');

            //         self.list_of_BOL[list_index].container_numbers[index_container].return_cy = select_val
            //         var container_id = self.list_of_BOL[list_index].container_numbers[index_container].id;


            //         var I = _.findIndex(self.return_cys, function (o) { return o.return_cy == select_val; });

            //         if (I == -1) {
            //             self.return_cys.push({ return_cy: select_val });

            //         }
            //         self.saveValidation(container_id, 'return_cy', select_val)


            //     })

            $('.validity_storage').datepicker({
                format: "yyyy-mm-dd", clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].validity_storage = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;

                    self.saveValidation(container_id, 'validity_storage', select_val);

                }).on("clearDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].container_numbers[index_container].validity_storage = select_val

                    var container_id = self.list_of_BOL[list_index].container_numbers[index_container].id;
                    self.list_of_BOL[list_index].container_numbers[index_container].revalidity_storage = null
                    self.list_of_BOL[list_index].container_numbers[index_container].revalidity_remarks = null

                    self.saveValidation(container_id, 'validity_storage', select_val);
                    self.saveValidation(container_id, 'revalidity_storage', "");
                    self.saveValidation(container_id, 'revalidity_remarks', "");



                });

            $('.pull_out').datepicker({
                format: "yyyy-mm-dd", clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].pull_out = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;

                    //CHANGE DUE REQUIRE TIME AND DATE
                    self.saveDelivery(list_index)

                    //self.saveValidation(container_id, 'pull_out', select_val);

                }).on("clearDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].pull_out = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;

                    self.saveDelivery(list_index)
                    //self.saveValidation(container_id, 'pull_out', select_val);



                });


                $('.detention_validity').datepicker({
                    format: "yyyy-mm-dd", clearBtn: true
                }).on(
                    "changeDate", (e) => {

                        console.log($(e.currentTarget))
                        var selected_element = $(e.currentTarget);
                        var select_val = selected_element.val();

                        var list_index = selected_element.data('index');
                        var index_container = selected_element.data('index_container');

                        self.list_of_BOL[list_index].detention_validity = select_val
                        var container_id = self.list_of_BOL[list_index].container_id;
                        self.saveValidation(container_id, 'detention_validity', select_val);

                    }).on("clearDate", (e) => {
                        var selected_element = $(e.currentTarget);
                        var select_val = selected_element.val();

                        var list_index = selected_element.data('index');
                        var index_container = selected_element.data('index_container');

                        self.list_of_BOL[list_index].detention_validity = select_val

                        var container_id = self.list_of_BOL[list_index].container_id;

                        self.saveValidation(container_id, 'detention_validity', select_val);

                    });



            $('.safe_keep').datepicker({
                format: "yyyy-mm-dd", clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].safe_keep = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;

                    self.saveValidation(container_id, 'safe_keep', select_val);

                }).on("clearDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].safe_keep = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;

                    self.saveValidation(container_id, 'safe_keep', select_val);



                });

                $('.dispatched_date').datepicker({
                    format: "yyyy-mm-dd", clearBtn: true
                }).on(
                    "changeDate", (e) => {

                        var selected_element = $(e.currentTarget);
                        var select_val = selected_element.val();

                        var list_index = selected_element.data('index');
                        var index_container = selected_element.data('index_container');

                        self.list_of_BOL[list_index].dispatched_date = select_val

                        var container_id = self.list_of_BOL[list_index].container_id;
                        //console.log( self.list_of_BOL[list_index].bl_no,list_index,container_id)
                        self.saveValidation(container_id, 'dispatched_date', select_val);

                    }).on("clearDate", (e) => {
                        var selected_element = $(e.currentTarget);
                        var select_val = selected_element.val();

                        var list_index = selected_element.data('index');
                        var index_container = selected_element.data('index_container');

                        self.list_of_BOL[list_index].dispatched_date = select_val

                        var container_id = self.list_of_BOL[list_index].container_id;

                        self.saveValidation(container_id, 'dispatched_date', select_val);



                    });


            $('.dismounted_date').datepicker({
                format: "yyyy-mm-dd", clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].dismounted_date = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;

                    self.saveValidation(container_id, 'dismounted_date', select_val);

                }).on("clearDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].dismounted_date = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;

                    self.saveValidation(container_id, 'dismounted_date', select_val);



                });

            $('.unload').datepicker({
                format: "yyyy-mm-dd", clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].unload = select_val

                    var container_id = self.list_of_BOL[list_index].id;

                    self.saveValidation(container_id, 'unload', select_val);

                }).on("clearDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].unload = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;

                    self.saveValidation(container_id, 'unload', select_val);



                });
            $('.return_date').datepicker({
                format: "yyyy-mm-dd", clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].return_date = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;

                    self.saveValidation(container_id, 'return_date', select_val);

                }).on("clearDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].return_date = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;

                    self.saveValidation(container_id, 'return_date', select_val);



                });

            $('.validity_demurrage').datepicker({
                format: "yyyy-mm-dd",
                clearBtn: true
            }).on(
                "changeDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');



                    self.list_of_BOL[list_index].validity_demurrage = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;


                    self.saveValidation(container_id, 'validity_demurrage', select_val);



                });
            $('.revalidity_storage').datepicker({
                format: "yyyy-mm-dd", clearBtn: true
            }).on(
                "changeDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');



                    self.list_of_BOL[list_index].revalidity_storage = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;


                    self.saveValidation(container_id, 'revalidity_storage', select_val);



                });

            $('.revalidity_demurrage').datepicker({
                format: "yyyy-mm-dd", clearBtn: true
            }).on(
                "changeDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var list_index = selected_element.data('index');
                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[list_index].revalidity_demurrage = select_val

                    var container_id = self.list_of_BOL[list_index].container_id;

                    self.saveValidation(container_id, 'revalidity_demurrage', select_val);


                });




        },
        formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        },
        compareDate(date1, date2, date3 = new Date()) {
            a = new Date(date1);
            b = new Date(date2);
            c = new Date(date3);

            if (((a.getTime() === b.getTime() || a > b) && date2 != null)) {
                return true
            }

        },



    },
    mounted() {
        var self = this

        setTimeout(() => {
            // Setup - add a text input to each footer cell
            $('#detail_obj thead tr').clone(true).appendTo( '#example thead' );
            $('#detail_obj thead tr th').each( function (i) {
                var title = $(this).text();
                $(this).html( title + '<br><input type="text" placeholder="Search '+title+'" />' );

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
                        'z-index':3,
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
        }, 500);

        self.refreshPlugins()

        this.dateToday = this.formatDate(new Date());





    },
})
