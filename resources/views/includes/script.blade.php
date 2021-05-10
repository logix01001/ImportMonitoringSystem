<script>
    window.pwdchp = "{{Session::get('password')}}"
</script>
<!-- start: MAIN JAVASCRIPTS -->

<!-- Mainly scripts -->
<script src="{{asset('template2/js/jquery-2.1.1.js')}}"></script>
<script src="{{asset('template2/assets/Inputmask/dist/jquery.inputmask.bundle.js')}}"></script>




<script src="{{asset('template2/js/bootstrap.min.js')}}"></script>
<script src="{{asset('template2/assets/js/jquery.sticky.js')}}"></script>
<script src="{{asset('template2/assets/js/jquery.dataTables.min.js')}}"></script>
{{-- <script src="{{asset('template2/assets/js/jquery.dataTablesFixedColumn.min.js')}}"></script> --}}
<script src="{{asset('js/dataTables.fixedColumns.min.js')}}"></script>
<script src="{{asset('template2/assets/js/JavaScript-MD5-master/js/md5.min.js')}}"></script>
<script src="{{asset('template2/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
<script src="{{asset('template2/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('template2/assets/js/iziToast.min.js')}}"></script>
<script src="{{asset('template2/assets/js/highcharts/highcharts.js')}}"></script>
<script src="{{asset('template2/assets/js/highcharts/data.js')}}"></script>
<script src="{{asset('template2/assets/js/highcharts/drilldown.js')}}"></script>
<script src="{{asset('template2/js/jquery-confirm.min.js')}}"></script>
<script src="{{asset('template2/assets/js/vuelidate-master/dist/vuelidate.min.js')}}"></script>
<!-- The builtin validators is added by adding the following line. -->
<script src="{{asset('template2/assets/js/vuelidate-master/dist/validators.min.js')}}"></script>



<script src="{{asset('template2/assets/js/vue.min.js')}}"></script>
<script src="{{asset('template2/assets/js/axios.min.js')}}"></script>
<script src="{{asset('js/clipboard.min.js')}}" ></script>
<script>
    Vue.config.devtools = true
    </script>

<script type="text/javascript" src="{{asset('template2/assets/js/lodash.min.js')}}"></script>
<script src="{{asset('template2/assets/js/sweetalert.min.js')}}"></script>
<!-- FooTable -->
<script src="{{asset('template2/js/plugins/footable/footable.all.min.js')}}"></script>
{{-- <script src="{{asset('template2/assets/js/footable.min.js')}}"></script> --}}

<!-- end: MAIN JAVASCRIPTS -->
<!-- start: CLIP-TWO JAVASCRIPTS -->
<!-- Mainly scripts -->
{{-- <script src="{{asset('template2/js/plugins/summernote/summernote.min.js')}}"></script> --}}

<!-- Custom and plugin javascript -->
<script src="{{asset('template2/js/inspinia.js')}}"></script>
<script src="{{asset('template2/js/plugins/pace/pace.min.js')}}"></script>
<script src="{{asset('template2/assets/js/robinselect2/select2.min.js')}}"></script>
<script src="{{asset('template2/assets/js/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
{{-- <script src="{{asset('js/moment.min.js')}}"></script> --}}
{{-- <script src="{{asset('js/daterangepicker.js')}}"></script> --}}
{{-- <script src="{{asset('template2/assets/js/tables/handsontable/handsontable.full.js')}}" type="text/javascript"></script>
<script src="{{asset('template2/assets/js/tables/handsontable/jsgrid.min.js')}}" type="text/javascript"></script>
<script src="{{asset('template2/assets/js/tables/handsontable/languages.min.js')}}"></script>
<script src="{{asset('template2/assets/js/scripts/tables/handsontable/handsontable-appearance.js')}}"></script> --}}
<script src="{{asset('template2/assets/js/tableHeadFixer.js')}}"></script>

<script src="{{asset('template2/assets/js/sticky_new.js')}}"></script>
<script src="{{asset('template2/assets/js/tabulator-master/dist/js/tabulator.min.js')}}"></script>
<script src="{{asset('template2/assets/js/switchery-master/dist/switchery.min.js')}}"></script>
<script src="{{asset('template2/assets/js/fixed-table-js-master/dist/fixed_table.min.js')}}"></script>


<!-- start: JavaScript Event Handlers for this page -->
<script>
    // var d = new Date();
    // d.setDate(d.getDate() + (2  - d.getDay()) % 7);
    // window.tuesday = (d.getMonth()+1).toString().padStart(2, '0').slice(-2) + "/" + d.getDate().toString().padStart(2, '0').slice(-2) + "/" + d.getFullYear();
</script>
<!-- iCheck -->
<script src="{{asset('template2/js/plugins/iCheck/icheck.min.js')}}"></script>
{{-- <script src="{{asset('/js/vuejs/topnavbar.js')}}"></script> --}}
{{--<script src="{{asset('template2/assets/js/inputmask/dist/inputmask/inputmask.js')}}"></script>
<script src="{{asset('template2/assets/js/inputmask/dist/inputmask/jquery.inputmask.js')}}"></script> --}}

{{-- <script src="{{asset('template2/assets/js/inputmask/dist/jquery.inputmask.bundle')}}"></script> --}}

{{-- <script src="{{asset('template2/assets/js/inputmask/dist/inputmask/bindings/inputmask.binding.js')}}"></script>
--}}

<!-- Toastr -->
<script src="{{asset('template2/js/plugins/toastr/toastr.min.js')}}"></script>
<script src="{{ asset('js/blockui.js') }}"></script>
<script>
    jQuery(document).ready(function() {

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });


        $('.datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            dateFormat: 'yyyy-mm-dd'
        });




    });
</script>

<script>
   $("#side-menu a").each(function () {
        if (this.href == window.location.href) {
            $(this).addClass("active");
            $(this).parent().addClass("active"); // add active to li of the current link
            $(this).parent().parent().addClass("in");
            $(this).parent().parent().prev().addClass("active"); // add active class to an anchor
            $(this).parent().parent().parent().addClass("active");
            $(this).parent().parent().parent().parent().addClass("in"); // add active to li of the current link
            $(this).parent().parent().parent().parent().parent().addClass("active");
        }
    });
    new Vue({
        el: '#change_password',
        data: {
            password: pwdchp,
            confirm_password: '',
            new_password: '',
            old_password: '',
            complete: false,
            updated: false,
        },
        methods: {
            check_password() {
                if (this.password === md5(this.old_password)) {
                    this.complete = true
                } else {
                    this.complete = false
                    return false;
                }
            },
            changepassword() {
                var self = this

                axios.post(`http://${window.location.host}/change_password`, {
                    newpassword: self.new_password
                }).then(function(res) {
                    self.updated = true
                    console.log(res.data)
                }).catch(function(err) {
                    console.log(err)
                })
            }
        }

    })
</script>
<!-- Scripts -->
<script>
    window.Laravel = @php  echo json_encode([
            'csrfToken' => csrf_token(),
        ]); @endphp

    window.axios.defaults.headers.common = {
        'X-CSRF-TOKEN': window.Laravel.csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
    };
</script>
<!-- end: JavaScript Event Handlers for this page -->
<!-- end: CLIP-TWO JAVASCRIPTS -->
