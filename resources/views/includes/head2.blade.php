<!--<![endif]-->
<!-- start: HEAD -->

<head>
    <title>IMS - Import Monitoring System</title>
    <!-- start: META -->
    <!--[if IE]><meta http-equiv='X-UA-Compatible' content="IE=edge,IE=9,IE=8,chrome=1" /><![endif]-->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <!-- end: META -->
    <!-- start: GOOGLE FONTS -->
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic"
        rel="stylesheet" type="text/css" />
    <!-- end: GOOGLE FONTS -->
    <!-- start: MAIN CSS -->

    <link href="{{asset('template2/css/bootstrap.min.css')}}" rel="stylesheet">
    {{--
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU"
        crossorigin="anonymous"> --}}

    <link href="{{asset('template2/fontawesome-free-5.5.0-web/css/all.min.css')}}" rel="stylesheet">
    {{--
    <link href="{{asset('template2/font-awesome/css/font-awesome.css')}}" rel="stylesheet"> --}}
    {{--
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU"
        crossorigin="anonymous"> --}}
    <link href="{{asset('template2/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
    <link href="{{asset('template2/css/animate.css')}}" rel="stylesheet">


    <link href="{{asset('template2/css/plugins/footable/footable.core.css')}}" rel="stylesheet">
    {{--
    <link href="{{asset('template2/assets/css/footable.bootstrap.min.css')}}" rel="stylesheet"> --}}


    <link href="{{asset('template2/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('template2/assets/css/iziToast.min.css')}}" rel="stylesheet" media="screen">
    <link href="{{asset('template2/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css')}}" rel="stylesheet">
    <!-- end: MAIN CSS -->
    <link href="{{asset('template2/assets/js/robinselect2/select2.css')}}" rel="stylesheet" media="screen">
    <link href="{{asset('template2/assets/js/bootstrap-datepicker/bootstrap-datepicker3.standalone.min.css')}}" rel="stylesheet"
        media="screen">
    <link rel="stylesheet" href="{{asset('template2/css/jqueryconfirm.min.css')}}">




        {{-- <link rel="stylesheet" href="{{asset('template2/assets/css/jquery.dataTables.min.css')}}"> --}}
    <link rel="stylesheet" href="{{asset('css/jquery.dataTables.min.css')}}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">



    <link rel="stylesheet" type="text/css" href="{{asset('template2/assets/css/tables/handsontable/handsontable.full.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('template2/assets/css/tables/handsontable/jsgrid-theme.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('template2/assets/css/tables/handsontable/jsgrid.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('template2/assets/css/plugins/tables/handsontable.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('template2/assets/js/tabulator-master/dist/css/tabulator.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('template2/assets/js/switchery-master/dist/switchery.min.css')}}">

    {{--
    <link rel="stylesheet" type="text/css" href="{{asset('template2/assets/js/tabulator-master/dist/css/tabulator_midnight.css')}}">
    --}}


    <!-- Toastr style -->
    <link href="{{asset('template2/css/plugins/toastr/toastr.min.css')}}" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- end: CLIP-TWO CSS -->
    <!-- start: CSS REQUIRED FOR THIS PAGE ONLY -->
    <!-- end: CSS REQUIRED FOR THIS PAGE ONLY -->

    @hasSection('headscript')
    @section('headscript')

    @show
    @else

    @endif
    <link href="{{asset('css/custom.css')}}" rel="stylesheet">
    <script>
        window.myBaseName = 'ims_dev/';
    </script>

</head>
<!-- end: HEAD -->
