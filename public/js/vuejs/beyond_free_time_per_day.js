

new Vue({
    el: '.vue_app_element',
    data: {
        reports : reports,
        date_request : date_request,
        search: '',
        factory: '',
        category: '',
        all: false,
        details_obj : [
            {
                factory: '',
                bl_no_fk: '',
                container_number: '',
                commodity : '',
                reason_of_delay_delivery: '',
            }
        ]
      
    },
    computed:{
        filteredList() {
            return this.details_obj.filter(post => {
              return post.bl_no_fk.toLowerCase().includes(this.search.toLowerCase()) ||
                    post.container_number.toLowerCase().includes(this.search.toLowerCase())
            })
          }
    },
    methods: {
        showDetails(factory,category,all = false){
            var self = this
            self.factory = factory;
            self.category = category;
            self.all = all;

            $("#detail_obj").dataTable().fnDestroy()
            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
            $.blockUI({ message: '<h3> Processing ...</h3>' }); 

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/get_beyond_free_time_oer_day_details`,
            {       factory: factory,
                    date_filter : this.date_request,
                    category: category,
                    all: all,
            }).then(function(res){
                self.details_obj = res.data
                $('#myModal5').modal('show');

                // Setup - add a text input to each footer cell
                setTimeout(function(){
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
                        }
                    
                    })

                },300)
                //console.log(res)
                //END BLOCK
                $.unblockUI();
            }).catch(function(err){
                    console.log(err)
            })

            
           
        },
        add(a, b) {
            return a + b;
        },
        sumObjectsByKey(...objs) {
            return objs.reduce((a, b) => {
              for (let k in b) {
                if (b.hasOwnProperty(k))
                  a[k] = (a[k] || 0) + b[k];
              }
              return a;
            }, {});
        },
        searchDate(date){
            var self = this
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/search_date_beyond_freetime_per_day`,
            {
                  
                    date_filter : date,

            }).then(function(res){
                self.reports = res.data
              
                
            }).catch(function(err){
                    console.log(err)
            })
        },
        port_charges(type,qty){
            //type
            //1 = 6 to 10
            //2 = 11 days beyond
            if(type == 1){
                var price =  (qty * 1443.90).toFixed(2)
            }else{
                var price = (qty * 10000).toFixed(2)
                
            }
            return price;

        },
        numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
       
    },
    mounted() {
        
        $('#date_filter').datepicker({
            format: "yyyy-mm-dd",
            endDate: new Date()
        }).on(
            "changeDate", (e) => {
                
               this.date_request =  $('#date_filter').val()
               this.searchDate( $('#date_filter').val() )

        })

    },
})