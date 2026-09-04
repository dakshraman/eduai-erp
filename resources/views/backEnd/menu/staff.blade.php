@php
    $menus = $menus ?? getMenus("staff");
    $paid_modules = $paid_modules ?? ['Branch', 'CbseExam','Zoom','University','Gmeet','QRCodeAttendance','BBB','ParentRegistration','InfixBiometrics','AiContent','Lms','Certificate','Jitsi','WhatsappSupport','InAppLiveClass', 'OnlineExam'];
    if(config('app.app_sync') == true){
       array_push($paid_modules, 'OnlineExam');
    }
    $module_enable = false;
    foreach($paid_modules as $module){
        if(moduleStatusCheck($module)){
            $module_enable = true;
            break;
        }
    }
    
    $free_modules = ['Chat','fees_collection','Fees'];
@endphp

@foreach($menus as $key => $menu)

    @if($menu->route == 'dashboard_section')

        <span class="menu_seperator" id="{{$menu->route}}"
              data-section="{{ $menu->route }}">{{ __($menu->lang_name) }} </span>
        @if($menu->childs->count() > 0)

            @foreach($menu->childs as $child)

                @if(userPermission($child->route))

                    <li class="{{ spn_active_link([$child->route], "mm-active") }} {{ $child->route }} main">
                        <a href="{{ validRouteUrl($child->route) }}">
                            <div class="nav_icon_small">
                                <span class="{{ $child->icon }}"></span>
                            </div>
                            <div class="nav_title">
                                <span>{{ !empty($child->lang_name) ?  __($child->lang_name):$child->name }}</span>
                            </div>
                        </a>
                    </li>
                @endif
            @endforeach
        @endif
        @if(auth()->user()->role_id == 1 && moduleStatusCheck('Saas'))
            @include('saas::menu.SaasSubscriptionSchool')
        @endif
    @else
        @if ($menu->route == 'module_section')
            @if($module_enable)
                @if($menu->childs->count() > 0)
                    <span class="menu_seperator" id="seperator_{{$menu->route}}"
                          data-section="{{ $menu->route }}">{{ __($menu->lang_name)}} </span>
                    @foreach($menu->childs as $child)
                        @if($child->childs->count() > 0)
                            @if(userPermission($child->route))
                                @if(!empty($child->module) && in_array($child->module,$paid_modules) )
                                    @if( moduleStatusCheck($child->module))
                                        @if($child->module == 'OnlineExam')
                                            {{-- Hide from module section, already in Exam section --}}
                                        @elseif(moduleStatusCheck("Saas"))
                                            @if(isModuleForSchool($child->module) && isMenuAllowToShow($child->module) )
                                                @includeIf('backEnd.menu.staff_sub_menu',compact('menu','child'))
                                            @endif
                                        @else
                                            @includeIf('backEnd.menu.staff_sub_menu',compact('menu','child'))
                                        @endif
                                    @endif
                                @else
                                    {{-- Non-paid-modules list: still guard by moduleStatusCheck so inactive modules stay hidden --}}
                                    @if(empty($child->module) || moduleStatusCheck($child->module))
                                        @if(moduleStatusCheck("Saas"))
                                            @if(isModuleForSchool($child->module) && isMenuAllowToShow($child->module))
                                                @includeIf('backEnd.menu.staff_sub_menu',compact('menu','child'))
                                            @endif
                                        @else
                                            @includeIf('backEnd.menu.staff_sub_menu',compact('menu','child'))
                                        @endif
                                    @endif
                                @endif
                            @endif

                        @else
                            @if(userPermission($child->route) && (empty($child->module) || moduleStatusCheck($child->module)))
                                @if(moduleStatusCheck("Saas"))
                                    @if(isModuleForSchool($child->route) && isMenuAllowToShow($child->route))
                                        <li class="{{ spn_active_link([$child->route], "mm-active") }}  main">
                                            <a href="{{ validRouteUrl($child->route) }}">
                                                <div class="nav_icon_small">
                                                    <span class="{{ $child->icon }}"></span>
                                                </div>
                                                <div class="nav_title">
                                                    <span>{{ !empty($child->lang_name) ?  __($child->lang_name):$child->name }}</span>
                                                </div>
                                            </a>
                                        </li>
                                    @endif
                                @else

                                    <li class="{{ spn_active_link([$child->route], "mm-active") }}  main">
                                        <a href="{{ validRouteUrl($child->route) }}">
                                            <div class="nav_icon_small">
                                                <span class="{{ $child->icon }}"></span>
                                            </div>
                                            <div class="nav_title">
                                                <span>{{ !empty($child->lang_name) ?  __($child->lang_name):$child->name }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                            @endif
                        @endif
                    @endforeach
                @endif

            @endif

        @else
            @if($menu->childs->count() > 0)

                <span class="menu_seperator" id="seperator_{{$menu->route}}"
                      data-section="{{ $menu->route }}">{{ __($menu->lang_name)}} </span>
                @php $rendered_top_routes = []; @endphp
                @foreach($menu->childs as $child)
                    @if ($child->route && in_array($child->route, $rendered_top_routes))
                        @continue
                    @endif
                    @php if($child->route) $rendered_top_routes[] = $child->route; @endphp
                    @if($child->childs->count() > 0)
                        @if(userPermission($child->route))

                            @if($child->module == 'Fees' || $child->module == 'fees_collection')
                             
                                @if($child->module == 'Fees' && generalSetting()->fees_status  == 1)
                                    @if(moduleStatusCheck("Saas"))
                                        @if(isModuleForSchool($child->route) && isMenuAllowToShow($child->route))
                                            @includeIf('backEnd.menu.staff_sub_menu',compact('menu','child'))
                                        @endif
                                    @else
                                        @includeIf('backEnd.menu.staff_sub_menu',compact('menu','child'))
                                    @endif
                                @endif
                                @if($child->module == 'fees_collection' && generalSetting()->fees_status  == 0)
                                    @if(moduleStatusCheck("Saas"))
                                        @if(isModuleForSchool($child->route) && isMenuAllowToShow($child->route))
                                            @includeIf('backEnd.menu.staff_sub_menu',compact('menu','child'))
                                        @endif
                                    @else
                                        @includeIf('backEnd.menu.staff_sub_menu',compact('menu','child'))
                                    @endif
                                @endif
                            @else
                                @if(empty($child->module) || moduleStatusCheck($child->module))
                                    @if(moduleStatusCheck("Saas"))
                                        @if(isModuleForSchool($child->route) && isMenuAllowToShow($child->route))
                                            @includeIf('backEnd.menu.staff_sub_menu',compact('menu','child'))
                                        @endif
                                    @else
                                        @includeIf('backEnd.menu.staff_sub_menu',compact('menu','child'))
                                    @endif
                                @endif
                            @endif
                        @endif

                    @else

                        @if(userPermission($child->route))
                            @if($child->route == 'manage-adons')
                                @if(! moduleStatusCheck('Saas'))
                                    <li class="{{ spn_active_link([$child->route], "mm-active") }} {{ $child->route }} main">
                                        <a href="{{ validRouteUrl($child->route) }}">
                                            <div class="nav_icon_small">
                                                <span class="{{ $child->icon }}"></span>
                                            </div>
                                            <div class="nav_title">
                                                <span>{{ !empty($child->lang_name) ?  __($child->lang_name):$child->name }} </span>
                                            </div>
                                        </a>
                                    </li>

                                @endif
                            @else
                                @if((empty($child->module) || moduleStatusCheck($child->module)) && isModuleForSchool($child->route) && isMenuAllowToShow($child->route))
                                    <li class="{{ spn_active_link([$child->route], "mm-active") }} {{ $child->route }} main">
                                        <a href="{{ validRouteUrl($child->route) }}">
                                            <div class="nav_icon_small">
                                                <span class="{{ $child->icon }}"></span>
                                            </div>
                                            <div class="nav_title">
                                                <span>{{ !empty($child->lang_name) ?  __($child->lang_name):$child->name }} </span>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                            @endif
                        @endif
                    @endif
                @endforeach
            @endif

        @endif
    @endif

@endforeach

@if (moduleStatusCheck('Saas') && auth()->user()->is_administrator !== 'yes')
    @includeIf('Saas::menu.staff')
@endif
