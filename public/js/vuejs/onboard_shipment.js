new Vue({
    el: '.vue_app_element',
    data: {
        date_filter: date_request,
        list_bl_volume: list_bl_volume,
        reference: 'D',
        range_start: ' ',
        range_end : ' ',
        dateMonth: ' ',
        dateYear: ' ',
        last_reference: 'D',
        factory:'',
        factory_all: false,
        pod : ' ',
        pod_all: false,
        category: '',
        details_obj : [
            {
                bl_no_fk: '',
                container_number: '',
                actual_time_arrival: '',
                commodity : '',
                actual_discharge: '',
            }
        ],
        search: '', 
        date_today: date_today,
        as_of_now: as_of_now,
        
    },
    computed: {
        filteredList() {
            return this.details_obj.filter(post => {
              return post.bl_no_fk.toLowerCase().includes(this.search.toLowerCase()) ||
                    post.container_number.toLowerCase().includes(this.search.toLowerCase())
            })
          }
    },
    methods: {
       
        searchDate(date = null,reference,as_of_now = false){
            var self = this
            self.as_of_now = as_of_now
            $.blockUI({ message: '<h3> Processing ...</h3>' }); 

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/onboard_shipment`,
            {
                reference: this.reference,
                date_filter : date,
                range_start : this.range_start,
                range_end : this.range_end,
                dateMonth : $('#date_month').val(),
                dateYear : $('#date_year').val(),
                as_of_now: self.as_of_now
            }).then(function(res){

                self.list_bl_volume = res.data
                self.last_reference = reference
                //console.log(res)
                $.unblockUI();
            }).catch(function(err){
                    console.log(err)
            })



        },
        showDetails(factory=null,category=null,pod=null,pod_all = false,factory_all = false){
            var self = this

            self.factory = factory
            self.factory_all = factory_all
            self.pod = pod
            self.pod_all = pod_all
            self.category = category
           
            $("#detail_obj").dataTable().fnDestroy()
            $.blockUI({ message: '<h3> Processing ...</h3>' }); 

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/onboard_shipment_details`,
            {
                reference: this.last_reference,
                date_filter : this.date_filter,
                range_start : this.range_start,
                range_end : this.range_end,
                dateMonth : $('#date_month').val(),
                dateYear : $('#date_year').val(),
                category: self.category, 
                pod: self.pod,
                pod_all: pod_all,
                factory_all: self.factory_all,
                factory: self.factory, 
                as_of_now: self.as_of_now

            }).then(function(res){
                    $('.myModal5').modal('show');
               
                   
                    self.details_obj = res.data
                    setTimeout(function(){
                        
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
                            autoWidth: true,
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
                $.unblockUI();
            }).catch(function(err){
                    console.log(err)
            })
        }
       
    },
    mounted() {
        var self = this
        $('#date_filter').datepicker({
            format: "yyyy-mm-dd"
        }).on(
            "changeDate", (e) => {
         
                this.date_filter = $('#date_filter').val()
                this.searchDate($('#date_filter').val(),'D')
                
              
        })

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

        $("#date_month").datepicker( {
            format: "yyyy-mm",
            startView: "months", 
            minViewMode: "months"
        }).on(
            "changeDate", (e) => {
                this.dateMonth = $('#date_month').val()
                this.searchDate($('#date_month').val(),'M')
               // this.searchDate($("#date_month").val() )
              
        });

        $("#date_year").datepicker( {
            format: "yyyy",
            startView: "years", 
            minViewMode: "years"
        }).on(
            "changeDate", (e) => {
                self.dateYear = $('#date_year').val()
                self.searchDate($('#date_year').val(),'Y')
               // this.searchDate($("#date_year").val() )
        });
        
        

    },
})