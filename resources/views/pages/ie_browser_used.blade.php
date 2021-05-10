<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Please download Firefox or Chrome</title>

    <link href="{{asset('template2/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('template2/font-awesome/css/font-awesome.css')}}" rel="stylesheet">

    <link href="{{asset('template2/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('template2/css/style.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="gray-bg">


    <div class="middle-box text-center animated fadeInDown">
        <h1>IE</h1>
        <h2 class="font-bold">Browser Detected</h2>
        <h3 class="font-bold">IMS System Message</h3>

        <div class="error-desc">
                Please use a newer Browser like firefox or Chrome for better usage of the IMS.
        </div>
        <div>
                <a href={{url('/download/69.0.3497.100_chrome_installer.exe') }} > CHROME </a>
        </div>
    </div>


    
    <!-- Mainly scripts -->

    <script src="{{asset('template2/js/jquery-2.1.1.js')}}"></script>

    <script src="{{asset('template2/js/bootstrap.min.js')}}"></script>
	<script>
			window.Laravel = {!! json_encode([
				'csrfToken' => csrf_token(),
			]) !!};

			window.axios.defaults.headers.common = {
				'X-CSRF-TOKEN': window.Laravel.csrfToken,
				'X-Requested-With': 'XMLHttpRequest'
			};

		</script>
</body>

</html>
