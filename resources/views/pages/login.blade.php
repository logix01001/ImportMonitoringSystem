<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>IMAP - Import Monitoring at Port | Login</title>

    <link href="{{asset('template2/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('template2/font-awesome/css/font-awesome.css')}}" rel="stylesheet">

    <link href="{{asset('template2/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('template2/css/style.css')}}" rel="stylesheet">
<style>
canvas {
	cursor: crosshair;
	display: block;
}
canvas{
    position:absolute;top:0;left:0
    /* background-image: linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
    background-image: -o-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
    background-image: -moz-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
    background-image: -webkit-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
    background-image: -ms-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%); */

    /* background-image: -webkit-gradient(
        linear,
        left bottom,
        left top,
        color-stop(0, rgb(105,173,212)),
        color-stop(0.84, rgb(23,82,145))
    ); */

    /* background: -webkit-linear-gradient(45deg, #0077b5,#008891);
    background: -moz-linear-gradient(45deg, #0077b5,#008891);
    background: -o-linear-gradient(45deg, #0077b5,#008891);
    background: linear-gradient(45deg, #0077b5,#008891);
        background-color: rgba(0, 0, 0, 0);
        background-attachment: scroll;
    background-attachment: fixed;
    background-color: #0077b5; */
}
/*
#175291
*/

.bodypyro{

    /* background-image: linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
    background-image: -o-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
    background-image: -moz-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
    background-image: -webkit-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
    background-image: -ms-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);

    background-image: -webkit-gradient(
        linear,
        left bottom,
        left top,
        color-stop(0, rgb(105,173,212)),
        color-stop(0.84, rgb(23,82,145))
    ); */

    background: -webkit-linear-gradient(45deg, #0077b5,#008891);
    background: -moz-linear-gradient(45deg, #0077b5,#008891);
    background: -o-linear-gradient(45deg, #0077b5,#008891);
    background: linear-gradient(45deg, #0077b5,#008891);
    background-color: rgba(0, 0, 0, 0);


    background-color: #0077b5;
}

.bodypyro2{

/* background-image: linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
background-image: -o-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
background-image: -moz-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
background-image: -webkit-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);
background-image: -ms-linear-gradient(bottom, rgb(105,173,212) 0%, rgb(23,82,145) 84%);

background-image: -webkit-gradient(
    linear,
    left bottom,
    left top,
    color-stop(0, rgb(105,173,212)),
    color-stop(0.84, rgb(23,82,145))
); */

background: -webkit-linear-gradient(45deg, #203A43,#203A43);
background: -moz-linear-gradient(45deg,#203A43,#203A43);
background: -o-linear-gradient(45deg, #203A43,#203A43);
background: linear-gradient(45deg, #203A43,#203A43);
background-color: rgba(0, 0, 0, 0);


background-color: #0077b5;


}
.pyro > .before, .pyro > .after {

  position: absolute;

  width: 5px;
  height: 5px;
  border-radius: 50%;
  box-shadow: -120px -218.66667px blue, 248px -16.66667px #00ff84, 190px 16.33333px #002bff, -113px -308.66667px #ff009d, -109px -287.66667px #ffb300, -50px -313.66667px #ff006e, 226px -31.66667px #ff4000, 180px -351.66667px #ff00d0, -12px -338.66667px #00f6ff, 220px -388.66667px #99ff00, -69px -27.66667px #ff0400, -111px -339.66667px #6200ff, 155px -237.66667px #00ddff, -152px -380.66667px #00ffd0, -50px -37.66667px #00ffdd, -95px -175.66667px #a6ff00, -88px 10.33333px #0d00ff, 112px -309.66667px #005eff, 69px -415.66667px #ff00a6, 168px -100.66667px #ff004c, -244px 24.33333px #ff6600, 97px -325.66667px #ff0066, -211px -182.66667px #00ffa2, 236px -126.66667px #b700ff, 140px -196.66667px #9000ff, 125px -175.66667px #00bbff, 118px -381.66667px #ff002f, 144px -111.66667px #ffae00, 36px -78.66667px #f600ff, -63px -196.66667px #c800ff, -218px -227.66667px #d4ff00, -134px -377.66667px #ea00ff, -36px -412.66667px #ff00d4, 209px -106.66667px #00fff2, 91px -278.66667px #000dff, -22px -191.66667px #9dff00, 139px -392.66667px #a6ff00, 56px -2.66667px #0099ff, -156px -276.66667px #ea00ff, -163px -233.66667px #00fffb, -238px -346.66667px #00ff73, 62px -363.66667px #0088ff, 244px -170.66667px #0062ff, 224px -142.66667px #b300ff, 141px -208.66667px #9000ff, 211px -285.66667px #ff6600, 181px -128.66667px #1e00ff, 90px -123.66667px #c800ff, 189px 70.33333px #00ffc8, -18px -383.66667px #00ff33, 100px -6.66667px #ff008c;
  -moz-animation: 1s bang ease-out infinite backwards, 1s gravity ease-in infinite backwards, 5s position linear infinite backwards;
  -webkit-animation: 1s bang ease-out infinite backwards, 1s gravity ease-in infinite backwards, 5s position linear infinite backwards;
  -o-animation: 1s bang ease-out infinite backwards, 1s gravity ease-in infinite backwards, 5s position linear infinite backwards;
  -ms-animation: 1s bang ease-out infinite backwards, 1s gravity ease-in infinite backwards, 5s position linear infinite backwards;
  animation: 1s bang ease-out infinite backwards, 1s gravity ease-in infinite backwards, 5s position linear infinite backwards; }

.pyro > .after {
  -moz-animation-delay: 1.25s, 1.25s, 1.25s;
  -webkit-animation-delay: 1.25s, 1.25s, 1.25s;
  -o-animation-delay: 1.25s, 1.25s, 1.25s;
  -ms-animation-delay: 1.25s, 1.25s, 1.25s;
  animation-delay: 1.25s, 1.25s, 1.25s;
  -moz-animation-duration: 1.25s, 1.25s, 6.25s;
  -webkit-animation-duration: 1.25s, 1.25s, 6.25s;
  -o-animation-duration: 1.25s, 1.25s, 6.25s;
  -ms-animation-duration: 1.25s, 1.25s, 6.25s;
  animation-duration: 1.25s, 1.25s, 6.25s; }

@-webkit-keyframes bang {
  from {
    box-shadow: 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white; } }
@-moz-keyframes bang {
  from {
    box-shadow: 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white; } }
@-o-keyframes bang {
  from {
    box-shadow: 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white; } }
@-ms-keyframes bang {
  from {
    box-shadow: 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white; } }
@keyframes bang {
  from {
    box-shadow: 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white, 0 0 white; } }
@-webkit-keyframes gravity {
  to {
    transform: translateY(200px);
    -moz-transform: translateY(200px);
    -webkit-transform: translateY(200px);
    -o-transform: translateY(200px);
    -ms-transform: translateY(200px);
    opacity: 0; } }
@-moz-keyframes gravity {
  to {
    transform: translateY(200px);
    -moz-transform: translateY(200px);
    -webkit-transform: translateY(200px);
    -o-transform: translateY(200px);
    -ms-transform: translateY(200px);
    opacity: 0; } }
@-o-keyframes gravity {
  to {
    transform: translateY(200px);
    -moz-transform: translateY(200px);
    -webkit-transform: translateY(200px);
    -o-transform: translateY(200px);
    -ms-transform: translateY(200px);
    opacity: 0; } }
@-ms-keyframes gravity {
  to {
    transform: translateY(200px);
    -moz-transform: translateY(200px);
    -webkit-transform: translateY(200px);
    -o-transform: translateY(200px);
    -ms-transform: translateY(200px);
    opacity: 0; } }
@keyframes gravity {
  to {
    transform: translateY(200px);
    -moz-transform: translateY(200px);
    -webkit-transform: translateY(200px);
    -o-transform: translateY(200px);
    -ms-transform: translateY(200px);
    opacity: 0; } }
@-webkit-keyframes position {
  0%, 19.9% {
    margin-top: 10%;
    margin-left: 40%; }

  20%, 39.9% {
    margin-top: 40%;
    margin-left: 30%; }

  40%, 59.9% {
    margin-top: 20%;
    margin-left: 70%; }

  60%, 79.9% {
    margin-top: 30%;
    margin-left: 20%; }

  80%, 99.9% {
    margin-top: 30%;
    margin-left: 80%; } }
@-moz-keyframes position {
  0%, 19.9% {
    margin-top: 10%;
    margin-left: 40%; }

  20%, 39.9% {
    margin-top: 40%;
    margin-left: 30%; }

  40%, 59.9% {
    margin-top: 20%;
    margin-left: 70%; }

  60%, 79.9% {
    margin-top: 30%;
    margin-left: 20%; }

  80%, 99.9% {
    margin-top: 30%;
    margin-left: 80%; } }
@-o-keyframes position {
  0%, 19.9% {
    margin-top: 10%;
    margin-left: 40%; }

  20%, 39.9% {
    margin-top: 40%;
    margin-left: 30%; }

  40%, 59.9% {
    margin-top: 20%;
    margin-left: 70%; }

  60%, 79.9% {
    margin-top: 30%;
    margin-left: 20%; }

  80%, 99.9% {
    margin-top: 30%;
    margin-left: 80%; } }
@-ms-keyframes position {
  0%, 19.9% {
    margin-top: 10%;
    margin-left: 40%; }

  20%, 39.9% {
    margin-top: 40%;
    margin-left: 30%; }

  40%, 59.9% {
    margin-top: 20%;
    margin-left: 70%; }

  60%, 79.9% {
    margin-top: 30%;
    margin-left: 20%; }

  80%, 99.9% {
    margin-top: 30%;
    margin-left: 80%; } }
@keyframes position {
  0%, 19.9% {
    margin-top: 10%;
    margin-left: 40%; }

  20%, 39.9% {
    margin-top: 40%;
    margin-left: 30%; }

  40%, 59.9% {
    margin-top: 20%;
    margin-left: 70%; }

  60%, 79.9% {
    margin-top: 30%;
    margin-left: 20%; }

  80%, 99.9% {
    margin-top: 30%;
    margin-left: 80%; } }

  .body-manila {
    background-image: url( 'img/containers1.jpg ');
    background-size:     cover;                      /* <------ */
    background-repeat:   no-repeat;
    background-position: center center;
  }
</style>
</head>

<body class="gray-bg body-manila">

        {{-- SNOW --}}
		{{-- <canvas id="canvas"></canvas> --}}
        {{-- NEW YEAR --}}
        {{-- <canvas id="canvas">Canvas is not supported in your browser.</canvas> --}}
        {{-- <div class="pyro ">
            <div class="before"></div>
            <div class="after"></div>
        </div> --}}
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>

                <h1 class="logo-name"></h1>

            </div>


            @if (isset($IE_Detected))
            <div class="text-center animated fadeInDown alert" style="color:#73b8db">

                    <h2 class="font-bold">You are using Internet Explorer</h2>
                    <p> Please use a newer Browser like firefox or Chrome for better usage of the IMS.</p>
                   <hr>
                   <div class="col-lg-6 col-sm-6">
                    <a href="{{url('/download/69.0.3497.100_chrome_installer.exe')}}">
                        <img src="{{asset('img/chrome.png')}}" width="50"  alt="">
                    </a>
                   </div>
                   <div class="col-lg-6 col-sm-6">
                        <a href="{{url('/download/Firefox_setup_64.0.exe')}}">
                            <img src="{{asset('img/firefox.png')}}" width="50"  alt="">
                        </a>
                   </div>
            </div>

            @else

                <form class="form-login" action="{{route('login.do')}}">
                    @csrf
                    @method('POST')
                    <fieldset>
                        <!-- if there are login errors, show them here -->
                        @foreach ($errors->all() as $item)
                            <p class="alert alert-danger">
                                {{ $item }}

                            </p>
                        @endforeach

                        @if (session('loginIncorrect'))
                            <p class="alert alert-danger">
                                {{ session('loginIncorrect') }}
                            </p>
                        @endif

                        @if (Session::get('employee_code'))
                            {{Session::get('employee_code')}}
                        @endif



                        <legend style="color: #2591bf">
                            <img src="{{asset('img/logoims.png')}}"  width="150" alt="" srcset="">

                        </legend>

                        <div class="form-group">
                                <div class="input-group m-b"><span class="input-group-addon"><i class="fa fa-user"></i></span> <input type="text" class="form-control" name="username" placeholder="Employee Number"></div>
                        </div>
                        <div class="form-group form-actions">
                                <div class="input-group m-b"><span class="input-group-addon"><i class="fa fa-key"></i></span> <input type="password" class="form-control password" name="password" placeholder="Password"></div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-block pull-right">
                                Login <i class="fa fa-arrow-circle-right"></i>
                            </button>

                            <a href="/ims/index" class="btn btn-info btn-block pull-right">Enter as guest <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </fieldset>
                </form>
                @if (Session::has('errorMessage'))

                <div class="text-center animated fadeInDown" style="color:#73b8db">
                    <h1>403</h1>
                    <h3 class="font-bold">Access Denied</h3>

                    <div class="error-desc">
                        {{Session::get('errorMessage')}}
                    </div>
                </div>



                @endif


                <p class="m-t" style="color:#1ab394"> <small>System Team</small> </p>


            @endif




        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="{{asset('template2/js/jquery-2.1.1.js')}}"></script>
    <script src="{{asset('template2/js/bootstrap.min.js')}}"></script>
	<script>
	(function() {
    var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame ||
    function(callback) {
        window.setTimeout(callback, 1000 / 60);
    };
    window.requestAnimationFrame = requestAnimationFrame;
})();


// var flakes = [],
//     canvas = document.getElementById("canvas"),
//     ctx = canvas.getContext("2d"),
//     flakeCount = 400,
//     mX = -100,
//     mY = -100

//     canvas.width = window.innerWidth;
//     canvas.height = window.innerHeight;

// function snow() {
//     ctx.clearRect(0, 0, canvas.width, canvas.height);

//     for (var i = 0; i < flakeCount; i++) {
//         var flake = flakes[i],
//             x = mX,
//             y = mY,
//             minDist = 150,
//             x2 = flake.x,
//             y2 = flake.y;

//         var dist = Math.sqrt((x2 - x) * (x2 - x) + (y2 - y) * (y2 - y)),
//             dx = x2 - x,
//             dy = y2 - y;

//         if (dist < minDist) {
//             var force = minDist / (dist * dist),
//                 xcomp = (x - x2) / dist,
//                 ycomp = (y - y2) / dist,
//                 deltaV = force / 2;

//             flake.velX -= deltaV * xcomp;
//             flake.velY -= deltaV * ycomp;

//         } else {
//             flake.velX *= .98;
//             if (flake.velY <= flake.speed) {
//                 flake.velY = flake.speed
//             }
//             flake.velX += Math.cos(flake.step += .05) * flake.stepSize;
//         }

//         ctx.fillStyle = "rgba(255,255,255," + flake.opacity + ")";
//         flake.y += flake.velY;
//         flake.x += flake.velX;

//         if (flake.y >= canvas.height || flake.y <= 0) {
//             reset(flake);
//         }


//         if (flake.x >= canvas.width || flake.x <= 0) {
//             reset(flake);
//         }

//         ctx.beginPath();
//         ctx.arc(flake.x, flake.y, flake.size, 0, Math.PI * 2);
//         ctx.fill();
//     }
//     requestAnimationFrame(snow);
// };

// function reset(flake) {
//     flake.x = Math.floor(Math.random() * canvas.width);
//     flake.y = 0;
//     flake.size = (Math.random() * 3) + 2;
//     flake.speed = (Math.random() * 1) + 0.5;
//     flake.velY = flake.speed;
//     flake.velX = 0;
//     flake.opacity = (Math.random() * 0.5) + 0.3;
// }

// function init() {
//     for (var i = 0; i < flakeCount; i++) {
//         var x = Math.floor(Math.random() * canvas.width),
//             y = Math.floor(Math.random() * canvas.height),
//             size = (Math.random() * 3) + 2,
//             speed = (Math.random() * 1) + 0.5,
//             opacity = (Math.random() * 0.5) + 0.3;

//         flakes.push({
//             speed: speed,
//             velY: speed,
//             velX: 0,
//             x: x,
//             y: y,
//             size: size,
//             stepSize: (Math.random()) / 30,
//             step: 0,
//             opacity: opacity
//         });
//     }

//     snow();
// };

// canvas.addEventListener("mousemove", function(e) {
//     mX = e.clientX,
//     mY = e.clientY
// });

// window.addEventListener("resize",function(){
//     canvas.width = window.innerWidth;
//     canvas.height = window.innerHeight;
// })

// init();
// when animating on canvas, it is best to use requestAnimationFrame instead of setTimeout or setInterval
// not supported in all browsers though and sometimes needs a prefix, so we need a shim
window.requestAnimFrame = ( function() {
	return window.requestAnimationFrame ||
				window.webkitRequestAnimationFrame ||
				window.mozRequestAnimationFrame ||
				function( callback ) {
					window.setTimeout( callback, 1000 / 60 );
				};
})();

// now we will setup our basic variables for the demo
var canvas = document.getElementById( 'canvas' ),
		ctx = canvas.getContext( '2d' ),
		// full screen dimensions
		cw = window.innerWidth,
		ch = window.innerHeight,
		// firework collection
		fireworks = [],
		// particle collection
		particles = [],
		// starting hue
		hue = 120,
		// when launching fireworks with a click, too many get launched at once without a limiter, one launch per 5 loop ticks
		limiterTotal = 5,
		limiterTick = 0,
		// this will time the auto launches of fireworks, one launch per 80 loop ticks
		timerTotal = 80,
		timerTick = 0,
		mousedown = false,
		// mouse x coordinate,
		mx,
		// mouse y coordinate
		my;

// set canvas dimensions
canvas.width = cw;
canvas.height = ch;

// now we are going to setup our function placeholders for the entire demo

// get a random number within a range
function random( min, max ) {
	return Math.random() * ( max - min ) + min;
}

// calculate the distance between two points
function calculateDistance( p1x, p1y, p2x, p2y ) {
	var xDistance = p1x - p2x,
			yDistance = p1y - p2y;
	return Math.sqrt( Math.pow( xDistance, 2 ) + Math.pow( yDistance, 2 ) );
}

// create firework
function Firework( sx, sy, tx, ty ) {
	// actual coordinates
	this.x = sx;
	this.y = sy;
	// starting coordinates
	this.sx = sx;
	this.sy = sy;
	// target coordinates
	this.tx = tx;
	this.ty = ty;
	// distance from starting point to target
	this.distanceToTarget = calculateDistance( sx, sy, tx, ty );
	this.distanceTraveled = 0;
	// track the past coordinates of each firework to create a trail effect, increase the coordinate count to create more prominent trails
	this.coordinates = [];
	this.coordinateCount = 3;
	// populate initial coordinate collection with the current coordinates
	while( this.coordinateCount-- ) {
		this.coordinates.push( [ this.x, this.y ] );
	}
	this.angle = Math.atan2( ty - sy, tx - sx );
	this.speed = 2;
	this.acceleration = 1.05;
	this.brightness = random( 50, 70 );
	// circle target indicator radius
	this.targetRadius = 1;
}

// update firework
Firework.prototype.update = function( index ) {
	// remove last item in coordinates array
	this.coordinates.pop();
	// add current coordinates to the start of the array
	this.coordinates.unshift( [ this.x, this.y ] );

	// cycle the circle target indicator radius
	if( this.targetRadius < 8 ) {
		this.targetRadius += 0.3;
	} else {
		this.targetRadius = 1;
	}

	// speed up the firework
	this.speed *= this.acceleration;

	// get the current velocities based on angle and speed
	var vx = Math.cos( this.angle ) * this.speed,
			vy = Math.sin( this.angle ) * this.speed;
	// how far will the firework have traveled with velocities applied?
	this.distanceTraveled = calculateDistance( this.sx, this.sy, this.x + vx, this.y + vy );

	// if the distance traveled, including velocities, is greater than the initial distance to the target, then the target has been reached
	if( this.distanceTraveled >= this.distanceToTarget ) {
		createParticles( this.tx, this.ty );
		// remove the firework, use the index passed into the update function to determine which to remove
		fireworks.splice( index, 1 );
	} else {
		// target not reached, keep traveling
		this.x += vx;
		this.y += vy;
	}
}

// draw firework
Firework.prototype.draw = function() {
	ctx.beginPath();
	// move to the last tracked coordinate in the set, then draw a line to the current x and y
	ctx.moveTo( this.coordinates[ this.coordinates.length - 1][ 0 ], this.coordinates[ this.coordinates.length - 1][ 1 ] );
	ctx.lineTo( this.x, this.y );
	ctx.strokeStyle = 'hsl(' + hue + ', 100%, ' + this.brightness + '%)';
	ctx.stroke();

	ctx.beginPath();
	// draw the target for this firework with a pulsing circle
	ctx.arc( this.tx, this.ty, this.targetRadius, 0, Math.PI * 2 );
	ctx.stroke();
}

// create particle
function Particle( x, y ) {
	this.x = x;
	this.y = y;
	// track the past coordinates of each particle to create a trail effect, increase the coordinate count to create more prominent trails
	this.coordinates = [];
	this.coordinateCount = 5;
	while( this.coordinateCount-- ) {
		this.coordinates.push( [ this.x, this.y ] );
	}
	// set a random angle in all possible directions, in radians
	this.angle = random( 0, Math.PI * 2 );
	this.speed = random( 1, 10 );
	// friction will slow the particle down
	this.friction = 0.95;
	// gravity will be applied and pull the particle down
	this.gravity = 1;
	// set the hue to a random number +-50 of the overall hue variable
	this.hue = random( hue - 50, hue + 50 );
	this.brightness = random( 50, 80 );
	this.alpha = 1;
	// set how fast the particle fades out
	this.decay = random( 0.015, 0.03 );
}

// update particle
Particle.prototype.update = function( index ) {
	// remove last item in coordinates array
	this.coordinates.pop();
	// add current coordinates to the start of the array
	this.coordinates.unshift( [ this.x, this.y ] );
	// slow down the particle
	this.speed *= this.friction;
	// apply velocity
	this.x += Math.cos( this.angle ) * this.speed;
	this.y += Math.sin( this.angle ) * this.speed + this.gravity;
	// fade out the particle
	this.alpha -= this.decay;

	// remove the particle once the alpha is low enough, based on the passed in index
	if( this.alpha <= this.decay ) {
		particles.splice( index, 1 );
	}
}

// draw particle
Particle.prototype.draw = function() {
	ctx. beginPath();
	// move to the last tracked coordinates in the set, then draw a line to the current x and y
	ctx.moveTo( this.coordinates[ this.coordinates.length - 1 ][ 0 ], this.coordinates[ this.coordinates.length - 1 ][ 1 ] );
	ctx.lineTo( this.x, this.y );
	ctx.strokeStyle = 'hsla(' + this.hue + ', 100%, ' + this.brightness + '%, ' + this.alpha + ')';
	ctx.stroke();
}

// create particle group/explosion
function createParticles( x, y ) {
	// increase the particle count for a bigger explosion, beware of the canvas performance hit with the increased particles though
	var particleCount = 30;
	while( particleCount-- ) {
		particles.push( new Particle( x, y ) );
	}
}

// main demo loop
function loop() {
	// this function will run endlessly with requestAnimationFrame
	requestAnimFrame( loop );

	// increase the hue to get different colored fireworks over time
	//hue += 0.5;

  // create random color
  hue= random(0, 360 );

	// normally, clearRect() would be used to clear the canvas
	// we want to create a trailing effect though
	// setting the composite operation to destination-out will allow us to clear the canvas at a specific opacity, rather than wiping it entirely
	ctx.globalCompositeOperation = 'destination-out';
	// decrease the alpha property to create more prominent trails
	ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
	ctx.fillRect( 0, 0, cw, ch );
	// change the composite operation back to our main mode
	// lighter creates bright highlight points as the fireworks and particles overlap each other
	ctx.globalCompositeOperation = 'lighter';

	// loop over each firework, draw it, update it
	var i = fireworks.length;
	while( i-- ) {
		fireworks[ i ].draw();
		fireworks[ i ].update( i );
	}

	// loop over each particle, draw it, update it
	var i = particles.length;
	while( i-- ) {
		particles[ i ].draw();
		particles[ i ].update( i );
	}

	// launch fireworks automatically to random coordinates, when the mouse isn't down
	if( timerTick >= timerTotal ) {
		if( !mousedown ) {
			// start the firework at the bottom middle of the screen, then set the random target coordinates, the random y coordinates will be set within the range of the top half of the screen
			fireworks.push( new Firework( cw / 2, ch, random( 0, cw ), random( 0, ch / 2 ) ) );
			timerTick = 0;
		}
	} else {
		timerTick++;
	}

	// limit the rate at which fireworks get launched when mouse is down
	if( limiterTick >= limiterTotal ) {
		if( mousedown ) {
			// start the firework at the bottom middle of the screen, then set the current mouse coordinates as the target
			fireworks.push( new Firework( cw / 2, ch, mx, my ) );
			limiterTick = 0;
		}
	} else {
		limiterTick++;
	}
}

// mouse event bindings
// update the mouse coordinates on mousemove
canvas.addEventListener( 'mousemove', function( e ) {
	mx = e.pageX - canvas.offsetLeft;
	my = e.pageY - canvas.offsetTop;
});

// toggle mousedown state and prevent canvas from being selected
canvas.addEventListener( 'mousedown', function( e ) {
	e.preventDefault();
	mousedown = true;
});

canvas.addEventListener( 'mouseup', function( e ) {
	e.preventDefault();
	mousedown = false;
});

// once the window loads, we are ready for some fireworks!
window.onload = loop;



	</script>
</body>

</html>

