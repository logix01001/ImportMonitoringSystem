window.container_type_colors = ['#058DC7', '#50B432', '#ED561B', '#8f5c38', '#24CBE5','#198b9a','#f26df9','#006989', '#64E572', 
'#FF9655', '#2c4251', '#6AF9C4','#55769a','#fc5130','#d8a47f','#9dd1f1','#566e3d','#fe9920','#6279b8','#5f0a87','#254441','#db504a','#360568','#124e78'],

new Vue({
    el: '.vue_app_element',
    data: {
        
        categories: categories,
        series: series,
        series_2nd: series_2nd,
        series_dischare_gatepass_monitoring : [],
        series_delivery_gatepass_monitoring : [],
        series_dischare_gatepass_monitoring_table:[],
        dateMonth:  year + '-' + m,
        gatepass_series: gatepass_series,
        container_20gp: container_20gp,
        container_40hc: container_40hc,
        container_type_colors: container_type_colors,
        highchart_1 : {
            chart: {
                type: 'column'
            },
            colors: ['#336699', '#e88d67', '#41d3bd', '#910000', '#1aadce',
            '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'],
            title: {
                text: 'DISCHARGE DATE, GATEPASS,PULLOUT'
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
                text: '# of containers'
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
            series: this.series
        },
        highchart_2 : {
            chart: {
                type: 'column'
            },
            colors: ['#ff3366','#336699', '#41d3bd', '#910000', '#1aadce',
            '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'],
            title: {
                text: 'Target Delivery'
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
                text: '# of containers'
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
            series: [this.series_2nd[1]]
        },
        highchart_3 : {
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
        },
        highchart_3_1 : {
            chart: {
                type: 'column'
            },
            colors: container_type_colors,
            title: {
                text: 'Containers arrived at port (40 HC)'
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
            series: this.container_40hc
        },
        highchart_4 : {
            chart: {
                type: 'column'
            },
            colors: ['#336699', '#e88d67', '#41d3bd', '#910000', '#1aadce',
            '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'],
            title: {
                text: 'DISCHARGE DATE, GATEPASS,PULLOUT'
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
                text: '# of containers'
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
            series: this.series_dischare_gatepass_monitoring
        }
      
        
    },
    methods: {
        getColors(){
            alert(this.container_type_colors)
            return this.container_type_colors;
        },
        showGatepass(date){
            var self = this
            $.blockUI({ message: '<h3> Processing ...</h3>' }); 
            $("#detail_obj").dataTable().fnDestroy()
            axios.post(`http://${window.location.host}/${myBaseName}import_analysis_reports/discharge_gatepass`,
            {       
                date : date
                    
            }).then(function(res){
                
                self.series_dischare_gatepass_monitoring = res.data[0]
                self.series_delivery_gatepass_monitoring = res.data[3]
                self.series_dischare_gatepass_monitoring_table = res.data[2]
                
                setTimeout(()=>{
                    Highcharts.chart('container4',{
                        chart: {
                            type: 'column'
                        },
                        colors: ['#336699', '#e88d67', '#41d3bd', '#910000', '#1aadce',
                        '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'],
                        title: {
                            text: 'Discharge Status'
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
                            categories: res.data[1],
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                            text: '# of containers'
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
                        series: self.series_dischare_gatepass_monitoring
                    });

                    Highcharts.chart('container5',{
                        chart: {
                            type: 'column'
                        },
                        colors: ['#e88d67', '#41d3bd', '#41d3bd', '#910000', '#1aadce',
                        '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'],
                        title: {
                            text: 'Gatepass to delivery status'
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
                            categories: res.data[4],
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                            text: '# of containers'
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
                        series: self.series_delivery_gatepass_monitoring
                    });

                     // Setup - add a text input to each footer cell
                     
                 
                     var table = $('#detail_obj').DataTable({
                        "bSort": true,
                        bInfo : true,
                        "dom": '<"top"i>rt<"bottom"flp><"clear">'
                     });
 
 
 
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
                },500)
               
               

                $('#myModal5').modal('show');
                $.unblockUI();
                
            }).catch(function(err){
                $.blockUI({ message: '<h3> oooops something error... Please contact your system developer..</h3>' }); 
                console.log(err)
            })
        },
        searchDate(date){
            var year = date.split('-')[0];
            var month = date.split('-')[1];

            window.location.replace(`http://${window.location.host}/${myBaseName}import_analysis/${year}/${month}`);
        },
        // discharge_gatepass(data){
          
        // }
       
       
    },
    mounted() {

        var self = this

        $('.tooltip').tooltipster();
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

    
        Highcharts.chart('container',this.highchart_1);
        Highcharts.chart('container1',this.highchart_2);
        Highcharts.chart('container2',this.highchart_3);
        Highcharts.chart('container2_1',this.highchart_3_1);
      
    
        

    },
})