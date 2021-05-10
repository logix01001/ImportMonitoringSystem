window.container_type_colors = ['#058DC7', '#50B432', '#ED561B', '#8f5c38', '#24CBE5','#198b9a','#f26df9','#006989', '#64E572', 
'#FF9655', '#2c4251', '#6AF9C4','#55769a','#fc5130','#d8a47f','#9dd1f1','#566e3d','#fe9920','#6279b8','#5f0a87','#254441','#db504a','#360568','#124e78'],

new Vue({
    el: '.vue_app_element',
    data: {
        
        categories: categories,
        unload_series: unload_series,
        dateMonth:  year + '-' + m,
        total_unload_series: total_unload_series,
      /*  highchart_3 : {
            chart: {
                type: 'column'
            },
            colors: container_type_colors,
            title: {
                text: 'Containers arrived at port (20 GP)'
            },
            subtitle: {
                text: ''
            },
            credits: false,
            plotOptions: {
                series: {
                    dataLabels: {
                        enabled: true,
                    }
                }
            },
            xAxis: {
                categories: self.categories,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                text: 'Containers arrived'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.0f} Ctr(s)</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        crop: false,
                        overflow: 'none'
                    }
                }
            },
            series: this.container_20gp
        }, */
        
    },
    methods: {
        searchDate(date){
            var year = date.split('-')[0];
            var month = date.split('-')[1];

            window.location.replace(`http://${window.location.host}/${myBaseName}unload_analysis/${year}/${month}`);
        },
       
       
    },
    mounted() {


        $(".row_scroll").on("scroll", function(e) {
            var leftscroll = e.currentTarget.scrollLeft;

            if (leftscroll == 0) {
                $(".stickycolumn").css({
                    '-webkit-box-shadow': '',
                    'box-shadow': '',
                    left: 0,
                });
            } else {
                $(".stickycolumn").css({
                    '-webkit-box-shadow': '3px 0 5px -2px #888',
                    'box-shadow': '3px 0 5px -2px #888',
                    left: 0,
                    'z-index':10
                });
            }
            //console.log(leftscroll)
        });
        
        $("#date_month").datepicker( {
            format: "yyyy-mm",
            startView: "months", 
            minViewMode: "months",
            endDate: '+0m',
        }).on(
            "changeDate", (e) => {
                this.dateMonth = $('#date_month').val()
                this.searchDate($('#date_month').val())
               // this.searchDate($("#date_month").val() )
              
        });

      
    },
})