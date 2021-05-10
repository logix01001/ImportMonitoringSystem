

new Vue({
    el: '.vue_app_element',
    data: {
        summary : summary,
        data_charts: data_charts,
        series_charts: series_charts,
        distinct_year: distinct_year,
        selected_year: year,
        date_filter : '',
        search: '',
        factory: '',
        category: '',
        day: '',
        month: '',
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
        formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
        
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
        
            return [year, month, day].join('-');
        },        
        showDetails(factory,day,month,category,all = false){
            var months = "JanFebMarAprMayJunJulAugSepOctNovDec";
            var self = this
            self.day = (day + 1);
            self.month = month;
            self.factory = factory;
            self.category = category;
            self.all = all;

            self.date_filter = this.formatDate( this.selected_year + '-' + (months.indexOf(self.month) / 3 + 1) + '-' + this.day )
            $("#detail_obj").dataTable().fnDestroy()
            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
            $.blockUI({ message: '<h3> Processing ...</h3>' }); 

            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/get_beyond_free_time_oer_day_details`,
            {       factory: factory,
                    date_day : this.day,
                    date_month : this.month,
                    date_year : this.selected_year,
                    category: category,
                    all: all,
            }).then(function(res){
                self.details_obj = res.data
                $('#myModal5').modal('show');
                //console.log(res)
                //END BLOCK

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
                $.unblockUI();
            }).catch(function(err){
                    console.log(err)
            })

            
           
        },
        selectYear(){
            window.location.href=`http://${window.location.host}/ims/beyond_storage_free_time_summary/${this.selected_year}`
        },  
    },
    mounted() {
       // Create the chart
Highcharts.chart('container', {
    colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', 
    '#FF9655', '#FFF263', '#6AF9C4'],
    chart: {
        type: 'column'
    },
    title: {
        text: 'Summary for Beyond free time Storage ' + year
    },
    credits: false,
    subtitle: {
        text: 'Click the month to view per day.'
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Total counting of containers'
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
                format: '{point.y}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total containers<br/>'
    },

    "series": [
        {
            "name": "Browsers",
            "colorByPoint": true,
            "data": 
                this.data_charts
            
        }
    ],
    "drilldown": {
        "drillUpButton":{
            "position":{
                "verticalAlign":"top",
                "y" : "-10"
            }
        },
        "series": this.series_charts
    }
});
    },
})