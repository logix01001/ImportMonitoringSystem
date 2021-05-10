new Vue({
    el: '.vue_app_element',
    data: {
        users : users ,
        switchery : '',
        newUser:{
            id: '',
            employee_number: '',
            employee_name: '',
            password: '',
            master: 0,
            maintenance: 0,
            encoding: 0,
            arrival: 0,
            e2m: 0,
            current_status: 0,
            gatepass: 0,
            storage_validity: 0,
            container_movement: 0,
            safe_keep: 0,
        },
        editUser:{
            id: '',
            employee_number: '',
            employee_name: '',
            password: '',
            master: 0,
            maintenance: 0,
            encoding: 0,
            arrival: 0,
            e2m: 0,
            current_status: 0,
            gatepass: 0,
            storage_validity: 0,
            container_movement: 0,
            safe_keep: 0,
        },
        existUser: false
    },
    methods:{
        geteditUser(id){
            var self = this;
            let Obj = _.find(this.users,{'id':id})
           
            this.editUser = Obj
          
           
        },
        deleteUser(id){
        var self = this

        $.confirm({
            title: 'Delete user!',
            content: 'Are you sure to delete this user?',
            buttons: {
                confirm: function () {

                    axios.post(`http://${window.location.host}/${myBaseName}api/user/delete`,
                    {
                        'id' : id
    
                    }).then((response)=>{
                        var index = _.findIndex(self.users, {id: id})
                        self.users.splice(index,1)
                        $.alert({
                            title: 'Info!',
                            content: 'Success Deleted!',
                        });
                    })
                   
                },
                cancel: {
                    text: 'cancel',
                    btnClass: 'btn-warning'
                   
                }
            }
        });

    },
        saveObj(objUser,type){
            var self = this
            $.confirm({
                title: 'Save user!',
                content: 'Are you sure to save this data?',
                buttons: {
                    confirm: function () {
                        var checkUserExist = 0
                        self.existUser = false
                        axios.post(`http://${window.location.host}/${myBaseName}api/user/checkUser`,
                        {
                            'Users' : objUser,
                            'Type' : type
    
                        }).then((response)=>{
                            checkUserExist = response.data
                            if(checkUserExist == 0){

                               
                                axios.post(`http://${window.location.host}/${myBaseName}api/user/save`,
                                {
                                    'Users' : objUser,
                                    'Type' : type
            
                                }).then((response)=>{
                                    swal("Saved!", "Successfully Saved", "success");
                                  
                                })
                            
                            }else{
                                self.existUser = true
                                self.newUser.password = ''
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
        refreshSwitchery(){
            var self = this 
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

            elems.forEach(function(html) {
               self.switchery = new Switchery(html,{'color' : '#2f4050'});
            });
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch_edit'));

            elems.forEach(function(html) {
               self.switchery = new Switchery(html,{'color' : '#2f4050'});
            });


           
        }
    },
    mounted() {
       this.refreshSwitchery();
        
     
        $('.tableuser').DataTable( {

           searching: true
        
        } );
    },

})