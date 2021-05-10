

new Vue({
    el: '.vue_app_element',
    data: {
        summary : summary,
        date_filter: date_request,
        range_start : ' ',
        range_end : ' ',
        dateMonth: ' ',
        dateYear: ' ',
        search: '',
        factory: '',
        category: '',
        all: false,
        details_obj : [
            {
                bl_no_fk: '',
                container_number: '',
                actual_time_arrival: '',
                commodity : '',
                actual_discharge: '',
            }
        ],
        reference: 'D',
        last_reference: 'D',


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
        searchDate( date ){
            var self = this
            $.blockUI({ message: '<h3> Processing ...</h3>' });
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/search_date_tally_summary`,
            {
                reference: this.reference,
                date_filter : date,

            }).then(function(res){
                self.summary = res.data
                //console.log(res)
                $.unblockUI();

            }).catch(function(err){
                    console.log(err)
            })
        },
        searchRangeDate(){
            var self = this
            $.blockUI({ message: '<h3> Processing ...</h3>' });
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/search_date_tally_summary_range`,
            {

                range_start : this.range_start,
                range_end : this.range_end,

            }).then(function(res){
                self.summary = res.data
                self.last_reference = 'CR';
                $.unblockUI();
                //console.log(res)

            }).catch(function(err){
                    console.log(err)
            })
        },
        showDetails(factory,category,all = false){
            var self = this
            self.factory = factory;
            self.category = category;
            self.all = all;
            $("#detail_obj").dataTable().fnDestroy()
            //BLOCK THE PAGE SHOW PROCESSING MESSAGE
            $.blockUI({ message: '<h3> Processing ...</h3>' });
            axios.post(`http://${window.location.host}/${myBaseName}api/bill_of_lading/get_summary_tall_details`,
            {       factory: factory,
                    date_filter : this.date_filter,
                    category: category,
                    all: all,
                    reference: this.last_reference,
                    range_start : this.range_start,
                    range_end : this.range_end,
                    dateMonth : $('#date_month').val(),
                    dateYear : $('#date_year').val(),


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
                            //SEARCH NULL IN COLUMN
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
                this.searchDate($('#date_filter').val())
                this.last_reference = 'D';

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
                this.searchDate($('#date_month').val())
                this.last_reference = 'M';
               // this.searchDate($("#date_month").val() )

        });

        $("#date_year").datepicker( {
            format: "yyyy",
            startView: "years",
            minViewMode: "years"
        }).on(
            "changeDate", (e) => {
                this.dateYear = $('#date_year').val()
                this.searchDate($('#date_year').val())
                this.last_reference = 'Y';
               // this.searchDate($("#date_year").val() )
        });


    },
})
