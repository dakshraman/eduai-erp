@extends(config('pagebuilder.site_layout'),['edit' => false ])
@section(config('pagebuilder.site_section'))
<style>
    .list-unstyled li {
        font-size: 1.1rem;
        color: #6c757d;
    }
    .list-unstyled li i {
        color: #28a745;
        margin-right: 10px;
    }
    .list-unstyled li.mb-2 {
        margin-bottom: 1rem;
    }
    .primary-btn.fix-gr-bg {
        background: var(--primary-color) !important;
    }
    .lesson-extra-main {
        margin-top: 15px;
        font-size: 15px;
        color: #00124E;
        margin-bottom: 15px;
    }
</style>

{{headerContent()}}
    <section class="bradcrumb_area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="bradcrumb_area_inner">
                        <h1>{{$course->course_title}}<span><a href="{{url('/')}}">{{__('edulia.home')}}</a> / <a href="{{url('/online-course')}}">{{__('edulia.online_course')}}</a> / {{__('edulia.course_details')}}</span></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- course details start -->
    <section class="section_padding course course_details_page">
        <div class="container">
            <div class="row">
                <!-- course details left -->
                <div class="col-lg-4 col-md-12">
                    <div class="course_sidebar">
                        <div class="course_sidebar_thumbnail">
                            <img src="{{$course->preview_image != ""? assetPath($course->preview_image) : assetPath('/public/backEnd/default.jpeg')}}" alt="{{$course->title}}">
                        </div>
                        <div class="course_sidebar_content">
                            <div class="d-flex justify-content-between mb-3 p-3">
                                <div class="pt-2">
                                    <span class="course_sidebar_content_price">
                                        {{ coursePrice($course->id) }}
                                    </span>
                                </div>
                                <a href="{{route('frontend.watch-course', $course->id)}}" class="btn btn-primary px-4 py-2 align-self-start">
                                    Preview Course
                                </a>
                            </div>


                            <h5>This course includes:</h5>
                            <ul>
                                <li><i class="ti ti-alarm-clock"></i> {{ $course->total_duration }} @lang('exam.minutes')</li>
                                <li><i class="ti-book"></i>{{ $course->lessons_count }} @lang('lms::lms.lesson')</li>
                                <li><i class="ti ti-agenda"></i> {{ count($course->courseHomeworksAll) }} @lang('homework.homework')</li>
                                <li><i class="ti ti-book"></i> {{ count($course->studyMaterialsAll) }} @lang('study.study_material')</li>
                                @if (moduleStatusCheck('Zoom'))
                                    <li>
                                        <i class="ti-world"></i>
                                        {{ count($course->virtualClassesAll) }} @lang('zoom::zoom.virtual_class')
                                    </li>
                                @endif
                                @if (moduleStatusCheck('OnlineExam'))
                                    <li>
                                        <i class="ti-desktop"></i>
                                        {{ count($course->courseOnlineExamsAll) }} @lang('onlineexam::onlineExam.online_exam')
                                    </li>
                                @endif
                                @if (frontCourseSetting()->lms_checkout)
                                    <li>
                                        <i class="ti-user"></i>
                                        @lang('lms::lms.enrolled') {{ $course->purchase_logs_count }} @lang('student.students')
                                    </li>
                                    @if (isset($course->certificate))
                                        <li>
                                            <i class="ti ti-id-badge"></i>
                                            <p>@lang('lms::lms.certificate_of_completion')</p>
                                        </li>
                                    @endif
                                @endif
                                {{-- <li><i class="ti ti-thumb-up"></i> Full lifetime access</li> --}}
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- course details right -->
                <div class="col-xl-7 col-lg-8 col-md-12">
                    <div class="course_details">
                        <div class="course_details_mentor">
                            <div class="course_details_mentor_head">
                                <div class="course_details_mentor_profile">
                                    <img src="{{@$course->preview_image != ""? asset(@$course->preview_image) : assetPath('/public/backEnd/default.jpeg')}}" alt="">
                                </div>
                                <div class="course_details_mentor_title">
                                    <h5>{{@$course->course_title}}</h5>
                                    <ul>
                                        <li><i class="far fa-list"></i><span> {{ @$course->category->category_name }} @if($course->subCategory) - {{ !is_null($course->subCategory->category_name) ? $course->subCategory->category_name : ' ' }} @endif</span></li>
                                        <li><i class="far fa-book"></i>{{ @$course->subject->subject_name }}</li>
                                        @if (frontCourseSetting()->show_review_option) 
                                            <li><span class="course_details_mentor_rating">
                                                @php
                                                    $main_stars = $course->avgRating();
                                                    $stars = intval($course->avgRating());
                                                @endphp
                                                @for ($i = 0; $i < $stars; $i++)
                                                    <i class="fa fa-star"></i>
                                                @endfor
                                                @if ($main_stars > $stars)
                                                    <i class="fa fa-star-half-o" aria-hidden="true"></i>
                                                @endif
                                                @if (5 > $stars && $main_stars != 0)
                                                    @php
                                                        $uncheckStar = 5 - $stars;
                                                    @endphp
                                                    @for ($i = 1; $i < $uncheckStar; $i++)
                                                        <i class="fa fa-star unchecked"></i>
                                                    @endfor
                                                    @if ($uncheckStar == 1)
                                                        <i class="fa fa-star unchecked"></i>
                                                    @endif
                                                @endif
                                                @if ($main_stars == 0)
                                                    @for ($i = 0; $i < 5; $i++)
                                                        <i class="fa fa-star unchecked"></i>
                                                    @endfor
                                                @endif
                                                <span> {{ number_format($course->avgRating(), 1) }}
                                                    ({{ $course->reviews_count }})</span>
                                                </span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <nav class="course_details_menu">
                            <ul>
                                <li class='course_details_menu_list'><a href="#" class='course_details_menu_list_link active about-filter' data-name='overview'>Overview</a></li>
                                <li class='course_details_menu_list'><a href="#" class='course_details_menu_list_link about-filter' data-name='curriculum'>Curriculum</a></li>
                                <li class='course_details_menu_list'><a href="#" class='course_details_menu_list_link about-filter' data-name='instructors'>Instructors</a></li>
                                <li class='course_details_menu_list'><a href="#" class='course_details_menu_list_link about-filter' data-name='reviews'>Reviews</a></li>
                            </ul>
                        </nav>
                        <div class="course_details_abouts">
                            <div class="course_details_abouts_item overview">
                                <h3>Overview this Prerequisites</h3>
                                <p>{!! $course->prerequisites !!}</p>

                                <h3>Overview this Course</h3>
                                <p>{!! $course->overview !!}</p>

                                <h3>Overview this Description</h3>
                                <p>{!! $course->description !!}</p>
                            </div>
                            <div class="course_details_abouts_item curriculum">                                
                                <h3>Course Content</h3>
                                <p class="course_details_abouts_item_meta">{{ $course->lessons_count }} @lang('lms::lms.lesson')  •  {{ count($course->courseHomeworksAll) }} @lang('homework.homework')  •  {{ count($course->studyMaterialsAll) }} @lang('study.study_material')  •  {{ $course->total_duration }} @lang('exam.minutes')</p>
                                <div class="accordion" id="accordionExample">
                                    @foreach ($course->chapters as $index => $chapter)
                                        <div class="accordion-item">
                                            <div class="accordion-header" id="heading{{ $index }}">
                                                <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                                    aria-controls="collapse{{ $index }}">
                                                    {{ $chapter->chapter_name }}
                                                    <span>
                                                        {{ count($chapter->lessons) }} Lectures
                                                        •
                                                        {{ $chapter->lessons->sum('duration') ?? '0 Min' }}
                                                    </span>
                                                </button>
                                            </div>

                                            <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                                aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    @foreach ($chapter->lessons as $lesson)
                                                        @if ($lesson->is_lock == 1)
                                                            {{-- Locked - Show Modal --}}
                                                            <a href="#" 
                                                            class="accordion-body-item locked"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#lockedLessonModal">
                                                                <span class="accordion-body-item-icon">
                                                                    <i class="fa fa-lock"></i>
                                                                </span>
                                                                <span>
                                                                    {{ $lesson->lesson_name }}
                                                                    <span class="accordion-body-item-time">
                                                                        {{ $lesson->duration ?? '00:00' }}
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        @else
                                                            {{-- Unlocked - Link to lesson --}}
                                                            <a href="{{ route('frontend.watch-course-lesson', [$course->id, $lesson->id]) }}"
                                                            class="accordion-body-item mfp-iframe">
                                                                <span class="accordion-body-item-icon">
                                                                    <i class="fa fa-play"></i>
                                                                </span>
                                                                <span>
                                                                    {{ $lesson->lesson_name }}
                                                                    <span class="accordion-body-item-time">
                                                                        {{ $lesson->duration ?? '00:00' }}
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        @endif


                                                        {{-- Expanded Lesson Content (like quizzes, study materials, etc.) --}}
                                                        <div class="lesson-extra mt-2 ms-4">
                                                            @foreach ($lesson->lessonOnlineExams ?? [] as $exam)
                                                                <div class="lesson-extra-main"><i class="fa fa-pencil-alt me-1"></i> {{ $exam->title }}</div>
                                                            @endforeach
                                                            @foreach ($lesson->lessonQuizs ?? [] as $quiz)
                                                                <div class="lesson-extra-main"><i class="fa fa-question-circle me-1"></i> {{ $quiz->quiz->quiz_title }}</div>
                                                            @endforeach
                                                            @foreach ($lesson->lessonStudyMaterials ?? [] as $material)
                                                                <div class="lesson-extra-main"><i class="fa fa-file-alt me-1"></i> {{ $material->content_title }}</div>
                                                            @endforeach
                                                            @foreach ($lesson->lessonVirtualClasses ?? [] as $meeting)
                                                                <div class="lesson-extra-main"><i class="fa fa-video me-1"></i> {{ $meeting->topic }}</div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="course_details_abouts_item instructors">
                                <h3>Course Instructor</h3>
                                <div class="course_details_abouts_content_createor">
                                    <div class="course_details_abouts_content_createor_wrapper">
                                        <div class="course_details_abouts_content_createor_wrapper_profile">
                                            <img src="{{@$course->instructor->staff_photo != ""? asset(@$course->instructor->staff_photo) : assetPath('/public/backEnd/default.jpeg')}}" alt="">
                                        </div>
                                        <div class="course_details_abouts_content_createor_wrapper_content">
                                            <a href="instructor_details.php"><h4>{{ @$course->instructor->full_name }}</h4></a>
                                            <p>Building your Cloud and Data Centre career</p>
                                            <ul>
                                                <li><span><i class="ti ti-comments"></i></span> {{ $course->reviews_count }} Reviews</li>
                                                <li><span><i class="ti ti-user"></i></span> {{ $course->purchase_logs_count }} Students</li>
                                                <li><span><i class="ti ti-desktop"></i></span> {{ count($instructorCourses) }} Courses</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <p>{{ @$course->instructor->staff_bio }}</p>
                                </div>
                            </div>
                            <div class="course_details_abouts_item reviews">
                                <h3>Student Feedback</h3>
                                <div class="course_details_abouts_rating">
                                    <div class="course_details_abouts_rating_total">
                                        <h1>{{ number_format($course->avgRating(), 1) }}</h1>
                                        <div class="course_details_abouts_rating_total_star">
                                            @php
                                                $main_stars = $course->avgRating();
                                                $stars = intval($course->avgRating());
                                            @endphp
                                            @for ($i = 0; $i < $stars; $i++)
                                                <i class="fa fa-star"></i>
                                            @endfor
                                            @if ($main_stars > $stars)
                                                <i class="fa fa-star-half-o" aria-hidden="true"></i>
                                            @endif
                                            @if (5 > $stars && $main_stars != 0)
                                                @php
                                                    $uncheckStar = 5 - $stars;
                                                @endphp
                                                @for ($i = 1; $i < $uncheckStar; $i++)
                                                    <i class="fa fa-star unchecked"></i>
                                                @endfor
                                                @if ($uncheckStar == 1)
                                                    <i class="fa fa-star unchecked"></i>
                                                @endif
                                            @endif
                                            @if ($main_stars == 0)
                                                @for ($i = 0; $i < 5; $i++)
                                                    <i class="fa fa-star unchecked"></i>
                                                @endfor
                                            @endif
                                        </div>
                                        <p>Course Rating</p>
                                    </div>
                                    <div class="course_details_abouts_rating_overall">
                                        <div class="course_details_abouts_rating_overall_item">
                                            <div class="course_details_abouts_rating_overall_item_left"><span style="--width: {{ $count_5 }}%" data-width='{{ $count_5 }}'></span></div>
                                            <div class="course_details_abouts_rating_overall_item_right">
                                                <div class="course_details_abouts_rating_overall_item_star">
                                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                                    <span>{{ $count_5 }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="course_details_abouts_rating_overall_item">
                                            <div class="course_details_abouts_rating_overall_item_left"><span style="--width: {{ $count_4 }}%" data-width='{{ $count_4 }}'></span ></div>
                                            <div class="course_details_abouts_rating_overall_item_right">
                                                <div class="course_details_abouts_rating_overall_item_star">
                                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="far fa-star"></i>
                                                    <span>{{ $count_4 }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="course_details_abouts_rating_overall_item">
                                            <div class="course_details_abouts_rating_overall_item_left"><span style="--width: {{ $count_3 }}%" data-width='{{ $count_3 }}'></span ></div>
                                            <div class="course_details_abouts_rating_overall_item_right">
                                                <div class="course_details_abouts_rating_overall_item_star">
                                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                                                    <span>{{ $count_3 }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="course_details_abouts_rating_overall_item">
                                            <div class="course_details_abouts_rating_overall_item_left"><span style="--width: {{ $count_2 }}%" data-width='{{ $count_2 }}'></span ></div>
                                            <div class="course_details_abouts_rating_overall_item_right">
                                                <div class="course_details_abouts_rating_overall_item_star">
                                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                                                    <span>{{ $count_2 }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="course_details_abouts_rating_overall_item">
                                            <div class="course_details_abouts_rating_overall_item_left"><span style="--width: {{ $count_1 }}%" data-width='{{ $count_1 }}'></span ></div>
                                            <div class="course_details_abouts_rating_overall_item_right">
                                                <div class="course_details_abouts_rating_overall_item_star">
                                                    <i class="fa fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                                                    <span>{{ $count_1 }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="course_details_abouts_reviews">
                                    <div class="course_details_abouts_reviews_title">        
                                        <h3>Reviews</h3>
                                    </div>
                                    @foreach ($course->reviews as $review)
                                    <div class="course_details_abouts_reviews_wrapper">
                                        <div class="course_details_abouts_reviews_wrapper_left">
                                            <div class="course_details_abouts_reviews_wrapper_profile">
                                                @if (!empty(profile()))
                                                    <img
                                                        src="{{ asset(profile()) }}"
                                                        alt="Profile Pic">
                                                @else
                                                    <img
                                                        src="{{ assetPath('public/uploads/staff/demo/staff.jpg') }}"
                                                        alt="Profile Pic">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="course_details_abouts_reviews_wrapper_right">
                                            <div class="course_details_abouts_reviews_wrapper_content">
                                                <h4>{{ $review->user->full_name }}</h4>
                                                <p class="star">
                                                    @for ($i = 0; $i < $review->star; $i++)
                                                        <i class="fa fa-star"></i>
                                                    @endfor
                                                    @if (5 > intval($review->star))
                                                        @php
                                                            $uncheckStar =
                                                                5 - intval($review->star);
                                                        @endphp
                                                        @for ($i = 0; $i < $uncheckStar; $i++)
                                                            <i
                                                                class="fa fa-star unchecked"></i>
                                                        @endfor
                                                    @endif
                                                    <span>{{ dateconvert($review->created_at) }}</span>
                                                </p>
                                                <p>{{ $review->review_comment }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- course details end -->

    <!-- Locked Lesson Modal -->
    <div class="modal fade" id="lockedLessonModal" tabindex="-1" aria-labelledby="lockedLessonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary rounded-top-4">
                    <h6 class="modal-title text-white" id="lockedLessonModalLabel">
                        Lesson Locked
                    </h6>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                    </svg>
                    <h5 class="fw-bold mb-2 mt-3">Oops! This content is locked</h5>
                    <p class="text-muted mb-4">
                        This lesson is included in a premium course. To gain full access, please complete your purchase.
                    </p>
                </div>
            </div>
        </div>
    </div>


    {{ footerContent() }}
@endsection

@pushonce(config('pagebuilder.site_script_var'))
    <script>
        const filterBtns = document.querySelectorAll('.about-filter');
        const aboutItems = document.querySelectorAll('.course_details_abouts_item');

        filterBtns.forEach((btn) => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                filterBtns.forEach((btn) => btn.classList.remove('active'));

                this.classList.add('active');

                const value = this.dataset.name;

                aboutItems.forEach((item) => {
                    if (item.classList.contains(value)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        document.querySelector('.course_details_abouts_item.overview').style.display = 'block';
        document.querySelector('.about-filter[data-name="overview"]').classList.add('active');
    </script>
@endpushonce