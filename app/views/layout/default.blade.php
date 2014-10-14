<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @section('title')
        	{{ Config::get('config.app-title') }}
        @show
    </title>
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="description" content="" />

    {{-- Mobile Specific Metas --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{{ asset('css/bootstrap.min.css') }}}">
    <link rel="stylesheet" href="{{{ asset('css/normalize.css') }}}">
    <link rel="stylesheet" href="{{{ asset('css/main.css') }}}">
    @yield('page-css')
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="shortcut icon" href="{{{ asset('favicon.ico') }}}" type="image/x-icon" />
</head>

<body>
<!--[if lt IE 7]>
    <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
            {{-- <div class="page-header text-center">
                @section('header')
                    @include('partials.banner')
                @show
            </div> --}}
            <div class="row content-top">
                @if(Session::has('notice'))
                    <div class="alert alert-success">
                        {{ Session::get('notice') }}
                    </div>
                @endif
                @if ( Session::get('error') )
                    <div class="alert alert-error alert-danger">
                        @if (is_array(Session::get('error')) )
                            {{ head(Session::get('error')) }}
                        @else
                            {{ Session::get('error') }}
                        @endif
                    </div>
                @endif
                @yield('content')
            </div>
            <div id="footer" class="footer-top">
            <br><hr>
                <div class="col-xs-12 col-md-12 text-right">
                    <p>© 2014 BridgeAsia Limited. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Javascripts --}}
<script src="{{{ asset('js/vendor/jquery-1.11.1.min.js') }}}"></script>
<script src="{{{ asset('js/bootstrap.min.js') }}}"></script>
<script src="{{{ asset('js/plugins.js') }}}"></script>
<script src="{{{ asset('js/main.js') }}}"></script>
@yield('page-js-script')
</body>
</html>
