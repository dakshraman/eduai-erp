<?php
use Illuminate\Support\Facades\Route;
use Modules\AiAssignment\Http\Controllers\AiAssignmentController;
use Modules\AiAssignment\Http\Controllers\AiGradingController;

Route::middleware(['auth'])->prefix('ai-assignment')->group(function () {
    Route::get('/', [AiAssignmentController::class, 'index'])->name('ai-assignment.dashboard');
    Route::get('/generate', [AiAssignmentController::class, 'generateForm'])->name('ai-assignment.generate');
    Route::post('/generate', [AiAssignmentController::class, 'generate'])->name('ai-assignment.generate.store');
    Route::get('/preview', [AiAssignmentController::class, 'preview'])->name('ai-assignment.preview');
    Route::post('/save', [AiAssignmentController::class, 'saveGenerated'])->name('ai-assignment.save');
    Route::get('/analytics', [AiAssignmentController::class, 'analytics'])->name('ai-assignment.analytics');
    Route::get('/bulk-grade', [AiGradingController::class, 'bulkGradeForm'])->name('ai-grading.form');
    Route::post('/bulk-grade', [AiGradingController::class, 'bulkGrade'])->name('ai-grading.process');
});
