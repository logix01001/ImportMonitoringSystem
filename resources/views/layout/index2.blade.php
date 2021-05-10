


<!DOCTYPE html>

<html lang="en">

@include('includes.head2')

    <body class="" id="vueapp">

         <div >

            @include('includes.topnavbar2')

            @include('includes.content_title')


            <div class="vue_app_element wrapper  " style="height: auto;">
                

                    @section('body')
                        
                    @show
               
            </div>
            <br>
            <br>
            <br>
            @include('includes.footer2')

        @include('includes.script')
        
        @section('vuejsscript')
        @show

    </body>
</html>

<?php
$output = ob_get_contents();
ob_end_clean();

echo $output;
?>