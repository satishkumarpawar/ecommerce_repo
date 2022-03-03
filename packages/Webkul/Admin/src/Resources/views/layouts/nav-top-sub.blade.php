
<div class="navbar-top" style="top:61px; width:100%">
<div class="navbar-top-right"  style="width:100%; text-align:left;">
        <div class="profile">  
        @if(isset($menu)) 
                     @foreach ($menu->items as $menuItem)
                    <div class="profile-info"   style="margin-top:5px;">
                    <div class="dropdown-toggle">
                    @if(count($menuItem['children'])) 
                        <div style="display: inline-block; vertical-align: middle;">
                            <span class="topmenu {{ $menu->getActive($menuItem) }}">
                                  {{ trans($menuItem['name']) }}
                            </span>
                        </div>
                        <i class="icon arrow-down-icon active"></i>
                    @else
                        <div style="display: inline-block; vertical-align: middle;">
                            <span class="topmenu {{ $menu->getActive($menuItem) }}">
                                <a href="{{ count($menuItem['children']) ? current($menuItem['children'])['url'] : $menuItem['url'] }}">
                                {{ trans($menuItem['name']) }}
                                </a>
                            </span>
                        </div>
                    @endif
                    </div>
                    @if(count($menuItem['children'])) 
                        <div class="dropdown-list bottom-right"  style="margin-top:-5px;">
                            <div class="dropdown-container">
                                <ul>
                                    @foreach ($menuItem['children'] as $key => $item)
                                        <li class="topmenu {{$menu->getActive($item)}}">
                                            <a class="{{$menu->getActive($item)}}" href="{{ $item['url'] }}">
                                             {{ isset($item['name']) ? trans($item['name']) : '' }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    </div>
                    @endforeach
        @endif
            
</div></div></div>



