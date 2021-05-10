

new Vue({
    el: '.vue_app_element',
    data: {
        list_of_place_endorsement : list_of_place_endorsement,
        search_bl_no :'',
        search_actual_process: '',
        search:false,
        newBOL: {
            bl_no: '',
            date_endorse: '',
            place_endorsement : '',
            actual_process: '',
            remarks_of_docs: '',
            tsad_no: '',
            assessment_tag: '',
        },
        commodities:[]
        
    },
    methods:{
        searchBL(){
            var self = this

            axios.post(`http://${window.location.host}/api/bill_of_lading/get_info_boc`,
            {
                'bl_no' : this.search_bl_no

            }).then((response)=>{
             
               
                self.newBOL = response.data['info'][0]   
                self.commodities = response.data['commodities']  
                setTimeout(function(){
                    $('.boc_select2').trigger('change')
                },200)
            })
        },
        save(){
            var self = this

            $.confirm({
                title: 'Save!',
                content: 'Are you sure you want to update?',
                buttons: {
                    confirm: function () {

                        axios.post(`http://${window.location.host}/api/bill_of_lading/boc/save`,
                        {
                            'BOC' : self.newBOL
            
                        }).then((response)=>{
                         
                                $.alert({
                                    title: 'Info!',
                                    content: 'Success Saved!',
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
    mounted(){
        var self = this
        $('#date_endorse').datepicker({
            format: "yyyy-mm-dd"
        }).on(
            "changeDate", () => {
                self.newBOL.date_endorse = $('#date_endorse').val()

        });

        $("#search_actual_process").datepicker({
            format: "yyyy-mm-dd"
        }).on(
            "changeDate", () => {
                self.search_actual_process = $('#search_actual_process').val()

        });
        $("#actual_process").datepicker({
            format: "yyyy-mm-dd"
        }).on(
            "changeDate", () => {
                self.newBOL.actual_process = $('#actual_process').val()

        });

        
        $('#place_endorsement').select2({
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
            self.newBOL.place_endorsement = selected_element.val();
    
        })
        $('#assessment_tag').select2({ width: '100%'}).on(
            "select2:select", (e) => {
            //    this.selectedModel_Number = $('#modelhouselist').val()
            //    this.getSelectedModelHouse()
            var selected_element = $(e.currentTarget);
            self.newBOL.assessment_tag = selected_element.val();
    
        })

        
      
    }
})