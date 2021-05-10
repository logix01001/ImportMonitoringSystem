<!DOCTYPE html>
<!-- Template Name: Clip-Two - Responsive Admin Template build with Twitter Bootstrap 3.x | Author: ClipTheme -->
<!--[if IE 8]><html class="ie8" lang="en"><![endif]-->
<!--[if IE 9]><html class="ie9" lang="en"><![endif]-->
<!--[if !IE]><!-->
<html lang="en">

     @include('includes.head')

	<body id='vueapp'>
        {{-- Begin APP --}}
		<div id="app">
			
            @include('includes.sidebar')

			<div class="app-content">
                 
                @include('includes.topnavbar')

				<div class="main-content" >
					<div class="wrap-content container" id="container">
                        {{-- Content Start Here --}}
                        
						@section('body')
							
						@show


                        
                        {{-- Content End's Here --}}
					</div>
				</div>
			</div>
			
			@include('includes.footer')
			

		</div>
		{{-- End APP --}}
		@include('includes.script')
		@section('vuejsscript')
			
		@show

	</body>
</html>
