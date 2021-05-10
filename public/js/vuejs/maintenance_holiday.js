new Vue({
    el: '.vue_app_element',
    data: {
        holidays : holidays ,
        newHolidayObj:{
            id: '',
            holiday_name: '',
            holiday_date: '',
            holiday_day: ''
        },
        editHolidayObj:{
            id: '',
            holiday_name: '',
            holiday_date: '',
            holiday_day: ''
        },
        weekday: ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]
    },
    methods:{
        editHoliday(id){
            var obj = _.find(this.holidays,{'id':id})
            this.editHolidayObj.id = obj.id;
            this.editHolidayObj.holiday_name = obj.holiday_name;
            this.editHolidayObj.holiday_date = obj.holiday_date;
            this.editHolidayObj.holiday_day = obj.holiday_day;
            $('#editHolidayDate').val(obj.holiday_date)
            $('#editHolidayDate').datepicker("refresh");
           
           
           
            
        },
        deleteUser(id){
        var self = this

        $.confirm({
            title: 'Delete user!',
            content: 'Are you sure to delete this user?',
            buttons: {
                confirm: {
                    text: 'Delete',
                    btnClass: 'btn-danger',
                    action:  function () {

                        axios.post(`http://${window.location.host}/${myBaseName}api/holiday/delete`,
                        {
                            'id' : id
        
                        }).then((response)=>{
                            var index = _.findIndex(self.holidays, {id: id})
                            self.holidays.splice(index,1)
                            $.alert({
                                title: 'Info!',
                                content: 'Success Deleted!',
                            });
                        })
                       
                    },
                },
                cancel: {
                    text: 'cancel',
                    btnClass: 'btn-warning'
                   
                }
            }
        });

    },
    saveObj(objHoliday,type){
            var self = this
            $.confirm({
                title: 'Save Holiday!',
                content: 'Are you sure to save this data?',
                buttons: {
                    confirm: function () {
                        var checkUserExist = 0
  
                        axios.post(`http://${window.location.host}/${myBaseName}api/holiday/save`,
                        {
                            'Holidays' : objHoliday,
                            'Type' : type
    
                        }).then((response)=>{
                            $.alert({
                                title: 'Info!',
                                content: 'Success Saved!',
                            });

                            if(type == 'EDIT'){
                                var obj = _.find(self.holidays,{'id': objHoliday.id })
                                obj.holiday_name = objHoliday.holiday_name;
                                obj.holiday_date = objHoliday.holiday_date;
                                obj.holiday_day = objHoliday.holiday_day;
                            }else{
                                self.holidays.push(objHoliday)
                            }

                            
                            
                        })
                            
                       
                       
                    },
                    cancel: {
                        text: 'cancel',
                        btnClass: 'btn-warning'
                       
                    }
                }
            });


        },
    },
    mounted(){
        
        $("#newHolidayDate").datepicker({
                format: "yyyy-mm-dd"
        }).on(
                "changeDate", () => {
                    this.newHolidayObj.holiday_date = $('#newHolidayDate').val()
                    var d = $('#newHolidayDate').datepicker('getDate').getDay();  
                    this.newHolidayObj.holiday_day = this.weekday[d]

        });



            $("#editHolidayDate").datepicker({
                format: "yyyy-mm-dd"
            }).on(
                    "changeDate", () => {
                        this.editHolidayObj.holiday_date = $('#editHolidayDate').val()
                        var d = $('#editHolidayDate').datepicker('getDate').getDay();  
                        this.editHolidayObj.holiday_day = this.weekday[d]
            });
       
        
        
    }

})