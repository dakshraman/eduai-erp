<?php

namespace Modules\Lesson\Entities;

use App\Models\Shift;
use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Database\Eloquent\Model;

class SmLessonTopic extends Model
{
    protected $fillable = [];

    public function class()
    {
        return $this->belongsTo(\App\Models\SmClass::class, 'class_id', 'id');
    }

    public function section()
    {
        return $this->belongsTo(\App\Models\SmSection::class, 'section_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(\App\Models\SmSubject::class, 'subject_id', 'id');
    }

    public function topics()
    {
        return $this->hasMany(SmLessonTopicDetail::class, 'topic_id', 'id');
    }

    public function lesson()
    {
        return $this->belongsTo(SmLesson::class, 'lesson_id', 'id');
    }

    public function unSession()
    {
        return $this->belongsTo(\Modules\University\Entities\UnSession::class, 'un_session_id', 'id')->withDefault();
    }

    public function unFaculty()
    {
        return $this->belongsTo(\Modules\University\Entities\UnFaculty::class, 'un_faculty_id', 'id')->withDefault();
    }

    public function unDepartment()
    {
        return $this->belongsTo(\Modules\University\Entities\UnDepartment::class, 'un_department_id', 'id')->withDefault();
    }

    public function unAcademic()
    {
        return $this->belongsTo(\Modules\University\Entities\UnAcademicYear::class, 'un_academic_id', 'id')->withDefault();
    }

    public function unSemester()
    {
        return $this->belongsTo(\Modules\University\Entities\UnSemester::class, 'un_semester_id', 'id')->withDefault();
    }

    public function unSemesterLabel()
    {
        return $this->belongsTo(\Modules\University\Entities\UnSemesterLabel::class, 'un_semester_label_id', 'id')->withDefault();
    }

    public function unSubject()
    {
        return $this->belongsTo(\Modules\University\Entities\UnSubject::class, 'un_subject_id', 'id')->withDefault();
    }

    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'branch_id', 'id')->withDefault();
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StatusAcademicSchoolScope);
    }
}
