<?php
namespace Modules\AiAssignment\Entities;

use Illuminate\Database\Eloquent\Model;

class SmStudentCompetency extends Model {
    protected $table = 'sm_student_competencies';
    protected $fillable = ['student_id', 'subject_id', 'topic', 'mastery_level', 'total_assignments', 'completed_assignments', 'average_score', 'weak_areas', 'strong_areas'];
    protected $casts = ['weak_areas' => 'array', 'strong_areas' => 'array', 'mastery_level' => 'decimal:2', 'average_score' => 'decimal:2'];

    public function student() { return $this->belongsTo(\App\SmStudent::class, 'student_id'); }
    public function subject() { return $this->belongsTo(\App\SmSubject::class, 'subject_id'); }
}
