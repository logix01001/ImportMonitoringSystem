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
        cy : ' ',
        cy_all: false,
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


        factory_array: factory_array,
        factory_drilldown_array: factory_drilldown_array,
        cType: cType,
        factory_count: factory_count,
        total: [],
        factory_total: factory_total,
        
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
       
        showDetails(factory=null,factory_all = false){
            var self = this

            self.factory = factory
            self.factory_all = factory_all
           
            $("#detail_obj").dataTable().fnDestroy()
            $.blockUI({ message: '<h3> Processing ...</h3>' }); 

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/container_irs_report_details`,
            {
                
                factory_all: self.factory_all,
                factory: self.factory, 

            }).then(function(res){
                $('.myModal5').modal('show');
               
                    self.details_obj = res.data
                    setTimeout(function(){
                        $('#BL_DETAILS').modal('show');
    
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

 // Create the chart
 Highcharts.chart('container', {
    chart: {
        type: 'column'
    },

    credits: false,
    colors: ['#336699', '#e88d67', '#41d3bd', '#910000', '#1aadce',
    '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'],
    title: {
        text: 'Container Count '
    },
    subtitle: {
        text: 'Click the columns to view container type per each factory.'
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Total Container'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:f}</b> of total<br/>'
    },

    "series": [
        {
            "name": "Container #",
            "colorByPoint": true,
            "data": this.factory_array
        }
    ],
    "drilldown": {
        "drillUpButton": {
            "position": {
                "verticalAlign": "top",
                "y": "-10"
            }
        },
        "series": this.factory_drilldown_array
    }
});


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