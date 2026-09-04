<?php

/**
 * Upload Path Registry
 *
 * Single source of truth for all upload directories used in the application.
 * Previously these paths were hard-coded as string literals in 173+ controller methods.
 *
 * USAGE:
 *   // Old (scattered, hard-coded):
 *   $file->move('public/uploads/leave_request/', $fileName);
 *
 *   // New (centralized via service):
 *   app(FileUploadServiceInterface::class)->upload($file, 'leave_request');
 *   // or still via helper:
 *   fileUpload($file, uploadPath('leave_request'));
 *
 * ADDING NEW DIRECTORIES:
 *   Add an entry here. Never hard-code a new upload path in a controller.
 *
 * PATH FORMAT:
 *   All paths use 'public/uploads/{module}/' format.
 *   'public/' is the web root on shared hosting — files are served via asset().
 *   DB columns store the full relative path: 'public/uploads/student/abc.jpg'
 *   Views render via: assetPath('public/uploads/student/abc.jpg')
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Student
    |--------------------------------------------------------------------------
    */
    'student' => 'public/uploads/student/',
    'student_documents' => 'public/uploads/student/document/',
    'student_timeline' => 'public/uploads/student/timeline/',
    'student_id_card' => 'public/uploads/studentIdCard/',

    /*
    |--------------------------------------------------------------------------
    | Staff / HR
    |--------------------------------------------------------------------------
    */
    'staff' => 'public/uploads/staff/',
    'staff_documents' => 'public/uploads/staff/document/',
    'staff_timeline' => 'public/uploads/staff/timeline/',
    'resume' => 'public/uploads/resume/',

    /*
    |--------------------------------------------------------------------------
    | Academic
    |--------------------------------------------------------------------------
    */
    'homework' => 'public/uploads/homework/',
    'homework_content' => 'public/uploads/homeworkcontent/',
    'exam' => 'public/uploads/exam/',
    'upload_contents' => 'public/uploads/upload_contents/',
    'question_bank' => 'public/uploads/upload_contents/',
    'certificate' => 'public/uploads/certificate/',
    'academic_calendar' => 'public/uploads/academic_calendar/',

    /*
    |--------------------------------------------------------------------------
    | Administration
    |--------------------------------------------------------------------------
    */
    'leave_request' => 'public/uploads/leave_request/',
    'complaint' => 'public/uploads/complaint/',
    'visitor' => 'public/uploads/visitor/',
    'postal' => 'public/uploads/postal/',
    'bank_slip' => 'public/uploads/bankSlip/',
    'custom_fields' => 'public/uploads/customFields/',
    'zoom_meeting' => 'public/uploads/zoom-meeting/',

    /*
    |--------------------------------------------------------------------------
    | Finance
    |--------------------------------------------------------------------------
    */
    'add_income' => 'public/uploads/add_income/',
    'add_expense' => 'public/uploads/addExpense/',

    /*
    |--------------------------------------------------------------------------
    | Events & News
    |--------------------------------------------------------------------------
    */
    'events' => 'public/uploads/events/',
    'holidays' => 'public/uploads/holidays/',
    'news' => 'public/uploads/news/',

    /*
    |--------------------------------------------------------------------------
    | Settings & Branding
    |--------------------------------------------------------------------------
    */
    'settings' => 'public/uploads/settings/',
    'preloader' => 'public/uploads/preloader/',
    'background' => 'public/uploads/backgroundImage/',
    'theme' => 'public/uploads/theme/',

    /*
    |--------------------------------------------------------------------------
    | Frontend / CMS
    |--------------------------------------------------------------------------
    */
    'pages' => 'public/uploads/pages/',
    'testimonials' => 'public/uploads/testimonial/',
    'course' => 'public/uploads/course/',
    'editor_images' => 'public/uploads/editor-image/',
    'contact_page' => 'public/uploads/contactPage/',
    'about_page' => 'public/uploads/about_page/',
    'homepage' => 'public/uploads/homepageCreate/',
    'front_class_routine' => 'public/uploads/front_class_routine/',
    'front_exam_routine' => 'public/uploads/front_exam_routine/',
    'front_result' => 'public/uploads/front_result/',

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types by category (used by validate())
    |--------------------------------------------------------------------------
    */
    'allowed_types' => [
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
        'document' => ['application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'],
        'any' => [], // empty = allow all types
    ],
];
