@if(isset($menu)) 

<div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                <a class="navbar-brand" style="margin-top:-14px;" href="{{ route('admin.dashboard.index') }}">
                @if (core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode()))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url(core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode())) }}" alt="{{ config('app.name') }}" style="height: 40px; width: 110px; "/>
                @else
                    <img src="{{ asset('vendor/webkul/ui/assets/images/admin-logo.png') }}" style="max-height:50px;" alt="{{ config('app.name') }}"/>
                @endif
                </a>
                    <a class="navbar-brand" href="javascript:void(0);">Control Panel</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav" style="float:left; clear:none;">

                    @foreach ($menu->items as $menuItem)
                        @if(count($menuItem['children'])) 
                            <li>
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                    <span class="topmenu {{ $menu->getActive($menuItem) }}">
                                        {{ trans($menuItem['name']) }}
                                    </span>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach ($menuItem['children'] as $key => $item)
                                        
                                        @if(count($item['children']))

                                            <li class="divider"></li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                                    <span class="topmenu {{ $menu->getActive($item) }}">
                                                        {{ trans($item['name']) }}
                                                    </span>
                                                    <b class="caret caret-right"></b>
                                                </a>
                                                <ul class="dropdown-menu">

                                                    @foreach ($item['children'] as $key => $item2)
                                                        
                                                        <li>
                                                            <a href="{{ $item2['url'] }}">
                                                                <span class="topmenu {{ $menu->getActive($item2) }}">
                                                                {{ trans($item2['name']) }}
                                                                </span>
                                                            </a>
                                                        </li>

                                                    @endforeach
                                                    
                                                </ul>
                                            </li>

                                        @else
                                            <li>
                                                <a href="{{ $item['url'] }}">
                                                    <span class="topmenu {{ $menu->getActive($item) }}">
                                                    {{ trans($item['name']) }}
                                                    </span>
                                                </a>
                                            </li>
                                        @endif
                                    
                                    @endforeach

                                    
                                </ul>
                            </li>

                        @else

                            <li>
                                <a href="{{ $menuItem['url'] }}">
                                    <span class="topmenu {{ $menu->getActive($menuItem) }}">
                                    {{ trans($menuItem['name']) }}
                                    </span>
                                </a>
                            </li>
                            
                        @endif

                        

                    @endforeach

                    </ul>
                    <ul class="nav navbar-nav navbar-right" style="float:right; clear:none;">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="name" style="font-size:12px; float:left; clear:none;">
                                    {{ auth()->guard('admin')->user()->name }}
                                </span><br>
                                <span class="role" style="float:left; clear:none;">
                                    {{ auth()->guard('admin')->user()->role['name'] }}
                                </span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:void(0);"><span class="app-version">{{ __('admin::app.layouts.app-version', ['version' => 'v' . config('app.version')]) }}</span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"><label>Account</label>
                                </li>
                                <li>
                                    <a href="{{ route('shop.home.index') }}" target="_blank">{{ __('admin::app.layouts.visit-shop') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.account.edit') }}">{{ __('admin::app.layouts.my-account') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.session.destroy') }}">{{ __('admin::app.layouts.logout') }}</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div><!--/.nav-collapse -->
               
            </div>
        </div>


@endif
