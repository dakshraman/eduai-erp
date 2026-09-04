<?php

namespace App\Imports;

use App\Models\SmStudent;
use App\Models\SmSubjectAttendance;
use App\Models\StudentRecord;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SubjectWiseAttendanceImport implements ToModel, WithHeadingRow, WithStartRow
{
    /**
     * @param  array  $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public $class;

    public $section;

    public $subject_id;

    public function __construct($class, $section, $subject_id)
    {
        $this->class = $class;
        $this->section = $section;
        $this->subject_id = $subject_id;
    }

    public function model(array $row)
    {
        $student = SmStudent::where('admission_no', $row['admission_no'])
            ->where('school_id', Auth::user()->school_id)
            ->first();

        if (! $student) {
            return null;
        }

        $record = StudentRecord::where('student_id', $student->id)
            ->where('class_id', $this->class)
            ->where('section_id', $this->section)
            ->first();

        if (! $record) {
            return null;
        }

        $date = is_numeric($row['attendance_date'])
            ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['attendance_date'])->format('Y-m-d')
            : date('Y-m-d', strtotime($row['attendance_date']));

        // Delete existing attendance
        SmSubjectAttendance::where('student_record_id', $record->id)
            ->where('subject_id', $this->subject_id)
            ->where('attendance_date', $date)
            ->delete();

        return new SmSubjectAttendance([
            'student_record_id' => $record->id,
            'student_id' => $student->id,
            'class_id' => $this->class,
            'section_id' => $this->section,
            'subject_id' => $this->subject_id,
            'attendance_date' => $date,
            'attendance_type' => $row['attendance_type'],
            'notes' => $row['note'],
            'school_id' => Auth::user()->school_id,
            'academic_id' => getAcademicId(),
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
