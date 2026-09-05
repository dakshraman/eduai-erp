@php
    $paid_modules = $paid_modules ?? ['CbseExam','Zoom','University','Gmeet','QRCodeAttendance','BBB','ParentRegistration','InfixBiometrics','AiContent','Lms','Certificate','Jitsi','WhatsappSupport','InAppLiveClass'];

    // Routes whose menu item expands into per-child links (parent_children_menu partial).
    // All other leaf items render a single direct link.
    $childInjectedRoutes = [
        'my_children',
        'fees.student-fees-list-parent',
        'parent_class_routine',
        'parent_homework',
        'parent_attendance',
        'parent_subjects',
        'parent_teacher_list',
        'parent_transport',
        'parent_dormitory_list',
    ];
@endphp
@foreach($menus as $key => $menu)
<span class="menu_seperator" id="seperator_{{ \Illuminate\Support\str::lower($menu->name) }}" data-section="{{ $menu->route }}">{{ __($menu->lang_name) }}</span>
    @if($menu->childs->count() > 0)
        @foreach($menu->childs as $child)
            @if($child->childs->count() > 0)
                {{-- ---- Parent menu item that has sub-children ---- --}}
                @if(userPermission($child->route))
                    @if(!empty($child->module) && in_array($child->module, $paid_modules))
                        @if(moduleStatusCheck($child->module) && isMenuAllowToShow($child->module))
                            @includeIf('backEnd.menu.parent_sub_menu', ['menu' => $menu, 'child' => $child])
                        @endif
                    @elseif(isMenuAllowToShow($child->route))
                        @includeIf('backEnd.menu.parent_sub_menu', ['menu' => $menu, 'child' => $child])
                    @endif
                @endif
            @else
                {{-- ---- Leaf / single-link menu item ---- --}}
                @if(userPermission($child->route) && isMenuAllowToShow($child->route))
                    @if(in_array($child->route, $childInjectedRoutes))
                        {{-- Expand into one link per child student --}}
                        @includeIf('backEnd.menu.parent_children_menu', ['menu' => $menu, 'child' => $child])
                    @else
                        <li class="{{ spn_active_link([$child->route], 'mm-active') }} {{ $child->route }} main">
                            <a href="{{ validRouteUrl($child->route) }}">
                                <div class="nav_icon_small">
                                    <span class="{{ $child->icon }}"></span>
                                </div>
                                <div class="nav_title">
                                    <span>{{ !empty($child->lang_name) ? __($child->lang_name) : $child->name }}</span>
                                </div>
                            </a>
                        </li>
                    @endif
                @endif
            @endif
        @endforeach
    @endif
@endforeach