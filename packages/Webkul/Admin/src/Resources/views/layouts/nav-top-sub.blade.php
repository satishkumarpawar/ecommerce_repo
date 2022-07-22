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
                    <a class="navbar-brand" href="#">Control Panel</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">

                    @foreach ($menu->items as $menuItem)
                        @if(count($menuItem['children'])) 
                            <li>
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ trans($menuItem['name']) }} <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    @foreach ($menuItem['children'] as $key => $item)
                                        
                                        @if(count($item['children']))

                                            <li class="divider"></li>
                                            <li>
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ trans($item['name']) }} <b class="caret caret-right"></b></a>
                                                <ul class="dropdown-menu">

                                                    @foreach ($item['children'] as $key => $item2)

                                                    <li class="{{ $menu->getActive($menuItem) }}"><a href="{{ $item2['url'] }}">{{ trans($item2['name']) }}</a></li>
                                                    
                                                    @endforeach
                                                    
                                                </ul>
                                            </li>

                                        @else
                                            <li class="{{ $menu->getActive($menuItem) }}"><a href="{{ $item['url'] }}">{{ trans($item['name']) }}</a></li>
                                        @endif
                                    
                                    @endforeach

                                    
                                </ul>
                            </li>

                        @else
                            <li class="{{ $menu->getActive($menuItem) }}"><a href="{{ $menuItem['url'] }}">{{ trans($menuItem['name']) }}</a></li>
                        @endif

                        

                    @endforeach

                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>


@endif
