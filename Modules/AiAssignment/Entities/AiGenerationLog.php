<?php
namespace Modules\AiAssignment\Entities;

use Illuminate\Database\Eloquent\Model;

class AiGenerationLog extends Model {
    protected $table = 'ai_generation_logs';
    protected $fillable = ['user_id', 'school_id', 'action', 'model_used', 'input_tokens', 'output_tokens', 'cost', 'metadata'];
    protected $casts = ['metadata' => 'array', 'cost' => 'decimal:6'];

    public function user() { return $this->belongsTo(\App\User::class); }
    public function school() { return $this->belongsTo(\App\Models\SmSchool::class, 'school_id'); }
}
