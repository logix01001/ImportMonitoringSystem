

new Vue({
    el: '.vue_app_element',
    data: {
        containers_tally: summary.containers_tally,
        dismounted : summary.dismounted,
        date_filter: date_request,
        reference: 'D',
      
    },
    methods: {
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
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/search_date_tally_breakdown`,
            {
                    reference: this.reference,
                    date_filter : date,

            }).then(function(res){
                self.containers_tally = res.data.containers_tally
                self.dismounted = res.data.dismounted
                
            }).catch(function(err){
                    console.log(err)
            })
        }
    },
    mounted() {
        $('#date_filter').datepicker({
            format: "yyyy-mm-dd",clearBtn: true
        }).on(
            "changeDate", (e) => {
         
           
                this.searchDate( $('#date_filter').val() )
              
        })

        $("#date_month").datepicker( {
            format: "yyyy-mm",
            startView: "months", 
            minViewMode: "months"
        }).on(
            "changeDate", (e) => {
         
              
                this.searchDate($("#date_month").val() )
              
        });

        $("#date_year").datepicker( {
            format: "yyyy",
            startView: "years", 
            minViewMode: "years"
        }).on(
            "changeDate", (e) => {
                this.searchDate($("#date_year").val() )
        });
    },
})