<?php

namespace Modules\ExamPlan\Entities;

use App\Models\SmExamType;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmitCard extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function studentRecord()
    {
        return $this->belongsTo(StudentRecord::class, 'student_record_id', 'id');
    }

    public function examType()
    {
        return $this->belongsTo(SmExamType::class, 'exam_type_id', 'id');
    }
}
