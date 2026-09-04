<?php

namespace App\Http\Controllers;

use Exception;
use Modules\Lms\Entities\Course;
use Modules\Lms\Entities\CourseLesson;

class OnlineCourseController extends Controller
{
    public static function reviewCount($reviews, $echReview, $total)
    {
        $echReviewCount = $reviews->where('star', $echReview)->count();
        if ($echReviewCount > 0) {
            return ($echReviewCount / $total) * 100;
        }

        return 0;
    }

    public function onlineCourse()
    {
        try {
            $data['page'] = (object) [
                'title' => __('edulia.online_course'),
            ];
            $data['all_course'] = Course::where('active_status', 1)->where('school_id', app('school')->id)->get();

            return view('frontEnd.theme.'.activeTheme().'.online-course.online_course', $data);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function onlineCourseDetails($slug)
    {
        $course = Course::where('active_status', 1)
            ->where('school_id', app('school')->id)
            ->where('slug', $slug)
            ->with([
                'chapters',
                'comments',
                'reviews',
                'smOnlineExam.assignQuestions.questionBank',
            ])
            ->withCount(['reviews', 'purchaseLogs', 'lessons'])
            ->first();

        if (! $course) {
            abort(404, 'Course not found.');
        }

        $data['page'] = (object) [
            'title' => __('edulia.online_course_details'),
        ];
        $data['course'] = $course;

        $data['count_5'] = static::reviewCount($course->reviews, 5.00, $course->reviews_count);
        $data['count_4'] = static::reviewCount($course->reviews, 4.00, $course->reviews_count);
        $data['count_3'] = static::reviewCount($course->reviews, 3.00, $course->reviews_count);
        $data['count_2'] = static::reviewCount($course->reviews, 2.00, $course->reviews_count);
        $data['count_1'] = static::reviewCount($course->reviews, 1.00, $course->reviews_count);

        $data['instructorCourses'] = Course::where('instructor_id', $course->instructor_id)
            ->get('avg_rating');

        return view('frontEnd.theme.'.activeTheme().'.online-course.online_course_details', $data);
    }

    public function onlineCourseWatch($id)
    {
        $course = Course::with('chapters')->where('id', $id)->firstOrFail();
        if (! $course) {
            abort(404, 'Course not found.');
        }

        return redirect()->back();
    }

    public function onlineCourseLessonWatch($id, $lesson_id)
    {
        $course = Course::with('chapters')->where('id', $id)->firstOrFail();
        $lesson = CourseLesson::findOrFail($lesson_id);
        if (! $course) {
            abort(404, 'Course not found.');
        }

        return redirect()->back();
    }
}
