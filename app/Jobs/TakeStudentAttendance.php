<?php

namespace App\Jobs;

use App\Models\SmStudent;
use App\Traits\NotificationSend;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class TakeStudentAttendance implements ShouldQueue
{
    use NotificationSend, Queueable;

    /**
     * Create a new job instance.
     */
    protected $attendances;

    public function __construct($data)
    {
        $this->attendances = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::table('sm_student_attendances')->insert($this->attendances);
        foreach ($this->attendances as $attendance) {
            if ($attendance['attendance_type'] === 'H') {
                $type = 'Holiday';
            } elseif ($attendance['attendance_type'] === 'P') {
                $type = 'Present';
            } elseif ($attendance['attendance_type'] === 'L') {
                $type = 'Late';
            } elseif ($attendance['attendance_type'] === 'F') {
                $type = 'Half  Day';
            } else {
                $type = 'Absent';
            }
            $student_user_id = SmStudent::find($attendance['student_id'])->user_id;
            $data['class_id'] = $attendance['class_id'];
            $data['section_id'] = $attendance['section_id'];
            $data['attendance_type'] = $type;
            $this->sent_notifications('Student_Attendance', [$student_user_id], $data, ['Student', 'Parent']);
        }

    }
}
