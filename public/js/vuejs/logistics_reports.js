

new Vue({
    el: '.vue_app_element',
    data: {
        range_start: '',
        range_end: '',
        vessel: false,
        ETA: true,
        ATA: true,
        ATB: true,
        port: false,
        gatepass: false,
        delivery: false,
        unloaded: false,
        returned: false,


        last_range_start: '',
        last_range_end: '',
        last_vessel: false,
        last_ETA: true,
        last_ATA: true,
        last_ATB: true,
        last_port: false,
        last_gatepass: false,
        last_delivery: false,
        last_unloaded: false,
        last_returned: false,

        list_of_BOL: [],


        selectedIndex: 0,
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
    methods: {
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
        filterDate(){
            var self = this
            $.blockUI({ message: '<h3> Processing ...</h3>' }); 

            self.last_range_start= self.range_start,
            self.last_range_end= self.range_end,
            self.last_vessel= self.vessel,
            self.last_ETA= self.ETA,
            self.last_ATA= self.ATA,
            self.last_ATB= self.ATB,
            self.last_port= self.port,
            self.last_gatepass= self.gatepass,
            self.last_delivery= self.delivery,
            self.last_unloaded= self.unloaded,
            self.last_returned= self.returned,


            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/get_logistics_reports`,
            {

                range_start : this.range_start,
                range_end : this.range_end,
                vessel: this.vessel,
                ETA: this.ETA,
                ATA: this.ATA,
                ATB: this.ATB,
                port: this.port,
                gatepass: this.gatepass,
                delivery: this.delivery,
                unloaded: this.unloaded,
                returned: this.returned,

            }).then(function(res){

                self.list_of_BOL = res.data
                //console.log(res)
                $.unblockUI();
            }).catch(function(err){
                    console.log(err)
            })



        },
          // END SHIPMENT ON PROCESS
          closeModal(){
            this.showModal = false
        },
       
       
    },
    mounted() {
        var self = this
        $('.input-daterange').datepicker({

            format: "yyyy-mm-dd",
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true

        }).on(
            "changeDate", (e) => {
         
                self.range_start = $('#range_start').val()
                self.range_end = $('#range_end').val()
               
                
              
        });

    },
})