@php
    $menus = getMenus("student");   
    $setting = generalSetting();
    $paid_modules = ['Zoom','OnlineExam','University','Gmeet','QRCodeAttendance','BBB','ParentRegistration','InAppLiveClass','AiContent','Lms','Certificate','Jitsi','WhatsappSupport','CbseExam','InfixBiometrics'];
    $onlineExamRoutes = ['online_exam', 'student_online_exam', 'student_view_result', 'student_written_exam', 'om_student_view_result', 'om_student_online_exam'];
@endphp
@foreach($menus as $key => $menu)
<span class="menu_seperator" id="seperator_{{ \Illuminate\Support\str::lower($menu->name) }}"  data-section="{{ $menu->route }}">{{ __($menu->lang_name)}}</span>
    
    @if($menu->childs->count() > 0)      
        @foreach($menu->childs as $child)
            {{-- Do not render stale database menu entries for disabled modules. --}}
            @if($child->route === 'zoom' && !moduleStatusCheck('Zoom'))
                @continue
            @endif
            @if(
                (in_array($child->route, $onlineExamRoutes, true)
                    || strtolower(trim((string) $child->name)) === 'online exam')
                && !moduleStatusCheck('OnlineExam')
            )
                @continue
            @endif
            @if($child->childs->count() > 0)
                @if(userPermission($child->route))
                    @if(!empty($child->module) && in_array($child->module, $paid_modules))
                        @if(moduleStatusCheck($child->module))
                            @includeIf('backEnd.menu.student_sub_menu',['menu' => $menu,'child' => $child]) 
                        @endif
                    @else    
                        @includeIf('backEnd.menu.student_sub_menu',['menu' => $menu,'child' => $child]) 
                    @endif
                @endif
            @else  
                @if(userPermission($child->route) && isMenuAllowToShow($child->route) )  
                   @if($child->route == 'fees.student-fees-list' || $child->route == 'student-fees')
                        @if($setting->fees_status == 1)

                            <li data-route='fees.student-fees-list' class="{{ spn_active_link(['fees.student-fees-list'], "mm-active") }} fees.student-fees-list main">
                                <a href="{{ validRouteUrl('fees.student-fees-list') }}">
                                    <div class="nav_icon_small">
                                        <span class="{{ $child->icon }}"></span>
                                    </div>
                                    <div class="nav_title">
                                        <span> {{ !empty($child->lang_name) ?  __($child->lang_name):$child->name }} </span>
                                    </div>
                                </a>
                            </li>
                        @endif

                        @if($setting->fees_status == 0)
                            <li data-route='student_fees' class="{{ spn_active_link(['student_fees'], "mm-active") }} student_fees main">
                                <a href="{{ validRouteUrl('student_fees') }}">
                                    <div class="nav_icon_small">
                                        <span class="{{ $child->icon }}"></span>
                                    </div>
                                    <div class="nav_title">
                                        <span> {{ !empty($child->lang_name) ?  __($child->lang_name):$child->name }} </span>
                                    </div>
                                </a>
                            </li>
                        @endif
                   @else
                         <li data-route='{{$child->route}}' class="{{ spn_active_link([$child->route], "mm-active") }} {{ $child->route }} main">
                            <a href="{{ validRouteUrl($child->route) }}">
                                <div class="nav_icon_small">
                                    <span class="{{ $child->icon }}"></span>
                                </div>
                                <div class="nav_title">
                                    <span> {{ !empty($child->lang_name) ?  __($child->lang_name):$child->name }} </span>
                                </div>
                            </a>
                        </li>

                   @endif

                @endif               
            @endif
        @endforeach
    @endif
@endforeach
