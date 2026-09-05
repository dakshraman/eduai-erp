@php
    $routes = subModuleRoute($child);
    $paid_modules = [
        'Branch',
        'CbseExam',
        'Zoom',
        'University',
        'Gmeet',
        'QRCodeAttendance',
        'BBB',
        'ParentRegistration',
        'InAppLiveClass',
        'AiContent',
        'Lms',
        'Certificate',
        'Jitsi',
        'WhatsappSupport',
        'InfixBiometrics',
    ];
    if(config('app.app_sync') == true){
       array_push($paid_modules, 'OnlineExam');
    }
    $default_theme = [
        'course-heading-update',
        'admin-home-page',
        'custom-links',
        'social-media',
        'From Download',
        'class-exam-routine-page',
        'course-details-heading',
        'news-heading-update',
        'exam-result-page',
        'contact-page',
        'about-page',
        'conpactPage',
    ];
    $edulia_theme = [
        'home-slider',
        'admin-home-page',
        'pagebuilder',
        'expert-teacher',
        'photo-gallery',
        'video-gallery',
        'front-result',
        'front-class-routine',
        'front-exam-routine',
        'front-academic-calendar',
        'class-exam-routine-page',
    ];
    $active_theme = activeTheme();
    $new_fees = [
        'fees.fees-group',
        'fees.due-fees',
        'fees.fees-type',
        'fees.fine-report',
        'fees',
        'fees.fees-invoice-list',
        'fees.payment-report',
        'fees-invoice-bulk-print',
        'fees.bank-payment',
        'fees.balance-report',
        'fees-invoice-bulk-print-settings',
        'fees_forward',
        'fees.waiver-report',
    ];
    $old_fees = [
        'fees_statement',
        'balance_fees_report',
        'transaction_report',
        'fine-report',
        'fees-bulk-print',
        'fees_group',
        'fees_type',
        'fees-master',
        'fees_discount',
        'collect_fees',
        'search_fees_payment',
        'search_fees_due',
        'fees_forward',
        'bank-payment-slip',
    ];
    $sass_general_setting = ['administrator-notice'];
    $sass_school_disable = ['general-settings', 'update-system', 'api/permission', 'cron-job', 'language-list'];
    $online_exam = ['question-group', 'question-bank', 'online-exam'];
    $online_exam_module = [
        'online-question-group',
        'online-question-bank',
        'om-online-exam-add',
        'om-online-exam',
        'written_exam',
        'online-exam-setting',
        'om_student_online_exam',
        'om_student_view_result',
        'student_written_exam',
        'student_view_written_result',
        'om_parent_online_examination',
        'om_parent_online_examination_result',
        'parent_pdf_exam',
        'parent_view_pdf_result',
    ];
@endphp


<li class="{{ spn_active_link($routes, 'mm-active') }} {{ $menu->route }}">
    <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
        <div class="nav_icon_small">
            <span class="{{ $child->icon }}"></span>
        </div>
        <div class="nav_title">
            <span> {{ !empty($child->lang_name) ? __($child->lang_name) : $child->name }} </span>
            @if (!empty($child->module) && in_array($child->module, $paid_modules) && config('app.app_sync') == true)
                <span class="demo_addons">Addon</span>
            @endif
        </div>
    </a>
    <ul class="mm-collapse">
        @php $rendered_routes = []; @endphp

        @if($child->route === 'teacher-evaluation'
            && (int) auth()->user()->role_id === 4
            && userPermission('teacher-panel-evaluation-report')
            && !$child->childs->contains('route', 'teacher-panel-evaluation-report'))
            <li>
                <a href="{{ validRouteUrl('teacher-panel-evaluation-report') }}"
                   class="{{ spn_active_link('teacher-panel-evaluation-report') }}">
                    {{ __('teacherEvaluation.my_report') }}
                </a>
            </li>
        @endif

        @foreach ($child->childs as $third)
            @if ($third->route && in_array($third->route, $rendered_routes))
                @continue
            @endif
            @php if($third->route) $rendered_routes[] = $third->route; @endphp

            @if($third->route == 'shift.index' && !generalSetting()->shift_enable)
                @continue
            @endif

            @if($third->route == 'teacher-panel-evaluation-report' && (int) auth()->user()->role_id !== 4)
                @continue
            @endif

                @if($third->route == 'branch.index' && !moduleStatusCheck('Branch'))
                    @continue
                @endif

                @if($third->route ==  'saas.custom-domain' && (!moduleStatusCheck('Saas') || !config('app.allow_custom_domain')))
                    @continue
                @endif
            @if (userPermission($third->route))
                @php
                    $disable_routes = ['class_optional', 'academic-year'];
                @endphp
                @if (in_array($third->route, $disable_routes))
                    @if (!moduleStatusCheck('University'))
                        <li>
                            <a href="{{ validRouteUrl($third->route) }}" class="{{ spn_active_link($third->route) }}">
                                {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                            </a>
                        </li>
                    @endif
                @else
                    @if (in_array($third->route, $sass_general_setting))
                        @if (moduleStatusCheck('Saas'))
                            <li>
                                <a href="{{ validRouteUrl($third->route) }}"
                                    class="{{ spn_active_link($third->route) }} ">
                                    {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                </a>
                            </li>
                        @endif
                    @else
                        @if ($third->route == 'manage-adons')
                            @if (!moduleStatusCheck('Saas'))
                                <li>
                                    <a href="{{ validRouteUrl($third->route) }}"
                                        class="{{ spn_active_link($third->route) }} ">
                                        {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                    </a>
                                </li>
                            @endif
                        @else
                            @if ($third->route == 'online_exam')
                                @if (!moduleStatusCheck('Saas'))
                                    <li>
                                        <a href="{{ validRouteUrl($third->route) }}"
                                            class="{{ spn_active_link($third->route) }} ">
                                            {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                        </a>
                                    </li>
                                @endif
                            @else
                                @if (in_array($third->route, [
                                        'view-teacher-lessonPlan-overview',
                                        'view-teacher-lessonPlan',
                                        'teacher_class_routine_report',
                                    ]))
                                    @if (isTeacher())
                                        <li>
                                            <a href="{{ validRouteUrl($third->route) }}"
                                                class="{{ spn_active_link($third->route) }} ">
                                                {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                            </a>
                                        </li>
                                    @endif
                                @else
                                    @if (in_array($third->route, $edulia_theme))
                                        @if ($active_theme == 'edulia')
                                            <li>
                                                <a href="{{ validRouteUrl($third->route) }}"
                                                    class="{{ spn_active_link($third->route) }} ">
                                                    {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                </a>
                                            </li>
                                        @endif
                                    @else
                                        @if (in_array($third->route, $default_theme))
                                            @if ($active_theme == 'default')
                                                <li>
                                                    <a href="{{ validRouteUrl($third->route) }}"
                                                        class="{{ spn_active_link($third->route) }} ">
                                                        {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                    </a>
                                                </li>
                                            @endif
                                        @else
                                            @if (in_array($third->route, $new_fees) || in_array($third->route, $old_fees))
                                                @if (in_array($third->route, $new_fees) && generalSetting()->fees_status == 1)
                                                    <li class="">
                                                        <a href="{{ validRouteUrl($third->route) }}"
                                                            class="{{ spn_active_link($third->route) }} ">
                                                            {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if (in_array($third->route, $old_fees) && generalSetting()->fees_status == 0)
                                                    <li class="">
                                                        <a href="{{ validRouteUrl($third->route) }}"
                                                            class="{{ spn_active_link($third->route) }} ">
                                                            {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @else
                                                @if ($third->route == 'school-general-settings')
                                                    @if (saasSettings('general-settings'))
                                                        <li>
                                                            <a href="{{ validRouteUrl($third->route) }}"
                                                                class="{{ spn_active_link($third->route) }} ">
                                                                {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                @else
                                                    @if ($third->route == 'email-settings' && moduleStatusCheck('Saas'))
                                                        @if (saasSettings('email-settings'))
                                                            <li>
                                                                <a href="{{ validRouteUrl($third->route) }}"
                                                                    class="{{ spn_active_link($third->route) }} ">
                                                                    {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @else
                                                        @if ($third->route == 'sms-settings' && moduleStatusCheck('Saas'))

                                                            @if (saasSettings('sms-settings'))
                                                                <li>
                                                                    <a href="{{ validRouteUrl($third->route) }}"
                                                                        class="{{ spn_active_link($third->route) }} ">
                                                                        {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @else
                                                            @if ($third->route == 'manage-currency' && moduleStatusCheck('Saas'))
                                                                @if (saasSettings('manage-currency'))
                                                                    <li>
                                                                        <a href="{{ validRouteUrl($third->route) }}"
                                                                            class="{{ spn_active_link($third->route) }} ">
                                                                            {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            @else
                                                                @if ($third->route == 'base_setup' && moduleStatusCheck('Saas'))
                                                                    @if (saasSettings('base_setup'))
                                                                        <li>
                                                                            <a href="{{ validRouteUrl($third->route) }}"
                                                                                class="{{ spn_active_link($third->route) }} ">
                                                                                {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                            </a>
                                                                        </li>
                                                                    @endif
                                                                @else
                                                                    @if ($third->route == 'backup-settings' && moduleStatusCheck('Saas'))
                                                                        @if (saasSettings('backup-settings'))
                                                                            <li>
                                                                                <a href="{{ validRouteUrl($third->route) }}"
                                                                                    class="{{ spn_active_link($third->route) }} ">
                                                                                    {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                                </a>
                                                                            </li>
                                                                        @endif
                                                                    @else
                                                                        @if ($third->route == 'button-disable-enable' && moduleStatusCheck('Saas'))
                                                                            @if (saasSettings('button-disable-enable'))
                                                                                <li>
                                                                                    <a href="{{ validRouteUrl($third->route) }}"
                                                                                        class="{{ spn_active_link($third->route) }} ">
                                                                                        {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                                        {{ $third->route }}
                                                                                    </a>
                                                                                </li>
                                                                            @endif
                                                                        @else
                                                                            @if ($third->route == 'templatesettings.sms-template' && moduleStatusCheck('Saas'))
                                                                                @if (saasSettings('templatesettings.sms-template'))
                                                                                    <li>
                                                                                        <a href="{{ validRouteUrl($third->route) }}"
                                                                                            class="{{ spn_active_link($third->route) }} ">
                                                                                            {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                                        </a>
                                                                                    </li>
                                                                                @endif
                                                                            @else
                                                                                @if ($third->route == 'templatesettings.email-template' && moduleStatusCheck('Saas'))
                                                                                    @if (saasSettings('templatesettings.email-template'))
                                                                                        <li>
                                                                                            <a href="{{ validRouteUrl($third->route) }}"
                                                                                                class="{{ spn_active_link($third->route) }} ">
                                                                                                {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                                            </a>
                                                                                        </li>
                                                                                    @endif
                                                                                @else
                                                                                    @if (in_array($third->route, $sass_school_disable))
                                                                                        @if (!moduleStatusCheck('Saas'))
                                                                                            <li>
                                                                                                <a href="{{ validRouteUrl($third->route) }}"
                                                                                                    class="{{ spn_active_link($third->route) }} ">
                                                                                                    {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                                                </a>
                                                                                            </li>
                                                                                        @endif
                                                                                    @else
                                                                                        @if ($third->route == 'saas.custom-domain' && moduleStatusCheck('Saas') )
                                                                                            @if (config('app.allow_custom_domain'))
                                                                                                <li>
                                                                                                    <a href="{{ validRouteUrl($third->route) }}"
                                                                                                        class="{{ spn_active_link($third->route) }} ">
                                                                                                        {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                                                    </a>
                                                                                                </li>
                                                                                            @endif
                                                                                        @else
                                                                                            @if(in_array($third->route, $online_exam) || in_array($third->route, $online_exam_module) )
                                                                                                {{-- OnlineExam --}}
                                                                                                @if(in_array($third->route, $online_exam))
                                                                                                    @if(config('app.app_sync') == true)
                                                                                                        <li>
                                                                                                            <a href="{{ validRouteUrl($third->route) }}"
                                                                                                                class="{{ spn_active_link($third->route) }} ">
                                                                                                                {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                                                            </a>
                                                                                                        </li>
                                                                                                    @else  
                                                                                                        @if (!moduleStatusCheck('OnlineExam'))
                                                                                                            <li>
                                                                                                                <a href="{{ validRouteUrl($third->route) }}"
                                                                                                                    class="{{ spn_active_link($third->route) }} ">
                                                                                                                    {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                                                                </a>
                                                                                                            </li>
                                                                                                        @endif
                                                                                                    @endif
                                                                                                @endif
                                                                                                
                                                                                                @if(in_array($third->route, $online_exam_module) && $third->ignore == 0)
                                                                                                    @if(config('app.app_sync') == false)
                                                                                                        @if (moduleStatusCheck('OnlineExam'))
                                                                                                             <li>
                                                                                                                <a href="{{ validRouteUrl($third->route) }}"
                                                                                                                    class="{{ spn_active_link($third->route) }} ">
                                                                                                                    {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                                                                </a>
                                                                                                             </li>
                                                                                                        @endif
                                                                                                    @endif
                                                                                                @endif

                                                                                            @else

                                                                                                @php
                                                                                                    $thirdRouteUrl = $third->module == 'University' && $third->route == 'section'
                                                                                                        ? route('university.section.index')
                                                                                                        : validRouteUrl($third->route);
                                                                                                    $thirdActiveRoute = $third->module == 'University' && $third->route == 'section'
                                                                                                        ? ['section', 'university.section.index']
                                                                                                        : $third->route;
                                                                                                @endphp
                                                                                                <li>
                                                                                                    <a href="{{ $thirdRouteUrl }}"
                                                                                                        class="{{ spn_active_link($thirdActiveRoute) }} ">
                                                                                                        {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                                                                                                    </a>
                                                                                                </li>
                                                                                            @endif

                                                                                        @endif
                                                                                    @endif
                                                                                @endif
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif

                                            @endif
                                        @endif
                                    @endif
                                @endif
                            @endif
                        @endif
                    @endif
                @endif
            @endif
            @if($third->module == 'OnlineExam' && $third->ignore == 1)
                @if(config('app.app_sync') == true)
                    <li>
                        <a href="{{ validRouteUrl($third->route) }}" class="{{ spn_active_link($third->route) }}">
                            {{ !empty($third->lang_name) ? __($third->lang_name) : $third->name }}
                        </a>
                    </li>
                @endif
            @endif
        @endforeach
    </ul>
</li>
