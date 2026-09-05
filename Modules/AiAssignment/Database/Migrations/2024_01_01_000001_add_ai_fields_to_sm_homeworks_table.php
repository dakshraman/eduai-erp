<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sm_homeworks', function (Blueprint $table) {
            $table->string('difficulty')->nullable()->after('marks');
            $table->string('bloom_level')->nullable()->after('difficulty');
            $table->json('learning_objectives')->nullable()->after('bloom_level');
            $table->boolean('ai_generated')->default(false)->after('learning_objectives');
            $table->text('ai_prompt')->nullable()->after('ai_generated');
        });
    }

    public function down(): void {
        Schema::table('sm_homeworks', function (Blueprint $table) {
            $table->dropColumn(['difficulty', 'bloom_level', 'learning_objectives', 'ai_generated', 'ai_prompt']);
        });
    }
};
