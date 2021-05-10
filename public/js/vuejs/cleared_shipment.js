new Vue({
    el: '.vue_app_element',
    data: {
        list_of_BOL: list_of_BOL,
        list_of_BOL_Total: list_of_BOL_Total,
        loading_data: false,
        numberofTake: 100,
        showprogress: false,
        filter_search: '',
        search_cn: ''
    },
    methods: {
        refresh() {
            var self = this
            self.loading_data = true;
            self.list_of_BOL = {}
            self.loading_data = true 
            self.showprogress = true;
          
            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
            $.blockUI({ message: '<h3> Processing ...</h3>' });
            $("#detail_obj").dataTable().fnDestroy()
           
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/refresh_cleared_shipment`).then((response) => {
                
                self.list_of_BOL = response.data
                self.showprogress = false;
             
                setTimeout(function () {

                    self.loading_data = false
                    self.dataTableInit();
                    $.unblockUI();
                }, 500)
               
    
            })
            self.loading_data = false;
         
            
        },
        filterSearch() {

            var self = this
            if (self.filter_search != '') {
                axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/cleared_shipment_search  `,
                    {

                        category: self.filter_search,
                        search: self.search_cn

                    }).then(function (res) {

                        self.list_of_BOL = {};
                        self.list_of_BOL = res.data;
                        $("#detail_obj").dataTable().fnDestroy()
                        setTimeout(function () {
                            self.dataTableInit();
                        }, 100)
                        

                    }).catch(function (err) {
                        console.log(err)
                    })

            }


        },
        getRecord_Cleared(take){

            var self = this;
            let skip = this.list_of_BOL.length;
            //let role = role
            self.loading_data = true 
            self.showprogress = true;
          
            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
             $.blockUI({ message: '<h3> Processing ...</h3>' });
           
            axios
                .post(
                    `http://${
                        window.location.host
                    }/${myBaseName}api/bill_of_lading/get_cleared_shipment_all`,
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
                       
                       
                        self.loading_data = false
                         // Setup - add a text input to each footer cell
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
        dataTableInit(){
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
    mounted(){

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
          
              scrollY: "600px",
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
      

    }
      
})