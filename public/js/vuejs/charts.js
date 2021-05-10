new Vue({
    el: '.vue_app_element',
    data: {
        factory_array: factory_array,
        factory_drilldown_array: factory_drilldown_array,
        factory_drilldown_array_pod: factory_drilldown_array_pod,
        cType: cType,
        factory_count: factory_count,
        total: [],
        factory_total: factory_total,
        details_obj : [
            {
                bl_no_fk: '',
                invoices: '',
                container_number: '',
                actual_time_arrival: '',
                commodity : '',
                actual_discharge: '',
            }
        ],
        search: '',
        factory: '',
        container_type: '',
        factory_all: '',
        container_all: '',
        header_bl_no: '',
        header_invoice_no: '',
    },
    computed: {
        filteredList() {
            return this.details_obj.filter(post => {
              return post.bl_no_fk.includes(this.header_bl_no) &&
                    post.invoices.includes(this.header_invoice_no)
            })
          }
    },
    methods: {
        add(a, b) {
            return a + b;
        },
        
        showDetails(factory,container_type,all = false, cAll = false){
            var self = this
            //BLOCK THE PAGE SHOW PROCESSING MESSAGE

            self.factory = factory;
            self.container_type = container_type;
            self.factory_all = all;
            self.container_all = cAll;
            $("#detail_obj").dataTable().fnDestroy()

            $.blockUI({ message: '<h3> Processing ...</h3>' }); 
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/get_chart_details`,
            {       
                    factory: factory,
                    container_type: container_type,
                    factory_all: all,
                    container_all: cAll
                    
            }).then(function(res){
                self.details_obj = res.data
                $('#myModal5').modal('show');
                //console.log(res)
                 //END BLOCK
                 $.unblockUI();
                
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
                },100)
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
                "series": this.factory_drilldown_array_pod
            }
        });
    },
})