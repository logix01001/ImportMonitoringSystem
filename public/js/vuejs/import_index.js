//Vue.config.devtools = true
new Vue({
    el: '.vue_app_element',
    data: {
        selected_factory: selected_factory,
        factories: factories,
        list_of_BOL: list_of_BOL,
        totalRecord: totalRecord,
        connecting_vessels: connecting_vessels,
        dismounted_cys: dismounted_cys,
        return_cys: return_cys,
        truckers: truckers,
        dateToday : '',
        search_bl_no : '',
        filter_month : '',
        filter_search :'BL',
        search:false,
        newBOL: {},
        selectedBOL: {},
        selectedIndex: 0,
        list_container: [],
        firstLoad : true,
        opened: [],
        numberofTake: 20,
        view_mode: true,
        showprogress: false,
        showingRecords : true,
        showingE2m : false,
        showingGatepass: false,
        showingOnHand: false,
        showingDelivered: false,
        showingCompleted: false,
        showingSouth: false,
        showingNorth: false,
        showModal: false,

        showingTransit: false,
        showingArrived: false,
        showingBerthed: false,
        showingDischarge: false,
        showingOnProcess: false,
        showingWithGatepass: false,
        showingPullOut: false,
        showingUnload: false,
        showingReturn: false,


        legends:[
            {
                icon: 'fa fa-ship text-legend-light-blue',
                title: 'E2m Arrival Discharge'
            },
            {
                icon: 'fa fa-ticket-alt  text-info',
                title: 'Gatepass'
            },
            {
                icon: 'fa fa-shipping-fast  text-warning',
                title: 'Validation and Container Movement'
            },
            {
                icon: 'fa fa-check text-legend-light-success',
                title: 'Completed'
            }
        ]
    },
    mutations: {
        count_listBOL (state) {
          // mutate state
            return  state.count++
        }
    },
    computed:{
        // filteredList() {
        //     return this.list_of_BOL.filter(post => {
        //         return post.bl_no.toLowerCase().includes(this.header_bl.toLowerCase())
        //     })
        // },
        listForTransit: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.estimated_time_departure != null
            })
        },
        listForArrived: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.actual_time_arrival != null
            })
        },
        listForBerthed: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.actual_berthing_date != null
            })
        },
        listForDischarge: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.total_container_discharged == i.container_numbers.length
            })
        },
        listForOnProcess: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.actual_process != null
            })
        },
        listForWithGatepass: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.total_with_gatepass == i.container_numbers.length
            })
        },
        listForPullOut: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.total_container_pullout == i.container_numbers.length
            })
        },
        listForUnload: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.total_container_unload == i.container_numbers.length
            })
        },
        listForReturn: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.total_round_use == i.container_numbers.length
            })
        },
        listForE2M: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.e2m === null
            })
        },
        listForGatepass: function() {
            let obj = this.list_of_BOL
            // return obj.filter(function(i) {
            //   return i.e2m != null && i.actual_gatepass == null
            // })
            return obj.filter(function(i) {
                return i.actual_process != null && (i.total_with_gatepass != i.container_numbers.length)
            })
            // this.list_of_BOL,
            //     ({ actual_process,total_with_gatepass,container_numbers }) => Number(actual_process != null && (total_with_gatepass != container_numbers.length))
            // );
        },
        listForOnHand: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
                return i.total_container_onhand > 0
            })
        },
        listForDelivered: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.total_container_delivered > 0
            })
        },
        listForCompleted: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return (i.e2m != null && i.actual_gatepass != null) && i.total_round_use == i.container_numbers.length
            })
        },
        listForSouth: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
                return i.pod == "SOUTH"
            })
        },
        listForNorth: function() {
            let obj = this.list_of_BOL
            return obj.filter(function(i) {
              return i.pod == "NORTH"
            })
        },
        totalForTransit : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ estimated_time_departure }) => Number(estimated_time_departure != null)
              );
              return count
        },
        totalForArrived : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ actual_time_arrival }) => Number(actual_time_arrival != null)
              );
              return count
        },
        totalForBerthed : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ actual_berthing_date }) => Number(actual_berthing_date != null)
              );
              return count
        },
        totalForDischarge : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ total_container_discharged , container_numbers}) => Number(total_container_discharged == container_numbers.length)
              );
              return count
        },
        totalForOnProcess : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ actual_process}) => Number(actual_process != null)
              );
              return count
        },
        totalForWithGatepass : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ total_with_gatepass , container_numbers}) => Number(total_with_gatepass == container_numbers.length)
              );
              return count
        },
        totalForPullout : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ total_container_pullout , container_numbers}) => Number(total_container_pullout == container_numbers.length)
              );
              return count
        },
        totalForUnload : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ total_container_unload , container_numbers}) => Number(total_container_unload == container_numbers.length)
              );
              return count
        },
        totalForReturn : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ total_round_use , container_numbers}) => Number(total_round_use == container_numbers.length)
              );
              return count
        },
        totalForE2M : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ e2m }) => Number(e2m == null)
              );
              return count
        },
        totalForGatepass : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ actual_process,total_with_gatepass,container_numbers }) => Number(actual_process != null && (total_with_gatepass != container_numbers.length))
            );
            return count
        },
        totalWithoutGatepass : function(){
            let count = _.sumBy(
                this.list_of_BOL,
              (o)=>{
                 return o.total_container_without_gatepass
              }
            );

            return count
        },
        totalonHand : function(){
            let count = _.sumBy(
                this.list_of_BOL,
              (o)=>{
                 return o.total_container_onhand
              }
            );
            return count
        },
        totalDelivered : function(){
            let count = _.sumBy(
                this.list_of_BOL,
              (o)=>{
                 return o.total_container_delivered
              }
            );
            return count
        },
        totalRoundUse : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ e2m,actual_gatepass,total_round_use,container_numbers }) => Number((e2m != null && actual_gatepass != null) && total_round_use != container_numbers.length)
              );
              return count
        },

        totalCompleted : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ e2m,actual_gatepass,total_round_use,container_numbers }) => Number((e2m != null && actual_gatepass != null) && total_round_use == container_numbers.length)
              );
              return count
        },
        totalSouth : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ pod }) => Number(pod == 'SOUTH')
              );
              return count
        },
        totalNorth : function(){
            let count = _.sumBy(
                this.list_of_BOL,
                ({ pod }) => Number(pod == 'NORTH')
              );
              return count
        }
    },
    methods:{

        save_bl_remarks(id,columnName,value){
            $.confirm({
                title: 'IMS Confirm Action',
                content: 'Are you sure you want to save this Bl Remarks?',
                buttons: {
                    confirm: function () {


                    axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_column_value_bl`,
                    {
                            id : id,
                            columnName : columnName,
                            value : value

                    }).then(function(res){
                        $.alert({
                            title: 'System Information',
                            content: 'Save Remarks',
                            animation: 'left',
                            closeAnimation: 'right',
                        });
                    }).catch(function(err){
                            console.log(err)
                    })



                    },
                    cancel: {
                        text: 'cancel',
                        btnClass: 'btn-warning'

                    }
                }
            });
        },
        CountingDays(discharge, pullout) {


            var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
            var firstDate = new Date(discharge);
            var secondDate = new Date(pullout);

            var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay)));

            return diffDays;

        },
        changeFactorySelected(){
            window.location.href = `http://${window.location.host}/${myBaseName}importation/${this.selected_factory}`;
        },
        filterOff(property){

            this.showingRecords = false,
            this.showingE2m = false,
            this.showingGatepass = false,
            this.showingOnHand = false,
            this.showingCompleted = false,
            this.showingSouth = false,
            this.showingNorth = false,
            this.showingDelivered = false,

            this.showingTransit = false,
            this.showingArrived = false,
            this.showingBerthed = false,
            this.showingDischarge = false,
            this.showingOnProcess = false,
            this.showingWithGatepass = false,
            this.showingPullOut = false,
            this.showingUnload = false,
            this.showingReturn = false,
            this[property] = true


        },
        changeMode(){
            if(this.view_mode){
                this.view_mode = false

                setTimeout(() => {
                    this.refreshPlugins()
                }, 300);
            }else{
                this.view_mode = true
            }
        },
        // FOR EDITING ARRIVAL
        connect_confirm(id,value){

            //value = this.trueOrFalse(value)

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_connecting_confirm`,
            {
                    id : id,
                    value : value

            }).then(function(res){
                    console.log(res.data)
            }).catch(function(err){
                    console.log(err)
            })
        },
        saveBlur(id,columnName,value){

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_arrival_blur`,
            {
                    id : id,
                    columnName : columnName,
                    value : value

            }).then(function(res){
                    console.log(res.data)
            }).catch(function(err){
                    console.log(err)
            })

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
        saveDischarge(id,discharge_date,bl_no){
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_container_discharge`,
            {
                    id : id,
                    value : discharge_date,
					bl_no: bl_no
            }).then(function(res){
                    console.log(res.data)
            }).catch(function(err){
                    console.log(err)
            })
        },
        saveDischarge_all(ids,discharge_date,bl_no){
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_container_discharge_all`,
            {
                ids : ids,
                value : discharge_date,
				bl_no: bl_no

            }).then(function(res){
                    console.log(res.data)
            }).catch(function(err){
                    console.log(err)
            })
        },
        //STORAGE DEMURRAGE
        isNumber: function(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode > 31 && (charCode < 48 || charCode > 57))) {
              evt.preventDefault();;
            } else {
              return true;
            }
        },
        xray(id,value){

            //value = this.trueOrFalse(value)

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_xray_confirm`,
            {
                    id : id,
                    value : value

            }).then(function(res){
                    console.log(res.data)
            }).catch(function(err){
                    console.log(err)
            })
        },
        saveValidation(id,column,discharge_date){
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/save_validation_revalidation_date`,
            {
                    id : id,
                    columnName: column,
                    value : discharge_date

            }).then(function(res){
                    console.log(res.data)
            }).catch(function(err){
                    console.log(err)
            })
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
        compareDate(date1,date2,date3 = new Date()){
            a = new Date(date1);
            b = new Date(date2);
            c = new Date(date3);

                if(((a.getTime() === b.getTime() || a > b) && date2 != null)  ){
                    return true
                }

        },
        //END STORAGE DEMURRAGE
        assesmentTagBlur(id){
            var self = this
           // var object = _.find(this.list_of_BOL, { 'id':  id });


            if(self.list_of_BOL[self.selectedIndex].remarks_of_docs == null){
                if(self.list_of_BOL[self.selectedIndex].assessment_tag == 'RED'){
                    self.list_of_BOL[self.selectedIndex].remarks_of_docs = "AS RED ";
                }else{
                    self.list_of_BOL[self.selectedIndex].remarks_of_docs = "";
                }

            }else{
                switch(self.list_of_BOL[self.selectedIndex].assessment_tag){
                    case 'RED':
                        if(!self.list_of_BOL[self.selectedIndex].remarks_of_docs.includes("AS RED")){
                            self.list_of_BOL[self.selectedIndex].remarks_of_docs = "AS RED " + self.selectedBOL.remarks_of_docs

                        }

                        break;
                    default:
                        if(self.list_of_BOL[self.selectedIndex].remarks_of_docs.includes("AS RED")){
                            self.list_of_BOL[self.selectedIndex].remarks_of_docs = self.list_of_BOL[self.selectedIndex].remarks_of_docs.replace('AS RED ','')
                        }
                }
            }



            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/assessment_tag_remarks`,
            {

                bl_no : self.list_of_BOL[self.selectedIndex].bl_no,
                remarks_of_docs : self.list_of_BOL[self.selectedIndex].remarks_of_docs,
                assessment_tag : self.list_of_BOL[self.selectedIndex].assessment_tag

            }).then(function(res){

            }).catch(function(err){

            })

        },
        refreshPlugins(){
            var self = this

            //REFRESH FOR STORAGE DEMURRAGE
            $('.trucker').select2({
                width: '90%',
				dropdownParent: $("#BL_DETAILS"),
                tags: true,
                insertTag: function(data, tag){
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

                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].trucker = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;




                var I = _.findIndex(self.truckers, function(o) { return o.trucker == select_val; });

                if(I == -1){
                    self.truckers.push({trucker: select_val });
                    console.log(self.truckers)
                }
                self.saveValidation( container_id,'trucker',select_val)


            })

            $('.dismounted_cy').select2({
                width: '90%',
				dropdownParent: $("#BL_DETAILS"),
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New CY ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {

                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val().toUpperCase();


                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].dismounted_cy = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;




                var I = _.findIndex(self.dismounted_cys, function(o) { return o.dismounted_cy == select_val; });

                if(I == -1){
                    self.dismounted_cys.push({dismounted_cy: select_val });

                }
                self.saveValidation( container_id,'dismounted_cy',select_val)


            })

            $('.return_cy').select2({
                width: '90%',
				dropdownParent: $("#BL_DETAILS"),
                tags: true,
                insertTag: function(data, tag){
                    tag.text = "New CY ? Click Here To Add";
                    data.push(tag);
                },
                escapeMarkup: function (markup) { return markup; }

            }).on(
                "select2:select", (e) => {

                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val().toUpperCase();

                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].return_cy = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;




                var I = _.findIndex(self.return_cys, function(o) { return o.return_cy == select_val; });

                if(I == -1){
                    self.return_cys.push({return_cy: select_val });

                }
                self.saveValidation( container_id,'return_cy',select_val)


            })

            $('.validity_storage').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].validity_storage = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;

                    self.saveValidation(container_id,'validity_storage',select_val);

            }).on("clearDate",(e)=>{
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].validity_storage = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].revalidity_storage = null
                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].revalidity_remarks = null

                self.saveValidation(container_id,'validity_storage',select_val);
                self.saveValidation(container_id,'revalidity_storage',null);
                self.saveValidation(container_id,'revalidity_remarks',null);

            });

            $('.pull_out').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();


                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].pull_out = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;


                    self.saveValidation(container_id,'pull_out',select_val);

            }).on("clearDate",(e)=>{
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].pull_out = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;

                self.saveValidation(container_id,'pull_out',select_val);



            });


            $('.detention_validity').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();


                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].detention_validity = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;


                    self.saveValidation(container_id,'detention_validity',select_val);

            }).on("clearDate",(e)=>{
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].detention_validity = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;

                self.saveValidation(container_id,'detention_validity',select_val);



            });


            $('.safe_keep').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].safe_keep = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;



                    self.saveValidation(container_id,'safe_keep',select_val);

            }).on("clearDate",(e)=>{
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');

                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].safe_keep = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;

                self.saveValidation(container_id,'safe_keep',select_val);



            });


            $('.dismounted_date').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].dismounted_date = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;


                    self.saveValidation(container_id,'dismounted_date',select_val);

            }).on("clearDate",(e)=>{
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].dismounted_date = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;

                self.saveValidation(container_id,'dismounted_date',select_val);



            });

            $('.unload').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].unload = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;



                    self.saveValidation(container_id,'unload',select_val);

            }).on("clearDate",(e)=>{
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].unload = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;

                self.saveValidation(container_id,'unload',select_val);



            });
            $('.return_date').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].return_date = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;



                    self.saveValidation(container_id,'return_date',select_val);

            }).on("clearDate",(e)=>{
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');

                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].return_date = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;

                self.saveValidation(container_id,'return_date',select_val);



            });

            $('.validity_demurrage').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].validity_demurrage = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;





                    self.saveValidation(container_id,'validity_demurrage',select_val);



            }).on("clearDate",(e)=>{
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].validity_demurrage = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;

                self.saveValidation(container_id,'validity_demurrage',select_val);

            });
            $('.revalidity_storage').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].revalidity_storage = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;



                self.saveValidation(container_id,'revalidity_storage',select_val);



            }).on("clearDate",(e)=>{
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].revalidity_storage = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;


                self.saveValidation(container_id,'revalidity_storage',select_val);

            });

            $('.revalidity_demurrage').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var index_container = selected_element.data('index_container');

                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].revalidity_demurrage = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;


                    self.saveValidation(container_id,'revalidity_demurrage',select_val);


            }).on("clearDate",(e)=>{
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');

                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].revalidity_demurrage = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;


                self.saveValidation(container_id,'revalidity_demurrage',select_val);

            });

            //END REFRESH FOR STORAGE DEMURRAGE


            $('.container_discharge').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();

                var index_container = selected_element.data('index_container');



                self.list_of_BOL[self.selectedIndex].container_numbers[index_container].actual_discharge = select_val

                var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;


                if(self.list_of_BOL[self.selectedIndex].sameDischarge){
                    _.each(self.list_of_BOL[self.selectedIndex].container_numbers, function(obj, key) { obj['actual_discharge'] = select_val })
                    var list_of_container_id = _.map(self.list_of_BOL[self.selectedIndex].container_numbers, 'id');
                    self.saveDischarge_all(list_of_container_id,select_val, self.list_of_BOL[self.selectedIndex].bl_no)
                }else{
                    self.saveDischarge(container_id,select_val,self.list_of_BOL[self.selectedIndex].bl_no);
                }

            }).on(
                "clearDate", (e) => {
                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();

                    var index_container = selected_element.data('index_container');



                    self.list_of_BOL[self.selectedIndex].container_numbers[index_container].actual_discharge = select_val

                    var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;


                    if(self.list_of_BOL[self.selectedIndex].sameDischarge){
                        _.each(self.list_of_BOL[self.selectedIndex].container_numbers, function(obj, key) { obj['actual_discharge'] = select_val })
                        var list_of_container_id = _.map(self.list_of_BOL[self.selectedIndex].container_numbers, 'id');
                        self.saveDischarge_all(list_of_container_id,select_val,self.list_of_BOL[self.selectedIndex].bl_no)
                    }else{
                        self.saveDischarge(container_id,select_val,self.list_of_BOL[self.selectedIndex].bl_no);
                    }

            });



            $('.date_endorse').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.date_endorse  = select_val;

                self.saveBlur( id,'date_endorse',select_val)

            })
            .on("clearDate",(e)=>{

                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.date_endorse  = null;

                self.saveBlur( id,'date_endorse',null)

            });



            $('.date_approve_ip').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.date_approve_ip  = select_val;

                self.saveBlur( id,'date_approve_ip',select_val)

            }).on("clearDate",(e)=>{

                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.date_approve_ip  = null;

                self.saveBlur( id,'date_approve_ip',null)

            });

            $('.e2m').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.e2m  = select_val;

                self.saveBlur( id,'e2m',select_val)

            }).on("clearDate",(e)=>{

                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.e2m  = null;

                self.saveBlur( id,'e2m',null)

            });

            $('.actual_process').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.actual_process  = select_val;

                self.saveBlur( id,'actual_process',select_val)

            }).on("clearDate",(e)=>{

                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.actual_process  = null;

                self.saveBlur( id,'actual_process',null)

            });


            $('.actual_time_arrival').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.actual_time_arrival  = select_val;

                self.saveBlur( id,'actual_time_arrival',select_val)

            }).on("clearDate",(e)=>{

                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.actual_time_arrival  = null;

                self.saveBlur( id,'actual_time_arrival',null)

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
                        var object = _.find(self.list_of_BOL, { 'id':  id });
                        console.log(object)
                        object.latest_estimated_time_arrival = select_val;

                        self.saveBlur(id, "latest_estimated_time_arrival", select_val);

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


            $('.estimated_time_arrival').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.estimated_time_arrival  = select_val;

                self.saveBlur( id,'estimated_time_arrival',select_val)

            }).on("clearDate",(e)=>{

                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.estimated_time_arrival  = null;

                self.saveBlur( id,'estimated_time_arrival',null)

            });

            $('.berthing_date').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.actual_berthing_date = select_val;

                self.saveBlur( id,'actual_berthing_date',select_val)


            });
            $('.connecting_vessels').select2({
                width: '90%',
				dropdownParent: $("#BL_DETAILS"),
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
                var select_val = selected_element.val().toUpperCase();
                //console.log(selected_element.prev().html())
                var id = parseInt(selected_element.prev().html());



                self.saveBlur( id,'connecting_vessel',select_val)



            })

            $('.pod').select2({
                width: '90%',
				dropdownParent: $("#BL_DETAILS"),
                tags: true,
            }).on(
                "select2:select", (e) => {

                //    this.selectedModel_Number = $('#modelhouselist').val()
                //    this.getSelectedModelHouse()
                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val().toUpperCase();
                //console.log(selected_element.prev().html())
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.pod = select_val;



                self.saveBlur( id,'pod',select_val)
            })

            //SHIPMENT ON PROCESS

            $('.actual_gatepass').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", (e) => {


                var selected_element = $(e.currentTarget);
                var select_val = selected_element.val();
                var id = parseInt(selected_element.prev().html());
                var object = _.find(this.list_of_BOL, { 'id':  id });
                object.actual_gatepass  = select_val;
                if(self.checkIfDelay( object.target_gatepass, object.actual_gatepass)){
                    $.alert({
                        title: 'Delay Information',
                        content: 'The reason of delay is require to fill up.',
                        animation: 'left',
                        closeAnimation: 'right',
                    });



                        object.reason_of_delay_gatepass_boolean = true

                        object.sop_current_status  = object.sop_current_status_temporary



                        self.saveBlurGatepass( id,'actual_gatepass',null)
                        self.saveBlurGatepass( id,'sop_current_status',  object.sop_current_status)
                }else{
                    object.reason_of_delay_gatepass_boolean = false
                    object.sop_current_status_temporary =  object.sop_current_status
                    object.sop_current_status = 'Achieved Gatepass';

                    object.actual_gatepass  = select_val;





                    self.saveBlurGatepass( id,'actual_gatepass',select_val)
                    self.saveBlurGatepass( id,'sop_current_status','Achieved Gatepass')

                }



            }).on(
                "clearDate", (e) => {

                    var selected_element = $(e.currentTarget);
                    var select_val = selected_element.val();
                    var id = parseInt(selected_element.prev().html());
                    var object = _.find(this.list_of_BOL, { 'id':  id });
                    object.sop_current_status = '';

                    self.saveBlurGatepass( id,'actual_gatepass',select_val)
                    self.saveBlurGatepass( id,'sop_current_status','')




            });

             //END SHIPMENT ON PROCESS
        },
        // END EDITING ARRIVAL E2M



        // BEGIN SHIPMENT ON PROCESS
        checkIfDelay(date1,date2){
            a = new Date(date1);
            b = new Date(date2);

                if(b > a){
                    return true
                }else{
                    return false
                }

        },
        saveDelayReasonContainer(id,gatepass,reason,current_status){
            $.confirm({
                title: 'IMS Confirm Action',
                content: 'Are you sure you want to save this delay information?',
                buttons: {
                    confirm: function () {


                       axios.post(`http://${window.location.host}/${myBaseName}bill_of_lading/reason_delay_gatepass_containerweb`,
                       {
                               id : id,
                               gatepass : gatepass,
                               reason : reason,
                               current_status: 'Achieved Gatepass'

                       }).then(function(res){
                           $.alert({
                               title: 'System Information',
                               content: 'Save Successfully',
                               animation: 'left',
                               closeAnimation: 'right',
                           });
                       }).catch(function(err){
                               console.log(err)
                       })



                    },
                    cancel: {
                        text: 'cancel',
                        btnClass: 'btn-warning'

                    }
                }
            });


        },
        saveDelayReason(id,gatepass,reason,current_status){
            $.confirm({
                title: 'IMAP Confirm Action',
                content: 'Are you sure you want to save this delay information?',
                buttons: {
                    confirm: function () {


                       axios.post(`http://${window.location.host}/${myBaseName}bill_of_lading/reason_delay_gatepass_web`,
                       {
                               id : id,
                               gatepass : gatepass,
                               reason : reason,
                               current_status: 'Achieved Gatepass'

                       }).then(function(res){
                           $.alert({
                               title: 'System Information',
                               content: 'Save Successfully',
                               animation: 'left',
                               closeAnimation: 'right',
                           });
                       }).catch(function(err){
                               console.log(err)
                       })



                    },
                    cancel: {
                        text: 'cancel',
                        btnClass: 'btn-warning'

                    }
                }
            });


        },
        saveContainerBlur(id,columnName,value){


             axios.post(`http://${window.location.host}/${myBaseName}bill_of_lading/save_container_columnweb`,
            {
                    id : id,
                    columnName : columnName,
                    value : value

            }).then(function(res){
                    console.log(res.data)
            }).catch(function(err){
                    console.log(err)
            })


        },
        // END SHIPMENT ON PROCESS
        closeModal(){
            this.showModal = false
        },
        openModal(id){

            var Obj = _.find(this.list_of_BOL,{'id': id})
            this.selectedBOL = Obj
            var I = _.findIndex(this.list_of_BOL,{'id': id})
            this.selectedIndex =  I;
            console.log(Obj);

            this.showModal = true
            setTimeout(function(){
                $('#BL_DETAILS').modal('show');
                $('#containerRow').on('scroll',function(e){

                    var leftscroll = e.currentTarget.scrollLeft;

                     if(leftscroll == 0){
                        $('.stickycolumn').css({
                            'background-color': 'transparent ',
                            'color':'#676a6c',
                            'left':53,
                            'z-index':0
                         })
                     }else{
                        $('.stickycolumn').css({
                            'background': 'rgb(47,64,80,0.8)',
                            'color':'#fff',
                            'left':0,
                            'z-index':6
                         })
                     }

                })
            },100)






        },
        getRecord(take){
            var self = this
            let skip = this.list_of_BOL.length
            self.showprogress = true

            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
            $.blockUI({ message: '<h3> Processing ...</h3>' });

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/get_Record_index`,
            {
                    'skip' : skip,
                    'take' : take,
                    'category': self.filter_search,
                    'bl_no' : self.search_bl_no,
                    'selected_factory': self.selected_factory

            }).then((response)=>{

                response.data.forEach((value)=>{
                    this.list_of_BOL.push(value)

                })
                self.showprogress = false

                //UNBLOCK
                $.unblockUI();



           }).catch((err)=>{
            self.showprogress = false
           })


        },
        toggle(id) {
            var self = this
            const index = this.opened.indexOf(id);

            if (index > -1) {
                this.opened.splice(index, 1)
            } else {
                this.opened.push(id)
            }





        },
        viewInfo(bl_no){
            var self = this
            this.search = true

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/get_info`,
            {
                'bl_no' : bl_no

            }).then((response)=>{

                self.newBOL = response.data


            })
        },
        refresh(){
            var self = this
            self.search = false
            self.showprogress = true

            self.newBOL = {}

            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
            $.blockUI({ message: '<h3> Processing ...</h3>' });

            setTimeout(() => {
                axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/refresh_master`,{
                    selected_factory : self.selected_factory
                }).then((response)=>{

                self.list_of_BOL = []
                response.data.forEach((value)=>{
                    self.list_of_BOL.push(value)

                })
                self.showprogress = false
                //END BLOCK
                $.unblockUI();

            }).catch((err)=>{
                self.showprogress = false
            })
            }, 100);


            self.firstLoad = false
        },
        searchBL(){
            var self = this
            this.search = false
            self.firstLoad = false
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/get_info`,
            {
                'category': self.filter_search,
                'bl_no' : self.search_bl_no

            }).then((response)=>{

                if(self.filter_search == 'I' || self.filter_search == 'C'){
                    self.list_of_BOL = []
                    response.data.forEach((value)=>{
                        self.list_of_BOL.push(value)

                    })


                }else{

                    if(response.data.length == 0){

                        self.list_of_BOL = []
                    }else{


                            self.list_of_BOL = []
                            response.data.forEach((value)=>{
                                self.list_of_BOL.push(value)

                            })



                    }

                }



            })




        }
    },
    mounted(){
        var self = this

            // requires jquery library




        self.dateToday = self.formatDate(new Date() );

        // requires jquery library


        $(document).ready(function() {


            $('#filter_month').datepicker({
                format: "yyyy-mm-dd",clearBtn: true
            }).on(
                "changeDate", () => {
                    self.filter_month =    $('#filter_month').val()

            })

            $( document ).on( "focus", ".actual_gatepass_container:not(.hasDatepicker)", function() {

                 $(this).addClass('hasDatepicker');

                 $( this ).datepicker({
                     format: "yyyy-mm-dd",clearBtn: true,
                     beforeShow: function(input, inst) {

                         $(this).addClass('hasDatepicker');
                     }
                 }).on(
                     "changeDate", (e) => {
                     e.stopPropagation();
                     var selected_element = $(e.currentTarget);
                     var select_val = selected_element.val();


                     var index_container = selected_element.data('index_container');


                     self.list_of_BOL[self.selectedIndex].container_numbers[index_container].actual_gatepass = select_val

                     var container_id = self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;







                     if(self.checkIfDelay( self.list_of_BOL[self.selectedIndex].target_gatepass, select_val)){
                         $.alert({
                             title: 'Delay Information',
                             content: 'The reason of delay is require to fill up.',
                             animation: 'left',
                             closeAnimation: 'right',
                         });

                         self.list_of_BOL[self.selectedIndex].container_numbers[index_container].reason_of_delay_gatepass_boolean = true
                         self.list_of_BOL[self.selectedIndex].container_numbers[index_container].sop_current_status = 'Achieved Gatepass';

                         self.saveContainerBlur(container_id,'actual_gatepass',null)


                     }
                     else{
                        self.list_of_BOL[self.selectedIndex].container_numbers[index_container].reason_of_delay_gatepass_boolean = false
                        self.list_of_BOL[self.selectedIndex].container_numbers[index_container].sop_current_status = 'Achieved Gatepass';
                         self.saveContainerBlur(container_id,'actual_gatepass',select_val)
                         self.saveContainerBlur(container_id,'sop_current_status','Achieved Gatepass')


                     }


                 }).on(
                     "clearDate", (e) => {
                     e.stopPropagation();
                     var selected_element = $(e.currentTarget);
                     var select_val = selected_element.val();


                     var index_container = selected_element.data('index_container');

                     self.list_of_BOL[self.selectedIndex].container_numbers[index_container].actual_gatepass = select_val

                     var container_id =  self.list_of_BOL[self.selectedIndex].container_numbers[index_container].id;

                     self.saveContainerBlur(container_id,'actual_gatepass',null)
                     self.saveContainerBlur(container_id,'sop_current_status','Achieved Gatepass')


                 });


             });




        });





    }
})
