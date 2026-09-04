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
</style>

{{headerContent()}}
    <section class="bradcrumb_area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="bradcrumb_area_inner">
                        <h1>{{__('edulia.online_course')}}<span><a href="{{url('/')}}">{{__('edulia.home')}}</a> / {{__('edulia.online_course')}}</span></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section_padding course">
        <div class="container">
            <div class="row">
                @include('frontEnd.theme.' . activeTheme() . '.online-course.filter')
                <div class="col-lg-9 col-md-8">
                    <div class="row">
                        @foreach($all_course as $course)
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('frontend.online-course-details', $course->slug) }}" class="course_item">
                                <div class="course_item_img">
                                    <div class="course_item_img_inner">                                
                                        <img src="{{assetPath($course->preview_image != ""? $course->preview_image : '/public/backEnd/default.jpeg')}}" alt="{{$course->course_title}}">
                                    </div>
                                    <span class="course_item_img_status orange">{{$course->category->category_name}}</span>
                                </div>
                                <div class="course_item_inner">
                                    <h4>{{$course->course_title}}</h4>
                                    <p><span class='course_item_inner_rating'>{{ number_format($course->avgRating(), 1) ? number_format($course->avgRating(), 1) : 0 }}/5 <i class="fa fa-star"></i></span> {{ coursePrice($course->id) }}</p>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{ footerContent() }}
@endsection

