
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
                                        @if(count($item['children']))
                                            <li style="border-bottom:1px solid #e8e8e8; " class="topmenu {{$menu->getActive($item)}}">
                                                <a class="{{$menu->getActive($item)}}" href="{{ $item['url'] }}">
                                                {{ isset($item['name']) ? trans($item['name']) : '' }}
                                                </a>
                                            </li> 
                                            <li >
                                           
                                                <ul>
                                                    @foreach ($item['children'] as $key => $item2)
                                                        <li style="background-color:#fbfbfb; margin-top:2px; border-radius:2px; padding-left:5px;" class="submenu {{$menu->getActive($item2)}}">
                                                            <a class="{{$menu->getActive($item2)}}" href="{{ $item2['url'] }}">
                                                            {{ isset($item2['name']) ? trans($item2['name']) : '' }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                               
                                            </li>
                                        @else
                                        <li class="topmenu {{$menu->getActive($item)}}">
                                            <a class="{{$menu->getActive($item)}}" href="{{ $item['url'] }}">
                                             {{ isset($item['name']) ? trans($item['name']) : '' }}
                                            </a>
                                        </li>
                                            
                                        @endif
                                       
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    </div>
                    @endforeach
        @endif
            
</div></div></div>



