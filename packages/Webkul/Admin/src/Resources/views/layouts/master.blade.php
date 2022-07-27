<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <title>@yield('page_title')</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if ($favicon = core()->getConfigData('general.design.admin_logo.favicon', core()->getCurrentChannelCode()))
            <link rel="icon" sizes="16x16" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}" />
        @else
            <link rel="icon" sizes="16x16" href="{{ asset('vendor/webkul/ui/assets/images/favicon.ico') }}" />
        @endif

        <link rel="stylesheet" href="{{ asset('vendor/webkul/ui/assets/css/ui.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/webkul/admin/assets/css/admin.css') }}">

        @yield('head')
        <style>
            .content-container .content .page-header {
               top:120px;
            }
            .content-container .content .page-content {
               margin-top:30px;
            }
            .navbar-top .navbar-top-right .profile-info{
                margin: 0 20px 0 25px;
            }
            .topmenu, .topmenu a:link {
            text-decoration: none;
            color: #000000;
            }
            .topmenu.active, .topmenu.active a:link, .topmenu a:active, .topmenu a:focus, .topmenu a:hover, .topmenu a:visited{
            text-decoration: none;
            color: #0041ff;
            }
            
        </style>
        @yield('css')
        
        {!! view_render_event('bagisto.admin.layout.head') !!}
            <!-- SKP Strt Need for top menu and datatable-->
            @if (strpos(Request::url(),'wallet')!==false || strpos(Request::url(),'survey')!==false || strpos(Request::url(),'society')!==false || strpos(Request::url(),'cms/category')!==false)
           
            @else
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
            @endif
           </head>

    <body @if (core()->getCurrentLocale() && core()->getCurrentLocale()->direction == 'rtl') class="rtl" @endif style="scroll-behavior: smooth;">
        {!! view_render_event('bagisto.admin.layout.body.before') !!}

        <div id="app">

            <flash-wrapper ref='flashes'></flash-wrapper>

            {!! view_render_event('bagisto.admin.layout.nav-top.before') !!}

            {{-- @include ('admin::layouts.nav-top') SKP--}}

            {!! view_render_event('bagisto.admin.layout.nav-top.after') !!}


            {!! view_render_event('bagisto.admin.layout.nav-left.before') !!}

            @include ('admin::layouts.nav-left',['place'=>'top'])

            {!! view_render_event('bagisto.admin.layout.nav-left.after') !!}

            
            <div class="content-container"  style="padding-left: 0px;">

                {!! view_render_event('bagisto.admin.layout.content.before') !!}

                @yield('content-wrapper')

                {!! view_render_event('bagisto.admin.layout.content.after') !!}

            </div>

        </div>

        <script type="text/javascript">
            window.flashMessages = [];

            @foreach (['success', 'warning', 'error', 'info'] as $key)
                @if ($value = session($key))
                    window.flashMessages.push({'type': 'alert-{{ $key }}', 'message': "{{ $value }}" });
                @endif
            @endforeach

            window.serverErrors = [];
            @if (isset($errors))
                @if (count($errors))
                    window.serverErrors = @json($errors->getMessages());
                @endif
            @endif
        </script>

        <script type="text/javascript" src="{{ asset('vendor/webkul/admin/assets/js/admin.js') }}"></script>
        <script type="text/javascript" src="{{ asset('vendor/webkul/ui/assets/js/ui.js') }}"></script>
        <script type="text/javascript">
            window.addEventListener('DOMContentLoaded', function() {
                moveDown = 60;
                moveUp =  -60;
                count = 0;
                countKeyUp = 0;
                pageDown = 60;
                pageUp = -60;
                scroll = 0;

                listLastElement = $('.menubar li:last-child').offset();

                if (listLastElement) {
                    lastElementOfNavBar = listLastElement.top;
                }

                navbarTop = $('.navbar-left').css("top");
                menuTopValue = $('.navbar-left').css('top');
                menubarTopValue = menuTopValue;

                documentHeight = $(document).height();
                menubarHeight = $('ul.menubar').height();
                navbarHeight = $('.navbar-left').height();
                windowHeight = $(window).height();
                contentHeight = $('.content').height();
                innerSectionHeight = $('.inner-section').height();
                gridHeight = $('.grid-container').height();
                pageContentHeight = $('.page-content').height();

                if (menubarHeight <= windowHeight) {
                    differenceInHeight = windowHeight - menubarHeight;
                } else {
                    differenceInHeight = menubarHeight - windowHeight;
                }

                if (menubarHeight > windowHeight) {
                    document.addEventListener("keydown", function(event) {
                        if ((event.keyCode == 38) && count <= 0) {
                            count = count + moveDown;

                            $('.navbar-left').css("top", count + "px");
                        } else if ((event.keyCode == 40) && count >= -differenceInHeight) {
                            count = count + moveUp;

                            $('.navbar-left').css("top", count + "px");
                        } else if ((event.keyCode == 33) && countKeyUp <= 0) {
                            countKeyUp = countKeyUp + pageDown;

                            $('.navbar-left').css("top", countKeyUp + "px");
                        } else if ((event.keyCode == 34) && countKeyUp >= -differenceInHeight) {
                            countKeyUp = countKeyUp + pageUp;

                            $('.navbar-left').css("top", countKeyUp + "px");
                        } else {
                            $('.navbar-left').css("position", "fixed");
                        }
                    });

                    $("body").css({minHeight: $(".menubar").outerHeight() + 100 + "px"});

                    window.addEventListener('scroll', function() {
                        documentScrollWhenScrolled = $(document).scrollTop();

                            if (documentScrollWhenScrolled <= differenceInHeight + 200) {
                                $('.navbar-left').css('top', -documentScrollWhenScrolled + 60 + 'px');
                                scrollTopValueWhenNavBarFixed = $(document).scrollTop();
                            }
                    });
                }
            });
        </script>
        @stack('scripts')

        {!! view_render_event('bagisto.admin.layout.body.after') !!}

        <div class="modal-overlay"></div>
<style>



@media (max-width: 1250px) {
    .navbar-header {
        float: none;
    }
    .navbar-toggle {
        display: block;
    }
    .navbar-collapse {
        border-top: 1px solid transparent;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.1);
    }
    .navbar-collapse.collapse {
        display: none!important;
    }
    .navbar-nav {
        float: none!important;
        margin: 7.5px -15px;
    }
    .navbar-nav>li {
        float: none;
    }
    .navbar-nav>li>a {
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .navbar-text {
        float: none;
        margin: 15px 0;
    }
    /* since 3.1.0 */
    .navbar-collapse.collapse.in { 
        display: block!important;
    }
    .collapsing {
        overflow: hidden!important;
    }
}

     .content-container .content .page-header {
               top:0px;
               padding-top:0px;
            }
            .content-container .content .page-content {
               margin-top:0px;
            }
            .content-container .content {
                margin-top:0px;
            }
            .content-container .content.full-page {
                padding-top: 0px; 
            }
            .content-container .content-wrapper {
                padding-top:0px;
                margin-top:0px;
            }
    @media (min-width: 1200px){
    .navbar .container {
       width: 100%;
    }
    @media (max-width: 1000px){
    .navbar .container {
        width: 750px;
    }
    }
    @media (min-width: 767px) {
    .caret-right {
        width: 0; 
        height: 0; 
        border-top: 4px solid rgba(0, 0, 0, 0);
        border-bottom: 4px solid rgba(0, 0, 0, 0);
        border-left: 4px solid #777777;

    }
    }
   
</style>
<script>
    $(document).ready(function() {
    $('.navbar a.dropdown-toggle').on('click', function(e) {
        var elmnt = $(this).parent().parent();
        if (!elmnt.hasClass('nav')) {
            var li = $(this).parent();
            var heightParent = parseInt(elmnt.css('height').replace('px', '')) / 2;
            var widthParent = parseInt(elmnt.css('width').replace('px', '')) - 10;
            
            if(!li.hasClass('open')) li.addClass('open')
            else li.removeClass('open');
            $(this).next().css('top', heightParent + 'px');
            $(this).next().css('left', widthParent + 'px');
            
            return false;
        }
    });
});
</script>

</body>
</html>
    
