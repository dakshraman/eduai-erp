<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sm_student_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('sm_students');
            $table->foreignId('subject_id')->constrained('sm_subjects');
            $table->string('topic')->nullable();
            $table->decimal('mastery_level', 5, 2)->default(0);
            $table->integer('total_assignments')->default(0);
            $table->integer('completed_assignments')->default(0);
            $table->decimal('average_score', 5, 2)->default(0);
            $table->json('weak_areas')->nullable();
            $table->json('strong_areas')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'subject_id', 'topic']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('sm_student_competencies');
    }
};
