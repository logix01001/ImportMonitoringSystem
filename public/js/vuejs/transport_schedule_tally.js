new Vue({
    el: '.vue_app_element',
    data: {
        data: data,
        factories: factories,
        range_start: '',
        range_end: '',
        today: today,
        port: ['SOUTH','NORTH']

    },
    computed: {



    },
    created(){

    },
    watch:{

    },
    methods: {
        copydata(){


            iziToast.success({
                title: 'Copied',
                message: 'Transport schedule tally copied.',
            });
        },
        selectElementContents(el) {

            var body = document.body, range, sel;
            if (document.createRange && window.getSelection) {
                range = document.createRange();
                sel = window.getSelection();
                sel.removeAllRanges();
                try {
                    range.selectNodeContents(el);
                    sel.addRange(range);
                } catch (e) {
                    range.selectNode(el);
                    sel.addRange(range);
                }
            } else if (body.createTextRange) {
                range = body.createTextRange();
                range.moveToElementText(el);
                range.select();

            }




        },
        // selectElementContents(el) {
        //     var body = document.body, range, sel;
        //     if (document.createRange && window.getSelection) {

        //         range = document.createRange();
        //         sel = window.getSelection();
        //         sel.removeAllRanges();
        //         try {
        //             range.selectNodeContents(el);
        //             sel.addRange(range);
        //         } catch (e) {
        //             range.selectNode(el);
        //             sel.addRange(range);
        //         }

        //     } else if (body.createTextRange) {
        //         range = body.createTextRange();
        //         range.moveToElementText(el);
        //         range.select();
        //     }

        //     //document.execCommand("copy")


        // }
        // searchRangeDate(){
        //     var self = this
        //     $.blockUI({ message: '<h3> Processing ...</h3>' });
        //     axios.post(`http://${window.location.host}/${myBaseName}bill_of_lading/api_transport_schedule_tally`,
        //     {

        //         range_start : this.range_start,
        //         range_end : this.range_end,

        //     }).then(function(res){

        //         self.data = res.data

        //         $.unblockUI();
        //         //console.log(res)

        //     }).catch(function(err){
        //             console.log(err)
        //     })
        // },

    },
    mounted() {
        var self = this


        var clipboard = new Clipboard('.download');

        clipboard.on('success', function(e) {


            e.clearSelection();
        });

        clipboard.on('error', function(e) {

        });


    setTimeout(() => {



        $('#tablediv').on('scroll',function(e){

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
                    'z-index':3,
                    '-webkit-box-shadow': '',
                    'box-shadow': '',
                })
                $('.stickycolumn2').css({
                    'background-color': 'transparent ',
                    'color':'#676a6c',
                    'left':0,
                    'z-index':3,
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
                    'left':81,
                    'z-index':3
                })
                $('.stickycolumn2').css({
                    // 'background': 'rgb(47,64,80,0.8)',
                    'background': '#f5f5f6',
                    '-webkit-box-shadow': '10px 0 5px -2px #888',
                    'box-shadow': '10px 0 5px -2px #888',
                    'color':'#676a6c',
                    'left':162,
                    'z-index':3
                })

            }
        })
    }, 1000);

        // $('.input-daterange').datepicker({

        //     format: "yyyy-mm-dd",
        //     keyboardNavigation: false,
        //     forceParse: false,
        //     autoclose: true

        // }).on(
        //     "changeDate", (e) => {

        //         self.range_start = $('#range_start').val()
        //         self.range_end = $('#range_end').val()



        // });
    },
})
