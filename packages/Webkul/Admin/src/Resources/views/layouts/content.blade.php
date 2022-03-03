@extends('admin::layouts.master')

@section('content-wrapper')
   
    <div class="inner-section" style="margin-left:0px;">

        {{--@include ('admin::layouts.nav-aside')--}}

        <div class="content-wrapper" style="margin-left:0px;margin-top:0px;">

            @include ('admin::layouts.tabs')
       
            @yield('content')

        </div>

    </div>
@stop