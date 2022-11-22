<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width">
    <!-- Title -->
    <title>@yield('title')</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('assets/admin')}}/img/favicon.png">
    <!-- Font -->
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/google-fonts.css">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/theme.minc619.css?v=1.0">
    <!-- select picker -->
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/bootstrap-select.min.css"/>
    @stack('css_or_js')

    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css"/>

    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">
</head>

<body class="footer-offset">

{{--loader--}}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" class="d-none">
                <div class="loader-img">
                    <img width="200" src="{{asset('assets/admin/img/loader.gif')}}">
                </div>
            </div>
        </div>
    </div>
</div>
{{--loader--}}

<!-- JS Preview mode only -->
@include('layouts.admin.partials._header')
@include('layouts.admin.partials._sidebar')
<!-- END ONLY DEV -->

<main id="content" role="main" class="main pointer-event">
    <!-- Content -->
@yield('content')
<!-- End Content -->

    <!-- Footer -->
@include('layouts.admin.partials._footer')
<!-- End Footer -->

    <div class="modal fade" id="popup-modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <center>
                                <h2 class="title-new-order">
                                    <i class="tio-shopping-cart-outlined"></i> {{\App\CPU\translate('You_have_new_order,_Check_Please')}}.
                                </h2>
                                <hr>
                                <button onclick="check_order()" class="btn btn-primary">{{\App\CPU\translate('Ok,_let_me_check')}}</button>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
<!-- ========== END MAIN CONTENT ========== -->

<!-- ========== END SECONDARY CONTENTS ========== -->
<script src="{{asset('assets/admin')}}/js/custom.js"></script>
<!-- JS Implementing Plugins -->

@stack('script')

<!-- JS Front -->
<script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('assets/admin')}}/js/toastr.js"></script>
<!-- select picker -->
<script src="{{asset('assets/admin')}}/js/bootstrap-select.min.js"></script>
<!-- ck editor -->
<script src="{{asset('assets/admin')}}/js/ck-editor.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif
<!-- JS Plugins Init. -->
<script src="{{asset('assets/admin')}}/js/app-page.js"></script>

@stack('script_2')
<audio id="myAudio">
    <source src="{{asset('assets/admin/sound/notification.mp3')}}" type="audio/mpeg">
</audio>

</body>
</html>
