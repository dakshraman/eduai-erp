@extends('frontEnd.home.front_master')

@push('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        /* Hero */
        .hero-gradient {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 50%, #A855F7 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .hero-gradient::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }
        .hero-title { font-size: 3.5rem; font-weight: 800; line-height: 1.1; letter-spacing: -0.02em; }
        .hero-subtitle { font-size: 1.25rem; font-weight: 300; opacity: 0.9; line-height: 1.6; }
        .hero-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 1rem 2rem; border-radius: 12px; font-weight: 600; font-size: 1rem;
            transition: all 0.3s ease; text-decoration: none;
        }
        .hero-btn-primary { background: #fff; color: #4F46E5; }
        .hero-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(0,0,0,0.2); color: #4338CA; }
        .hero-btn-outline { border: 2px solid rgba(255,255,255,0.4); color: #fff; background: transparent; }
        .hero-btn-outline:hover { background: rgba(255,255,255,0.1); border-color: #fff; }

        /* Stats */
        .stat-card {
            background: #fff; border-radius: 16px; padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 20px rgba(0,0,0,0.04);
            transition: all 0.3s ease; border: 1px solid #f1f5f9;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 40px rgba(79,70,229,0.12); }
        .stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stat-number { font-size: 2rem; font-weight: 800; color: #1e293b; line-height: 1; }
        .stat-label { font-size: 0.875rem; color: #64748b; font-weight: 500; margin-top: 0.25rem; }

        /* Section Headers */
        .section-header { text-align: center; margin-bottom: 3rem; }
        .section-badge {
            display: inline-block; padding: 0.375rem 1rem; border-radius: 100px;
            font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
            background: #EEF2FF; color: #4F46E5; margin-bottom: 1rem;
        }
        .section-title { font-size: 2.25rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
        .section-desc { font-size: 1.125rem; color: #64748b; margin-top: 0.75rem; max-width: 600px; margin-left: auto; margin-right: auto; }

        /* Cards */
        .card-modern {
            background: #fff; border-radius: 16px; overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 20px rgba(0,0,0,0.04);
            transition: all 0.3s ease; border: 1px solid #f1f5f9;
        }
        .card-modern:hover { transform: translateY(-6px); box-shadow: 0 12px 40px rgba(0,0,0,0.1); }
        .card-img-wrap { height: 200px; overflow: hidden; position: relative; }
        .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
        .card-modern:hover .card-img-wrap img { transform: scale(1.05); }
        .card-body { padding: 1.5rem; }
        .card-date { font-size: 0.75rem; font-weight: 600; color: #4F46E5; text-transform: uppercase; letter-spacing: 0.05em; }
        .card-title { font-size: 1.125rem; font-weight: 700; color: #0f172a; margin: 0.5rem 0; line-height: 1.4; }
        .card-title a { color: inherit; text-decoration: none; }
        .card-title a:hover { color: #4F46E5; }
        .card-text { font-size: 0.875rem; color: #64748b; line-height: 1.6; }
        .card-link { display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; font-weight: 600; color: #4F46E5; text-decoration: none; margin-top: 1rem; }
        .card-link:hover { gap: 0.625rem; }

        /* Notice Board */
        .notice-board { background: #f8fafc; border-radius: 16px; padding: 1.5rem; border: 1px solid #e2e8f0; }
        .notice-item { padding: 1rem 0; border-bottom: 1px solid #e2e8f0; }
        .notice-item:last-child { border-bottom: none; }
        .notice-date { font-size: 0.75rem; font-weight: 600; color: #4F46E5; }
        .notice-title { font-size: 0.9375rem; font-weight: 600; color: #1e293b; margin-top: 0.25rem; cursor: pointer; }
        .notice-title:hover { color: #4F46E5; }

        /* Events */
        .event-card {
            background: #fff; border-radius: 16px; padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06); border: 1px solid #f1f5f9;
            transition: all 0.3s ease; display: flex; gap: 1rem; align-items: flex-start;
        }
        .event-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); transform: translateY(-2px); }
        .event-date-box {
            min-width: 60px; text-align: center; background: #EEF2FF; border-radius: 12px; padding: 0.75rem;
        }
        .event-date-box .day { font-size: 1.5rem; font-weight: 800; color: #4F46E5; line-height: 1; }
        .event-date-box .month { font-size: 0.75rem; font-weight: 600; color: #6366f1; text-transform: uppercase; }
        .event-info h4 { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem; }
        .event-info p { font-size: 0.8125rem; color: #64748b; display: flex; align-items: center; gap: 0.375rem; }

        /* Testimonials */
        .testimonial-section { background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); position: relative; overflow: hidden; }
        .testimonial-section::before {
            content: ''; position: absolute; top: -100px; right: -100px;
            width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%;
        }
        .testimonial-card {
            background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);
            border-radius: 16px; padding: 2rem; border: 1px solid rgba(255,255,255,0.15);
            color: #fff;
        }
        .testimonial-text { font-size: 1rem; line-height: 1.7; opacity: 0.95; font-style: italic; }
        .testimonial-author { display: flex; align-items: center; gap: 1rem; margin-top: 1.5rem; }
        .testimonial-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.3); }
        .testimonial-name { font-weight: 700; font-size: 0.9375rem; }
        .testimonial-role { font-size: 0.8125rem; opacity: 0.7; }

        /* CTA */
        .cta-section { background: #f8fafc; }
        .cta-card {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            border-radius: 24px; padding: 4rem 3rem; text-align: center; color: #fff;
            position: relative; overflow: hidden;
        }
        .cta-card::before {
            content: ''; position: absolute; top: -50%; right: -20%;
            width: 400px; height: 400px; background: rgba(255,255,255,0.05); border-radius: 50%;
        }
        .cta-title { font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; }
        .cta-desc { font-size: 1.125rem; opacity: 0.9; margin-bottom: 2rem; max-width: 500px; margin-left: auto; margin-right: auto; }
        .cta-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 1rem 2.5rem; background: #fff; color: #4F46E5;
            border-radius: 12px; font-weight: 700; font-size: 1rem;
            text-decoration: none; transition: all 0.3s ease;
        }
        .cta-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(0,0,0,0.2); }

        /* Footer */
        .footer-modern { background: #0f172a; color: #94a3b8; }
        .footer-title { color: #fff; font-size: 1rem; font-weight: 700; margin-bottom: 1rem; }
        .footer-link { color: #94a3b8; text-decoration: none; font-size: 0.875rem; display: block; padding: 0.25rem 0; transition: color 0.2s; }
        .footer-link:hover { color: #fff; }
        .footer-bottom { border-top: 1px solid #1e293b; padding-top: 2rem; margin-top: 3rem; }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title { font-size: 2.25rem; }
            .section-title { font-size: 1.75rem; }
            .cta-card { padding: 2.5rem 1.5rem; }
            .cta-title { font-size: 1.75rem; }
        }
    </style>
@endpush

@section('main_content')
    {{-- Hero Section --}}
    <section class="hero-gradient" style="padding: 6rem 0 5rem; min-height: 80vh; display: flex; align-items: center;">
        <div class="container" style="position: relative; z-index: 1;">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    @if(isset($per["Image Banner"]) && isset($homePage))
                        <p class="hero-subtitle mb-3" style="opacity: 0.8;">{{ $homePage->title }}</p>
                        <h1 class="hero-title text-white mb-4">{{ $homePage->long_title }}</h1>
                        <p class="hero-subtitle mb-5">{{ $homePage->short_description }}</p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="{{ $homePage->link_url }}" class="hero-btn hero-btn-primary">
                                {{ $homePage->link_label }} <i class="fas fa-arrow-right"></i>
                            </a>
                            <a href="/login" class="hero-btn hero-btn-outline">
                                <i class="fas fa-play-circle"></i> Live Demo
                            </a>
                        </div>
                    @else
                        <p class="hero-subtitle mb-3" style="opacity: 0.8;">Smart School Management</p>
                        <h1 class="hero-title text-white mb-4">The Modern Way to Manage Your School</h1>
                        <p class="hero-subtitle mb-5">All-in-one platform for attendance, fees, exams, and communication — powered by AI insights.</p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="/register" class="hero-btn hero-btn-primary">
                                Start Free Trial <i class="fas fa-arrow-right"></i>
                            </a>
                            <a href="/login" class="hero-btn hero-btn-outline">
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </a>
                        </div>
                    @endif
                </div>
                <div class="col-lg-5 text-center mt-5 mt-lg-0">
                    <img src="{{ assetPath('public/uploads/settings/logo.png') }}" alt="EduAI"
                         style="max-width: 280px; filter: brightness(0) invert(1); opacity: 0.9;">
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section style="padding: 4rem 0; margin-top: -3rem; position: relative; z-index: 2;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto" style="background: #EEF2FF; color: #4F46E5;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-number mt-3">500+</div>
                        <div class="stat-label">Students Enrolled</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto" style="background: #F0FDF4; color: #16a34a;">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-number mt-3">50+</div>
                        <div class="stat-label">Expert Teachers</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto" style="background: #FFF7ED; color: #ea580c;">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="stat-number mt-3">30+</div>
                        <div class="stat-label">Courses Available</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto" style="background: #FDF2F8; color: #db2777;">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="stat-number mt-3">98%</div>
                        <div class="stat-label">Success Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- News + Notice Board --}}
    @if(isset($per["Latest News"]) || isset($per["Notice Board"]))
    <section style="padding: 5rem 0; background: #fff;">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Stay Updated</span>
                <h2 class="section-title">News & Announcements</h2>
                <p class="section-desc">Stay informed with the latest happenings and important notices from our school.</p>
            </div>
            <div class="row g-4">
                @if(isset($per["Latest News"]))
                <div class="col-lg-8">
                    <div class="row g-4">
                        @foreach($news as $value)
                        <div class="col-md-6">
                            <div class="card-modern">
                                <div class="card-img-wrap">
                                    <img src="{{ assetPath($value->image) }}" alt="{{ $value->news_title }}">
                                </div>
                                <div class="card-body">
                                    <span class="card-date">{{ $value->publish_date != "" ? dateConvert($value->publish_date) : '' }}</span>
                                    <h3 class="card-title">
                                        <a href="{{ url('news-details/'.$value->id) }}">{{ $value->news_title }}</a>
                                    </h3>
                                    <a href="{{ url('news-details/'.$value->id) }}" class="card-link">
                                        Read More <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(isset($per["Notice Board"]))
                <div class="col-lg-4">
                    <div class="notice-board">
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 1.25rem;">
                            <i class="fas fa-bullhorn" style="color: #4F46E5;"></i> Notice Board
                        </h3>
                        @foreach($notice_board as $notice)
                        <div class="notice-item">
                            <span class="notice-date">{{ $notice->publish_on != "" ? dateConvert($notice->publish_on) : '' }}</span>
                            <h4 class="notice-title" data-toggle="modal" data-target="#NoticeDetails{{ $notice->id }}">
                                {{ $notice->notice_title }}
                            </h4>

                            <div class="modal fade admin-query" id="NoticeDetails{{ $notice->id }}">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                        <div class="modal-header" style="background: #4F46E5; border-radius: 16px 16px 0 0;">
                                            <h4 class="modal-title text-white">{{ $notice->notice_title }}</h4>
                                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body" style="padding: 2rem;">
                                            {!! $notice->notice_message !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- Courses Section --}}
    @if(isset($per["Academics"]))
    <section style="padding: 5rem 0; background: #f8fafc;">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Our Programs</span>
                <h2 class="section-title">Explore Courses</h2>
                <p class="section-desc">Discover a wide range of courses designed to shape future leaders.</p>
            </div>
            <div class="row g-4">
                @foreach($academics as $academic)
                <div class="col-lg-4 col-md-6">
                    <div class="card-modern">
                        <div class="card-img-wrap">
                            <img src="{{ assetPath($academic->image) }}" alt="{{ $academic->title }}">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">
                                <a href="{{ url('course-Details/'.$academic->id) }}">{{ $academic->title }}</a>
                            </h3>
                            <p class="card-text">{!! substr(strip_tags($academic->overview), 0, 80) !!}...</p>
                            <a href="{{ url('course-Details/'.$academic->id) }}" class="card-link">
                                Learn More <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Events Section --}}
    @if(isset($per["Event List"]))
    <section style="padding: 5rem 0; background: #fff;">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">What's Happening</span>
                <h2 class="section-title">Upcoming Events</h2>
                <p class="section-desc">Mark your calendar for these exciting upcoming events.</p>
            </div>
            <div class="row g-4">
                @foreach($events as $event)
                <div class="col-lg-6">
                    <div class="event-card">
                        @php
                            $fromDate = $event->from_date ? \Carbon\Carbon::parse($event->from_date) : null;
                        @endphp
                        @if($fromDate)
                        <div class="event-date-box">
                            <div class="day">{{ $fromDate->format('d') }}</div>
                            <div class="month">{{ $fromDate->format('M') }}</div>
                        </div>
                        @endif
                        <div class="event-info">
                            <h4>{{ $event->event_title }}</h4>
                            <p><i class="fas fa-map-marker-alt"></i> {{ $event->event_location }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Testimonials --}}
    @if(isset($per["Testimonial"]))
    <section class="testimonial-section" style="padding: 5rem 0;">
        <div class="container" style="position: relative; z-index: 1;">
            <div class="section-header">
                <span class="section-badge" style="background: rgba(255,255,255,0.15); color: #fff;">Testimonials</span>
                <h2 class="section-title text-white">What People Say</h2>
                <p class="section-desc" style="color: rgba(255,255,255,0.7);">Hear from our students, parents, and staff.</p>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach($testimonial as $value)
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <div class="testimonial-text">"{{ $value->description }}"</div>
                        <div class="testimonial-author">
                            @if(!empty($value->image))
                                <img class="testimonial-avatar" src="{{ assetPath($value->image) }}" alt="{{ $value->name }}">
                            @else
                                <img class="testimonial-avatar" src="{{ assetPath('public/uploads/sample.jpg') }}" alt="{{ $value->name }}">
                            @endif
                            <div>
                                <div class="testimonial-name">{{ $value->name }}</div>
                                <div class="testimonial-role">{{ $value->designation }}, {{ $value->institution_name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- CTA Section --}}
    <section class="cta-section" style="padding: 5rem 0;">
        <div class="container">
            <div class="cta-card">
                <h2 class="cta-title text-white">Ready to Transform Your School?</h2>
                <p class="cta-desc">Join thousands of schools already using EduAI to streamline their operations.</p>
                <a href="/register" class="cta-btn">
                    Get Started Free <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="footer-modern" style="padding: 4rem 0 2rem;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h3 class="footer-title">
                        <img src="{{ defaultLogo($setting->logo) }}" alt="EduAI" style="max-width: 120px; margin-bottom: 1rem; filter: brightness(0) invert(1);">
                    </h3>
                    <p style="font-size: 0.9375rem; line-height: 1.7;">Modern school management platform built for the future of education.</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" style="color: #94a3b8; font-size: 1.25rem;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" style="color: #94a3b8; font-size: 1.25rem;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: #94a3b8; font-size: 1.25rem;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: #94a3b8; font-size: 1.25rem;"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h4 class="footer-title">Product</h4>
                    <a href="/register" class="footer-link">Pricing</a>
                    <a href="/login" class="footer-link">Demo</a>
                    <a href="#" class="footer-link">Features</a>
                    <a href="#" class="footer-link">Integrations</a>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h4 class="footer-title">Company</h4>
                    <a href="#" class="footer-link">About Us</a>
                    <a href="#" class="footer-link">Careers</a>
                    <a href="#" class="footer-link">Blog</a>
                    <a href="#" class="footer-link">Contact</a>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h4 class="footer-title">Support</h4>
                    <a href="#" class="footer-link">Help Center</a>
                    <a href="#" class="footer-link">Documentation</a>
                    <a href="#" class="footer-link">API Status</a>
                    <a href="#" class="footer-link">Community</a>
                </div>
                <div class="col-lg-2">
                    <h4 class="footer-title">Legal</h4>
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms of Service</a>
                    <a href="#" class="footer-link">Cookie Policy</a>
                </div>
            </div>
            <div class="footer-bottom text-center">
                <p style="margin: 0; font-size: 0.875rem;">&copy; {{ date('Y') }} EduAI. All rights reserved.</p>
            </div>
        </div>
    </footer>
@endsection
