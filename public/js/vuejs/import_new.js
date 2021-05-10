

Vue.config.ignoredElements = ['bl_with_split']

new Vue({
    el: '.vue_app_element',
    data: {
        logged_in: logged_in,
        selectedIndexViewSplit : 0,
        bl_with_split: bl_with_split,
        factories: factories,
        container_types: container_types,
        commodities: commodities,
        suppliers : suppliers,
        vessels:vessels,
        connecting_vessels:connecting_vessels,
        shipping_lines:shipping_lines,
        forwarders:forwarders,
        brokers:brokers,
        pol:pol,
        countries: countries,
        list_of_bl_no_for_split : list_of_bl_no_for_split,
        split_lists: split_lists,
        newBOL: {
            factory:'',
            bl_no: '',
            invoice_no:'',
            supplier:'',
            commodity:[],
            vessel: '',
            shipping_line: '',
            forwarder: '',
            broker: '',
            pol: '',
            country: '',
            pod: '',
            volume: '',
            shipping_docs: '',
            shipping_docs_time: '',
            processing_date: '',
            estimated_time_departure: '',
            estimated_time_arrival: '',
            incoterm: ''
        },
        number_container: 0,
        list_container: [],
        list_container_required: false,
        list_of_empty_fields : [],
        sameContainer: false,
        distinct_container_type: [],
        //------------------------------------*/
        editBOL:{
            factory:'',
            bl_no: '',
            invoice_no:'',
            supplier:'',
            commodity:[],
            vessel: '',
            shipping_line: '',
            forwarder: '',
            broker: '',
            pol: '',
            country: '',
            pod: '',
            volume: '',
            shipping_docs: '',
            shipping_docs_time: '',
            processing_date: '',
            estimated_time_departure: '',
            estimated_time_arrival: '',
            incoterm: '',
            edit_list_container: []
       },
       edit_number_container: 0,
       edit_list_container: [],
       edit_list_container_required: false,
       edit_list_of_empty_fields : [],
       edit_sameContainer: false,
       edit_distinct_container_type: [],
       noRecord : true,
       firstLoad: true,
       newRecord : false,

       transferBLNo: '',
       transferNewBLNo: '',
       ready_transfer: false,
       transfer_not_exit_bl: false,
       container_code_invalid_new: 0,
       container_code_invalid_edit: 0,
       selectedIndexSplitUpdate: -1,

    },
    computed: {
        // check_invalid_container_code_new(){

        //     return $('.container_code_invalid_new').length;
        // },
        number_of_container_type_new() {
            if(this.list_container.length > 0){
                var total = this.list_container.length;
                var count = 0;
                this.list_container.forEach((container)=>{
                    if(container.container_type == ''){
                        count++;
                    }
                })
                if(count == 0){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        },
        number_of_container_type_edit() {
            if(this.editBOL.edit_list_container.length > 0){
                var total = this.editBOL.edit_list_container.length;
                var count = 0;
                this.editBOL.edit_list_container.forEach((container)=>{
                    if(container.container_type == '' || container.container_type == null){

                        count++;

                    }
					console.log(container.container_type, count)
                })
                if(count == 0){
                    return true
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    },
    methods:{
        updatesplitcontainer(index,key){
            this.split_lists[index][key] = this.split_lists[index].difference[key]

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_validation_revalidation_date`,
            {
                id: this.split_lists[index].cid,
                columnName: key,
                value: this.split_lists[index].difference[key]

            }).then(function (res) {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };
                toastr.success('Save Completed', 'Successfully update ' + key);
            }).catch(function (err) {
                console.log(err)
            })


        },
        splitlistsindex(i){
            this.selectedIndexSplitUpdate = i
        },
        checkInvalidCodeNew(){
            var self = this
            self.container_code_invalid_new = 0
            self.list_container.forEach(function(obj){
                if(obj.container_number.search('_') > -1 ){
                    self.container_code_invalid_new++
                }
            })
        },
        checkInvalidCodeEdit(){
            var self = this
            self.container_code_invalid_edit = 0
            self.editBOL.edit_list_container.forEach(function(obj){
                if(obj.container_number.search('_') > -1 ){
                    self.container_code_invalid_edit++
                }
            })
        },
        searchExistForTransfer(){
            var self = this
            self.ready_transfer = false
            self.transfer_not_exit_bl = false
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/search_bl_exist`,
            {
                'bl_no' : self.transferBLNo.trim()

            }).then((response)=>{


                if(  response.data == 1){
                    self.ready_transfer = true
                }else{
                    self.transfer_not_exit_bl = true
                }

            })

        },
        updateTransferBL(){
            var self = this

            $.confirm({
                title: 'Save!',
                content: 'Are you sure you want to change this data?',
                buttons: {
                    confirm: function () {

                        axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/transfer_bl_no_edit`,
                        {
                            'bl_no' : self.transferBLNo.trim(),
                            'new_bl_no' : self.transferNewBLNo.trim(),
                            'edited_by' : self.logged_in,
                        }).then((response)=>{

                            $.alert({
                                title: 'IMS Message',
                                content: `Success Saved! from ${self.transferBLNo.trim()} to ${self.transferNewBLNo.trim()}`,
                            });

                            self.ready_transfer = false
                            self.transferBLNo = ''
                            self.transferNewBLNo = ''
                            self.transfer_not_exit_bl = false

                        })

                    },
                    cancel: {
                        text: 'cancel',
                        btnClass: 'btn-warning'

                    }
                }
            });





        },
        modalSplit(i){
            var self = this
            self.selectedIndexViewSplit = i
            $('#myModal').modal().show();
        },
        searchDate(date){
            var self = this
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/filter_split_checking`,
            {

                    date_filter : date,

            }).then(function(res){

                self.selectedIndexViewSplit = 0;

                self.bl_with_split = res.data


            }).catch(function(err){
                    console.log(err)
            })
        },
        saveSplitQuantity(id,qty){
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/split_save_qty`,
            {
                'id' : id,
                'qty' : parseInt(qty),

            }).then((response)=>{
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };
                toastr.success('Save Completed', 'Successfully Save');
            }).catch((err)=>{
                $.alert({
                    title: 'Failed to saved!',
                    content: err,
                });
            })
        },
        isNumber: function(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode > 31 && (charCode < 48 || charCode > 57))) {
              evt.preventDefault();;
            } else {
              return true;
            }
        },
        searchEdit(){
            var self = this
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/edit_search_bl`,
            {
                'bl_no' : self.editBOL.bl_no

            }).then((response)=>{
                self.noRecord = true



                //console.log(response.data.length)
                self.firstLoad = false
                if(response.data.length == 1){
                    self.noRecord = false
                    self.editBOL = response.data[0]
					//console.log(self.editBOL.edit_list_container

                    setTimeout(function(){
                        $('.container_register_Edit').inputmask("AAAA9999999",{ "clearIncomplete": true });  //static mask
                        self.refreshpluginEdit()
                    },
                    200)
                }else{
                    self.editBOL = {
                        factory:'',
                        bl_no: '',
                        invoice_no:'',
                        supplier:'',
                        commodity:[],
                        vessel: '',
                        shipping_line: '',
                        forwarder: '',
                        broker: '',
                        pol: '',
                        country: '',
                        pod: '',
                        volume: '',
                        shipping_docs: '',
                        shipping_docs_time: '',
                        processing_date: '',
                        estimated_time_departure: '',
                        estimated_time_arrival: '',
                        incoterm: '',
                        edit_list_container: []
                   }
                }




                var result = _(this.editBOL.edit_list_container)
                .countBy('container_type')
                .map((count, container_type) => ({ container_type, count }))
                .value();

                this.edit_distinct_container_type = result
				 setTimeout(() => {

					self.editBOL.edit_list_container.forEach((bl)=>{



						if(bl.hasOwnProperty('split_bl_no_fk')){
							bl.split_bl_no_fk.forEach((split)=>{


								if(!_.find(self.list_of_bl_no_for_split, {'bl_no': split})) {
									self.list_of_bl_no_for_split.push({'bl_no': split});
									console.log(split);
								  }

								  console.log(split);

							})
						}


					})

				}, 500);


                setTimeout(() => {
                    $('.split_bl_edit').select2({
                        placeholder: "Select multiple BL SPLIT NO",
                        width: '100%',
                        tags: true,
                        insertTag: function(data, tag){
                            tag.text = "Split BL ? Click Here To Add";
                            data.push(tag);
                        },
                        escapeMarkup: function (markup) { return markup; }

                    }).on(
                        "select2:select", (e) => {
                        //    this.selectedModel_Number = $('#modelhouselist').val()
                        //    this.getSelectedModelHouse()
                        var selected_element = $(e.currentTarget);
                        var index = selected_element.data('index');
                        self.editBOL.edit_list_container[index].split_bl_no_fk = [];
                        self.editBOL.edit_list_container[index].split_bl_no_fk = selected_element.val();


                    })
                    .on(
                        "select2:unselect", (e) => {
                        //    this.selectedModel_Number = $('#modelhouselist').val()
                        //    this.getSelectedModelHouse()
                        var selected_element = $(e.currentTarget);
                        var index = selected_element.data('index');
                        self.editBOL.edit_list_container[index].split_bl_no_fk = [];

                        if(selected_element.val() != null){
                            self.editBOL.edit_list_container[index].split_bl_no_fk = selected_element.val();
                        }

                    })
                }, 1000);


            })

        },
        searchExist(){
            var self = this
            self.newRecord = false
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/search_bl_exist`,
            {
                'bl_no' : self.newBOL.bl_no

            }).then((response)=>{

                if(  response.data == 0){
                    self.newRecord = true



                }else{

                    $.alert({
                        title: 'Info!',
                        content: 'This BL Number is Already exist',
                    });

                }

            })

        },
        check_same_container_type(newvalue){
            var self = this
            if(this.sameContainer){
                _.each(self.list_container, function(obj, key) { obj['container_type'] = newvalue })
            }
            var result = _(this.list_container)
                    .countBy('container_type')
                    .map((count, container_type) => ({ container_type, count }))
                    .value();

           this.distinct_container_type = result
        },
        check_same_container_type_edit(newvalue){
            var self = this
            if(this.edit_sameContainer){
                _.each(self.editBOL.edit_list_container, function(obj, key) { obj['container_type'] = newvalue })
            }
            var result = _(this.editBOL.edit_list_container)
            .countBy('container_type')
            .map((count, container_type) => ({ container_type, count }))
            .value();

            this.edit_distinct_container_type = result
        },
        feedback: function(element) {
			if(element == null)
				return '';
            return element.trim().length == 0 ? 'form-group' : 'form-group has-success';

        },
        saveObj(){
            var self = this
            var required = 0;


            self.list_container_required = false
            if(self.list_container.length == 0){
                self.list_container_required = true
                required++;
            }



            if(required == 0){
                $.confirm({
                    title: 'Save!',
                    content: 'Are you sure you want to save this data?',
                    buttons: {
                        confirm: function () {
                            var checkUserExist = 0

                            $('#saveNewButton').prop('disabled',true);

                            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save`,
                            {
                                'BOL' : self.newBOL,
                                'Containers' : self.list_container

                            }).then((response)=>{
                                $.alert({
                                    title: 'Info!',
                                    content: 'Success Saved!',
                                });
                                // setTimeout(function(){
                                //     window.location.reload(1);
                                //  }, 3000);


                            }).catch((error)=>{

                                $.alert({
                                    title: 'Error!',
                                    content: `System Error. please copy this referrence error code  ${error.response.data.message} for us to determine the error of the system.`,
                                });
                            })



                        },
                        cancel: {
                            text: 'cancel',
                            btnClass: 'btn-warning'

                        }
                    }
                });
            }



        },
        updateObj(){
            var self = this
            var required = 0;


            self.editBOL.edit_list_container_required = false
            if(self.editBOL.edit_list_container_required.length == 0){
                self.list_container_required = true
                required++;
            }
            if(required == 0){
                $.confirm({
                    title: 'Save!',
                    content: 'Are you sure you want to save this data?',
                    buttons: {
                        confirm: function () {
                            var checkUserExist = 0
                           // self.editBOL.edit_list_container = lodash.pick(self.editBOL.edit_list_container, ['id', 'container_number', 'container_type','quantity']);


                            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/edit_update`,
                            {
                                'BOL' : self.editBOL,

                            }).then((response)=>{
                                $.alert({
                                    title: 'Info!',
                                    content: 'Success Saved!',
                                });

                                setTimeout(function(){
                                    window.location.reload(1);
                                 }, 3000);

                            })

                        },
                        cancel: {
                            text: 'cancel',
                            btnClass: 'btn-warning'

                        }
                    }
                });
            }



        },
        remove_container_row(index)
       {
            this.list_container.splice(index,1)
            var total =  parseInt(this.list_container.length)

            if(total == 0){
                this.newBOL.volume = ''
            }
            else if( total == 1){
                this.newBOL.volume = total + ' CONTAINER'
            }else{
                this.newBOL.volume = total + ' CONTAINERS'
            }

            var result = _(this.list_container)
            .countBy('container_type')
            .map((count, container_type) => ({ container_type, count }))
            .value();

            this.distinct_container_type = result
       },
       remove_container_row_edit(index)
       {

            // var obj = this.editBOL.edit_list_container[index];
            // if(obj.id == ''){
            //  this.editBOL.edit_list_container.splice(index,1)
            // }else{
            //     obj.remove = true;
            // }

            this.editBOL.edit_list_container.splice(index,1)

            var total =  parseInt(this.editBOL.edit_list_container.length)

            if(total == 0){
                this.editBOL.volume = ''
            }
            else if( total == 1){
                this.editBOL.volume = total + ' CONTAINER'
            }else{
                this.editBOL.volume = total + ' CONTAINERS'
            }

            var result = _(this.editBOL.edit_list_container)
            .countBy('container_type')
            .map((count, container_type) => ({ container_type, count }))
            .value();

            this.edit_distinct_container_type = result
       },
        generateContainer(){
            var self = this
            var total =  parseInt(this.list_container.length) + parseInt(this.number_container)

            for(var i = 1; i <= this.number_container; i++){
                var obj =
                {
                    container_number: '',
                    container_type: '',
                    split_bl_no_fk: [],
                    quantity: 1,
                }
                this.list_container.push(obj)
            }



            if( total == 1){
                this.newBOL.volume = total + ' CONTAINER'
            }else{
                this.newBOL.volume = total + ' CONTAINERS'
            }
            this.number_container = 0

            setTimeout(() => {
                $('.container_register').inputmask("AAAA9999999",{ "clearIncomplete": true });  //static mask
                $('.split_bl').select2({
                    placeholder: "Select multiple BL SPLIT NO",
                    width: '100%',
                    tags: true,
                    insertTag: function(data, tag){
                        tag.text = "Split BL ? Click Here To Add";
                        data.push(tag);
                    },
                    escapeMarkup: function (markup) { return markup; }

                }).on(
                    "select2:select", (e) => {
                    //    this.selectedModel_Number = $('#modelhouselist').val()
                    //    this.getSelectedModelHouse()
                    var selected_element = $(e.currentTarget);
                    var index = selected_element.data('index');
                    self.list_container[index].split_bl_no_fk = [];
                    self.list_container[index].split_bl_no_fk = selected_element.val();


                })
                .on(
                    "select2:unselect", (e) => {
                    //    this.selectedModel_Number = $('#modelhouselist').val()
                    //    this.getSelectedModelHouse()
                    var selected_element = $(e.currentTarget);
                    var index = selected_element.data('index');
                    self.list_container[index].split_bl_no_fk = [];

                    if(selected_element.val() != null){
                        self.list_container[index].split_bl_no_fk = selected_element.val();
                    }

                })
            }, 300);



        },
        generateContainer_edit(){
            var self = this;
            var total =  parseInt(this.editBOL.edit_list_container.length) + parseInt(this.edit_number_container)

            for(var i = 1; i <= this.edit_number_container; i++){


                var obj =
                {
                    id: '',
                    container_number: '',
                    container_type: '',
                    split_bl_no_fk: [],
                    quantity: 1,
                }



                this.editBOL.edit_list_container.push(obj)
            }



            if( total == 1){
                this.editBOL.volume = total + ' CONTAINER'
            }else{
                this.editBOL.volume = total + ' CONTAINERS'
            }
            this.edit_number_container = 0

            setTimeout(() => {
                $('.container_register_Edit').inputmask("AAAA9999999",{ "clearIncomplete": true });  //static mask
                $('.split_bl_edit').select2({
                    placeholder: "Select multiple BL SPLIT NO",
                    width: '100%',
                    tags: true,
                    insertTag: function(data, tag){
                        tag.text = "Split BL ? Click Here To Add";
                        data.push(tag);
                    },
                    escapeMarkup: function (markup) { return markup; }

                }).on(
                    "select2:select", (e) => {
                    //    this.selectedModel_Number = $('#modelhouselist').val()
                    //    this.getSelectedModelHouse()
                    var selected_element = $(e.currentTarget);
                    var index = selected_element.data('index');
                    console.log(index);
                    self.editBOL.edit_list_container[index].split_bl_no_fk = [];
                    self.editBOL.edit_list_container[index].split_bl_no_fk = selected_element.val();


                })
                .on(
                    "select2:unselect", (e) => {
                    //    this.selectedModel_Number = $('#modelhouselist').val()
                    //    this.getSelectedModelHouse()
                    var selected_element = $(e.currentTarget);
                    var index = selected_element.data('index');
                    self.editBOL.edit_list_container[index].split_bl_no_fk = [];

                    if(selected_element.val() != null){
                        self.editBOL.edit_list_container[index].split_bl_no_fk = selected_element.val();
                    }

                })
            }, 300);
        },
        refreshpluginEdit(){
            var self = this
            $("#editBOL_bl_ETD").datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                    "changeDate", () => {
                        self.editBOL.estimated_time_departure = $('#editBOL_bl_ETD').val()

            });

            $("#editBOL_bl_ETA").datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                    "changeDate", () => {
                        self.editBOL.estimated_time_arrival = $('#editBOL_bl_ETA').val()

            });

            $('#editBOL_bl_factory').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(

                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.factory = selected_element.val();

                console.log( selected_element.val() )

            })

            $('#editBOL_bl_supplier').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.supplier = selected_element.val();

            })

            $('#editBOL_bl_commodity').select2({
                placeholder: "Select multiple commodity",
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Commodity ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.commodity = [];
                self.editBOL.commodity = selected_element.val();


            })
            .on(
                "select2:unselect", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.commodity = [];
                if(selected_element.val() != null){
                    self.editBOL.commodity = selected_element.val();
                }

            })




            $('#editBOL_bl_vessel').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.vessel = selected_element.val();

            })


            $('#editBOL_bl_shipping_Line').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.shipping_line = selected_element.val();

            })

            $('#editBOL_bl_forwarder').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.forwarder = selected_element.val();

            })

            $('#editBOL_bl_broker').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.broker = selected_element.val();

            })

            $('#editBOL_bl_pol').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.pol = selected_element.val();

            })

            $('#editBOL_bl_pod').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.pod = selected_element.val();

            })


            $('#editBOL_bl_country').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.country = selected_element.val();

            })

            $('#editBOL_bl_country').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.editBOL.country = selected_element.val();

            })

            $("#edit_bl_processing_date").datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                    "changeDate", () => {
                        self.editBOL.processing_date = $('#edit_bl_processing_date').val()

            });

            $("#edit_bl_shipping_docs_date").datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                    "changeDate", () => {
                        self.editBOL.shipping_docs = $('#edit_bl_shipping_docs_date').val()

            });

        }
    },
    mounted(){


        var self = this
        //$(":input").inputmask();
        $(document).ready(function(){



            $("#bl_shipping_docs_date").datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                    "changeDate", () => {
                        self.newBOL.shipping_docs = $('#bl_shipping_docs_date').val()

            });

            $("#bl_processing_date").datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                    "changeDate", () => {
                        self.newBOL.processing_date = $('#bl_processing_date').val()

            });



            $("#bl_ETD").datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                    "changeDate", () => {
                        self.newBOL.estimated_time_departure = $('#bl_ETD').val()

            });



            $("#bl_ETA").datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                    "changeDate", () => {
                        self.newBOL.estimated_time_arrival = $('#bl_ETA').val()

            });





            $('#bl_factory').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.newBOL.factory = selected_element.val();

            })



            $('#bl_supplier').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.newBOL.supplier = selected_element.val();

            })


            $('#bl_commodity').select2({
                placeholder: "Select multiple commodity",
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Commodity ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.newBOL.commodity = [];
                self.newBOL.commodity = selected_element.val();


            })
            .on(
                "select2:unselect", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.newBOL.commodity = [];
                if(selected_element.val() != null){
                    self.newBOL.commodity = selected_element.val();
                }

            })


            $('#bl_vessel').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.newBOL.vessel = selected_element.val();

            })




            $('#bl_shipping_Line').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.newBOL.shipping_line = selected_element.val();

            })



            $('#bl_forwarder').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.newBOL.forwarder = selected_element.val();

            })



            $('#bl_broker').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.newBOL.broker = selected_element.val();

            })



            $('#bl_pol').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.newBOL.pol = selected_element.val();

            })




            $('#bl_country').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.newBOL.country = selected_element.val();

            })


            $('#bl_pod').select2({
                width: '100%',
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New Item ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {
                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                self.newBOL.pod = selected_element.val();

            })
        })

        $("#date_month").datepicker( {
            format: "yyyy-mm",
            startView: "months",
            minViewMode: "months"
        }).on(
            "changeDate", (e) => {


                this.searchDate($("#date_month").val() )

        });



    }
})
